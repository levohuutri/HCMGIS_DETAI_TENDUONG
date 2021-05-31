<?php ?>
<div id="map-container" style="height: 500px">

</div>
<script>
    var APP = APP || {};

    function initMap() {
        APP.map = L.map('map-container').setView([51.505, -0.09], 13);
        initLayers();
        initEvents();
    }

    function initLayers() {
        APP.layers = {
            basemap: {
                osm:  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                })
            },
            overlay: {

            }
        }
        
        APP.layers.basemap.osm.addTo(APP.map);
    }

    function initEvents() {
        APP.map.on("moveend", function() {
            $(document).trigger("onMapMoveEnd");
        })

        APP.map.on("zoomend", function() {
            $(document).trigger("onMapZoomEnd");
        })
    }

    $(document).ready(function() {
        initMap();
    });
</script>