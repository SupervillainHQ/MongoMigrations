<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-27
 * Time: 11:47
 */

namespace SupervillainHQ\MongoMigrations\Core\Dependencies {


	use SupervillainHQ\MongoMigrations\Config\Config;
	use SupervillainHQ\MongoMigrations\Core\Dependency;
	use MongoDB\Client as MongoClient;

	class Mongo implements Dependency{

		public function shared():bool{
			return true;
		}

		public function getName():string{
			return 'mongo';
		}

		public function definition(){
			$host = trim(Config::instance()->host);
			$port = trim(Config::instance()->port);
			$database = trim(Config::instance()->database);
			$authFile = Config::instance()->authorization;
			if(($authFile instanceof \stdClass) and property_exists($authFile, 'file')){
				$authPath = trim($authFile->file);
				$authPath = Config::instance()->relPath($authPath);
			}

			$connection = "mongodb://{$host}:{$port}";
			if(isset($authPath) && is_readable($authPath) && is_file($authPath)){
				$auth = json_decode(file_get_contents($authPath));
				$user = trim($auth->user);
				$password = trim($auth->password);
				$database = trim($auth->authenticationDatabase);
				$connection = "mongodb://{$user}:{$password}@{$host}:{$port}/{$database}";
			}

			return function() use($connection, $database){
				$mongo = new MongoClient($connection);
				return $mongo->selectDatabase("{$database}");
			};
		}

	}
}
