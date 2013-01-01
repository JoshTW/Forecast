<?php
//////////////////////////////////////////////////
// Josh Trotter-Wanner
// Dec. 2012
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
echo "<fc1>Low</fc1>\n";
echo "<fc2>Medium</fc2>\n";
echo "<fc3>Very High</fc3>\n";
echo "<sn>";
echo $_GET["sn"];
echo "</sn>\n";
echo "\n";
*/

$ip_Loc_url = 'http://api.hostip.info/get_json.php?position=true&ip=';

////////////////////////////////////////////////////////////////////////////////
// Testing
//$_SERVER['REMOTE_ADDR'] = "134.36.2.74"; //dundee.ac.uk
////////////////////////////////////////////////////////////////////////////////

//Define Connection
$con = mysqli_connect("localhost","FC_mysql_user","FC_mysql_pwd","forecast");
if (mysqli_connect_errno()) { //check connection
  printf("Could not connect: %s\n", mysqli_connect_error());
  exit();
}

function getLocation() {
    global $con, $ip_Loc_url;
    $ip = $_SERVER['REMOTE_ADDR'];

    if ($ip == '::1') { //Then bad IP address
        printf("Bad client IP address\n");
        return -1;
    }    

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
//    printf("Latitude=%s<br>\nLongitude=%s<br>\n",$lat,$lng);
    
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
    $result = mysqli_query($con, $sql);
    if (!$result) {
      die('Query failed: ' . mysqli_error($con));
    }

    // return the locationID
    $row = mysqli_fetch_row($result);
    mysqli_free_result($result);
    return $row[0];
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
    // Forecast1Type = 0;
    // Forecast2Type = 0;
    // Forecast3Type = 0;
    
    // Find an unused device ID
//    $sql = "INSERT INTO device (byte1, byte2, byte3, byte4, lastIPaddress) VALUES ".
//            "('123','123','123','123','".$ip."');";
    $sql = "INSERT INTO device (ForecastQty, Forecast1Type, lastIPaddress) VALUES ".
            "(1,1,'".$ip."');";
    $result = mysqli_query($con,$sql);
    if (!$result) {
      die('Query failed: ' . mysqli_error($con));
    }
    //$result should contain the auto_increment value
    // check for bytes with 255

    $val = mysqli_insert_id($con);
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
    if ($ar[4] == 255) {  // "2147417854"=(254.254.254.127) seems to be the largest serial number that can be divided into bytes
      // Then something has gone very wrong
      $val = $val + 256*256*256;
    }
    if ( $val <> mysqli_insert_id($con) ) {
        // Then this serial number won't do
        // Replace serial number $result with $val and set AUTO_INCREMENT to $val+1
//        $sql = "";
        // delete bad deviceID for the device table ( $result )
        // 
        $sql = "DELETE FROM device WHERE deviceID=".mysqli_insert_id($con);
        $result = mysqli_query($con,$sql);
        if (!$result) {
          die('Query failed: ' . mysqli_error($con));
        }

        // set AUTO_INCREMENT to the new ID ( $val )
        $sql = "ALTER TABLE device AUTO_INCREMENT=".$val;
        $result = mysqli_query($con,$sql);
        if (!$result) {
          die('Query failed: ' . mysqli_error($con));
        }
        
        // create new deviceID with $val
        $sql = "INSERT INTO device (ForecastQty, Forecast1Type, lastIPaddress) VALUES ".
                "(1,1,'".$ip."');";
        $result = mysqli_query($con,$sql);
        if (!$result) {
          die('Query failed: ' . mysqli_error($con));
        }
        $val = mysqli_insert_id($con); // This should be unnessiary
    } // fixed bad ID number

    ////////////////////////////////////////////////////////////////////////////
    //Set byte values (will probably remove this)
    $ar = unpack("C*", pack("V", $val));
    $sql = "UPDATE device SET byte1=".$ar[1].", byte2=".$ar[2].", ".
            "byte3=".$ar[3].", byte4=".$ar[4]." WHERE ".
            "deviceID=".$val.";";
    $result = mysqli_query($con,$sql);
    if (!$result) {
      die('Query failed: ' . mysqli_error($con));
    }
    //
    // End of unnessicory part
    ////////////////////////////////////////////////////////////////////////////
    
    
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
    printf("Dude! I don't know you.");
    exit();
}

