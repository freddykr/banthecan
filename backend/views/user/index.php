<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php //echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create User'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\ActionColumn'],
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            'username:ntext',
            'created_at:datetime',
            'updated_at:datetime',
            //'password_hash:ntext',
            //'password_reset_token:ntext',
            'email:ntext',
            // 'auth_key:ntext',
             'statusText:ntext:Status',
            // 'password:ntext',
             'boardNames:ntext:Boards',

        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
