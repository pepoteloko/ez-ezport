<?php
/**
 * File
 * Save the CVS file
 *
 * @author Josep Rius
 * @package Eka
 */
class File {

	const MAX_LINES_PER_FILE = 300;
	const FILE_PATH = "files/";

	private $line;
	private $fichero;
	private $rotate;

	public function __construct() {
		// Initial values
		$this -> line = 0;
		$this -> rotate = 1;

		$this -> cleanDirectory();
		$this -> startFile();
	}

	public function __destruct() {
		fclose($this -> fichero);
	}

	/**
	 * saveLine
	 *
	 * Add a line to the file
	 *
	 * @param string $text
	 */
	public function saveLine($text) {
		if ($this -> line >= self::MAX_LINES_PER_FILE) {
			$this -> rotateFile();
		}

		fwrite($this -> fichero, $text . "\n");
		$this -> line++;
	}

	/**
	 * rotateFile
	 *
	 * Close the file and open the next one using the same pointer.
	 * Then $this -> fichero always poiting to the actual file.
	 */
	private function rotateFile() {
		$this -> line = 0;
		fclose($this -> fichero);
		$this -> rotate++;
		$this -> startFile();
	}

	/**
	 * cleanDirectory
	 *
	 * Deletes all the files from the export directory
	 */
	private function cleanDirectory() {
		$files = glob(self::FILE_PATH . '*.csv'); // get all file names
		foreach($files as $file) { // iterate files
			if(is_file($file))
				unlink($file); // delete file
		}
	}

	private function startFile() {
		// Obrir
		echo "<p>Creado fichero " .$this -> rotate . ".csv</p>\n";
		$this -> fichero = fopen(self::FILE_PATH . $this -> rotate . ".csv", "w");
		// Pintar titols
		$titols = '"post_title","post_excerpt","post_content","post_categories","post_date","post_type","post_thumbnail"';
		$this -> saveLine($titols);
	}

}
