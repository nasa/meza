<?php
/**
 * download.php
 *
 * Download backup files for a given wiki.
 *
 * Parameters:
 * - file: The name of the file to download (required)
 * - wiki: The wiki name (required)
 * - dir: The directory within the wiki's backup folder (optional)
 * - stream: If set, the file will be streamed inline; otherwise, it will be downloaded as an attachment.
 *
 * Authentication:
 * - Requires Single Sign On (SSO) via SimpleSAMLphp.
 *
 * Security:
 * - Validates input parameters to prevent path traversal and unauthorized access.
 * - Checks user permissions against allowed downloaders.
 *
 * Usage:
 * Example URL: /BackupDownload/download.php?file=backup.sql.gz&wiki=mywiki&dir=daily
 *
 * @author Greg Rundlett
 * @license GPLv2 or later
 */

// load meza config
// Dynamically determine Meza installation directory for portability

// Method 1: Try to find meza command in PATH and determine install dir
$mezaCommand = trim(shell_exec('which meza 2>/dev/null'));
if ($mezaCommand && file_exists($mezaCommand)) {
	// Get the real path and go up directories: /install/meza/src/scripts/meza.py -> /install
	$installDir = dirname(dirname(dirname(dirname(realpath($mezaCommand)))));
} else {
	// Method 2: Fallback - assume current script is in /install/htdocs/BackupDownload/
	// Go up from htdocs/BackupDownload to find install directory
	$currentDir = dirname(dirname(__FILE__)); // htdocs directory
	$installDir = dirname($currentDir); // install directory
}

$configPath = $installDir . '/.deploy-meza/config.php';

// Verify config file exists before including
if (!file_exists($configPath)) {
	http_response_code(500);
	die('Configuration file not found. Please ensure Meza is properly configured.');
}

// Include the generated configuration
require_once($configPath);

// hide notices
@ini_set('error_reporting', E_ALL & ~ E_NOTICE);

//- turn off compression on the server
@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 'Off');

/**
 * Validate and sanitize input parameters
 */
function validateInputs() {
	if (!isset($_REQUEST['file']) || empty($_REQUEST['file']) || 
		!isset($_REQUEST['wiki']) || empty($_REQUEST['wiki'])) {
		header("HTTP/1.0 400 Bad Request");
		exit;
	}

	// Validate wiki name - only allow alphanumeric, underscore, hyphen
	$wiki = $_REQUEST['wiki'];
	if (!preg_match('/^[a-zA-Z0-9_-]+$/', $wiki)) {
		header("HTTP/1.0 400 Bad Request");
		exit("Invalid wiki name");
	}

	// Validate directory name if provided
	$directory = null;
	if (isset($_REQUEST['dir']) && !empty($_REQUEST['dir'])) {
		$directory = $_REQUEST['dir'];
		if (!preg_match('/^[a-zA-Z0-9_-]+$/', $directory)) {
			header("HTTP/1.0 400 Bad Request");
			exit("Invalid directory name");
		}
	}

	// Validate filename - only basename, no path components
	$requested_file = $_REQUEST['file'];
	$file_name = basename($requested_file);
	
	// Ensure the basename matches the original request (no path traversal)
	if ($file_name !== $requested_file) {
		header("HTTP/1.0 400 Bad Request");
		exit("Invalid file name");
	}

	// Validate file extension
	$allowed_extensions = ['sql', 'xml', 'gz', 'zip', 'tar', 'bz2'];
	$path_parts = pathinfo($file_name);
	$file_ext = strtolower($path_parts['extension'] ?? '');
	
	if (!in_array($file_ext, $allowed_extensions)) {
		header("HTTP/1.0 400 Bad Request");
		exit("File type not allowed");
	}

	return [
		'wiki' => $wiki,
		'directory' => $directory,
		'file_name' => $file_name,
		'file_ext' => $file_ext
	];
}

/**
 * Get the environment directory safely
 */
function getEnvironment($m_backups, $backups_environment = null) {
	if (isset($backups_environment)) {
		$env = $backups_environment;
	} else {
		$undesiredStrings = array(".", "..", ".DS_Store", ".htaccess", "README");
		$envs = array_diff(scandir($m_backups), $undesiredStrings);
		$env = array_pop($envs);
	}

	// Validate environment name
	if (!preg_match('/^[a-zA-Z0-9_-]+$/', $env)) {
		header("HTTP/1.0 400 Bad Request");
		exit("Invalid environment");
	}

	return $env;
}

/**
 * Build and validate the complete file path
 */
function buildSecureFilePath($m_backups, $env, $wiki, $directory, $file_name) {
	// Build base path
	$base_path = realpath($m_backups . '/' . $env);
	if ($base_path === false) {
		header("HTTP/1.0 404 Not Found");
		exit("Environment not found");
	}

	// Build wiki path
	$wiki_path = $base_path . '/' . $wiki;
	if (!is_dir($wiki_path)) {
		header("HTTP/1.0 404 Not Found");
		exit("Wiki not found");
	}

	// Build directory path if specified
	$target_dir = $wiki_path;
	if ($directory !== null) {
		$target_dir = $wiki_path . '/' . $directory;
		if (!is_dir($target_dir)) {
			header("HTTP/1.0 404 Not Found");
			exit("Directory not found");
		}
	}

	// Build final file path
	$file_path = $target_dir . '/' . $file_name;
	
	// Ensure the resolved path is within our allowed directory
	$real_file_path = realpath($file_path);
	$real_base_path = realpath($base_path);
	
	if ($real_file_path === false || strpos($real_file_path, $real_base_path . DIRECTORY_SEPARATOR) !== 0) {
		header("HTTP/1.0 403 Forbidden");
		exit("Access denied");
	}

	return $real_file_path;
}

