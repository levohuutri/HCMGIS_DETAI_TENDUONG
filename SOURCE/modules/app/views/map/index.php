<?php

use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use app\modules\contrib\gxassets\GxVueInfiniteLoadingAsset;
use app\modules\contrib\gxassets\GxVueLazyloadAsset;

GxVueInfiniteLoadingAsset::register($this);
GxVueLazyloadAsset::register($this);

$pageData = [
    'pageTitle' => 'Custom maps of users',
    'headerElements' => [],
];
?>
<?= $this->render(PathConfig::getAppViewPath('tagPageHeader'), $pageData); ?>

<div class="container" id="user-map-page">
    <div class="card-header header-elements-inline">
        <h4 class="card-title">Totals: {{ totalMaps }}</h4>
        <div class="header-elements">
            <a href="<?= APPConfig::getUrl('map/create') ?>" class="btn btn-primary"><i class="icon-plus2 mr-2"></i>CREATE MAP</a>
        </div>
    </div>
    <hr class="my-0">
    <div class="list-data mt-3 mb-4">
        <div class="row">
            <div class="col-md-4 cursor-pointer mb-3 map-item" v-for="(map, index) in maps">
                <div class="card list-images-custom mb-0">
                    <div class="card-img-actions mx-1 mt-1 position-relative overflow-hidden">
                        <a :href="'<?= APPConfig::getUrl('map/detail/') ?>' + map.slug">
                            <img v-lazy="'/uploads/' + map.thumbnail" class="card-img img-fluid h-100 w-100">
                        </a>
                    </div>
                    <div class="card-body p-2 d-flex flex-column justify-content-between position-relative">
                        <a :href="'<?= APPConfig::getUrl('map/detail/') ?>' + map.slug" class="images-address mt-2">
                            <h5 :title="map.title" class="font-weight-bold text-custom">{{ map.title }}</h5>
                        </a>
                        <div class="images-summary flex-1">
                            <div class="d-flex align-items-center">
                                <i class="icon-user mr-2"></i>
                                <div class="d-flex justify-content-center align-items-center">
                                    <h5 class="mb-0">
                                        <a href="<?= APPConfig::getUrl('user/my-profile') ?>">{{ map.author }}</a>
                                    </h5>
                                    <p class="mb-0 text-muted mx-1">•</p>
                                    <p class="mb-0 text-muted">{{ formatTime(map.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="list-icons list-icons-extended ml-auto position-absolute top-0" style="right: 1rem; transform: translateY(-50%);">
                            <button class="btn bg-white btn-float rounded-round p-2" @click="openShareModal(map.slug)">
                                <i class="icon-share3 text-primary"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <infinite-loading :identifier="infiniteId" @infinite="infiniteHandler" force-use-infinite-wrapper="true">
            <div slot="no-more"></div>
            <div slot="no-results"><?= Yii::t('app', 'Empty list') ?></div>
        </infinite-loading>
    </div>
    <share-modal :url="shareurl" :key="'url' + shareurl"></share-modal>
</div>

<script>
    $(function() {
        Vue.use(VueLazyload, {
            preLoad: 1.3,
            error: '<?= Yii::$app->homeUrl . 'resources/images/default.jpg' ?>',
            loading: '<?= Yii::$app->homeUrl . 'resources/images/loading.svg' ?>',
            attempt: 1
        });

        var vm = new Vue({
            el: '#user-map-page',
            data: {
                maps: [],
                infiniteId: +new Date(),
                page: 1,
                keyword: '',
                selectedid: 0,
                shareurl: '',
                totalMaps: 0
            },
            methods: {
                infiniteHandler($state) {
                    var api = '<?= APPConfig::getUrl('map/get-public-maps') ?>' + 
                        `?page=${this.page}&keyword=${this.keyword}`

                    axios.get(api, {}).then(({ data }) => {
                        if (data.maps.length) {
                            this.page += 1;
                            this.maps.push(...data.maps);
                            this.fixImageHeight();
                            this.totalMaps = data.pagination.total;
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

                formatTime: function(timeStr) {
                    return formatTime(timeStr);
                },

                openShareModal: function(slug) {
                    this.shareurl = location.protocol + '//' + location.host + '/app/map/detail/' + slug;
                    this.$nextTick(function() {
                        $('#share-modal').modal();
                    })
                }
            }
        })
    });
</script>