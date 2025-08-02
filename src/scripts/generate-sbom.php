<?php

/**
 * Generate Software Bill of Materials (SBOM) for Meza project
 * 
 * This script parses composer.lock and generates SBOM in multiple formats:
 * - SPDX JSON format
 * - CycloneDX JSON format
 * - Human-readable format
 */

class SBOMGenerator
{
    private array $composerData;
    private array $packages = [];
    private string $projectName;
    private string $projectVersion;

    public function __construct(string $composerLockPath, string $projectName = 'Meza', string $projectVersion = '43.20.0')
    {
        $this->projectName = $projectName;
        $this->projectVersion = $projectVersion;
        
        if (!file_exists($composerLockPath)) {
            throw new Exception("Composer lock file not found: $composerLockPath");
        }
        
        $content = file_get_contents($composerLockPath);
        $this->composerData = json_decode($content, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Invalid JSON in composer.lock: " . json_last_error_msg());
        }
        
        $this->parsePackages();
    }

    private function parsePackages(): void
    {
        // Parse main packages
        if (isset($this->composerData['packages'])) {
            foreach ($this->composerData['packages'] as $package) {
                $this->packages[] = $this->extractPackageInfo($package, 'runtime');
            }
        }
        
        // Parse dev packages
        if (isset($this->composerData['packages-dev'])) {
            foreach ($this->composerData['packages-dev'] as $package) {
                $this->packages[] = $this->extractPackageInfo($package, 'development');
            }
        }
        
        // Add platform requirements
        if (isset($this->composerData['platform'])) {
            foreach ($this->composerData['platform'] as $name => $version) {
                $this->packages[] = [
                    'name' => $name,
                    'version' => $version === '*' ? 'any' : $version,
                    'type' => 'platform',
                    'scope' => 'runtime',
                    'license' => $this->getPlatformLicense($name),
                    'description' => $this->getPlatformDescription($name),
                    'homepage' => null,
                    'repository' => null,
                    'authors' => [],
                    'source' => null,
                    'dist' => null
                ];
            }
        }
    }

    private function extractPackageInfo(array $package, string $scope): array
    {
        return [
            'name' => $package['name'] ?? 'unknown',
            'version' => $package['version'] ?? 'unknown',
            'type' => $package['type'] ?? 'library',
            'scope' => $scope,
            'license' => $package['license'] ?? [],
            'description' => $package['description'] ?? '',
            'homepage' => $package['homepage'] ?? null,
            'repository' => $package['source']['url'] ?? null,
            'authors' => $package['authors'] ?? [],
            'source' => $package['source'] ?? null,
            'dist' => $package['dist'] ?? null,
            'time' => $package['time'] ?? null,
            'keywords' => $package['keywords'] ?? [],
            'support' => $package['support'] ?? []
        ];
    }

    private function getPlatformLicense(string $name): array
    {
        if (str_starts_with($name, 'php')) {
            return ['PHP-3.01'];
        }
        if (str_starts_with($name, 'ext-')) {
            return ['PHP-3.01']; // Most PHP extensions use PHP license
        }
        return ['Unknown'];
    }

    private function getPlatformDescription(string $name): string
    {
        if ($name === 'php') {
            return 'PHP runtime';
        }
        if (str_starts_with($name, 'ext-')) {
            $ext = substr($name, 4);
            return "PHP $ext extension";
        }
        return "Platform requirement: $name";
    }

    public function generateSPDXJson(): array
    {
        $spdx = [
            'spdxVersion' => 'SPDX-2.3',
            'dataLicense' => 'CC0-1.0',
            'SPDXID' => 'SPDXRef-DOCUMENT',
            'name' => $this->projectName . ' SBOM',
            'documentNamespace' => 'https://meza.org/sbom/' . uniqid(),
            'creationInfo' => [
                'created' => date('c'),
                'creators' => ['Tool: Meza SBOM Generator'],
                'licenseListVersion' => '3.21'
            ],
            'packageVerificationCode' => [
                'packageVerificationCodeValue' => hash('sha1', json_encode($this->packages))
            ],
            'packages' => []
        ];

        // Add root package
        $spdx['packages'][] = [
            'SPDXID' => 'SPDXRef-Package-' . $this->projectName,
            'name' => $this->projectName,
            'versionInfo' => $this->projectVersion,
            'downloadLocation' => 'https://github.com/enterprisemediawiki/meza',
            'filesAnalyzed' => false,
            'licenseConcluded' => 'GPL-3.0-or-later',
            'licenseDeclared' => 'GPL-3.0-or-later',
            'copyrightText' => 'Copyright Enterprise MediaWiki',
            'supplier' => 'Organization: Enterprise MediaWiki'
        ];

        // Add dependencies
        foreach ($this->packages as $package) {
            $spdxPackage = [
                'SPDXID' => 'SPDXRef-Package-' . str_replace(['/', '-', '.'], '_', $package['name']),
                'name' => $package['name'],
                'versionInfo' => $package['version'],
                'downloadLocation' => $package['repository'] ?? 'NOASSERTION',
                'filesAnalyzed' => false,
                'licenseConcluded' => $this->formatLicenses($package['license']),
                'licenseDeclared' => $this->formatLicenses($package['license']),
                'copyrightText' => 'NOASSERTION',
                'externalRefs' => []
            ];

            if ($package['homepage']) {
                $spdxPackage['externalRefs'][] = [
                    'referenceCategory' => 'OTHER',
                    'referenceType' => 'website',
                    'referenceLocator' => $package['homepage']
                ];
            }

            $spdx['packages'][] = $spdxPackage;
        }

        return $spdx;
    }

