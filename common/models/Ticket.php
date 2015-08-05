<?php

namespace common\models;

use Faker\Factory;
use dosamigos\taggable\Taggable;
use yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;


/**
 * This is the model class for table "ticket".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property string  $title
 * @property string  $description
 * @property integer $column_id
 * @property integer $board_id
 * @property integer $ticket_order
 *
 * @property BoardColumn $column
 */
class Ticket extends \yii\db\ActiveRecord
{
    const DEMO_BACKLOG_TICKETS = 100;
    const DEMO_BOARD_TICKETS = 5;
    const DEMO_COMPLETED_TICKETS = 50;

    /**
     * The status (column_id) of tickets in the backlog
     */
    const DEFAULT_BACKLOG_STATUS = 0;

    /**
     * Alternate status (column_id) of tickets in the backlog
     * Tis is needed when using a mysql foreign Key Constraint on the tickets. On DELETE of the column the
     * column ID of the ticket is set back to null (0 is not feasible, no default value)
     */
    const ALTERNATE_BACKLOG_STATUS = null;

    /**
     * The default status (column_id) of tickets that are completed
     */
    const DEFAULT_COMPLETED_STATUS = -1;

    /**
     * The default status (column_id) of tickets that are on the kanban board
     */
    const DEFAULT_KANBANBOARD_STATUS = 1;

    /**
     * Error Message when assigning ticket to current active board
     */
    const ACTIVE_BOARD_NOT_FOUND = 'Current Active Board Not Found';

    /**
     * If this variable is (> 0) thann all queries obtained through the find() function
     * will be restricted to this value, i.e. (board_id = self::$restrictQueryToBoardId)
     * Subsequent query modifications must use the andWhere (and related) methods in order to
     * preserve this restriction. Subsequent use of a standard where() query will eliminate
     * this restriction.
     *
     * This variable is set automatically from the board model
     *
     * @var int
     */
    public static $restrictQueryToBoardId = 0;

