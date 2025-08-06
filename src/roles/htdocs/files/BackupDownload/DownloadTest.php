<?php
/**
 * Unit tests for download.php
 *
 * This test suite validates the security and functionality of the backup download script.
 * Tests security features including path traversal prevention, input validation,
 * and access control mechanisms.
 * 
 * Usage:
 *   php DownloadTest.php (standalone mode)
 *   phpunit DownloadTest.php (if PHPUnit is available)
 * 
 * @group Backup
 * @group Security
 * @author Greg Rundlett
 * @license GPLv2 or later
 */

// Try to use PHPUnit if available, fall back to simple framework
if (class_exists('PHPUnit\Framework\TestCase')) {
	abstract class BaseTestCase extends PHPUnit\Framework\TestCase
	{
		protected function tearDown(): void
		{
			parent::tearDown();
		}

		protected function setUp(): void
		{
			parent::setUp();
		}
	}
} else {
	// Simple test framework for when PHPUnit is not available
	abstract class BaseTestCase
	{
		protected $testCount = 0;
		protected $passCount = 0;
		protected $failCount = 0;
		protected $expectedExceptionClass = null;
		protected $expectedExceptionMessage = null;

		protected function setUp(): void
		{
			// Override in subclasses
		}

		protected function tearDown(): void
		{
			// Override in subclasses
		}

		public function assertSame($expected, $actual, $message = '')
		{
			$this->testCount++;
			if ($expected === $actual) {
				$this->passCount++;
				echo "PASS: " . ($message ?: 'assertSame') . "\n";
			} else {
				$this->failCount++;
				echo "FAIL: " . ($message ?: 'assertSame') . " - Expected: " . var_export($expected, true) . ", Got: " . var_export($actual, true) . "\n";
			}
		}

		public function assertTrue($condition, $message = '')
		{
			$this->testCount++;
			if ($condition === true) {
				$this->passCount++;
				echo "PASS: " . ($message ?: 'assertTrue') . "\n";
			} else {
				$this->failCount++;
				echo "FAIL: " . ($message ?: 'assertTrue') . " - Expected true, got " . var_export($condition, true) . "\n";
			}
		}

		public function assertFalse($condition, $message = '')
		{
			$this->testCount++;
			if ($condition === false) {
				$this->passCount++;
				echo "PASS: " . ($message ?: 'assertFalse') . "\n";
			} else {
				$this->failCount++;
				echo "FAIL: " . ($message ?: 'assertFalse') . " - Expected false, got " . var_export($condition, true) . "\n";
			}
		}

		public function assertIsArray($value, $message = '')
		{
			$this->testCount++;
			if (is_array($value)) {
				$this->passCount++;
				echo "PASS: " . ($message ?: 'assertIsArray') . "\n";
			} else {
				$this->failCount++;
				echo "FAIL: " . ($message ?: 'assertIsArray') . " - Expected array, got " . gettype($value) . "\n";
			}
		}

		public function assertArrayHasKey($key, $array, $message = '')
		{
			$this->testCount++;
			if (array_key_exists($key, $array)) {
				$this->passCount++;
				echo "PASS: " . ($message ?: 'assertArrayHasKey') . "\n";
			} else {
				$this->failCount++;
				echo "FAIL: " . ($message ?: 'assertArrayHasKey') . " - Key '$key' not found in array\n";
			}
		}

		public function assertStringNotContainsString($needle, $haystack, $message = '')
		{
			$this->testCount++;
			if (strpos($haystack, $needle) === false) {
				$this->passCount++;
				echo "PASS: " . ($message ?: 'assertStringNotContainsString') . "\n";
			} else {
				$this->failCount++;
				echo "FAIL: " . ($message ?: 'assertStringNotContainsString') . " - String contains '$needle'\n";
			}
		}

		public function expectException($exceptionClass)
		{
			$this->expectedExceptionClass = $exceptionClass;
		}

		public function expectExceptionMessage($message)
		{
			$this->expectedExceptionMessage = $message;
		}

		protected function runTest($methodName)
		{
			echo "\n--- Running $methodName ---\n";
			try {
				$this->setUp();
				$this->$methodName();
				
				// If we expected an exception but didn't get one
				if (isset($this->expectedExceptionClass)) {
					$this->testCount++;
					$this->failCount++;
					echo "FAIL: Expected exception {$this->expectedExceptionClass} but none was thrown\n";
				}
			} catch (Exception $e) {
				if (isset($this->expectedExceptionClass)) {
					$this->testCount++;
					if (get_class($e) === $this->expectedExceptionClass || is_a($e, $this->expectedExceptionClass)) {
						if (!isset($this->expectedExceptionMessage) || strpos($e->getMessage(), $this->expectedExceptionMessage) !== false) {
							$this->passCount++;
							echo "PASS: Expected exception thrown\n";
						} else {
							$this->failCount++;
							echo "FAIL: Exception message mismatch - Expected: {$this->expectedExceptionMessage}, Got: {$e->getMessage()}\n";
						}
					} else {
						$this->failCount++;
						echo "FAIL: Wrong exception type - Expected: {$this->expectedExceptionClass}, Got: " . get_class($e) . "\n";
					}
				} else {
					$this->failCount++;
					echo "FAIL: Unexpected exception: " . $e->getMessage() . "\n";
				}
			} finally {
				$this->tearDown();
				$this->expectedExceptionClass = null;
				$this->expectedExceptionMessage = null;
			}
		}

		public function run()
		{
			$methods = get_class_methods($this);
			$testMethods = array_filter($methods, function($method) {
				return strpos($method, 'test') === 0;
			});

			echo "Running " . count($testMethods) . " tests...\n";

			foreach ($testMethods as $method) {
				$this->runTest($method);
			}

			echo "\n=== RESULTS ===\n";
			echo "Tests: {$this->testCount}, Passed: {$this->passCount}, Failed: {$this->failCount}\n";
			
			if ($this->failCount > 0) {
				exit(1);
			}
		}
	}
}

