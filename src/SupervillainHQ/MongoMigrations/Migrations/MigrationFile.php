<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-19
 * Time: 10:25
 */

namespace SupervillainHQ\MongoMigrations\Migrations {


	class MigrationFile implements \JsonSerializable {

		protected $filename;
		protected $filePath;

		protected $collection;


		function __construct(string $collection) {
			$this->collection = $collection;
		}


		function collection():string{
			return $this->collection;
		}


		/**
		 * Specify data which should be serialized to JSON
		 * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
		 * @return mixed data which can be serialized by <b>json_encode</b>,
		 * which is a value of any type other than a resource.
		 * @since 5.4.0
		 */
		public function jsonSerialize() {
			// TODO: Implement jsonSerialize() method.
		}
	}
}
