<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 10:18
 */

namespace Svhq\MongoMigrations\Cli\Commands {


    use Svhq\Core\Cli\CliCommand;
    use Svhq\MongoMigrations\Migrations\MigrationFile;

	class Create implements CliCommand {

		/**
		 * @var string
		 */
		private $collection;

		function __construct(string $collection) {
			$this->collection = $collection;
		}

		function execute(): int {
			// create new migration file for repository sharing
			$migration = MigrationFile::create($this->collection);
			$migration->saveAsMson();
			return 0;
		}

		function help() {
			echo "Mongo-migrations Create command help:\n";
		}
	}
}
