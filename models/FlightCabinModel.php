<?php
/**
 * Created by PhpStorm.
 * User: zx
 * Date: 2016/9/18
 * Time: 18:02
 */

namespace app\models;



use yii\db\ActiveRecord;

class FlightCabinModel extends ActiveRecord
{
    /**
     * @desc: 设定操作的表
     * @return string
     */
    public static function tableName()
    {
        return 'flight_rules';
    }

}