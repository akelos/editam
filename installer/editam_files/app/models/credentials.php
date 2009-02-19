<?php

// +----------------------------------------------------------------------+
// Editam is a content management platform developed by Akelos Media, S.L.|
// Copyright (C) 2006 - 2007 Akelos Media, S.L.                           |
//                                                                        |
// This program is free software; you can redistribute it and/or modify   |
// it under the terms of the GNU General Public License version 3 as      |
// published by the Free Software Foundation.                             |
//                                                                        |
// This program is distributed in the hope that it will be useful, but    |
// WITHOUT ANY WARRANTY; without even the implied warranty of             |
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                   |
// See the GNU General Public License for more details.                   |
//                                                                        |
// You should have received a copy of the GNU General Public License      |
// along with this program; if not, see http://www.gnu.org/licenses or    |
// write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth |
// Floor, Boston, MA 02110-1301 USA.                                      |
//                                                                        |
// You can contact Akelos Media, S.L. headquarters at                     |
// C/ Pasodoble Amparito Roca, 6, 46240 - Carlet (Valencia) - Spain       |
// or at email address contact@akelos.com.                                |
//                                                                        |
// The interactive user interfaces in modified source and object code     |
// versions of this program must display Appropriate Legal Notices, as    |
// required under Section 5 of the GNU General Public License version 3.  |
//                                                                        |
// In accordance with Section 7(b) of the GNU General Public License      |
// version 3, these Appropriate Legal Notices must retain the display of  |
// the "Powered by Editam" logo. If the display of the logo is not        |
// reasonably feasible for technical reasons, the Appropriate Legal       |
// Notices must display the words "Powered by Editam".                    |
// +----------------------------------------------------------------------+

Ak::import('editam');

defined('EDITAM_CACHE_PERMISSIONS_ON_SESSION') ? null : define('EDITAM_CACHE_PERMISSIONS_ON_SESSION', false);

require_once(AK_LIB_DIR.DS.'AkActiveRecord.php');
require_once(AK_APP_DIR.DS.'shared_model.php');
require_once(AK_MODELS_DIR.DS.'user.php');

class Credentials
{
    var $_credentials_key = '__credentials';

    function Credentials()
    {
        if($this->hasCredentials()){
            $this->credentials = $this->getCredentials();
            foreach ($this->credentials as $k=>$v){
                $this->$k = $v;
            }
        }
    }

    function authenticate($username, $password)
    {
        $User =& new User();
        $User->set('password', @$password);
        $User->encryptPassword();
        if ($User =& $User->findFirstBy('login AND password AND is_enabled', @$username, $User->get('password'), true)){
            $User->set('last_login_at', Ak::getDate());
            $User->save();

            $this->setCredentials($User->getAttributes());
            Editam::can($this->_loadPermissions(), null, true);

            return true;
        }
        return false;
    }

    function setCredentials($user_details)
    {
        $_SESSION[$this->_credentials_key] = $user_details;
    }

    function getCredentials()
    {
        return $_SESSION[$this->_credentials_key];
    }

    function getAttribute($attribute)
    {
        return isset($_SESSION[$this->_credentials_key][$attribute]) ? $_SESSION[$this->_credentials_key][$attribute] : null;
    }

    function get($attribute)
    {
        return $this->getAttribute($attribute);
    }

    function setAttribute($attribute, $value)
    {
        $_SESSION[$this->_credentials_key][$attribute] = $value;
    }

    function set($attribute, $value)
    {
        $this->setAttribute($attribute, $value);
    }

    function hasCredentials()
    {
        return !empty($_SESSION[$this->_credentials_key]);
    }

    function revokeCredentials()
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
    function can($action, $Extension)
    {
        return Editam::can($action, $Extension);
    }

    function _loadPermissions()
    {
        if(EDITAM_CACHE_PERMISSIONS_ON_SESSION && isset($_SESSION[$this->_credentials_key]['__permissions'])){
            $this->_permissions = $_SESSION[$this->_credentials_key]['__permissions'];
        }else{
            $this->_permissions = array();
            if (!empty($this->role_id)) {
                require_once(AK_MODELS_DIR.DS.'permission.php');
                $Permission =& new Permission();
                if ($Permissions =& $Permission->findAllBy('role_id', $this->role_id)){
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


?>