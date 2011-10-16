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

defined('EDITAM_CSS_MEDIA_TYPES') ? null : define('EDITAM_CSS_MEDIA_TYPES', 'screen|print|handheld|tv|projection|all|aural|braille|embossed|tty');

require_once(AK_LIB_DIR.DS.'AkActionView'.DS.'AkActionViewHelper.php');
require_once(AK_LIB_DIR.DS.'AkActionView'.DS.'AkHelperLoader.php'); 

Ak::import('snippet,editags');

class EditagsHelper extends AkActionViewHelper
{

    function can($action, $Extension)
    {
        return Editam::can($action, $Extension);
    }

    function settings_for($Extension, $preference_name)
    {
        return Editam::settings_for($Extension, $preference_name);
    }

    function init()
    {
        $this->Page =& $this->_controller->Page;
        $this->Snippet =& new Snippet();
    }

    function title($Page = null)
    {
        $Page = empty($Page) ? $this->Page : $Page;
        return $Page->get('title');
    }

    function slug($Page = null)
    {
        $Page = empty($Page) ? $this->Page : $Page;
        return $Page->get('slug');
    }

    function content($Page = null)
    {
        $Page = empty($Page) ? $this->Page : $Page;
        if(is_object($Page)){
            return $Page->_Behaviour->renderPart('body');
        }elseif (is_array($Page) && !empty($Page['part'])){
            return $this->_controller->Page->_Behaviour->renderPart($Page['part'], !empty($Page['inherit']));
        }
    }

    function part($Page = null, $part_name = null, $inherit = false)
    {
        $args = func_get_args();
        if(count($args) != 3){
            $Page = null;
            $part_name = $args[0];
            $inherit = !empty($args[1]);
        }
        $Page = empty($Page) ? $this->Page : $Page;

        return $Page->_Behaviour->renderPart($part_name, $inherit);
    }

    function breadcrumb($LastPage = null)
    {
        $LastPage = empty($LastPage) ? $this->Page : $LastPage;
        if(!empty($LastPage->nested_set)){
            return join(' <span class="delimiter">&gt;</span> ',array_values($LastPage->collect($LastPage->nested_set->getSelfAndAncestors(),'slug','title')));
        }
    }


    function snippet($name, $local_assigns = array())
    {
        if($Snippet =& $this->_controller->Snippet->findFirstBy('name',$name)){
            return $Snippet->render($this->_controller, $local_assigns);
        }
    }

    function site_name()
    {
        return $this->_controller->t(Editam::settings_for('core','site_title'));
    }

    function mail_to()
    {
        $args = func_get_args();
        return call_user_func_array(array(&$this->_controller->url_helper,'mail_to'), $args);
    }

    /**
     * Stylesheets include tags will be retrieved from the stylesheets folder at current theme.
     * 
     * By default Editam looks for a theme_name.css file. If that file is found, ONLY thet stylesheet will
     * be included. If the theme_name.css file does not exist it will look for including existing stylesheets
     * that match a WC3 media type (http://www.w3.org/TR/REC-CSS2/media.html) like:
     * all.css, aural.css, braille.css, embossed.css, handheld.css, print.css, 
     * projection.css, screen.css, tty.css or tv.css 
     */
    function theme_stylesheet()
    {
        $result = '';
        $theme_name = $this->theme();
        $theme_path = 'themes'.DS.$theme_name.DS.'stylesheets';
        if(file_exists(AK_PUBLIC_DIR.DS.$theme_path.DS.$theme_name.'.css')){
            $result .= $this->_controller->asset_tag_helper->stylesheet_link_tag($this->_computePublicPath($theme_name, $theme_path, 'css'));
        }else{
            $stylesheets = AkFileSystem::dir(AK_PUBLIC_DIR.DS.$theme_path);
            foreach ($stylesheets as $stylesheet){
                if(preg_match('/^('.EDITAM_CSS_MEDIA_TYPES.')\.css$/',$stylesheet)){

                    $result .= $this->_controller->asset_tag_helper->stylesheet_link_tag($this->_computePublicPath($stylesheet, $theme_path, 'css'), array('media'=>str_replace('.css','',$stylesheet)));
                }
            }
        }
        return $result;
    }

