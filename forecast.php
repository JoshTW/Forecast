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
echo "<fcOne>Low</fcOne>\n";
echo "<fcTwo>Medium</fcTwo>\n";
echo "<fcThree>Very High</fcThree>\n";
echo "<sn>";
echo $_GET["sn"];
echo "</sn>\n";
echo "\n";


// Note on designing this file and it's companions

// Step One:
// get location
// - if $_GET["sn"] contains a 255 value (eg "255.255.255.255")then generate a new serial number
//   -  the serial number is made of 4 byte values from 0 to 254 seperated by .'s eg. <sn>123.23.212.6</sn>
// - if using a new serial number or one that is not in the database then
// use the ip address ( $_SERVER['REMOTE_ADDR'] ) for IP geolocation and match the 
// IP location to a location in the database
// - if the serial number is in the database then use it's assoated location

// Step Two:
// get forecast
// - use the location and the forecast type (defaults to pollen forecast) to
// choose the source (use the source rank if more than one is avalible)
//   - The source table should point to a php file and the location form the php file uses
//   - All the retrieving and scrapping to be done in these seperate files.

// Step Three:
// return forecast and serial number
// - see the example at the beginning of this file.



// SQL database
//
// Table: Location
// uint(key): LocationID
// string: Name1
// string: Name2
// string: Name3
// float: lat
// float: long
//
//
// Table: Source
// uint(key): SourceID
// string: php file name
// time: time of day when updated
// time: interval between updates
// uint: ForecastTypeID
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
// byte1: serial number part 1
// byte2: serial number part 2
// byte3: serial number part 3
// byte4: serial number part 4
// string: last ip address  //used to identify the device when they connect with a browser
// uint: LocationID
// uint: ForecastOneType
// uint: ForecastTwoType
// uint: ForecastThreeType
// 
?>
