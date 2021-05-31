<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\AppAsset;
use app\modules\cms\CMSConfig;
use app\modules\contrib\gxassets\GxLimitlessTemplateAsset;
use app\modules\contrib\gxassets\GxVueAsset;
use app\modules\contrib\widgets\FlashMessageWidget;

GxLimitlessTemplateAsset::register($this);
AppAsset::register($this);
GxVueAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <link rel="shortcut icon" href="<?= Yii::$app->homeUrl ?>resources/images/favicon.ico" type="image/x-icon">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= CMSConfig::$CONFIG['siteName'] ?></title>
    <?php $this->head() ?>
    <script>
        var APP = {};
        new WOW().init();
    </script>
</head>

<body cz-shortcut-listen="true">
    <?php $this->beginBody() ?>
    <?= $this->render('header') ?>
    <div class="page-content">
    <?= $this->render('sidebar') ?>
        <div class="content-wrapper">
            <?= FlashMessageWidget::widget() ?>
            <?= $content ?>
        </div>
    </div>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage(); ?>