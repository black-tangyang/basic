<?php

namespace app\controllers;

use app\models\FlightCabinModel;
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
use PHPExcel_Reader_Excel2007;
use PHPExcel_Reader_Excel5;


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
        $filePath = __DIR__."/test.xlsx"; // 要读取的文件的路径

        $PHPExcel = new PHPExcel(); // 拿到实例，待会儿用

        $PHPReader = new PHPExcel_Reader_Excel2007(); // Reader很关键，用来读excel文件

        if (!$PHPReader->canRead($filePath)) { // 这里是用Reader尝试去读文件，07不行用05，05不行就报错。注意，这里的return是Yii框架的方式。
            $PHPReader = new PHPExcel_Reader_Excel5();
            if (!$PHPReader->canRead($filePath)) {
                $errorMessage = "Can not read file.";
                echo $errorMessage;
            }
        }

        $PHPExcel = $PHPReader->load($filePath); // Reader读出来后，加载给Excel实例

        $allSheet = $PHPExcel->getSheetCount(); // sheet数

        $currentSheet = $PHPExcel->getSheet(0); // 拿到第一个sheet（工作簿？）

        $content = $currentSheet->toArray('', true, true);

        $allColumn = $currentSheet->getHighestColumn(); // 最高的列，比如AU. 列从A开始

        $highestRow = $currentSheet->getHighestRow(); // 取得总行数

        $highestColumn = $currentSheet->getHighestColumn(); // 取得总列数

        echo "<pre>";
        //print_r($content);


        for($i=1;$i<87;){
            $airline_code = $content[$i][0];
            foreach($content[$i] as $k=>$v){
                if($k == 0 || $k == 1){
                    continue;
                }
                if($v == ''){
                    break;
                }
                if($content[$i+2][$k] == 70){
                    $arr[] = array(
                        'airline_code' => $airline_code,
                        'cabin_code' => $content[$i + 1][$k],
                        'cabin_name' => $content[$i][$k],
                        'cabin_discount' => 0.07,
                    );
                }else {
                    $arr[] = array(
                        'airline_code' => $airline_code,
                        'cabin_code' => $content[$i + 1][$k],
                        'cabin_name' => $content[$i][$k],
                        'cabin_discount' => $content[$i + 2][$k] == '' ? 1 : $content[$i + 2][$k] * 0.01,
                    );
                }
            }
            $i=$i+3;
        }

        print_r($arr);
        /*$model = new FlightCabinModel();
        $result = $model->find()->asArray()->all();
        var_dump($result);*/
        exit;
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
