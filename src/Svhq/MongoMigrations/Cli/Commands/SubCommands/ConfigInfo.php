<?php


namespace Svhq\MongoMigrations\Cli\Commands\SubCommands {


    use Svhq\Core\Cli\CliCommand;
    use Svhq\Core\Cli\Console;
    use Svhq\Core\Cli\ExitCodes;
    use Svhq\Core\Config\Config;
    use Svhq\Core\Credentials\CredentialsStorage;
	use Svhq\MongoMigrations\MongoMigrationsCliApplication;

	class ConfigInfo implements CliCommand {

		function execute(): int {
            $mongoInfo = Config::instance()->getMongo();
            $credentialsList = CredentialsStorage::zone()->getCredentials('mongo');

            $info = [
		        'Paths' => [
                    'config path' => Config::instance()->location(),
                    'project root' => Config::instance()->projectRoot(),
                    'vendor path' => Config::instance()->vendorPath('Svhq\\Core'),
                ],
                'Mongo' => [
                    'migrations path' => MongoMigrationsCliApplication::migrationDir() ?? 'n/a',
                    'migrations log' => MongoMigrationsCliApplication::logCollection() ?? 'n/a'
                ],
                'Database' => [
                    'name' => $mongoInfo->database ?? 'n/a'
                ]
            ];
            if($credentials = array_shift($credentialsList)){
                $info['Database']['user'] = $credentials->user();
            }

            Console::export($info);
			return ExitCodes::OK;
		}
	}
}
