<?php

namespace frontend\controllers; //namespace must be the first statement

use common\models\Board; //Interesting, I just discovered that the "use" must come after "namespace"
use common\models\User;
use common\models\Ticket;

class KanbanboardController extends \yii\web\Controller {

    public function actionIndex() {

        //initialize arrays, otherwise possible error in call to render, if they don't exist
        $ticketData = null;
        $columnData = null;

        $board = Board::findOne(1);

        $columnRecords = $board->getBoardColumns()->where('id > 0')->orderBy('display_order, id')->all();
        foreach ($columnRecords as $singleColumnRecord) {
            $columnData[] = [
                'title' => $singleColumnRecord->title,
                'attribute' => $singleColumnRecord->id,
                'displayOrder' => $singleColumnRecord->display_order,
            ];

            $columnTickets = $singleColumnRecord->getTickets()->orderBy('column_id, ticket_order')->asArray()->all();
            foreach ($columnTickets as $singleColumnTicket) {
                $newTicketDataRecord = [
                    'title' => $singleColumnTicket['title'],
                    'id' => $singleColumnTicket['id'],
                    'description' => $singleColumnTicket['description'],
                    'user_id' => $singleColumnTicket['user_id'],
                    'assignedName' => User::findOne($singleColumnTicket['user_id'])->username,
                    'columnId' => $singleColumnTicket['column_id'],
                    'created_at' => $singleColumnTicket['created_at'],
                    'ticketOrder' => $singleColumnTicket['ticket_order'],
                ];

                $ticketData[]= $newTicketDataRecord;
            }
        }

        return $this->render('index', [
                'boardTitle' => $board->title,
                'boardDescription' => $board->description,
                'columnData' => $columnData ? $columnData : [],
                'ticketData' => $ticketData ? $ticketData : [],
            ]
        );
    }

    public function actionBacklog() {
        $tickets = ticket::findBacklog();

        return $this->render('backlog', [
            'tickets' => $tickets,
        ]);
    }

    public function actionCompleted() {
        $tickets = ticket::findCompleted();

        return $this->render('completed', [
            'tickets' => $tickets,
        ]);
    }
}