<?php

namespace frontend\controllers;

use Yii;
use frontend\models\User;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/** *
 * UserController implements the CRUD actions for
 * Usermodel.
 */
class UserController extends Controller
{
    public function behaviors() {

        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => ['delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Usermodels.
     * @return mixed
     */
    /*public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' =>
                User::find(),
        ]);
        return $this->render('index', [
            'dataProvider' =>
                $dataProvider,
        ]);
    } */

    /**
     * Displays a single Usermodel of the current user.
     *
     * @return mixed
     */
    public function actionView()
    {
        return $this->render('view', [
            'model' => $this->findModel(Yii::$app->getUser()->id),
        ]);
    }

    /**
     * Creates a new Usermodel.
     * If creation is successful, the browser will be redirected to
     * the 'view' page.
     * @return mixed
     */
    /* public function actionCreate()
    {
        $model = new
        User();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                'view',
                'id' => $model->id
            ]);
        } else {
            return $this->render('create', ['model' => $model,]);
        }
    } */

    /**
     * Updates the Usermodel for the current user.
     * If update is successful, the browser will be redirected to the 'view' page.
     *
     * @return mixed
     */
    public function actionUpdate()
    {
        $model = $this->findModel(Yii::$app->getUser()->id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect([
                'view',
                'id' => $model->id
            ]);
        } else {
            return $this->render('update', ['model' => $model,]);
        }
    }

    public function actionUpload()
    {
        if (Yii::$app->request->isPost) {
            $model = $this->findModel(Yii::$app->getUser()->id);
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');
            if ($model->upload()) {
                // file is uploaded successfully
                return $this->render('view', ['model' => $model]);
            }
        }

        return $this->render('update', ['model' => $model]);
    }

    /**
     * Deletes an existing Usermodel.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    /* public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    } */

    /**
     * Finds the Usermodel based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
