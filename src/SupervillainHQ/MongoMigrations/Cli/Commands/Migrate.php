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
	use SupervillainHQ\MongoMigrations\Migrations\MigrationLog;
	use SupervillainHQ\MongoMigrations\Operations\ExecuteMigration;

	class Migrate implements CliCommand {

		function execute(): int {
			if(!MigrationLog::initiated()){
				$freshLogCollection = MigrationLog::initiate();
			}
			$migrationFiles = MigrationFile::listFiles();
			foreach ($migrationFiles as $migrationFile) {
				if($migrationFile instanceof MigrationFile){
					$op = new ExecuteMigration($migrationFile->collection());
					// ExecuteMigration instances return false if the collection already exists. This will happen for all
					// old files. Only new files will actually execute.
					if($op->change() || isset($freshLogCollection)){
						// If the migration returns true, the migration created a new collection, so we must add a log entry
						MigrationLog::createEntry($migrationFile->fileName(), $migrationFile->collection());
					}
				}
			}
			return 0;
		}

		function help() {
			// TODO: Implement help() method.
		}
	}
}
