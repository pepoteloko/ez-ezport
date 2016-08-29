<?php
/**
 * Linia
 * Linia del fichero CSV que importaremos
 *
 * @author Josep Rius
 * @package Eka
 */
class Line {
	private $post_id;
	private $title;
	private $image;
	private $short;
	private $long;
	private $published;

	private $proc;

	public function __construct() {
		$this -> proc = new XsltProcessor();
	}

	public function getID() {
		return $this -> post_id;
	}

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
			return $this -> xmlToHtml($this -> short, 'short');
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
			return substr($this -> xmlToHtml($this -> long, 'long'), 0, -1);
		} else {
			return $this -> long;
		}
	}

	public function setId($a) {
		$this -> post_id = $a;
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
	 * Return line in CSV format or with the specified char.
	 * All the fields goes between " and comma separated
	 *
	 * @param char $sC Separator character
	 * @return string
	 */
	public function printLineCSV($sC = ',') {
		//$titols = '"post_id","post_title","post_type","post_status","post_date","post_category","post_thumbnail","post_excerpt","post_content"';
		$linia  = '"post_id"' . $sC;
		$linia .= '"' . $this -> getTitle() . '"' . $sC;
		$linia .= '"post"' . $sC;
		$linia .= '"publish"' . $sC;
		$linia .= '"' . $this -> getPublished() . '"' . $sC;
		$linia .= '"noticias"' . $sC;
		$linia .= '"' . $this -> getImage() . '"' . $sC;
		$linia .= '"' . $this -> getShort() . '"' . $sC;
		$linia .= '"' . $this -> getLong() . '"';

		return $linia;
	}

	/**
	 * xmlToHtml
	 *
	 * Converts EZ xml to HTML using the ez functions
	 *
	 * @param $XMLContent string XML content
	 * @param $type [long|short] Indicate if is excerpt or body
	 * @return string
	 */
	private function xmlToHtml($XMLContent, $type) {
		$xslt = new DOMDocument();
		$xml = new DOMDocument();

		$GoodContent = utf8_encode($XMLContent);
		$GoodContent = iconv('UTF-8', 'UTF-8//IGNORE', $XMLContent);

		$xml -> loadXML($GoodContent);

		if ($type == 'short') {
			$xslt -> load("inc/excerpt.xslt");
		} else {
			$xslt -> load("inc/map.xslt");
		}
		$this -> proc -> importStylesheet($xslt);

		return $this -> proc -> transformToXML($xml);
	}

}
?>
