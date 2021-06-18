<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 10:18
 */

namespace Svhq\MongoMigrations\Cli\Commands {


    use Svhq\Core\Cli\CliCommand;
    use Svhq\Core\Cli\CliParser;
    use Svhq\Core\Cli\Console;
    use Svhq\Core\Cli\ExitCodes;
    use Svhq\MongoMigrations\Migrations\MigrationFile;

	class Create implements CliCommand {

		/**
		 * @var string
		 */
		private string $collection;

		function __construct(string $collection = null) {
		    if(is_null($collection)){
                $collection = trim(CliParser::instance()->getArgumentValue('collection'));
            }
            if(is_null($collection)){
                throw new \InvalidArgumentException("Required argument 'collection' missing");
            }
			$this->collection = $collection;
		}

		function execute(): int {
			// create new migration file for repository sharing
			$migration = MigrationFile::create($this->collection);
			$migration->saveAsMson();
			Console::instance()->log("Migration file {$migration->fileName()} created");
			return ExitCodes::OK;
		}
	}
}
