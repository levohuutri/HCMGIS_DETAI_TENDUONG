<?php

use app\modules\app\APPConfig;
use app\modules\cms\CMSConfig;
use app\modules\contrib\gxassets\GxLeafletPruneClusterAsset;
use app\modules\contrib\gxassets\GxMapLocationAsset;
use app\modules\contrib\gxassets\GxLeafletDrawAsset;

GxMapLocationAsset::register($this);
GxLeafletPruneClusterAsset::register($this);
GxLeafletDrawAsset::register($this);
?>

<style>
    .point-object-on-map {
        border: 3px solid #999;
        object-fit: cover;
        position: relative;
    }

    .point-object-on-map:before {
        content: ' ';
        display: block;
        position: absolute;
        top: 100%;
        left: 50%;
        transform: translate(-50%, 3px);
        width: 0;
        height: 0;
        border: 5px solid transparent;
        border-top-color: #999;
    }

    .point-object-on-map img {
        object-fit: cover;
        width: 100%;
        height: 100%;
    }

    .count-cluster {
        position: absolute;
        top: 0;
        right: 0;
        padding: 1px 5px;
        background: #262d3c;
        color: #fff;
        border-radius: 5px;
        transform: translate(70%, -70%);
        font-size: .8rem;
        font-weight: bold
    }

    .form-search{
        position: absolute;
        z-index: 900000;
        top: 10px;
        left: 50px;
        border-radius: .1875rem;
    }

    .form-search .input-group{
        border-radius: .1875rem
    }

    .leaflet-popup-content-wrapper,
    .leaflet-popup-tip {
        background: #262d3c;
    }

    footer{
        display: none;
    }
</style>

<div class="position-relative  w-100" id="points-map">
<div id="map" class="w-100 h-100" style="z-index: 0"></div>
    <form class="form-search w-100 w-md-25" id="form-search-map">
        <div class="input-group" style="background: #262d3c">
            <input name="geojson" type="hidden" v-model="geojsonStr">
            <input class="form-control" name="keyword" type="text" placeholder="Point cloud title, tags">
            <span class="input-group-btn">
                <button class="btn btn-default btn-search" id="btn-search-map" @click="searchPoints"><i class="icon-search4"></i></button>
            </span>
        </div>
    </form>
</div>

