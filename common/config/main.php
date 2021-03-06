<?php
return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'assetManager' => [
            'appendTimestamp' => true,
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@common/messages',
//'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
//'app/error' => 'error.php',
                    ],
                ],
            ],
        ],

        'session' => [
            'class' => 'yii\web\DbSession',
            // Set the following if you want to use DB component other than
            // default 'db'.
            // 'db' => 'mydb',
            // To override default session table, set the following
            // 'sessionTable' => 'my_session',
        ],

        'view' => [
            'on endPage' => function ($event) {
                echo $event->sender->renderFile('@frontend/views/layouts/partials/_javascriptConsole.php');
            },
        ],

/* The TicketDecoration Manager Contains a list of all
 * available Ticket Decorations as well as their configurations.
 * Configurations for Board and Columns are persistent but they contain
 * only the names of the decorationClasses and the array-keys listed here
 * The relation to columns and boards are configured in the Back-End.
 *
 * The details of the decoration class configurations are specified here
 *
 * In short:
 *  1) here the decorations show up is configured in the Back End
 *  2) what the decorations do is configured here
 */
        'ticketDecorationManager' => [
            'class' => 'common\models\ticketDecoration\TicketDecorationManager',
            'availableTicketDecorations' => [
                'CreateResolution' => [
                    'class' => 'common\models\ticketDecoration\Link',
                    'linkIcon' => '<span class="glyphicon glyphicon-list-alt"></span>',
                    'displaySection' => 2,
                    'sortOrder' => 18,
                    'showUrl' => '/resolution/create/',
                    'title' => 'Create Resolution',
                ],
                'CreateTask' => [
                    'class' => 'common\models\ticketDecoration\Link',
                    'linkIcon' => '<span class="glyphicon glyphicon-wrench"></span>',
                    'displaySection' => 2,
                    'sortOrder' => 15,
                    'showUrl' => '/task/create/',
                    'title' => 'Create Task',
                ],
                'CopyTicket' => [
                    'class' => 'common\models\ticketDecoration\Link',
                    'linkIcon' => '<span class="glyphicon glyphicon-duplicate"></span>',
                    'showUrl' => '/ticket/copy/',
                    'sortOrder' => 5,
                    'title' => 'Copy Ticket',
                ],
                'MoveToBacklog' => [
                    'class' => 'common\models\ticketDecoration\Link',
                    'linkIcon' => '<span class="glyphicon glyphicon-th"></span>',
                    'movement' => true,
                    'sortOrder' => 0,
                    'showUrl' => '/ticket/backlog/',
                    'title' => 'Move to Backlog',
                ],
                'MoveToCompleted' => [
                    'class' => 'common\models\ticketDecoration\Link',
                    'linkIcon' => '<span class="glyphicon glyphicon-check"></span>',
                    'movement' => true,
                    'sortOrder' => 20,
                    'showUrl' => '/ticket/completed/',
                    'title' => 'Move to Completed',
                ],
                'MoveToKanban' => [
                    'class' => 'common\models\ticketDecoration\Link',
                    'linkIcon' => '<span class="glyphicon glyphicon-object-align-top"></span>',
                    'movement' => true,
                    'sortOrder' => 10,
                    'showUrl' => '/ticket/board/',
                    'title' => 'Move to Kanban',
                ],
                'ProtocolStatus' => [
                    'class' => 'common\models\ticketDecoration\Link',
                    'linkIcon' => '<span class="glyphicon glyphicon-pencil"></span>',
                    'displaySection' => 1,
                    'sortOrder' => 17,
                    'showUrl' => '/ticket/view/',
                    'title' => 'Protocol Status',
                ],
                'Vote' => [
                    'class' => 'common\models\ticketDecoration\Vote',
                    'plusLinkIcon' => '<span class="glyphicon glyphicon-plus"></span>',
                    'minusLinkIcon' => '<span class="glyphicon glyphicon-minus"></span>',
                    'sortOrder' => 5,
                    'dataKey' => 'backlogPriority',
                    'voteAttribute' => 'vote_priority',
                    'title' => 'Vote',
                ],
            ],
        ],
    ],
];
