<?php

namespace frontend\controllers; //namespace must be the first statement

use yii;
use common\models\Board;
use common\models\Ticket;
use common\models\TicketSearch;
use yii\data\ActiveDataProvider;
use common\models\User;
use yii\filters\AccessControl;


class BoardController extends \yii\web\Controller {

    const DEFAULT_PAGE_SIZE = 18;
    private $currentBoard = null;

    /**
     * @inheritdoc
     */
    public function behaviors() {

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {

        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Initialize the Board to the Session Board_id, and implicitly
     * restrict all ticket queries to members of this board for
     * the actions: completed, backlog and index
     *
     * @param yii\base\Action $action
     * @return bool
     * @throws yii\web\BadRequestHttpException
     */
    public function beforeAction($action)
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        if ($action->id == 'completed' or
            $action->id == 'backlog' or
            $action->id == 'index') {
            $this->currentBoard = Board::getActiveboard();
        }

        return true; // or false to not run the action
    }
    /**
     * Default Action, shows active tickets in a KanBan Board
     */
    public function actionIndex() {
        return $this->render('index', [
            'board' => $this->currentBoard,
        ]);
    }

    /**
     * Shows tickets in the Backlog
     */
    public function actionBacklog() {
        $boardRecord = Board::getActiveboard();
        $searchModel = Yii::createObject('common\models\TicketSearch');

        //$tdManager = Yii::createObject('ticketDecorationManager');
        $tdManager = Yii::createObject([
            'class' => 'common\models\ticketDecoration\TicketDecorationManager',
            'decorationClasses' => [
                'Dummy' => [
                    'class' => 'common\models\ticketDecoration\Dummy',
                ],
                'Generic' => [
                    'class' => 'common\models\ticketDecoration\Generic',
                ],
                'Smart' => [
                    'class' => 'common\models\ticketDecoration\Smart',
                ],
                'MoveToBoard' => [
                    'class' => 'common\models\ticketDecoration\MoveToBoard',
                ],
            ],
        ]);
        // Create a Container Dependency Injection Definition using an alias
        // This alias is referenced by each ticket to attach the defined behaviors
        if (trim($boardRecord->ticket_completed_configuration) != '') {
            Yii::$container->set(
                Ticket::TICKET_DECORATION_CLASS_ALIAS,
                unserialize($boardRecord->ticket_backlog_configuration)
            );
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 0);
        $dataProvider->pagination->pageSize = self::DEFAULT_PAGE_SIZE;

        return $this->render('backlog', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pageTitle' => $this->currentBoard->backlog_name,
            'action' => $this->action->id,
        ]);
    }

    /**
     * Shows completed tickets
     */
    public function actionCompleted() {
        $boardRecord = Board::getActiveboard();
        $searchModel = Yii::createObject('common\models\TicketSearch');

        // Create a Container Dependency Injection Definition using an alias
        // This alias is referenced by each ticket to attach the defined behaviors
        if (trim($boardRecord->ticket_completed_configuration) != '') {
            Yii::$container->set(
                Ticket::TICKET_DECORATION_CLASS_ALIAS,
                unserialize($boardRecord->ticket_completed_configuration)
            );
        }

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, -1);
        $dataProvider->pagination->pageSize = self::DEFAULT_PAGE_SIZE;

        return $this->render('completed', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pageTitle' => $this->currentBoard->completed_name,
            'action' => $this->action->id,
        ]);
    }

    /**
     * Allows the current user to select the active board from his/her board options
     */
    public function actionSelect() {
        $userBoardId = explode(',', User::findOne(Yii::$app->getUser()->id)->board_id);

        $userBoards = new ActiveDataProvider([
            'query' => Board::find()->where(['id' => $userBoardId]),
        ]);
        $boardCount = $userBoards->getTotalCount();

        if ($boardCount == 0) {
            // No Boards, log user out
            Yii::$app->user->logout();
            return $this->render('noBoard');
        } elseif ($boardCount == 1) {
            // Only one board for user, activate it automatically
            $activeBoardId = $userBoards->getModels()[0]->id;
            $this->redirect(['activate','id' => $activeBoardId]);
        } else {
            // USer must select which board to activate
            return $this->render('select',['userBoards' => $userBoards]);
        }
    }

    /**
     * Activates the Board for the current User. This means the selected board is made
     * available globally via cookies and(or) sessions
     */
    public function actionActivate() {
        $session = Yii::$app->session;
        $request = Yii::$app->request;
        $activeBoardId = $request->get('id');
        $session->set('currentBoardId' , $activeBoardId);
        $boardRecord = Board::getActiveboard();
        $session->setFlash('success', 'Board activated: ' . $boardRecord->title);
        Yii::$app->params['title'] = $boardRecord->title;
        $this->goHome();
    }
}
