<?php

namespace Svhq\MongoMigrations\Cli\Util {

    use MongoDB\Database;
    use Phalcon\Di;

    class MongoUtil {
        public static function hasCollection(string $collectionName){
            $mongo = Di::getDefault()->get('mongo');
            if($mongo instanceof Database) {
                $collections = $mongo->listCollections();

                foreach ($collections as $collection) {
                    if($collection->getName() == $collectionName){
                        return true;
                    }
                }
            }
            return false;
        }

        public static function initCollection(string $collectionName){
            $mongo = Di::getDefault()->get('mongo');
            if($mongo instanceof Database) {
                if(strlen($collectionName)){
                    return $mongo->createCollection($collectionName);
                }
            }
            return null;
        }

        public static function verifyDbAccess():bool{
            $mongo = Di::getDefault()->get('mongo');
            return $mongo instanceof Database;
        }
    }
}