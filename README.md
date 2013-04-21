Forecast
========

The web site part of the forecast device on welovedata

The forecast device connects to the webserver and sends the following lines.

<code>
GET /forecast/forecast.php?sn=255.255.255.255 HTTP/1.0\\r\\n<br>
\\r\\n<br>
<code/>

The device sends it's stored serial number, if it contains a default value (ie 255) 
then a new serial number is generated, added to the device table, and assigned a 
near by pollen forecast based on ip geo-location.  If a previously assigned serial number 
is sent in the GET request then its stored location is used.

Example returned data:
<code><br>
    \<root\><br>
      \<sn\>6.0.0.0\</sn\><br>
      \<fc1\>High\</fc1\><br>
    \</root\>
<code/>


ToDo
----
- Make a web page that allows setting the location and forecast for the devices at 
the same ip address.
- Add code to expire old devices from the database table
- add new forecasts and locations
