<?php

$source_name = $_GET['source'];
$location_name = $_GET['location'];

if ( empty($source_name) ) {
  return;
} else {
  require('./sources/'.$source_name.'.php');
}

/*
echo "test";
echo "<br>";
echo $source_name;
echo " is ";
echo gettype($source_name);
echo "<br>";
echo $location_name;
echo " is ";
echo gettype($location_name);
echo "<br><br>";

//echo $source_name();
 * 
 */
echo $source_name($location_name);

//echo "<br><br>Ran function"

?>
