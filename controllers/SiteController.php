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

    public $enableCsrfValidation = false;

    /**
     * Displays homepage.
     *
     * @return string
     */


    public function actionIndex()
    {
        

        define("TOKEN", "tang");

        if(isset($_GET['echostr']))
        {
            $echoStr = $_GET["echostr"];

            //valid signature , option
            if($this->checkSignature())
            {
                echo $echoStr;
                exit;
            }
        }
        else
        {
            $this->responseMsg();
        }
        $value = json_encode($_POST);

        $path = './test.txt';
        $str = $value;
        file_put_contents($path,$str,FILE_APPEND);
        return FALSE;

    }

    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];

        //$postStr = file_get_contents("php://input");

        $path = './test.txt';
        $str = $postStr;
        file_put_contents($path,$str,FILE_APPEND);

        //extract post data
        if (!empty($postStr)) {
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
               the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUsername = $postObj->ToUserName;
            $info_type = $postObj->MsgType;
            $keyword = trim($postObj->Content);
            $time = time();

            if ($info_type == 'text') {
                $textTpl = "<xml>
							<ToUserName><![CDATA[%s]]></ToUserName>
							<FromUserName><![CDATA[%s]]></FromUserName>
							<CreateTime>%s</CreateTime>
							<MsgType><![CDATA[%s]]></MsgType>
							<Content><![CDATA[%s]]></Content>
							<FuncFlag>0</FuncFlag>
							</xml>";
                $msgType = "text";
                $contentStr = $keyword;
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $contentStr);
                echo $resultStr;
                exit;
            } elseif ($info_type == 'image') {

                $textTpl = "<xml>
                          <ToUserName><![CDATA[%s]]></ToUserName>
                          <FromUserName><![CDATA[%s]]></FromUserName>
                          <CreateTime>%s</CreateTime>
                          <MsgType><![CDATA[%s]]></MsgType>
                          <ArticleCount>1</ArticleCount>
                          <Articles>
                          <item>
                          <Title><![CDATA[%s]]></Title>
                          <Description><![CDATA[%s]]></Description>
                          <PicUrl><![CDATA[%s]]></PicUrl>
                          <Url><![CDATA[%s]]></Url>
                          </item>
                          </Articles>
                          </xml>";

                $msgType = "news";
                $Title = '测试题目';
                $Description = '测试题目的一些描述';
                $PicUrl = 'http://qiniu.codexueyuan.com/FiMhaujau9l52xIDjn9_a5A7lmbj';
                $Url = 'www.tangyangyang.top/index.php?r=site/test_info';

                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, $msgType, $Title, $Description, $PicUrl, $Url);
                echo $resultStr;
                exit;
            }else{
                echo '';
                exit;
            }
        }
    }

    private function checkSignature()
    {
        // you must define TOKEN by yourself
             if (!defined("TOKEN")) {
            throw new Exception('TOKEN is not defined!');
        }

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function get_user_info($penid){
        $token = $this->get_access_token();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$token.'&openid='.$penid.'&lang=zh_CN';
        $result = $this->getcurl($url);
        var_dump($result);
        exit;
        $arr = json_decode($result,true);
        return $arr;
    }


    public function get_access_token(){
        $cache=Yii::$app->cache;
        $app_id="wxaf58d9ec0390e62d";
        $APPSECRET="eeff12f9a3ea747b5a2912fa23345fdb ";
        $url="https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$app_id."&secret=".$APPSECRET;
        $access_token=$cache->get("access_token");
        if($access_token){
            return $access_token;
        }else{
            $access_token=$this->getcurl($url);
            $cache->set("access_token",$access_token['access_token'],3600);
            return $access_token['access_token'];
        }

    }


    /*创建自定义菜单*/
    public  function actionCreatemenu(){
        $access_token=$this->get_access_token();
      /*  echo $access_token;
        exit;*/

        $app_id="wxaf58d9ec0390e62d";
        $url=urlencode("http://maitian.codexueyuan.com/index.php?r=site/call_back");
        $data = '{
             "button":[
              {
                   "name":"任务",
                   "type":"view",
                    "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$app_id.'&redirect_uri='.$url.'&response_type=code&scope=snsapi_userinfo&state=1#wechat_redirect"

               },
                  {
                       "name": "会员中心",
                        "sub_button": [
                            {
                                "type": "view",
                                "name": "当团长",
                                "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$app_id.'&redirect_uri='.$url.'&response_type=code&scope=snsapi_userinfo&state=2#wechat_redirect"

                            },
                            {
                                "type": "view",
                                "name": "个人中心",
                                "url":"https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$app_id.'&redirect_uri='.$url.'&response_type=code&scope=snsapi_userinfo&state=3#wechat_redirect"

                            }
                        ]

                   }

               ]
         }';


        $menu_url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$access_token;

        var_dump($this->postcurl($menu_url,$data));

    }


    /*curl-post调用方法*/
    public function postcurl($url,$data){
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        curl_setopt ($ch, CURLOPT_POSTFIELDS,$data);
        $output = curl_exec($ch);
        $json_str=json_decode($output,true);
        /*return access_token*/
        return $json_str;
    }
    /*curl-get调用方法*/
    public  function getcurl($url){
        $ch=curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        $output = curl_exec($ch) ;
        $json_str=json_decode($output,true);
        /*return access_token*/
        return $json_str;
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
                        'cabin_discount' => 0.7,
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
        foreach($arr as $key=>$value){
            $model = new FlightCabinModel();
            $model->setAttributes($value);
            $result = $model->save();
            if(!$result){
                echo '报错';
                exit;
            }
        }

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


    public function actionTest_info(){
        echo '你好，傻逼！';
    }

    public function actionTest_user(){
        $arr = $this->get_user_info('oYqTAvypUqkovHGFHe7Xsjn4exNo');
        echo "<pre>";
        print_r($arr);
    }
}
