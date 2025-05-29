<?php
namespace bilipl\modules\UserManagement\controllers;
use Yii;
use bilipl\modules\UserManagement\models\UserVisitLog;
use bilipl\modules\UserManagement\models\search\UserVisitLogSearch;
use bilipl\modules\UserManagement\components\AdminDefaultController;

/**
 * UserVisitLogController implements the CRUD actions for UserVisitLog model.
 */
class UserVisitLogController extends AdminDefaultController
{
	/**
	 * @var UserVisitLog
	 */
	public $modelClass = 'bilipl\modules\UserManagement\models\UserVisitLog';

	/**
	 * @var UserVisitLogSearch
	 */
	public $modelSearchClass = 'bilipl\modules\UserManagement\models\search\UserVisitLogSearch';

	public $enableOnlyActions = ['index', 'view', 'grid-page-size'];
}
