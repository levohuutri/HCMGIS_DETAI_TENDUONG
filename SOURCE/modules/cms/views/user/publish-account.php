<?php

use app\modules\cms\CMSConfig;
use app\modules\cms\PathConfig;

$pageData = [
    'pageTitle' => 'Manage Account Private/Public',
    'headerElements' => [],
];
?>
<?= $this->render(PathConfig::getAppViewPath('tagPageHeader'), $pageData); ?>


<div class="content" id="user-manage-pageadmin">
    <table class="table table-striped">
        <tr>
            <th>#</th>
            <th></th>
            <th>
                <span class="font-weight-bold">PRIVATE</span>
                <a href="" class="btn btn-icon btn-sm btn-outline-primary ml-2"><i class="icon-pencil"></i></a>
            </th>
            <th>
                <span class="font-weight-bold">PUBLIC</span>
                <a href="" class="btn btn-icon btn-sm btn-outline-primary ml-2"><i class="icon-pencil"></i></a>
            </th>
        </tr>
        <tr>
            <td>1</td>
            <td>Maximum 3D Viewer</td>
            <td>20</td>
            <td>5</td>
        </tr>
        <tr>
            <td>2</td>
            <td>Maximum File Size</td>
            <td>1GB</td>
            <td>500MB</td>
        </tr>
    </table>
</div>

<script>
    $(function() {
        var vm = new Vue({
            el: '#user-manage-pageadmin',
            data: {
                users: users,
                sortMap: {
                    created_at: '<?= Yii::t('app', '3D Viewer Created') ?>',
                    avg_rating: '<?= Yii::t('app', 'Most Followers') ?>',
                    count_like: '<?= Yii::t('app', 'Most Following') ?>',
                    count_view: '<?= Yii::t('app', 'Most 3D Viewer Liked') ?>'
                },
            },
            created: function() {
                var _this = this;
            },

            methods: {

            }
        })
    })
</script>