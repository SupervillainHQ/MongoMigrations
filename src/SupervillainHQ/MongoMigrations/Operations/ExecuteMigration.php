<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 11:26
 */

namespace SupervillainHQ\MongoMigrations\Operations {



	class ExecuteMigration extends Migration{

		private $op;

		function __construct(string $collection) {
			parent::__construct($collection);
			$this->op = new CreateCollectionOperation($collection);
		}

		function change():bool{
			return $this->op->up();
		}
	}
}
