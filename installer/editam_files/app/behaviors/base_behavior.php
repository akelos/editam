<?php

# Author Bermi Ferrer - MIT LICENSE

defined('EDITAM_COMPILED_TEMPLATES_PATH') ? null : define('EDITAM_COMPILED_TEMPLATES_PATH', AK_TMP_DIR);


class BaseBehavior extends AkObserver 
{
    public $Page;
    public $Controller;
    public $Request;
    public $Response;

    public function init($Controller)
    {
        $this->Controller = $Controller;
        $this->Page = $Controller->Page;
        $this->Request = $Controller->Request;
        $this->Response = $Controller->Response;
        $this->registerTemplateHandlers();
    }

    public function registerTemplateHandlers()
    {
        $this->Controller->Template->_registerTemplateHandler('editags','EditagsTemplateHandler');
    }

    public function getPageUrl()
    {
    }

    public function hasPageCache()
    {
    }

    public function &findPageByUrl($url, $show_unpublished = false)
    {
        $result = $this->Page->_findByUrl($url, $show_unpublished);
        return $result;
    }

    public function &findMissingPageForUrl($url)
    {
        $Page = false;
        if(is_array($url)){
            while (count($url) > 0) {
                if($Page = $this->Page->_findByUrl($url, false, true)){
                    return $Page;
                }
                array_pop($url);
            }
        }
        return $Page;
    }



    public function renderPart($name, $use_inherited_if_unavailable = false)
    {
        if($part = $this->Page->getFilteredPart($name, $use_inherited_if_unavailable)){
            return $this->render($part);
        }
        return '';
    }

    public function isPageVirtual()
    {
        return false;
    }

    public function process()
    {
        $this->Page->part->load();
        $this->renderPage();
    }

    public function renderPage()
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

    public function enableCahe()
    {
        if($this->Page->canUsePageCache()){
            $this->Controller->_enableCache();
        }
    }

    public function saveCache()
    {
        if($this->Page->canUsePageCache()){
            $this->Controller->_saveCache();
        }
    }

    public function enableGzCompression()
    {
        if(EDITAM_COMPRESS_OUTPUT &&
        function_exists('ob_gzhandler') &&
        preg_match('/gzip|deflate/', @$_SERVER['HTTP_ACCEPT_ENCODING'])){
            ob_start('ob_gzhandler');
        }
    }

    public function render($code)
    {
        return $this->Controller->renderToString(array('inline' => $code, 'type'=>'editags'));
    }


    public function getChildUrl()
    {
    }

    public function getPageHeaders()
    {
        return array('Status' => 200 );
    }


    public function canUsePageCache()
    {
        return true;
    }


    public function getTemplateLocals()
    {
        return array();
    }
    
    public function enable_behavior_html()
    {
    }
    
    public function disable_behavior_html()
    {
    }
}

