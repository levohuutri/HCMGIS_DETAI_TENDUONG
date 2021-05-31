<?php

use app\modules\app\APPConfig;
use app\modules\cms\services\PointCloudService;
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
                <div class="user-name text-center mt-3 mb-0" v-cloak>
                    <h3>{{ user.fullname }} ({{ user.totalpoints }} points)</h3>
                </div>
            </div>
            <div class="tab-swapper w-md-50 w-100">
                <ul class="nav nav-tabs nav-tabs-bottom nav-justified" v-cloak>
                    <li class="nav-item"><a href="#public" class="nav-link border-0 active" data-toggle="tab"><?= Yii::t('app', 'Public') ?> ({{ public.points.length }})</a></li>
                    <li class="nav-item"><a href="#private" class="nav-link border-0" data-toggle="tab"><?= Yii::t('app', 'Private') ?> ({{ private.points.length }})</a></li>
                    <li class="nav-item"><a href="#liked" class="nav-link border-0" data-toggle="tab"><?= Yii::t('app', 'Liked') ?> ({{ liked.points.length }})</a></li>
                    <li class="nav-item"><a href="#following" class="nav-link border-0" data-toggle="tab"><?= Yii::t('app', 'Following') ?> ({{ following.points.length }})</a></li>
                </ul>
            </div>
        </div>
        <div class="user-pointcloud-page-body py-5 container" v-cloak>
            <div class="tab-content w-100 d-flex justify-content-center">
                <div class="tab-pane fade show active w-100" id="public">
                    <div class="list-data">
                        <div class="row">
                            <my-point v-for="(point, index) in public.points"
                                :point="point"  
                                :key="'public-' + index"
                                @confirm-publish="confirmPublish"
                                @confirm-delete="confirmDelete"
                                @download="openDownloadModal"
                                @share="openShareModal">
                            </my-point>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade w-100" id="private">
                    <div class="list-data">
                        <div class="row">
                            <my-point v-for="(point, index) in private.points"
                                :point="point"  
                                :key="'private-' + index"
                                @confirm-publish="confirmPublish"
                                @confirm-delete="confirmDelete"
                                @download="openDownloadModal"
                                @share="openShareModal">
                            </my-point>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade w-100" id="liked">
                    <div class="list-data">
                        <div class="row">
                            <point 
                                v-for="(point, index) in following.points"
                                :point="point"  
                                :key="'liked-' + index"
                                @download="openDownloadModal"
                                @share="openShareModal"></point>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade w-100" id="following">
                    <div class="list-data">
                        <div class="row">
                            <point 
                                v-for="(point, index) in following.points"
                                :point="point"  
                                :key="'following-' + index"
                                @download="openDownloadModal"
                                @share="openShareModal"></point>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <delete-modal 
        :deletewarning="'Are you sure delete this point cloud'" 
        @delete="deletePoint"></delete-modal>
    <change-modal 
        :textwarning="'Are you sure change publish type of this point cloud'" 
        @change="changePublishType"></change-modal>

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
                public: {
                    points: [],
                    page: 1,
                },
                private: {
                    points: [],
                    page: 1,
                },
                liked: {
                    points: [],
                    page: 1,
                },
                following: {
                    points: [],
                    page: 1,
                },
                pointIdSelected: null,
                selectedid: 0,
                shareurl: ''
            },
            created: function() {
                this.getPublicPoints()
                this.getPrivatePoints()
                this.getLikedPoints()
                this.getFollowingPoints()
            },
            methods: {
                getPublicPoints() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('user/get-user-points') ?>' + 
                        `?userid=${this.user.id}&page=${this.public.page}&type=${<?= PointCloudService::$TYPE['PUBLIC'] ?>}`

                    sendAjax(api, {}, function(resp) {
                        if(resp.status) {
                            _this.public.points = resp.points
                        }
                    }, 'GET')
                },

                getPrivatePoints() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('user/get-user-points') ?>' + 
                        `?userid=${this.user.id}&page=${this.private.page}&type=${<?= PointCloudService::$TYPE['PRIVATE'] ?>}`

                    sendAjax(api, {}, function(resp) {
                        if(resp.status) {
                            _this.private.points = resp.points
                        }
                    }, 'GET')
                },

                getLikedPoints() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('user/get-interacted-points') ?>' + 
                        `?userid=${this.user.id}&page=${this.liked.page}&type=LIKED`

                    sendAjax(api, {}, function(resp) {
                        if(resp.status) {
                            _this.liked.points = resp.points
                        }
                    }, 'GET')
                },

                getFollowingPoints() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('user/get-interacted-points') ?>' + 
                        `?userid=${this.user.id}&page=${this.following.page}&type=FOLLOWING`
                    
                    sendAjax(api, {}, function(resp) {
                        if(resp.status) {
                            _this.following.points = resp.points
                        }
                    }, 'GET')
                },

                fixImageHeight: function() {
                    this.$nextTick(function() {
                        fixImageHeight()
                    })
                },

                getAvatarPath: function(avatar) {
                    var path = '<?= Yii::$app->homeUrl ?>' + (avatar ? 'uploads/' + avatar : 'resources/images/no_avatar.jpg')
                    return path
                },

                confirmPublish: function(id, type) {
                    this.pointIdSelected = id
                    var warningtext
                    if(type) {
                        warningtext = 'Are you sure change this point cloud to the public?'
                    } else {
                        warningtext = 'Are you sure change this point cloud to the private?'
                    }
                    $('#change-modal .warning-text').empty().append(warningtext)
                    $('#change-modal').modal()
                },

                changePublishType: function() {
                    var api = '<?= APPConfig::getUrl('point-cloud/change-publish-type') ?>',
                        data = {pointid: this.pointIdSelected}
                    sendAjax(api, data, function(resp) {
                        if(resp.status) {
                            window.location.reload()
                        } else {
                            toastMessage('error', resp.message)
                        }
                    })
                },

                confirmDelete: function(id) {
                    this.pointIdSelected = id
                    $('#delete-modal').modal()
                },

                deletePoint: function() {
                    var api = '<?= APPConfig::getUrl('point-cloud/delete') ?>',
                        data = {pointid: this.pointIdSelected}
                    sendAjax(api, data, function(resp) {
                        if(resp.status) {
                            window.location.reload()
                        } else {
                            toastMessage('error', resp.message)
                        }
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
                }
            }
        })
    })
</script>