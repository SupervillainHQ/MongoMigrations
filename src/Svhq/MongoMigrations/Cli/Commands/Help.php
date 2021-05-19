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

    class Help implements CliCommand {


		function execute(): int {
			$this->help();
			return 0;
		}

		function help() {
			Console::log("Mongo-migrations help:");
			Console::log("  Main command:");
		}
	}
}
