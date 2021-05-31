<?php

use app\modules\app\APPConfig;
use app\modules\cms\CMSConfig;
use app\modules\contrib\gxassets\GxLeafletPruneClusterAsset;
use app\modules\contrib\gxassets\GxMapLocationAsset;
use app\modules\contrib\gxassets\GxLeafletDrawAsset;

GxMapLocationAsset::register($this);
GxLeafletPruneClusterAsset::register($this);
GxLeafletDrawAsset::register($this);
include('map_ext.php');
?>

<style>
    footer {
        display: none;
    }
</style>

<div class="position-relative w-100 d-flex flex-column flex-md-row" id="map-detail-page">
    <div class="list-point-clouds show" v-cloak>
        <div class="card mb-0 h-100 position-relative">
            <div class="p-2">
                <h4 class="mb-0 font-weight-bold">Map detail</h4>
            </div>
            <hr class="mt-0 mb-2">
            <div class="px-3 py-1" v-if="mapData">
                <h6><i class="icon-info3 mr-1"></i>Title: {{ mapData.title }}</h6>
                <h6><i class="icon-user mr-1"></i>Author: {{ mapData.author }}</h6>
                <h6><i class="icon-calendar mr-1"></i>Published on: {{ formatTime(mapData.created_at) }}</h6>
            </div>
            <hr class="my-2">
            <div class="p-3 py-1 list" style="overflow-y: scroll;">
                <div v-if="pointclouds.length == 0"></div>
                <div v-else>
                    <ul class="media-list media-list-bordered">
                        <li v-for="(point, index) in pointclouds" class="media px-0">
                            <div class="mr-3">
                                <a :href="'<?= APPConfig::getUrl('point-cloud/detail/') ?>' + point.slug">
                                    <img :src="'<?= Yii::$app->homeUrl . 'uploads/' ?>' + point.thumbnail" width="75" height="50" class="border-radius-1">
                                </a>
                            </div>
                            <div class="media-body">
                                <a :href="'<?= APPConfig::getUrl('point-cloud/detail/') ?>' + point.slug">
                                    <h6 class="media-title point-cloud-title mb-1">{{ point.title }}</h6>
                                </a>
                                <div class="point-author d-flex align-items-center mb-0">
                                    <i class="icon-user mr-1"></i>
                                    <a :href="'<?= APPConfig::getUrl('user/point-cloud/') ?>' + point.author_slug">
                                        <h6 class="mb-0">{{ point.author }}</h6>
                                    </a>
                                </div>
                            </div>
                            <div class="ml-3">
                                <button class="btn btn-outline-primary btn-icon btn-sm" @click="zoomToPointCloud(index)"><i class="icon-location4"></i></button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <!-- <div class="card-toggle position-absolute px-1 py-2" @click="toggleSelectedPointClouds">
                <i class="icon-arrow-right5"></i>
            </div> -->
        </div>
    </div>
    <div class="map-detail" id="map-detail"></div>
</div>

