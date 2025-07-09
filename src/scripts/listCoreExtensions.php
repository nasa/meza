<?php
/**
 * This script will list the extensions found in Meza 'Core' extensions.
 * It relies on PHP's native 'yaml' parser which is not always included.
 * You can add it to your host with dnf install php-yaml or the same with apt.
 */


// Read the YAML file
$yamlContent = file_get_contents('/opt/meza/config/MezaCoreExtensions.yml');

// Parse the YAML content
$extensions = yaml_parse($yamlContent);

// Extract the name elements
$names = [];
foreach ($extensions['list'] as $extension) {
    if (isset($extension['name'])) {
        $names[] = $extension['name'];
    }
}

// Alphabetize the names
sort($names, SORT_STRING | SORT_FLAG_CASE);

// Output the alphabetized list
echo "MediaWiki Extensions:\n";
foreach ($names as $name) {
    echo "- $name\n";
}
