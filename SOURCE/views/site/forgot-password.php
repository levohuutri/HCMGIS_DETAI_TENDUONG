<?php
use kartik\form\ActiveForm;
use app\modules\contrib\gxassets\GxLaddaAsset;

GxLaddaAsset::register($this);
?>

<style>
    .navbar,
    footer {
        display: none !important;
    }
</style>

<div class="content d-flex justify-content-center align-items-center forgot-password-page" id="forgot-password-page">
    <?php $form = ActiveForm::begin([
        'id' => 'forgot-password-form',
    ]); ?>
    <div class="card card-body login-form" style="width: 344px;">
        <div class="text-center">
            <div class="mb-3">
                <img src="<?= Yii::$app->homeUrl ?>resources/images/logo.png" style="max-width: 120px">
            </div>
            <h4 class="font-weight-bold text-uppercase mb-1">HCMGIS 3DVIEWER</h4>
            <h5 class="font-weight-bold text-uppercase">FORGOT PASSWORD</h5>
        </div>
        <div class="text-center forgot-password">
            <div class="form-group my-3">
                <h6 class="text-muted">Please enter the email you have registered with the system. We will send instructions to reset your password to your email</h6>
                <div class="form-group form-group-feedback form-group-feedback-right">
                    <input type="email" name="email" class="form-control px-1" placeholder="Email">
                    <div class="form-control-feedback">
                        <i class="icon-mail5 text-muted"></i>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-primary btn-block ladda-button text-uppercase" id="btn-forgot-password" @click="confirmEmail"><i class="icon-spinner11 mr-2"></i> Submit</button>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(function() {
        var vm = new Vue({
            el: '#forgot-password-page',
            data: {},
            methods: {
                confirmEmail: function(e) {
                    e.preventDefault();
                    var ladda = Ladda.create($('#btn-forgot-password')[0]);
                    ladda.start();
                    $.ajax({
                        data: $('#forgot-password-form').serialize(),
                        type: 'POST',
                        success: function(resp) {
                            if (resp.status) {
                                toastMessage('success', resp.message);
                            } else {
                                toastMessage('error', resp.message);
                            }
                            ladda.stop();
                        },
                        error: function(msg) {
                            console.log(msg);
                            ladda.stop();
                        }
                    });
                }
            }
        })

        $('#forgot-password-form').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>