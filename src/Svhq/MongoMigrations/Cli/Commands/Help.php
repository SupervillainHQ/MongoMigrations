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
			Console::instance()
                ->log("Mongo-migrations help.")
                ->log("Syntax:")
                ->log("  mm <sub-command>[ ..<options>]")
                ->log("")
                ->log("Sub-commands:")
                ->log("  mm help")
                ->log("  mm init")
                ->log("  mm migrate")
                ->log("  mm config")
                ->log("  mm create")
			;
		}
	}
}
