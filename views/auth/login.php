<?php
/**
 * @var $this yii\web\View
 * @var $model bilipl\modules\UserManagement\models\forms\LoginForm
 */

use bilipl\modules\UserManagement\components\GhostHtml;
use bilipl\modules\UserManagement\UserManagementModule;
use yii\bootstrap5\ActiveForm;
use yii\helpers\Html;
?>

    <div class="container" id="login-wrapper">
        <div class="row justify-content-center"> <!-- Dodano `justify-content-center` -->
            <div class="col-md-4 mx-auto"> <!-- Zmieniono `col-md-offset-4` na `mx-auto` -->
                <div class="card"> <!-- Zmieniono `panel panel-default` -->
                    <div class="card-header"> <!-- Zmieniono `panel-heading` -->
                        <h3 class="card-title"><?= UserManagementModule::t('front', 'Authorization') ?></h3>
                    </div>
                    <div class="card-body"> <!-- Zmieniono `panel-body` -->

					<?php $form = ActiveForm::begin([
						'id'      => 'login-form',
						'options'=>['autocomplete'=>'off'],
						'validateOnBlur'=>false,
						'fieldConfig' => [
							'template'=>"{input}\n{error}",
						],
					]) ?>

					<?= $form->field($model, 'username')
						->textInput(['placeholder'=>$model->getAttributeLabel('username'), 'autocomplete'=>'off']) ?>

					<?= $form->field($model, 'password')
						->passwordInput(['placeholder'=>$model->getAttributeLabel('password'), 'autocomplete'=>'off']) ?>

					<?= (isset(Yii::$app->user->enableAutoLogin) && Yii::$app->user->enableAutoLogin) ? $form->field($model, 'rememberMe')->checkbox(['value'=>true]) : '' ?>

                        <?= Html::submitButton(
                            UserManagementModule::t('front', 'Login'),
                            ['class' => 'btn btn-lg btn-primary w-100'] // Zmieniono `btn-block` na `w-100`
                        ) ?>


                        <div class="row registration-block">
						<div class="col-6">
							<?= GhostHtml::a(
								UserManagementModule::t('front', "Registration"),
								['/user-management/auth/registration']
							) ?>
						</div>
						<div class="col-6 text-end">
							<?= GhostHtml::a(
								UserManagementModule::t('front', "Forgot password ?"),
								['/user-management/auth/password-recovery']
							) ?>
						</div>
					</div>




					<?php ActiveForm::end() ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$css = <<<CSS
html, body {
	background: #eee;
	box-shadow: inset 0 0 50px rgba(0, 0, 0, 0.3);
	height: 100%;
}
#login-wrapper {
	position: relative;
	top: 30%;
}
#login-wrapper .registration-block {
	margin-top: 15px;
}
CSS;

$this->registerCss($css);
?>