<?php
// database constants
// make sure the information is correct
const DB_SERVER = "internship";
const DB_USER = "root";
const DB_PASS = "";
const DB_NAME = "test_samson";

// connection to the database 
$dbhandle = mysqli_connect(DB_SERVER, DB_USER, DB_PASS)
   or die("Unable to connect to MySQL");

// select a database to work with 
$selected = mysqli_select_db($dbhandle, DB_NAME)
   or die("Could not select examples");

// return all available tables 
$result_tbl = mysqli_query( $dbhandle, "SHOW TABLES FROM ".DB_NAME );

$tables = array(); 
while ($row = mysqli_fetch_row($result_tbl)) {
   $tables[] = $row[0]; 
} 

$output = "<?xml version=\"1.0\" ?>\n";
$output .= "<schema>"; 

// iterate over each table and return the fields for each table
foreach ( $tables as $table ) { 
   $output .= "<table name=\"$table\">"; 
   $result_fld = mysqli_query( $dbhandle, "SHOW FIELDS FROM ".$table );

   while( $row1 = mysqli_fetch_row($result_fld) ) {
      $output .= "<field name=\"$row1[0]\" type=\"$row1[1]\"";
      $output .= ($row1[3] == "PRI") ? " primary_key=\"yes\" />" : " />";
   } 

   $output .= "</table>"; 
} 

$output .= "</schema>"; 

// tell the browser what kind of file is come in
header("Content-type: text/xml"); 
// print out XML that describes the schema
echo $output; 

// close the connection 
mysqli_close($dbhandle);
?> 