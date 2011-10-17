<?php

# Author Bermi Ferrer - MIT LICENSE


defined('EDITAM_IS_MULTILINGUAL') ? null : define('EDITAM_IS_MULTILINGUAL', count(Ak::langs()) > 1);

/**
 * Static function enclosed into the Editam namespace
 */

class Editam
{
    public static function can($action, $Extension, $__set_permissions_statically = false)
    {
        static $_permissions;
        if($__set_permissions_statically && empty($_permissions)){
            // Storing statically the permissions to keep them available on runtime
            $_permissions = $action;
        }
        if(empty($_permissions)){
            return false;
        }
        $extension_id = is_object($Extension) ? $Extension->getId() : $Extension;
        return empty($_permissions[$extension_id]) ? false : in_array($action, $_permissions[$extension_id]);
    }

    public static function settings_for($Extension, $preference_name, $__set_preferences_hack = false)
    {
        static $_preferences;
        if($__set_preferences_hack && empty($_preferences)){
            // Hack for storing statically the preferences to keep them available on runtime
            $_preferences = $Extension;
            return null;
        }
        if(empty($_preferences)){
            return null;
        }
        $extension_id = is_object($Extension) ? $Extension->getId() : $Extension;
        return !isset($_preferences[$extension_id][$preference_name]) ? null : $_preferences[$extension_id][$preference_name];
    }

    public static function isMultilingual()
    {
        return EDITAM_IS_MULTILINGUAL;
    }
}

