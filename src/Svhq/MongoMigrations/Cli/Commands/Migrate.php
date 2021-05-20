<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 10:18
 */

namespace Svhq\MongoMigrations\Cli\Commands {


    use Svhq\Core\Cli\CliCommand;
    use Svhq\Core\Cli\Console;
    use Svhq\Core\Cli\ExitCodes;
    use Svhq\MongoMigrations\Migrations\MigrationFile;
	use Svhq\MongoMigrations\Migrations\MigrationLog;
	use Svhq\MongoMigrations\MongoMigrationsCliApplication;
	use Svhq\MongoMigrations\Operations\ExecuteMigration;

	class Migrate implements CliCommand {

		function execute(): int {
            $forceCreate = !MigrationLog::initiated();
            if($forceCreate){
                MigrationLog::initiate();
			}
			$migrationFiles = MigrationFile::listFiles();
			$migrationDir = MongoMigrationsCliApplication::migrationDir();
			Console::instance()->log("running migrations from dir {$migrationDir}");

			foreach ($migrationFiles as $migrationFile) {
				if($migrationFile instanceof MigrationFile){
					$op = new ExecuteMigration($migrationFile->collection());
					// ExecuteMigration instances return false if the collection already exists. This will happen for all
					// old files. Only new files will actually execute.
					if(!$op->change() && !$forceCreate){
						// If the collection was already created we just need to verify that the migration log entry also
                        // exists
                        if(MigrationLog::getEntry($migrationFile->fileName())){
                            Console::instance()->log("skipped collection '{$migrationFile->collection()}' ({$migrationFile->fileName()})");
                            continue;
                        }
					}
					// The ExecuteMigration operation created its collection, so we log that in our migration-log
					MigrationLog::createEntry($migrationFile->fileName(), $migrationFile->collection());
					Console::instance()->log("created collection '{$migrationFile->collection()}' ({$migrationFile->fileName()})");
				}
			}
			return ExitCodes::OK;
		}
	}
}