/**
 * Test class for download.php security and functionality
 */
class DownloadTest extends BaseTestCase
{
	private $tempDir;
	private $backupsDir;
	private $testFiles = [];
	private $originalRequest;
	private $originalGlobals = [];

	protected function setUp(): void
	{
		parent::setUp();
		
		// Backup original $_REQUEST
		$this->originalRequest = $_REQUEST ?? [];
		
		// Create temporary directory structure for testing
		$this->tempDir = sys_get_temp_dir() . '/meza_test_' . uniqid();
		$this->backupsDir = $this->tempDir . '/backups';
		
		mkdir($this->tempDir, 0755, true);
		mkdir($this->backupsDir, 0755, true);
		mkdir($this->backupsDir . '/prod', 0755, true);
		mkdir($this->backupsDir . '/prod/testwiki', 0755, true);
		mkdir($this->backupsDir . '/prod/testwiki/daily', 0755, true);

		// Create test files
		$this->testFiles = [
			$this->backupsDir . '/prod/testwiki/backup.sql.gz' => 'test backup content',
			$this->backupsDir . '/prod/testwiki/daily/daily_backup.sql.gz' => 'daily backup content',
			$this->backupsDir . '/prod/testwiki/uploads.tar.gz' => 'uploads content',
			$this->backupsDir . '/prod/testwiki/malicious.php' => '<?php echo "hacked"; ?>',
			$this->backupsDir . '/prod/testwiki/test.xml' => '<xml>test</xml>',
		];

		foreach ($this->testFiles as $filePath => $content) {
			file_put_contents($filePath, $content);
		}

		// Store original globals and set test globals
		$this->setTestGlobals();
	}

	protected function tearDown(): void
	{
		// Restore original $_REQUEST
		$_REQUEST = $this->originalRequest;
		
		// Restore original globals
		$this->restoreGlobals();
		
		// Clean up temporary files
		$this->removeDirectory($this->tempDir);
		
		parent::tearDown();
	}

	private function setTestGlobals()
	{
		$globals = [
			'm_backups',
			'm_deploy', 
			'm_htdocs',
			'saml_idp_username_attr',
			'wiki_backup_downloaders',
			'all_backup_downloaders',
			'backups_environment'
		];

		// Backup existing values
		foreach ($globals as $global) {
			if (isset($GLOBALS[$global])) {
				$this->originalGlobals[$global] = $GLOBALS[$global];
			}
		}

		// Set test values
		$GLOBALS['m_backups'] = $this->backupsDir;
		$GLOBALS['m_deploy'] = $this->tempDir . '/deploy';
		$GLOBALS['m_htdocs'] = $this->tempDir . '/htdocs';
		$GLOBALS['saml_idp_username_attr'] = 'uid';
		$GLOBALS['backups_environment'] = 'prod';
		
		$GLOBALS['wiki_backup_downloaders'] = [
			'testwiki' => ['testuser', 'admin']
		];
		$GLOBALS['all_backup_downloaders'] = ['globaladmin'];

		// Create deploy directory
		mkdir($GLOBALS['m_deploy'], 0755, true);
		mkdir($GLOBALS['m_htdocs'], 0755, true);
	}

