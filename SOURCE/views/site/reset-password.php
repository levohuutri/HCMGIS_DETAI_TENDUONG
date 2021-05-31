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

<div class="content d-flex justify-content-center align-items-center reset-password-page" id="reset-password-page">
    <?php $form = ActiveForm::begin([
        'id' => 'reset-password-form',
    ]); ?>
    <div class="card card-body login-form" style="width: 344px;">
        <div class="text-center">
            <div class="mb-3">
                <img src="<?= Yii::$app->homeUrl ?>resources/images/logo.png" style="max-width: 120px">
            </div>
            <h4 class="font-weight-bold text-uppercase mb-1">HCMGIS 3DVIEWER</h4>
            <h5 class="font-weight-bold text-uppercase">RESET PASSWORD</h5>
        </div>
        <div class="text-center reset-password">
            <div class="form-group my-3">
                <h6 class="text-muted">Enter and confirm your new password</h6>
                <div class="form-group form-group-feedback form-group-feedback-right">
                    <input type="password" name="AuthUser[password]" class="form-control px-1" placeholder="Password">
                </div>
                <div class="form-group form-group-feedback form-group-feedback-right">
                    <input type="password" name="AuthUser[cpassword]" class="form-control px-1" placeholder="Confirm Password">
                </div>
            </div>
            <div class="form-group">
                <input type="hidden" name="token" value="<?= $token ?>">
                <input type="hidden" name="auth" value="<?= $auth ?>">
                <button type="button" class="btn btn-primary btn-block ladda-button text-uppercase" id="btn-reset-password" @click="resetPassword"><i class="icon-spinner11 mr-2"></i> Submit</button>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script>
    $(function(){
        var vm = new Vue({
            el: '#reset-password-form',
            data: {},
            methods: {
                resetPassword: function(e) {
                    e.preventDefault();
                    var api = '<?= Yii::$app->homeUrl . 'site/set-new-password' ?>',
                        data = $('#reset-password-form').serialize(),
                        ladda = Ladda.create($('#btn-reset-password')[0]);

                    ladda.start();
                    sendAjax(api, data, (resp) => {
                        if (resp.status) {
                            window.location.assign('<?= Yii::$app->homeUrl . 'site/login' ?>');
                        } else {
                            toastMessage('error', resp.message);
                        }
                        ladda.stop();
                    }, 'POST');
                }
            }
        })

        $('#reset-password-form').on('keyup keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
    })
</script>