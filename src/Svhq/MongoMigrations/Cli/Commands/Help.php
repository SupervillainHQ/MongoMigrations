<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-01-22
 * Time: 14:24
 */

namespace Svhq\MongoMigrations\Cli\Commands {


    use Svhq\Core\Cli\CliCommand;
    use Svhq\Core\Cli\Console;
    use Svhq\Core\Cli\ExitCodes;

    /**
     * Class Help
     * @package Svhq\MongoMigrations\Cli\Commands
     * @HelpText("Custom Help Command")
     * @HelpDescription("Dscribes MongoMigration command")
     * @CommandDescription("php vendor/bin/mm help")
     */
    class Help implements CliCommand {


		function execute(): int {
			$this->help();
			return ExitCodes::OK;
		}

		function help() {
			Console::log("Mongo-migrations help:");
			Console::log("  Main command:");
		}
	}
}
