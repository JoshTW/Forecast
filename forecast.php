<?php
//////////////////////////////////////////////////
// Josh Trotter-Wanner
// Oct. 2012
//
// Take a get request from the Pollen Predictor then
// send a new serial number if the received one contains a 255 value 
// and the location/settings appropiate forecasts.
//
////// Example request from an unset serial number
// GET /forecast/forecast.php?sn=255.255.255.255 HTTP/1.0\r\n
// \r\n
//////
// sn=four bytes deliminated by "."'s  eg. <sn> = 255.255.255.255
// <fcOne>, <fcTwo>, <fcThree>
// "Low", "Medium", "High", and "Very High"
// 
// An example response
/*
echo "<fcOne>Low</fcOne>\n";
echo "<fcTwo>Medium</fcTwo>\n";
echo "<fcThree>Very High</fcThree>\n";
echo "<sn>";
echo $_GET["sn"];
echo "</sn>\n";
echo "\n";
*/

$ip_Loc_url = 'http://api.hostip.info/get_json.php?position=true&ip=';


//Define Connection
$con = mysql_connect("localhost","FC_mysql_user","FC_mysql_pwd");
if (!$con)
  {
  die('Could not connect: ' . mysql_error());
  }

/* New
$con = mysqli_connect("localhost","FC_mysql_user","FC_mysql_pwd","forecast");
if (mysqli_connect_errno()) { //check connection
  printf("Could not connect: %s\n", mysqli_connect_error());
  exit();
}
*/

$db_selected = mysql_select_db("forecast", $con);

if (!$db_selected)
  {
  die ("Can\'t use test_db : " . mysql_error());
  }

function getLocation() {
    global $con, $ip_Loc_url;
    $ip = $_SERVER['REMOTE_ADDR'];
    
    //testing ip's
    //gooder
    //$ip = "134.36.2.74";
    //bad
    //$ip = "208.181.168.37";
    

    // http://freegeoip.net/xml/ $_SERVER['REMOTE_ADDR']
    // http://api.hostip.info/get_html.php?ip=12.215.42.19&position=true
    //http://api.hostip.info/get_json.php
    $curl = curl_init($ip_Loc_url.$ip);
    if ( curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE) ) {
        $string = curl_exec( $curl );
    }
//    echo $string;
    //var_dump(json_decode($string));
    $results = json_decode($string);
    $lat = $results->lat;
    $lng = $results->lng;
    // check for null valuse in the returned page
    echo "<br>\n";
    echo "Latitude";
    echo $lat;
    echo "<br>\nLongitude";
    echo $lng;
    
    //Check for null $lat & $lng
    if ( ($lat == null) OR ($lng == null) ) {
        // The visiters location is unknown return an error
        return -1;
    }

/* Select the closest point to Latitude (North is positive), Longitude (East is positive)
SELECT GLength( linestringfromwkb( LineString( GeomFromWKB( GeoLoc ) , GeomFromWKB( Point( 10, 10 ) ) ) ) ) , LocationID, Name1, X( GeoLoc ) , Y( GeoLoc )
FROM location order by GLength( linestringfromwkb( LineString( GeomFromWKB( GeoLoc ) , GeomFromWKB( Point( 10, 10 ) ) ) ) )
asc limit 1;
*/
/*    $sql = "SELECT GLength( linestringfromwkb( LineString( GeomFromWKB( GeoLoc ) , GeomFromWKB( Point( ".
            $lat.", ".$lng." ) ) ) ) ) , LocationID ".
            "FROM location order by ".
            "GLength( linestringfromwkb( LineString( GeomFromWKB( GeoLoc ) , GeomFromWKB( Point( ".
            $lat.", ".$lng." ) ) ) ) ) asc limit 1;";*/
    $sql = "SELECT LocationID ".
            "FROM location order by ".
            "GLength( linestringfromwkb( LineString( GeomFromWKB( GeoLoc ) , GeomFromWKB( Point( ".
            $lat.", ".$lng." ) ) ) ) ) asc limit 1;";
