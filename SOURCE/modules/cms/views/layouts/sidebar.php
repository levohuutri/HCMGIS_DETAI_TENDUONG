<?php

use app\modules\cms\CMSConfig;

$adminSidebar = CMSConfig::$CONFIG['adminSidebar'];
?>
<div class="sidebar sidebar-light sidebar-main sidebar-expand-md">
    <div class="sidebar-mobile-toggler text-center">
        <a href="#" class="sidebar-mobile-main-toggle">
            <i class="icon-arrow-left8"></i>
        </a>
        Điều hướng
        <a href="#" class="sidebar-mobile-expand">
            <i class="icon-screen-full"></i>
            <i class="icon-screen-normal"></i>
        </a>
    </div>
    <div class="sidebar-content">
        <div class="card card-sidebar-mobile">
            <ul class="nav nav-sidebar" data-nav-type="accordion">
                <li class="nav-item-header">
                    <div class="text-uppercase font-size-xs line-height-xs">Quản trị</div> <i class="icon-menu" title="Quản trị"></i>
                </li>
                <?php foreach ($adminSidebar as $key => $nav) : ?>
                    <?php if (isset($nav['children'])) : ?>
                        <li class="nav-item nav-item-submenu nav-item-open">
                            <a class='nav-link legitRipple'><i class="<?= $nav['icon'] ?>"></i> <span><?= $nav['name'] ?></span></a>
                            <ul class="nav nav-group-sub" style="display: block;">
                                <?php foreach ($nav['children'] as $navchild) : ?>
                                    <li class="nav-item"><a class='nav-link legitRipple' href="<?= Yii::$app->homeUrl ?><?= $navchild['url'] ?>"><i class="<?= $navchild['icon'] ?>"></i> <span><?= $navchild['name'] ?></span></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php else : ?>
                        <li class="nav-item"><a class='nav-link legitRipple' href="<?= Yii::$app->homeUrl ?><?= $nav['url'] ?>"><i class="<?= $nav['icon'] ?>"></i> <span><?= $nav['name'] ?></span></a></li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>