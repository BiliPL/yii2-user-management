<?php
use bilipl\modules\UserManagement\gridbulkactions\GridBulkActions;
use bilipl\modules\UserManagement\gridpagesize\GridPageSize;
use bilipl\modules\UserManagement\components\GhostHtml;
use bilipl\modules\UserManagement\models\rbacDB\AuthItemGroup;
use bilipl\modules\UserManagement\models\rbacDB\Permission;
use bilipl\modules\UserManagement\UserManagementModule;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

/**
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var bilipl\modules\UserManagement\models\rbacDB\search\PermissionSearch $searchModel
 * @var yii\web\View $this
 */

$this->title = UserManagementModule::t('back', 'Permissions');
$this->params['breadcrumbs'][] = $this->title;

?>

<h2 class="lte-hide-title"><?= $this->title ?></h2>

<div class="card"> <!-- Zamiana panel na card -->
    <div class="card-body"> <!-- Zamiana panel-body na card-body -->
        <div class="row">
            <div class="col-md-6">
                <p>
                    <?= GhostHtml::a(
                        '<span class="bi bi-plus-circle"></span> ' . UserManagementModule::t('back', 'Create'),
                        ['create'],
                        ['class' => 'btn btn-success']
                    ) ?>
                </p>
            </div>

            <div class="col-md-6 text-end"> <!-- Zamiana text-right na text-end -->
                <?= GridPageSize::widget(['pjaxId' => 'permission-grid-pjax']) ?>
            </div>
        </div>

        <?php Pjax::begin([
            'id' => 'permission-grid-pjax',
        ]) ?>

        <?= GridView::widget([
            'id' => 'permission-grid',
            'dataProvider' => $dataProvider,
            'pager' => [
                'options' => ['class' => 'pagination pagination-sm'],
                'hideOnSinglePage' => true,
                'lastPageLabel' => '>>',
                'firstPageLabel' => '<<',
            ],
            'filterModel' => $searchModel,
            'layout' => '{items}
                <div class="row">
                    <div class="col-md-8">{pager}</div>
                    <div class="col-md-4 text-end">{summary}' . GridBulkActions::widget([
                    'gridId' => 'permission-grid',
                    'actions' => [Url::to(['bulk-delete']) => GridBulkActions::t('app', 'Delete')],
                ]) . '</div>
                </div>',
            'columns' => [
                ['class' => 'yii\grid\SerialColumn', 'options' => ['style' => 'width:10px']],

                [
                    'attribute' => 'description',
                    'value' => function ($model) {
                        if ($model->name == Yii::$app->getModule('user-management')->commonPermissionName) {
                            return Html::a(
                                $model->description,
                                ['view', 'id' => $model->name],
                                ['data-pjax' => 0, 'class' => 'badge bg-primary'] // Zamiana label na badge w Bootstrap 5
                            );
                        } else {
                            return Html::a($model->description, ['view', 'id' => $model->name], ['data-pjax' => 0]);
                        }
                    },
                    'format' => 'raw',
                ],
                'name',
                [
                    'attribute' => 'group_code',
                    'filter' => ArrayHelper::map(AuthItemGroup::find()->asArray()->all(), 'code', 'name'),
                    'value' => function (Permission $model) {
                        return $model->group_code ? $model->group->name : '';
                    },
                ],
                ['class' => 'yii\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => ['style' => 'width:70px; text-align:center;'],
                ],
            ],
        ]) ?>

        <?php Pjax::end() ?>
    </div>
</div>