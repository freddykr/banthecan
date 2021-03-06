<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel common\models\ResolutionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Resolutions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="resolution-index">

    <h1><?= Html::encode($this->title) ?></h1>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'title',
                'format' => 'ntext',
                'label' => \Yii::t('app', 'Title'),
            ],
            [
                'attribute' => 'description',
                'format' => 'ntext',
                'label' => \Yii::t('app', 'Description'),
            ],
            [
                'attribute' => 'ticket.title',
                'format' => 'ntext',
                'label' => \Yii::t('app', 'Ticket'),
            ],
            [
                'format' => 'raw',
                'label' => \Yii::t('app', 'Created By'),
                'content' => function ($model, $key, $index, $column) {
                    return $this->render('@frontend/views/user/partials/_blame', [
                            'model' => $model,
                        ]
                    );
                },
            ],
            [
                'format' => 'raw',
                'label' => \Yii::t('app', 'Updated By'),
                'content' => function ($model, $key, $index, $column) {
                    return $this->render('@frontend/views/user/partials/_blame', [
                            'model' => $model,
                            'useUpdated' => true                        ]
                    );
                },
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
