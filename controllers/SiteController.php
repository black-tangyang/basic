<?php

namespace app\controllers;

use app\models\YiiModel;
use yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\common\CommonFunction;
use app\behavior\MyBehavior;
use yii\redis;
use yii\helpers;
use yii\bootstrap\BootstrapAsset;
use PHPExcel;

class SiteController extends Controller
{


    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //--------------直接存redis
       /* $redis = Yii::$app->redis;
        if($redis->get('test_yiibasic_kdm') == NULL){
            $redis->set('test_yiibasic_kdm','tangyang');
            $redis->expire('test_yiibasic_kdm',60);
        };
        $value = $redis->get('test_yiibasic_kdm');
        var_dump($value);
        exit;*/


        //---------------以session方式存redis
       /* $session = Yii::$app->session;
        if($session->get('test_session_one') == NULL){
            $session->set('test_session_one','one');
        };

        var_dump($session->get('test_session_one'));
         unset($_SESSION['test_session_one']);
        var_dump($session->get('test_session_one'));*/

        $url = yii\helpers\Url::toRoute(['site/test']);
        var_dump($url);


        //---------------插入数据库
        /*$model =  new YiiModel();
        $model->user_name = 'hong';
        $result = $model->save();
        var_dump($result);
        echo "<pre>";*/


        //CommonFunction::test();
        //return $this->render('index');
    }


    public function actionTest(){
        $phpexcel = new PHPExcel();
        var_dump($phpexcel);
        echo 'test';
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
