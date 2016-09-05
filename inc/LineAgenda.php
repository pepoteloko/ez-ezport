<?php
/**
 * Linia
 * Linia del fichero CSV que importaremos
 *
 * @author Josep Rius
 * @package Eka
 */
class LineAgenda extends Line {

	/**
	 * Constructor
	 *
	 * Creamos una nueva linia
	 *
	 * @param mysqli $sql
	 */
	public function __construct(mysqli $sql) {
		parent::__construct($sql, 'agenda');
	}

	/**
	 * getStartDate
	 *
	 * Returns the start date extracted from publish date
	 *
	 * @return string
	 */
	public function getStartDate() {
		$time = DateTime::createFromFormat('U', $this -> published, new DateTimeZone('Europe/Madrid'));
		return $time -> format('d-m-Y');
	}

	/**
	 * getStartTime
	 *
	 * Returns the start time extracted from publish date
	 *
	 * @return string
	 */
	public function getStartTime() {
		$time = DateTime::createFromFormat('U', $this -> published, new DateTimeZone('UTC'));
		$time -> setTimezone(new DateTimeZone('Europe/Madrid'));
		return $time -> format('H:i:s');
	}

	/**
	 * clearLine
	 *
	 * Emptys the object
	 */
	public function clearLine() {
		parent::clearLine();
		// TODO Camps extres
	}

	public function debugImage() {
		$image = new SimpleXMLElement($this -> image);
		print_r($image);
	}

	public function printLine() {
		parent::printLine();
		// Afegir camps nous
	}

	/**
	 * getTitles
	 *
	 * Return the fields titles as string
	 * Return the title line for the CSV file in format "field_title", "field_title", "another_field"
	 *
	 * @return string Titles
	 */
	public function getTitles() {
		return '"ID","event_title","start_date","start_time","thumbnail","excerpt","content"';
	}

	/**
	 * printLineCSV
	 *
	 * Return line in CSV format or with the specified char.
	 * All the fields goes between " and comma separated
	 *
	 * @param char $sC Separator character
	 * @return string
	 */
	public function printLineCSV($sC = ',') {
		// Control errores
		if ($this -> long == "") {
			parent::writeLineError($this, "Cuerpo vacio");
		} elseif ($this -> short == "") {
			parent::writeLineError($this, "Excerpt vacio");
		}

		$linia = '"' . $this -> getID() . '"' . $sC;
		$linia .= '"' . $this -> getTitle() . '"' . $sC;
		$linia .= '"' . $this -> getStartDate() . '"' . $sC;
		$linia .= '"' . $this -> getStartTime() . '"' . $sC;
		$linia .= '"' . $this -> getImage() . '"' . $sC;
		$linia .= '"' . $this -> getShort() . '"' . $sC;
		$linia .= '"' . $this -> getLong() . '"';

		return $linia;
	}
}
?>
