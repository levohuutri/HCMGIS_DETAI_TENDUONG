<?php
use app\modules\cms\CMSConfig;
use app\modules\contrib\auth\AuthService;

$userSidebarNav = CMSConfig::$CONFIG['userSidebarNav'];
?>

<div class="sidebar sidebar-light sidebar-main sidebar-expand-md">
        <div class="sidebar-mobile-toggler text-center">
				<a href="#" class="sidebar-mobile-main-toggle">
					<i class="icon-arrow-left8"></i>
				</a>
				<span class="font-weight-semibold">Điều hướng</span>
				<a href="#" class="sidebar-mobile-expand">
					<i class="icon-screen-full"></i>
					<i class="icon-screen-normal"></i>
				</a>
			</div>
    <div class="sidebar-content">

        <!-- User menu -->
        <div class="sidebar-user-material">
            <div class="sidebar-user-material-body" style="background: url(<?= Yii::$app->homeUrl ?>resources/images/backgrounds/user_bg3.jpg)">
                <div class="card-body text-center">
                    <a href="<?= CMSConfig::getUrl('user/dashboard')?>">
                        <img src="<?= Yii::$app->homeUrl ?>resources/images/image.png" class="img-fluid rounded-circle shadow-1 mb-3" width="80" height="80" alt="">
                    </a>
                    <h6 class="mb-0 text-white text-shadow-dark"><?= AuthService::UserFullName()?></h6>
                </div>
                                            
                <div class="sidebar-user-material-footer">
                    <a href="#user-nav" class="d-flex justify-content-between align-items-center text-shadow-dark dropdown-toggle legitRipple" data-toggle="collapse"><span>Tài khoản của tôi</span></a>
                </div>
            </div>
            <div class="collapse" id="user-nav" style="">
                <ul class="nav nav-sidebar">
                    <li class="nav-item">
                        <a href="<?= Yii::$app->homeUrl . 'contrib/auth/auth-user/profile' ?>" class="nav-link legitRipple">
                            <i class="icon-user"></i>
                            <span>Quản lý tài khoản</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= Yii::$app->homeUrl ?>site/logout" class="nav-link legitRipple">
                            <i class="icon-switch"></i>
                            <span>Đăng xuất</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /user menu -->


        <!-- Main navigation -->
        <div class="card card-sidebar-mobile">
            <ul class="nav nav-sidebar" data-nav-type="accordion">

                <!-- Main -->
                <li class="nav-item-header"><span>Trang chủ</span> <i class="icon-menu" title="Main pages"></i></li>
                <?php foreach( $userSidebarNav as $key => $nav ): ?>
                    <?php if (isset($nav['children'])): ?>
                        <li class="nav-item nav-item-submenu nav-item-open">
                            <a class='nav-link legitRipple'><i class="<?= $nav['icon'] ?>"></i> <span><?= $nav['name'] ?></span></a>
                            <ul class="nav nav-group-sub" style="display: block;">
                                <?php foreach( $nav['children'] as $navchild ): ?>
                                    <li class="nav-item"><a class='nav-link legitRipple' href="<?= Yii::$app->homeUrl ?><?= $navchild['url'] ?>"><i class="<?= $navchild['icon'] ?>"></i> <span><?= $navchild['name'] ?></span></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class='nav-link legitRipple' href="<?= Yii::$app->homeUrl ?><?= $nav['url'] ?>"><i class="<?= $nav['icon'] ?>"></i> <span><?= $nav['name'] ?></span></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
                <!-- /main -->

                <?php if(AuthService::IsAdmin()) :?>
                <!--Admin-->
                <li class="nav-item-header"><span>Quản lý người dùng</span> <i class="icon-menu" title="Quản lý người dùng"></i></li>
                <li class="nav-item"><a class='nav-link legitRipple' href="#"><i class="icon-user-plus"></i> <span>Thêm mới tài khoản</span></a></li>

                <?php endif; ?>

<!--                <li class="nav-item-header"><span>Tài liệu</span> <i class="icon-book2" title="Tài liệu"></i></li>-->
<!--                <li class="nav-item"><a class='nav-link legitRipple' href="#"><i class="icon-file-pdf"></i> <span>Hướng dẫn sử dụng</span></a></li>-->
<!--                <li class="nav-item-header"><span>Mở rộng</span> <i class="icon-book2" title="Mở rộng"></i></li>-->
<!--                <li class="nav-item"><a class='nav-link legitRipple' href="#"><i class="icon-envelop2"></i> <span>Góp ý, phản hồi</span></a></li>-->

            </ul>
        </div>
        <!-- /main navigation -->

    </div>
</div>