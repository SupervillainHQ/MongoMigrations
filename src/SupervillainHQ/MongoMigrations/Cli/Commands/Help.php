<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-01-22
 * Time: 14:24
 */

namespace SupervillainHQ\MongoMigrations\Cli\Commands {


	use SupervillainHQ\MongoMigrations\Cli\CliCommand;

	class Help implements CliCommand {


		function execute(): int {
			$this->help();
			return 0;
		}

		function help() {
			echo "Mongo-migrations help:\n";
			echo "  Main command:\n";
		}
	}
}
