<?php

use app\modules\app\APPConfig;
use app\modules\cms\CMSConfig;
use app\modules\contrib\gxassets\GxLaddaAsset;

GxLaddaAsset::register($this);
?>

<style>
    .btn-upload-avatar,
    .confirm-change-avatar {
        background-color: rgba(0, 0, 0, 0.5);
        transition: all .5s;
    }

    .user-avatar:hover .btn-upload-avatar {
        opacity: 1;
    }

    .user-avatar {
        width: 150px;
        height: 150px;
    }

    .user-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<div class="content user-page" id="user-page" v-cloak>
    <div class="">
        <div class="user-page-header d-flex justify-content-center align-items-center flex-column card card-body pb-0 mb-0">
            <div class="user-swapper py-4 d-flex flex-column align-items-center">
                <div class="user-avatar rounded-circle overflow-hidden position-relative border-2 border-primary">
                    <img :src="getAvatarPath(avatar)" :alt="profile.fullname" width="150" height="150" style="object-fit: cover">
                    <div class="btn-upload-avatar position-absolute h-50 w-100 bottom-0 opacity-0" v-if="!uploadedAvatar">
                        <div class="position-relative h-100 w-100 d-flex justify-content-center align-items-center">
                            <i class="icon-camera icon-2x"></i>
                            <input class="file-upload-input h-100 position-absolute top-0 w-100 opacity-0 cursor-pointer" type='file' @change="readFileInfo" accept=".jpg, .jpeg" />
                        </div>
                    </div>
                    <div class="confirm-change-avatar position-absolute h-100 w-100 top-0 d-flex justify-content-center align-items-center" v-else>
                        <button class="btn btn-sm btn-icon btn-outline-danger rounded-circle mr-1" @click="cancelChangeAvatar"><i class="icon-trash"></i></button>
                        <button class="btn btn-sm btn-icon btn-outline-primary rounded-circle ml-1" @click="confirmChangeAvatar"><i class="icon-checkmark3"></i></button>
                    </div>
                </div>
                <div class="user-name text-center mt-3 mb-0">
                    <h3>{{ profile.fullname }}</h3>
                </div>
            </div>
            <div class="tab-swapper w-md-50 w-100">
                <ul class="nav nav-tabs nav-tabs-bottom nav-justified">
                    <li class="nav-item"><a href="#information" class="nav-link border-0 active" data-toggle="tab"><?= Yii::t('app', 'Information') ?></a></li>
                    <li class="nav-item"><a href="#setting" class="nav-link border-0" data-toggle="tab"><?= Yii::t('app', 'Setting') ?></a></li>
                    <li class="nav-item"><a href="#following" class="nav-link border-0" data-toggle="tab"><?= Yii::t('app', 'Following') ?> <span v-if="following">({{ following.length }})</span></a></li>
                    <li class="nav-item"><a href="#follower" class="nav-link border-0" data-toggle="tab"><?= Yii::t('app', 'Followers') ?> <span v-if="follower">({{ follower.length }})</span></a></li>
                </ul>
            </div>
        </div>
        <div class="user-page-body py-5">
            <div class="tab-content w-100 d-flex justify-content-center">
                <div class="tab-pane fade show active w-100 w-md-50" id="information">
                    <form action="" id="user-information-form">
                        <div class="row form-group">
                            <div class="col-3 col-form-label"><?= Yii::t('app', 'Fullname') ?>*</div>
                            <div class="col-9">
                                <input type="text" class="form-control" name="AuthUser[fullname]" :value="profile.fullname">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-3 col-form-label"><?= Yii::t('app', 'Birthday') ?></div>
                            <div class="col-9">
                                <input type="date" class="form-control" name="AuthUserInfo[birthday]" :value="profile.birthday">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-3 col-form-label"><?= Yii::t('app', 'Gender') ?></div>
                            <div class="col-9">
                                <select name="AuthUserInfo[gender]" :value="profile.gender" class="form-control">
                                    <option value=""><?= Yii::t('app', 'Gender') ?></option>
                                    <option value="1"><?= Yii::t('app', 'Male') ?></option>
                                    <option value="0"><?= Yii::t('app', 'Female') ?></option>
                                </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-3 col-form-label">Email</div>
                            <div class="col-9">
                                <input type="text" class="form-control" :value="profile.username" disabled>
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-3 col-form-label"><?= Yii::t('app', 'Phone number') ?></div>
                            <div class="col-9">
                                <input type="text" class="form-control" name="AuthUserInfo[phone]" :value="profile.phone" pattern="[0-9]" maxlength="10">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-3 col-form-label"><?= Yii::t('app', 'Company') ?></div>
                            <div class="col-9">
                                <input type="text" class="form-control" name="AuthUserInfo[company]" :value="profile.company">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-3 col-form-label"><?= Yii::t('app', 'Address') ?></div>
                            <div class="col-9">
                                <input type="text" class="form-control" name="AuthUserInfo[address]" :value="profile.address">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-12 text-right">
                                <button class="btn btn-sm btn-primary text-uppercase btn-save-user-information" @click="saveUserInformation"><?= Yii::t('app', 'Save') ?></button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="tab-pane fade w-100 w-md-50" id="setting">
                    <form action="" id="change-password-form">
                        <div class="row form-group">
                            <div class="col-3 col-form-label"><?= Yii::t('app', 'Current password') ?></div>
                            <div class="col-9">
                                <input type="password" class="form-control" name="AuthUser[password]">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-3 col-form-label"><?= Yii::t('app', 'New password') ?></div>
                            <div class="col-9">
                                <input type="password" class="form-control" name="AuthUser[newpassword]">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-3 col-form-label"><?= Yii::t('app', 'Confirm new password') ?></div>
                            <div class="col-9">
                                <input type="password" class="form-control" name="AuthUser[confirmpassword]">
                            </div>
                        </div>
                        <div class="row form-group">
                            <div class="col-12 text-right">
                                <button class="btn btn-sm btn-primary text-uppercase btn-change-password" @click="changePassword"><?= Yii::t('app', 'Save') ?></button>
                            </div>
                        </div>
                    </form>
                    <hr>
                    <div class="row form-group">
                        <div class="col-3 col-form-label"><?= Yii::t('app', 'Logout account') ?></div>
                        <div class="col-9 text-right">
                            <a href="<?= Yii::$app->homeUrl . 'site/logout' ?>" class="btn btn-sm btn-primary"><?= Yii::t('app', 'Logout') ?> <i class="icon-switch ml-2"></i></a>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade w-100" id="following">
                    <div class="loading-data d-flex justify-content-center align-items-center" v-if="following == null">
                        <i class="icon-spinner spinner2"></i>
                    </div>
                    <div class="loaded-data" v-else>
                        <div class="empty-data" v-if="following.length == 0"></div>
                        <div class="available-data container" v-else>
                            <div class="row">
                                <div class="col-md-3" v-for="user in following">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <div class="card-img-actions d-inline-block mb-3">
                                                <img class="rounded-circle" :src="getAvatarPath(user.avatar)" width="150" height="150" style="object-fit: cover">
                                            </div>
                                            <a :href="'<?= APPConfig::getUrl('user/point-cloud/') ?>' + user.slug">
                                                <h6 class="font-weight-semibold mb-0">{{ user.fullname }}</h6>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade w-100" id="follower">
                    <div class="loading-data d-flex justify-content-center align-items-center" v-if="follower == null">
                        <i class="icon-spinner spinner2"></i>
                    </div>
                    <div class="loaded-data" v-else>
                        <div class="empty-data" v-if="follower.length == 0"></div>
                        <div class="available-data container" v-else>
                            <div class="row">
                                <div class="col-md-3" v-for="user in follower">
                                    <div class="card">
                                        <div class="card-body text-center">
                                            <div class="card-img-actions d-inline-block mb-3">
                                                <img class="rounded-circle" :src="getAvatarPath(user.avatar)" width="150" height="150" style="object-fit: cover">
                                            </div>
                                            <a :href="'<?= APPConfig::getUrl('user/point-cloud/') ?>' + user.slug">
                                                <h6 class="font-weight-semibold mb-0">{{ user.fullname }}</h6>
                                            </a>
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
</div>
</div>

