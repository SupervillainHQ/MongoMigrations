<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 13:01
 */

namespace Svhq\MongoMigrations\Migrations {

	use MongoDB\BSON\ObjectId;
    use Svhq\Core\Config\Config;
    use Svhq\Core\Mongo\Document;
    use Svhq\MongoMigrations\Cli\Util\MongoUtil;

    class MigrationLogEntry extends Document {
		public string $collection;
		public string $name;
		public ?\DateTime $creation;


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

		public static function one(\stdClass $filter) {
			$instance = new MigrationLogEntry();
			$collection = $instance->getCollection();

			if($found = $collection->findOne((array) $filter)) {
				$obj = clone $instance;
				self::parseBson($obj, $found);
				return $obj;
			}
			return null;
		}

		public static function query(\stdClass $filter) {
			$instance = new MigrationLogEntry();
			$collection = $instance->getCollection();

			return $collection->find((array) $filter);
		}

		protected static function parseBson(&$instance, $data, object $options = null, object $bindTypes = null):void{
			parent::parseBson($instance, $data);
			if(property_exists($instance->creation, 'date')){
				$instance->creation = \DateTime::createFromFormat('Y-m-d H:i:s', trim($instance->creation->date));
			}
		}

		public static function initiated():bool{
            $instance = new MigrationLogEntry();
			return MongoUtil::hasCollection($instance->getCollection());
		}

		public static function initiate(){
            $instance = new MigrationLogEntry();
		    return MongoUtil::initCollection($instance->getCollection());
		}


		public static function getSource(): string {
			return trim(Config::instance()->getMigrations('entries'));
		}

		/**
		 * @return \DateTime
		 */
		public function created():\DateTime{
			return $this->creation;
		}


		/**
		 * Should update if exists, and create if not exists
		 * @throws \Exception
		 */
		public function save():void{
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
				$collection->updateOne(['_id' => $this->_id], ['$set' => $data]);
				return;
			}
			$result = $collection->insertOne($data);
			$this->_id = $result->getInsertedId();
			if($this->_id instanceof ObjectId){
				return;
			}
			throw new \Exception("Failed to save");
		}

		/**
		 * Should insert if not exists
		 * @return mixed
		 */
		public function update():void {
			// TODO: Implement update() method.
		}

		/**
		 * Should fail if already exists
		 * @return mixed
		 */
		public function create():void{
			// TODO: Implement create() method.
		}

		/**
		 * Should continue if not exists
		 * @return mixed
		 */
		public function delete():void {
			// TODO: Implement delete() method.
		}
	}
}
