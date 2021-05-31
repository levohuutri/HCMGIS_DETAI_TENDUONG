<?php 
use app\modules\cms\CMSConfig;
use app\modules\cms\PathConfig;
use app\modules\contrib\gxassets\GxLaddaAsset;

GxLaddaAsset::register($this);
$pageData = [
    'pageTitle' => 'Thông tin tài khoản người dùng',
    'headerElements' => [],
];
?>
<?= $this->render(PathConfig::getAppViewPath('tagPageHeader'), $pageData); ?>

<div class="content" id="detail-user-pageadmin">
    <div class="card">
        <div class="card-header">
            
        </div>
        <div class="card-body">
            <div class="user-detail">
                <div class="row form-group">
                    <div class="col-3 col-form-label">Họ tên</div>
                    <div class="col-9">
                        <input type="text" class="form-control" :value="user.fullname" disabled>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-3 col-form-label">Email</div>
                    <div class="col-9">
                        <input type="text" class="form-control" :value="user.username" disabled>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-3 col-form-label">Ngày sinh</div>
                    <div class="col-9">
                        <input type="date" class="form-control" :value="user.birthday" disabled>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-3 col-form-label">Giới tính</div>
                    <div class="col-9">
                        <select class="form-control" :value="user.gender" disabled>
                            <option value="">Giới tính</option>
                            <option value="1">Nam</option>
                            <option value="0">Nữ</option>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-3 col-form-label">Điện thoại</div>
                    <div class="col-9">
                        <input type="text" class="form-control" :value="user.phone" disabled>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-3 col-form-label">Địa chỉ</div>
                    <div class="col-9">
                        <input type="text" class="form-control" :value="user.address" disabled>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-3 col-form-label">Công ty</div>
                    <div class="col-9">
                        <input type="text" class="form-control" :value="user.company" disabled>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-3 col-form-label">Loại tài khoản</div>
                    <div class="col-9">
                        <select class="form-control" :value="user.type" disabled>
                            <option value="1">Public</option>
                            <option value="0">Private</option>
                        </select>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-3 col-form-label">Quyền</div>
                    <div class="col-9">
                        <select class="form-control" :value="user.auth_role_id" disabled>
                            <option v-for="(label, value) in roles" :value="value">{{ label }}</option>
                        </select>
                    </div>
                </div>
            </div>
            <hr>
            <div class="form-reset-password">
                <div class="row form-group">
                    <div class="col-3 col-form-label">Mật khẩu mới</div>
                    <div class="col-9">
                        <input type="text" class="form-control" v-model="newpassword">
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-12 text-right">
                        <button class="btn btn-sm btn-warning text-uppercase" @click="generatePassword">Tạo mật khẩu mới</button>
                        <button class="btn btn-sm btn-primary text-uppercase btn-reset-password" @click="resetPassword">Đặt lại mật khẩu</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>

    var vm = new Vue({
        el: '#detail-user-pageadmin',
        data: {
            user: JSON.parse('<?= json_encode($user, true) ?>'),
            roles: JSON.parse('<?= json_encode($roles, true) ?>'),
            newpassword: null
        },
        methods: {
            generatePassword: function(e) {
                var length = 8,
                    charset = 'abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789',
                    password = ''
                for (var i = 0, n = charset.length; i < length; ++i) {
                    password += charset.charAt(Math.floor(Math.random() * n));
                }

                this.newpassword = password;
            },

            resetPassword: function(e) {
                e.preventDefault()
                var api = '<?= CMSConfig::getUrl('user/reset-password') ?>',
                    data = {newpassword: this.newpassword},
                    ladda = Ladda.create($('.btn-reset-password')[0]);
                
                ladda.start()
                sendAjax(api, data, function(resp) {
                    ladda.stop()
                    if(resp.status) {
                        window.location.assign('<?= CMSConfig::getUrl('user') ?>')
                    } else {
                        toastMessage('error', resp.message)
                    }
                })
            }
        }
    })
</script>