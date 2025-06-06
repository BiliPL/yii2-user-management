<?php
/**
 * @var $this yii\web\View
 * @var $user bilipl\modules\UserManagement\models\User
 */
use yii\helpers\Html;

?>
<?php
$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['/user-management/auth/confirm-email-receive', 'token' => $user->confirmation_token]);
?>

Hello <?= Html::encode($user->username) ?>, follow this link to confirm your email:

<?= Html::a('Confirm E-mail', $resetLink) ?>