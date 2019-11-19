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

		/**
		 * @param string $name
		 * @return MigrationLogEntry|null
		 */
		public static function getEntry(string $name){
			return MigrationLogEntry::one((object) ['name' => $name]);
		}

		public static function createEntry(string $name, string $collectionName, \DateTime $created = null):MigrationLogEntry{
			$instance = MigrationLogEntry::createNew($name, $collectionName, $created);
			$instance->save();
			return $instance;
		}

		public static function initiated():bool{
			return MigrationLogEntry::initiated();
		}

		public static function initiate() {
			return MigrationLogEntry::initiate();
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
