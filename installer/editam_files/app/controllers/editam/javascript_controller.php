<?php

# Author Bermi Ferrer - MIT LICENSE

class Editam_JavascriptController extends EditamController
{
    public $layout = false;

    public function framework()
    {
        $this->Response->addHeader(array('Expires'=>gmdate("D, d M Y H:i:s", Ak::getTimestamp() + (60*60*24*365))));
        $this->action_cache();
        $js = AkFileSystem::file_get_contents('public/javascripts/prototype.js');
        $js .= AkFileSystem::file_get_contents('public/javascripts/event_selectors.js');
        $js .= str_replace('function(include)','{});//', AkFileSystem::file_get_contents('public/javascripts/scriptaculous.js'));
        $js .= AkFileSystem::file_get_contents('public/javascripts/builder.js');
        $js .= AkFileSystem::file_get_contents('public/javascripts/effects.js');
        $js .= AkFileSystem::file_get_contents('public/javascripts/dragdrop.js');
        $js .= AkFileSystem::file_get_contents('public/javascripts/controls.js');
        $js .= AkFileSystem::file_get_contents('public/javascripts/file_uploader.js');
        $js .= AkFileSystem::file_get_contents('public/javascripts/editam.js');
        $this->renderText($js);
    }


    public function page_constants()
    {
        $this->js_constants = array();
    }

}