<?php

\backend\assets\AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language ?>">
<head>
    <meta charset="utf-8"/>
    <title>登录页面 - <?=Yii::$app->params['webname'] ?></title>
    <?php $this->head() ?>
</head>
<body class="login-layout">
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>