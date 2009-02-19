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

defined('EDITAM_COMPILED_TEMPLATES_PATH') ? null : define('EDITAM_COMPILED_TEMPLATES_PATH', AK_TMP_DIR);
require_once(AK_LIB_DIR.DS.'AkActiveRecord'.DS.'AkObserver.php');

class BaseBehaviour extends AkObserver 
{
    var $Page;
    var $Controller;
    var $Request;
    var $Response;

    function init(&$Controller)
    {
        $this->Controller =& $Controller;
        $this->Page =& $Controller->Page;
        $this->Request =& $Controller->Request;
        $this->Response =& $Controller->Response;
        $this->registerTemplateHandlers();
    }

    function registerTemplateHandlers()
    {
        require_once(AK_MODELS_DIR.DS.'editags.php');
        $this->Controller->Template->_registerTemplateHandler('editags','EditagsTemplateHandler');
    }

    function getPageUrl()
    {
    }

    function hasPageCache()
    {
    }

    function &findPageByUrl($url, $show_unpublished = false)
    {
        $result =& $this->Page->_findByUrl($url, $show_unpublished);
        return $result;
    }

    function &findMissingPageForUrl($url)
    {
        $Page = false;
        if(is_array($url)){
            while (count($url) > 0) {
                if($Page =& $this->Page->_findByUrl($url, false, true)){
                    return $Page;
                }
                array_pop($url);
            }
        }
        return $Page;
    }



    function renderPart($name, $use_inherited_if_unavailable = false)
    {
        if($part = $this->Page->getFilteredPart($name, $use_inherited_if_unavailable)){
            return $this->render($part);
        }
        return '';
    }

    function isPageVirtual()
    {
        return false;
    }

    function process()
    {
        $this->Page->part->load();
        $this->renderPage();
    }

    function renderPage()
    {
        $this->Controller->title = $this->Page->title;
        $this->Controller->url = AK_CURRENT_URL;
        $this->Controller->editags_helper->_instantiateEditagsHelpers();

        $this->enableCahe();
        $this->enableGzCompression();
        
        $this->Controller->render(array(
        'inline' => $this->Page->getLayout(),
        'type'=>'editags'
        ));
        
        $this->Controller->Response->addHeader($this->getPageHeaders());
        
        $this->saveCache();
    }

    function enableCahe()
    {
        if($this->Page->canUsePageCache()){
            $this->Controller->_enableCache();
        }
    }

    function saveCache()
    {
        if($this->Page->canUsePageCache()){
            $this->Controller->_saveCache();
        }
    }

    function enableGzCompression()
    {
        if(EDITAM_COMPRESS_OUTPUT &&
        function_exists('ob_gzhandler') &&
        preg_match('/gzip|deflate/', @$_SERVER['HTTP_ACCEPT_ENCODING'])){
            ob_start('ob_gzhandler');
        }
    }

    function render($code)
    {
        return $this->Controller->renderToString(array('inline' => $code, 'type'=>'editags'));
    }


    function getChildUrl()
    {
    }

    function getPageHeaders()
    {
        return array('Status' => 200 );
    }


    function canUsePageCache()
    {
        return true;
    }


    function getTemplateLocals()
    {
        return array();
    }
    
    function enable_behaviour_html()
    {
    }
    
    function disable_behaviour_html()
    {
    }
}

?>