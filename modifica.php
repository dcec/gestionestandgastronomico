<?php
	
        include('config.php');
		include('sidebar.php');
		
		$link   = DbConnect($dbhost,$dbuser,$dbpass,$dbname);
		if (!isset($_SESSION)) session_start();
		$articolo['col_buttone'] = "a5cc52";
		$articolo['col_testo'] = "ffffff";
		
	if(isset($_GET['modifica'])){
		if($_GET['id'] == 'new'){
			$articolo['descrizione'] = $_GET['descrizione'];
			$articolo['descrizionebreve'] = $_GET['descrizionebreve'];
			$articolo['prezzo'] = $_GET['prezzo'];
			# controllo campi obbligatori
			if(empty($_GET['descrizione'])){ print '<pre><h4>Descrizione Richiesta</h4>';print '</pre>';goto end;}
			if(empty($_GET['descrizionebreve'])){ print '<pre><h4>Descrizionebreve Richiesta</h4>';print '</pre>';goto end;}
			if(empty($_GET['prezzo'])){ print '<pre><h4>Prezzo Richiesta</h4>';print '</pre>';goto end;}
			foreach($_GET as $key => $value) {
				if($key != 'modifica' && $key != 'id'){
					$data[] = $key;
					if($key == 'copia_cucina' || $key == 'copia_bar' ||$key == 'copia_pizzeria' ||$key == 'copia_rosticceria'){
						if($value == ""){$values[] = 'f';}else{$values[] = 't';}
					}else{
						$values[] = pg_escape_string ($value);
					}
					if($key == 'descrizionebreve') $descrizionebreve = pg_escape_string ($value);
					if($key == 'descrizione') $descrizione = pg_escape_string ($value);
				}
			}
			
			$query = "select count(id) as count from articoli where descrizione = E'".$descrizione."';";
			$res    = DbQuery($query,$link);
			$count = DbFetchArray($res);
			if($count['count'] > 0){ print '<pre><h4>Descrizione già esistente</h4>';print '</pre>';goto end;}
			
			$query = "select count(id) as count from articoli where descrizionebreve = E'".$descrizionebreve."';";
			$res    = DbQuery($query,$link);
			$count = DbFetchArray($res);
			if($count['count'] > 0){ print '<pre><h4>Descrizionebreve già esistente</h4>';print '</pre>';goto end;}
			
			$query = "SELECT max(articoli.posizione) AS max  from articoli;";
			$res    = DbQuery($query,$link);
			$posizione = DbFetchArray($res);
			#print '<pre><hr><h4>DEBUG</h4>';print_r ($posizione);print '</pre>';
			
			$queryu = "INSERT INTO articoli (".implode(" ,", $data).",posizione) VALUES ('".implode("' ,'", $values)."','".$posizione['max']."') RETURNING articoli.id;";
			#print '<pre><hr><h4>DEBUG</h4>';print_r ($queryu);print '</pre>';			
		}else{
			$queryu = "UPDATE articoli SET ";
			if(isset($_GET['descrizione'])){$queryu .= "descrizione = '".pg_escape_string ($_GET['descrizione'])."',";}
			if(isset($_GET['descrizionebreve'])){$queryu .= "descrizionebreve = '".pg_escape_string ($_GET['descrizionebreve'])."',";}
			if(isset($_GET['prezzo'])){$queryu .= "prezzo = '".pg_escape_string ($_GET['prezzo'])."',";}
			if(isset($_GET['id_tipologia'])){$queryu .= "id_tipologia = '".pg_escape_string ($_GET['id_tipologia'])."',";}
			if(isset($_GET['copia_cucina'])){$queryu .= "copia_cucina = 't',";}else{$queryu .= "copia_cucina = 'f',";}
			if(isset($_GET['copia_bar'])){$queryu .= "copia_bar = 't',";}else{$queryu .= "copia_bar = 'f',";}
			if(isset($_GET['copia_pizzeria'])){$queryu .= "copia_pizzeria = 't',";}else{$queryu .= "copia_pizzeria = 'f',";}
			if(isset($_GET['copia_rosticceria'])){$queryu .= "copia_rosticceria = 't',";}else{$queryu .= "copia_rosticceria = 'f',";}
			if(isset($_GET['col_buttone'])){$queryu .= "col_buttone = '".pg_escape_string ($_GET['col_buttone'])."',";}
			if(isset($_GET['col_testo'])){$queryu .= "col_testo = '".pg_escape_string ($_GET['col_testo'])."',";}
			$queryu = preg_replace('/,$/', '', $queryu);
			$queryu .= " WHERE id='".$_GET['id']."' RETURNING articoli.id;";
		}
		$res	= DbQuery($queryu,$link);
		$idrighe_articoli = DbFetchArray($res);
		if ($idrighe_articoli > 0){ 
			print '<script type="text/javascript">refreshAndClose();</script>';
		}else{print '<pre><h4>Errore Query:'.$idrighe_articoli.' </h4>';print '</pre>';}
		end:
	}
	if(isset($_GET['id']) && $_GET['id'] != 'new'){  
		$query = "select * from articoli where id = '".$_GET['id']."';";
		$res    = DbQuery($query,$link);
		$articolo = DbFetchArray($res);
		#print '<pre><hr><h4>DEBUG</h4>';print_r ($articolo);print '</pre>';
	}	
	#
		$query = "select * from tipologie;";
		$res    = DbQuery($query,$link);
		while ($array = DbFetchArray($res)){
			$tipologie[$array['id']] = $array;
		}
		
		
		print "<form>";
		print '<input type="text" name="id" value="'.$_GET['id'].'" hidden>';
		print '<table style="width:100%;" valign="top" border="1">';
		print '<tr><td>Descrizione</td><td><input type="text" name="descrizione" value="'.$articolo['descrizione'].'"></td></tr>';
		print '<td >Descrizione breve</td><td><input type="text" name="descrizionebreve" value="'.$articolo['descrizionebreve'].'"></td></tr>';
		print '<td >Prezzo</td><td><input type="text" name="prezzo" value="'.$articolo['prezzo'].'"></td></tr>';
		print '<td >Tipologia</td><td><select name="id_tipologia">';
		foreach ($tipologie as $key=>$tipologia){
			print '<option';
			if($key == $articolo['id_tipologia']){print ' selected="selected"';}
			print ' value="'.$key.'">'.$tipologia['descrizione'].'</option>';
		}
		print '</select></td></tr>';
		print '<td >Copia cucina</td><td><input type="checkbox" name="copia_cucina" value="'.$articolo['copia_cucina'].'" ';
		if($articolo['copia_cucina'] == 't'){print ' checked="checked"';}print '></td></tr>';
		print '<td >Copia bar</td><td><input type="checkbox" name="copia_bar" value="'.$articolo['copia_bar'].'" ';
		if($articolo['copia_bar'] == 't'){print ' checked="checked"';}print '></td></tr>';
		print '<td >Copia pizzeria</td><td><input type="checkbox" name="copia_pizzeria" value="'.$articolo['copia_pizzeria'].'" ';
		if($articolo['copia_pizzeria'] == 't'){print ' checked="checked"';}print '></td></tr>';
		print '<td >Copia rosticceria</td><td><input type="checkbox" name="copia_rosticceria" value="'.$articolo['copia_rosticceria'].'" ';
		if($articolo['copia_rosticceria'] == 't'){print ' checked="checked"';}print '></td></tr>';
		print '<td >Colore Bottone</td><td><input class="Multiple" name="col_buttone" type="text" value="'.$articolo['col_buttone'].'"></td></tr>';
		print '<td >Colore Testo</td><td><input class="Multiple" name="col_testo" type="text" value="'.$articolo['col_testo'].'"></td></tr>';
		print '</table>';
		print '<button name="modifica" class="button-t" type="submit" style="width:100%;background:#a5cc52;" value="1">';
		if($_GET['id'] == 'new'){print 'Inserisci';}else{print 'Modifica';}
		print '</button>';
		if (isset($_GET['debug'])){print '<input type="hidden" name="debug">';}
		if(isset($_GET['debug'])){print '<pre><hr><h4>DEBUG</h4>';print_r ($_SESSION);print '</pre>';}
		print '</form>';
		#if(isset($_GET['debug'])){print '<pre><hr><h4>DEBUG</h4>';print_r ($result);print '</pre>';}
		
?>        