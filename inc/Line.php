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
	private $published;

	public function getTitle() {
		return $this -> title;
	}

	public function getImage() {
		$image = new SimpleXMLElement($this -> image);
		return $image['url'] . 'jpg';
	}

	/**
	 * getPublished
	 *
	 * Returns the published date in pretty format :)
	 *
	 * @return string
	 */
	public function getPublished() {
		$time = DateTime::createFromFormat( 'U', $this -> published );
		return $time -> format('Y-m-d H:i:s');
	}

	/**
	 * getShort
	 *
	 * Get the short text
	 *
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
	 *
	 * Get the long text
	 *
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

	public function setPublished($a) {
		$this -> published = $a;
	}

	public function setShort($a) {
		$this -> short = $a;
	}

	public function setLong($a) {
		$this -> long = $a;
	}

	/**
	 * clearLine
	 *
	 * Emptys the object
	 */
	public function clearLine() {
		$this -> title = '';
		$this -> image = '';
		$this -> short = '';
		$this -> long = '';
		$this -> published = 0;
	}

	public function debugImage() {
		$image = new SimpleXMLElement($this -> image);
		print_r($image);
	}

	public function printLine() {
		//TODO Controlar que tots els camps estiguin plens i fer un log de problemes
		echo "<pre>";
		echo 'TITLE:' . $this -> getTitle() . '<br>';
		echo 'IMAGE:' . $this -> getImage() . '<br>';
		echo 'SHORT:' . $this -> getShort() . '<br>';
		echo 'LONG:' . $this -> getLong() . '<br>';
		echo 'PUBLISHED:' . $this -> getPublished();
		echo "</pre><br />";
	}

	/**
	 * printLineCSV
	 *
	 * Echoes this line in CSV format or with the specified char.
	 *
	 * @param char $sC Separator character
	 */
	public function printLineCSV($sC = ';') {
		echo $this -> getTitle() . $sC;
		echo $this -> getImage() . $sC;
		echo $this -> getShort() . $sC;
		echo $this -> getLong() . $sC;
		echo $this -> getPublished();
	}

	/**
	 * xmlToHtml
	 *
	 * Converts EZ xml to HTML using the ez functions
	 *
	 * @return string
	 */
	private function xmlToHtml($XMLContent) {
		$GoodContent = utf8_encode($XMLContent);
		$outputHandler = new eZXHTMLXMLOutput($GoodContent, false);
		$html = &$outputHandler -> outputText();
		return $html;
	}

}
?>