<script>
    $(function() {
        var profile = JSON.parse('<?= json_encode($profile, true) ?>')
        var vm = new Vue({
            el: '#user-page',
            data: {
                profile: profile,
                avatar: profile.avatar,
                uploadedAvatar: false,
                following: null,
                follower: null
            },
            created: function() {
                var _this = this;
                _this.$nextTick(function() {
                    _this.getFollowing()
                    _this.getFollower()
                })
            },
            methods: {
                readFileInfo: function(event) {
                    var _this = this,
                        input = event.target,
                        api = '<?= CMSConfig::getUrl('file/upload') ?>'

                    this.uploadFiles(input.files, api, (resp) => {
                        if (resp.fails.length > 0) {
                            toastMessage('error', resp.fails[0] + ' cannot be uploaded')
                        }
                        if (resp.successes.length > 0) {
                            _this.avatar = resp.successes[0].path
                            _this.uploadedAvatar = true
                        }
                    })
                },

                uploadFiles: function(files, api, callback) {
                    var form = new FormData(),
                        xhr = new XMLHttpRequest();

                    for (var i = 0; i < files.length; i++) {
                        var file = files[i];

                        if (['image/jpeg', 'image/jpg'].indexOf(file.type) == -1) {
                            toastMessage('error', file.name + ': Unsupported file format: jpg/jpeg')
                        } else if (file.size > 1048576) {
                            toastMessage('error', file.name + ': Maximum size exceeded: 1MB')
                        } else {
                            form.append('Files[]', file, file.name);
                        }
                    }

                    xhr.onload = function() {
                        if (xhr.status == 200) {
                            response = JSON.parse(this.response)
                            callback(response)
                        } else {
                            toastMessage('error', 'Can not upload image')
                        }
                    }

                    xhr.open('POST', api);
                    xhr.send(form);
                },

                cancelChangeAvatar: function() {
                    this.avatar = this.profile.avatar
                    this.deleteFile(this.uploadedAvatar)
                    this.uploadedAvatar = false
                },

                deleteFile: function(file) {
                    var _this = this
                    var api = '<?= CMSConfig::getUrl('file/delete') ?>',
                        data = {
                            file: file
                        }
                    sendAjax(api, data, function(resp) {})
                },

                confirmChangeAvatar: function() {
                    var _this = this
                    var api = '<?= APPConfig::getUrl('user/change-avatar') ?>',
                        data = {
                            auth_user_id: this.profile.auth_user_id,
                            avatar: this.avatar
                        }
                    sendAjax(api, data, function(resp) {
                        if (resp.status) {
                            _this.uploadedAvatar = false
                            toastMessage('success', resp.message)
                        } else {
                            toastMessage('error', resp.message)
                        }
                    })
                },

                saveUserInformation: function(e) {
                    e.preventDefault()
                    var _this = this
                    var api = '<?= APPConfig::getUrl('user/change-information') ?>',
                        form = $('#user-information-form'),
                        ladda = Ladda.create($(".btn-save-user-information")[0]),
                        data = form.serialize()

                    ladda.start()
                    sendAjax(api, data, function(resp) {
                        if (resp.status) {
                            toastMessage('success', resp.message)
                        } else {
                            toastMessage('error', resp.message)
                        }
                        ladda.stop()
                    })
                },

                changePassword: function(e) {
                    e.preventDefault()
                    var _this = this
                    var api = '<?= APPConfig::getUrl('user/change-password') ?>',
                        form = $('#change-password-form'),
                        ladda = Ladda.create($(".btn-change-password")[0]);
                    data = form.serialize()

                    ladda.start()
                    sendAjax(api, data, function(resp) {
                        if (resp.status) {
                            toastMessage('success', resp.message)
                        } else {
                            toastMessage('error', resp.message)
                        }
                        ladda.stop()
                    })
                },

                getFollowing: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('user/get-following') ?>'

                    sendAjax(api, {}, function(resp) {
                        if (resp.status) {
                            _this.following = resp.following
                        }
                    })
                },

                getFollower: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('user/get-follower') ?>'

                    sendAjax(api, {}, function(resp) {
                        if (resp.status) {
                            _this.follower = resp.follower
                        }
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