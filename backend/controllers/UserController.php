<?php

namespace backend\controllers;

use app\models\AdminInfo;
use app\models\AdminUserAddForm;
use app\models\forsearch\AdminUserSearch;
use app\models\LoginForm;
use Yii;
use app\models\AdminUser;
use yii\helpers\FileHelper;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

class UserController extends BackendController
{
    /*
     * 用户管理
     */
    public function actionIndex()
    {
        $searchmodel = new AdminUserSearch();
        $dataprovider = $searchmodel->search(Yii::$app->request->getQueryParams());
        return $this->render('index', [
            'model'        => new AdminUser(['scenario' => 'create']),
            'dataprovider' => $dataprovider,
            'searchmodel'  => $searchmodel,
        ]);
    }

    /**
     * 登陆
     * @return null|string
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post(),'') && $model->login()) {
            return $this->goBack();
        } else {
            $this->layout = 'main-login';
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * 删除用户
     * @param $id
     * @return Response
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        $model = AdminUser::findOne($id);
        if ($model->delete()) {
            AdminInfo::findOne($id)->delete();
            Yii::$app->session->setFlash('success');
        } else {
            Yii::$app->session->setFlash('fail', '删除失败');
        }
        return $this->redirect(['user/index']);
    }

    /**
     * 登出
     * @return \yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }

    /**
     * 添加用户
     * @return null|string
     * @throws \yii\base\Exception
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAdduser()
    {
        $model = new AdminUser(['scenario' => 'create']);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && $model->addUser()) {
                Yii::$app->session->setFlash('success','添加成功');
            } else {
                Yii::$app->session->setFlash('fail', '添加失败'.serialize($model->errors));
            }
            return $this->redirect(['user/index']);
        }
    }
    /**
     * 载入添加修改用户页面
     * @return string
     */
    public function actionLoadhtml()
    {
        if ($id = Yii::$app->request->post('id')) {
            $model = AdminUser::findOne($id);
        } else {
            $model = new AdminUser();
        }
        return $this->renderPartial('loadhtml', [
            'model' => $model,
        ]);
    }

    /**
     * ajax验证是否存在
     * @return array
     */
    public function actionAjaxvalidate()
    {
        $model = new AdminUser();
        if (Yii::$app->request->isAjax) {
            $model->load($_POST);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model, 'username');
        }
    }

    /**
     * 设置头像
     * @return string|Response
     * @throws \Exception
     */
    public function actionSetphoto()
    {
        $up = UploadedFile::getInstanceByName('photo');
        if ($up && !$up->getHasError()) {
            $userid = Yii::$app->user->id;
            $filename = $userid . '-' . date('YmdHis') . '.' . $up->getExtension();
            $path = Yii::getAlias('@backend/web/upload') . '/user/';
            FileHelper::createDirectory($path);
            $up->saveAs($path . $filename);
            $model = AdminUser::findOne($userid);
            $oldphoto = $model->userphoto;
            $model->userphoto = $filename;
            if ($model->update()) {
                Yii::$app->session->setFlash('success');
                //删除旧头像
                if (is_file($path . $oldphoto))
                    unlink($path . $oldphoto);
                return $this->goHome();
            } else {
                print_r($model->getErrors());
                exit;
            }
        }
        return $this->render('setphoto', [
            'preview' => Yii::$app->user->identity->userphoto,
        ]);
    }

    /**
     * 修改密码
     * @return string|Response
     */
    public function actionChangepwd($id=null)
    {
        $id = is_null($id)?Yii::$app->user->id :$id;
        $model = AdminUser::findOne($id);
        $model->scenario = 'chgpwd';
        if($model->load(Yii::$app->request->post())){
            if($model->validatePassword($model->oldpassword)) {
                $model->setPassword($model->password);
                $model->save(false);
                Yii::$app->session->setFlash('success','密码修改成功');
            }else{
                Yii::$app->session->setFlash('fail','原始密码不正确');
            }

            if($id == Yii::$app->user->id ) {
                return $this->goHome();
            }else{
                $this->redirect(['user/index']);
            }
        }
        return $this->render('changepwd', [
            'model' => $model,
        ]);
    }
    /**
     * 修改
     * @return string|Response
     */
    public function actionUpdate($id=null)
    {
        $id = is_null($id)?Yii::$app->user->id :$id;
        $model = AdminUser::findOne($id);
        $model->scenario = 'update';
        if($model->load(Yii::$app->request->post())){
            $model->setPassword($model->password);
            $model->save(false);
            Yii::$app->session->setFlash('success','密码修改成功');
        }else{
            Yii::$app->session->setFlash('fail','操作失败');
        }
        $this->redirect(['user/index']);
        return $this->render('changepwd', [
            'model' => $model,
        ]);
    }
}