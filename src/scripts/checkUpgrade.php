<?php
// where do we want to search for possible issues
// $codePath = '/opt/meza';
// Use the grandparent of whatever directory this script is in.
$codePath = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR ;
$codePath = realpath($codePath);
// echo $codePath . "\n"; exit();
$codePath = '/opt/htdocs/mediawiki/extensions';

// what are the MediaWiki config parameters affected in this upgrade
$changedConfigParametersREL1_39 = [
'$wgInvalidUsernameCharacters',
'$wgLocalFileRepo', 
'$wgLBFactoryConf', 
'$wgDBservers',
'$wgLBFactoryConf', 
'$wgObjectCaches' ];

$removedConfigParametersREL1_39 = [
'$wgMultiContentRevisionSchemaMigrationStage',
'$wgActorTableSchemaMigrationStage',
'$wgWikiFarmSiteDetector',
'$wgParserCacheUseJson',
'$wgAllowJavaUploads',
'$wgMaxRedirects',
'$wgElementTiming',
'$wgPriorityHints',
'$wgPriorityHintsRatio',
'$wgIncludeLegacyJavaScript',
'$wgLegacySchemaConversion',
'$wgInterwikiPrefixDisplayTypes',
'$wgMangleFlashPolicy' ];

// combine all our 'inputs' into one array
$changed = array_merge($changedConfigParametersREL1_39, $removedConfigParametersREL1_39);
// and sort them so it's easier to read the output
sort($changed);

// print a report header
echo "Searching for the following Configs in the codebase at $codePath:\n";
for ( $i=0, $size = count($changed), $n = 1; $i < $size; $i++, $n++ ) {
	echo "{$n}. " . $changed[$i] . "\n";
}
echo "\n";

// search for each config parameter
foreach ( $changed as $k => $v ) {
	$results[] = searchForConfigParameter($v, $codePath);
}

if ( $results ) {
//	var_dump ( $results );
	printResults( $results );
} else {
	echo "no Configs found\n";
}

// search for a string in a directory using recursion and iterators
function searchForConfigParameter ($string, $path) {
	$results = [];
	$dir = new RecursiveDirectoryIterator($path);
	foreach (new RecursiveIteratorIterator($dir) as $filename => $file) {
		// skip getting the 'contents' of directories
		if (is_dir($file)) { continue; }
		// skip THIS file because it would be a false positive
		if ( $file->getPathname() == __FILE__ ) { continue; }
		// use 'file()' so that we can even report the line number.
		$content = file($file->getPathname());
		foreach ($content as $k => $line) {
			if (strpos($line, $string) !== false) {
				$lineNumber = $k+1;
				echo "$string found in file: " . $file->getPathname() . " at line $lineNumber\n";
				$results[] = $string;
			}
		}
	}
	return $results;
}

function printResults ( array $results ) {
	$totalHits = 0;
	foreach ( $results as $matchSet ) {
		$hits = count ( $matchSet );
		if ( $hits ) {
			$totalHits += $hits;
			$config = $matchSet[0];
			printf ( "Config %s found %d times\n", $config, $hits );
			printf ( "See the manual for %s at https://www.mediawiki.org/wiki/Manual:%s\n", $config, $config ) ;
			unset( $config );
			unset( $hits );
		}
	}
	printf ( "Total hits: %d\n", $totalHits );
}