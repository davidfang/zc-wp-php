<?php
namespace common\components;
use Yii;
use yii\db\BaseActiveRecord;
use yii\base\Behavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * 数据操作日志
 * 
 *
 */
class DatabaseLog extends Behavior
{
    private static $_log_table  = '{{%db_log}}';
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * 绑定事件
     * @return array
     */
    public function events()
    {
        return array_merge(parent::events(),[
            BaseActiveRecord::EVENT_AFTER_INSERT    => [$this, 'log'],
            BaseActiveRecord::EVENT_AFTER_UPDATE    => [$this, 'log'],
            BaseActiveRecord::EVENT_AFTER_DELETE    => [$this, 'log'],
        ]);
    }

    /**
     * 设置日志记录表
     * @param $table
     */
    public function setLogTable( $table )
    {
        self::$_log_table   = $table;
    }

    /**
     * 写入数据日志
     * @param $event
     */
    public static function log( $event )
    {
        Yii::$app->getDb()->createCommand()->insert(self::getLogTable($event), [
            'table'             => self::getTable( $event ),
            'unique_key'        => self::getUniqueKey( $event ),
            'uri'               => self::getUri(),
            'action'            => self::getAction(),
            'type'              => self::getType( $event ),
            'prev_data'         => self::getPrevData( $event ),
            'cur_data'          => self::getCurData( $event ),
            'process_time'      => self::getProcessTime(),
            'process_user'      => self::getProcessUser(),
            'process_ip'        => self::getProcessIp(),
            'process_user_agent'=> self::getProcessUserAgent(),
        ])->execute();
    }

    /**
     * 获取事件触发的数据表
     * @param $event
     * @return mixed
     */
    public static function getTable( $event )
    {
        return $event->sender->tableName();
    }

    /**
     * 获取事件触发的数据行唯一标示
     * @param $event
     * @return string
     */
    public static function getUniqueKey( $event )
    {
        $unique_key = $event->sender->getPrimaryKey();
        if(is_array( $unique_key ))
        {
            $tmp_keys   = [];
            foreach( $unique_key AS $name => $value )
            {
                $tmp_keys   = "{$name}_{$value}";
            }
            $unique_key = implode('_', $tmp_keys);
        }
        return $unique_key;
    }

    /**
     * 获取事件触发的url
     * @param $event
     * @return string
     */
    public static function getUri()
    {
        return  Yii::$app->getRequest()->getIsConsoleRequest() == TRUE
            ?   Yii::$app->getRequest()->getScriptFile() . ' ' . implode(' ',Yii::$app->getRequest()->getParams())
            :   Yii::$app->getRequest()->getUrl();
    }

    /**
     * 获取事件触发的controller@action
     * @param $event
     * @return string
     */
    public static function getAction()
    {
        return Yii::$app->requestedAction->controller->className()."@".Yii::$app->requestedAction->actionMethod;
    }

    /**
     * 获取触发事件类型
     * @param $event
     * @return null|string
     */
    public static function getType( $event )
    {
        $type   = NULL;
        if( $event->name == 'afterInsert')      $type   = 'INSERT';
        else if( $event->name == 'afterUpdate') $type   = 'UPDATE';
        else if( $event->name == 'afterDelete') $type   = 'DELETE';
        return $type;
    }

    /**
     * 获取动作前数据信息
     * @param $event
     * @return string
     */
    public static function getPrevData( $event )
    {
        $prev_data  = $event->sender->getOldAttributes();
        foreach( $prev_data AS $key => $value )
        {
            $prev_data[$key]    = isset( $event->changedAttributes[$key] )
                                ? $event->changedAttributes[$key]
                                : $value;
        }
        return json_encode( $prev_data );
    }

    /**
     * 获取动作以后的数据信息
     * @param $event
     * @return mixed
     */
    public static function getCurData( $event )
    {
        return json_encode(ArrayHelper::toArray($event->sender));
    }

    /**
     * 获取事件触发时间
     * @return Expression
     */
    public static function getProcessTime()
    {
        return new Expression('UNIX_TIMESTAMP()');
    }

    /**
     * 获取事件触发的用户
     * @return mixed
     */
    public static function getProcessUser()
    {
        if(Yii::$app->getRequest()->getIsConsoleRequest() == TRUE)  return 'SYSTEM';

        $identity_info  = explode('\\',get_class(Yii::$app->getUser()->getIdentity()));
        $user_type      = array_pop( $identity_info );

        return "[{$user_type}]". Yii::$app->getUser()->getIdentity()->getAttribute('username');
    }

    /**
     * 获取触发事件的ip地址
     * @return string
     */
    public static function getProcessIp()
    {
        return  Yii::$app->getRequest()->getIsConsoleRequest() == TRUE
            ?   'SYSTEM-SERVER'
            :   Yii::$app->getRequest()->getUserIP();
    }

    public static function getProcessUserAgent()
    {
        return  Yii::$app->getRequest()->getIsConsoleRequest() == TRUE
            ?   'SYSTEM-SERVER'
            :   Yii::$app->getRequest()->getUserAgent();
    }

    /**
     * 日志数据表
     * @param $event
     * @return string
     */
    public static function getLogTable( $event )
    {
        if(!empty( $event->sender->log_table )) self::$_log_table  = $event->sender->log_table;
        return self::$_log_table;
    }
}
