<?php
use app\modules\app\APPConfig;
use app\modules\app\PathConfig;
use app\modules\contrib\gxassets\GxPointCloudAsset;

GxPointCloudAsset::register($this);
$pageData = [
    'pageTitle' => 'Point cloud detail',
    'headerElements' => [],
];
include('detail_ext.php');
?>
<?= $this->render(PathConfig::getAppViewPath('tagPageHeader'), $pageData); ?>
<div class="container my-3" id="pointcloud-detail-page">
    <div class="row">
        <div class="col-md-8">
            <div class="pointcloud-wrap">
                <div class="btn-fullscreen cursor-pointer" @click="toggleFullscreen" v-cloak>
                    <i class="icon-enlarge" v-if="!fullscreen"></i>
                    <i class="icon-shrink" v-else></i>
                </div>
                <div class="pointcloud-view card mb-0 border-primary border-1 h-100">
                    <div class="d-flex justify-content-center align-items-center h-100 w-100">
                        <i class="icon-spinner2 spinner icon-2x"></i>
                    </div>
                </div>
            </div>
            <div class="feartures-wrap my-3">
                <div class="d-flex flex-colum flex-md-row justify-content-between align-items-center">
                    <div class="btns-left d-flex">
                        <point-like 
                            :like="point.current_user.like" 
                            :pointid="point.id" 
                            :pointname="point.title"
                            @change="point.count_like += $event"></point-like>
                        <point-following 
                            :following="point.current_user.follow" 
                            :pointid="point.id" 
                            :pointname="point.title"
                            @change="point.count_follow += $event"></point-following>
                        <a class="btn btn-sm btn-icon btn-outline border-white bg-white text-white rounded-round mr-2" :href="'<?= APPConfig::getUrl('point-cloud/download?pointid=') ?>' + point.id">
                            <i class="icon-cloud-download2"></i>   
                        </a>
                        <button class="btn btn-sm btn-icon btn-outline border-white bg-white text-white rounded-round mr-2" @click="showShareModal">
                            <i class="icon-share3"></i>   
                        </button>
                        <button data-target="#comment-box" data-toggle="collapse" class="btn btn-sm btn-icon btn-outline border-white bg-white text-white rounded-round">
                            <i class="icon-comment"></i>   
                        </button>
                    </div>
                    <div class="btns-right">
                        <rating :rating="point.current_user.rating"
                                :pointname="point.title"
                                :pointid="point.id"></rating>
                    </div>
                </div>
                <div class="comment-box my-3 collapse" id="comment-box">
                    <div class="form-group">
                        <textarea id="comment-text" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="form-group text-right">
                        <button class="btn btn-primary btn-sm" @click="submitComment"><?= Yii::t('app', 'Submit') ?></button>
                    </div>
                </div>
            </div>
            <hr>
            <div class="comments" v-cloak>
                <ul class="media-list media-chat media-chat-scrollable mb-3">
                    <li class="media" v-for="cmt in comments">
                        <div class="mr-3">
                            <a :href="'<?= APPConfig::getUrl('user/point-cloud/') ?>' + cmt.author_slug">
                                <img :src="getAvatarPath(cmt.author_avatar)" class="rounded-circle" width="40" height="40" style="object-fit: cover">
                            </a>
                        </div>

                        <div class="media-body">
                            <div class="media-chat-item">{{ cmt.content }}</div>
                            <div class="font-size-sm text-muted mt-2"><a href="#"><b>{{ cmt.author }}</b></a> • {{ formatTime(cmt.created_at) }}</div>
                        </div>
                    </li>
                </ul>
                <div class="loadmore d-flex justify-content-center" v-if="comments.length < totalComment">
                    <button class="btn btn-sm btn-primary" @click="getComments(++cmtPage)"><?= Yii::t('app', 'Load more') ?></button>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pointcloud-information">
                <div class="card card-body" v-cloak>
                    <h3 class="card-title text-center font-weight-bold mb-0">{{ point.title }}</h3>
                    <div class="current-user-interactive d-flex justify-content-center"></div>
                    <hr>
                    <div class="counter">
                        <h6><i class="icon-cloud2 mr-2"></i><?= Yii::t('app', 'Points') ?>: <b>{{ formatNumber(point.count_points) }}</b></h6>
                        <h6><i class="icon-eye2 mr-2"></i><?= Yii::t('app', 'Viewed') ?>: <b>{{ point.count_view ? point.count_view : '0' }}</b></h6>
                        <h6><i class="icon-cloud-download2 mr-2"></i><?= Yii::t('app', 'Downloaded') ?>: <b>{{ point.count_download  ? point.count_download : '0' }}</b></h6>
                        <h6><i class="icon-heart5 mr-2"></i><?= Yii::t('app', 'Liked') ?>: <b>{{ point.count_like ? point.count_like : '0' }}</b></h6>
                        <h6><i class="icon-eye mr-2"></i><?= Yii::t('app', 'Following') ?>: <b>{{ point.count_follow ? point.count_follow : '0' }}</b></h6>
                        <h6><i class="icon-star-full2 mr-2"></i><?= Yii::t('app', 'Rating') ?>: <b>{{ point.count_rating ? (parseFloat(point.avg_rating).toFixed(1) + '(' + point.count_rating + ')') : '0' }} </b></h6>
                        <h6><i class="icon-comment mr-2"></i><?= Yii::t('app', 'Comment') ?>: <b>{{ totalComment }}</b></h6>
                    </div>
                    <hr>
                    <div class="">
                        <h6><i class="icon-user mr-2"></i><?= Yii::t('app', 'Author') ?>: <a :href="'<?= APPConfig::getUrl('user/point-cloud/') ?>' + point.author_slug"><b>{{ point.author }}</b></a></h6>
                        <h6><i class="icon-calendar mr-2"></i><?= Yii::t('app', 'Published on') ?>: <b>{{ formatTime(point.created_at) }}</b></h6>
                        <h6><i class="icon-make-group mr-2"></i><?= Yii::t('app', 'Collectors') ?>: <b>{{ point.collectors }}</b></h6>
                        <h6><i class="icon-link mr-2"></i><?= Yii::t('app', 'Reference') ?>: <a :href="point.reference"><b>{{ point.reference }}</b></a></h6>
                        <h6><i class="icon-price-tag2 mr-2"></i><?= Yii::t('app', 'Tags') ?>: <a :href="'<?= APPConfig::getUrl('point-cloud/tag?key=') ?>' + tag" class="point-cloud-tag ml-2" v-if="tag" v-for="tag in point.tags">{{ tag }}</a></h6>
                    </div>
                    <hr>
                    <div class="description">
                        <p>{{ point.description }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <share-modal :url="url" :key="url"></share-modal>
    <download-modal :pointid="point.id" @downloaded="point.count_download = $event"></download-modal>
</div>
<script>
    $(function() {
        var point = JSON.parse(`<?= json_encode($point, true) ?>`)
        var vm = new Vue({
            el: '#pointcloud-detail-page',
            data: {
                point: point,
                url: location.protocol + '//' + location.host + location.pathname,
                comments: [],
                totalComment: 0,
                cmtPage: 1,
                fullscreen: false
            },
            created: function() {
                this.getPointCloudViewer();
                this.countView();
                this.getComments(this.cmtPage);
            },
            methods: {
                getPointCloudViewer: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('point-cloud/view') ?>' + '?slug=' + this.point.slug;
                    
                    sendAjax(api, {}, function(resp) {
                        $('.pointcloud-view').empty().append(resp);
                    });
                },

                countView: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('point-cloud/count-view') ?>'
                        data = {pointid: this.point.id}
                    
                    sendAjax(api, data, function(resp) {
                        if(resp.status) {
                            _this.count_view = resp.count_view
                        }
                    })
                },

                getComments: function(page) {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('point-cloud/get-comments') ?>' + 
                        `?page=${page}&perpage=10&pointid=${this.point.id}`
                    
                    sendAjax(api, {}, function(resp) {
                        if(resp.status) {
                            _this.comments = _this.comments.concat(resp.comments)
                            _this.totalComment = resp.total
                        }
                    }, 'GET')
                },

                submitComment: function() {
                    var _this = this,
                        api = '<?= APPConfig::getUrl('point-cloud/comment') ?>',
                        comment = $('#comment-text'),
                        data = {pointid: this.point.id, content: comment.val()}
                    
                    sendAjax(api, data, function(resp) {
                        if(resp.status) {
                            toastMessage('success', resp.message)
                            comment.val('')
                        } else {
                            toastMessage('error', resp.message)
                        }
                    })
                },

                showShareModal: function() {
                    $('#share-modal').modal()
                },

                downloadPointcloud: function() {
                    var _this = this,
                        api = '/app/point-cloud/download',
                        data = {pointid: _this.point.id, type: 'laz'}
                    
                    sendAjax(api, data, function(resp) {
                        if(resp.status) {
                            // _this.$emit('downloaded', resp.count_download)
                        }
                    }, 'GET');
                },

                // showDownloadModal: function() {
                //     $('#download-modal').modal()
                // },

                formatTime: function(time) {
                    return formatTime(time);
                },

                getAvatarPath: function(path) {
                    return getAvatarPath(path)
                },

                toggleFullscreen: function() {
                    var body = $('body'),
                        pointcloudwrap = $('.pointcloud-wrap')

                    this.fullscreen = !this.fullscreen
                
                    if(this.fullscreen) {
                        if(!body.hasClass('point-fullscreen')) {
                            body.addClass('point-fullscreen')
                        }

                        if(!pointcloudwrap.hasClass('point-fullscreen')) {
                            pointcloudwrap.addClass('point-fullscreen')
                        }
                    } else {
                        if(body.hasClass('point-fullscreen')) {
                            body.removeClass('point-fullscreen')
                        }

                        if(pointcloudwrap.hasClass('point-fullscreen')) {
                            pointcloudwrap.removeClass('point-fullscreen')
                        }
                    }
                },

                formatNumber: function(number) {
                    if(number) return number.replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
                    return 0;
                }
            }
        })
    })
</script>