    public function generateCycloneDXJson(): array
    {
        $cycloneDx = [
            'bomFormat' => 'CycloneDX',
            'specVersion' => '1.4',
            'serialNumber' => 'urn:uuid:' . $this->generateUuid(),
            'version' => 1,
            'metadata' => [
                'timestamp' => date('c'),
                'tools' => [
                    [
                        'vendor' => 'Meza',
                        'name' => 'SBOM Generator',
                        'version' => '1.0.0'
                    ]
                ],
                'component' => [
                    'type' => 'application',
                    'bom-ref' => $this->projectName,
                    'name' => $this->projectName,
                    'version' => $this->projectVersion,
                    'description' => 'MediaWiki Enterprise Application Platform',
                    'licenses' => [
                        ['license' => ['id' => 'GPL-3.0-or-later']]
                    ]
                ]
            ],
            'components' => []
        ];

        foreach ($this->packages as $package) {
            $component = [
                'type' => $this->mapPackageType($package['type']),
                'bom-ref' => $package['name'] . '@' . $package['version'],
                'name' => $package['name'],
                'version' => $package['version'],
                'scope' => $package['scope'] === 'development' ? 'optional' : 'required'
            ];

            if (!empty($package['description'])) {
                $component['description'] = $package['description'];
            }

            if (!empty($package['license'])) {
                $component['licenses'] = array_map(function($license) {
                    return ['license' => ['id' => $license]];
                }, is_array($package['license']) ? $package['license'] : [$package['license']]);
            }

            if ($package['homepage']) {
                $component['externalReferences'] = [
                    [
                        'type' => 'website',
                        'url' => $package['homepage']
                    ]
                ];
            }

            if ($package['repository']) {
                $component['externalReferences'][] = [
                    'type' => 'vcs',
                    'url' => $package['repository']
                ];
            }

            $cycloneDx['components'][] = $component;
        }

        return $cycloneDx;
    }

    public function generateHumanReadable(): string
    {
        $output = "Software Bill of Materials (SBOM) for {$this->projectName}\n";
        $output .= str_repeat("=", 60) . "\n";
        $output .= "Generated: " . date('Y-m-d H:i:s T') . "\n";
        $output .= "Total Components: " . count($this->packages) . "\n\n";

        // Group by scope
        $byScope = [];
        foreach ($this->packages as $package) {
            $byScope[$package['scope']][] = $package;
        }

        foreach (['runtime', 'development', 'platform'] as $scope) {
            if (empty($byScope[$scope])) continue;
            
            $output .= strtoupper($scope) . " DEPENDENCIES (" . count($byScope[$scope]) . ")\n";
            $output .= str_repeat("-", 40) . "\n";

            usort($byScope[$scope], fn($a, $b) => strcmp($a['name'], $b['name']));

            foreach ($byScope[$scope] as $package) {
                $output .= sprintf("%-40s %s\n", $package['name'], $package['version']);
                
                if (!empty($package['license'])) {
                    $licenses = is_array($package['license']) ? implode(', ', $package['license']) : $package['license'];
                    $output .= "  License: $licenses\n";
                }
                
                if (!empty($package['description'])) {
                    $output .= "  Description: " . substr($package['description'], 0, 80) . "\n";
                }
                
                $output .= "\n";
            }
            $output .= "\n";
        }

        // License summary
        $licenses = [];
        foreach ($this->packages as $package) {
            if (empty($package['license'])) continue;
            $packageLicenses = is_array($package['license']) ? $package['license'] : [$package['license']];
            foreach ($packageLicenses as $license) {
                $licenses[$license] = ($licenses[$license] ?? 0) + 1;
            }
        }

        $output .= "LICENSE SUMMARY\n";
        $output .= str_repeat("-", 40) . "\n";
        arsort($licenses);
        foreach ($licenses as $license => $count) {
            $output .= sprintf("%-30s %d packages\n", $license, $count);
        }

        return $output;
    }

