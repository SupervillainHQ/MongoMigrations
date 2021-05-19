<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-01-22
 * Time: 14:09
 */

namespace Svhq\MongoMigrations {

	use Phalcon\Di\FactoryDefault\Cli as CliDI;
    use Svhq\Core\Application\CliApplication;
    use Svhq\Core\Cli\Console;
    use Svhq\Core\Cli\ExitCodes;
    use Svhq\Core\Config\Config;
    use Svhq\Core\System\DependencyLoader;

    class MongoMigrationsCliApplication extends CliApplication {

		private static $migrationDir;
		private static $mongoDatabase;

		static function migrationDir(){
			return realpath(self::$migrationDir);
		}

		static function database():string{
			return trim(self::$mongoDatabase);
		}

		static function run(string $configFilePath): int{
//			$di = new CliDI();
//			Config::load($configPath);

//			self::$migrationDir = Config::instance()->migrations->path;
//			self::$mongoDatabase = Config::instance()->database;

//			$dependencies = [
//				'Svhq\MongoMigrations\Core\Dependencies\Mongo'
//			];
//			self::loadDependencies($dependencies);

//			if(!is_writable(self::$migrationDir) || !is_dir(self::$migrationDir)){
//				$path = self::$migrationDir;
//				throw new \Exception("Invalid migration directory. Unable to verify path '{$path}'");
//			}


			//--
            $di = new CliDi();
            Config::loadFromPath($configFilePath);

            $defaults = Config::instance()->getDefaults();
            $defaultMigrationDir = trim($defaults->migrations->path);
            $authDatabase = trim($defaults->database);
            self::$migrationDir = $defaultMigrationDir;
            self::$mongoDatabase = $authDatabase;

            // TODO: load user-config be able to determine user/project-sourcepaths
            $userCfgPath = null;
            $localPaths = Config::instance()->getConfig('local.paths');
            foreach ($localPaths as $path) {
                if($aPath = Config::instance()->absolutePath($path)){
                    $userCfgPath = $aPath;
                    break;
                }
            }
            if(is_null($userCfgPath)){
                $globalPaths = Config::instance()->getConfig('global.paths');
                foreach ($globalPaths as $path) {
                    if($aPath = Config::instance()->absolutePath($path)){
                        $userCfgPath = $aPath;
                        break;
                    }
                }
            }

            if($userCfgPath){
                Config::loadFromPath($userCfgPath);
            }
            DependencyLoader::loadFromConfig($di);
//            DependencyLoader::load(['Svhq\Core\Dependencies/Mongo']);

            if(!is_writable(self::$migrationDir) || !is_dir(self::$migrationDir)){
                $path = self::$migrationDir;
                throw new \Exception("Invalid migration directory. Unable to verify path '{$path}'");
            }

            if (stripos(getenv("APP_ENV"), "prod")){
                set_exception_handler(function (\Throwable $e) use ($di) {
                    $debug = $di->get("debug");
                    $debug->log(\Phalcon\Logger::CRITICAL, "Message: " . $e->getMessage());
                    $debug->log(\Phalcon\Logger::CRITICAL, "File: " . $e->getFile());
                    $debug->log(\Phalcon\Logger::CRITICAL, "Line: " . $e->getLine());
                    $debug->log(\Phalcon\Logger::CRITICAL, "Trace: " . $e->getTraceAsString());
                    Console::log("Command failed. See log for details");
                });
            }

            $instance = self::instance();
            $commandNamespaces = $instance->commandNamespaces();
            try{
                $exitCode = $instance->evaluate($commandNamespaces);
            }
            catch (\Exception $exception){
                Console::log("<red>Command evaluation failed. See log for details</red>");
                $debug = $di->get("debug");
                $debug->log(\Phalcon\Logger::CRITICAL, $exception->getMessage());
                return ExitCodes::GENERIC_ERROR;
            }
            if(isset($exitCode)){
                return $exitCode;
            }

            Console::log("<red>Command evaluated but did not return a valid exit-code</red>");
            return ExitCodes::GENERIC_ERROR;
		}

//		private static function evaluate(Command $command){
//			$cmd = ucfirst($command['command']);
//			$arguments = $command->getArgumentValues();
//
//			$commandClass = "Svhq\\MongoMigrations\\Cli\\Commands\\{$cmd}";
//			if (!class_exists($commandClass)) {
//				$commandClass = "Svhq\\MongoMigrations\\Cli\\Commands\\Help";
//			}
//			$reflector = new \ReflectionClass($commandClass);
//			if ($reflector->implementsInterface('Svhq\MongoMigrations\Cli\CliCommand')) {
//				if ($reflector->hasMethod('__construct')) {
//					$method = $reflector->getMethod('__construct');
//					$params = $method->getParameters();
//					if (count($params)) {
//						$cliCommand = $reflector->newInstanceArgs($arguments);
//					}
//					else{
//						$cliCommand = $reflector->newInstance();
//					}
//				}
//				else{
//					$cliCommand = $reflector->newInstance();
//				}
//				if(isset($cliCommand)){
//					$exitCode = $cliCommand->execute();
//					exit($exitCode);
//				}
//			}
//			throw new \Exception("No such command");
//		}

//		private static function loadDependencies(array $dependencies, DiInterface $dependencyInjector = null) {
//			if(is_null($dependencyInjector)){
//				$dependencyInjector = Di::getDefault();
//			}
//
//			foreach ($dependencies as $dependency) {
//				$reflection = new \ReflectionClass($dependency);
//
//				if ($reflection->implementsInterface("Svhq\\MongoMigrations\\Core\\Dependency")) {
//					$service = $reflection->newInstance();
//
//					if ($service->shared()) {
//						$dependencyInjector->setShared($service->getName(), $service->definition());
//					}
//					else {
//						$dependencyInjector->set($service->getName(), $service->definition());
//					}
//				}
//
//			}
//		}

        protected function commandNamespaces(): array
        {
            return ['Svhq\\MongoMigrations\\Cli\\Commands'];
        }
    }
}
