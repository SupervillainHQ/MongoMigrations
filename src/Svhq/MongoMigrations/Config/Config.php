<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-19
 * Time: 10:31
 */

namespace Svhq\MongoMigrations\Config {


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

		function realPath(string $path = ''){
			$cfgDir = dirname($this->cfgPath);
			return realpath("{$cfgDir}/{$path}");
		}

		function vendorPath(){
			$filePath = __FILE__;
			if(false !== strpos($filePath, 'vendor/')){
				$fragments = explode('/', $filePath);
				$vendorPath = '';
				while ($fragment = array_shift($fragments)){
					if('vendor' == $fragment){
						break;
					}
					$vendorPath = "{$vendorPath}/{$fragment}";
				}
				if($absVendorPath = realpath($vendorPath)){
					return $absVendorPath;
				}
			}
			$root = $this->realPath();
			$rootPath = $root;
			$rootVendor = "{$root}/vendor";
			while(!is_dir($rootVendor)){
				$rootPath = dirname($rootPath);
				$rootVendor = "{$rootPath}/vendor";
				if(strlen($rootPath) < 3 || '/' == $rootPath){
					break;
				}
			}
			if(is_dir($rootVendor)){
				return $rootVendor;
			}
			return null;
		}

		function basePath(string $path = ''){
			if($vendorPath = $this->vendorPath()){
				$basePath = "{$vendorPath}/..";
				if(strlen($path)){
					$basePath = rtrim($basePath) . '/' .rtrim($path, '/');
				}
				return realpath($basePath);
			}
			return null;
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
