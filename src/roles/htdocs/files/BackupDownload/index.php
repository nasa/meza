<!DOCTYPE html>
<html>
<head>
		<meta charset="utf-8">
		<title>Meza Backup Listing</title>
</head>
<body>
<h1>Backup Files</h1>

<?php

# Debug
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

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

// if there's a SAML config file, we need to authenticate with SAML, like, now.
if ( is_file( $m_deploy.'/SAMLConfig.php' ) ) {
	require_once $m_htdocs.'/NonMediaWikiSimpleSamlAuth.php';
}
else {
	header('HTTP/1.0 403 Forbidden');
	echo "Backup downloading is not permitted without Single Sign On";
}

$as = new SimpleSAML_Auth_Simple( 'default-sp' );
$as->requireAuth();
$attributes = $as->getAttributes();
$userID = $attributes[ $saml_idp_username_attr ][0];

$undesiredStrings = array(
	".",
	"..",
	".DS_Store",
	".htaccess",
	"README",
);

if ( isset( $backups_environment ) ) {
    $env = $backups_environment;
}
else {
    $envs = array_diff( scandir( $m_backups ), $undesiredStrings );

    // if there are multiple environments backed up to this server, and
    // $backups_environment isn't specified for which environment to disply in
    // backupListing.php, then just guess the first one.
    // FIXME #816: This probably is all totally broken in a polylithic setup.
    $env = array_pop( $envs );
}

// path to backups is backups directory + environment
$path = realpath( "$m_backups/$env" );

// Scan stuff at $path, but remove things like "." and ".."
$wikis = array_diff( scandir( $path ), $undesiredStrings );

// wiki_backup_downloaders and all_backup_downloaders from config.php if set
if ( ! isset( $wiki_backup_downloaders ) || ! is_array( $wiki_backup_downloaders ) ) {
    $wiki_backup_downloaders = array();
}
if ( ! isset( $all_backup_downloaders ) || ! is_array( $all_backup_downloaders ) ) {
    $all_backup_downloaders = array();
}

$urlBase = "https://$wiki_app_fqdn/BackupDownload/download.php";

foreach( $wikis as $wiki ){

    if ( ! isset( $wiki_backup_downloaders[ $wiki ] ) ) {
        $wiki_backup_downloaders[ $wiki ] = array();
    }

    $allowedUsers = array_merge( $all_backup_downloaders, $wiki_backup_downloaders[ $wiki ] );

	if ( in_array($userID, $allowedUsers) ) {

		// Display name of wiki
		echo "<h2>$wiki</h2>";
		echo "<ul>";

		// Get contents of wiki directory
		$objects = scandir( $path . "/" . $wiki );

		$dbDumps = preg_grep("/^(.*)_wiki.sql/", $objects);

		// Display contents of directory with links
		foreach( $dbDumps as $dbDump ){
			echo "<li><a href='$urlBase?wiki=$wiki&file=$dbDump'>$dbDump</a></li>";
		}

		echo "</ul>";
	}
}

?></body>
</html>
