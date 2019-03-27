<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 13:01
 */

namespace SupervillainHQ\MongoMigrations\Migrations {

	use MongoDB\BSON\ObjectId;
	use SupervillainHQ\MongoMigrations\Config\Config;
	use SupervillainHQ\MongoMigrations\Core\Document;

	class MigrationLogEntry extends Document {
		public $collection;
		public $name;
		public $creation;


		public static function createNew(string $name, string $collection, \DateTime $created = null):MigrationLogEntry{
			if(is_null($created)){
				$created = new \DateTime('now', new \DateTimeZone('UTC'));
			}

			$instance = new MigrationLogEntry();
			$instance->name = $name;
			$instance->collection = $collection;
			$instance->creation = $created;
			return $instance;
		}

		public static function all() {
			$instance = new MigrationLogEntry();
			$collection = $instance->getCollection();

			$entries = [];
			$findings = $collection->find([]);
			foreach ($findings as $entry) {
				$obj = clone $instance;
				self::parseBson($obj, $entry);
				array_push($entries, $obj);
			}
			return $entries;
		}

		public static function query(\stdClass $filter) {
			$instance = new MigrationLogEntry();
			$collection = $instance->getCollection();

			return $collection->find((array) $filter);
		}

		protected static function parseBson(&$instance, \ArrayObject $data){
			parent::parseBson($instance, $data);
			if(property_exists($instance->creation, 'date')){
				$instance->creation = \DateTime::createFromFormat('Y-m-d H:i:s', trim($instance->creation->date));
			}
		}


		public function getSource(): string {
			$collectionName = trim(Config::instance()->migrations->entries);
			return $collectionName;
		}


		/**
		 * Should update if exists, and create if not exists
		 * @return mixed
		 * @throws \Exception
		 */
		public function save() {
			$collection = $this->getCollection();
			$data = [
				'name' => $this->name,
				'collection' => $this->collection,
			];
			if($this->creation instanceof \DateTime){
				$data['creation'] = (object) [
					'date' => $this->creation->format('Y-m-d H:i:s')
				];
			}
			if($this->_id instanceof ObjectId){
				return $collection->updateOne(['_id' => $this->_id], ['$set' => $data]);
			}
			$result = $collection->insertOne($data);
			$this->_id = $result->getInsertedId();
			if($this->_id instanceof ObjectId){
				return $result;
			}
			throw new \Exception("Failed to save");
		}

		/**
		 * Should insert if not exists
		 * @return mixed
		 */
		public function update() {
			// TODO: Implement update() method.
		}

		/**
		 * Should fail if already exists
		 * @return mixed
		 */
		public function create() {
			// TODO: Implement create() method.
		}

		/**
		 * Should continue if not exists
		 * @return mixed
		 */
		public function delete() {
			// TODO: Implement delete() method.
		}
	}
}
