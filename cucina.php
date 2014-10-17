<?php
$page = $_SERVER['PHP_SELF'];
$sec = "10";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN">

<html>
<head>
  <meta name="generator" content="HTML Tidy for Linux (vers 14 June 2007), see www.w3.org">
  <meta http-equiv="refresh" content="<?php echo $sec?>;URL='<?php echo $page?>'">
</head><?php


		include('sidebar.php');
        include('config.php');  

        $link   = DbConnect($dbhost,$dbuser,$dbpass,$dbname);
        
        #$query = "SELECT *,COUNT( descrizionebreve) AS toselle from ordini left join righe on ordini.id = id_ordine;";
        $query  = " SELECT descrizione,sum(quantita) as total from ordini 
        left join righe on ordini.id = id_ordine
        left join righe_articoli on righe.id = id_riga
        where stato < 2 and copia_cucina = 't'
        group by descrizione order by total DESC;";
        #$query = "SELECT ordini.*,(SELECT  COUNT(*) from righe where ordini.id = id_ordine and descrizionebreve like 'Tosella') as Toselle from ordini order by ordini.id;";
        #$query = "SELECT *.ordini, COUNT( descrizionebreve.righe  ) AS toselle from ordini left join righe on ordini.id = id_ordine; SELECT ordini.*,(SELECT  COUNT(*) from righe where ordini.id = id_ordine and descrizionebreve like 'Tosella') as Toselle from ordini ;
        $res    = DbQuery($query,$link);
        while ($array = DbFetchArray($res)){
                $result[] = $array;
        }
        
        print "<form>";
        print '<div class="TabellaCucina" >';
    print '<table ><tr><td>PRODOTTO</td><td>QUANTITA\'</td></tr>';
        foreach ($result as $c){
                print '<tr><td >'.$c['descrizione'].'</td><td>'.$c['total'].'</td></tr>';
        }
        print '</table>';
        print '</form>';

        if(isset($_GET['debug'])){print '<pre><hr><h4>DEBUG</h4>';print_r ($result);print '</pre>';}
?>

<body>
</body>
</html>
