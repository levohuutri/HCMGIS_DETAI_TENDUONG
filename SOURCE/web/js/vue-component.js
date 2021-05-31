Vue.component('userFollowing', {
    props: ['following', 'userid', 'fullname'],
    template: `<div class="following d-flex justify-content-center">
        <button class="btn btn-sm btn-warning" @click="follow" v-if="!followingStt"><i class="icon-user-plus mr-2"></i>Follow</button>
        <button class="btn btn-sm btn-primary" @click="unfollow" v-else><i class="icon-user-check mr-2"></i>Unfollow</button>
    </div>`,
    data: function () {
        return {
            followingStt: this.following
        }
    },
    methods: {
        follow: function () {
            var _this = this,
                api = '/app/user/follow',
                data = {
                    userid: this.userid,
                    fullname: this.fullname
                }

            sendAjax(api, data, function (resp) {
                if (resp.status) {
                    _this.followingStt = true
                } else {
                    toastMessage('error', resp.message)
                }
            })
        },

        unfollow: function () {
            var _this = this,
                api = '/app/user/unfollow',
                data = {
                    userid: this.userid,
                    fullname: this.fullname
                }

            sendAjax(api, data, function (resp) {
                if (resp.status) {
                    _this.followingStt = false
                } else {
                    toastMessage('error', resp.message)
                }
            })
        },
    }
})

Vue.component('point-following', {
    props: ['following', 'pointid', 'pointname'],
    template: `<div class="btn-following-wrap mr-2">
        <button class="btn btn-outline bg-white text-white border-white btn-sm btn-icon rounded-round" @click="interactive('FOLLOW')" v-if="!followingStt">
            <i class="icon-eye-plus"></i>
        </button>
        <button class="btn btn-outline-primary btn-sm btn-icon rounded-round" @click="interactive('UNFOLLOW')" v-else>
            <i class="icon-eye"></i>
        </button>
    </div>`,
    data: function () {
        return {
            followingStt: this.following,
            followMAP: { 'FOLLOW': 1, 'UNFOLLOW': 0 }
        }
    },
    methods: {
        interactive: function (type) {
            var _this = this,
                api = '/app/point-cloud/interactive',
                data = {
                    pointid: this.pointid,
                    pointname: this.pointname,
                    type: type
                }

            sendAjax(api, data, function (resp) {
                if (resp.status) {
                    _this.followingStt = _this.followMAP[type]
                    _this.$emit('change', _this.followingStt ? 1 : -1)
                } else {
                    toastMessage('error', resp.message)
                }
            })
        }
    }
})

Vue.component('point-like', {
    props: ['like', 'pointid', 'pointname'],
    template: `<div class="btn-like-wrap mr-2">
        <button class="btn btn-outline bg-white text-white border-white btn-sm btn-icon rounded-round" @click="interactive('LIKE')" v-if="!likeStt">
            <i class="icon-heart6"></i>
        </button>
        <button class="btn btn-outline-primary btn-sm btn-icon rounded-round" @click="interactive('UNLIKE')" v-else>
            <i class="icon-heart5"></i>
        </button>
    </div>`,
    data: function () {
        return {
            likeStt: this.like,
            likeMAP: { 'LIKE': 1, 'UNLIKE': 0 }
        }
    },
    methods: {
        interactive: function (type) {
            var _this = this,
                api = '/app/point-cloud/interactive',
                data = {
                    pointid: this.pointid,
                    pointname: this.pointname,
                    type: type
                }

            sendAjax(api, data, function (resp) {
                if (resp.status) {
                    _this.likeStt = _this.likeMAP[type]
                    _this.$emit('change', _this.likeStt ? 1 : -1)
                } else {
                    toastMessage('error', resp.message)
                }
            })
        }
    }
})

