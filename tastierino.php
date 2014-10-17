<link rel="stylesheet" href="tables-min.css">
<?php
		#if(!$_SESSION['ordine']['totalePagato']){$_SESSION['ordine']['totalePagato']=0;}
		if (!isset($_SESSION)) session_start();
		if(isset($_GET['number'])){
			$_SESSION['ordine']['totalePagato'] .= $_GET['number'];
		}
		if(isset($_GET['cancel'])){
			$_SESSION['ordine']['totalePagato'] = "";
		}
		print "<form>";
		print '<table style="width:100%;"><td>';
		print '<table style="width:100%;"><td>';
		$banconote = array("0,1"=>"0,1","0,2"=>"0,2","0,5"=>"0,5","1"=>"1","2"=>"2","5"=>"5","10"=>"10","20"=>"20","50"=>"50","100"=>"100","200"=>"200");
		foreach ($banconote as $nome=>$valore){
			print '<tr><td><button name="add" class="green" type="submit" style="width:100%;" value="'.$valore.'">'.$nome.'</button></td></tr>';
		}
		print '</td></table></td>';
		print '<td><table style="width:100%;"><td><tr>';
		for ($i = 1; $i <= 3; $i++) { print '<td><button name="number" class="green" type="submit" style="width:100%;" value="'.$i.'">'.$i.'</button></td>';}
		print '</tr><tr>';
		for ($i = 4; $i <= 6; $i++) { print '<td><button name="number" class="green" type="submit" style="width:100%;" value="'.$i.'">'.$i.'</button></td>';}
		print '</tr><tr>';
		for ($i = 7; $i <= 9; $i++) { print '<td><button name="number" class="green" type="submit" style="width:100%;" value="'.$i.'">'.$i.'</button></td>';}
		print '</tr><tr>';
		print '<td colspan="2"><button name="number" class="green" type="submit" style="width:100%;" value="0">0</button></td>';
		print '<td><button name="number" class="green" type="submit" style="width:100%;" value=",">,</button></td>';
		print '</tr>';
		print '<tr><td colspan="3"><button name="cancel" class="green" type="submit" style="width:100%;" value="">cancel</button></td></tr>';
		print '<tr><td colspan="2"><input type="text" style="width:100%;" name="total" value="'.$_SESSION['ordine']['totalePagato'].'"></td>';
		print '<td><button name="calc" class="green" type="submit" style="width:100%;" value="">calc</button></td></tr>';
		print '<tr><td colspan="3"><button name="pagato" class="orange" type="submit" style="width:100%;" value="">pagato</button></td></tr>';
		print '</td></table>';
		print '</form>';
?>        