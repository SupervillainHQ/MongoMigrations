<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 10:18
 */

namespace SupervillainHQ\MongoMigrations\Cli\Commands {


	use SupervillainHQ\MongoMigrations\Cli\CliCommand;
	use SupervillainHQ\MongoMigrations\Migrations\MigrationFile;
	use SupervillainHQ\MongoMigrations\MongoMigrationsCliApplication;

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
