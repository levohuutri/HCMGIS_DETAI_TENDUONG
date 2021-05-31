<?php
use app\modules\app\APPConfig;
use app\modules\contrib\gxassets\GxVueInfiniteLoadingAsset;
use app\modules\contrib\gxassets\GxVueLazyloadAsset;

GxVueInfiniteLoadingAsset::register($this);
GxVueLazyloadAsset::register($this);
?>

<style>
    .homepage-header {
        height: 60vh;
        max-height: 1000px;
        min-height: 500px;
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url(<?= Yii::$app->homeUrl . 'resources/images/banner.jpg' ?>);
        background-position: center;
        background-size: cover;
    }
</style>

<div class="homepage" id="homepage">
    <div class="homepage-header">
        <div class="banner container h-100">
            <div class="row h-100">
                <div class="col md-6 d-flex justify-content-center align-items-center flex-column">
                    <div class="text-center mb-3 wow animated fadeInUp">
                        <h1 class="homepage-title font-weight-bold text-uppercase mb-2"><?= Yii::t('app', 'HCMGIS 3D Viewer') ?></h1>
                        <h5 class="homepage-subtitle"><?= Yii::t('app', 'Your 3D Viewer on the Cloud') ?></h5>
                    </div>
                    <div class="homepage-feature-button wow animated fadeInUp">
                        <a href="<?= APPConfig::getUrl('point-cloud/upload') ?>" 
                            class="btn btn-outline text-white bg-white border-white border-2 rounded-round">
                            <i class="icon-cloud-upload icon-2x mr-2"></i>
                            <?= Yii::t('app', 'Upload Your 3D Viewer') ?>
                        </a>
                    </div>
                </div>
                <div class="col-md-6"></div>
            </div>
        </div>
    </div>
    <div class="homepage-content" id="list-points">
        <div class="container my-5">
            <div class="search-wrap d-flex flex-column align-items-center my-3">
                <div class="search-form w-100 w-md-75">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" id="point-cloud-search-box" placeholder="Point cloud title, tags" v-model="keyword">
                            <div class="input-group-append ml-0">
                                <button type="button" class="btn btn-light btn-icon" id="btn-search" @click="changeKeyword"><i class="icon-search4"></i></button>
                                <!-- <button type="button" class="btn btn-light btn-icon"><i class="icon-plus2"></i></button> -->
                                <button type="button" class="btn btn-light dropdown-toggle btn-icon btn-order" data-toggle="dropdown" aria-expanded="false"></button>
                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" 
                                    style="position: absolute; transform: translate3d(789px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                                    <span class="dropdown-item" v-for="(label, key) in sortMap" 
                                        :class="sort == key ? 'active' : ''" @click="sort = key">{{ label }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- <div class="form-group">
                        <div class="search-metadata"></div>
                    </div> -->
                </div>
            </div>
            <div class="list-data">
                <div class="total-points">
                    <h4 class="mb-4">Total: {{ totalPoints }}</h4>
                </div>
                <div class="row">
                    <point 
                        v-for="(point, index) in points"
                        :point="point" 
                        :key="index"
                        @download="openDownloadModal"
                        @share="openShareModal"></point>
                </div>
                <infinite-loading 
                    :identifier="infiniteId" 
                    @infinite="infiniteHandler" 
                    force-use-infinite-wrapper="true">
                    <div slot="no-more"></div>
                    <div slot="no-results"><?= Yii::t('app', 'Empty list')?></div>
                </infinite-loading>
            </div>
        </div>
    </div>
    <share-modal :url="shareurl" :key="'url' + shareurl"></share-modal>
    <download-modal :pointid="selectedid" :key="'download' + selectedid"></download-modal>
</div>


<script>
    $(function() {
        Vue.use(VueLazyload, {
            preLoad: 1.3,
            error: '<?= Yii::$app->homeUrl . 'resources/images/default.jpg' ?>',
            loading: '<?= Yii::$app->homeUrl . 'resources/images/loading.svg' ?>',
            attempt: 1
        })

        var vm = new Vue({
            el: '#homepage',
            data: {
                points: [],
                page: 1,
                sort: 'most-recent',
                keyword: '',
                infiniteId: +new Date(),
                sortMap: {
                    'most-recent': '<?= Yii::t('app', 'Most recent') ?>',
                    'most-rating': '<?= Yii::t('app', 'Most rating') ?>',
                    'most-like': '<?= Yii::t('app', 'Most like') ?>',
                    'most-view': '<?= Yii::t('app', 'Most view') ?>',
                    'most-download': '<?= Yii::t('app', 'Most download') ?>',
                    'by-title': '<?= Yii::t('app', 'By title') ?>'
                },
                selectedid: 0,
                shareurl: '',
                totalPoints: 0
            },
            watch: {
                sort: function() {
                    this.getNewData()
                },
            },
            methods: {
                infiniteHandler($state) {
                    var api = '<?= APPConfig::getUrl('point-cloud/get-list') ?>' + 
                        `?page=${this.page}&keyword=${this.keyword}&sort=${this.sort}`

                    axios.get(api, {}).then(({ data }) => {
                        if (data.points.length) {
                            this.page += 1;
                            this.points.push(...data.points);
                            this.fixImageHeight();
                            this.totalPoints = data.pagination.total;
                            $state.loaded();
                        } else {
                            $state.complete();
                        }
                    })
                },

                fixImageHeight: function() {
                    this.$nextTick(function() {
                        fixImageHeight();
                    })
                },

                changeKeyword: function() {
                    if(this.keyword) {
                        this.getNewData();
                    }
                },

                getNewData: function() {
                    this.points = [];
                    this.page = 1;
                    this.infiniteId += 1;
                },

                openDownloadModal: function(pointid) {
                    this.selectedid = pointid;
                    this.$nextTick(function() {
                        $('#download-modal').modal();
                    })
                },

                openShareModal: function(pointslug) {
                    this.shareurl = location.protocol + '//' + location.host + '/app/point-cloud/detail/' + pointslug;
                    this.$nextTick(function() {
                        $('#share-modal').modal();
                    })
                }
            }
        });

        $('#point-cloud-search-box').keypress((event) => {
            if(event.keyCode == 13) {
                $('#btn-search').click();
            }
        })
    });
</script>