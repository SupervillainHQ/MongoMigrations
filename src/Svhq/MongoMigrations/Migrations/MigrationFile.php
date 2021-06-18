<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-19
 * Time: 10:25
 */

namespace Svhq\MongoMigrations\Migrations {


    use Phalcon\Di;
    use Svhq\Core\Resource\FileResource;
    use Svhq\Core\Resource\ResourceManager;
    use Svhq\MongoMigrations\MongoMigrationsCliApplication;

	class MigrationFile implements \JsonSerializable {

		protected string $filename;
		protected ?string $filePath;

		protected string $collection;
		private \DateTime $when;


		function __construct(string $collection, string $filePath = null) {
            if(empty(trim($collection))){
                throw new \InvalidArgumentException("Invalid collection");
            }
            $this->collection = trim($collection);
            $this->filePath = $filePath;
            $this->when = new \DateTime('now', new \DateTimeZone('UTC'));
            $this->filename = "{$this->when->format('YmdHis')}-{$this->collection}";
        }


		function collection():string{
			return $this->collection;
		}

		function fileName():string{
			return $this->filename;
		}

        /**
         * Save migration file to local path
         */
		function saveAsMson():void{
			$migrationDir = MongoMigrationsCliApplication::migrationDir();
            $this->filename = "{$this->when->format('YmdHis')}-{$this->collection}";
			$filePath = "{$migrationDir}/{$this->filename}.mson";

			$buffer = $this->jsonSerialize();
			$resMan = Di::getDefault()->getResource($filePath);
            /**
             * @var ResourceManager
             */
            $resMan->write(json_encode($buffer), true);
		}

		static function fromFile(string $filePath):MigrationFile{
            if(is_file($filePath) && is_readable($filePath)){
                $migrationFile = new MigrationFile('');
                $filename = pathinfo($filePath, PATHINFO_FILENAME);
                $contents = file_get_contents($filePath);
                self::inflate($migrationFile, json_decode($contents));
                $migrationFile->filename = $filename;
                return $migrationFile;
            }
            throw new \InvalidArgumentException("Invalid file path {$filePath}");
        }

        /**
         * Returns a list of existing migration files
         * @return array
         */
		static function listFiles():array{
		    // Maybe use ResourceManager to abstract away local/remote fs-api
			$migrationDir = MongoMigrationsCliApplication::migrationDir();
			$files = array_diff(scandir($migrationDir), ['.', '..']);
			$migrationFiles = [];
			foreach ($files as $file) {
				$ext = pathinfo($file, PATHINFO_EXTENSION);
//				$filename = pathinfo($file, PATHINFO_FILENAME);
				if($ext == 'mson'){
                    $migrationFilePath = realpath("{$migrationDir}/{$file}");
                    $migrationFile = MigrationFile::fromFile($migrationFilePath);
                    array_push($migrationFiles, $migrationFile);
//                    if(is_file($migrationFilePath) && is_readable($migrationFilePath)){
//						$contents = file_get_contents($migrationFilePath);
//						self::inflate($migrationFile, json_decode($contents));
//						$migrationFile->filename = $filename;
//						array_push($migrationFiles, $migrationFile);
//					}
				}
			}
			return $migrationFiles;
		}

		protected static function inflate(MigrationFile &$instance, \stdClass $data){
			if(property_exists($data, 'collection')){
				$instance->collection = trim($data->collection);
			}
			if(property_exists($data, 'when')){
				$instance->when = new \DateTime(trim($data->when));
			}
		}

		/**
		 * Specify data which should be serialized to JSON
		 * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
		 * @return mixed data which can be serialized by <b>json_encode</b>,
		 * which is a value of any type other than a resource.
		 * @since 5.4.0
		 */
		public function jsonSerialize() {
			$simple = (object) [
				"collection" => trim($this->collection),
			];
			return $simple;
		}
	}
}
