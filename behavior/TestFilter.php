<?php
/**
 * Created by PhpStorm.
 * User: zx
 * Date: 2016/9/5
 * Time: 9:58
 */

namespace app\behavior;


use Yii;
use yii\base\Action;
use yii\base\ActionFilter;

class TestFilter  extends ActionFilter
{
    //在action之前运行，可用来过滤输入
    public function beforeAction($action) {
        echo '在调用action前显示<br/>';
        return TRUE;//如果返回值为false,则action不会运行
    }
    //在action之后运行，可用来过滤输出
    public function afterAction($action, $result) {
        return $result.'在调用action后显示<br/>';//可以对action输出的$result进行过滤，retun的内容会直接显示
    }

}