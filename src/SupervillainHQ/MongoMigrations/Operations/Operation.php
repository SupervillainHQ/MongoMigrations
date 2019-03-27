<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 11:29
 */

namespace SupervillainHQ\MongoMigrations\Operations {


	interface Operation {

		function up():bool;

		function down():bool;
	}
}
