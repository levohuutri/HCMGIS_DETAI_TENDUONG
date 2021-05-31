<?php

use app\modules\cms\CMSConfig;
use app\modules\cms\PathConfig;

$pageData = [
    'pageTitle' => '3D Viewer statistic',
    'headerElements' => [],
];
?>
<?= $this->render(PathConfig::getAppViewPath('tagPageHeader'), $pageData); ?>


<div class="content" id="user-manage-pageadmin">
    <div class="homepage-content" id="list-points">
        <div class="my-3">
            <div class="search-wrap d-flex flex-column align-items-center my-3">
                <div class="search-form w-100 w-md-75">
                    <div class="form-group">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Point cloud title, tags" v-model="keyword">
                            <div class="input-group-append ml-0">
                                <button type="button" class="btn btn-light btn-icon" @click="changeKeyword"><i class="icon-search4"></i></button>
                                <!-- <button type="button" class="btn btn-light btn-icon"><i class="icon-plus2"></i></button> -->
                                <button type="button" class="btn btn-light dropdown-toggle btn-icon btn-order" data-toggle="dropdown" aria-expanded="false"></button>
                                <div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end" style="position: absolute; transform: translate3d(789px, 36px, 0px); top: 0px; left: 0px; will-change: transform;">
                                    <span class="dropdown-item" v-for="(label, key) in sortMap" :class="sort == key ? 'active' : ''" @click="sort = key">{{ label }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="search-metadata"></div>
                    </div>
                </div>
            </div>
            <table class="table table-striped">
                <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>AVG Rating</th>
                    <th>Liked</th>
                    <th>Viewed</th>
                    <th>Downloaded</th>
                    <th>Actions</th>
                </tr>
                <tr v-for="(point, index) in points">
                    <td>{{ index + 1 }}</td>
                    <td>{{ point.title }}</td>
                    <td>{{ Math.floor(Math.random() * 20) * 0.1 + 3 }}</td>
                    <th>{{ Math.floor(Math.random() * 10) }}</th>
                    <th>{{ Math.floor(Math.random() * 20) }}</th>
                    <th>{{ Math.floor(Math.random() * 20) }}</th>
                    <td>
                        <a href="#" class="btn btn-primary btn-sm">View 3D Viewer</a>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script>
    $(function() {
        var points = JSON.parse('<?= json_encode($points, true) ?>');
        var vm = new Vue({
            el: '#user-manage-pageadmin',
            data: {
                points: points,
                sortMap: {
                    created_at: '<?= Yii::t('app', 'Most recent') ?>',
                    avg_rating: '<?= Yii::t('app', 'Most rating') ?>',
                    count_like: '<?= Yii::t('app', 'Most like') ?>',
                    count_view: '<?= Yii::t('app', 'Most view') ?>',
                    count_download: '<?= Yii::t('app', 'Most download') ?>'
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