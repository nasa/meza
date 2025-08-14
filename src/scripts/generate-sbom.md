# Software Bill of Materials (SBOM) for Meza

This directory contains automatically generated Software Bill of Materials (SBOM) files for the Meza project.

## Files

- `meza-sbom.spdx.json` - SPDX 2.3 format SBOM
- `meza-sbom.cyclonedx.json` - CycloneDX 1.4 format SBOM  
- `meza-sbom.txt` - Human-readable text format
- `meza-sbom-stats.txt` - Package statistics and license summary

## Generation

SBOMs are generated from both `composer.lock` and `composer.local.lock` (if present), manually.

You should generate a new SBOM if your code changes will impact these files. We could do this automatically
however these files are not tracked in this repository thus we'll use a manual procedure for now.

1. **Manually** in a controller or deployed web node, execute the generate-sbom.php script.

### Dependency Sources

The SBOM generator analyzes multiple files to create a comprehensive inventory:

- **`composer.lock`**: Primary dependency file with locked versions
- **`composer.local.lock`**: Local overrides and additional dependencies (if present)

When both files contain the same package, the version from `composer.lock` takes precedence. Additional packages from `composer.local.lock` are included with a `[LOCAL]` indicator in the text format.

### Manual Generation

To generate SBOM files locally:

```bash
# Output will be in the directory where you execute the script
cd /opt/meza/src/scripts

# Generate all formats (automatically detects composer.local.lock)
php generate-sbom.php

# Generate specific format
php generate-sbom.php --format spdx --output my-sbom.json

# Show package statistics including source file breakdown
php generate-sbom.php --stats
```

## SBOM Formats

### SPDX (Software Package Data Exchange)

SPDX is an open standard for communicating software bill of materials information. The SPDX format includes:

- Package identification and versioning
- License information
- Security vulnerability data
- Relationship information
- Source file tracking (composer.lock vs composer.local.lock)

**Use cases:**
- License compliance
- Security vulnerability tracking
- Supply chain risk management

### CycloneDX

CycloneDX is designed for application security contexts and supply chain component analysis. It includes:

- Component inventory with source file metadata
- Dependency relationships
- License and copyright information
- Known vulnerabilities
- Custom properties for Meza-specific data

**Use cases:**
- DevSecOps integration
- Dependency analysis
- Vulnerability management

### Text Format

Human-readable format showing:
- All dependencies organized by scope (runtime/development/platform)
- Source file indicators (`[LOCAL]` for composer.local.lock packages)
- License information for each package
- Summary statistics by source file

## Package Categories

### Runtime Dependencies
Core packages required for Meza to function in production:
- MediaWiki core and extensions
- PHP libraries and frameworks
- Database and search components

### Development Dependencies
Packages used only during development and testing:
- Testing frameworks (PHPUnit)
- Code quality tools (PHPCS, Phan)
- Development utilities

### Platform Dependencies
System-level requirements:
- PHP runtime and extensions
- Operating system requirements

### Local Dependencies
Additional packages from `composer.local.lock`:
- Environment-specific overrides
- Development customizations
- Local testing dependencies

## License Compliance

The SBOM includes comprehensive license information for all dependencies from both composer files. Key licenses include:

- **GPL-2.0-or-later**: MediaWiki and related components
- **MIT**: Many PHP libraries
- **Apache-2.0**: Various Apache Foundation projects
- **BSD-3-Clause**: BSD-licensed libraries

Review the license summary in the text format SBOM for complete details.

## Security Considerations

SBOMs are valuable for:

1. **Vulnerability Management**: Identify components with known security issues across all dependency sources
2. **Supply Chain Security**: Track all software components and their sources (including local modifications)
3. **Compliance**: Meet regulatory requirements for software transparency
4. **Risk Assessment**: Evaluate the security posture of both standard and local dependencies

## Integration

These SBOM files can be integrated with:

- **Dependency scanning tools** (Dependabot, Snyk, etc.)
- **License compliance platforms**
- **Security information and event management (SIEM) systems**
- **Software composition analysis (SCA) tools**

## Updates

SBOMs are manually updated when dependencies change in either `composer.lock` or `composer.local.lock`. For the most current information, always refer to the latest generated files.

The generator will note when packages exist in both files and which version is being used in the final SBOM.

Last updated: [Generated timestamp in SBOM files]