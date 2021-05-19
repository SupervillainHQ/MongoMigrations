<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 11:25
 */

namespace Svhq\MongoMigrations\Operations {


	use MongoDB\Database;
	use Phalcon\Di;
	use Svhq\MongoMigrations\Migrations\MigrationLog;

	class CreateCollectionOperation implements Operation {

		/**
		 * @var string
		 */
		private $collection;

		function __construct(string $collection) {
			$this->collection = trim($collection);
		}

		function up():bool{
			// query migration log and halt if necessary
			$migrationLog = MigrationLog::load();
			$filter = (object) [
				"creation" => [
					'$exists' => true
				],
				"collection" => $this->collection
			];
			if($migrationLog->hasMigration($filter)){
				// skip execution of this op
				return false;
			}
			// create the collection
			$mongo = Di::getDefault()->get('mongo');
			if($mongo instanceof Database) {
				if($collections = $mongo->listCollections()){
					foreach ($collections as $collection) {
						if($collection->getName() == $this->collection){
							return false;
						}
					}

					$mongo->createCollection($this->collection);
					return true;
				}
			}
			return false;
		}

		function down():bool{
			return false;
		}
	}
}
