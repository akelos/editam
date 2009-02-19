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


defined('EDITAM_IS_MULTILINGUAL') ? null : define('EDITAM_IS_MULTILINGUAL', count(Ak::langs()) > 1);

/**
 * Static function enclosed into the Editam namespace
 */

class Editam
{
    function can($action, $Extension, $__set_permissions_hack = false)
    {
        static $_permissions;
        if($__set_permissions_hack && empty($_permissions)){
            // Hack for storing statically the permissions to keep them available on runtime
            $_permissions = $action;
        }
        if(empty($_permissions)){
            return false;
        }
        $extension_id = is_object($Extension) ? $Extension->getId() : $Extension;
        return empty($_permissions[$extension_id]) ? false : in_array($action, $_permissions[$extension_id]);
    }

    function settings_for($Extension, $preference_name, $__set_preferences_hack = false)
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

    function isMultilingual()
    {
        return EDITAM_IS_MULTILINGUAL;
    }
}

?>