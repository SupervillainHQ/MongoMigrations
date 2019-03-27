<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 11:27
 */

namespace SupervillainHQ\MongoMigrations\Operations {


	class Migration implements Operation {


		/**
		 * @var string
		 */
		private $collection;

		function __construct(string $collection) {
			$this->collection = $collection;
		}

		function collection():string{
			return $this->collection;
		}

		function up():bool{
			return false;
		}

		function down():bool{
			return false;
		}
	}
}
