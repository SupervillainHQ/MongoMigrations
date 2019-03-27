<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 12:56
 */

namespace SupervillainHQ\MongoMigrations\Migrations {



	class MigrationLog {

		private $entries;


		static function load():MigrationLog{
			$instance = new MigrationLog();
			$instance->entries = MigrationLogEntry::all();
			return $instance;
		}

		public static function createEntry(string $name, string $collectionName):MigrationLogEntry{
			$instance = MigrationLogEntry::createNew($name, $collectionName);
			$instance->save();
			return $instance;
		}

		function entries():array {
			return $this->entries;
		}

		public function hasMigration(\stdClass $filter):bool{
			$results = MigrationLogEntry::query($filter);
			$list = $results->toArray();
			$has = count($list) > 0;
			return $has;
		}
	}
}
