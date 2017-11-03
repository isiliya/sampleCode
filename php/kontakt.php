<!DOCTYPE html>
<html>
	<head>
		<title>Kontakt</title>
		<meta charset="UTF-8">
		<style>
		.meldung {
			font-family:arial;
			font-size:11pt;
			color:#369;
		}
		</style>
	</head>
	<body>
		<p class="meldung">
		<?php
		//Error Log Dateiname
		define("ERRORLOG","error.log");
		
		//Prüfen ob Datei mit den Zugangsdaten existiert und lesbar ist
		$zugang_db = "zugang_kontakte.php";
		if(is_readable($zugang_db))
		{
			//Benötigte Datei mit den Zugangsdaten einbinden
			require_once $zugang_db;
		}
		else
		{
			echo "Diese Seite ist zur Zeit nicht nutzbar<br>";
			//Fehler in die Error.Log schreiben
			$fehlertext = date("c") . ": Datei '$zugang_db' konnte nicht gelesen werden\n";
			file_put_contents(ERRORLOG,$fehlertext,FILE_APPEND | LOCK_EX);
			//Programm beenden
			exit();
		}
		
		
		$form = array("vorname" => "","nachname" => "","email" => "","mitteilung" => "","anrede" => "");
		//Variable für die Fehlerzahl
		$fehler = 0;
		
		?>
		</p>
		<p class="meldung">
		<?php
		//print_r($_POST);
		echo "<br>";
		
		//wurden die formulardaten versendet?
		if(isset($_POST["senden"]))
		{
			//Daten aus $_POST in $form speichern
			$form = $_POST;
			
			//Ist das vorname-Feld nicht ausgefüllt?
			if(empty($_POST["vorname"]))
			{
				$fehler++;
				echo "Bitte geben Sie Ihren Vornamen in das dafür vorgesehene Feld ein<br>";
			}
			
			//Ist das nachname-Feld nicht ausgefüllt?
			if(empty($_POST["nachname"]))
			{
				$fehler++;
				echo "Bitte geben Sie Ihren Nachnamen in das dafür vorgesehene Feld ein<br>";
			}
			
			//Ist das email-Feld nicht ausgefüllt?
			if(empty($_POST["email"]))
			{
				$fehler++;
				echo "Bitte geben Sie Ihre E-Mail-Adresse in das dafür vorgesehene Feld ein<br>";
			}
			
			//Ist das mitteilung-Feld nicht ausgefüllt?
			if(empty($_POST["mitteilung"]))
			{
				$fehler++;
				echo "Bitte geben Sie Ihre Mitteilung in das dafür vorgesehene Feld ein<br>";
			}
			
			//Wenn kein Fehler vorliegt
			if(!$fehler)
			{
				//Zugang zum DB-Server herstellen
				$db = new mysqli(HOST,USER,PASSWORD,DB);
				//Zeichenkodierung auf UTF8
				$db->set_charset("UTF8");
				//SQL-Befehl erstellen
				$sql = sprintf("INSERT INTO formular_kontakt
				(anrede,vorname,nachname,email,mitteilung) 
				VALUES
				('%s','%s','%s','%s','%s')",
				$_POST["anrede"],
				$_POST["vorname"],
				$_POST["nachname"],
				$_POST["email"],
				$_POST["mitteilung"]
				);
				//Befehl an den DB-Server schicken
				$db->query($sql);
				
				//Ist ein neuer Datensatz gespeichert worden?
				if($db->affected_rows == 1)
				{
					//speichern hat geklappt
					echo "Ihre Daten wurden erfolgreich gespeichert, Vielen Dank<br>";
					//wieder leeren
					$form = array("vorname" => "","nachname" => "","email" => "","mitteilung" => "","anrede" => "");
				}
				else
				{
					echo "Leider konnten Ihre Daten nicht gespeichert werden, Bitte versuchen Sie zu einem späteren Zeitpunkt noch einmal.<br>";
					//Informationen für die Error.log 
					//Fehlerliste
					$fehlerliste = $db->error_list[0];
					//Fehlertext erstellen
					$fehlertext = "";
					foreach($fehlerliste as $name => $wert)
					{
						$fehlertext .= ucfirst($name).": $wert ";
					}
					//Datum hinzufügen
					$fehlertext = date("c") . ": " . $fehlertext . "\n";
					
					//Fehlertext in die Error.log-Datei speichern
					file_put_contents(ERRORLOG,$fehlertext,FILE_APPEND | LOCK_EX);
				}
				
				//DB-Verbindung schließen
				$db->close();
			}
			
			
			
		}//Ende isset($_POST["senden"])
		
		?>
		</p>
		<h1>Kontakt</h1>
		<form action="kontakt.php" method="post">
		<label>Anrede:</label><br>
		<select name="anrede">
			<option><?php echo $form["anrede"]?></option>
			<option>Herr</option>
			<option>Frau</option>
		</select><br>
		
		<label>Vorname:</label><br>
		<input type="text" name="vorname" value="<?php echo $form["vorname"]?>"><br>
		
		<label>Nachname:</label><br>
		<input type="text" name="nachname" value="<?php echo $form["nachname"]?>"><br>
		
		<label>E-Mail:</label><br>
		<input type="text" name="email" value="<?php echo $form["email"]?>"><br>
		
		<label>Mitteilung:</label><br>
		<textarea name="mitteilung" cols="24" rows="6"><?php echo $form["mitteilung"]?></textarea><br>
		
		<input type="submit" name="senden" value="Senden">
		</form>
		
		<?php
		
		?>
	</body>
</html>