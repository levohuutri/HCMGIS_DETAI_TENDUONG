<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use app\modules\contrib\gxassets\GxLeafletAsset;

GxLeafletAsset::register($this);

$pageData = [
    'pageTitle' => 'Create map',
    'headerElements' => [],
];
include('map_ext.php');
?>
<?= $this->render(PathConfig::getAppViewPath('tagPageHeader'), $pageData); ?>

<div class="content mb-3" id="create-map-page">
    <div class="row" style="height: 100vh">
        <div class="col-md-3 h-100">
            <div class="card h-100">
                <div class="card-header header-elements-inline p-2">
                    <h5 class="card-title">Point clouds and Layers</h5>
                </div>

                <div class="card-body p-2 d-flex flex-column overflow-hidden">
                    <ul class="nav nav-tabs nav-tabs-bottom nav-justified mb-3">
                        <li class="nav-item"><a href="#pointclouds" class="nav-link border-0 active" data-toggle="tab">Point clouds</a></li>
                        <li class="nav-item"><a href="#base-layers" class="nav-link border-0" data-toggle="tab">Base layers</a></li>
                        <li class="nav-item"><a href="#overlay-layers" class="nav-link border-0" data-toggle="tab">Overlay layers</a></li>
                    </ul>
                    <div class="tab-content" style="flex: 1 1 auto; overflow-y: scroll;">
                        <div class="tab-pane fade show active" id="pointclouds" v-cloak>
                            <div class="loading-data d-flex justify-content-center p-3" v-if="pointclouds.loading">
                                <div class="loading-content"><i class="icon-spinner2 spinner icon-2x"></i></div>
                            </div>
                            <div class="loaded-data" v-else>
                                <div class="empty-data d-flex justify-content-center align-items-center flex-column p-3" v-if="pointclouds.data.length == 0">
                                    <h5 class="text-center mb-4">Empty list</h5>
                                    <!-- <a href="<?php //APPConfig::getUrl('point-cloud/upload') 
                                                    ?>" class="btn btn-primary"><i class="icon-plus2 mr-2"></i>UPLOAD POINT CLOUD</a> -->
                                </div>
                                <div class="available-data" v-else>
                                    <ul class="media-list media-list-bordered">
                                        <li v-for="(point, index) in pointclouds.data" class="media px-0">
                                            <div class="mr-2">
                                                <a :href="'<?= APPConfig::getUrl('point-cloud/detail/') ?>' + point.slug">
                                                    <img :src="'<?= Yii::$app->homeUrl . 'uploads/' ?>' + point.thumbnail" width="75" height="50" class="border-radius-1">
                                                </a>
                                            </div>
                                            <div class="media-body">
                                                <a :href="'<?= APPConfig::getUrl('point-cloud/detail/') ?>' + point.slug">
                                                    <h6 class="media-title point-cloud-title mb-1">{{ point.title }}</h6>
                                                </a>
                                                <!-- <div class="point-author d-flex align-items-center mb-0">
                                                    <i class="icon-user mr-1"></i>
                                                    <a :href="'<?= APPConfig::getUrl('user/point-cloud/') ?>' + point.author_slug">
                                                        <h6 class="mb-0">{{ point.author }}</h6>
                                                    </a>
                                                    <span class="mb-0 text-muted mx-1">•</span>
                                                    <span class="mb-0 text-muted">{{ formatTime(point.created_at) }}</span>
                                                </div> -->
                                                <!-- <div class="point-summary text-muted">
                                                    <span class="mr-2"><i class="icon-eye2 mr-1"></i>{{ point.count_view ? point.count_view : '0' }}</span>
                                                    <span class="mr-2"><i class="icon-cloud-download2 mr-1"></i>{{ point.count_download ? point.count_download : '0' }}</span>
                                                    <span class="mr-2"><i class="icon-heart5 mr-1"></i>{{ point.count_liked ? point.count_liked : '0' }}</span>
                                                    <span class="mr-2"><i class="icon-star-full2 mr-1"></i>{{ point.avg_rating ? parseFloat(point.avg_rating).toFixed(1) : '0' }}</span>
                                                </div> -->
                                            </div>
                                            <div class="ml-2">
                                                <button class="btn btn-primary btn-sm btn-icon" v-if="map.pointids.indexOf(point.id) == -1" @click="selectPointCloud(index)"><i class="icon-plus2"></i></button>
                                            </div>
                                        </li>
                                    </ul>
                                    <pagination :current="pointclouds.pagination.current" :pages="pointclouds.pagination.pages" @change="pointclouds.page = $event"></pagination>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="base-layers"></div>
                        <div class="tab-pane fade" id="v-layers"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9 h-100">
            <div class="h-100 overflow-hidden position-relative">
                <map-builder ref="mapBuilder" :baseLayers="map.baseLayers" :overlayLayers="map.overlayLayers" :points="map.pointclouds"></map-builder>
                <div class="selected-point-clouds position-absolute top-0 left-0 h-100 show" v-cloak>
                    <div class="card mb-0 h-100 position-relative">
                        <div class="card-header header-elements-inline p-2">
                            <h5 class="card-title">Selected point clouds</h5>
                        </div>
                        <div class="card-body p-2 list" style="overflow-y: scroll;">
                            <div v-if="map.pointclouds.length == 0">
                                <div class="border-2 border-dashed border-dark p-2">
                                    <h6 class="mb-0">Choose point clouds from left list</h6>
                                </div>
                            </div>
                            <div v-else>
                                <ul class="media-list media-list-bordered">
                                    <li v-for="(point, index) in map.pointclouds" class="media px-0">
                                        <div class="mr-3">
                                            <a :href="'<?= APPConfig::getUrl('point-cloud/detail/') ?>' + point.slug">
                                                <img :src="'<?= Yii::$app->homeUrl . 'uploads/' ?>' + point.thumbnail" width="75" height="50" class="border-radius-1">
                                            </a>
                                        </div>
                                        <div class="media-body">
                                            <a :href="'<?= APPConfig::getUrl('point-cloud/detail/') ?>' + point.slug">
                                                <h6 class="media-title point-cloud-title mb-1">{{ point.title }}</h6>
                                            </a>
                                            <!-- <div class="point-author d-flex align-items-center mb-0">
                                                <i class="icon-user mr-1"></i>
                                                <a :href="'<?= APPConfig::getUrl('user/point-cloud/') ?>' + point.author_slug">
                                                    <h6 class="mb-0">{{ point.author }}</h6>
                                                </a>
                                            </div> -->
                                        </div>
                                        <div class="ml-3">
                                            <button class="btn btn-danger btn-sm btn-icon" @click="unselectPointCloud(index)"><i class="icon-minus3"></i></button>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-toggle position-absolute px-1 py-2" @click="toggleSelectedPointClouds">
                            <i class="icon-arrow-right5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12 text-right">
            <button class="btn btn-primary btn-lg text-uppercase" @click="openInformationModal">
            Next Step <i class="icon-arrow-right8 ml-2"></i> 
            </button>
        </div>
    </div>

    <div class="modal fade" id="map-information-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="mapInformationModalLabel">HCMGIS 3D Viewer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label for="map-title">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" v-model="map.title">
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="map-description">Description</label>
                            <textarea rows="3" class="form-control" v-model="map.description"></textarea>
                        </div>
                        <div class="col-md-12 form-group">
                            <label for="map-publish-type">Publish Type</label>
                            <select class="form-control" v-model="map.publishType">
                                <option :value="0">Private</option>
                                <option :value="1">Public</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="saveMap"><i class="icon-floppy-disk mr-2"></i>Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        var vm = new Vue({
            el: '#create-map-page',
            data: {
                pointclouds: {
                    'data': [],
                    'pagination': {},
                    'page': 1,
                    'title': '',
                    'sort': 'most-recent',
                    'loading': true
                },
                map: {
                    'id': null,
                    'title': '',
                    'description': '',
                    'publishType': 1,
                    'pointclouds': [],
                    'pointids': [],
                    'baseLayers': [],
                    'overlayLayers': [],
                    'thumbnail': ''
                },
            },
            created: function() {
                this.getPointClouds();
            },
            computed: {
                pointcloudPage: function() {
                    return this.pointclouds.page;
                }
            },
            watch: {
                pointcloudPage: function() {
                    this.getPointClouds();
                }
            },
            methods: {
                getPointClouds: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('point-cloud/get-list') ?>' +
                        `?page=${this.pointclouds.page}&keyword=${this.pointclouds.title}&sort=${this.pointclouds.sort}`

                    _this.pointclouds.loading = true;
                    sendAjax(api, {}, (resp) => {
                        if (resp.status) {
                            _this.pointclouds.data = resp.points;
                            _this.pointclouds.pagination = resp.pagination;
                            _this.pointclouds.loading = false;
                        } else {
                            toastMessage('error', 'Opps, something went wrong!');
                        }
                    }, 'GET');
                },

                selectPointCloud: function(index) {
                    var _this = this,
                        point = _this.pointclouds.data[index];

                    if (_this.map.pointids.indexOf(point.id) == -1) {
                        _this.map.pointclouds.push(point);
                        _this.map.pointids.push(point.id);
                    }

                    _this.$refs.mapBuilder.updatePointCloudLayer(_this.map.pointclouds);
                },

                unselectPointCloud: function(index) {
                    var _this = this;
                    _this.map.pointclouds.splice(index, 1);
                    _this.map.pointids.splice(index, 1);

                    _this.$refs.mapBuilder.updatePointCloudLayer(_this.map.pointclouds);
                },

                toggleSelectedPointClouds: function() {
                    var elSelectedPointClouds = $('.selected-point-clouds');
                    if(elSelectedPointClouds.hasClass('show')) elSelectedPointClouds.removeClass('show');
                    else elSelectedPointClouds.addClass('show');
                },

                openInformationModal: function() {
                    $('#map-information-modal').modal();
                },

                formatTime: function(time) {
                    return formatTime(time);
                },

                saveMap: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('map/save'); ?>',
                        data = {
                            id: _this.map.id,
                            title: _this.map.title,
                            description: _this.map.description,
                            pointcloud_ids: JSON.stringify(_this.map.pointids),
                            base_layers: JSON.stringify(_this.map.baseLayers),
                            overlay_layers: JSON.stringify(_this.map.overlayLayers),
                            publish_type: _this.map.publishType,
                            thumbnail: _this.map.thumbnail
                        }

                    sendAjax(api, data, (resp) => {
                        if(resp.status) {
                            window.location.assign(resp.direction);
                        } else {
                            toastMessage('error', resp.message);
                        }
                    })
                }
            }
        })
    });
</script>