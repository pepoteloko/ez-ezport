<?php
/**
 * Linia
 * Linia del fichero CSV que importaremos
 *
 * @author Josep Rius
 * @package Eka
 */
class Line {
	private $title;
	private $image;
	private $short;
	private $long;

	public function getTitle() {
		return $this -> title;
	}

	public function getImage() {
		$image = new SimpleXMLElement($this -> image);
		return $image['url'] . 'jpg';
	}

	/**
	 * getShort
	 * Get the short text
	 * @param bool $html If set to true tries to convert the text from XML to HTML
	 * @return string
	 */
	public function getShort($html = true) {
		if ($html) {
			return $this -> xmlToHtml($this -> short);
		} else {
			return $this -> short;
		}
	}

	/**
	 * getlong
	 * Get the long text
	 * @param bool $html If set to true tries to convert the text from XML to HTML
	 * @return string
	 */
	public function getLong($html = true) {
		if ($html) {
			return $this -> xmlToHtml($this -> long);
		} else {
			return $this -> long;
		}
	}

	public function setTitle($a) {
		$this -> title = $a;
	}

	public function setImage($a) {
		$this -> image = $a;
	}

	public function setShort($a) {
		$this -> short = $a;
	}

	public function setLong($a) {
		$this -> long = $a;
	}

	/**
	 * clearLine
	 * Emptys the object
	 */
	public function clearLine() {
		$this -> title = '';
		$this -> image = '';
		$this -> short = '';
		$this -> long = '';
	}

	public function debugImage() {
		$image = new SimpleXMLElement($this -> image);
		print_r($image);
	}

	public function printLine() {
		//TODO Controlar que tots els camps estiguin plens i fer un log de problemes
		echo "<pre>";
		echo 'TITLE:' . $this -> getTitle() . ';';
		echo 'IMAGE:' . $this -> getImage() . ';';
		echo 'SHORT:' . $this -> getShort() . ';';
		echo 'LONG:' . $this -> getLong();
		echo "</pre><br />";
	}

	/**
	 * printLineCSV
	 * Echoes this line in CSV format or with the specified char.
	 * @param char $sC Separator character
	 */
	public function printLineCSV($sC = ';') {
		echo $this -> getTitle() . $sC;
		echo $this -> getImage() . $sC;
		echo $this -> getShort() . $sC;
		echo $this -> getLong();
	}

	/**
	 * xmlToHtml
	 * Converts EZ xml to HTML using the ez functions
	 * @return string
	 */
	private function xmlToHtml($XMLContent) {
		$outputHandler = new eZXHTMLXMLOutput($XMLContent, false);
		$html = &$outputHandler -> outputText();
		return $html;
	}

}
?>