/**
 * Get list of allowed files for the user and wiki
 */
function getAllowedFiles($file_path, $wiki, $userID, $wiki_backup_downloaders, $all_backup_downloaders) {
	// Check if user has permission for this wiki
	if (!isset($wiki_backup_downloaders) || !is_array($wiki_backup_downloaders)) {
		$wiki_backup_downloaders = array();
	}
	if (!isset($all_backup_downloaders) || !is_array($all_backup_downloaders)) {
		$all_backup_downloaders = array();
	}
	if (!isset($wiki_backup_downloaders[$wiki])) {
		$wiki_backup_downloaders[$wiki] = array();
	}

	$allowedUsers = array_merge($all_backup_downloaders, $wiki_backup_downloaders[$wiki]);
	
	if (!in_array($userID, $allowedUsers)) {
		return false;
	}

	// Get list of actual files in the directory
	$directory = dirname($file_path);
	$requested_file = basename($file_path);
	
	if (!is_dir($directory)) {
		return false;
	}

	$allowed_files = array();
	$files = scandir($directory);
	foreach ($files as $file) {
		if ($file !== '.' && $file !== '..' && is_file($directory . '/' . $file)) {
			$allowed_files[] = $file;
		}
	}

	return in_array($requested_file, $allowed_files);
}

// Main execution
try {
	// Validate inputs
	$inputs = validateInputs();
	$wiki = $inputs['wiki'];
	$directory = $inputs['directory'];
	$file_name = $inputs['file_name'];
	$file_ext = $inputs['file_ext'];

	// Get environment
	$env = getEnvironment($m_backups, $backups_environment ?? null);

	// Build secure file path
	$file_path = buildSecureFilePath($m_backups, $env, $wiki, $directory, $file_name);

	// Check if streaming or attachment
	$is_attachment = !isset($_REQUEST['stream']);

	// Handle authentication
	if (is_file($m_deploy . '/SAMLConfig.php')) {
		$auth_file = $m_htdocs . '/NonMediaWikiSimpleSamlAuth.php';
		if (!file_exists($auth_file)) {
			header('HTTP/1.0 500 Internal Server Error');
			exit("Unavailable without Authentication. Authentication module not found.");
		}
		require_once $auth_file;
	} else {
		header('HTTP/1.0 403 Forbidden');
		exit("Authentication is required for this operation.");
	}

	$as = new SimpleSAML_Auth_Simple('default-sp');
	$as->requireAuth();
	$attributes = $as->getAttributes();
	$userID = $attributes[$saml_idp_username_attr][0];

	// Validate file access permissions
	if (!getAllowedFiles($file_path, $wiki, $userID, $wiki_backup_downloaders ?? [], $all_backup_downloaders ?? [])) {
		header("HTTP/1.0 403 Forbidden");
		exit("Access denied");
	}

	// Verify file exists and is readable
	if (!is_file($file_path) || !is_readable($file_path)) {
		header("HTTP/1.0 404 Not Found");
		exit("File not found");
	}

	// Open file for download
	$file_size = filesize($file_path);
	$file = @fopen($file_path, "rb");
	if (!$file) {
		header("HTTP/1.0 500 Internal Server Error");
		exit("Cannot open file");
	}

	// Set headers
	header("Pragma: public");
	header("Expires: -1");
	header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");

	// Set appropriate headers for attachment or streamed file
	if ($is_attachment) {
		header("Content-Disposition: attachment; filename=\"$file_name\"");
	} else {
		header('Content-Disposition: inline;');
	}

	// Set MIME type
	$content_types = array(
		"sql" => "application/sql",
		"xml" => "application/xml",
		"zip" => "application/zip",
		"gz" => "application/gzip",
		"tar" => "application/x-tar",
		"bz2" => "application/x-bzip2"
	);
	$ctype = $content_types[$file_ext] ?? "application/octet-stream";
	header("Content-Type: " . $ctype);

	// Handle range requests
	$range = '';
	if (isset($_SERVER['HTTP_RANGE'])) {
		if (!preg_match('/^bytes=\d*-\d*$/', $_SERVER['HTTP_RANGE'])) {
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			exit;
		}
		list($size_unit, $range_orig) = explode('=', $_SERVER['HTTP_RANGE'], 2);
		if ($size_unit == 'bytes') {
			list($range, $extra_ranges) = explode(',', $range_orig, 2);
		} else {
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			exit;
		}
	}

	// Calculate download range
	list($seek_start, $seek_end) = explode('-', $range, 2);
	$seek_end = (empty($seek_end)) ? ($file_size - 1) : min(abs(intval($seek_end)), ($file_size - 1));
	$seek_start = (empty($seek_start) || $seek_end < abs(intval($seek_start))) ? 0 : max(abs(intval($seek_start)), 0);

	// Send appropriate headers
	if ($seek_start > 0 || $seek_end < ($file_size - 1)) {
		header('HTTP/1.1 206 Partial Content');
		header('Content-Range: bytes ' . $seek_start . '-' . $seek_end . '/' . $file_size);
		header('Content-Length: ' . ($seek_end - $seek_start + 1));
	} else {
		header("Content-Length: $file_size");
	}

	header('Accept-Ranges: bytes');
	set_time_limit(0);
	fseek($file, $seek_start);

	// Stream file content
	while (!feof($file)) {
		print(@fread($file, 1024 * 8));
		ob_flush();
		flush();
		if (connection_status() != 0) {
			@fclose($file);
			exit;
		}
	}

	@fclose($file);
	exit;

} catch (Exception $e) {
	error_log("Download error: " . $e->getMessage());
	header("HTTP/1.0 500 Internal Server Error");
	exit("Internal server error");
}

