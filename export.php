<html>
	<head>
		<meta charset="UTF-8">
		<title>Export Data to CSV</title>
	</head>
	<body>
		<h2>Migración de las Noticias</h2>
		<p>Empezamos el proceso: <?php echo date('Y/m/d H:i:s') ?></p>
		<?php
		// Autoload class
		function __autoload($name) {
			$fullpath = 'inc/'.$name.'.php';
			if(file_exists($fullpath)) require_once($fullpath);
		}

		$mysqli = new mysqli("127.0.0.1", "root", "root", "madrid");

		/* comprobar la conexión */
		if ($mysqli -> connect_errno) {
			printf("Falló la conexión: %s\n", $mysqli -> connect_error);
			exit();
		}

		/* cambiar el conjunto de caracteres a utf8 */
		if (!$mysqli->set_charset("utf8")) {
			printf("Error cargando el conjunto de caracteres utf8: %s\n", $mysqli->error);
			exit();
		} else {
			printf("Conjunto de caracteres actual: %s\n", $mysqli->character_set_name());
		}

		echo "<h3>Migramos Noticias</h3>";

		/* Consultas de selección que devuelven un conjunto de resultados */
		$largo = true;
		$newsCategory = 30;
		if ($largo) {
			$query = "SELECT o.id,o.name,o.section_id,a.data_int,a.data_text ,a.data_type_string,a.version,c.identifier, published
				FROM ezcontentobject o inner join ezcontentobject_attribute a on o.id = a.contentobject_id
				inner join ezcontentclass_attribute c on a.contentclassattribute_id = c.id
				WHERE o.current_version = a.version -- Coje la versión actual
				AND o.contentclass_id = $newsCategory -- Filtra por noticias
				AND status = 1;";
		} else {
			$query = "SELECT o.id,o.name,o.section_id,a.data_int,a.data_text ,a.data_type_string,a.version,c.identifier, published
				FROM `ezcontentobject` o
				inner join ezcontentobject_attribute a on o.id = a.contentobject_id
				inner join ezcontentclass_attribute c on a.contentclassattribute_id = c.id
				where o.id in (868, 24302, 29845, 29779) and data_text <> ''
				AND o.current_version = a.version -- Coje la versión actual";

		}

		/* Si se ha de recuperar una gran cantidad de datos se emplea MYSQLI_USE_RESULT */
		if ($result = $mysqli->query($query)) {

			$id = 0;
			$line = new LinePosts($mysqli);
			$fichero = new File('posts', $line -> getTitles());


			// Cycle through results
			while ($row = $result -> fetch_object()){

				if($id == 0) {
					// Cas especial 1 volta
					$id = $row -> id;
				}

				if($id != $row -> id) {
					if ($line -> getPublished() == "") {
						$line -> setPublished($fecha);
					}

					try {
						$fichero -> saveLine($line -> printLineCSV());
					} catch (Exception $e) {
						echo "Line: " . $row -> id;
					}

					$line -> clearLine();
					$id = $row -> id;
				}

				//Omplim els camps fixes de la linia
				$line -> setId($row -> id);
				$fecha = $row -> published;

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
					case 'fecha':
						$line -> setPublished($row -> data_int);
						break;
				}
			}

			$result -> close();

			// El último a veces queda colgado
			try {
				$fichero -> saveLine($line -> printLineCSV());
			} catch (Exception $e) {
				echo "Line: " . $id;
			}
		}

		$mysqli->close();
		?>
		<p>Proceso Finalizado: <?php echo date('Y/m/d H:i:s') ?></p></p>
	</body>
</html>