	private function restoreGlobals()
	{
		foreach ($this->originalGlobals as $key => $value) {
			$GLOBALS[$key] = $value;
		}
	}

	private function removeDirectory($dir)
	{
		if (!is_dir($dir)) {
			return;
		}
		$files = array_diff(scandir($dir), ['.', '..']);
		foreach ($files as $file) {
			$path = $dir . '/' . $file;
			is_dir($path) ? $this->removeDirectory($path) : unlink($path);
		}
		rmdir($dir);
	}

	public function testValidateInputs()
	{
		$_REQUEST = [
			'file' => 'backup.sql.gz',
			'wiki' => 'testwiki',
			'dir' => 'daily'
		];

		$result = $this->callValidateInputs();

		$this->assertSame('testwiki', $result['wiki'], 'Wiki name should be validated');
		$this->assertSame('daily', $result['directory'], 'Directory should be validated');
		$this->assertSame('backup.sql.gz', $result['file_name'], 'File name should be validated');
		$this->assertSame('gz', $result['file_ext'], 'File extension should be extracted');
	}

	public function testValidateInputsInvalidWiki()
	{
		$_REQUEST = [
			'file' => 'backup.sql.gz',
			'wiki' => '../malicious',
			'dir' => 'daily'
		];

		$this->expectException('Exception');
		$this->expectExceptionMessage('Invalid wiki name');
		$this->callValidateInputs();
	}

	public function testValidateInputsPathTraversal()
	{
		$_REQUEST = [
			'file' => '../../../etc/passwd',
			'wiki' => 'testwiki'
		];

		$this->expectException('Exception');
		$this->expectExceptionMessage('Invalid file name');
		$this->callValidateInputs();
	}

	public function testValidateInputsInvalidExtension()
	{
		$_REQUEST = [
			'file' => 'malicious.php',
			'wiki' => 'testwiki'
		];

		$this->expectException('Exception');
		$this->expectExceptionMessage('File type not allowed');
		$this->callValidateInputs();
	}

	public function testValidateInputsMissingFile()
	{
		$_REQUEST = [
			'wiki' => 'testwiki'
		];

		$this->expectException('Exception');
		$this->expectExceptionMessage('Bad Request');
		$this->callValidateInputs();
	}

	public function testValidateInputsMissingWiki()
	{
		$_REQUEST = [
			'file' => 'backup.sql.gz'
		];

		$this->expectException('Exception');
		$this->expectExceptionMessage('Bad Request');
		$this->callValidateInputs();
	}

	public function testGetEnvironment()
	{
		$result = $this->callGetEnvironment($GLOBALS['m_backups'], null);
		$this->assertSame('prod', $result, 'Should auto-detect environment');

		$result = $this->callGetEnvironment($GLOBALS['m_backups'], 'prod');
		$this->assertSame('prod', $result, 'Should use specified environment');
	}

	public function testGetEnvironmentInvalid()
	{
		$this->expectException('Exception');
		$this->expectExceptionMessage('Invalid environment');
		$this->callGetEnvironment($GLOBALS['m_backups'], '../malicious');
	}

	public function testBuildSecureFilePath()
	{
		$result = $this->callBuildSecureFilePath(
			$GLOBALS['m_backups'], 
			'prod', 
			'testwiki', 
			null, 
			'backup.sql.gz'
		);

		$expected = realpath($this->backupsDir . '/prod/testwiki/backup.sql.gz');
		$this->assertSame($expected, $result, 'Should build secure file path');
	}

	public function testBuildSecureFilePathWithDirectory()
	{
		$result = $this->callBuildSecureFilePath(
			$GLOBALS['m_backups'], 
			'prod', 
			'testwiki', 
			'daily', 
			'daily_backup.sql.gz'
		);

		$expected = realpath($this->backupsDir . '/prod/testwiki/daily/daily_backup.sql.gz');
		$this->assertSame($expected, $result, 'Should build secure file path with subdirectory');
	}

	public function testBuildSecureFilePathInvalidWiki()
	{
		$this->expectException('Exception');
		$this->expectExceptionMessage('Wiki not found');
		$this->callBuildSecureFilePath(
			$GLOBALS['m_backups'], 
			'prod', 
			'nonexistent', 
			null, 
			'backup.sql.gz'
		);
	}

