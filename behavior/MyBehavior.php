<?php
/**
 * Created by PhpStorm.
 * User: zx
 * Date: 2016/9/5
 * Time: 9:58
 */

namespace app\behavior;


use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\base\Model;

class MyBehavior extends Behavior
{
    public $test;

    /*public function events(){
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'before_insert',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'before_update',
        ];
    }*/

    public function before_insert(){
        echo 'insert';
        exit;
    }

    public function before_update(){
        echo 'update';
        exit;
    }

}