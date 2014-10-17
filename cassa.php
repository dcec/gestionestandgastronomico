<?php
		include('sidebar.php');
		include('config.php'); 
		menu();
		$link   = DbConnect($dbhost,$dbuser,$dbpass,$dbname);
		if (!isset($_SESSION)) session_start();
		if (!empty($_GET)){
			if (!empty( $_SESSION['got'])){
				if ($_SESSION['got'] != $_GET){
					$new = 1;
					$_SESSION['got'] = $_GET;
					#unset($_SESSION['got']);
					#echo "<h5>New  OK</h5>";
				}else{
					#echo "<h5>Old</h5>";
				}
			}else{
				$_SESSION['got'] = $_GET;
			}	
		}
		$query = "select * from configurazione;";
		$res    = DbQuery($query,$link);
		$array = DbFetchArray($res);
		$qta_soglia = $array['qta_soglia'];
			
		if(isset($_GET['cancel'])){
			$_SESSION['ordine'] = array();
			$_SESSION['articoli'] = array();
			$_SESSION['tipologia'] = array();
			$_GET['asporto'] = "";
			$_GET['tavolo'] = "";
			$_GET['coperti'] = "";
		}
		#print_r ($_SESSION['got']);		
        if(empty( $_SESSION['articoli']) || empty( $_SESSION['tipologia'])){
			if(isset($_GET['debug'])){echo "<h5>Refresh articoli</h5>";}
			$query = "select * from configurazione;";
			$res    = DbQuery($query,$link);
			$array = DbFetchArray($res);
			$qta_soglia = $array['qta_soglia'];
			#$query = "select articoli.*,tipologie.descrizione as tipologia from articoli left join tipologie on id_tipologia = tipologie.id order by descrizionebreve;";
			$query = "select articoli.*,tipologie.descrizione as desc_tipologia,tipologie.posizione as pos_tipologia,scorta_iniziale,data_disponibilita from articoli left join tipologie on id_tipologia = tipologie.id left join giacenze on id_giacenza = giacenze.id order by posizione;";
			$res    = DbQuery($query,$link);
			while ($array = DbFetchArray($res)){
				$result[$array['desc_tipologia']][] = $array;
				$articoli[$array['id']] = $array;
			}
			$_SESSION['articoli'] = $articoli;
			$_SESSION['tipologia'] = $result;
		}
		#if(isset($_GET['debug'])){echo "<h5>New: ".$new."</h5>";}
		if(isset($_GET['stampa']) && $new){
			#echo "<h5>Stampa</h5>";
			$queryu	= "select progressivo,(select data from legame_serata_ordini where a_id is null) as data from ordini order by progressivo desc limit 1;";
			$res	= DbQuery($queryu,$link);
			while ($array = DbFetchArray($res)){
                $data[] = $array;
			}
			#echo "<h5>Stampa:".$data[0]['data']."</h5>";
			$export = (isset($_GET['asporto']))?t:f;
			$totale = $_GET['totale'] + $_GET['resto'];
			$coperti = ($_GET['coperti'] != "")? $_GET['coperti']:0;
			$queryi = "INSERT INTO ordini (\"numeroTavolo\", cassiere, progressivo, data, ora, coperti, \"totalePagato\", resto, esportazione, anno, serata, menu_omaggio )";
			$queryi .= "VALUES ('".$_GET['tavolo']."', '', '".($data[0]['progressivo']+1)."',current_date,current_time,'".$coperti."','".$totale."','".$_GET['resto']."','".$export."',EXTRACT(YEAR FROM now()),'".$data[0]['data']."','f') RETURNING ordini.id;";
			if(isset($_GET['debug'])){print $queryi."\n";}
			$res	= DbQuery($queryi,$link);
			$array = DbFetchArray($res);
			if(!$array['id']){echo "<h5>Errore inserimento</h5>";}
			else{
				#echo "<h5>Stampa:".$array['id']."</h5>";
				#if(isset($_GET['debug'])){print '<pre><hr><h4>DEBUG</h4>';print_r ($id);print '</pre>';}
				foreach ($_SESSION['ordine'] as $articolo){
					$articolo['descrizione'] = pg_escape_string ($articolo['descrizione']);
					$articolo['descrizionebreve'] = pg_escape_string ($articolo['descrizionebreve']);
					$queryi = "INSERT INTO righe (quantita, id_ordine, type, descrizione, descrizionebreve)";
					$queryi .= "VALUES ('".$articolo['quantita']."','".$array['id']."','riga_articolo','".$articolo['descrizione']."','".$articolo['descrizionebreve']."') RETURNING id;";
					if(isset($_GET['debug'])){print $queryi."\n";}
					$res	= DbQuery($queryi,$link);
					$idrighe = DbFetchArray($res);
					#echo "<h5>Stampa:".$idrighe['id']."</h5>";
					$queryi = "INSERT INTO righe_articoli (id, id_riga, prezzo, copia_cucina, copia_bar, copia_cliente, copia_pizzeria, desc_tipologia, pos_tipologia, posizione, note, copia_rosticceria)";
					$queryi .= "VALUES ('".$idrighe['id']."','".$idrighe['id']."','".$articolo['prezzo']."','".$articolo['copia_cucina']."','".$articolo['copia_bar']."','".$articolo['copia_cliente']."','".$articolo['copia_pizzeria']."'";
					$queryi .= ",'".$articolo['desc_tipologia']."','".$articolo['pos_tipologia']."','".$articolo['posizione']."','".$articolo['note']."','".$articolo['copia_rosticceria']."') RETURNING id;";
					if(isset($_GET['debug'])){print $queryi."\n";}
					$res	= DbQuery($queryi,$link);
					$idrighe_articoli = DbFetchArray($res);
					#echo "<h5>Stampa:".$idrighe_articoli['id']."</h5>";
				}
				echo "<h5>Ordine ".$array['progressivo']." inserito</h5>";
				$_SESSION['ordine'] = array();
				$_GET['asporto'] = "";
				$_GET['tavolo'] = "";
				$_GET['coperti'] = "";
			}
		}
		
		if(isset($_GET['id']) && $new){
			if (array_key_exists($_GET['id'],$_SESSION['ordine'])){
				$_SESSION['ordine'][$_GET['id']]['quantita'] += 1;
				#if(isset($_GET['debug'])){print '<pre><hr><h4>update '.$_SESSION['articoli'][$_GET['id']]['descrizionebreve'].'</h4>';print '</pre>';}
			}else{
				$_SESSION['ordine'][$_GET['id']] = $_SESSION['articoli'][$_GET['id']];
				$_SESSION['ordine'][$_GET['id']]['quantita'] = 1;
				#if(isset($_GET['debug'])){print '<pre><hr><h4>add '.$_SESSION['articoli'][$_GET['id']]['descrizionebreve'].'</h4>';print '</pre>';}
			}
			#if (array_key_exists('totale',$_SESSION['ordine'])){
			#	$_SESSION['ordine']['totale'] += $_SESSION['ordine'][$_GET['id']]['prezzo'];
			#}else{
			#	$_SESSION['ordine']['totale'] = $_SESSION['ordine'][$_GET['id']]['prezzo'];
			#}
		}
		if(isset($_GET['delete']) && $new){
			if ($_SESSION['ordine'][$_GET['delete']]['quantita'] > 1){
				$_SESSION['ordine'][$_GET['delete']]['quantita'] -= 1;
			}else{
				unset($_SESSION['ordine'][$_GET['delete']]);
			}
		}
		
		print "<form>";
		print '<table style="width:100%;"><td valign="top">';
		foreach ($_SESSION['tipologia'] as $nome=>$tipologia){
			$count = 0;
			$totcop = 0;
			$button = "";
			foreach ($tipologia as $articolo){
				if(($count % $nbutton) == 0 and $count > 0){$button .= '</tr><tr>';}
				$class = "green";
				$disabled = "";
				$soglia = "";
				$style = 'style="width:95%;background:#'.$articolo['col_buttone'].';color:#'.$articolo['col_testo'].';"';
				$button .= '<td><button name="id" class="button-t" type="submit" id="'.$articolo['id'].'" value="'.$articolo['id'].'" ' ;
				if($articolo['scorta_iniziale'] != ""){
				#	#$querys	= "SELECT descrizionebreve,sum(quantita) as total from ordini left join righe on ordini.id = id_ordine where '".$articolo['data_disponibilita']."';";
					$querys	= "SELECT sum(quantita) as total from ordini left join righe on ordini.id = id_ordine  where descrizione = '".pg_escape_string ($articolo['descrizione'])."' and (data + ora ) > '".$articolo['data_disponibilita']."';";
					$res = DbQuery($querys,$link);
					$result = DbFetchArray($res);
					$totale = ($result['total'] == "")?0:$result['total'];
					$battuto = ($_SESSION['ordine'][$articolo['id']]['quantita'])?$_SESSION['ordine'][$articolo['id']]['quantita']:0;
					#$totale['total'] = ($totale['total'] = "")?0:$totale['total'];
					$rimanenza =  $articolo['scorta_iniziale'] - $totale - $battuto;
					$disabled = 'totale="'.$totale.'" rimanenza="'.$rimanenza.'" qta_soglia="'.$qta_soglia.'" battuto="'.$battuto.'"';
					if($rimanenza < $qta_soglia && $rimanenza > 0){
						
						$soglia = '&nbsp;&nbsp;<span style="border:1px solid black;border-radius: 6px;padding : 0 3px;background-color:#8c9d8c">'.$rimanenza.'</span>';
				#		$button .= '>'.$articolo['descrizionebreve'].'&nbsp;&nbsp;<span style="border:1px solid black;border-radius: 6px;padding : 0 3px;background-color:#8c9d8c">'.$rimanenza.'</span>';
					}#else{ $disabled = 'disabled rimanenza="'.$rimanenza.'" qta_soglia="'.$qta_soglia.'"';}
					if ($rimanenza <= 0){ 
						$disabled = 'disabled rimanenza="'.$rimanenza.'" qta_soglia="'.$qta_soglia.'"';
						$style = 'style="width:95%;background:#E6E6E6;"';
					}
				}#else{$button .= '>'.$articolo['descrizionebreve'];}
				$button .= $style . $disabled .'>'.$articolo['descrizionebreve'].$soglia.'</button></td>';
				$count ++;
			}		
			print '<table name="tipologia" style="width:100%;border-collapse:separate;border-spacing:0 5px;" class="CSSTable"><tr>';
			print '<td colspan="'.$nbutton.'">'.$nome.'</td></tr><tr>';
			print $button;
			print '</tr></table>';
		}
		
		print '</td><td valign="top" border="1" class="CSSTableGenerator CSSTableGeneratorcassa">';
		print '<table style="width:100%;">';
		print '<tr><td colspan="5">Asporto<input type="checkbox" name="asporto" value="1" ';
		if($_GET['asporto'] == 1){print ' checked="checked"';};
		print '>&nbsp;Tavolo<input name="tavolo" size="2" id="tavolo" type="text" value="'.$_GET['tavolo'].'">&nbsp;Coperti<input name="coperti" size="2" id="coperti" type="text" value="'.$_GET['coperti'].'"></td></tr>';
		print '<tr><td>Descrizione</td><td>Prezzo</td><td>Quantità</td><td>Totale</td><td></td></tr>';
		$totale = 0;
		foreach ($_SESSION['ordine'] as $key=>$articolo){
			if ($key != 'totale'){
			print '<tr><td >'.$articolo['descrizionebreve'].'</td><td>'.$articolo['prezzo'].'&euro;</td>';
			print '<td >'.$articolo['quantita'].'</td><td>'.sprintf('%0.2f', ($articolo['prezzo']*$articolo['quantita'])).'&euro;</td>';
			print '<td style="text-align:center;"><button class="pure-button" name="delete" type="submit" style="background-color:transparent;" value="'.$articolo['id'].'"><i class="fa fa-trash"></i></button></td></tr>';
			$totale += $articolo['prezzo'] * $articolo['quantita'];
			}
		}
		#print '<table style="width:100%;">';
		print '<tr><td colspan="5">Totale<input type="text" size="2" id="totale" name="totale" value="'.sprintf('%0.2f',$totale).'" class="ui-keyboard-input ui-widget-content ui-corner-all"/>&euro;&nbsp;&nbsp;';
		print 'Contanti<input name="contanti" size="2" id="contanti" onchange="updateInput(value)" type="text">&euro;&nbsp;&nbsp;Resto<input type="text" size="2" id="resto" name="resto" value="0" class="ui-keyboard-input ui-widget-content ui-corner-all"/>&euro;</td></tr>';
		#print '</table>';
		print '<tr><td colspan="3"><button name="stampa" class="button-t" type="submit" style="width:100%;background:#a5cc52;" value="1">Stampa</button></td>';
		print '<td colspan="2"><button name="cancel" class="button-t" type="submit" style="width:95%;background:DarkOrange;">Cancella</button></td></tr>';
		print '</table>';
		print '</td></table>';
		if (isset($_GET['debug'])){print '<input type="hidden" name="debug">';}
		if(isset($_GET['debug'])){print '<pre><hr><h4>DEBUG</h4>';print_r ($_SESSION);print '</pre>';}
		print '</form>';
		#if(isset($_GET['debug'])){print '<pre><hr><h4>DEBUG</h4>';print_r ($result);print '</pre>';}
		
?>        