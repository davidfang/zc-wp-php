<?php
/**
 * Created by David
 * User: David.Fang
 * Date: 2015/7/20
 * Time: 15:27
 */

namespace app\models;


class LoginForm extends \common\models\LoginForm {
    private $_user = false;
    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = AdminUser::findByUsername($this->username);
        }

        return $this->_user;
    }
}