<script>
    $(function() {
        fixPageHeight();
        var vm = new Vue({
            el: '#map-detail-page',
            data: {
                slug: '<?= $slug ?>',
                mapData: null,
                pointclouds: [],
                map: {
                    instance: null,
                    layers: {
                        base: {},
                        overlay: {}
                    }
                }
            },
            created: function() {
                this.loadMap();
            },
            methods: {
                loadMap: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('map/get-map-detail') ?>',
                        data = {
                            slug: _this.slug
                        };

                    sendAjax(api, data, (resp) => {
                        if (resp.status) {
                            _this.mapData = resp.map;
                            _this.pointclouds = resp.pointclouds;
                            _this.$nextTick(function() {
                                _this.initMap();
                            });
                        } else {
                            toastMessage('error', resp.message);
                        }
                    }, 'GET');
                },

                toggleSelectedPointClouds: function() {
                    var elListPointClouds = $('.list-point-clouds');
                    if (elListPointClouds.hasClass('show')) elListPointClouds.removeClass('show');
                    else elListPointClouds.addClass('show');
                },

                initMap: function() {
                    this.map.instance = L.map('map-detail', {
                        minZoom: 1,
                        maxZoom: 16,
                        // zoomControl: false
                    }).setView([31.4606, 20.7927], 2);
                    this.initLayers();
                    this.initControl();
                },

                initLayers: function() {
                    this.initBaseLayer();
                    this.initHstsLayer();
                    this.initOverlayLayer();
                    this.initPointCloudLayer();
                },

                initControl: function() {
                    let _this = this;
                    L.control.layers(_this.map.layers.base, _this.map.layers.overlay).addTo(_this.map.instance);
                    // L.control.zoom({ position: 'topright' }).addTo(_this.map.instance);
                },

                initBaseLayer: function() {
                    this.map.layers.base['World Dark Gray'] = L.tileLayer('http://server.arcgisonline.com/arcgis/rest/services/Canvas/World_Dark_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
                        minZoom: 1,
                        maxZoom: 16,
                        attribution: 'World Dark Gray'
                    }).addTo(this.map.instance);
                },

                initOverlayLayer: function() {

                },

                initPointCloudLayer: function() {
                    var _this = this;
                    if (_this.map.layers.overlay['Point cloud'] == undefined) {
                        _this.map.layers.overlay['Point cloud'] = L.featureGroup().addTo(_this.map.instance);
                    }
                    _this.pointclouds.forEach(function(p, index) {
                        var html = `<img src="<?= Yii::$app->homeUrl . 'uploads/' ?>${p.thumbnail}" id="point-object-on-map-${p.id}">`;
                        var icon = L.divIcon({
                            html: html,
                            className: 'point-object-on-map position-relative',
                            iconSize: [44, 44],
                            iconAnchor: [22, 49],
                            popupAnchor: [0, -40]
                        });

                        var marker = L.marker([p.lat, p.lng], {
                            icon: icon
                        }).bindPopup(_this.contentImagePopup(p));

                        _this.map.layers.overlay['Point cloud'].addLayer(marker);
                    });

                    _this.map.instance.fitBounds(_this.map.layers.overlay['Point cloud'].getBounds(), {padding: [20, 20]});
                },

                initHstsLayer: function() {
                    L.tileLayer.wms('https://wmsv1.hcmgis.vn/geoserver/geodb/wms', {
                        layers: 'layers=geodb:vietnam_hoangsa_truongsa_group',
                        format: 'image/png',
                        transparent: true,
                        minZoom: 1,
                        maxZoom: 20,
                    }).addTo(this.map.instance);
                },

                contentImagePopup: function(data) {
                    var created_at = formatTime(data.created_at);
                    var html = `
                    <div class="d-flex flex-column align-items-center" style="background: #262d3c">
                        <a href="/app/point-cloud/detail/${data.slug}">
                            <h5 class="mb-0 font-weight-bold text-white">${data.title}</h5>
                        </a>
                        <p class="text-muted mt-1 mb-2">
                            Published on ${created_at} by <a href="/app/user/point-cloud/${data.author_slug}" class="text-white">${data.author}</a>
                        </p>
                        <a href="/app/point-cloud/detail/${data.slug}">
                            <img src="/uploads/${data.thumbnail}" style="width: 270px; height: 170px; object-fit:cover">
                        </a>
                    </div>`
                    return html;
                },

                zoomToPointCloud: function(index) {
                    var _this = this;
                    var point = _this.pointclouds[index];
                    _this.map.instance.setView([point.lat, point.lng], 16);
                },

                formatTime: function(timeString) {
                    return formatTime(timeString);
                }
            }
        });
    });

    function fixPageHeight() {
        var $item = $('#map-detail-page');
        var $wHeight = $(window).height();
        var $navHeight = $(".navbar").height();
        $item.height($wHeight - $navHeight);
    }
</script>