Vue.component('rating', {
    props: ['pointid', 'rating', 'pointname'],
    template: `<div class="rating-wrap">
        <i class="icon-star icon-star-full2" :class="getClass(star, hover, 1)" 
            @mouseover="hover = 1" @mouseleave="hover = 0" @click="rate(1)"></i>
        <i class="icon-star icon-star-full2" :class="getClass(star, hover, 2)" 
            @mouseover="hover = 2" @mouseleave="hover = 0" @click="rate(2)"></i>
        <i class="icon-star icon-star-full2" :class="getClass(star, hover, 3)" 
            @mouseover="hover = 3" @mouseleave="hover = 0" @click="rate(3)"></i>
        <i class="icon-star icon-star-full2" :class="getClass(star, hover, 4)" 
            @mouseover="hover = 4" @mouseleave="hover = 0" @click="rate(4)"></i>
        <i class="icon-star icon-star-full2" :class="getClass(star, hover, 5)" 
            @mouseover="hover = 5" @mouseleave="hover = 0" @click="rate(5)"></i>
    </div>`,
    data: function () {
        return {
            star: this.rating,
            hover: 0
        }
    },
    methods: {
        rate: function (star) {
            var _this = this,
                api = '/app/point-cloud/interactive',
                data = {
                    pointid: this.pointid,
                    pointname: this.pointname,
                    type: 'RATING',
                    star: star
                }

            sendAjax(api, data, function (resp) {
                if (resp.status) {
                    _this.star = star
                }
            })
        },

        getClass: function (star, hover, value) {
            if (hover < value && star >= value) {
                return 'active'
            } else if (hover >= value && star >= value) {
                return 'active hover'
            } else if (hover >= value) {
                return 'hover'
            }

            return ''
        }
    }
})

Vue.component('pagination', {
    props: {
        current: [Number, String],
        pages: [Number, String],
    },
    data: function () {
        return {
            page: this.current
        }
    },
    template: `
    <ul class="pagination pagination-pager pagination-rounded justify-content-center">
        <li class="page-item first" :class="page == 1 ? 'disabled' : ''"  @click="page = 1">
            <span class="page-link">⇤</span>
        </li>
        <li class="page-item prev" :class="page == 1 ? 'disabled' : ''"  @click="page = --page < 1 ? 1 : page">
            <span class="page-link">⇠</span>
        </li>
        <li class="page-item next" :class="page == pages ? 'disabled' : ''"  @click="page = ++page > pages ? pages : page">
            <span class="page-link">⇢</span>
        </li>
        <li class="page-item last" :class="page == pages ? 'disabled' : ''" @click="page = pages">
            <span class="page-link">⇥</span>
        </li>
    </ul>`,
    watch: {
        page: function () {
            this.$emit('change', this.page);
        }
    }
})

Vue.component('pagination-summary', {
    props: ['current', 'from', 'to', 'total'],
    template: `
    <h5 class="mb-0">
        Page {{ current }}: <b>{{ from }}</b> - <b>{{ to }}</b> on <b>{{ total }}</b> results
    </h5>`
})

