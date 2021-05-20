<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 10:18
 */

namespace Svhq\MongoMigrations\Cli\Commands {


    use Svhq\Core\Cli\CliCommand;
    use Svhq\MongoMigrations\Migrations\MigrationFile;
	use Svhq\MongoMigrations\Migrations\MigrationLog;
	use Svhq\MongoMigrations\MongoMigrationsCliApplication;
	use Svhq\MongoMigrations\Operations\ExecuteMigration;

	class Migrate implements CliCommand {

		function execute(): int {
			if(!MigrationLog::initiated()){
				$freshLogCollection = MigrationLog::initiate();
			}
			$migrationFiles = MigrationFile::listFiles();
			$migrationDir = MongoMigrationsCliApplication::migrationDir();
			echo "running migrations from dir {$migrationDir}\n";

			foreach ($migrationFiles as $migrationFile) {
				if($migrationFile instanceof MigrationFile){
					$op = new ExecuteMigration($migrationFile->collection());
					// ExecuteMigration instances return false if the collection already exists. This will happen for all
					// old files. Only new files will actually execute.
					$created = null;
					if($op->change() || isset($freshLogCollection)){
						// If the migration returns true, the migration created a new collection, so we must add a log entry
//						MigrationLog::createEntry($migrationFile->fileName(), $migrationFile->collection());
					}
					else{
						// If the collection already exists, we just need to verify that the migration entry also exists
						if(isset($freshLogCollection)){
							$created = null;
						}
						else{
							if($entry = MigrationLog::getEntry($migrationFile->fileName())){
								echo "skipped collection '{$migrationFile->collection()}' ({$migrationFile->fileName()})\n";
								continue;
							}
						}
					}
					MigrationLog::createEntry($migrationFile->fileName(), $migrationFile->collection(), $created);
					echo "created collection '{$migrationFile->collection()}' ({$migrationFile->fileName()})\n";
				}
			}
			return 0;
		}
	}
}
