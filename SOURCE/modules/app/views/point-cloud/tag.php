<?php
use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use app\modules\contrib\gxassets\GxVueInfiniteLoadingAsset;
use app\modules\contrib\gxassets\GxVueLazyloadAsset;

GxVueInfiniteLoadingAsset::register($this);
GxVueLazyloadAsset::register($this);
$pageData = [
    'pageTitle' => 'Tag',
    'headerElements' => [],
];
?>
<?= $this->render(PathConfig::getAppViewPath('tagPageHeader'), $pageData); ?>

<div class="tag-page container" id="tag-page">
    <div class="mb-3">
        <div class="card-header">
            <h4 class="font-weight-bold"><i class="icon-price-tag2 mr-2"></i>Tag: <span v-cloak>{{ tag }}</span></h4>
        </div>
        <div class="list-data">
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
            el: '#tag-page',
            data: {
                points: [],
                page: 1,
                sort: 'most-recent',
                tag: '<?= $key ?>',
                infiniteId: +new Date(),
                sortMap: {
                    'most-recent': '<?= Yii::t('app', 'Most recent') ?>',
                    'most-rating': '<?= Yii::t('app', 'Most rating') ?>',
                    'most-like': '<?= Yii::t('app', 'Most like') ?>',
                    'most-view': '<?= Yii::t('app', 'Most view') ?>',
                    'most-download': '<?= Yii::t('app', 'Most download') ?>'
                },
                selectedid: 0,
                shareurl: ''
            },
            watch: {
                sort: function() {
                    this.getNewData()
                },
            },
            methods: {
                infiniteHandler($state) {
                    var api = '<?= APPConfig::getUrl('point-cloud/get-points-by-tag') ?>' + 
                        `?page=${this.page}&tag=${this.tag}&sort=${this.sort}`

                    axios.get(api, {}).then(({ data }) => {
                        if (data.points.length) {
                            this.page += 1
                            this.points.push(...data.points)
                            this.fixImageHeight()
                            $state.loaded()
                        } else {
                            $state.complete()
                        }
                    })
                },

                fixImageHeight: function() {
                    this.$nextTick(function() {
                        fixImageHeight()
                    })
                },

                changeKeyword: function() {
                    if(this.keyword) {
                        this.getNewData()
                    }
                },

                getNewData: function() {
                    this.points = []
                    this.page = 1
                    this.infiniteId += 1
                },

                openDownloadModal: function(pointid) {
                    this.selectedid = pointid
                    this.$nextTick(function() {
                        $('#download-modal').modal()
                    })
                },

                openShareModal: function(pointslug) {
                    this.shareurl = location.protocol + '//' + location.host + '/app/point-cloud/detail/' + pointslug
                    this.$nextTick(function() {
                        $('#share-modal').modal()
                    })
                }
            }
        })
    })
</script>