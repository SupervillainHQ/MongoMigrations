<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-01-22
 * Time: 14:24
 */

namespace SupervillainHQ\MongoMigrations\Cli\Commands {


	use SupervillainHQ\MongoMigrations\Cli\CliCommand;

	class HelpCommand implements CliCommand {


		function execute(): int {
			echo "Mongo-migrations help:\n";
			echo "  Main command:\n";
			return 0;
		}
	}
}
