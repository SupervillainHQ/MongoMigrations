<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 10:59
 */

namespace SupervillainHQ\MongoMigrations\Core {

	use MongoDB\Collection;
	use MongoDB\Database;
	use MongoDB\UpdateResult;
	use Phalcon\Di;

	abstract class Document implements MongoDocument{

		public static $defaultDatabaseID = 'mongo';

		public $_id;
		private $databaseID;

		/**
		 * Document constructor.
		 * @param string $databaseID The ID of the database-description, defined as a dependency in your app
		 */
		function __construct(string $databaseID = null){
			if(is_null($databaseID)){
				$databaseID = self::$defaultDatabaseID;
			}
			$this->databaseID = $databaseID;
		}


		static public function assign(Document &$document, \stdClass $data){
			$reflector = new \ReflectionClass($document);
			$props = get_object_vars($data);
			foreach ($props as $prop => $value) {
				if($reflector->hasProperty($prop)){
					$document->{$prop} = $value;
				}
			}
		}


		public static function hasCollection(){
			$class = get_called_class();
			$instance = new $class();
			$mongo = Di::getDefault()->get($instance->databaseID);
			if($mongo instanceof Database) {
				$collections = $mongo->listCollections();
				$collectionName = $instance->getSource();

				foreach ($collections as $collection) {
					if($collection->getName() == $collectionName){
						return true;
					}
				}
			}
			return false;
		}

		protected static function initCollection(){
			$class = get_called_class();
			$instance = new $class();
			$mongo = Di::getDefault()->get($instance->databaseID);
			if($mongo instanceof Database) {
				$collectionName = $instance->getSource();
				if(strlen($collectionName)){
					$collection = $mongo->createCollection($collectionName);
					return $collection;
				}
			}
			return null;
		}

		protected function getCollection(){
			$mongo = Di::getDefault()->get($this->databaseID);
			if($mongo instanceof Database) {
				$collection = $mongo->selectCollection($this->getSource());

				if ($collection instanceof Collection) {
					return $collection;
				}
			}
			return null;
		}

		protected function count($filter = [], array $options = []):int {
			$collection = $this->getCollection();
			return $collection->count($filter, $options);
		}
		protected function findOne(array $parameters){
			$collection = $this->getCollection();
			return $collection->findOne($parameters);
		}
		protected function find(array $parameters, array $properties = null){
			$collection = $this->getCollection();
			if(is_null($properties)){
				return $collection->find($parameters);
			}
			return $collection->find($parameters, $properties);
		}

		protected static function parseBson(&$instance, \ArrayObject $data){
			if(is_null($data)){
				return;
			}
			$hashTable = $data->getArrayCopy();
			$keys = array_keys($hashTable);
			foreach ($keys as $key){
				$value = $hashTable[$key];
				if($value instanceof \ArrayObject){
					$instance->{$key} = new \stdClass();
					self::parseBson($instance->{$key}, $value);
				}
				else{
					$instance->{$key} = $value;
				}
			}
		}

		protected function updateFields(array $fields):UpdateResult{
			if(!$this->_id){
				throw new \Exception("Document does not exist");
			}
			if(empty($fields)){
				throw new \InvalidArgumentException("Empty fields");
			}
			$collection = $this->getCollection();
			return $collection->updateOne(['_id' => $this->_id], ['$set' => $fields]);
		}

		public function getDatabase():string{
			return $this->databaseID;
		}

		abstract public function getSource():string;

		/**
		 * Should update if exists, and create if not exists
		 * @return mixed
		 */
		abstract public function save();

		/**
		 * Should insert if not exists
		 * @return mixed
		 */
		abstract public function update();

		/**
		 * Should fail if already exists
		 * @return mixed
		 */
		abstract public function create();

		/**
		 * Should continue if not exists
		 * @return mixed
		 */
		abstract public function delete();

	}
}
