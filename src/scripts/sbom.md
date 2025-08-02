# Software Bill of Materials (SBOM) for Meza

This directory contains automatically generated Software Bill of Materials (SBOM) files for the Meza project.

## Files

- `meza-sbom.spdx.json` - SPDX 2.3 format SBOM
- `meza-sbom.cyclonedx.json` - CycloneDX 1.4 format SBOM  
- `meza-sbom.txt` - Human-readable text format
- `package-stats.txt` - Package statistics and license summary

## Generation

SBOMs are automatically generated:

1. **On every push** that modifies `composer.lock` or `composer.json`
2. **Weekly** on Sundays at 2 AM UTC
3. **Manually** via GitHub Actions workflow dispatch

### Manual Generation

To generate SBOM files locally:

```bash
# Generate all formats
php scripts/generate-sbom.php

# Generate specific format
php scripts/generate-sbom.php --format spdx --output my-sbom.json

# Show package statistics
php scripts/generate-sbom.php --stats
```

## SBOM Formats

### SPDX (Software Package Data Exchange)

SPDX is an open standard for communicating software bill of materials information. The SPDX format includes:

- Package identification and versioning
- License information
- Security vulnerability data
- Relationship information

**Use cases:**
- License compliance
- Security vulnerability tracking
- Supply chain risk management

### CycloneDX

CycloneDX is designed for application security contexts and supply chain component analysis. It includes:

- Component inventory
- Dependency relationships
- License and copyright information
- Known vulnerabilities

**Use cases:**
- DevSecOps integration
- Dependency analysis
- Vulnerability management

### Text Format

Human-readable format showing:
- All dependencies organized by scope (runtime/development/platform)
- License information for each package
- Summary statistics

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

## License Compliance

The SBOM includes comprehensive license information for all dependencies. Key licenses include:

- **GPL-2.0-or-later**: MediaWiki and related components
- **MIT**: Many PHP libraries
- **Apache-2.0**: Various Apache Foundation projects
- **BSD-3-Clause**: BSD-licensed libraries

Review the license summary in the text format SBOM for complete details.

## Security Considerations

SBOMs are valuable for:

1. **Vulnerability Management**: Identify components with known security issues
2. **Supply Chain Security**: Track all software components and their sources
3. **Compliance**: Meet regulatory requirements for software transparency
4. **Risk Assessment**: Evaluate the security posture of dependencies

## Integration

These SBOM files can be integrated with:

- **Dependency scanning tools** (Dependabot, Snyk, etc.)
- **License compliance platforms**
- **Security information and event management (SIEM) systems**
- **Software composition analysis (SCA) tools**

## Updates

SBOMs are automatically updated when dependencies change. For the most current information, always refer to the latest generated files.

Last updated: [Generated timestamp in SBOM files]