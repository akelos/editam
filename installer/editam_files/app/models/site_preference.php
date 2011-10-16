<?php

# Author Bermi Ferrer - MIT LICENSE

defined('EDITAM_CACHE_PREFERENCES_ON_SESSION') ? null : define('EDITAM_CACHE_PREFERENCES_ON_SESSION', true);

class SitePreference extends ActiveRecord
{
    public function set($attribute, $value)
    {
        if($attribute == 'value' && !empty($this->is_core)) {
            $PreferenceHandler = new CorePreferences();
            $setter_method_name = 'set'.AkInflector::camelize($this->name);
            if(isset($PreferenceHandler) && method_exists($PreferenceHandler, $setter_method_name)){
                return $PreferenceHandler->$setter_method_name($this, $value);
            }
        }
        return parent::set($attribute, $value);
    }

    public function get($attribute)
    {
        if($attribute == 'value' && !empty($this->is_core)) {
            $PreferenceHandler = new CorePreferences();
            $getter_method_name = 'get'.AkInflector::camelize($this->name);
            if(isset($PreferenceHandler) && method_exists($PreferenceHandler, $getter_method_name)){
                return $PreferenceHandler->$getter_method_name($this, $attribute);
            }
        }

        return parent::get($attribute);
    }

    public function _loadPreferences()
    {
        if(EDITAM_CACHE_PREFERENCES_ON_SESSION && isset($_SESSION['__preferences'])){
            $this->_preferences = $_SESSION['__preferences'];
        }else{
            $this->_preferences = array();

            if($Preferences = $this->find('all', array('default'=>array()))){
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

