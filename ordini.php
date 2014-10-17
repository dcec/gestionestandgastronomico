<?php
$page = $_SERVER['PHP_SELF'];
$sec = "30";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html>
<head>
  <meta name="generator" content="HTML Tidy for Linux (vers 14 June 2007), see www.w3.org">
  <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
</head>
<?php

	
	include('sidebar.php');
	include('config.php'); 
	menu();
	
	if (!isset($_SESSION)) session_start();
    if (!empty($_GET)){
		if (!empty( $_SESSION['got'])){
			if ($_SESSION['got'] != $_GET){
				$new = 1;
				#unset($_SESSION['got']);
				$_SESSION['got'] = $_GET;
				#echo "<h5>New  OK</h5>";
			}else{
				#echo "<h5>Old</h5>";
			}
		}else{
			$_SESSION['got'] = $_GET;
		}	
    }
	
	#print_r ($_SESSION['got']);
	
	$link	= DbConnect($dbhost,$dbuser,$dbpass,$dbname);
	
	if(isset($_GET['nuovo']) and $new and $_GET['nuovo'] != ""){
		if ($solo_nuovi) { $queryu	= "UPDATE ordini SET stato=2 WHERE progressivo='".$_GET['nuovo']."';";}
		else{$queryu	= "UPDATE ordini SET stato=1 WHERE progressivo='".$_GET['nuovo']."';";}
		$res	= DbQuery($queryu,$link);
		$_SESSION['old'][] = $_GET['nuovo'];
		unset($_GET['nuovo']);
		#echo "<h5>nuovo</h5>";
	}
	elseif(isset($_GET['manuale']) and $new and $_GET['manuale'] != ""){
		if ($solo_nuovi) { $queryu	= "UPDATE ordini SET stato=2 WHERE progressivo='".$_GET['manuale']."';";}
		else{$queryu	= "UPDATE ordini SET stato=1 WHERE progressivo='".$_GET['manuale']."';";}
		$res	= DbQuery($queryu,$link);
		$_SESSION['old'][] = $_GET['manuale'];
		unset($_GET['manuale']);

		#echo "<h5>manuale</h5>";
	}
	elseif(isset($_GET['lavorazione']) and $new and $_GET['lavorazione'] != ""){
		$queryu	= "UPDATE ordini SET stato=2 WHERE progressivo='".$_GET['lavorazione']."';";
		$res	= DbQuery($queryu,$link);
		unset($_GET['lavorazione']);
		#echo "<h5>lavorazione</h5>";
	}
	elseif(isset($_GET['recuperam']) and $new and $_GET['recuperam'] != ""){
		$queryu	= "UPDATE ordini SET stato=0 WHERE progressivo='".$_GET['recuperam']."';";
		$res	= DbQuery($queryu,$link);
		unset($_GET['recuperam']);
		#echo "<h5>recupera</h5>";
	}
	elseif(isset($_GET['recupera']) and $_GET['recupera'] != ""){
		$queryu	= "UPDATE ordini SET stato=0 WHERE progressivo='".$_SESSION['old'][$_GET['recupera']]."';";
		$res	= DbQuery($queryu,$link);
		unset($_SESSION['old'][$_GET['recupera']]);
		$old = $_SESSION['old'];
		unset($_SESSION['old']);
		foreach ($old as $c){
			$_SESSION['old'][] = $c;
		}
		unset($_GET['recupera']);
		#echo "<h5>recupera</h5>";
	}
	
	if(isset($_GET['print'])){
		shell_exec('html2ps http://192.168.1.5/sagra/cucina.php | lp -d CLX-3300');
	}

	
	#$query	= "SELECT *,COUNT( descrizionebreve) AS toselle from ordini left join righe on ordini.id = id_ordine;";
	#$query	= "SELECT * from ordini;";
	$query	= "SELECT * FROM (SELECT ordini.*";
	$query	.= ",(SELECT  COUNT(*) from righe where ordini.id = id_ordine and descrizionebreve like 'Tosella') as Toselle";
	$query	.= ",(SELECT  COUNT(*) from righe left join righe_articoli on righe.id = righe_articoli.id_riga where ordini.id = id_ordine and copia_cucina = 't') as cucina ";
	$query	.= "from ordini where stato < 2) as ordini where cucina > 0 order by serata,progressivo;";# limit ".($limit*2)."
	#$query	= "SELECT *.ordini, COUNT( descrizionebreve.righe  ) AS toselle from ordini left join righe on ordini.id = id_ordine; SELECT ordini.*,(SELECT  COUNT(*) from righe where ordini.id = id_ordine and descrizionebreve like 'Tosella') as Toselle from ordini ;
	$res	= DbQuery($query,$link);
	while ($array = DbFetchArray($res)){
		$result[$array['stato']][] = $array;
	}
	
	print "<form>";
	if (isset($_GET['debug'])){print '<input type="hidden" name="debug">';}
	## NUOVI ORDINI
	$count = 0;
	$totcop = 0;
	#print '<button name="print" type="submit" style="width:100%;" id="stampa" >Stampa</button>';
	#print '<br><hr>';
	
	foreach ($result[0] as $c){
		#$count ++;
		#if($count == 6){$button .= '</tr><tr>';}
		#if($count == 11){$button .= '</tr><tr>';}
		#if($count == 16){$button .= '</tr><tr>';}
		#if($count == 21){$button .= '</tr><tr>';}
		if(($count % $nbutton) == 0 and $count > 0){$button .= '</tr><tr>';}
		$coperti = isset($c['coperti']) ? $c['coperti'] : "0";
		$totcop += $coperti;
		$class = ($c['toselle'] > 0) ? "orange" : "green";
		#if($count < 26){$button .= '<td><button name="nuovo" class="'.$class.'" type="submit" style="width:100%;" id="'.$c['id'].'" value="'.$c['id'].'">Ordine '.$c['progressivo'].', coperti '.$coperti.'</button></td>';}
		if($count < $limit){$button .= '<td><button name="nuovo" class="button '.$class.'" type="submit" style="width:95%;" id="'.$c['progressivo'].'" value="'.$c['progressivo'].'">'.$c['progressivo'].'</button></td>';}
		$count ++;
	}
	print '<h4>TOTALE NUOVI ORDINI '.$count.', COPERTI '.$totcop.'</h4>';
	print '<table style="width:100%;border-collapse:separate;border-spacing:0 12px;">
      <tr>';
	print $button;
	print '</tr>';
	print '<tr><td colspan="'.$nbutton.'">Nuovo ordine<input name="manuale" size="10" id="nuovo_ordine" type="text"><button type="submit">Inserisci</button></td></tr>';
    print '</table>';
	print '<br><hr>';
	## ORDINI IN LAVORAZIONE
	if (!$solo_nuovi) {
		$count = 0;
		$totcop = 0;
		foreach ($result[1] as $c){
			#$count ++;
			#if($count == 6){$buttonl .= '</tr><tr>';}
			#if($count == 11){$buttonl .= '</tr><tr>';}
			#if($count == 16){$buttonl .= '</tr><tr>';}
			#if($count == 21){$buttonl .= '</tr><tr>';}
			if(($count % $nbutton) == 0 and $count > 0){$button .= '</tr><tr>';}
			$coperti = isset($c['coperti']) ? $c['coperti'] : "0";
			$totcop += $coperti;
			$class = ($c['toselle'] > 0) ? "orange" : "green";
			#if($count < 26){$buttonl .= '<td><button name="lavorazione" class="'.$class.'" type="submit" style="width:100%;" id="'.$c['id'].'" value="'.$c['id'].'">Ordine '.$c['progressivo'].', coperti '.$coperti.'</button></td>';}
			if($count < $limit){$buttonl .= '<td><button name="lavorazione" class="'.$class.'" type="submit" style="width:100%;" id="'.$c['progressivo'].'" value="'.$c['progressivo'].'">'.$c['progressivo'].'</button></td>';}
			$count ++;
		}
		print '<h4>TOTALE ORDINI IN LAVORAZIONE '.$count.', COPERTI '.$totcop.'</h4>';
		print '<table style="width:100%;">
		  <tr>';
		print $buttonl;
		print '</tr></table>';
	}
	print '<table style="width:100%;"><tr>';
	#for ($i = count($_SESSION['old']); $i <= 10; $i++) {
	print '<tr>';
	$i = count($_SESSION['old']);
	#if(isset($_GET['debug'])){print '<pre><hr><h4>DEBUG</h4>';print_r ($_SESSION['old'][$i]);print '</pre>';}
	if ($i>0){print '<td><button name="recupera" class="button red" type="submit" style="width:100%;" value="'.($i-1).'">'.$_SESSION['old'][($i-1)].'</button></td>';}
	if ($i>1){print '<td><button name="recupera" class="button red" type="submit" style="width:100%;" value="'.($i-2).'">'.$_SESSION['old'][($i-2)].'</button></td>';}
	if ($i>2){print '<td><button name="recupera" class="button red" type="submit" style="width:100%;" value="'.($i-3).'">'.$_SESSION['old'][($i-3)].'</button></td>';}
	if ($i>3){print '<td><button name="recupera" class="button red" type="submit" style="width:100%;" value="'.($i-4).'">'.$_SESSION['old'][($i-4)].'</button></td>';}
	if ($i>4){print '<td><button name="recupera" class="button red" type="submit" style="width:100%;" value="'.($i-5).'">'.$_SESSION['old'][($i-5)].'</button></td>';}
	print '</tr>';
	print '<tr><td colspan="'.$nbutton.'">Recupera ordine<input name="recuperam" size="10" id="recupara_ordine" type="text"><button type="submit">Recupera</button></td></tr>';
	print '</tr></table>';
	print '</form>';

	if(isset($_GET['debug'])){print '<pre><hr><h4>DEBUG</h4>';print_r ($result);print_r ($_SESSION['old']);print '</pre>';}
?>