    function theme_javascript()
    {
        $result = '';
        $js_name = $this->theme();
        $js_path = 'themes'.DS.$js_name.DS.'javascripts';
        if(file_exists(AK_PUBLIC_DIR.DS.$js_path.DS.$js_name.'.js')){
            $result .= $this->_controller->asset_tag_helper->javascript_include_tag($this->_computePublicPath($js_name, $js_path, 'js'));
        }else{
            $javascripts = AkFileSystem::dir(AK_PUBLIC_DIR.DS.$js_path);
            foreach ($javascripts as $javascript){
                $result .= $this->_controller->asset_tag_helper->javascript_include_tag($this->_computePublicPath($javascript, $js_path, 'js'));
            }
        }
        return $result;
    }

    function theme()
    {
        return Editam::settings_for('core','theme');
    }

    function class_for($element_id)
    {
        $classes = $this->_getHtmlElementClasses();
        return isset($classes[$element_id]) ? $classes[$element_id] : '';
    }
    
    function _getHtmlElementClasses($set_classes = null)
    {
        static $classes = null;
        if(empty($set_classes) && is_null($classes)){
            $classes_file = AK_PUBLIC_DIR.DS.'themes'.DS.$this->theme().DS.'stylesheets'.DS.'classes.txt';
            if(file_exists($classes_file)){
                $classes = Ak::convert('yaml','array', AkFileSystem::file_get_contents($classes_file));
            }else{
                $classes = false;
            }
        }elseif(!empty($set_classes)){
            $classes = $set_classes;
        }
        return $classes;
    }

    function _instantiateEditagsHelpers()
    {
        $available_helpers = $this->_getEditagsHelperMethods();
        $helper_names = array_unique(array_values($available_helpers));

        foreach ($helper_names as $underscored_helper_name){
            $helper_class_name = AkInflector::camelize($underscored_helper_name);
            $this->_controller->$underscored_helper_name =& new $helper_class_name(&$this->_controller);
            if(method_exists($this->_controller->$underscored_helper_name,'setController')){
                $this->_controller->$underscored_helper_name->setController(&$this->_controller);
            }
            if(method_exists($this->_controller->$underscored_helper_name, 'init')){
                $this->_controller->$underscored_helper_name->init();
            }
        }
        $this->_registerEditagsHelperFuntions($available_helpers);
    }

    function _getEditagsHelperMethods()
    {
        $available_helpers = array();
        EditagsHelper::_addAkelosHelperMethods_($available_helpers);
        $helper_files = AkFileSystem::dir(EDITAGS_HELPERS_DIR, array('dirs'=>false));
        foreach ($helper_files as $helper_file){
            $underscored_helper_name = substr($helper_file,0,-4);
            include_once(EDITAGS_HELPERS_DIR.DS.$helper_file);
            EditagsHelper::_addHelperMethods_($underscored_helper_name, $available_helpers);
        }
        return $available_helpers;
    }

    function _addAkelosHelperMethods_(&$available_helpers)
    {
    	if($underscored_helper_names = AkHelperLoader::getInstantiatedHelperNames()){
        	foreach($underscored_helper_names as $underscored_helper_name){
            	EditagsHelper::_addHelperMethods_($underscored_helper_name, $available_helpers);
        	}
    	}
    }

    function _addHelperMethods_($underscored_helper_name, &$available_helpers)
    {
        $helper_class_name = AkInflector::camelize($underscored_helper_name);
        if(class_exists($helper_class_name)){
            foreach (get_class_methods($helper_class_name) as $method_name){
                if($method_name[0] != '_'){
                    $available_helpers[$method_name] = $underscored_helper_name;
                }
            }
        }
    }

    function _registerEditagsHelperFuntions($available_helpers = array())
    {
        $available_helpers = empty($available_helpers) ? $this->_getEditagsHelperMethods() : $available_helpers;
        defined('EDITAM_AVALABLE_HELPERS') ? null : define('EDITAM_AVALABLE_HELPERS', serialize($available_helpers));
    }

    function _computePublicPath()
    {
        $args = func_get_args();
        $computed_path = call_user_func_array(array(&$this->_controller->asset_tag_helper,'_compute_public_path'), $args);
        return strlen(AK_ASSET_URL_PREFIX) > 1 ? preg_replace('/^('.str_replace('/','\/',AK_ASSET_URL_PREFIX).')/', '', $computed_path) : $computed_path;
    }

}

?>