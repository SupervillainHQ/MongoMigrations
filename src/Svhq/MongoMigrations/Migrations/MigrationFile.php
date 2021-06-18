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

		protected string $fileName;
		protected ?string $filePath;

		protected string $collection;
		private \DateTime $when;


		function __construct(string $collection = null, string $filePath = null) {
            $this->collection = trim($collection);
            $this->filePath = $filePath;
            $this->when = new \DateTime('now', new \DateTimeZone('UTC'));
        }


		function collection():string{
			return $this->collection;
		}

		function fileName():string{
		    if(!isset($this->fileName)){
		        if(!isset($this->collection)){
		            throw new \Exception("Unable to derive filename without collection");
                }
                $this->fileName = "{$this->when->format('YmdHis')}-{$this->collection}";
            }
			return $this->fileName;
		}

        /**
         * Save migration file to local path
         */
		function saveAsMson():void{
			$migrationDir = MongoMigrationsCliApplication::migrationDir();
			$filePath = "{$migrationDir}/{$this->fileName()}.mson";

			$buffer = $this->jsonSerialize();
			$resMan = Di::getDefault()->getResource($filePath);
            /**
             * @var ResourceManager
             */
            $resMan->write(json_encode($buffer), true);
		}

		static function fromFile(string $filePath):MigrationFile{
            if(is_file($filePath) && is_readable($filePath)){
                $migrationFile = new MigrationFile();
                $fileName = pathinfo($filePath, PATHINFO_FILENAME);
                $contents = file_get_contents($filePath);
                self::inflate($migrationFile, json_decode($contents));
                $migrationFile->fileName = $fileName;
                $migrationFile->filePath = $filePath;
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
				if($ext == 'mson'){
                    $migrationFilePath = realpath("{$migrationDir}/{$file}");
                    $migrationFile = MigrationFile::fromFile($migrationFilePath);
                    array_push($migrationFiles, $migrationFile);
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
