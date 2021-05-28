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
    use Svhq\Core\Application\DistributedCliApplication;
    use Svhq\Core\Cli\Console;
    use Svhq\Core\Cli\ExitCodes;
    use Svhq\Core\Config\Config;
    use Svhq\Core\System\DependencyLoader;

    class MongoMigrationsCliApplication extends DistributedCliApplication {

        private static $overrideKey;
        private static $migrationDir;
        private static $logCollection;

		static function migrationDir(){
			return realpath(self::$migrationDir);
		}

		static function logCollection():string{
			return trim(self::$logCollection);
		}

		static function run(string $configFilePath): int{
            $di = new CliDi();
            $instance = self::instance();

            Config::register($instance->key(), $configFilePath);

            self::registerConfigs($configFilePath);

            $migrationDefaults = Config::instance($instance->key())->getDefaults('migrations');
            $defaultMigrationDir = trim($migrationDefaults->path);
            $defaultMigrationCollection = trim($migrationDefaults->entries);
            self::$migrationDir = $defaultMigrationDir;
            self::$logCollection = $defaultMigrationCollection;

            // TODO: check user-config and determine if we should register an extra config that should override our default
            //   mm-config
            $cfgOverride = null;
            $localPaths = Config::instance($instance->key())->getConfig('local.paths');
            foreach ($localPaths as $path) {
                if($aPath = Config::instance()->absolutePath($path)){
                    $cfgOverride = $aPath;
                    break;
                }
            }
            if(is_null($cfgOverride)){
                $globalPaths = Config::instance($instance->key())->getConfig('global.paths');
                foreach ($globalPaths as $path) {
                    if($aPath = Config::instance()->absolutePath($path)){
                        $cfgOverride = $aPath;
                        break;
                    }
                }
            }

            if($cfgOverride){
                self::$overrideKey = 'MM';
                Config::register(self::$overrideKey, $cfgOverride);
                if($migrationOverrides = Config::instance(self::$overrideKey)->getDefaults('migrations')){
                    self::$migrationDir = trim($migrationOverrides->path);
                    self::$logCollection = trim($migrationOverrides->entries);
                }
            }
            DependencyLoader::loadFromConfig($di);

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
                    Console::instance()->log("Command failed. See log for details");
                });
            }

            $commandNamespaces = $instance->commandNamespaces();
            try{
                $exitCode = $instance->evaluate($commandNamespaces);
            }
            catch (\Exception $exception){
                Console::instance()->log("<red>Command evaluation failed. See log for details</red>");
                $debug = $di->get("debug");
                $debug->log(\Phalcon\Logger::CRITICAL, $exception->getMessage());
                return ExitCodes::GENERIC_ERROR;
            }
            if(isset($exitCode)){
                return $exitCode;
            }

            Console::instance()->log("<red>Command evaluated but did not return a valid exit-code</red>");
            return ExitCodes::GENERIC_ERROR;
		}

        protected function commandNamespaces(): array
        {
            return ['Svhq\\MongoMigrations\\Cli\\Commands'];
        }

        public function key():string{
            return self::$overrideKey ?? basename(str_replace('\\', '/', static::class));
        }
    }
}
