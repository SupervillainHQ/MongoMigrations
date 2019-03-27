<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 11:45
 */

namespace SupervillainHQ\MongoMigrations\Core {


	interface Dependency {

		public function shared():bool;

		public function getName():string;

		public function definition();
	}
}
