<?php
/**
 * Linia
 * Linia del fichero CSV que importaremos
 *
 * @author Josep Rius
 * @package Eka
 */
class Line {
	protected $post_id;
	protected $title;
	protected $image;
	protected $short;
	protected $long;
	protected $published;

	private $Log;
	private $proc;
	private $sql;

	/**
	 * @param mysqli $sql
	 * @param string $logName Name of log file
	 */
	public function __construct(mysqli $sql, $logName = 'error') {
		$this -> proc = new XsltProcessor();
		$this -> Log = new Log($logName);
		$this -> sql = $sql;
	}

	public function getID() {
		return $this -> post_id;
	}

	public function getTitle() {
		return $this -> title;
	}

	/**
	 * getPublished
	 *
	 * Returns the published date in pretty format :)
	 *
	 * @return string
	 */
	public function getPublished() {
		if($this -> published == "") {
			return "";
		} else {
			$time = DateTime::createFromFormat('U', $this -> published);
			return $time -> format('Y-m-d H:i:s');
		}
	}

	public function getImage() {
		if ($this -> image != "") {
			$domain = 'http://aavvmadrid.org/';

			$image = new SimpleXMLElement($this -> image);

			if ($image['url'] != "") {
				return $domain . $image['url'];
			} else {
				return "";
			}
		} else {
			return "";
		}
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
		if ($this -> short == "") {
			return "";
		} else {
			if ($html) {
				$text = $this -> xmlToHtml($this -> short, 'short');
			} else {
				$text = $this -> short;
			}
			return str_replace('"', '”', $text);
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
		if ($this -> long == "") {
			return "";
		} else {
			$textXML = $this -> long;

			if ($html) {
				$text = substr($this -> xmlToHtml($this -> long, 'long'), 0, -1);
			} else {
				$text = $this -> long;
			}

			// Check for Links -> Change ID for URL
			$data = new SimpleXMLElement($textXML);
			foreach ($data -> xpath('//link') as $enlace) {
				if ($enlace['url_id'] == "") {
					$this -> Log -> writeLineError($this, "Link vacio :S");
				} else {
					$query = "SELECT * FROM ezurl WHERE id = " . $enlace['url_id'];
					$a = $this -> sql -> query($query);
					$link = $a -> fetch_object();

					if ($a -> num_rows == 0) {
						$this -> Log -> writeLineError($this, "Link no existe " . $enlace['url_id']);
					} else {
						$text = str_ireplace('href="' . $enlace['url_id'] . '">', 'href="' . $link -> url . '">', $text);
					}
					$a -> close();
				}

			}

			// usamos comillas simple para evitar romper el CSV
			return str_replace('"', "'", $text);
		}
	}

	public function setId($a) {
		$this -> post_id = $a;
	}

	public function setTitle($a) {
		$this -> title = str_replace('"', '”', $a);
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

	/**
	 * setLong
	 *
	 * @param string $xml
	 */
	public function setLong($xml) {
		$this -> long = $xml;
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

	/**
	 * getTitles
	 *
	 * Return the fields titles as string
	 * Return the title line for the CSV file in format "field_title", "field_title", "another_field"
	 *
	 * @return string Titles
	 */
	public function getTitles() {
		return '"post_id","post_title","post_type","post_status","post_date","post_category","post_thumbnail","post_excerpt","post_content"';
	}

	/**
	 * printLine
	 *
	 * Function for debug porpouses.
	 * Prints the content of the line.
	 */
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

	protected function writeLineError(Line $line, $txt) {
		$this -> Log -> writeLineError($line, $txt);
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

		if (!$xml -> loadXML($GoodContent)) {
			$this -> Log -> writeLineError($this, "Error al cargar el XML $type" . $GoodContent);
		}

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