    /*
     * Uses in conditions to test for a restrictedQuery based on board_Id
     * Thwe value of this constant should be a value that a board_Id cannot have
     */
    const NO_BOARD_QUERY_RESTRICTION = 0;


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
            Taggable::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'column_id'], 'required'],
            [['id', 'created_at', 'updated_at', 'created_by', 'updated_by', 'column_id', 'ticket_order'], 'integer'],
            [['title', 'description'], 'string'],
            [['id'], 'unique'],
            [['tagNames'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'title' => 'Title',
            'description' => 'Description',
            'column_id' => 'Column ID',
            'board_id' => 'Board ID',
            'ticket_order' => 'Ticket Order',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColumn() {
        return $this->hasOne(BoardColumn::className(), ['id' => 'column_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy() {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return string
     */
    public function getCreatedByName() {
        return $this->getCreatedBy()->one()->username;
    }

    /**
     * @return string
     */
    public function getCreatedByAvatar() {
        return $this->getCreatedBy()->one()->avatarUrlColor;
    }

    /**
     * Returns the status of a ticket, whether ot not it is currently active
     * on the KanBanBoard
     * @return Boolean true = backlog, false = not backlog
     */
    public function isBacklog() {
        return (bool)($this->getColumnId() == self::DEFAULT_BACKLOG_STATUS or
                      $this->getColumnId() == self::ALTERNATE_BACKLOG_STATUS);
    }

    /**
     * Returns the status of a ticket, whether ot not it is currently active
     * on the KanBanBoard
     * @return Boolean true = active, false = not active
     */
    public function isKanBanBoard() {
        return (bool)($this->getColumnId() >= self::DEFAULT_KANBANBOARD_STATUS);
    }

    /**
     * Returns the status of a ticket, whether ot not it is currently active
     * on the KanBanBoard
     * @return Boolean true = active, false = not active
     */
    public function isCompleted() {
        return (bool)($this->getColumnId() <= self::DEFAULT_COMPLETED_STATUS);
    }

    /**
     * Sets the Status of the Ticket to be in the Backlog
     *
     * @return $this common\models\ticket
     */
    public function moveToBacklog() {
        $this->column_id = self::DEFAULT_BACKLOG_STATUS;

        return $this;
    }

    /**
     * Sets the Status of the Ticket to be completed.
     *
     * @param integer New completed status, defaults to self::DEFAULT_COMPLETED_STATUS
     *                completed status can be any negative number. If the new status
     *                is not negative (i.e. not a completed status),
     *                then the default completed status is used)
     * @return $this common\models\ticket
     */
    public function moveToCompleted($newTicketStatus = self::DEFAULT_COMPLETED_STATUS) {
        if ($newTicketStatus <= self::DEFAULT_COMPLETED_STATUS){
            $this->column_id = $newTicketStatus;
        } else {
            $this->column_id = self::DEFAULT_COMPLETED_STATUS;
        }

        return $this;
    }

    /**
     * Sets the Status of the Ticket to be on the Kanban Board, The Default Board column,start position is used
     *
     * @return $this common\models\ticket
     */
    public function moveToKanBanBoard() {
        $this->column_id = self::DEFAULT_KANBANBOARD_STATUS;

        return $this;
    }

    /**
     * Sets the Status of the Ticket to be in the Backlog
     *
     * @return $this common\models\ticket
     */
    public function moveToColumn($newTicketStatus = self::DEFAULT_KANBANBOARD_STATUS) {
        $this->column_id = $newTicketStatus;

        return $this;
    }

    /**
     * Query to find all Backlog Tickets
     *
     * @return yii\db\QueryInterface
     */
    public function findBacklog() {

        return Ticket::find()
            ->where(['column_id' => 0])
            ->orWhere(['column_id' => null])
            ->orderBy(['updated_at' => SORT_DESC]);
    }

    /**
     * Query to find all Completed Tickets
     *
     * @return yii\db\QueryInterface
     */
    public function findCompleted() {

        return Ticket::find()
            ->where(['<', 'column_id', 0])
            ->orderBy(['updated_at' => SORT_DESC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tags::className(), ['id' => 'tag_id'])->viaTable('ticket_tag_mm', ['ticket_id' => 'id']);
    }

    /**
     * If specific conditions are active the standard find() method
     * is adapted and additional query conditions are applied.
     *
     *
     * @inheritdoc
     */
    public static function find() {
        if (self::$restrictQueryToBoardId != self::NO_BOARD_QUERY_RESTRICTION) {
            return parent::find()->andWhere(['board_id' => self::$restrictQueryToBoardId]);
        } else {
            return parent::find();
        }
    }

    /**
     * Retrieves the Current Active Board Id for this session and sets the
     * Ticket Class Variable self::$restrictQueryToBoardId to its value.
     * This causes all ticket queries to be restricted to the current BoardId
     * @param $currentBoardId Integer, Id to which all ticket queries will be restricted to
     */
    public static function restrictQueryToBoard($currentBoardId) {
        self::$restrictQueryToBoardId = $currentBoardId;
    }

    /**
     * Retrieves the Current Active Board Id for this session and sets the
     * Ticket Class Variable self::$restrictQueryToBoardId to its value.
     * This causes all ticket queries to be restricted to the current BoardId
     */
    public static function clearBoardQueryRestriction() {
        self::$restrictQueryToBoardId = self::NO_BOARD_QUERY_RESTRICTION;
    }

    /**
     * Creates a set of Demo Tickets
     *
     * @return boolean
     */
    public function createDemoTickets($boardId) {
        $faker = Factory::create();

        $this->deleteAll();

        // Create Backlog Tickets
        for ($i = 0; $i < self::DEMO_BACKLOG_TICKETS; $i++) {
            $this->title =          $faker->text(30);
            $this->description =    $faker->text();
            $this->column_id =      self::DEFAULT_BACKLOG_STATUS;
            $this->board_id = $boardId;
            $this->ticket_order = 0;
            $this->isNewRecord = true;
            $this->id = null;
            if (!$this->save()) {
                return false;
            }
        }

        // Create Completed Tickets
        for ($i = 0; $i < self::DEMO_COMPLETED_TICKETS; $i++) {
            $this->title =          $faker->text(30);
            $this->description =    $faker->text();
            $this->column_id =      self::DEFAULT_COMPLETED_STATUS;
            $this->board_id = $boardId;
            $this->ticket_order = 0;
            $this->isNewRecord = true;
            $this->id = null;
            if (!$this->save()) {
                return false;
            }
        }

        // Create KanBanBoard Tickets
        for ($i = 0; $i < self::DEMO_BOARD_TICKETS; $i++) {
            $this->title =          $faker->text(30);
            $this->description =    $faker->text();
            $this->column_id =      self::DEFAULT_KANBANBOARD_STATUS;
            $this->board_id = $boardId;
            $this->ticket_order = $i;
            $this->isNewRecord = true;
            $this->id = null;
            if (!$this->save()) {
                return false;
            }
        }

        return true;
    }

}