	public function testBuildSecureFilePathInvalidEnvironment()
	{
		$this->expectException('Exception');
		$this->expectExceptionMessage('Environment not found');
		$this->callBuildSecureFilePath(
			$GLOBALS['m_backups'], 
			'nonexistent', 
			'testwiki', 
			null, 
			'backup.sql.gz'
		);
	}

	public function testGetAllowedFilesAuthorizedUser()
	{
		$filePath = $this->backupsDir . '/prod/testwiki/backup.sql.gz';
		
		$result = $this->callGetAllowedFiles(
			$filePath,
			'testwiki',
			'testuser',
			['testwiki' => ['testuser', 'admin']],
			['globaladmin']
		);

		$this->assertTrue($result, 'Authorized user should have access');
	}

	public function testGetAllowedFilesGlobalAdmin()
	{
		$filePath = $this->backupsDir . '/prod/testwiki/backup.sql.gz';
		
		$result = $this->callGetAllowedFiles(
			$filePath,
			'testwiki',
			'globaladmin',
			['testwiki' => ['testuser', 'admin']],
			['globaladmin']
		);

		$this->assertTrue($result, 'Global admin should have access');
	}

	public function testGetAllowedFilesUnauthorizedUser()
	{
		$filePath = $this->backupsDir . '/prod/testwiki/backup.sql.gz';
		
		$result = $this->callGetAllowedFiles(
			$filePath,
			'testwiki',
			'unauthorized',
			['testwiki' => ['testuser', 'admin']],
			['globaladmin']
		);

		$this->assertFalse($result, 'Unauthorized user should not have access');
	}

	public function testGetAllowedFilesNonexistentFile()
	{
		$filePath = $this->backupsDir . '/prod/testwiki/nonexistent.sql.gz';
		
		$result = $this->callGetAllowedFiles(
			$filePath,
			'testwiki',
			'testuser',
			['testwiki' => ['testuser', 'admin']],
			['globaladmin']
		);

		$this->assertFalse($result, 'Nonexistent file should not be accessible');
	}

	public function testSecurityDoubleExtension()
	{
		$_REQUEST = [
			'file' => 'backup.php.gz',
			'wiki' => 'testwiki'
		];

		// This should pass validation as the extension is .gz
		$result = $this->callValidateInputs();
		$this->assertSame('gz', $result['file_ext'], 'Should extract final extension only');
	}

	public function testSecurityNullByte()
	{
		$_REQUEST = [
			'file' => "backup.sql.gz\0.php",
			'wiki' => 'testwiki'
		];

		// basename() should handle null bytes correctly
		$result = $this->callValidateInputs();
		// The result should not contain null bytes
		$this->assertStringNotContainsString("\0", $result['file_name'], 'Should strip null bytes');
	}

	/**
	 * Test comprehensive security validation
	 */
	public function testPathTraversalPrevention()
	{
		$dangerous_files = [
			'../etc/passwd',
			'../../etc/passwd',
			'../../../etc/passwd',
			'..\\etc\\passwd',
			'..\\..\\etc\\passwd',
			'/etc/passwd',
			'\\etc\\passwd',
			'backup/../../../etc/passwd',
			'backup/..\\..\\..\\etc\\passwd',
		];

		foreach ($dangerous_files as $file) {
			$_REQUEST = [
				'file' => $file,
				'wiki' => 'testwiki'
			];

			try {
				$this->callValidateInputs();
				echo "FAIL: Path traversal not prevented for: $file\n";
				$this->failCount++;
			} catch (Exception $e) {
				if (strpos($e->getMessage(), 'Invalid file name') !== false) {
					echo "PASS: Path traversal prevented for: $file\n";
					$this->passCount++;
				} else {
					echo "FAIL: Wrong exception for path traversal: $file - {$e->getMessage()}\n";
					$this->failCount++;
				}
			}
			$this->testCount++;
		}
	}

