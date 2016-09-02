<?php
/**
 * Created by PhpStorm.
 * User: zx
 * Date: 2016/9/2
 * Time: 10:16
 */

namespace app\models;


use yii\db\ActiveRecord;

class YiiModel extends ActiveRecord
{
    public static function tableName(){
        return 'user';
    }

}