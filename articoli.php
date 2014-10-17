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
		
		if(isset($_GET['delete']) && $new){
			$query = "delete from articoli where id='".$_GET['delete']."';";
			$res    = DbQuery($query,$link);
			DbFetchArray($res);
			print '<script type="text/javascript">window.opener.location.reload(true);</script>';
		}
		
       # if(empty( $_SESSION['articoli']) || empty( $_SESSION['tipologia'])){
			#if(isset($_GET['debug'])){echo "<h5>Refresh articoli</h5>";}
			#$query = "select * from configurazione;";
			#$res    = DbQuery($query,$link);
			#$array = DbFetchArray($res);
			#$qta_soglia = $array['qta_soglia'];
			#$query = "select articoli.*,tipologie.descrizione as tipologia from articoli left join tipologie on id_tipologia = tipologie.id order by descrizionebreve;";
			$query = "select articoli.*,tipologie.descrizione as tipologia from articoli left join tipologie on id_tipologia = tipologie.id order by posizione;";
			$res    = DbQuery($query,$link);
			while ($array = DbFetchArray($res)){
				#$result[$array['desc_tipologia']][] = $array;
				$articoli[$array['id']] = $array;
			}
		
		
		print "<form>";
		print '<button class="button-t" type="button" style="width:100%;background:#a5cc52;" onclick="window.open(\'modifica.php?id=new\',\'mywin\',\'width=600,height=500\');"><i class="fa fa-plus"></i>&nbsp;&nbsp;Nuovo</button>';
		print '<table style="width:100%;" valign="top" border="1" class="CSSTableGenerator">';
		print '<tr><td >Descrizione</td>';
		print '<td >Descrizione breve</td>';
		print '<td >Prezzo</td>';
		print '<td >Tipologia</td>';
		print '<td >Copia cucina</td>';
		print '<td >Copia bar</td>';
		print '<td >Copia pizzeria</td>';
		print '<td >Copia rosticceria</td>';
		print '<td >Pulsante</td>';
		print '<td >Modifica</td>';
		foreach ($articoli as $key=>$articolo){
			#if ($key != 'totale'){
			print '<tr><td>'.$articolo['descrizione'].'</td>';
			print '<td>'.$articolo['descrizionebreve'].'</td>';
			print '<td>'.$articolo['prezzo'].'</td>';
			print '<td>'.$articolo['tipologia'].'</td>';
			if($articolo['copia_cucina'] == 't'){print '<td style="text-align:center;"><i class="fa fa-check-square-o"></i></td>';}else{print '<td>&nbsp;</td>';};
			if($articolo['copia_bar'] == 't'){print '<td style="text-align:center;"><i class="fa fa-check-square-o"></i></td>';}else{print '<td>&nbsp;</td>';};
			if($articolo['copia_pizzeria'] == 't'){print '<td style="text-align:center;"><i class="fa fa-check-square-o"></i></td>';}else{print '<td>&nbsp;</td>';};
			if($articolo['copia_rosticceria'] == 't'){print '<td style="text-align:center;"><i class="fa fa-check-square-o"></i></td>';}else{print '<td>&nbsp;</td>';};
			#print '<td>'.$articolo['copia_cucina'].'</td>';
			#print '<td>'.$articolo['copia_bar'].'</td>';
			#print '<td>'.$articolo['copia_pizzeria'].'</td>';
			#print '<td>'.$articolo['copia_rosticceria'].'</td>';
			print '<td><button name="id" class="button-t" type="button" style="width:95%;background:#'.$articolo['col_buttone'].';color:#'.$articolo['col_testo'].';" >'.$articolo['descrizionebreve'].'</button></td>';
			print '<td style="text-align:center;"><button class="pure-button" type="button" style="background-color:transparent;" onclick="window.open(\'modifica.php?id='.$articolo['id'].'\',\'mywin\',\'width=600,height=300\');"><i class="fa fa-pencil"></i></button>';
			print '<button class="pure-button" name="delete" type="submit" style="background-color:transparent;" value="'.$articolo['id'].'"><i class="fa fa-trash"></i></button></td></tr>';
		}
		print '</table>';
		if (isset($_GET['debug'])){print '<input type="hidden" name="debug">';}
		if(isset($_GET['debug'])){print '<pre><hr><h4>DEBUG</h4>';print_r ($_SESSION);print '</pre>';}
		print '</form>';
		#if(isset($_GET['debug'])){print '<pre><hr><h4>DEBUG</h4>';print_r ($result);print '</pre>';}
		
?>        