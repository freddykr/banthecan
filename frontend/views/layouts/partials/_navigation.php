<?php
/**
 * Created by PhpStorm.
 * User: and
 * Date: 11/22/15
 * Time: 7:01 PM
 */

use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;

/* @var $boardObject yii\db\ActiveRecord */
/* @var $kanbanName String */
/* @var $backlogName String */

NavBar::begin([
    'brandLabel' => (YII_ENV_DEMO ? 'DEMO: ' : '') . $this->title,
    'brandUrl' => Yii::$app->homeUrl,
    'options' => [
        'class' => 'navbar-inverse navbar-fixed-top',
	],
    'innerContainerOptions' => [
        'class' => 'container-fluid'
        ]
    ]
);

if (Yii::$app->user->isGuest) {

	//$menuItems[] = ['label' => 'Signup', 'url' => ['/site/signup']];
	$menuItems[] = ['label' => \Yii::t('app', 'Login'), 'url' => ['/site/login']];
	$menuItems[] = ['label' => \Yii::t('app', 'Contact'), 'url' => ['/site/contact']];
	$menuItems[] = ['label' => \Yii::t('app', 'About'), 'url' => ['/site/about']];

} else {

    $menuItems[] = html::tag('li',
        $this->render('@frontend/views/site/partials/_userIcon',
        ['userId' => Yii::$app->getUser()->id]),
        ['class' => 'menu-avatar-li pull-right hidden-xs']);

	$menuItems[] = [
        'label' => \Yii::t('app', 'Ban The Can'),
        'options' => ['class' => 'pull-right hidden-xs'],
        'items' => [
			['label' => \Yii::t('app', 'Tickets'), 'url' => ['/ticket']],
			['label' => \Yii::t('app', 'Tags'), 'url' => ['/tags']],
			['label' => \Yii::t('app', 'Tasks'), 'url' => ['/task']],
			['label' => \Yii::t('app', 'Resolutions'), 'url' => ['/resolution']],
			['label' => \Yii::t('app', 'Select Board'), 'url' => ['/board/select']],
			['label' => \Yii::t('app', 'Logout'), 'url' => ['/site/logout'], 'linkOptions' => ['data-method' => 'post']],
			['label' => \Yii::t('app', 'User Settings'), 'url' => ['/user/view']],
			['label' => \Yii::t('app', 'Contact'), 'url' => ['/site/contact']],
			['label' => \Yii::t('app', 'About'), 'url' => ['/site/about']],
		],
	];

    $menuItems[] = Html::a(
        \Yii::t('app', 'Create Ticket'),
        '/ticket/create', [
        'class' => 'btn btn-success apc-header-button',
        'id' => 'header-create-button',
        //'data-toggle' => 'modal',
        //'data-target' => '#create-ticket-modal-content'
    ]);

    $menuItems[] = Html::a(
        $backlogName,
        '/board/backlog', [
        'class' => 'btn btn-primary apc-header-button',
        'id' => 'header-backlog-button',
    ]);

    $menuItems[] = Html::a(
        $kanbanName,
        '/board', [
        'class' => 'btn btn-primary apc-header-button',
        'id' => 'header-kanban-button',
    ]);

    $menuItems[] = Html::a(
        \Yii::t('app', 'Completed'),
        '/board/completed', [
        'class' => 'btn btn-primary apc-header-button',
        'id' => 'header-completed-button',
    ]);
}

echo Nav::widget([
	'options' => ['class' => 'navbar-nav navbar-right'],
	'items' => $menuItems,
]);

NavBar::end();