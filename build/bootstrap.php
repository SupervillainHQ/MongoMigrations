<?php
/**
 * stub.php.
 *
 * TODO: Documentation required!
 */

use SupervillainHQ\MongoMigrations\MongoMigrationsCliApplication;

$arguments = array_values($argv);

// Get our file-system bearings
$path = array_shift($arguments);
$pharPath = dirname(realpath($path));
$projectPath = dirname($pharPath);
// avoid trying to include an autoload.php from inside the phar file
if(false !== strpos($projectPath, '/vendor')){
    $paths = explode('/', ltrim($projectPath, '/'));
    $projectPath = '/';
    while($path = array_shift($paths)){
        if('vendor' == $path){
            break;
        }
        $projectPath = str_replace('//', '/', "{$projectPath}/{$path}");
    }
}
$vendorPath = "{$projectPath}/vendor";

// Require a config file path
$configPath = "{$projectPath}/config/core.json";
if($cfgPath = array_shift($arguments)){
    if(0 === strpos($cfgPath, '/')){
        $cfgPath = realpath($cfgPath);
    }
    else{
        $cfgPath = realpath("{$vendorPath}/{$cfgPath}");
    }
}
if(!$cfgPath){
    $cfgPath = $configPath;
}
$configPath = $cfgPath;
#var_dump($argv);
#echo "config-file: {$configPath}\n";

if($autoloadPath = realpath("{$vendorPath}/autoload.php")){
    include $autoloadPath;
//    $returnCode = SvhqCoreCliApplication::run($configPath);
    $returnCode = MongoMigrationsCliApplication::run($configPath);
    exit($returnCode);
}
echo "failed to load autoloader ('{$vendorPath}/autoload.php')";

