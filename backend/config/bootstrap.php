<?php
/************************************************************************************************************************************
 * 数据修改日志
 ***********************************************************************************************************************************/
\yii\base\Event::on(
    \yii\db\ActiveRecord::className(),
    \yii\db\ActiveRecord::EVENT_AFTER_INSERT,[common\components\DatabaseLog::className(),'log']
);
\yii\base\Event::on(
    \yii\db\ActiveRecord::className(),
    \yii\db\ActiveRecord::EVENT_AFTER_UPDATE,[common\components\DatabaseLog::className(),'log']
);
\yii\base\Event::on(
    \yii\db\ActiveRecord::className(),
    \yii\db\ActiveRecord::EVENT_AFTER_DELETE,[common\components\DatabaseLog::className(),'log']
);
/***********************************************************************************************************************************/