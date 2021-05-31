<?php

use app\modules\app\APPConfig;
use app\modules\contrib\gxassets\GxLaddaAsset;
use app\modules\contrib\gxassets\GxVueInfiniteLoadingAsset;
use app\modules\contrib\gxassets\GxVueLazyloadAsset;

GxLaddaAsset::register($this);
GxVueInfiniteLoadingAsset::register($this);
GxVueLazyloadAsset::register($this);
?>

<div class="content user-page" id="user-pointcloud-page">
    <div class="">
        <div class="user-pointcloud-page-header d-flex justify-content-center align-items-center flex-column card card-body pb-0 mb-0">
            <div class="user-swapper py-4 d-flex align-items-center flex-column" v-cloak>
                <div class="user-avatar rounded-circle overflow-hidden position-relative border-2 border-primary">
                    <img :src="getAvatarPath(user.avatar)" :alt="user.fullname" style="object-fit: cover">
                </div>
                <div class="user-name text-center mt-3 mb-0">
                    <h3>{{ user.fullname }} ({{ user.totalpoints }} points)</h3>
                </div>
                <user-following :following="user.following" :userid="user.id" :fullname="user.fullname"></user-following>
            </div>
        </div>
        <div class="user-pointcloud-page-body py-5 container" v-cloak>
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
                    @infinite="infiniteHandler" 
                    :distance="1500">
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
        
        var user = JSON.parse('<?= json_encode($user, true) ?>')
        var vm = new Vue({
            el: '#user-pointcloud-page',
            data: {
                user: user,
                points: [],
                page: 1,
                selectedid: 0,
                shareurl: ''
            },
            methods: {
                infiniteHandler: function($state) {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('user/get-user-points') ?>' + 
                        `?userid=${this.user.id}&page=${this.page}`
                    
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
                },

                getAvatarPath: function(avatar) {
                    var path = '<?= Yii::$app->homeUrl ?>' + (avatar ? 'uploads/' + avatar : 'resources/images/no_avatar.jpg')
                    return path
                }
            }
        })
    })
</script>