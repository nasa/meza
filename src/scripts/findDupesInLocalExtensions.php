<?php
// Function to extract names from YAML file
function extractExtensionNames($filePath) {
    // Read the YAML file
    $yamlContent = file_get_contents($filePath);
    
    // Parse the YAML content
    $extensions = yaml_parse($yamlContent);
    
    // Extract the name elements
    $names = [];
    if (isset($extensions['list'])) {
        foreach ($extensions['list'] as $extension) {
            if (isset($extension['name'])) {
                $names[] = $extension['name'];
            }
        }
    }
    
    // Alphabetize the names
    sort($names, SORT_STRING | SORT_FLAG_CASE);
    
    return $names;
}

// Process the first file
$file1 = '/opt/meza/config/MezaCoreExtensions.yml';
$names1 = extractExtensionNames($file1);

// Process the second file
$file2 = '/opt/conf-meza/public/MezaLocalExtensions.yml';
$names2 = extractExtensionNames($file2);

// Find duplicates (extensions that appear in both files)
$duplicates = array_intersect($names1, $names2);

// Output results
echo "Extensions in Core (" . count($names1) . "):\n";
foreach ($names1 as $name) {
    echo "- $name\n";
}

echo "\nExtensions included 'locally' (" . count($names2) . "):\n";
foreach ($names2 as $name) {
    echo "- $name\n";
}

echo "\nDuplicate extensions (" . count($duplicates) . "):\n";
foreach ($duplicates as $name) {
    echo "- $name\n";
}
