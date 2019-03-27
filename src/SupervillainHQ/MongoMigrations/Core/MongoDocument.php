<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 10:58
 */

namespace SupervillainHQ\MongoMigrations\Core {


	interface MongoDocument {
		public function getDatabase():string;

		public function getSource():string;

		public function save();

		public function update();

		public function create();

		public function delete();
	}
}