//    echo "<br>\n";
    $result = mysql_query($sql,$con);
    if (!$result) {
      die('Query failed: ' . mysql_error());
    }

    // return the locationID
    return mysql_result($result,0);
}

function newDevice() {
    global $con;
    $ip = $_SERVER['REMOTE_ADDR'];
    
    // Add device to the database
    // new Serial (and return new serial number)
    //   byte1, byte2, byte3, byte4
    // lastIPaddress
    //
    //Leave these as default
    // Location = null
    // ForecastQty = 0;
    // ForecastOneType = 0;
    // ForecastTwoType = 0;
    // ForecastThreeType = 0;
    
    // Find an unused device ID
//    $sql = "INSERT INTO device (byte1, byte2, byte3, byte4, lastIPaddress) VALUES ".
//            "('123','123','123','123','".$ip."');";
    $sql = "INSERT INTO device (ForecastQty, ForecastOneType, lastIPaddress) VALUES ".
            "(1,1,".$ip."');";
    $result = mysql_query($sql,$con);
    if (!$result) {
      die('Query failed: ' . mysql_error());
    }
    //$result should contain the auto_increment value
    // check for bytes with 255
    $val = $result;
    $ar = unpack("C*", pack("V", $val));
    if ($ar[1] == 255) {
      $val = $val + 1;
    }
    $ar = unpack("C*", pack("V", $val));
    if ($ar[2] == 255) {
      $val = $val + 256;
    }
    $ar = unpack("C*", pack("V", $val));
    if ($ar[3] == 255) {
      $val = $val + 256*256;
    }
    $ar = unpack("C*", pack("V", $val));
    if ($ar[4] == 255) {
      // Then something has gone very wrong
      $val = $val + 256*256*256;
    }
    if ( $val <> $result ) {
        // Then this serial number won't do
        // Replace serial number $result with $val and set AUTO_INCREMENT to $val+1
//        $sql = "";
        // delete bad deviceID for the device table ( $result )
        // 
        $sql = "DELETE FROM device WHERE deviceID=".$result;
        $result = mysql_query($sql,$con);
        if (!$result) {
          die('Query failed: ' . mysql_error());
        }

        // set AUTO_INCREMENT to the new ID ( $val )
        $sql = "ALTER TABLE device AUTO_INCREMENT=".$val;
        $result = mysql_query($sql,$con);
        if (!$result) {
          die('Query failed: ' . mysql_error());
        }
        
        // create new deviceID with $val
        $sql = "INSERT INTO device (ForecastQty, ForecastOneType, lastIPaddress) VALUES ".
                "(1,1,".$ip."');";
        $result = mysql_query($sql,$con);
        if (!$result) {
          die('Query failed: ' . mysql_error());
        }
        $val = $result; // This should be unnessiary
    } // fixed bad ID number

    // return the deviceID
    return $val;
}



///////////////////////////////////////////////////////////////
// Code begins


// Step One:
// Check serial number and if its the default value then generate a new one and get location
// - if $_GET["sn"] contains a 255 value (eg "255.255.255.255")then generate a new serial number
//   -  the serial number is made of 4 byte values from 0 to 254 seperated by .'s eg. <sn>123.23.212.6</sn>
// - if using a new serial number or one that is not in the database then
// use the ip address ( $_SERVER['REMOTE_ADDR'] ) for IP geolocation and match the 
// IP location to a location in the database
// - if the serial number is in the database then use it's assoated location

if (empty($_GET["sn"])) {
    echo "Dude! I don't know you.";
    exit();
}

