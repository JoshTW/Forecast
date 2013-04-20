<?php
//// ///////////////////////////////////////////////////////////////////////
// Josh Trotter-Wanner
// Nov. 1, 2012 to May 20, 2013
// 
// Return the pollen forecast ("Low", "Medium", "High", and "Very High") 
// for the location string provided

function metoffice_pollen($locationString) {
    
    
    $source= "http://www.metoffice.gov.uk/public/data/PWSCache/PollenForecast/Latest";
    // Returns "<title>404 Not Found - Met Office</title>"
    $curl = curl_init($source);
    if ( curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE) ) {
        $string = curl_exec( $curl );
    }
    curl_close($curl);

    //xml tags
    // "issue at", "region name", "day number"="1" "level"="Low"
    
    $xml = simplexml_load_string("$string");
    
    foreach($xml->children() as $child)  // contains "issue", "report"
      {
        if ($child->getName() == 'issue' ) {
            foreach($child->attributes() as $a => $b) {
                if ( $a == 'at' ) {
//                    echo $b . "<br>"; // When the forcast was issued
                }
            }
        }
        if ($child->getName() == 'report' ) {
            foreach($child->children() as $report) {
                foreach( $report->attributes() as $a => $b){  /////////////  Need a better / faster array handling code
                    //echo $a,'="',$b,"\"<br>\n";
                    if ( $a == 'name' ) {
                        //Check against $locationString
//                        echo $b . "<br />";
                        if ($b == $locationString ) {
                            foreach($report->children() as $region) {
                                $lastNumber=0;
                                foreach( $region->attributes() as $a => $b ) {
                                    if ( $a == 'number' ) { // if day number 1 
                                        $lastNumber = $b;
                                    } elseif ( $a == level && $lastNumber == 1 ) {
//                                        echo "The forcast is " . $b . "<br>";
                                        return $b;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
      }   
      // return = one of these "Low", "Medium", "High", and "Very High"
}



/*
 * 
 * INSERT INTO `forecast`.`location` (
 * `LocationID` ,
 * `Name1` ,
 * `GeoLoc`
 * )
 * VALUES (
 * '1', 'Orkney and Shetland', PointFromText( 'POINT(59.052209 -2.981415)' )
 * );
 * 
 */

/*
 * Review Location Table:
 * SELECT LocationID, Name1, X( GeoLoc ), Y(GeoLoc) FROM `location`
 * 
 */

/*
 * Source:
 * http://www.metoffice.gov.uk/public/data/PWSCache/PollenForecast/Latest
 * 
 * Levels:
 * Low
 * Moderate
 * High
 * Very high
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('1', 'Orkney and Shetland', PointFromText( 'POINT(59.052209 -2.981415)' ));
 * Locations:
 * 1
 * Orkney and Shetland, UK
 * Orkney and Shetland
 * 59.775°N 1.803°W
 * <geometry><location>
 *   <lat>59.7750000</lat>
 *   <lng>-1.8030000</lng></location>
 * <location_type>APPROXIMATE</location_type><viewport><southwest><lat>59.0482578</lat><lng>-3.8519502</lng></southwest><northeast><lat>60.4862572</lat><lng>0.2459502</lng></northeast></viewport></geometry>
 * ll=59.052209,-2.981415
 * POINT(59.052209 -2.981415)
 * PointFromText( 'POINT(59.052209 -2.981415)' )
 * 396.62638008981736 92.40164827990861
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('2', 'Highlands and Eilean Siar', PointFromText( 'POINT(55.877622 -5.115509)' ));
 * Locations:
 * 2
 * Highlands and Eilean Siar
 * ll=55.877622,-5.115509
 * 348.64722417679116 165.08080996020612
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('3', 'Grampian', PointFromText( 'POINT(57.230016 -2.617493)' ));
 * Grampian
 * ll=57.230016,-2.617493
 * 404.39724302346184 169.58081633483926
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('4', 'Strathclyde', PointFromText( 'POINT(55.877622 -5.115509)' ));
 * Strathclyde
 * ll=55.877622,-5.115509
 * 347.89722382120465 226.8308003323857
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('5', 'Central Tayside and Fife', PointFromText( 'POINT(56.260897 -3.162689)' ));
 * Central Tayside and Fife
 * ll=56.260897,-3.162689
 * 391.8972295118509 210.08081865887493
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('6', 'SW Scotland, Lothian Borders', PointFromText( 'POINT(55.446153 -3.304138)' ));
 * SW Scotland, Lothian Borders
 * ll=55.446153,-3.304138
 * 388.89722808950876 243.58079035216656
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('7', 'N Ireland', PointFromText( 'POINT(54.64525 -6.24057)' ));
 * N Ireland
 * ll=54.64525,-6.24057
 * 316.75137635329554 267.52663507250963
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('8', 'Wales', PointFromText( 'POINT(52.400743 -3.468933)' ));
 * Wales, UK
 * Wales
 * 51°29′N 3°11′W
 * <geometry><location>
 *   <lat>52.1306607</lat>
 *   <lng>-3.7837117</lng></location>
 * <location_type>APPROXIMATE</location_type><viewport><southwest><lat>51.3749686</lat><lng>-5.4824697</lng></southwest><northeast><lat>53.4356935</lat><lng>-2.6497994</lng></northeast></viewport><bounds><southwest><lat>51.3749686</lat><lng>-5.6700973</lng></southwest><northeast><lat>53.4356935</lat><lng>-2.6497994</lng></northeast></bounds></geometry>
 * ll=52.400743,-3.468933
 * 385.6472341338242 362.33081066560317
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('9', 'NW England', PointFromText( 'POINT(53.712965 -2.550201)' ));
 * North West England
 * NW England
 * ll=53.712965,-2.550201
 * 406.64724409021744 312.58079510579887
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('10', 'NE England', PointFromText( 'POINT(54.612641 -1.425476)' ));
 * North East England
 * NE England
 * ll=54.612641,-1.425476
 * 431.64722560232633 276.33079199939016
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('11', 'Yorks & Humber', PointFromText( 'POINT(53.653592 -0.829468)' ));
 * Yorkshire and the Humber
 * Yorks & Humber
 * ll=53.653592,-0.829468
 * 445.7722360917797 313.97246684967445
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('12', 'E Midlands', PointFromText( 'POINT(52.707179 -0.336456)' ));
 * East Midlands
 * E Midlands
 * ll=52.707179,-0.336456
 * 455.647236981063 350.33080819520774
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('13', 'W Midlands', PointFromText( 'POINT(52.503684 -1.934967)' ));
 * West Midlands
 * W Midlands
 * ll=52.503684,-1.934967
 * 420.7722318241152 357.9724970434013
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('14', 'E of England', PointFromText( 'POINT(52.287483 0.848694)' ));
 * East of England
 * E of England
 * ll=52.287483,0.848694
 * 52.24°N 0.41°E
 * 484.3972202710996 365.58082535776884
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('15', 'SW England', PointFromText( 'POINT(50.977453 -3.666687)' ));
 * South West England
 * SW England
 * ll=50.977453,-3.666687
 * 50.96°N 3.22°W
 * 380.64722417806877 415.08079229923237
 * 
INSERT INTO `forecast`.`location` (`LocationID` ,`Name1` ,`GeoLoc`) VALUES 
('16', 'London and SE England', PointFromText( 'POINT(51.37178 -0.458679)' ));
 * London & South East England
 * London and SE England
 * ll=51.37178,-0.458679
 * 51°30′N 0°5′W
 * 453.0666204476265 400.3308245152491
 * 
 */
?>