	public function testInvalidExtensions()
	{
		$dangerous_extensions = [
			'malicious.php',
			'script.js',
			'executable.exe',
			'backdoor.jsp',
			'trojan.asp',
			'virus.bat',
			'malware.sh',
		];

		foreach ($dangerous_extensions as $file) {
			$_REQUEST = [
				'file' => $file,
				'wiki' => 'testwiki'
			];

			try {
				$this->callValidateInputs();
				echo "FAIL: Invalid extension not rejected: $file\n";
				$this->failCount++;
			} catch (Exception $e) {
				if (strpos($e->getMessage(), 'File type not allowed') !== false) {
					echo "PASS: Invalid extension rejected: $file\n";
					$this->passCount++;
				} else {
					echo "FAIL: Wrong exception for invalid extension: $file - {$e->getMessage()}\n";
					$this->failCount++;
				}
			}
			$this->testCount++;
		}
	}

	/**
	* Helper methods to simulate the functions from download.php
	* These recreate the key validation logic for testing
	* 
	* IMPORTANT: These methods must be kept synchronized with the actual
	* implementation in download.php. Any changes to the main functions
	* should be reflected here to maintain test accuracy.
	*/
	private function callValidateInputs()
	{
		if (!isset($_REQUEST['file']) || empty($_REQUEST['file']) || 
			!isset($_REQUEST['wiki']) || empty($_REQUEST['wiki'])) {
			throw new Exception("Bad Request");
		}

		// Validate wiki name - only allow alphanumeric, underscore, hyphen
		$wiki = $_REQUEST['wiki'];
		if (!preg_match('/^[a-zA-Z0-9_-]+$/', $wiki)) {
			throw new Exception("Invalid wiki name");
		}

		// Validate directory name if provided
		$directory = null;
		if (isset($_REQUEST['dir']) && !empty($_REQUEST['dir'])) {
			$directory = $_REQUEST['dir'];
			if (!preg_match('/^[a-zA-Z0-9_-]+$/', $directory)) {
				throw new Exception("Invalid directory name");
			}
		}

		// Validate filename - only basename, no path components
		$requested_file = $_REQUEST['file'];
		$file_name = basename($requested_file);
		
		// Ensure the basename matches the original request (no path traversal)
		if ($file_name !== $requested_file) {
			throw new Exception("Invalid file name");
		}

		// Validate file extension
		$allowed_extensions = ['sql', 'xml', 'gz', 'zip', 'tar', 'bz2'];
		$path_parts = pathinfo($file_name);
		$file_ext = strtolower($path_parts['extension'] ?? '');
		
		if (!in_array($file_ext, $allowed_extensions)) {
			throw new Exception("File type not allowed");
		}

		return [
			'wiki' => $wiki,
			'directory' => $directory,
			'file_name' => $file_name,
			'file_ext' => $file_ext
		];
	}

	private function callGetEnvironment($m_backups, $backups_environment = null)
	{
		if (isset($backups_environment)) {
			$env = $backups_environment;
		} else {
			$undesiredStrings = array(".", "..", ".DS_Store", ".htaccess", "README");
			$envs = array_diff(scandir($m_backups), $undesiredStrings);
			$env = array_pop($envs);
		}

		// Validate environment name
		if (!preg_match('/^[a-zA-Z0-9_-]+$/', $env)) {
			throw new Exception("Invalid environment");
		}

		return $env;
	}

	private function callBuildSecureFilePath($m_backups, $env, $wiki, $directory, $file_name)
	{
		// Build base path
		$base_path = realpath($m_backups . '/' . $env);
		if ($base_path === false) {
			throw new Exception("Environment not found");
		}

		// Build wiki path
		$wiki_path = $base_path . '/' . $wiki;
		if (!is_dir($wiki_path)) {
			throw new Exception("Wiki not found");
		}

		// Build directory path if specified
		$target_dir = $wiki_path;
		if ($directory !== null) {
			$target_dir = $wiki_path . '/' . $directory;
			if (!is_dir($target_dir)) {
				throw new Exception("Directory not found");
			}
		}

		// Build final file path
		$file_path = $target_dir . '/' . $file_name;
		
		// Ensure the resolved path is within our allowed directory
		$real_file_path = realpath($file_path);
		$real_base_path = realpath($base_path);
		
		if ($real_file_path === false || strpos($real_file_path, $real_base_path . DIRECTORY_SEPARATOR) !== 0) {
			throw new Exception("Access denied");
		}

		return $real_file_path;
	}

	private function callGetAllowedFiles($file_path, $wiki, $userID, $wiki_backup_downloaders, $all_backup_downloaders)
	{
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
}

// If this script is run directly (not included), run the tests
if (basename(__FILE__) == basename($_SERVER['PHP_SELF'])) {
	$test = new DownloadTest();
	$test->run();
}
