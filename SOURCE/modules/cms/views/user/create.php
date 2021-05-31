<?php 
use app\modules\cms\CMSConfig;
use app\modules\cms\PathConfig;
use app\modules\contrib\gxassets\GxLaddaAsset;

GxLaddaAsset::register($this);
$pageData = [
    'pageTitle' => 'Tạo tài khoản người dùng',
    'headerElements' => [],
];
?>
<?= $this->render(PathConfig::getAppViewPath('tagPageHeader'), $pageData); ?>

<div class="content" id="create-user-pageadmin">
    <div class="card">
        <div class="card-header">
            
        </div>
        <div class="card-body">
            <div class="form-create">
                <form action="" id="user-create-form">
                    <div class="row form-group">
                        <div class="col-3 col-form-label">Họ tên*</div>
                        <div class="col-9">
                            <input type="text" class="form-control" name="AuthUser[fullname]">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 col-form-label">Email*</div>
                        <div class="col-9">
                            <input type="text" class="form-control" name="AuthUser[username]">
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 col-form-label">Loại tài khoản</div>
                        <div class="col-9">
                            <select class="form-control" name="AuthUser[type]">
                                <option value="1">Public</option>
                                <option value="0">Private</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-3 col-form-label">Quyền</div>
                        <div class="col-9">
                            <select class="form-control" name="AuthUser[auth_role_id]">
                                <option value="3">User</option>
                                <option value="2">Admin</option>
                            </select>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-12 text-right">
                            <button class="btn btn-sm btn-primary text-uppercase btn-create-user" @click="createUser">Tạo tài khoản</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    var vm = new Vue({
        el: '#create-user-pageadmin',
        data: {},
        methods: {
            createUser: function(e) {
                e.preventDefault()
                var api = '<?= CMSConfig::getUrl('user/save-model') ?>',
                    form = $('#user-create-form'),
                    data = form.serialize(),
                    ladda = Ladda.create($(".btn-create-user")[0]);
                
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