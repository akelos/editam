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

defined('EDITAM_CACHE_PREFERENCES_ON_SESSION') ? null : define('EDITAM_CACHE_PREFERENCES_ON_SESSION', true);

class SitePreference extends ActiveRecord
{
    function set($attribute, $value)
    {
        if($attribute == 'value' && !empty($this->is_core)){
            require_once(AK_MODELS_DIR.DS.'preferences'.DS.'core_preferences.php');
            $PreferenceHandler =& new CorePreferences();
            $setter_method_name = 'set'.AkInflector::camelize($this->name);
            if(isset($PreferenceHandler) && method_exists($PreferenceHandler, $setter_method_name)){
                return $PreferenceHandler->$setter_method_name($this, $value);
            }
        }
        return parent::set($attribute, $value);
    }

    function get($attribute)
    {
        if($attribute == 'value' && !empty($this->is_core)){
            require_once(AK_MODELS_DIR.DS.'preferences'.DS.'core_preferences.php');
            $PreferenceHandler =& new CorePreferences();
            $getter_method_name = 'get'.AkInflector::camelize($this->name);
            if(isset($PreferenceHandler) && method_exists($PreferenceHandler, $getter_method_name)){
                return $PreferenceHandler->$getter_method_name($this, $attribute);
            }
        }

        return parent::get($attribute);
    }

    function _loadPreferences()
    {
        if(EDITAM_CACHE_PREFERENCES_ON_SESSION && isset($_SESSION['__preferences'])){
            $this->_preferences = $_SESSION['__preferences'];
        }else{
            $this->_preferences = array();

            if($Preferences =& $this->find('all')){
                foreach (array_keys($Preferences) as $k){
                    $id = !empty($Preferences[$k]->is_core) ? 'core' : $Preferences[$k]->extension_id;
                    $this->_preferences[$id][$Preferences[$k]->name] = $Preferences[$k]->get('value');
                }
            }
            if(EDITAM_CACHE_PREFERENCES_ON_SESSION){
                $_SESSION['__preferences'] = $this->_preferences;
            }
        }
        return $this->_preferences;
    }
}

?>
