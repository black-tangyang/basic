<?php
/**
 * Created by PhpStorm.
 * User: zx
 * Date: 2016/9/2
 * Time: 10:16
 */

namespace app\models;


use app\behavior\MyBehavior;
use yii\db\ActiveRecord;

class YiiModel extends ActiveRecord
{
    public static function tableName(){
        return 'user';
    }

    public function behavior(){
        return [
            'class' => MyBehavior::className(),
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => 'before_insert',
                ActiveRecord::EVENT_BEFORE_UPDATE => 'before_update',
            ]
        ];
    }

}