<?php
/**
 * Created by PhpStorm.
 * User: anderskrarup
 * Date: 2019-03-19
 * Time: 10:25
 */

namespace SupervillainHQ\MongoMigrations\Migrations {


	use SupervillainHQ\MongoMigrations\MongoMigrationsCliApplication;

	class MigrationFile implements \JsonSerializable {

		protected $filename;
		protected $filePath;

		protected $collection;
		private $when;


		function __construct(string $collection = null, string $filePath = null) {
			$this->collection = trim($collection);
			$this->filePath = $filePath;
			$this->when = new \DateTime('now', new \DateTimeZone('UTC'));
		}


		function collection():string{
			return $this->collection;
		}

		function fileName():string{
			return $this->filename;
		}

		function saveAsMson(){
			$migrationDir = MongoMigrationsCliApplication::migrationDir();
			$fileName = "{$this->when->format('YmdHis')}-{$this->collection}";
			$filePath = "{$migrationDir}/{$fileName}.mson";

			$buffer = $this->jsonSerialize();
			file_put_contents($filePath, json_encode($buffer));
		}

		static function create(string $collection):MigrationFile{
			$instance = new MigrationFile();
			$instance->collection = trim($collection);
			return $instance;
		}

		static function listFiles(){
			$migrationDir = MongoMigrationsCliApplication::migrationDir();
			echo "Migrations in dir {$migrationDir}\n";
			$files = array_diff(scandir($migrationDir), ['.', '..']);
			$migrationFiles = [];
			foreach ($files as $file) {
				$ext = pathinfo($file, PATHINFO_EXTENSION);
				$filename = pathinfo($file, PATHINFO_FILENAME);
				if($ext == 'mson'){
					$migrationFile = new MigrationFile();
					$migrationFilePath = realpath("{$migrationDir}/{$file}");
					if(is_file($migrationFilePath) && is_readable($migrationFilePath)){
						$contents = file_get_contents($migrationFilePath);
						self::inflate($migrationFile, json_decode($contents));
						$migrationFile->filename = $filename;
						array_push($migrationFiles, $migrationFile);
						echo "{$file}\n";
					}
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
