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
        return 'flight_cabin_info';
    }

    public function rules(){
        return [
            [['airline_code','cabin_code','cabin_name','cabin_discount'],'required','message'=>'参数为空']
        ];
    }
}