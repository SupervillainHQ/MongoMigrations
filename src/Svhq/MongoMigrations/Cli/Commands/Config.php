<?php


namespace Svhq\MongoMigrations\Cli\Commands {


    use Svhq\Core\Cli\CliCommand;
    use Svhq\Core\Cli\CliParser;
    use Svhq\Core\Cli\Console;
    use Svhq\Core\Cli\ExitCodes;
    use Svhq\MongoMigrations\Cli\Commands\SubCommands\ConfigInfo;
	use Svhq\MongoMigrations\Cli\Commands\SubCommands\ConfigTest;

	class Config implements CliCommand {

		/**
		 * @var string
		 */
		private $subCommand;

		function __construct(string $subCommand = null) {
			if(is_null($subCommand)){
                $subCommand = CliParser::instance()->getCommand(1);
            }

            switch ($subCommand->value()){
                case 'test':
                default:
                    $this->subCommand = new ConfigTest();
                    break;
                case 'info':
                    $this->subCommand = new ConfigInfo();
                    break;
            }
        }

		function execute(): int {
			if($this->subCommand instanceof CliCommand){
				return $this->subCommand->execute();
			}
			Console::instance()
                ->log("Config command. Sub commands:")
                ->log("  info")
                ->log("  test");
			return ExitCodes::OK;
		}
	}
}
