<?php
/**
 * stub.php.
 *
 * TODO: Documentation required!
 */

use Svhq\MongoMigrations\MongoMigrationsCliApplication;

$arguments = array_values($argv);

$path = array_shift($arguments);
$pharPath = dirname(realpath($path));

$projectPath = dirname($pharPath);
$vendorPos = strpos($projectPath, '/vendor');
if(false !== $vendorPos){
    $projectPath = rtrim(substr($projectPath, 0, $vendorPos), '/');
}
$vendorPath = "{$projectPath}/vendor";
include "{$vendorPath}/autoload.php";

$localConfig = "{$projectPath}/mm.json";
$localConfigPath = realpath($localConfig);
$configPath = null;

if(is_readable($localConfigPath) && is_file($localConfigPath)){
    $configPath = $localConfigPath;
}

if(is_null($configPath)){
    echo "Invalid config path\n";
    echo "(local-path: {$localConfig})\n";
    echo "(project: {$projectPath})\n";
    echo "(exec: {$pharPath})\n";
    echo "(vendor: {$vendorPath})\n";
    return 0;
}
$returnCode = MongoMigrationsCliApplication::run($configPath);
exit($returnCode);

