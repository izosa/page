<?php


namespace izosa\page;

use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * Class Page Log Model
 * @package izosa\page
 *
 * @property integer $id
 * @property integer $created_at
 * @property string $url
 * @property string $file
 * @property integer $useragent
 * @property string $proxy
 * @property integer $status
 */
class Log extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => function() { return date('y-m-d H:i:s'); }
            ],
        ];
    }

    public static function tableName()
    {
        return '{{%page_log}}';
    }
}