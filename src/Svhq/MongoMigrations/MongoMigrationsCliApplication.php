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
		private static $logCollection;

		static function migrationDir(){
			return realpath(self::$migrationDir);
		}

		static function logCollection():string{
			return trim(self::$logCollection);
		}

		static function run(string $configFilePath): int{
            $di = new CliDi();
            Config::loadFromPath($configFilePath);

            $migrationDefaults = Config::instance()->getDefaults('migrations');
            $defaultMigrationDir = trim($migrationDefaults->path);
            $defaultMigrationCollection = trim($migrationDefaults->entries);
            self::$migrationDir = $defaultMigrationDir;
            self::$logCollection = $defaultMigrationCollection;

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

            $instance = self::instance();
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
    }
}
