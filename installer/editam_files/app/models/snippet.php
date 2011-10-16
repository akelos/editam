<?php

# Author Bermi Ferrer - MIT LICENSE

defined('EDITAM_SNIPPETS_DIRECTORY') ? null : define('EDITAM_SNIPPETS_DIRECTORY', AK_TMP_DIR.DS.'editam'.DS.'snippets');

class Snippet extends ActiveRecord
{
    public $base_path = EDITAM_SNIPPETS_DIRECTORY;

    public function render(&$Controller, $local_assigns = array())
    {
        if($this->get('is_enabled')){
            $Controller->_addVariablesToAssigns();
            $local_assigns = array_merge($local_assigns, $Controller->_assigns);
            extract($local_assigns, EXTR_SKIP);
            ob_start();
            include($this->getPath());
            !empty($shared) ? $Controller->Template->addSharedAttributes($shared) : null;
            return  ob_get_clean();
        }
    }

    public function validate()
    {
        $this->validatesPresenceOf(array('name','description','content'));
        $this->validatesUniquenessOf('name');
    }

    public function beforeSave()
    {
        if(!$this->validatesEditagsField('content', true)){
            $this->addError('content', $this->t('has non secure PHP code'));
            return false;
        }
        AkFileSystem::file_put_contents($this->getPath(), $this->_editags_php);
        return true;
    }

    public function getPath()
    {
        return $this->base_path.DS.$this->get('name').'.php';
    }

}