Vue.component('point', {
    props: ['point'],
    template: `
    <div class="col-md-4 cursor-pointer mb-3 point-item">
        <div class="card list-images-custom mb-0">
            <div class="card-img-actions mx-1 mt-1 position-relative overflow-hidden">
                <a :href="'/app/point-cloud/detail/' + point.slug">
                    <img v-lazy="'/uploads/' + point.thumbnail" class="card-img img-fluid h-100 w-100">
                </a>
                <div class="point-counter card-overlay wow animated fadeInDown">
                    <div class="count_view counter-item">
                        <a :href="'/app/point-cloud/detail/' + point.slug" class="text-white"><i class="icon-eye2 mr-1"></i>{{ point.count_view ? point.count_view : '0' }}</a>
                    </div>
                    <div class="count_download counter-item"> <!-- @click="$emit('download', point.id)" -->
                        <a :href="'/app/point-cloud/download?pointid=' + point.id" class="text-white"><i class="icon-cloud-download2 mr-1"></i>{{ point.count_download ? point.count_download : '0' }}</a>
                    </div>
                    <div class="count_like counter-item d-flex">
                        <div @click="like('UNLIKE')" v-if="likeStt">
                            <i class="icon-heart5 mr-1"></i>{{ countLike ? countLike : '0' }}
                        </div>
                        <div @click="like('LIKE')" v-else>
                            <i class="icon-heart6 mr-1"></i>{{ countLike ? countLike : '0' }}
                        </div>
                    </div>
                    <div class="avg_rating counter-item" @click="startRating = true">
                        <i class="icon-star-full2 mr-1"></i>{{ point.avg_rating ? parseFloat(point.avg_rating).toFixed(1) : '0' }}
                    </div> 
                </div>
                <div class="user-rating card-overlay" v-show="startRating">
                    <div class="content-rating">
                        <div class="rating-availale d-flex justify-content-center">
                            <rating :rating="point.current_user.rating"
                                :pointname="point.title"
                                :pointid="point.id"
                                :key="'rating' + point.id"></rating>

                            <button type="button" class="position-absolute close" style="right: 10px" @click="startRating = false">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-2 d-flex flex-column justify-content-between position-relative">
                <a :href="'/app/point-cloud/detail/' + point.slug" class="images-address mt-2">
                    <h5 :title="point.title" class="font-weight-bold text-custom">{{ point.title }}</h5>
                </a>
                <div class="images-summary flex-1">
                    <div class="d-flex align-items-center">
                        <i class="icon-user mr-2"></i>
                        <div class="d-flex justify-content-center align-items-center">
                            <h5 class="mb-0">
                                <a :href="'/app/user/point-cloud/' + point.author_slug">{{ point.author }}</a>
                            </h5>
                            <p class="mb-0 text-muted mx-1">•</p>
                            <p class="mb-0 text-muted">{{ formatTime(point.created_at) }}</p>
                        </div>
                    </div>
                </div>
                <div class="list-icons list-icons-extended ml-auto position-absolute top-0" style="right: 1rem; transform: translateY(-50%);">
                    <button class="btn bg-white btn-float rounded-round p-2" @click="$emit('share', point.slug)">
                        <i class="icon-share3 text-primary"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>`,
    data: function () {
        return {
            startRating: false,
            likeMAP: { 'LIKE': 1, 'UNLIKE': 0 },
            likeStt: this.point.current_user.like,
            countLike: this.point.count_like
        }
    },
    methods: {
        formatTime: function (time) {
            return formatTime(time);
        },

        like: function (type) {
            var _this = this,
                api = '/app/point-cloud/interactive',
                data = {
                    pointid: this.point.id,
                    pointname: this.point.title,
                    type: type
                }

            sendAjax(api, data, function (resp) {
                if (resp.status) {
                    _this.likeStt = _this.likeMAP[type]
                    _this.countLike += _this.likeStt ? 1 : -1
                } else {
                    toastMessage('error', resp.message)
                }
            })
        }
    }
})

