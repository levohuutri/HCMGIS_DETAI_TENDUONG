<?php ?>
<div id="map-container" style="height: 500px">

</div>
<script>
    var APP = APP || {};

    function initMap() {
        APP.map = L.map('map-container').setView([10.797964646603672, 106.69199882982356], 10);
        initLayers();
        initEvents();
    }

    function initLayers() {
        APP.layers = {
            basemap: {
                iotlink: L.tileLayer('http://rtile.map4d.vn/all/2d/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }),
                hcmbase: L.tileLayer.wms('http://pcd.hcmgis.vn/geoserver/gwc/service/wms', {layers: "hcm_map:hcm_map_all"})
            },
            overlay: {

            }
        }
        
        APP.layers.basemap.hcmbase.addTo(APP.map);
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