<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 12:12
 */

namespace Svhq\MongoMigrations\Cli\Commands {

    use Svhq\Core\Cli\CliCommand;
    use Svhq\Core\Cli\Console;
    use Svhq\Core\Cli\ExitCodes;
    use Svhq\MongoMigrations\Cli\Util\MigrationsDirectoryUtil;
    use Svhq\MongoMigrations\Cli\Util\MongoUtil;
    use Svhq\MongoMigrations\MongoMigrationsCliApplication;

    /**
     * Class Init
     * @package Svhq\MongoMigrations\Cli\Commands
     * @HelpText("Init Command sets up MongoMigrations in your current project")
     * @HelpDescription("Sets up MongoMigrations in your current project")
     * @CommandDescription("php vendor/bin/mm init")
     */
    class Init implements CliCommand {

		function execute(): int {
			// create config + auth file and a migration-list/status collection in the auth-database
			$migrationsPath = MongoMigrationsCliApplication::migrationDir();
			$migrationsCollection = MongoMigrationsCliApplication::logCollection();
			$mods = [];
			$status = [];

			$dirUtil = new MigrationsDirectoryUtil($migrationsPath);
			switch($dirUtil->init()){
                case MigrationsDirectoryUtil::CREATED:
				    $mods[] = "migrations directory created at '{$migrationsPath}'";
				    break;
                case MigrationsDirectoryUtil::EXISTS:
                    $mods[] = "migrations directory already exists ('{$migrationsPath}')";
                    break;
            }

            try{
                if(MongoUtil::verifyDbAccess()){
                    if(!MongoUtil::hasCollection($migrationsCollection)){
                        $entries = MongoUtil::initCollection($migrationsCollection);
                        $mods[] = isset($entries) ? "migrations log entries collection verified" : "unable to create migrations log entries collection";
                    }
                    else{
                        $mods[] = "migrations log entries collection verified";
                    }
                }
                else{
                    $mods[] = "mongo database inaccessible";
                }
            }
            catch (\Exception $exception){
                $mods[] = "mongo access verification failed: {$exception->getMessage()}";
            }

            foreach ($mods as $mod) {
                Console::log($mod);
			}
			return ExitCodes::OK;
		}

		function help() {
			// TODO: Implement help() method.
		}
	}
}
