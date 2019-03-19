<?php
/**
 * stub.php.
 *
 * TODO: Documentation required!
 */

use SupervillainHQ\MongoMigrations\MongoMigrationsCliApplication;
use Commando\Command as NateGoodCommand;

$arguments = array_values($argv);

$path = array_shift($arguments);
$pharPath = dirname(realpath($path));
$projectPath = dirname($pharPath);
$vendorPath = "{$projectPath}/vendor";
include "{$vendorPath}/autoload.php";

$cmd = new NateGoodCommand();

// Can't use imported classes until we've determined where the vendor-dir is, so we need to reserve the first argument for
// something that allows us to locate the vendor-dir
//$cmd->option()
//	->require()
//	->describedAs("path to config.xml");

$cmd->option('v')
	->aka('verbose')
	->describedAs('When set, extended logging is enabled')
	->count(3);

$cmd->option('c')
	->aka('command')
	->require()
	->describedAs('Maintenance command name. Case-sensitive!')
	->argument();

$verbose = $cmd['verbose'];

if($verbose){
	echo "$pharPath\n";
	echo "$projectPath\n";
	echo "$vendorPath\n";
}

MongoMigrationsCliApplication::run("{$projectPath}/config/config.json", $cmd);

