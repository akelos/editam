<?php

# Author Bermi Ferrer - MIT LICENSE

defined('EDITAM_CACHE_PERMISSIONS_ON_SESSION') ? null : define('EDITAM_CACHE_PERMISSIONS_ON_SESSION', false);

class Credentials
{
    public $_credentials_key = '__credentials';

    public function Credentials()
    {
        if($this->hasCredentials()){
            $this->credentials = $this->getCredentials();
            foreach ($this->credentials as $k=>$v){
                $this->$k = $v;
            }
        }
    }

    public function authenticate($username, $password)
    {
        $User = new User();
        $User->set('password', @$password);
        $User->encryptPassword();
        if ($User = $User->findFirstBy('login AND password AND is_enabled', @$username, $User->get('password'), true, array('default'=>false))){
            $User->set('last_login_at', Ak::getDate());
            $User->save();

            $this->setCredentials($User->getAttributes());
            Editam::can($this->_loadPermissions(), null, true);

            return true;
        }
        return false;
    }

    public function setCredentials($user_details)
    {
        $_SESSION[$this->_credentials_key] = $user_details;
    }

    public function getCredentials()
    {
        return $_SESSION[$this->_credentials_key];
    }

    public function getAttribute($attribute)
    {
        return isset($_SESSION[$this->_credentials_key][$attribute]) ? $_SESSION[$this->_credentials_key][$attribute] : null;
    }

    public function get($attribute)
    {
        return $this->getAttribute($attribute);
    }

    public function setAttribute($attribute, $value)
    {
        $_SESSION[$this->_credentials_key][$attribute] = $value;
    }

    public function set($attribute, $value)
    {
        $this->setAttribute($attribute, $value);
    }

    public function hasCredentials()
    {
        return !empty($_SESSION[$this->_credentials_key]);
    }

    public function revokeCredentials()
    {
        if($this->hasCredentials()){
            $this->credentials = $this->getCredentials();
            foreach ($this->credentials as $k=>$v){
                unset($this->$k);
            }
            unset($this->credentials);
            unset($_SESSION[$this->_credentials_key]);
        }
    }


    /**
     * Can user perform an action
     *
     * can('Delete posts', $Blog);
     * can('Delete posts', 12);
     */
    public function can($action, $Extension)
    {
        return Editam::can($action, $Extension);
    }

    public function _loadPermissions()
    {
        if(EDITAM_CACHE_PERMISSIONS_ON_SESSION && isset($_SESSION[$this->_credentials_key]['__permissions'])){
            $this->_permissions = $_SESSION[$this->_credentials_key]['__permissions'];
        }else{
            $this->_permissions = array();
            if (!empty($this->role_id)) {
                $Permission = new Permission();
                if ($Permissions = $Permission->findAllBy('role_id', $this->role_id, array('default'=>false))){
                    foreach (array_keys($Permissions) as $k) {
                        $this->_permissions[$Permissions[$k]->extension_id][] = $Permissions[$k]->get('name');
                    }
                }
            }
            if(EDITAM_CACHE_PERMISSIONS_ON_SESSION){
                $_SESSION[$this->_credentials_key]['__permissions'] = $this->_permissions;
            }
        }
        return $this->_permissions;
    }
}

