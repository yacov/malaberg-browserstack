<?php

require 'vendor/autoload.php';
use Symfony\Component\Yaml\Yaml;

$config_file = getenv('CONFIG_FILE') ?: 'config/single.conf.yml';
$CONFIG = Yaml::parseFile($config_file)["default"]["suites"]["default"]["contexts"][0]["FeatureContext"]["parameters"];

$procs = [];

foreach ($CONFIG['environments'] as $key => $value) {
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        // Windows
        $cmd = "set TASK_ID=$key && ./vendor/bin/behat --config=" . getenv("CONFIG_FILE") . " 2>&1";
    } else {
        // Linux or Mac
        $cmd = "TASK_ID=$key ./vendor/bin/behat --config=" . getenv("CONFIG_FILE") . " 2>&1";
    }
    echo "Executing: $cmd\n";

    $procs[$key] = popen($cmd, "r");
}

foreach ($procs as $key => $proc) {
    while (!feof($proc)) {
        echo fgets($proc, 4096);
    }
    pclose($proc);
}

?>
