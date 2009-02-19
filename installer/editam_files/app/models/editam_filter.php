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

class EditamFilter
{
    function getFilteredContent($Item)
    {
        $Filter =& EditamFilter::_getFilterInstance($Item->get('filter'));
        if(!is_object($Filter)){
            return $Item->get('content');
        }
        return $Filter->filter($Item->get('content'));
    }

    function &_getFilterInstance($filter_name)
    {
        static $filters;
        if(!empty($filters[$filter_name])){
            return $filters[$filter_name];
        }

        if(empty($filter_name) || !in_array($filter_name, array_keys(EditamFilter::getAvailableFilters()))){
            return $GLOBALS['false'];
        }

        if(!file_exists(AK_APP_DIR.DS.'filters'.DS.$filter_name.'_filter.php')){
            trigger_error(Ak::t('Could not find %filter filter file',array('%filter'=>$filter_name),' editam_filter'), E_USER_WARNING);
            return $GLOBALS['false'];
        }
        require_once(AK_APP_DIR.DS.'filters'.DS.$filter_name.'_filter.php');
        $filter_class_name = AkInflector::camelize($filter_name.'Filter');
        $filters[$filter_name] =& new $filter_class_name();
        return $filters[$filter_name];
    }

    function getAvailableFilters()
    {
        static $filters = array();
        if(empty($filters)){
            if(defined('EDITAM_AVAILABLE_FILTERS')){
                foreach (Ak::toArray(EDITAM_AVAILABLE_FILTERS) as $filter){
                    $filters[$filter] = Ak::t($filter, null , 'editam_filter');
                }
            }else{
                foreach (Ak::dir(AK_APP_DIR.DS.'filters') as $file){
                    if(substr($file,-11) == '_filter.php'){
                        $filter = substr($file,0,-11);
                        $filters[$filter] = Ak::t($filter, null, 'editam_filter');
                    }
                }
            }
            $filters = array_map(array('AkInflector','humanize'), $filters);
        }
        return $filters;
    }

}


?>