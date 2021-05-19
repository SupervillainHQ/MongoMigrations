<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 12:12
 */

namespace Svhq\MongoMigrations\Cli\Commands {


    use Svhq\Core\Cli\CliCommand;

    class Init implements CliCommand {

		function execute(): int {
			// create config + auth file and a migration-list/status collection in the auth-database
			$basePath = Config::instance()->basePath();
			$migrationsPath = Config::instance()->migrations->path;
			$mods = [];
			$status = [];

			if(!is_dir($migrationsPath)){
				mkdir($migrationsPath);
				$mods[] = "migrations directory created at '{$migrationsPath}'";
			}
			return 0;
		}

		function help() {
			// TODO: Implement help() method.
		}
	}
}
