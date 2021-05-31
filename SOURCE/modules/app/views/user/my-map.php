<?php

use app\modules\app\APPConfig;
use app\modules\cms\services\MapService;
use app\modules\contrib\gxassets\GxLaddaAsset;
use app\modules\contrib\gxassets\GxVueInfiniteLoadingAsset;
use app\modules\contrib\gxassets\GxVueLazyloadAsset;

GxLaddaAsset::register($this);
GxVueInfiniteLoadingAsset::register($this);
GxVueLazyloadAsset::register($this);
?>

<div class="content user-page" id="user-map-page">
    <div class="">
        <div class="user-map-page-header d-flex justify-content-center align-items-center flex-column card card-body pb-0 mb-0">
            <div class="user-swapper py-4 d-flex align-items-center flex-column" v-cloak>
                <div class="user-avatar rounded-circle overflow-hidden position-relative border-2 border-primary">
                    <img :src="getAvatarPath(user.avatar)" :alt="user.fullname" style="object-fit: cover">
                </div>
                <div class="user-name text-center mt-3 mb-0" v-cloak>
                    <h3>{{ user.fullname }} ({{ user.totalmaps }} maps)</h3>
                </div>
            </div>
            <div class="tab-swapper w-md-50 w-100">
                <ul class="nav nav-tabs nav-tabs-bottom nav-justified" v-cloak>
                    <li class="nav-item"><a href="#public" class="nav-link border-0 active" data-toggle="tab"><?= Yii::t('app', 'Public') ?> ({{ public.maps.length }})</a></li>
                    <li class="nav-item"><a href="#private" class="nav-link border-0" data-toggle="tab"><?= Yii::t('app', 'Private') ?> ({{ private.maps.length }})</a></li>
                </ul>
            </div>
        </div>
        <div class="user-map-page-body py-5 container" v-cloak>
            <div class="tab-content w-100 d-flex justify-content-center">
                <div class="tab-pane fade show active w-100" id="public">
                    <div class="list-data">
                        <div class="row">
                            <div class="col-md-4 cursor-pointer mb-3 map-item" v-for="(map, index) in public.maps">
                                <div class="card list-images-custom mb-0">
                                    <div class="card-img-actions mx-1 mt-1 position-relative overflow-hidden">
                                        <a :href="'<?= APPConfig::getUrl('map/detail/') ?>' + map.slug">
                                            <img v-lazy="'/uploads/' + map.thumbnail" class="card-img img-fluid h-100 w-100">
                                        </a>
                                        <div class="card-img-actions-overlay card-img">
                                            <button class="btn btn-outline-warning border-2 btn-icon rounded-round" @click="confirmPublish(map.id, !map.publish_type)">
                                                <i class="icon-lock5"></i>
                                            </button>
                                            <a class="btn btn-outline-primary border-2 btn-icon rounded-round mx-2" :href="'<?= APPConfig::getUrl('map/edit/') ?>' + map.slug">
                                                <i class="icon-pencil"></i>
                                            </a>
                                            <button class="btn btn-outline-danger border-2 btn-icon rounded-round" @click="confirmDelete(map.id)">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </div>
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
                    </div>
                </div>

                <div class="tab-pane fade w-100" id="private">
                    <div class="list-data">
                        <div class="row">
                            <div class="col-md-4 cursor-pointer mb-3 map-item" v-for="(map, index) in private.maps">
                                <div class="card list-images-custom mb-0">
                                    <div class="card-img-actions mx-1 mt-1 position-relative overflow-hidden">
                                        <a :href="'<?= APPConfig::getUrl('map/detail/') ?>' + map.slug">
                                            <img v-lazy="'/uploads/' + map.thumbnail" class="card-img img-fluid h-100 w-100">
                                        </a>
                                        <div class="card-img-actions-overlay card-img">
                                            <button class="btn btn-outline-warning border-2 btn-icon rounded-round" @click="confirmPublish(map.id, !map.publish_type)">
                                                <i class="icon-unlocked"></i>
                                            </button>
                                            <a class="btn btn-outline-primary border-2 btn-icon rounded-round mx-2" :href="'<?= APPConfig::getUrl('map/edit/') ?>' + map.slug">
                                                <i class="icon-pencil"></i>
                                            </a>
                                            <button class="btn btn-outline-danger border-2 btn-icon rounded-round" @click="confirmDelete(map.id)">
                                                <i class="icon-trash"></i>
                                            </button>
                                        </div>
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
                    </div>
                </div>
            </div>
        </div>
    </div>
    <delete-modal 
        :deletewarning="'Are you sure delete this map cloud'" 
        @delete="deletemap"></delete-modal>
    <change-modal 
        :textwarning="'Are you sure change publish type of this map cloud'" 
        @change="changePublishType"></change-modal>

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

        var user = JSON.parse('<?= json_encode($user, true) ?>')
        var vm = new Vue({
            el: '#user-map-page',
            data: {
                user: user,
                public: {
                    maps: [],
                    page: 1,
                },
                private: {
                    maps: [],
                    page: 1,
                },
                mapSelected: null,
                mapIdSelected: 0,
                shareurl: ''
            },
            created: function() {
                this.getPublicmaps();
                this.getPrivatemaps();
            },
            methods: {
                getPublicmaps() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('user/get-user-maps') ?>' + 
                        `?userid=${this.user.id}&page=${this.public.page}&type=${<?= mapService::$PUBLISH_TYPE['PUBLIC'] ?>}`

                    sendAjax(api, {}, function(resp) {
                        if(resp.status) {
                            _this.public.maps = resp.maps
                        }
                    }, 'GET')
                },

                getPrivatemaps() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('user/get-user-maps') ?>' + 
                        `?userid=${this.user.id}&page=${this.private.page}&type=${<?= MapService::$PUBLISH_TYPE['PRIVATE'] ?>}`;

                    sendAjax(api, {}, function(resp) {
                        if(resp.status) {
                            _this.private.maps = resp.maps;
                        }
                    }, 'GET');
                },

                fixImageHeight: function() {
                    this.$nextTick(function() {
                        fixImageHeight();
                    });
                },

                getAvatarPath: function(avatar) {
                    var path = '<?= Yii::$app->homeUrl ?>' + (avatar ? 'uploads/' + avatar : 'resources/images/no_avatar.jpg');
                    return path;
                },

                confirmPublish: function(id, type) {
                    this.mapIdSelected = id
                    var warningtext
                    if(type) {
                        warningtext = 'Are you sure change this map to the public?';
                    } else {
                        warningtext = 'Are you sure change this map to the private?';
                    }
                    $('#change-modal .warning-text').empty().append(warningtext);
                    $('#change-modal').modal();
                },

                changePublishType: function() {
                    var api = '<?= APPConfig::getUrl('map/change-publish-type') ?>',
                        data = {mapid: this.mapIdSelected};
                    sendAjax(api, data, function(resp) {
                        if(resp.status) {
                            window.location.reload();
                        } else {
                            toastMessage('error', resp.message);
                        }
                    });
                },

                confirmDelete: function(id) {
                    this.mapIdSelected = id;
                    $('#delete-modal').modal();
                },

                deletemap: function() {
                    var api = '<?= APPConfig::getUrl('map/delete') ?>',
                        data = {mapid: this.mapIdSelected}
                    sendAjax(api, data, function(resp) {
                        if(resp.status) {
                            window.location.reload();
                        } else {
                            toastMessage('error', resp.message);
                        }
                    });
                },

                openShareModal: function(mapslug) {
                    this.shareurl = location.protocol + '//' + location.host + '/app/map/detail/' + mapslug;
                    this.$nextTick(function() {
                        $('#share-modal').modal();
                    });
                },

                formatTime: function(timeStr) {
                    return formatTime(timeStr);
                }
            }
        })
    })
</script>