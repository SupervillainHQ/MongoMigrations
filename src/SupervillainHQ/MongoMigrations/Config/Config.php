<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-19
 * Time: 10:31
 */

namespace SupervillainHQ\MongoMigrations\Config {


	class Config {

		private static $instance;

		static function instance():Config{
			if(!is_null(self::$instance)){
				return self::$instance;
			}
			throw new \Exception("Config not loaded");
		}

		/**
		 * @var \stdClass
		 */
		private $options;
		/**
		 * @var string
		 */
		private $cfgPath;

		private function __construct(\stdClass $options, string $path) {
			$this->options = $options;
			$this->cfgPath = $path;
		}

		function __get($name) {
			if(property_exists($this->options, $name)){
				return $this->options->{$name};
			}
			return null;
		}

		function __call($name, $arguments) {
			if(0 === strpos($name, 'get')){
				if(empty($arguments)){
					return $this->__get(lcfirst(substr($name, 3)));
				}
				else{
					throw new \Exception("Mutator arguments not yet supported");
				}
			}
			throw new \Exception("Not implemented");
		}

		function relPath(string $path = ''){
			$cfgDir = dirname($this->cfgPath);
			return realpath("{$cfgDir}/{$path}");
		}

		static function load(string $filepath){
			if(is_file($filepath) && is_readable($filepath)){
				$raw = file_get_contents($filepath);
				if($json = json_decode($raw)){
					self::$instance = new Config($json, $filepath);
				}
				return;
			}
			throw new \Exception("Invalid config data");
		}
	}
}
