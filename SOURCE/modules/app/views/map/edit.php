<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use app\modules\cms\CMSConfig;
use app\modules\contrib\gxassets\GxLaddaAsset;
use app\modules\contrib\gxassets\GxLeafletScreenshoterAsset;
use app\modules\contrib\gxassets\GxLeafletAsset;

GxLeafletAsset::register($this);
GxLeafletScreenshoterAsset::register($this);
GxLaddaAsset::register($this);

$pageData = [
    'pageTitle' => 'Edit map',
    'headerElements' => [],
];
include('map_ext.php');
?>
<?= $this->render(PathConfig::getAppViewPath('tagPageHeader'), $pageData); ?>

<div class="content mb-3" id="edit-map-page">
    <div class="row" style="height: 100vh">
        <div class="col-md-3 h-100">
            <div class="card h-100">
                <div class="card-header header-elements-inline p-2">
                    <h5 class="card-title">Point clouds and Layers</h5>
                </div>

                <div class="card-body p-2 d-flex flex-column overflow-hidden">
                    <!-- <ul class="nav nav-tabs nav-tabs-bottom nav-justified mb-3">
                        <li class="nav-item"><a href="#pointclouds" class="nav-link border-0 active" data-toggle="tab">Point clouds</a></li>
                        <li class="nav-item"><a href="#base-layers" class="nav-link border-0" data-toggle="tab">Base layers</a></li>
                        <li class="nav-item"><a href="#overlay-layers" class="nav-link border-0" data-toggle="tab">Overlay layers</a></li>
                    </ul> -->
                    <div class="tab-content" style="flex: 1 1 auto; overflow-y: scroll;">
                        <div class="tab-pane fade show active" id="pointclouds" v-cloak>
                            <div class="loading-data d-flex justify-content-center p-3" v-if="pointclouds.loading">
                                <div class="loading-content"><i class="icon-spinner2 spinner icon-2x"></i></div>
                            </div>
                            <div class="loaded-data" v-else>
                                <div class="empty-data d-flex justify-content-center align-items-center flex-column p-3" v-if="pointclouds.data.length == 0">
                                    <h5 class="text-center mb-4">Empty list</h5>
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
                                            </div>
                                            <div class="ml-2">
                                                <button class="btn btn-primary btn-sm btn-icon" v-if="map.pointcloud_ids.indexOf(point.id) == -1" @click="selectPointCloud(index)"><i class="icon-plus2"></i></button>
                                            </div>
                                        </li>
                                    </ul>
                                    <pagination :current="pointclouds.pagination.current" :pages="pointclouds.pagination.pages" @change="pointclouds.page = $event"></pagination>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="tab-pane fade" id="base-layers"></div>
                        <div class="tab-pane fade" id="v-layers"></div> -->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9 h-100">
            <div class="h-100 overflow-hidden position-relative">
                <map-builder ref="mapBuilder" :baselayers="map.base_layers" :overlaylayers="map.overlay_layers" :points="map.pointclouds"></map-builder>
                <div class="list-point-clouds position-absolute top-0 left-0 h-100 show" v-cloak>
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
            <button id="btn-next-step" class="btn btn-primary btn-lg text-uppercase" @click="openInformationModal">
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
                            <div class="file-upload-wrap p-3" v-cloak>
                                <div class="file-upload h-100 w-100 p-5 border-1 border-dashed
                                    d-flex flex-column justify-content-center align-items-center position-relative" v-if="!map.thumbnail">
                                    <h4 class="mb-0 file-upload-title">Drop a thumbnail for your map</h4>
                                    <h5>(jpg, jpeg or png)</h5>
                                    <input type="file" name="thumbnail" accept=".jpg, .jpeg, .png" class="position-absolute top-0 w-100 h-100 opacity-0 cursor-pointer input-thumbnail" @change="readFileInfo">
                                </div>
                                <div class="file-uploaded thumbnail-wrap h-100 d-flex flex-column justify-content-center align-items-center" v-else>
                                    <img class="thumbnail w-100 w-md-75" :src="'<?= Yii::$app->homeUrl . 'uploads/' ?>' + map.thumbnail" alt="Point cloud map">
                                    <div class="d-flex position-relative my-2 w-50">
                                        <div class="progress progress-thumbnail w-100" style="height: 1rem;" v-if="progressUploadThumbnail >= 0">
                                            <div class="progress-bar progress-bar-striped bg-primary" :style="'width: ' + progressUploadThumbnail + '%'">
                                                <span>{{ progressUploadThumbnail }}% Complete</span>
                                            </div>
                                        </div>
                                        <i class="icon-checkmark2 text-primary position-absolute" style="right: -20px" v-if="progressUploadThumbnail >= 100"></i>
                                    </div>
                                    <h5 class="delete-file cursor-pointer text-danger" v-if="progressUploadThumbnail < 0 || progressUploadThumbnail >= 100" @click="removeThumbnail">
                                        <i class="icon-cancel-circle2 mr-2"></i>Remove thumbnail
                                    </h5>
                                </div>
                            </div>
                        </div>
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
                            <select class="form-control" v-model="map.publish_type">
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
        var $map = JSON.parse('<?= json_encode($map) ?>');
        var vm = new Vue({
            el: '#edit-map-page',
            data: {
                pointclouds: {
                    'data': [],
                    'pagination': {},
                    'page': 1,
                    'title': '',
                    'sort': 'most-recent',
                    'loading': true
                },
                map: $map,
                progressUploadThumbnail: 100
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
                            _this.movePointCloudsToSelectedArr();
                        } else {
                            toastMessage('error', 'Opps, something went wrong!');
                        }
                    }, 'GET');
                },

                movePointCloudsToSelectedArr: function() {
                    var _this = this;
                    _this.map.pointclouds = [];
                    _this.pointclouds.data.forEach((point) => {
                        if(_this.map.pointcloud_ids.indexOf(point.id) != -1) {
                            _this.map.pointclouds.push(point);
                        }
                    });
                },

                selectPointCloud: function(index) {
                    var _this = this,
                        point = _this.pointclouds.data[index];

                    if (_this.map.pointcloud_ids.indexOf(point.id) == -1) {
                        _this.map.pointclouds.push(point);
                        _this.map.pointcloud_ids.push(point.id);
                    }

                    _this.$refs.mapBuilder.updatePointCloudLayer(_this.map.pointclouds);
                },

                unselectPointCloud: function(index) {
                    var _this = this;
                    _this.map.pointclouds.splice(index, 1);
                    _this.map.pointcloud_ids.splice(index, 1);

                    _this.$refs.mapBuilder.updatePointCloudLayer(_this.map.pointclouds);
                },

                toggleSelectedPointClouds: function() {
                    var elListPointClouds = $('.list-point-clouds');
                    if(elListPointClouds.hasClass('show')) elListPointClouds.removeClass('show');
                    else elListPointClouds.addClass('show');
                },

                openInformationModal: function() {
                    var _this = this,
                        informationModal = $('#map-information-modal');
                    
                    _this.$refs.mapBuilder.toCenterPointcloudLayer();
                    if(_this.map.thumbnail) {
                        informationModal.modal();
                    } else {
                        var ladda = Ladda.create($('#btn-next-step')[0]);

                        ladda.start();
                        _this.$refs.mapBuilder.toCenterPointcloudLayer();
                        setTimeout(() => {
                            _this.$refs.mapBuilder.simpleMapScreenshoter.takeScreen('image', {
                                mimeType: 'image/jpeg'
                            }).then(imgBase64 => {
                                var api = '<?= APPConfig::getUrl('map/upload-screenshot') ?>',
                                    data = { 'imgBase64' : imgBase64 };

                                sendAjax(api, data, (resp) => {
                                    if(resp.status) {
                                        _this.map.thumbnail = resp.thumbnail;
                                        _this.progressUploadThumbnail = 100;
                                    }
                                    _this.$nextTick(function() {
                                        informationModal.modal();
                                    });
                                    ladda.stop();
                                });
                            }).catch(e => {
                                console.error(e);
                            });
                        }, 1000);
                    }
                    
                },

                saveMap: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('map/save'); ?>',
                        data = {
                            id: _this.map.id,
                            title: _this.map.title,
                            description: _this.map.description,
                            pointcloud_ids: JSON.stringify(_this.map.pointcloud_ids),
                            base_layers: JSON.stringify(_this.map.base_layers),
                            overlay_layers: JSON.stringify(_this.map.overlay_layers),
                            publish_type: _this.map.publish_type,
                            thumbnail: _this.map.thumbnail
                        }

                    sendAjax(api, data, (resp) => {
                        if(resp.status) {
                            window.location.assign('<?= APPConfig::getUrl('user/my-map') ?>');
                        } else {
                            toastMessage('error', resp.message);
                        }
                    })
                },

                formatTime: function(time) {
                    return formatTime(time);
                },

                readFileInfo: function(e) {
                    var _this = this,
                        input = e.target,
                        api = '<?= CMSConfig::getUrl('file/upload') ?>'

                    this.uploadFiles(input.files, api, (resp) => {
                        if (resp.fails.length > 0) {
                            toastMessage('error', resp.fails[0] + ' cannot be uploaded')
                        }
                        if (resp.successes.length > 0) {
                            _this.map.thumbnail = resp.successes[0].path
                        }
                    })
                },

                uploadFiles: function(files, api, callback) {
                    var _this = this;
                    var form = new FormData();
                    var file = files[0];

                    if (['image/jpeg', 'image/jpg', 'image/png'].indexOf(file.type) == -1) {
                        toastMessage('error', file.name + ': Unsupported file format: jpg/jpeg')
                    } else if (file.size > 5242880) {
                        toastMessage('error', file.name + ': Maximum size exceeded: 5MB')
                    } else {
                        form.append('Files[]', file, file.name);
                        _this.$nextTick(function() {
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                $('.thumbnail').attr('src', e.target.result);
                            }
                            reader.readAsDataURL(file);
                        })
                    }

                    if(form.has('Files[]')) {
                        var xhr = new XMLHttpRequest();
                        xhr.upload.addEventListener('progress', function(evt){
                            if (evt.lengthComputable) {
                                var percent = (evt.loaded / evt.total) * 100
                                _this.progressUploadThumbnail = Math.round(percent)
                            }
                        }, false)
                        xhr.addEventListener('load', function(evt) {
                            if(this.status == 200) {
                                var resp = JSON.parse(this.response)
                                callback(resp)
                            }
                        }, false);
                        xhr.addEventListener('error', function(evt) {
                            _this.progressUploadThumbnail = -1
                        }, false);

                        xhr.open('POST', api);
                        xhr.send(form);
                    }
                },
                
                removeThumbnail: function() {
                    this.deleteFile(this.map.thumbnail)
                    this.map.thumbnail = null
                    this.progressUploadThumbnail = 0
                },

                deleteFile: function(file) {
                    var _this = this
                    var api = '<?= CMSConfig::getUrl('file/delete') ?>',
                        data = {
                            file: file
                        }
                    sendAjax(api, data, function(resp) {})
                },
            }
        })
    });
</script>