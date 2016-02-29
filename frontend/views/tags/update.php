<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Tags */

$this->title = \Yii::t('app', 'Update Tag');
$this->params['breadcrumbs'][] = ['label' => \Yii::t('app', 'Tags'), 'url' => ['index']];
?>

<div class="tags-update">

    <h1><?= Html::encode($this->title) ?></h1>

<?php
    echo $this->render('partials/_form', [
        'model' => $model,
    ]);
?>
</div>
