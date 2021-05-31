<?php

use app\modules\cms\CMSConfig;
use app\modules\contrib\auth\AuthService;
?>
<div class="navbar navbar-expand-md navbar-dark bg-teal navbar-static">
    <div class="navbar-brand">
        <a class="sitename" href="<?= Yii::$app->homeUrl ?>">
            <span class=''><img src='<?= Yii::$app->homeUrl ?>resources/images/logo-site.png'/><?= CMSConfig::$CONFIG['siteName'] ?></span>
        </a>
    </div>
    <div class="d-md-none">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile">
            <i class="icon-tree5"></i>
        </button>
        <button class="navbar-toggler sidebar-mobile-main-toggle" type="button">
            <i class="icon-paragraph-justify3"></i>
        </button>
    </div>
    <div class="collapse navbar-collapse" id="navbar-mobile">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a href="#" class="navbar-nav-link sidebar-control sidebar-main-toggle d-none d-md-block legitRipple">
                    <i class="icon-paragraph-justify3"></i>
                </a>
            </li>
        </ul>

<!--        <span class="navbar-text ml-md-3">-->
<!--            <span class="badge badge-mark border-orange-300 mr-2"></span>-->
<!--        </span>-->

        <ul class="navbar-nav ml-md-auto align-items-center">
            <li class="nav-item ml-3">
                <span>
                    Xin chào, <a href="<?= Yii::$app->homeUrl . 'contrib/auth/auth-user/profile' ?>" class="text-white"><?= AuthService::UserFullName()?></a>
                </span>
                <span class="ml-3">
                    <a href="<?= Yii::$app->homeUrl ?>site/logout" title="Đăng xuất" class="text-white"><i
                                class="icon-switch"></i></a>
                </span>
            </li>
        </ul>
    </div>
</div>