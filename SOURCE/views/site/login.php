<?php

use app\assets\AppAsset;
use app\modules\contrib\gxassets\GxLimitlessTemplateAsset;
use yii\bootstrap\ActiveForm;
use yii\authclient\widgets\AuthChoice;

$this->title = 'Login';

GxLimitlessTemplateAsset::register($this);
AppAsset::register($this);
?>

<style>
    .navbar,
    footer {
        display: none !important;
    }
</style>

<div class="content d-flex justify-content-center align-items-center page-login flex-column">
    <?php if($referrer == 'register'): ?>
        <div class="alert alert-primary border-0">
            <?= Yii::t('app', 'Register account successfully. We have sent a confirmation email. Please check and follow the instructions to confirm your registration.') ?>
        </div>
    <?php elseif($referrer == 'confirm-email'): ?>
        <div class="alert alert-primary border-0">
        <?= Yii::t('app', 'Congratulations on your successful registration. You can login and experience HCMGIS PointCloud now.') ?>
        </div>
    <?php endif; ?>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
    ]); ?>
    <div class="card card-body login-form border-top-primary" style="width: 364px;">
        <div class="text-center">
            <a href="<?= Yii::$app->homeUrl ?>" class="mb-2 d-block">
                <img src="<?= Yii::$app->homeUrl ?>resources/images/logo.png" style="max-width: 120px">
            </a>
            <h4 class="font-weight-bold text-uppercase mb-1">HCMGIS 3DVIEWER</h4>
            <h5 class="font-weight-bold text-uppercase">Login</h5>
        </div>

        <div class="form-group text-left">
            <?= $form->field($model, 'username')->textInput(['placeholder' => 'Email'])->label(false) ?>
        </div>

        <div class="form-group text-left">
            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Password'])->label(false) ?>
        </div>

        <div class="form-group d-flex align-items-center">
            <a href="<?= Yii::$app->homeUrl . 'site/forgot-password' ?>" class="ml-auto">Forgot password?</a>
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block text-uppercase font-weight-bold">ACCESS<i class="icon-circle-right2 ml-2"></i></button>
        </div>

        <div class="form-group text-center text-muted content-divider">
            <span class="px-2">OR</span>
        </div>

        <div class="form-group text-center">
            <?php $authChoice = AuthChoice::begin([
                'baseAuthUrl' => ['site/auth']
            ]); ?>
            <?php foreach($authChoice->getClients() as $client) :?>
                    <a href="<?= $authChoice->createClientUrl($client) ?>" 
                        class="btn btn-icon rounded-round border-2 mx-1 <?= $client->getName() === 'google' ? 'btn-outline-danger' : 'btn-outline-primary' ?>"
                        data-popup-width="800" data-popup-height="500">
                        <i class="<?= $client->getName() === 'google' ? 'icon-google' : 'icon-facebook' ?>"></i>
                    </a>
            <?php endforeach; ?>
            <?php AuthChoice::end() ?>
        </div>

        <div class="content-group">
            <div class="text-center">
                <p class="display-block">You have no account? <a href="register" class="font-weight-bold">Register</a></p>
            </div>
        </div>

        <h6 class="help-block text-center no-margin"> © 2020 HCMGIS</h6>
    </div>
    <?php ActiveForm::end(); ?>
</div>