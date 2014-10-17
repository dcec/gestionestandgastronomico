<?php

// Replace <USERNAME_DATABASE>, <USERNAME>, and <PASSWORD> below with your actual DB, user, and password.
 
 $dbh = pg_connect("host=localhost dbname=sagra user=sagra password=sagra");
 if (!$dbh) {
     die("Error in connection: " . pg_last_error());
 }       


 $sql = "SELECT * FROM testschema.testtable";

 $result = pg_query($dbh, $sql);

 if (!$result) {
     die("Error in SQL query: " . pg_last_error());
 }       


 while ($row = pg_fetch_array($result)) {
     echo "test_id: " . $row[0] . "<br />";
     echo "test_name: " . $row[1] . "<br />";
     echo "test_email: " . $row[2] . "<p />";
 }       

 
 pg_free_result($result);       


 pg_close($dbh);
?>
