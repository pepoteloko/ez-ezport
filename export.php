<?php

require 'autoload.php';

//$kernel = new ezpKernel( new ezpKernelWeb() );

$mysqli = new mysqli("127.0.0.1", "root", "root", "madrid");

/* comprobar la conexión */
if ($mysqli->connect_errno) {
	printf("Falló la conexión: %s\n", $mysqli->connect_error);
	exit();
}


/* Consultas de selección que devuelven un conjunto de resultados */
$query = "SELECT o.id,o.name,o.section_id,a.data_int,a.data_text ,a.data_type_string,a.version,c.identifier
		FROM `ezcontentobject` o
		inner join ezcontentobject_attribute a on o.id = a.contentobject_id
		inner join ezcontentclass_attribute c on a.contentclassattribute_id = c.id
		where o.id in (867, 868) and data_text <> ''";

/* Si se ha de recuperar una gran cantidad de datos se emplea MYSQLI_USE_RESULT */
if ($result = $mysqli->query($query, MYSQLI_USE_RESULT)) {

	$id = 0;
	$line = new Line();

	// Cycle through results
	while ($row = $result->fetch_object()){

		if($id == 0) {
			// Cas especial 1 volta
			$id = $row -> id;
		}

		if($id != $row -> id) {
			$line -> printLine();
			$line -> clearLine();
			$id = $row -> id;
		}

		// Omplim el camp que toca d'aquesta linia
		switch ($row -> identifier) {
			case 'titular':
				$line -> setTitle($row -> data_text);
				break;
			case 'imagen':
				$line -> setImage($row -> data_text);
				//$line -> debugImage();
				break;
			case 'entradilla':
				$line -> setShort($row -> data_text);
				break;
			case 'cuerpo':
				$line -> setLong($row -> data_text);
				break;
		}
	}

	$result -> close();

	// L'últim queda penjat
	$line -> printLine();
}

$mysqli->close();

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
	public function getShort($html = true) {
		if($html) {
			return $this -> xmlToHtml($this -> short);
		} else {
			return $this -> short;
		}
	}
	public function getLong($html = true) {
		if($html) {
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
		echo 'IMAGE:' . $this -> getImage()  . ';';
		//echo 'SHORT:' . $this -> getShort()  . ';';
		//echo 'LONG:' . $this -> getLong();
		echo "</pre><br />";
	}

	private function xmlToHtml($XMLContent) {
		$outputHandler = new eZXHTMLXMLOutput( $XMLContent, false );
		$html =& $outputHandler -> outputText();
		return $html;
	}
}
?>
