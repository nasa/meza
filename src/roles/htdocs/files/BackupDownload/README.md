# Meza Backup Download Security Implementation

## Overview

This directory contains a secure backup file download system for the Meza MediaWiki platform. The implementation addresses CWE-22 (Path Traversal) vulnerabilities and provides comprehensive security validation.

## Files

### `download.php`
The main secure download script that provides:
- **Input validation** - Validates wiki names, file names, and directory names
- **Path traversal prevention** - Uses `basename()` and `realpath()` to prevent directory traversal attacks
- **File type validation** - Only allows approved file extensions (sql, xml, gz, zip, tar, bz2)
- **Authentication integration** - Works with SAML and Meza user management
- **Authorization controls** - Per-wiki and global administrator access controls

### `DownloadTest.php`
Comprehensive test suite that validates:
- **Input validation functions** - Tests all validation logic
- **Security features** - Tests path traversal prevention and malicious input handling
- **Authorization logic** - Tests user permission checking
- **Edge cases** - Tests null bytes, double extensions, and various attack vectors

## Security Features

### Path Traversal Prevention (CWE-22)
The script prevents path traversal attacks through multiple layers:

1. **Input sanitization** - `basename()` strips path components
2. **Path validation** - Ensures basename matches original request  
3. **Realpath resolution** - `realpath()` resolves symbolic links and relative paths
4. **Directory confinement** - Validates final path is within allowed directory tree

### File Type Validation
Only approved backup file types are allowed:
- `.sql` - Database dumps
- `.xml` - XML exports  
- `.gz` - Compressed files
- `.zip` - Archive files
- `.tar` - Archive files
- `.bz2` - Compressed files

### Input Validation
All user inputs are validated with strict regex patterns:
- Wiki names: `^[a-zA-Z0-9_-]+$`
- Directory names: `^[a-zA-Z0-9_-]+$`
- Environment names: `^[a-zA-Z0-9_-]+$`

## Authentication & Authorization

The system integrates with Meza's SAML authentication and provides two levels of access control:

1. **Wiki-specific downloaders** - Users authorized for specific wikis
2. **Global administrators** - Users with access to all backup files

Configuration is managed through Meza's global variables:
- `$wiki_backup_downloaders` - Per-wiki access control
- `$all_backup_downloaders` - Global administrator list

## Testing

Run the comprehensive test suite:

```bash
php DownloadTest.php
```

The test suite includes:
- 20+ individual test methods
- 37+ total security validations
- Path traversal attack simulations
- Invalid file type testing
- Authorization testing
- Edge case validation

## Usage Examples

### Download a database backup
```http
GET /download.php?wiki=mywiki&file=backup.sql.gz
```

### Download from subdirectory
```http
GET /download.php?wiki=mywiki&dir=daily&file=daily_backup.sql.gz
```

### Download uploads archive
```http
GET /download.php?wiki=mywiki&file=uploads.tar.gz
```

## Security Considerations

### Approved Patterns
✅ `backup.sql.gz` - Valid backup file  
✅ `daily/backup.sql.gz` - Valid subdirectory access  
✅ `uploads.tar.gz` - Valid uploads archive

### Blocked Patterns  
❌ `../../../etc/passwd` - Path traversal attempt  
❌ `malicious.php` - Invalid file type  
❌ `backup.php.gz` - Potential double extension attack (rejected by extension validation)  
❌ `/etc/passwd` - Absolute path attempt

## Error Handling

The script provides appropriate HTTP error responses:
- `400 Bad Request` - Missing or invalid parameters
- `403 Forbidden` - Access denied or file not allowed  
- `404 Not Found` - File or directory not found
- `500 Internal Server Error` - System errors

## Logging

All download attempts and security violations are logged through Meza's logging system for security monitoring and incident response.

---

## Development Notes

- Code follows MediaWiki coding standards (tabs for indentation)
- Compatible with both standalone PHP and PHPUnit testing frameworks
- Extensively tested for security vulnerabilities
- Documented with comprehensive inline comments
