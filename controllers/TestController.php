<?php
/**
 * Created by PhpStorm.
 * User: ty
 * Date: 2016/9/5
 * Time: 14:28
 */

namespace app\controllers;


use yii\web\Controller;

class TestController extends Controller
{
    public function behaviors() {
        return [
            'test' => [
                'class' => 'app\behavior\TestFilter',//调用过滤器
                'only' => ['filter'],
                /*'rules' => [
                    // 允许认证用户
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],*/
                'actions' => [
                        'filter' => ['post'],//登出只允许提交方式为post,否则报错</span>
                ],
            ],
        ];
    }
    public function actionFilter() {
        return '当前action显示<br/>';//返回的内容会递交给过滤器，由afterAction进行处理
    }
}