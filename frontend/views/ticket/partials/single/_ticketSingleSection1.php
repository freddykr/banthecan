<?php

use yii\helpers\Html;

//Ticket Decoration Bar displays the Ticket decorations
/* @var $this yii\web\View */
/* @var $model common\models\Ticket */
/* @var $showVote boolean */
?>

<?php
    echo $this->render('@frontend/views/user/partials/_blame', [
        'model' => $model,
        'useUpdated' => true,
        'alignRight' => true,
        'textBelow' => true,
        'showName' => false,
        'dateFormat' => 'php:d.m'
        ]
    );
?>

<?php $showAvatar = ($model->created_by !== $model->updated_by); ?>

<?php
    echo $this->render('@frontend/views/user/partials/_blame', [
        'model' => $model,
        'textBelow' => true,
        'showName' => false,
        'showAvatar' => $showAvatar,
        'dateFormat' => 'php:d.m'
        ]
    );
?>

<?php
    if (!$showVote) {
        $voteClass = 'ticket-vote pull-left';

        if ($model->vote_priority > 0) {
            $voteClass .= ' ticket-vote-plus';
            $voteText = '+' . $model->vote_priority;
        } elseif ($model->vote_priority < 0) {
            $voteClass .= ' ticket-vote-minus';
            $voteText = $model->vote_priority;
        } elseif ($model->vote_priority !== null) {
            $voteClass .= ' ticket-vote-minus';
            $voteText = '&plusmn';
        }

        if (isset($voteText)) {
            echo Html::tag('div', $voteText, ['class' => $voteClass]);
        }
    }

    echo $model->title;
?>