Vue.component('my-point', {
    props: ['point'],
    template: `
    <div class="col-md-4 cursor-pointer mb-3 point-item">
        <div class="card list-images-custom mb-0">
            <div class="card-img-actions mx-1 mt-1 position-relative overflow-hidden">
                <a :href="'/app/point-cloud/detail/' + point.slug">
                    <img v-lazy="'/uploads/' + point.thumbnail" class="card-img img-fluid h-100 w-100">
                </a>
                <div class="card-img-actions-overlay card-img">
                    <button class="btn btn-outline-warning border-2 btn-icon rounded-round mr-2" @click="$emit('confirm-publish', point.id, !point.type)" v-if="point.type">
                        <i class="icon-lock5"></i>
                    </button>
                    <button class="btn btn-outline-warning border-2 btn-icon rounded-round mr-2" @click="$emit('confirm-publish', point.id, !point.type)" v-else>
                        <i class="icon-unlocked2"></i>
                    </button>
                    <button class="btn btn-outline-primary border-2 btn-icon rounded-round ml-2">
                        <i class="icon-pencil"></i>
                    </button>
                    <button class="btn btn-outline-danger border-2 btn-icon rounded-round ml-2" @click="$emit('confirm-delete', point.id)">
                        <i class="icon-trash"></i>
                    </button>
                </div>
                <div class="point-counter card-overlay wow animated fadeInDown">
                    <div class="count_view counter-item">
                        <a :href="'/app/point-cloud/detail/' + point.slug" class="text-white"><i class="icon-eye2 mr-1"></i>{{ point.count_view ? point.count_view : '0' }}</a>
                    </div>
                    <div class="count_download counter-item"> <!-- @click="$emit('download', point.id)" -->
                        <a :href="'/app/point-cloud/download?pointid=' + point.id" class="text-white"><i class="icon-cloud-download2 mr-1"></i>{{ point.count_download ? point.count_download : '0' }}</a>
                    </div>
                    <div class="count_like counter-item d-flex">
                        <div @click="like('UNLIKE')" v-if="likeStt">
                            <i class="icon-heart5 mr-1"></i>{{ countLike ? countLike : '0' }}
                        </div>
                        <div @click="like('LIKE')" v-else>
                            <i class="icon-heart6 mr-1"></i>{{ countLike ? countLike : '0' }}
                        </div>
                    </div>
                    <div class="avg_rating counter-item" @click="startRating = true">
                        <i class="icon-star-full2 mr-1"></i>{{ point.avg_rating ? parseFloat(point.avg_rating).toFixed(1) : '0' }}
                    </div> 
                </div>
                <div class="user-rating card-overlay" v-show="startRating">
                    <div class="content-rating">
                        <div class="rating-availale d-flex justify-content-center">
                            <rating :rating="point.current_user.rating"
                                :pointname="point.title"
                                :pointid="point.id"
                                :key="'rating' + point.id"></rating>

                            <button type="button" class="position-absolute close" style="right: 10px" @click="startRating = false">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-2 d-flex flex-column justify-content-between position-relative">
                <a :href="'/app/point-cloud/detail/' + point.slug" class="images-address mt-2">
                    <h5 :title="point.title" class="font-weight-bold text-custom">{{ point.title }}</h5>
                </a>
                <div class="images-summary flex-1">
                    <div class="d-flex align-items-center">
                        <i class="icon-user mr-2"></i>
                        <div class="d-flex justify-content-center align-items-center">
                            <h5 class="mb-0">
                                <a :href="'/app/user/point-cloud/' + point.author_slug">{{ point.author }}</a>
                            </h5>
                            <p class="mb-0 text-muted mx-1">•</p>
                            <p class="mb-0 text-muted">{{ formatTime(point.created_at) }}</p>
                        </div>
                    </div>
                </div>
                <div class="list-icons list-icons-extended ml-auto position-absolute top-0" style="right: 1rem; transform: translateY(-50%);">
                    <button class="btn bg-white btn-float rounded-round p-2" @click="$emit('share', point.slug)">
                        <i class="icon-share3 text-primary"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>`,
    data: function () {
        return {
            startRating: false,
            likeMAP: { 'LIKE': 1, 'UNLIKE': 0 },
            likeStt: this.point.current_user.like,
            countLike: this.point.count_like
        }
    },
    methods: {
        formatTime: function (time) {
            return formatTime(time);
        },

        like: function (type) {
            var _this = this,
                api = '/app/point-cloud/interactive',
                data = {
                    pointid: this.point.id,
                    pointname: this.point.title,
                    type: type
                }

            sendAjax(api, data, function (resp) {
                if (resp.status) {
                    _this.likeStt = _this.likeMAP[type]
                    _this.countLike += _this.likeStt ? 1 : -1
                } else {
                    toastMessage('error', resp.message)
                }
            })
        }
    }
})
/**<a :href="'/app/point-cloud/edit/' + point.slug" class="btn btn-outline-primary border-2 btn-icon rounded-round">
    <i class="icon-pencil"></i>
</a> */

/**MODALS */

Vue.component('delete-modal', {
    props: ['deletewarning'],
    template: `<div class="modal fade" id="delete-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="deleteModalLabel">HCMGIS 3D Viewer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="warning-text">{{ deletewarning }}</h5>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal" @click="$emit('delete')">Delete</button>
            </div>
            </div>
        </div>
    </div>`
})

Vue.component('change-modal', {
    props: ['warningtext'],
    template: `<div class="modal fade" id="change-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="changeModalLabel">HCMGIS 3D Viewer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5 class="warning-text">{{ warningtext }}</h5>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" data-dismiss="modal" @click="$emit('change')">Change</button>
            </div>
            </div>
        </div>
    </div>`
})

