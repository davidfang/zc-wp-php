<?php
/**
* AdmininfoController控制器
* Created by David
* User: David.Fang
* Date: 2015-07-21* Time: 13:45:56*/
namespace backend\controllers;

use app\models\AdminUser;
use Yii;
use app\models\AdminInfo;
use app\models\AdmininfoSearch;
use backend\controllers\BackendController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AdmininfoController 控制器对 AdminInfo 模型 CRUD 操作.
 */
class AdmininfoController extends BackendController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        return array_merge($behaviors, [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ]);
    }

    /**
     *  Admininfo 模型列表.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AdmininfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Admininfo 模型详情
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Admininfo model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AdminInfo();
        $model_primary = new AdminUser();
        if(Yii::$app->request->isPost){
            if ($model_primary->load(Yii::$app->request->post(),'AdminUser') && $model_primary->addUser()) {
                $model->load(Yii::$app->request->post(),'AdminInfo');
                $model->id = $model_primary->id;
                if( $model->save())
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        //var_dump($model->errors);
        //var_dump($model_primary->errors);exit;
            return $this->render('create', [
                'model' => $model,
                'model_primary' => $model_primary,
            ]);

    }

    /**
     * AdminInfo 模型更新操作
     * 如果更新成功将跳转到“查看”页面
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model_primary = AdminUser::findOne($id);
        //var_dump($model);
        //var_dump($model_primary);exit;
        if ($model->load(Yii::$app->request->post()) && $model->save() && $model_primary->load(Yii::$app->request->post()) && $model_primary->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }
            return $this->render('update', [
                'model' => $model,
                'model_primary' => $model_primary,
            ]);

    }

    /**
     * AdminInfo模型删除操作
     * 如果删除成功，跳转到“列表”页
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        AdminUser::findOne($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * 根据primary key查找 AdminInfo 模型的信息
     * 如果数据不存在跳转到 404
     * @param integer $id
     * @return AdminInfo the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AdminInfo::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