if ( strpos($_GET["sn"], "255") == null ) { // If contains a default value
    // then generate a new serial number and send the new one
    $locID = getLocation();

    $DevID = newDevice();
    
    //check for error ( $loc = -1 )
    if ($locID == -1) {  //then don't return a forecast
        //leave devices location blank
        
    } else { 
        //setLocation($serialNumber, $locID);
        $sql = "UPDATE device SET Location=".$locID." WHERE deviceID=".$DevID;
        $result = mysql_query($sql,$con);
        if (!$result) {
          die('Query failed: ' . mysql_error());
        }
    }
    // Generate new serial number
    $serialNumber = unpack("C*", pack("V", $DevID));
    $serialNumber = $serialNumber[1].".".$serialNumber[2].".".$serialNumber[3].".".$serialNumber[4];
    // Done
} else { //Use the existing Serial Number
    //echo "Good SN<br>";
    //
    //Check that the device exists in the database
    //// If it is not there then add it.
    //
    //*******************************************************************************************
    // Put code here
    //
    //
    //
    //
    //*******************************************************************************************
    //
    //
    //Check that the serial number has a location
    //// Make sure that the Location does not = 0
    
    $serialNumber = $_GET["sn"];
    $SNar = str_getcsv($serialNumber,'.');
    $val = chr($SNar[1]).chr($SNar[2]).chr($SNar[3]).chr($SNar[4]);
    $DevID = unpack("V", $val);
    
    // Look up serial number's location and forecast settings
    // get $locID
    $sql = "SELECT Location FROM device WHERE deviceID=".$DevID;
    $result = mysql_query($sql,$con);
    if (!$result) {
      die('Query failed: ' . mysql_error());
    }
    $locID = $result;  ///////////////////////////////////////////////////////////// Bad
    if ( $locID == 0 ) { // Then try to assign a location
      $locID = getLocation();
      //check for error ( $loc = -1 )
      if ($locID == -1) {  //then don't return a forecast
          //leave devices location blank
      } else { 
          //setLocation($serialNumber, $locID);
          $sql = "UPDATE device SET Location=".$locID." WHERE deviceID=".$DevID;
          $result = mysql_query($sql,$con);
          if (!$result) {
            die('Query failed: ' . mysql_error());
          }
      }    
    }
}


// Step Two:
// get forecast 
// using the DeviceID step thru the forecasts based on the ForecastQty
// - use the location and the forecast type (defaults to pollen forecast) to
// choose the source (use the source rank if more than one is avalible)
//   - The source table should point to a php file and the location form the php file uses
//   - All the retrieving and scrapping to be done in these seperate files.
//
// Under normal circumstances step one and two can/should be done with one sql request


if ($locID <> -1) {
    // Get forecast Qty and Types
    
    for ($i=1; $i <= $Qty; $i++) {
      $sql = "SELECT Location FROM device WHERE deviceID=".$DevID;
      $result = mysql_query($sql,$con);
      if (!$result) {
        die('Query failed: ' . mysql_error());
      }
      $locID = $result; 
        
    }
    
}


// Step Three:
// return forecast and serial number
// - see the example at the beginning of this file.

echo "<sn>";
echo $serialNumber;
echo "</sn>\n";



// SQL database: Forecast
// 
// Table: location
// int(11): LocationID
// 
// varchar(32): Name1
// varchar(32): Name2
// varchar(32): Name3
// point: GeoLoc    //// GeomFromText('POINT(47.551721 -122.304827) ')
//
//
// Table: source
// uint(key): SourceID
// string: php file name
// time: time of day when updated
// time: interval between updates
// int(11): ForecastType
// string: LocationKey used ( eg. Name2 )
// 
// 
// Table: Forecast Types
// uint(key): ForecastTypeID
// string: ForecastType
// 
//
// Table: Relation
// uint(key): LocaionID
// uint(key): SourceID 
// uint: rank
////// each Locaion can have multiple sources
// 
// 
// Table: Device
// TinyInt: byte1
// TinyInt: byte2
// TinyInt: byte3
// TinyInt: byte4
// VARCHAR(32): lastIPaddress  //used to identify the device when they connect with a browser
// uint: Location
// TinyInt: ForecastQty
// uint: ForecastOneType
// uint: ForecastTwoType
// uint: ForecastThreeType
// 

//Destructor
mysql_close($con);
?>
