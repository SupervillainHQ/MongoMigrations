<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-01-22
 * Time: 14:09
 */

namespace SupervillainHQ\MongoMigrations {


	use Commando\Command;
	use Phalcon\Di\FactoryDefault\Cli as CliDI;
	use SupervillainHQ\MongoMigrations\Config\Config;

	class MongoMigrationsCliApplication {

		private static $migrationDir;

		static function migrationDir(){
			return realpath(self::$migrationDir);
		}

		static function run(string $configPath, Command $command){
			$di = new CliDI();
			Config::load($configPath);

			self::$migrationDir = Config::instance()->migrations->path;

			if(!is_writable(self::$migrationDir) || !is_dir(self::$migrationDir)){
				throw new \Exception("Invalid migration directory");
			}

			if (stripos(getenv("APP_ENV"), "prod")){
				set_exception_handler(function (\Throwable $e) use ($di) {
					$debug = $di->get("debug");
					$debug->log(\Phalcon\Logger::CRITICAL, "Message: " . $e->getMessage());
					$debug->log(\Phalcon\Logger::CRITICAL, "File: " . $e->getFile());
					$debug->log(\Phalcon\Logger::CRITICAL, "Line: " . $e->getLine());
					$debug->log(\Phalcon\Logger::CRITICAL, "Trace: " . $e->getTraceAsString());

					if($bugsnag = $di->get('bugsnag')){
						$bugsnag->notifyException($e);
					}
					echo "Command failed. See log for details\n";
				});
			}

			self::evaluate($command);
		}

		private static function evaluate(Command $command){
			$cmd = ucfirst($command['command']);
			$arguments = $command->getArgumentValues();

			$commandClass = "SupervillainHQ\\MongoMigrations\\Cli\\Commands\\{$cmd}";
			if (!class_exists($commandClass)) {
				$commandClass = "SupervillainHQ\\MongoMigrations\\Cli\\Commands\\Help";
			}
			$reflector = new \ReflectionClass($commandClass);
			if ($reflector->implementsInterface('SupervillainHQ\MongoMigrations\Cli\CliCommand')) {
				if ($reflector->hasMethod('__construct')) {
					$method = $reflector->getMethod('__construct');
					$params = $method->getParameters();
					if (count($params)) {
						$cliCommand = $reflector->newInstanceArgs($arguments);
					}
					else{
						$cliCommand = $reflector->newInstance();
					}
				}
				else{
					$cliCommand = $reflector->newInstance();
				}
				if(isset($cliCommand)){
					$exitCode = $cliCommand->execute();
					exit($exitCode);
				}
			}
			throw new \Exception("No such command");
		}

	}
}
