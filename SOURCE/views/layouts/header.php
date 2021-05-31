<?php

/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 04-Mar-19
 * Time: 2:54 PM
 */
?>
<?php

use app\modules\app\APPConfig;
use app\modules\cms\CMSConfig;
use app\modules\cms\services\AuthService;
use app\modules\contrib\notifications\widgets\NotificationWidget;

?>
<div class="navbar navbar-expand-md navbar-light navbar-static">
    <div class="navbar-brand p-2 wmin-250">
        <a class="sitename d-flex align-items-center" href="<?= Yii::$app->homeUrl ?>">
            <img src="<?= Yii::$app->homeUrl . 'resources/images/logo.png' ?>" alt="">
            <span class="ml-1 font-weight-bold text-white">ĐỀ TÀI ĐẶT ĐỔI TÊN ĐƯỜNG</span>
        </a>
    </div>
    <div>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar-mobile" aria-expanded="true" id="navbar-toggle">
            <i class="icon-menu7"></i>
        </button>
    </div>
    <div class="collapse navbar-collapse" id="navbar-mobile">
        <ul class="navbar-nav main-menu">
            <li class="nav-item">
                <a href="<?= APPConfig::getUrl('map-search') ?>" class="navbar-nav-link">
                    <h6 class="mb-0 font-weight-bold"><i class="icon-home mr-2"></i><?= Yii::t('app', 'Trang chủ') ?></h6>
                </a>
            </li>
            <!-- <li class="nav-item">
                <a href="<?= APPConfig::getUrl('point-cloud/map') ?>" class="navbar-nav-link">
                    <h6 class="mb-0 font-weight-bold"><?= Yii::t('app', 'L') ?></h6>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= APPConfig::getUrl('map') ?>" class="navbar-nav-link">
                    <h6 class="mb-0 font-weight-bold"><?= Yii::t('app', 'Custom maps') ?></h6>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= APPConfig::getUrl('point-cloud/upload') ?>" class="navbar-nav-link">
                    <h6 class="mb-0 font-weight-bold"><?= Yii::t('app', 'Upload') ?></h6>
                </a>
            </li> -->
            <?php if (AuthService::IsAdmin()) : ?>
            <li class="nav-item">
                <a href="<?= CMSConfig::getUrl('user') ?>" class="navbar-nav-link">
                    <h6 class="mb-0 font-weight-bold"><?= Yii::t('app', 'Admin') ?></h6>
                </a>
            </li>
            <?php endif; ?>
        </ul>
        <ul class="navbar-nav ml-md-auto">
            <?php if (Yii::$app->user->isGuest) : ?>
                <li class="nav-item">
                    <a href="<?= Yii::$app->homeUrl . "site/login" ?>" class="navbar-nav-link">
                        <h6 class="mb-0 font-weight-bold"><?= Yii::t('app', 'Login') ?></h6>
                    </a>
                </li>
                <li>
                    <a href="<?= Yii::$app->homeUrl . "site/register" ?>" class="navbar-nav-link">
                        <h6 class="mb-0 font-weight-bold"><?= Yii::t('app', 'Register') ?></h6>
                    </a>
                </li>
            <?php else : ?>
                <?= NotificationWidget::widget() ?>
                <li class="nav-item dropdown">
                    <a href="#" class="navbar-nav-link d-flex align-items-center dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                        <h6 class="mb-0 font-weight-bold"><?= AuthService::UserFullName() ?></h6>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right" style="z-index: 102">
                        <a href="<?= APPConfig::getUrl('user/my-point-cloud') ?>" class="dropdown-item"><i class="icon-grid4"></i> <?= Yii::t('app', 'My point clouds') ?></a>
                        <div class="dropdown-divider my-0"></div>
                        <a href="<?= APPConfig::getUrl('user/my-map') ?>" class="dropdown-item"><i class="icon-map4"></i> <?= Yii::t('app', 'My maps') ?></a>
                        <div class="dropdown-divider my-0"></div>
                        <a href="<?= APPConfig::getUrl('user/my-profile') ?>" class="dropdown-item"><i class="icon-user"></i> <?= Yii::t('app', 'My profile') ?></a>
                        <div class="dropdown-divider my-0"></div>
                        <a href="<?= Yii::$app->homeUrl ?>site/logout" class="dropdown-item"><i class="icon-switch2"></i> <?= Yii::t('app', 'Logout') ?></a>
                    </div>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</div>