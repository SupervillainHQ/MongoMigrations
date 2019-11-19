<?php


namespace SupervillainHQ\MongoMigrations\Cli\Commands {


	use SupervillainHQ\MongoMigrations\Cli\CliCommand;
	use SupervillainHQ\MongoMigrations\Cli\Commands\SubCommands\ConfigInfo;
	use SupervillainHQ\MongoMigrations\Cli\Commands\SubCommands\ConfigTest;

	class Config implements CliCommand {

		/**
		 * @var string
		 */
		private $subCommand;

		function __construct(string $subCommand = null) {
			if(!is_null($subCommand)){
				switch ($subCommand){
					case 'test':
					default:
						$this->subCommand = new ConfigTest();
						break;
					case 'info':
						$this->subCommand = new ConfigInfo();
						break;
				}

			}
		}

		function execute(): int {
			if($this->subCommand instanceof CliCommand){
				return $this->subCommand->execute();
			}
			echo "Config command. Sub commands:\n";
			echo "  Config test\n";
			return 0;
		}

		function help() {
			echo "Mongo-migrations Config command help:\n";
		}
	}
}
