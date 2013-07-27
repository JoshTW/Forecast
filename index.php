<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <meta name="viewport" content="width=device-width, inital-scale=1.0, maximum-scale=1.0, user-scalable=0">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <title>Forecasts</title>
        <link rel="stylesheet" href="map/theme/default/style.css" type="text/css">
        <link rel="stylesheet" href="style.css" type="text/css">
        <script src="./map/OpenLayers.js"></script>
        <script type="text/javascript">
            var greenstyle = {
                fillColor: '#0f0',
                //fillColor: '#00FF00',
                fillOpacity: 0.5,
                strokeWidth: 1
            };
            var yellowstyle = {
                fillColor: '#ff0',
                //fillColor: '#FFFF00',
                fillOpacity: 0.5,
                strokeWidth: 0
            };
            var redstyle = {
                fillColor: '#f00',
                //fillColor: '#FF0000',
                fillOpacity: 0.5,
                strokeWidth: 0
            };
            var map, osm, /*gmap,*/ layer, marker;
            function init() {
                map = new OpenLayers.Map({div: "map", allOverlays: true});
                osm = new OpenLayers.Layer.OSM( "Open Street Map" );  //"Simple OSM Map"
//                osm = new OpenLayers.Layer.OSM( "Open Street Map", { minZoomLevel: 2 } );  //"Simple OSM Map"
                map.addLayer(osm);
                //gmap = new OpenLayers.Layer.Google( "Google Map", {visibility: false} );
                //map.addLayer(gmap);
                //map.addLayers([osm, gmap]);

                layer = new OpenLayers.Layer.Vector('vector');
                //map.addLayers([osm, layer]);
                


                layer.setName("Forecast");
                map.setCenter(  //Center map on users location or location on file
                    new OpenLayers.LonLat(-121.575556, 53.101667).transform(
                        new OpenLayers.Projection("EPSG:4326"),
                        map.getProjectionObject()
                    ), 2
                );
//                map.zoomToMaxExtent();
<?php /*          
 */ ?>
                map.updateSize(100,100);
               // layer.removeAllFeatures();
                var greencircle = new OpenLayers.Feature.Vector(
                   OpenLayers.Geometry.Polygon.createRegularPolygon(
                       new OpenLayers.Geometry.Point(-121.575556, 53.101667).transform(
                            new OpenLayers.Projection("EPSG:4326"),
                            map.getProjectionObject()
                       ),
                       100000.0,
                       20,
                       0
                   ),
                   {},
                   redstyle
                );
                //map.addLayer(layer);
                layer.addFeatures([greencircle]);
                //layer.addFeature(greencircle);
                greencircle = new OpenLayers.Feature.Vector(
                   OpenLayers.Geometry.Polygon.createRegularPolygon(
                       new OpenLayers.Geometry.Point(-134.707222, 60.1675).transform(
                            new OpenLayers.Projection("EPSG:4326"),
                            map.getProjectionObject()
                       ),
                       100000.0,
                       20,
                       0
                   ),
                   {},
                   greenstyle
                );
                layer.addFeatures([greencircle]);
                map.addLayer(layer);
                map.addControl(new OpenLayers.Control.LayerSwitcher());

                marker = new OpenLayers.Layer.Markers("FCast");
                map.addLayer(marker);
                var size = new OpenLayers.Size(15,15);
                var offset = new OpenLayers.Pixel(-(size.w/2), -size.h);
                var icon = new OpenLayers.Icon('map/img/ani.gif',size,offset);
                marker.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(0,0).transform(
                            new OpenLayers.Projection("EPSG:4326"),
                            map.getProjectionObject()
                       ),icon));
//                marker.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(-121.575556, 53.101667),icon.clone()));
                marker.addMarker(new OpenLayers.Marker(new OpenLayers.LonLat(-121.575556, 53.101667).transform(
                            new OpenLayers.Projection("EPSG:4326"),
                            map.getProjectionObject()
                       ),icon.clone()));

            }
<?php /*          
var map;

function init() {

    map = new OpenLayers.Map({
        div: "map",
        allOverlays: true
    });

    var osm = new OpenLayers.Layer.OSM();
    var gmap = new OpenLayers.Layer.Google("Google Streets", {visibility: false});

    // note that first layer must be visible
    map.addLayers([osm, gmap]);

    map.addControl(new OpenLayers.Control.LayerSwitcher());
    map.zoomToMaxExtent();

}
 */ ?>
        </script>
    </head>
    <body onload="init()">
        <?php
        // This file is to ask the user to confirm/correct there location setting
        //   - Using there IP address to find the pollen predictor serial number 
        //     that last connected from the same IP
        // This file should also ask the user to choose which of the forecasts 
        // that are avalible for there location they want to recieve and in which order
        // eg. Location = Exeter
        //     Forecast one = Pollen forecast
        //     Forecast two = wind speed
        //     Forecast three = UV index
        ?>
        <div>Welcome to the map</div>
        <div id="map" class="smallmap"></div>
        
    </body>
</html>