    private function formatLicenses(array $licenses): string
    {
        if (empty($licenses)) {
            return 'NOASSERTION';
        }
        if (count($licenses) === 1) {
            return $licenses[0];
        }
        return '(' . implode(' OR ', $licenses) . ')';
    }

    private function mapPackageType(string $type): string
    {
        $mapping = [
            'library' => 'library',
            'mediawiki-extension' => 'library',
            'mediawiki-skin' => 'library',
            'composer-plugin' => 'library',
            'platform' => 'library',
            'project' => 'application'
        ];
        return $mapping[$type] ?? 'library';
    }

    private function generateUuid(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function saveToFile(string $format, string $filename): void
    {
        switch (strtolower($format)) {
            case 'spdx':
                $data = json_encode($this->generateSPDXJson(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                break;
            case 'cyclonedx':
                $data = json_encode($this->generateCycloneDXJson(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                break;
            case 'text':
                $data = $this->generateHumanReadable();
                break;
            default:
                throw new Exception("Unsupported format: $format");
        }

        file_put_contents($filename, $data);
        echo "SBOM saved to: $filename\n";
    }

    public function getPackageStats(): array
    {
        $stats = [
            'total' => count($this->packages),
            'by_scope' => [],
            'by_type' => [],
            'by_license' => []
        ];

        foreach ($this->packages as $package) {
            $stats['by_scope'][$package['scope']] = ($stats['by_scope'][$package['scope']] ?? 0) + 1;
            $stats['by_type'][$package['type']] = ($stats['by_type'][$package['type']] ?? 0) + 1;
            
            $licenses = is_array($package['license']) ? $package['license'] : [$package['license']];
            foreach ($licenses as $license) {
                if ($license) {
                    $stats['by_license'][$license] = ($stats['by_license'][$license] ?? 0) + 1;
                }
            }
        }

        return $stats;
    }
}

// CLI execution
if (php_sapi_name() === 'cli') {
    $options = getopt('f:o:h', ['format:', 'output:', 'help', 'stats']);
    
    if (isset($options['h']) || isset($options['help'])) {
        echo "Usage: php generate-sbom.php [options]\n";
        echo "Options:\n";
        echo "  -f, --format FORMAT   Output format: spdx, cyclonedx, text (default: all)\n";
        echo "  -o, --output FILE     Output file (default: auto-generated)\n";
        echo "  --stats               Show package statistics\n";
        echo "  -h, --help            Show this help\n";
        exit(0);
    }

    try {
        // $composerLockPath = __DIR__ . '/../composer.lock';
		// hardcoded path for Meza project
        $composerLockPath = '/opt/htdocs/mediawiki/composer.lock';
        $generator = new SBOMGenerator($composerLockPath, 'Meza');
        
        if (isset($options['stats'])) {
            $stats = $generator->getPackageStats();
            echo "Package Statistics:\n";
            echo "Total packages: {$stats['total']}\n\n";
            
            echo "By scope:\n";
            foreach ($stats['by_scope'] as $scope => $count) {
                echo "  $scope: $count\n";
            }
            
            echo "\nBy type:\n";
            foreach ($stats['by_type'] as $type => $count) {
                echo "  $type: $count\n";
            }
            
            echo "\nTop licenses:\n";
            arsort($stats['by_license']);
            $top = array_slice($stats['by_license'], 0, 10, true);
            foreach ($top as $license => $count) {
                echo "  $license: $count\n";
            }
            exit(0);
        }

        $format = $options['f'] ?? $options['format'] ?? 'all';
        $outputBase = $options['o'] ?? $options['output'] ?? 'meza-sbom';

        if ($format === 'all') {
            $generator->saveToFile('spdx', $outputBase . '.spdx.json');
            $generator->saveToFile('cyclonedx', $outputBase . '.cyclonedx.json');
            $generator->saveToFile('text', $outputBase . '.txt');
        } else {
            $extension = $format === 'text' ? 'txt' : 'json';
            $filename = strpos($outputBase, '.') ? $outputBase : "$outputBase.$format.$extension";
            $generator->saveToFile($format, $filename);
        }

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        exit(1);
    }
}