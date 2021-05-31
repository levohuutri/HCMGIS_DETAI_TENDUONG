<?php 
use app\modules\cms\CMSConfig;
use app\modules\cms\PathConfig;

$pageData = [
    'pageTitle' => 'Quản trị người dùng',
    'headerElements' => [],
];
?>
<?= $this->render(PathConfig::getAppViewPath('tagPageHeader'), $pageData); ?>


<div class="content" id="user-manage-pageadmin">
    <div class="card">
        <div class="card-header header-elements-inline">
            <h4 class="card-title font-weight-bold">#Danh sách người dùng</h4>
            <div class="header-elemnts">
                <a href="<?= CMSConfig::getUrl('user/create') ?>" class="btn btn-sm btn-primary"><i class="icon-user-plus mr-2"></i>Tạo tài khoản mới</a>
            </div>
        </div>
        <div class="card-body" v-cloak>
            <div class="loading-users text-center my-3" v-if="!loadedUsers" >
                <span><i class="icon-spinner2 spinner icon-2x mr-2"></i> Đang tải danh sách người dùng</span>
            </div>
            <div class="users-available" v-else>
                <table class="table table-striped">
                    <tr>
                        <th>#</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Loại tài khoản</th>
                        <th>Quyền</th>
                        <th>Xác minh</th>
                        <th>Thao tác</th>
                    </tr>
                    <tr v-for="(user, index) in users">
                        <td>{{ index + 1 }}</td>
                        <td>{{ user.fullname }}</td>
                        <td>{{ user.username }}</td>
                        <td>
                            <select class="form-control" v-model="user.type" @change="changeType(user)">
                                <option value="1">Public</option>
                                <option value="0">Private</option>
                            </select>
                        </td>
                        <td>
                            <select class="form-control" v-model="user.auth_role_id" @change="changeRole(user)">
                                <option v-for="(label, value) in roles" :value="value">{{ label }}</option>
                            </select>
                        </td>
                        <th>
                            <span class="badge badge-danger" v-if="!user.confirmed"><i class="icon-cross"></i></span>
                            <span class="badge badge-primary" v-else><i class="icon-checkmark2"></i></span>
                        </th>
                        <td>
                            <a :href="'<?= CMSConfig::getUrl('user/detail?slug=') ?>' + user.slug" class="btn btn-icon btn-sm btn-outline-primary"><i class="icon-eye"></i></a>
                            <button class="btn btn-icon btn-sm btn-outline-danger" @click="confirmDelete(user.id)"><i class="icon-trash"></i></button>
                        </td>
                    </tr>
                </table>
                <ul class="pagination-flat justify-content-center twbs-visible-pages pagination" v-if="paginations.pages > 1">
                    <li class="page-item" v-for="p, i in paginations.links" :class="p == 'current' ? 'active' : '' ">
                        <a href="#" class="page-link" @click="changePage(p)">
                            {{ i == 0 ? '← &nbsp; Đầu tiên' : (i == paginations.links.length - 1 ? 'Cuối cùng &nbsp; →' : (p == 'current' ? paginations.current : p)) }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="exampleModalLabel">HCMGIS 3D Viewer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Bạn có chắc chắn xóa người dùng này?</h5>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" id="btn-delete-work" @click="deleteUser">Delete</button>
            </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(function() {
        var vm = new Vue({
            el: '#user-manage-pageadmin',
            data: {
                users: null,
                selecteduser: null,
                loadedUsers: false,
                roles: JSON.parse('<?= json_encode($roles, true) ?>'),
                page: 1
            },
            created: function() {
                var _this = this;
                _this.$nextTick(function() {
                    _this.loadUsers();
                })
            },

            methods: {
                loadUsers: function() {
                    var _this = this;
                    var api = '<?= CMSConfig::getUrl('user/get-list?page=') ?>' + _this.page;
                    _this.loadedUsers = false;
                    
                    _this.sendAjax(api, {}, function(resp) {
                        if (resp.status) {
                            _this.users = resp.users;
                            _this.paginations = resp.paginations;
                            _this.loadedUsers = true;
                        } else {
                            toastMessage('error', resp.message);
                        }
                    })
                },

                changePage: function(page) {
                    if(page != 'current') {
                        this.page = page;
                        this.loadWorks();
                    }
                },

                changeType: function(user) {
                    var _this = this,
                        api = '<?= CMSConfig::getUrl('user/change-type')?>',
                        data = {
                            userid: user.id,
                            type: user.type
                        }
                        
                    _this.sendAjax(api, data, function(resp) {
                        if(resp.status) {
                            toastMessage('success', resp.message);
                            _this.loadUsers();
                        } else {
                            toastMessage('error', resp.message);
                        }
                    })
                },
                
                changeRole: function(user) {
                    var _this = this,
                        api = '<?= CMSConfig::getUrl('user/change-role')?>',
                        data = {
                            userid: user.id,
                            roleid: user.auth_role_id
                        }
                        
                    _this.sendAjax(api, data, function(resp) {
                        if(resp.status) {
                            toastMessage('success', resp.message);
                            _this.loadUsers();
                        } else {
                            toastMessage('error', resp.message);
                        }
                    })
                },

                confirmDelete: function(id) {
                    this.selecteduser = id
                    $('#btn-delete-work').css('display', 'block');
                    $('#delete-modal').modal('show');
                },
                
                deleteUser: function() {
                    var _this = this,
                        api = '<?= CMSConfig::getUrl('user/delete')?>',
                        data = {
                            id: _this.selecteduser
                        }
                    _this.sendAjax(api, data, function(resp) {
                        if(resp.status) {
                            toastMessage('success', resp.message);
                            _this.loadUsers();
                        } else {
                            toastMessage('error', resp.message);
                        }
                    })
                },

                sendAjax(api, data, callback) {
                    console.log(data)
                    $.ajax({
                        url: api,
                        type: 'POST',
                        data: data,
                        success: function(resp) {
                            callback(resp)
                        },
                        error: (msg) => {
                            toastMessage('error', 'Lỗi!')
                        }
                    })
                },
            }
        })
    })
</script>