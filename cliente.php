<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 
<html>
<?php

        include('config.php'); 

        $link   = DbConnect($dbhost,$dbuser,$dbpass,$dbname);
		$query = "SELECT * from ordini where id = '".$_GET['id']."';";
        $res    = DbQuery($query,$link);
		$ordine = DbFetchArray($res);
		$query = "select sum(prezzo) from righe left join righe_articoli on righe.id = id_riga where id_ordine = '".$_GET['id']."';";
		$res    = DbQuery($query,$link);
		$totale = DbFetchRow($res);
		$query = "select * from righe left join righe_articoli on righe.id = id_riga where id_ordine = '".$_GET['id']."';";
		$res    = DbQuery($query,$link);
		while ($array = DbFetchArray($res)){
            $righe_articolo[] = $array;
		}
		if(isset($_GET['debug'])){print '<pre><hr><h4>DEBUG</h4>';print_r ($totale);print '</pre>';}
?>        
<body>
<img src="logo_bottom.png" >
<font size="-3">
<table cellpadding='4' width='100%'>
	<tr>
		<td>Cliente <b><?php echo $ordine['cliente']; ?></b></td>
		<td>Ordine numero <?php echo $ordine['progressivo']; ?></td>
		<td>del  <?php echo $ordine['data']; ?></td>
		<td>delle ore <?php echo $ordine['ora']; ?></td>
	</tr>
</table>
Cassiere <?php echo $ordine['cassiere']; ?><p /><font size='5'>COPIA PER CLIENTE</font>
<?php if ($ordine['esportazione'] == 't'){ ?>
<h4>PER ESPORTAZIONE</h4>
<?php } ?><p />
<font size='5'>Coperti <?php echo $ordine['coperti']; ?></font> &nbsp;  &nbsp;  &nbsp;<font size='5'> N. tavolo <?php echo $ordine['numeroTavolo']; ?></font><p />
<table>
	<tr>
		<td>Descrizione</td>
		<td>Quantit&agrave;</td>
		<td>Prezzo unitario</td>
		<td>Prezzo totale</td>
		<td>Note</td>
	</tr>

		<?php foreach ($righe_articolo as $riga_ordine){ ?>
				<tr>
					<td><?php echo $riga_ordine['descrizione']; ?></td>
					<td align='center'><?php echo $riga_ordine['quantita']; ?></td>
					<td align='center'><?php echo $riga_ordine['prezzo']; ?>&euro;</td>
					<td align='center'><?php echo ($riga_ordine['prezzo'] * $riga_ordine['quantita']); ?>&euro;</td>
					<td align='center'><?php echo $riga_ordine['note']; ?></td>
				</tr>	
		<?php } ?>
</table><p />
Note: <?php echo $ordine['note']; ?><p />
<h4>Totale ordine:<?php echo $totale[0]; ?>&euro;</h4>
</font>
</body>
</html>