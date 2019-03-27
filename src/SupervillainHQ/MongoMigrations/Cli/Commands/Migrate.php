<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 10:18
 */

namespace SupervillainHQ\MongoMigrations\Cli\Commands {


	use SupervillainHQ\MongoMigrations\Cli\CliCommand;
	use SupervillainHQ\MongoMigrations\Migrations\MigrationFile;

	class Migrate implements CliCommand {

		function execute(): int {
			$migrations = MigrationFile::listFiles();
			return 0;
		}

		function help() {
			// TODO: Implement help() method.
		}
	}
}
