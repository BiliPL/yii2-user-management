<?php
namespace bilipl\modules\UserManagement\controllers;
use bilipl\modules\UserManagement\components\AdminDefaultController;
use Yii;
use bilipl\modules\UserManagement\models\User;
use bilipl\modules\UserManagement\models\search\UserSearch;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends AdminDefaultController
{
	/**
	 * @var User
	 */
	public $modelClass = 'bilipl\modules\UserManagement\models\User';

	/**
	 * @var UserSearch
	 */
	public $modelSearchClass = 'bilipl\modules\UserManagement\models\search\UserSearch';

	/**
	 * @return mixed|string|\yii\web\Response
	 */
	public function actionCreate()
	{
		$model = new User(['scenario'=>'newUser']);

		if ( $model->load(Yii::$app->request->post()) && $model->save() )
		{
			return $this->redirect(['view',	'id' => $model->id]);
		}

		return $this->renderIsAjax('create', compact('model'));
	}

	/**
	 * @param int $id User ID
	 *
	 * @throws \yii\web\NotFoundHttpException
	 * @return string
	 */
	public function actionChangePassword($id)
	{
		$model = User::findOne($id);

		if ( !$model )
		{
			throw new NotFoundHttpException('User not found');
		}

		$model->scenario = 'changePassword';

		if ( $model->load(Yii::$app->request->post()) && $model->save() )
		{
			return $this->redirect(['view',	'id' => $model->id]);
		}

		return $this->renderIsAjax('changePassword', compact('model'));
	}

}
