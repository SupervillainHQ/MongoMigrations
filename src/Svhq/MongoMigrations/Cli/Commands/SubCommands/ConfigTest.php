<?php


namespace Svhq\MongoMigrations\Cli\Commands\SubCommands {


    use Svhq\Core\Cli\CliCommand;
    use Svhq\Core\Cli\ExitCodes;

    class ConfigTest implements CliCommand {

		function execute(): int {
		    // TODO: figure out what to test
			return ExitCodes::OK;
		}
	}
}
