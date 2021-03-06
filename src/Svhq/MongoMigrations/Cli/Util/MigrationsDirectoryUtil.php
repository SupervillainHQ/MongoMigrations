<?php

namespace Svhq\MongoMigrations\Cli\Util {

    use Phalcon\Di;
    use Svhq\Core\Resource\ResourceManager;

    class MigrationsDirectoryUtil {
        const CREATED = 1;
        const EXISTS = 2;

        private string $path;

        public function __construct(string $path){
            $this->path = $path;
        }

        public function init():int{
            $resMan = Di::getDefault()->getResource($this->path);

            if($resMan->isDirectory()){
                return self::EXISTS;
            }
            $resMan->validatePath(true);
            return self::CREATED;
        }
    }
}