// If the serial number contains a default value then assign new number
if ( strpos($_GET["sn"], "255") !== FALSE ) { // 255 being at the beginning results with a value of 0 which looks simular to FALSE
    
    // then generate a new serial number and send the new one
    $locID = getLocation();

    $DevID = newDevice();
    
    //check for error ( $loc = -1 )
    if ($locID == -1) {  //then don't return a forecast
        //leave devices location blank
        
    } else { 
        //setLocation($serialNumber, $locID);
//        printf("<br>\nlocID=%s, DevID=%s<br>\n",$locID,$DevID);
        $sql = "UPDATE device SET Location=".$locID." WHERE deviceID=".$DevID;
        $result = mysqli_query($con,$sql);
        if (!$result) {
          die('Query failed: ' . mysqli_error($con));
        }
    }
    // Generate new serial number
    $serialNumber = unpack("C*", pack("V", $DevID));
    $serialNumber = $serialNumber[1].".".$serialNumber[2].".".$serialNumber[3].".".$serialNumber[4];
    // Done
} else { //Use the existing Serial Number
    //echo "Good SN<br>";
    //
    
    $serialNumber = $_GET["sn"];
    $SNar = str_getcsv($serialNumber,'.');
    $val = chr($SNar[0]).chr($SNar[1]).chr($SNar[2]).chr($SNar[3]);
//    echo $SNar[0].".".$SNar[1].".".$SNar[2].".".$SNar[3]."<br>\n";
    $DevID = unpack("V", $val); //Returns an array
    $DevID = $DevID[1];
    //print_r( $DevID);
    
    // Look up serial number's location and forecast settings
    // get $locID
    $sql = "SELECT Location FROM device WHERE deviceID=".$DevID;
    $result = mysqli_query($con,$sql);
    if (!$result) {
      die('Query failed: ' . mysqli_error($con));
    }
    $row = mysqli_fetch_row($result);
    mysqli_free_result($result);
    $locID =  $row[0];
    //printf("Location=%s<br>\n",$locID);

    //Check that the device exists in the database
    if ( $locID === NULL ) {
        /*
        // Then the Device was not found in the database
        // Add it not with default settings
        // (it should not have a 255 value because of the earlier if statement)
        $ar = unpack("C*", pack("V", $DevID[1]));
        $sql = "INSERT INTO device (deviceID, byte1, byte2, byte3, byte4, ".
               "ForecastQty, Forecast1Type, lastIPaddress) VALUES ".
               "(".$DevID[1].",".$ar[1].",".$ar[2].",".$ar[3].",".$ar[4].",".
                "1,1,'".$_SERVER['REMOTE_ADDR']."');";
        printf("INSERT Query=%s<br>\n",$sql);
        $result = mysqli_query($con,$sql);
        if (!$result) {
          die('Query failed: ' . mysqli_error($con));
        }
        * 
        */
        //Add as new device
        $DevID = newDevice();
    }
    //Check that the serial number has a location
    if ( $locID == 0 ) { // Then try to assign a location
      $locID = getLocation();
//      printf("NewLocation=%s<br>\n",$locID);
      //check for error ( $loc = -1 )
      if ($locID == -1) {  //then don't return a forecast
          //leave devices location blank
      } else { 
          //setLocation($serialNumber, $locID);
          $sql = "UPDATE device SET Location=".$locID." WHERE deviceID=".$DevID;
          $result = mysqli_query($con,$sql);
          if (!$result) {
            die('Query failed: ' . mysqli_error($con));
          }
      }    
    }
    // Generate formated serial number (should not be nessicary if talking to an actual device)
    $serialNumber = unpack("C*", pack("V", $DevID));
    $serialNumber = $serialNumber[1].".".$serialNumber[2].".".$serialNumber[3].".".$serialNumber[4];
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
    
    $sql = "SELECT ForecastQty FROM device WHERE deviceID=".$DevID;
    $result = mysqli_query($con,$sql);
    if (!$result) {
      die('Query failed: ' . mysqli_error($con));
    }
    $row = mysqli_fetch_row($result);
    mysqli_free_result($result);
    $Qty =  $row[0];

    // Givens DevID, locID, ForecastType
    // Results Source php File, Location Value
    // 
    // DevID -> locID & ForecastType
    // locID & ForecastType -> sources for this location and the required type (phpFile, LocationField)
    // locID & LocationField -> Location Value
    
    // For forecast using deviceID only (I am stuck doing this with two queries.
    // 
    //SELECT source.LocationField FROM device INNER JOIN location ON device.Location=location.LocationID INNER JOIN relation ON location.LocationID=relation.Location INNER JOIN source ON relation.Source=source.SourceID WHERE device.deviceID=8 AND device.Forecast1Type=source.ForecastType ORDER BY Rank ASC;
    // -> Name1
    //SELECT source.phpFile, location.Name1 FROM device INNER JOIN location ON device.Location=location.LocationID INNER JOIN relation ON location.LocationID=relation.Location INNER JOIN source ON relation.Source=source.SourceID WHERE device.deviceID=8 AND device.Forecast1Type=source.ForecastType ORDER BY Rank ASC;
    
    ////////////////////////////////
    //The above sql join
    //SELECT * FROM device INNER JOIN location ON device.Location=location.LocationID INNER JOIN relation ON location.LocationID=relation.Location INNER JOIN source ON relation.Source=source.SourceID WHERE device.deviceID=8 AND device.Forecast1Type=source.ForecastType ORDER BY Rank ASC;
    // SELECT * 
    //   FROM device 
    //     INNER JOIN location
    //       ON device.Location=location.LocationID
    //     INNER JOIN relation
    //       ON location.LocationID=relation.Location 
    //     INNER JOIN source
    //       ON relation.Source=source.SourceID
    //   WHERE device.deviceID=8
    //     AND device.Forecast1Type=source.ForecastType
    //   ORDER BY Rank ASC;
    
    // Grab the source (php file name and location field value) that is for the location and the forecast type
    for ($i=1; $i <= $Qty; $i++) {
      $sql = "SELECT source.LocationField ".
                "FROM device ".
                  "INNER JOIN location ".
                    "ON device.Location=location.LocationID ".
                  "INNER JOIN relation ".
                    "ON location.LocationID=relation.Location ".
                  "INNER JOIN source ".
                    "ON relation.Source=source.SourceID ".
                "WHERE device.deviceID=".$DevID.
                  " AND device.Forecast".$i."Type=source.ForecastType ".
                "ORDER BY Rank ASC;";
      $result = mysqli_query($con,$sql);
      if (!$result) {
        die('Query failed: ' . mysqli_error($con));
      }
      $row = mysqli_fetch_row($result); //no rows if no source found
      //mysqli_free_result($result);

      $sql = "SELECT source.phpFile, location.".$row[0].
               " FROM device ".
                  "INNER JOIN location ".
                    "ON device.Location=location.LocationID ".
                  "INNER JOIN relation ".
                    "ON location.LocationID=relation.Location ".
                  "INNER JOIN source ".
                    "ON relation.Source=source.SourceID ".
                "WHERE device.deviceID=".$DevID.
                 " AND device.Forecast".$i."Type=source.ForecastType ".
                "ORDER BY Rank ASC;";
      $result = mysqli_query($con,$sql);
      if (!$result) {
        die('Query failed: ' . mysqli_error($con));
      }
      $row = mysqli_fetch_row($result); //no rows if no source found
      mysqli_free_result($result);
      
      //$row[0] is phpFile name
      //$row[1] is location string
      
      // Give the location string to the phpFile and store the forecast
      include "sources\\".$row[0].".php";
      $forecast[$i]=$row[0]($row[1]);
      
    }
    
}


// Step Three:
// return forecast and serial number
// - see the example at the beginning of this file.
// This should be done in one quick transmition so the device can receive one data burst.
printf ("<root>\n");
printf ("  <sn>%s</sn>\n",$serialNumber);
if ($locID <> -1) {
  for ($i=1; $i <= $Qty; $i++) {
      printf("  <fc%s>%s</fc%s>\n",$i,$forecast[$i],$i);
  }
}
printf ("</root>\n");


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
// uint: DeviceID
// TinyInt: byte1
// TinyInt: byte2
// TinyInt: byte3
// TinyInt: byte4
// VARCHAR(32): lastIPaddress  //used to identify the device when they connect with a browser
// uint: Location
// TinyInt: ForecastQty
// uint: Forecast1Type
// uint: Forecast2Type
// uint: Forecast3Type
// 

//Destructor
mysqli_close($con);
?>
