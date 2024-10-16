<?php

declare(strict_types=1);

$threshold = $argv[1] ?? 60;
$filename = $argv[2] ?? 'coverage.xml';

if (PHP_SAPI !== 'cli') {
    echo "\"bin/check-coverage\" should be invoked via the CLI version on PHP\n";

    exit(1);
}
if (!filter_var($threshold, FILTER_VALIDATE_INT)) {
    echo "Threshold parameter must be integer ({$threshold} given)\n";

    exit(1);
}

if (!file_exists($filename)) {
    echo "Coverage file \"{$filename}\" not found\n";

    exit(1);
}

$xmlContent = file_get_contents($filename);
try {
    $xml = new SimpleXMLElement($xmlContent);
} catch (Exception $e) {
    echo "Coverage file \"{$filename} is empty or broken\n";

    exit(1);
}

$elements = 0;
$coveredElements = 0;

$metrics = $xml->xpath('//metrics');

foreach ($metrics as $metric) {
    $elements += $metric['elements'];
    $coveredElements += $metric['coveredelements'];
}

$coverage = round(($coveredElements / $elements) * 100);


if ($coverage < $threshold) {
    echo "Code coverage is {$coverage}%, which is below the accepted {$threshold}%\n";

    exit(1);
}
echo "Code coverage is {$coverage}% - OK!";

exit(0);
