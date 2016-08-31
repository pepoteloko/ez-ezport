<?php
/**
 * File
 * Save the CVS file
 *
 * @author Josep Rius
 * @package Eka
 */
class Log {

	const FILE_PATH = "files/";

	private $fichero;
	private $name;

	public function __construct($name = 'error') {
		$this -> name = $name;
		// Initial values
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
	public function writeLineError(Line $line, $txt) {
		$text = '[' . $line->getID() . '] ' . substr($line -> getTitle(), 0, 50) . ": $txt";
		fwrite($this -> fichero, $text . "\n");
	}

	/**
	 * cleanDirectory
	 *
	 * Deletes all the files from the export directory
	 */
	private function cleanDirectory() {
		$files = glob(self::FILE_PATH . $this -> name . '.log'); // get all file names
		foreach($files as $file) { // iterate files
			if(is_file($file))
				unlink($file); // delete file
		}
	}

	private function startFile() {
		// Obrir
		echo "\t\t<p>Creado fichero " . $this -> name . ".log</p>\n";
		$this -> fichero = fopen(self::FILE_PATH . $this -> name . ".log", "w");
	}

}