<script>
    $(function() {
        initMapHeight();
        var vm = new Vue({
            el: '#points-map',
            data: {
                points: null,
                geojson: null,
                map: null,
                layers: {
                    base: {},
                    overlay: {},
                    pointsLayer: null
                },
                controls: {},
                dataBaseLayer: [
                    {
                        domain: 'http://server.arcgisonline.com/arcgis/rest/services/Canvas/World_Dark_Gray_Base/MapServer/tile/{z}/{y}/{x}',
                        minZoom: 0,
                        maxZoom: 16,
                        attribution: 'World Dark Gray'
                    },
                    {
                        domain: 'http://mt1.google.com/vt/lyrs=m&x={x}&y={y}&z={z}',
                        minZoom: 0,
                        maxZoom: 22,
                        attribution: 'Google Maps'
                    }, {
                        domain: 'http://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}',
                        minZoom: 0,
                        maxZoom: 22,
                        attribution: 'Google Satellite'
                    }, {
                        domain: 'http://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}',
                        minZoom: 0,
                        maxZoom: 22,
                        attribution: 'Google Satellite Hybrid'
                    }, {
                        domain: 'http://server.arcgisonline.com/arcgis/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
                        subdomains: ['mt0', 'mt1', 'mt2', 'mt3'],
                        minZoom: 0,
                        maxZoom: 22,
                        attribution: 'Esri Street'
                    }
                ]
            },
            created: function() {
                this.getPoints()
            },
            computed: {
                geojsonStr: function() {
                    return JSON.stringify(this.geojson);
                }
            },
            mounted: function() {
                this.initMap();
            },
            methods: {
                initMap: function() {
                    this.map = L.map('map', {
                        maxZoom: 16,
                        minZoom: 0
                    }).setView([16.047079, 108.206230], 6);
                    this.initControls();
                    this.initHtTsLayer();
                },

                initControls: function() {
                    var _this = this;
                    //init Base Layer and controls
                    _this.dataBaseLayer.forEach(function(el, idx) {
                        _this.layers.base[el.attribution] = L.tileLayer.wms(el.domain, el);
                        if (idx === 0) {
                            _this.map.addLayer(_this.layers.base[el.attribution]);
                        };
                    })

                    _this.controls.controllayer = L.control.layers(_this.layers.base);
                    _this.controls.controllayer.addTo(_this.map);

                    //drawControl
                    _this.initDrawControl();
                },

                initDrawControl: function() {
                    var _this = this;
                    _this.layers.drawItems = new L.FeatureGroup();
                    _this.map.addLayer(_this.layers.drawItems);
                    _this.controls.drawControl = new L.Control.Draw({
                        edit: {
                            featureGroup: _this.layers.drawItems
                        },
                        draw: {
                            polyline: false,
                            marker: false,
                            circlemarker: false,
                        }
                    });
                    _this.map.addControl(_this.controls.drawControl);
                    _this.map.on(L.Draw.Event.CREATED, function(e) {
                        _this.layers.drawItems.clearLayers();
                        _this.layers.drawItems.addLayer(e.layer);
                        let geojson = e.layer.toGeoJSON();
                        geojson.properties.layerType = e.layerType;
                        
                        if(e.layerType == 'circle') {
                            geojson.properties.radius = e.layer.getRadius()
                        }

                        _this.geojson = geojson;

                        _this.$nextTick(function() {
                            _this.getPoints();
                        });
                    });

                    _this.map.on(L.Draw.Event.EDITED, function(e) {
                        var layers = e.layers;
                        layers.eachLayer(function(layer) {
                            let geojson = layer.toGeoJSON();
                            geojson.properties = _this.geojson.properties;

                            if(geojson.properties.layerType == 'circle') {
                                geojson.properties.radius = layer.getRadius()
                            }

                            _this.geojson = geojson;

                            _this.$nextTick(function() {
                                _this.getPoints();
                            });
                        });
                    });

                    _this.map.on(L.Draw.Event.DELETED, function(e) {
                        var layers = e.layers;
                        _this.geojson = null;
                        _this.$nextTick(function() {
                            _this.getPoints();
                        });
                    });
                },

                initHtTsLayer: function() {
                    this.layers.overlay['hstsLayer'] = L.tileLayer.wms('http://wmsv1.hcmgis.vn/geoserver/geodb/wms', {
                        layers: 'layers=geodb:vietnam_hoangsa_truongsa_group',
                        format: 'image/png',
                        transparent: true,
                        minZoom: 0,
                        maxZoom: 24,
                    }).addTo(this.map);
                },

                initPointsLayer: function(fitBounds = false) {
                    var _this = this,
                        points = _this.points,
                        markers = [],
                        arrBounds = [];

                    PruneCluster.Cluster.ENABLE_MARKERS_LIST = true
                    _this.layers.pointsLayer = new PruneClusterForLeaflet();

                    for (var i = 0; i < points.length; ++i) {
                        var point = points[i];
                        var marker = new PruneCluster.Marker(point['lat'], point['lng']);
                        arrBounds.push([point['lat'], point['lng']]);

                        marker.data.ID = point['id'];
                        marker.data.name = point['name'] ? point['name'] : 'No title';
                        marker.data.author = point['author'];
                        marker.data.created_by = point['created_by'];
                        marker.data.created_at = point['created_at'];
                        marker.data.path = point['path'];

                        marker.data = {
                            ID: point['id'],
                            title: point['title'],
                            author: point['author'],
                            author_slug: point['author_slug'],
                            created_at: point['created_at'],
                            thumbnail: point['thumbnail'],
                            slug: point['slug']
                        }

                        var pointIcon = `<img src="${'<?= Yii::$app->homeUrl . 'uploads/' ?>'}${point['thumbnail']}" id="point-object-on-map-${point['id']}">`;
                        marker.data.icon = L.divIcon({
                            html: pointIcon,
                            className: 'point-object-on-map position-relative',
                            iconSize: [44, 44],
                            iconAnchor: [22, 49],
                            popupAnchor: [0, -40]
                        });

                        marker.data.popup = _this.contentImagePopup(marker.data);

                        markers.push(marker);
                        _this.layers.pointsLayer.RegisterMarker(marker);
                    }

                    _this.layers.pointsLayer.BuildLeafletClusterIcon = function(cluster) {
                        var count = cluster.population;
                        var marker = cluster.lastMarker;

                        var pointIcon = `<img src="${'<?= Yii::$app->homeUrl . 'uploads/' ?>'}${marker.data.thumbnail}" id="point-object-on-map-${marker.data.ID}"><span class="count-cluster">${count}</span>`;
                        return L.divIcon({
                            html: pointIcon,
                            className: 'point-object-on-map position-relative',
                            iconSize: [48, 48],
                            iconAnchor: [24, 53],
                            popupAnchor: [0, -44]
                        });
                    };

                    _this.map.addLayer(_this.layers.pointsLayer);

                    if (fitBounds) {
                        _this.map.fitBounds(arrBounds, {
                            padding: [50, 50]
                        });
                    }
                },

                contentImagePopup: function(data) {
                    var created_at = formatTime(data.created_at);
                    var html = '<div class="d-flex flex-column align-items-center" style="background: #262d3c">'
                    html += '<a href="<?= APPConfig::getUrl('point-cloud/detail/') ?>' + data.slug + '"><h5 class="mb-0 font-weight-bold text-white">' + data.title + '</h5></a>';
                    html += '<p class="text-muted mt-1 mb-2"> Published on ' + created_at + ' by <a href="<?= APPConfig::getUrl('user/point-cloud/') ?>' + data.author_slug + '" class="text-white">' + data.author + '</a></p>';
                    html += '<a href="<?= APPConfig::getUrl('point-cloud/detail/') ?>' + data.slug + '"><img src="' + '<?= Yii::$app->homeUrl . 'uploads/' ?>' + data.thumbnail + '" style="width: 270px; height: 170px; object-fit:cover"></a>';
                    html += '</div>'
                    return html;
                },

                getPoints: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('point-cloud/get-points-map') ?>',
                        data = $('#form-search-map').serialize();

                    sendAjax(api, data, (resp) => {
                        if (resp.status) {
                            if(_this.map.hasLayer(_this.layers.pointsLayer)) {
                                _this.map.removeLayer(_this.layers.pointsLayer);
                            }
                            
                            _this.points = resp.points;
                            _this.initPointsLayer(true);
                            
                        }
                    })
                },

                searchPoints: function(e) {
                    e.preventDefault();
                    this.getPoints();
                },
            }
        })
    });

    function initMapHeight ()
    {
        var $item = $('#points-map');
        var $wHeight = $(window).height();
        var $navHeight = $(".navbar").height();
        $item.height($wHeight - $navHeight);
    }

</script>