Vue.component('share-modal', {
    props: {
        url: String
    },
    mounted: function () {
        let _this = this;
        $('#share-modal').on('shown.bs.modal', () => {
            _this.selectUrl();
        });
    },
    template: `<div class="modal fade" id="share-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-light">
                    <h5 class="modal-title" id="shareModalLabel">HCMGIS 3D Viewer</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="social-wrap">
                        <button class="btn btn-sm btn-icon btn-primary rounded-round" @click="share">
                            <i class="icon-facebook"></i>
                        </button>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="">Copy and paste anywhere</label>
                        <input type="text" :value="url" id="point-cloud-url" class="form-control">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-dismiss="modal" @click="copyIdentifyAddressLink">Copy</button>
                </div>
            </div>
        </div>
    </div>`,
    methods: {
        share: function () {
            let shareurl = this.url
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${escape(shareurl)}&t=${document.title}`, '',
                'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=300,width=600');
            return false;
        },

        copyIdentifyAddressLink: function () {
            this.selectUrl();
            document.execCommand("copy");
            toastMessage('success', 'Copied to clipboard');
        },

        selectUrl: function () {
            var url = document.getElementById('point-cloud-url');
            url.select();
            url.setSelectionRange(0, 99999);
        }
    }
})

Vue.component('download-modal', {
    props: ['pointid'],
    template: `<div class="modal fade" id="download-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="downloadModalLabel">HCMGIS 3D Viewer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h5>Choose your download</h5>
                <ul class="media-list media-list-linked media-list-bordered">
                    <li>
                        <a href="#" class="media py-2 align-items-center" @click="download('image')">
                            <div class="mr-3">
                                <i class="icon-image2 icon-2x"></i>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-0">Image</h6>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="media py-2 align-items-center" @click="download('csv/txt')">
                            <div class="mr-3">
                                <i class="icon-file-text2 icon-2x"></i>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-0">CSV/TXT</h6>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="media py-2 align-items-center" @click="download('las')">
                            <div class="mr-3">
                                <i class="icon-file-zip icon-2x"></i>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-0">LAS</h6>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="media py-2 align-items-center" @click="download('laz')">
                            <div class="mr-3">
                                <i class="icon-file-zip icon-2x"></i>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-0">LAZ</h6>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="media py-2 align-items-center" @click="download('rar')">
                            <div class="mr-3">
                                <i class="icon-file-zip icon-2x"></i>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-0">RAR</h6>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="media py-2 align-items-center" @click="download('zip')">
                            <div class="mr-3">
                                <i class="icon-file-zip icon-2x"></i>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-0">ZIP</h6>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="media py-2 align-items-center" @click="download('gzip')">
                            <div class="mr-3">
                                <i class="icon-file-zip icon-2x"></i>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-0">GZIP</h6>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="media py-2 align-items-center" @click="download('7z')">
                            <div class="mr-3">
                                <i class="icon-file-zip icon-2x"></i>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-0">7Z</h6>
                            </div>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="media py-2 align-items-center" @click="download('html')">
                            <div class="mr-3">
                                <i class="icon-file-xml icon-2x"></i>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-0">HTML</h6>
                            </div>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
    </div>`,
    methods: {
        download: function (type) {
            var _this = this,
                api = '/app/point-cloud/download',
                data = { pointid: this.pointid, type: type }

            sendAjax(api, data, function (resp) {
                if (resp.status) {
                    _this.$emit('downloaded', resp.count_download)
                }
            });
        }
    }
})

Vue.component('radio-button', {
    props: ['value', 'selected', 'label'],
    template: `<div class="form-check form-check-inline">
        <label class="form-check-label">
            <div class="uniform-choice">
                <span :class="selected == value ? 'checked' : ''">
                    <input type="radio" class="form-check-input-styled" checked="" data-fouc="" @click="$emit('choose', value)">
                </span>
            </div>
            {{ label }}
        </label>
    </div>`
})

Vue.component('checkbox-button', {
    props: ['value', 'selected', 'label'],
    template: `<div class="form-check form-check-inline">
        <label class="form-check-label">
            <div class="uniform-checker">
                <span :class="selected.includes(value) ? 'checked' : ''">
                    <input type="checkbox" class="form-check-input-styled" checked="" data-fouc="" @click="toggleSelect">
                </span>
            </div>
            {{ label }}
        </label>
    </div>`,
    methods: {
        toggleSelect: function () {
            if (this.selected.includes(this.value)) {
                let index = this.selected.indexOf(this.value)
                this.selected.splice(index, 1)
            } else {
                this.selected.push(this.value)
            }
        }
    }
})

Vue.component('alert-modal', {
    props: ['message', 'url'],
    template: `<div class="modal fade" id="alert-modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body py-2">
                    <h6 class="warning-text">{{ message }}</h6>
                </div>
                <div class="modal-footer py-2 bg-light">
                    <a :href="url" class="btn btn-secondary">OK</a>
                </div>
            </div>
        </div>
    </div>`
})

Vue.component('map-picker', {
    props: {
        latitude: [String, Number],
        longitude: [String, Number]
    },
    data: function () {
        return {
            lat: this.latitude ? this.latitude : 16.047079,
            lng: this.longitude ? this.longitude : 108.206230,
            map: null,
            layers: {
                base: [],
                overlay: []
            },
            MARKER: null
        }
    },
    mounted: function () {
        this.init();
    },
    methods: {
        init: function () {
            this.map = L.map('gxmap_create_map', {
                minZoom: 1,
                maxZoom: 16,
            }).setView([this.lat, this.lng], 6);
            this.initControl();
            this.initExtends();
        },

        initControl: function () {
            this.initBaseLayer();
            this.initHstsLayer();
            this.initSearchPlaceControl();
        },

        initBaseLayer: function () {
            this.layers.base['World Dark Gray'] = L.tileLayer('http://server.arcgisonline.com/arcgis/rest/services/Canvas/World_Dark_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
                minZoom: 1,
                maxZoom: 16,
                attribution: 'World Dark Gray'
            }).addTo(this.map);
        },

        initHstsLayer: function () {
            this.layers.overlay['hstsLayer'] = L.tileLayer.wms('https://wmsv1.hcmgis.vn/geoserver/geodb/wms', {
                layers: 'layers=geodb:vietnam_hoangsa_truongsa_group',
                format: 'image/png',
                transparent: true,
                minZoom: 1,
                maxZoom: 20,
            }).addTo(this.map);
        },

        initExtends: function () {
            this.initDragMarker(null, false);
            this.initClickToMapEvent();
        },

        initSearchPlaceControl: function () {
            let _this = this;
            let placeControlId = '_searchplacecontrol';
            let placeControlItemsId = '_placecontrolitems';

            let placeControl = $('#' + placeControlId);
            let placeControlItems = $('#' + placeControlItemsId)
            placeControl.on('input', function (e) {
                $.ajax({
                    url: 'https://places.demo.api.here.com/places/v1/discover/search?app_id=zSfLmO4akpNNRkXp0CG9&app_code=Qx4lDVRUvipDhgpvpMjFFg&at=10.7974,106.7348&q=' + placeControl.val(),
                    success: function (e) {
                        let items = e.results.items;
                        placeControlResults = e.results;
                        placeControlItems.empty();
                        for (let i = 0; i < items.length; i++) {
                            let item = items[i];
                            let placeItemHtml = "<div class='place-item' style='padding: 5px; cursor: pointer' data-idx='" + i + "'>" + item.title + "</div>";
                            placeControlItems.append(placeItemHtml);
                        }
                        placeControlItems.on('click', '.place-item', function (event) {
                            let idx = $(this).attr('data-idx');
                            let item = placeControlResults.items[idx];
                            _this.map.panTo(item.position, 16);
                            if (_this.MARKER != undefined) {
                                _this.map.removeLayer(_this.MARKER);
                                _this.initDragMarker(item.position);
                            };
                            $('#' + placeControlItemsId).empty();
                            placeControl.val(this.innerHTML)
                        })
                    }
                })
            })
        },

        initDragMarker: function (coords, zoom = true) {
            coords = coords === null ? [this.lat, this.lng] : coords;
            var iconOption = L.icon({
                iconUrl: '/resources/images/marker_hcmgis.png',
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -20]
            });

            this.MARKER = L.marker(coords, {
                draggable: true,
                icon: iconOption
            }).bindPopup('<p>Move the marker or manually enter in the <b>Lat</b> and <b>Lng</b> below to update your point cloud coordinates</p>');
            this.MARKER.addTo(this.map);
            this.map.setView(coords, zoom ? 12 : 6);
            this.initBindingMarkerAndGeometryInput();
        },

        initClickToMapEvent: function () {
            this.map.on('click', function (e) {
                if (this.MARKER != undefined) {
                    this.map.removeLayer(this.MARKER);
                    this.initDragMarker(e.latlng, false);
                };
            })
        },

        onBlurLatLng: function () {
            let lat = $('#geom_lat').val();
            let lng = $('#geom_lng').val();

            this.initDragMarker([lat, lng]);
        },

        initBindingMarkerAndGeometryInput: function () {
            var ipLat = $('#geom_lat');
            var ipLng = $('#geom_lng');

            var latlng = this.MARKER.getLatLng();
            ipLat.val(latlng.lat);
            ipLng.val(latlng.lng);

            ipLat.on('change', function () {
                this.MARKER.setLatLng([ipLat.val(), ipLng.val()]);
            });

            ipLng.on('change', function () {
                this.MARKER.setLatLng([ipLat.val(), ipLng.val()]);
            })

            this.MARKER.on('dragend', function (e) {
                var latlng = e.target._latlng;
                ipLat.val(latlng.lat);
                ipLng.val(latlng.lng);
            })
        }
    },
    template: `
    <div class="gxmap_create_map_container h-100">
        <div class="row m-0 position-relative overflow-hidden" style="height: calc(100% - 70px); border-radius: .1875rem">
            <div id="gxmap_create_map" class="col-12 p-0 h-100" style="z-index: 99"></div>
            <div id="_placecontrolcontainer" class="position-absolute" style="width: 250px; top: 12px; left: 58px; z-index: 1000; background: #262d3c">
                <input id="_searchplacecontrol" placeholder="Enter the address.." class="form-control px-2" autocomplete="off">
                <div id="_placecontrolitems" class="place-items"></div>
            </div>
        </div>
        <div class="row form-group m-0 d-flex align-items-center" style="height: 70px">
            <div class="col-2 col-md-1 col-form-label">
                <span class="font-weight-semibold">Lat</span>
            </div>
            <div class="col-4 col-md-5">
                <input class="form-control" id="geom_lat" name="PointCloud[lat]" type="text" @blur="onBlurLatLng" />
            </div>
            <div class="col-2 col-md-1 col-form-label">
                <span class="font-weight-semibold">Long</span>
            </div>
            <div class="col-4 col-md-5">
                <input class="form-control" id="geom_lng" name="PointCloud[lng]" type="text" @blur="onBlurLatLng" />
            </div>
        </div>
    </div>`
})

Vue.component('map-builder', {
    props: {
        baselayers: [Array, Object],
        overlaylayers: [Array, Object],
        points: [Array, Object]
    },
    data: function () {
        return {
            map: null,
            simpleMapScreenshoter: null,
            layers: {
                base: {},
                overlay: {},
                hsts: null
            }
        }
    },
    mounted: function () {
        this.init();
    },
    methods: {
        init: function () {
            this.map = L.map('gxmap_create_map', {
                minZoom: 1,
                maxZoom: 16,
                zoomControl: false
            }).setView([31.4606, 20.7927], 2);
            this.initLayers();
            this.initControl();
        },

        initLayers: function () {
            this.initBaseLayer();
            this.initHstsLayer();
            this.initOverlayLayer();
            this.initPointCloudLayer();
            this.initScreenshoter();
        },

        initControl: function () {
            let _this = this;
            L.control.layers(_this.layers.base, _this.layers.overlay).addTo(_this.map);
            L.control.zoom({ position: 'topright' }).addTo(_this.map);
        },

        initBaseLayer: function () {
            this.layers.base['World Dark Gray'] = L.tileLayer('https://server.arcgisonline.com/arcgis/rest/services/Canvas/World_Dark_Gray_Base/MapServer/tile/{z}/{y}/{x}', {
                minZoom: 1,
                maxZoom: 16,
                attribution: 'World Dark Gray'
            }).addTo(this.map);
        },

        initOverlayLayer: function () {
            var _this = this, zoomLevel = 0;
            _this.layers.hsts = L.featureGroup();
            zoomLevel = _this.map.getZoom();

            if(zoomLevel > 3) _this.map.addLayer(_this.layers.hsts);
            _this.map.on('zoomend', function() {
                zoomLevel = _this.map.getZoom();
                if (zoomLevel > 3){
                    if(!_this.map.hasLayer(_this.layers.hsts)) _this.map.addLayer(_this.layers.hsts);
                } else {
                    if(_this.map.hasLayer(_this.layers.hsts)) _this.map.removeLayer(_this.layers.hsts);
                }
            });
        },

        initScreenshoter: function() {
            this.simpleMapScreenshoter = L.simpleMapScreenshoter({position: 'bottomright'}).addTo(this.map);
        },

        initPointCloudLayer: function () {
            var _this = this;
            if (_this.layers.overlay['Point cloud'] == undefined) {
                _this.layers.overlay['Point cloud'] = L.featureGroup().addTo(_this.map);
            }
            _this.points.forEach(function (p, index) {
                var marker = L.circleMarker([p.lat, p.lng], {
                    radius: 7,
                    weight: 2,
                    color: '#2196F3',
                    fillOpacity: 0.5
                });
                marker.bindPopup(_this.contentImagePopup(p));
                _this.layers.overlay['Point cloud'].addLayer(marker);
            })

        },

        initHstsLayer: function () {
            var _this = this,
                api = '/app/map/get-hoang-sa-truong-sa-layer';

            sendAjax(api, {}, (resp) => {
                if(resp.status) {
                    L.geoJSON(resp.geojson, {
                        style: (feature) => {
                            return {
                                'color': '#414143',
                                'weight': 1,
                                'fillOpacity': 0
                            }
                        },
                        onEachFeature: (feature, layer) => {
                            L.marker(layer.getBounds().getCenter(), {
                                icon: L.divIcon({
                                    className: "labelHSTS",
                                    html: feature.properties.id == "576" ? "Trường Sa" : "Hoàng Sa",
                                    iconSize: [100, 20]
                                })
                            }).addTo(_this.layers.hsts);
                        }
                    }).addTo(_this.map);
                }
            });
        },

        updatePointCloudLayer: function (points) {
            var _this = this;
            _this.points = points;
            _this.layers.overlay['Point cloud'].clearLayers();
            _this.initPointCloudLayer();
        },

        contentImagePopup: function (data) {
            var created_at = formatTime(data.created_at);
            var html = `
            <div class="d-flex flex-column align-items-center" style="background: #262d3c">
                <a href="/app/point-cloud/detail/${data.slug}">
                    <h5 class="mb-0 font-weight-bold text-white">${data.title}</h5>
                </a>
                <p class="text-muted mt-1 mb-2">
                    Published on ${created_at} by <a href="/app/user/point-cloud/${data.author_slug}" class="text-white">${data.author}</a>
                </p>
                <a href="/app/point-cloud/detail/${data.slug}">
                    <img src="/uploads/${data.thumbnail}" style="width: 270px; height: 170px; object-fit:cover">
                </a>
            </div>`
            return html;
        },

        toCenterPointcloudLayer: function() {
            if(this.points.length > 0) {
                var bounds = this.layers.overlay['Point cloud'].getBounds();
                this.map.fitBounds(bounds, {padding: [50, 50]});
            }
        },

        takeScreen: function() {
            var _this = this;
            _this.simpleMapScreenshoter = L.simpleMapScreenshoter().addTo(_this.map);
            var format = 'image';
            var overridedPluginOptions = {
                mimeType: 'image/jpeg'
            };
            var imgBase64;
            _this.simpleMapScreenshoter.takeScreen(format, overridedPluginOptions).then(image => {
                imgBase64 = image;
            }).catch(e => {
                console.error(e)
            });
            return imgBase64;
        }
    },
    template: `
    <div class="gxmap_create_map_container h-100" id="gxmap_create_map_container">
        <div id="gxmap_create_map" class="h-100" style="z-index: 99"></div>
    </div>`
})