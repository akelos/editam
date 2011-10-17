<?php

# Author Bermi Ferrer - MIT LICENSE

defined('EDITAM_COMPILED_TEMPLATES_PATH') ? null : define('EDITAM_COMPILED_TEMPLATES_PATH', AK_TMP_DIR);


class BaseBehavior extends AkObserver 
{
    public $Page;
    public $Controller;
    public $Request;
    public $Response;
    private $_edit_mode = false;
    private $_editor_js_included = false;

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
        $result = '';
        if($part = $this->Page->getFilteredPart($name, $use_inherited_if_unavailable)){
            $result = $this->render($part);
            if($this->isInPlaceEditorEnabled()){
                $this->injectInPlaceEditorCode($result, $name, $use_inherited_if_unavailable);
            }
        }
        return $result;
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
        return array('Status' => 200, 'Content-Type' => 'text/html; charset='.AK_CHARSET);
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

    public function enableInPlaceEditor()
    {
        $this->_edit_mode = true;
    }
    
    public function isInPlaceEditorEnabled()
    {
        return $this->_edit_mode;
    }
    
    public function injectInPlaceEditorCode(&$result, $name, $use_inherited_if_unavailable = false)
    {
        $PartInstance = $this->Page->getPartToRender($name, $use_inherited_if_unavailable);
        $page_id = empty($PartInstance->inherited) ? $this->Controller->Page->getId() : $PartInstance->Page->getId();

        $url_options = array(
        'controller' => 'page',
        'module' => 'editam',
        'action' => 'edit',
        'id' => $page_id,
        'anchor' => 'part-id-'.$PartInstance->getId()
        );
        
        if($PartInstance->get('name') == 'body'){
            unset($url_options['anchor']);
        }
        
        $url = $this->Controller->urlFor($url_options);

        $result = '<span class="hoverable editable" id="editable-part-'.$PartInstance->id.'">'.

        '<span class="editam-part-menu">'.
        ( empty($PartInstance->inherited) ?
        '<a href="'.$url.'" class="edit-button" id="edit-part-'.$PartInstance->id.'">'.$this->Controller->t('edit').'</a>' :
        '<a href="'.$url.'" class="edit-button" id="edit-part-'.$PartInstance->id.'">'.$this->Controller->t('edit on %page', array(
        '%page' => $PartInstance->Page->get('slug')
        )).'</a>'
        ).'</span><div id="editam-editor-area"></div>'.
        $result."</span>";
        if(empty($this->_editor_js_included)){
            $this->_editor_js_included = true;
            $result .= $this->Controller->asset_tag_helper->javascript_include_tag('editam/editor');
            $result .= $this->Controller->asset_tag_helper->stylesheet_link_tag('editam/editor');
            $result .= '<span id="editam-editor-header-template" style="display:none">
                    <div id="editam-editor-header">'.
                    '<a href="#" id="editam-editor-enable">'.$this->Controller->t('enable website editor').'</a>'.
                    '<a href="#" id="editam-editor-disable">'.$this->Controller->t('disable website editor').'</a>'.
                    '</div></span>';
        }
    }
}

