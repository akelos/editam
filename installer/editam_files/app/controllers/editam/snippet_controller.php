<?php

# Author Bermi Ferrer - MIT LICENSE

class Editam_SnippetController extends EditamController
{
    public $admin_selected_tab = 'CMS';
    
    public $controller_menu_options = array(
    'Pages'   => array('id' => 'page', 'url'=>array('controller'=>'page', 'action'=>'listing', 'module'=>'editam')),
    'Layouts'   => array('id' => 'content_layout', 'url'=>array('controller'=>'content_layout', 'module'=>'editam')),
    'Snippets'   => array('id' => 'snippet', 'url'=>array('controller'=>'snippet', 'action'=>'manage', 'module'=>'editam')),
    'Preferences'   => array('id' => 'preferences', 'url'=>array('controller'=>'preferences', 'action'=>'setup', 'module'=>'editam'))
    );
    public $controller_selected_tab = 'Snippets';

    public $flash_options = array('seconds_to_close' => 10);
    
    public function manage(){
        $this->redirectToAction('listing');    
    }
    
    public function index()
    {
        $this->redirectToAction('listing');
    }

    public function listing()
    {
        $this->snippet_pages = $this->pagination_helper->getPaginator($this->Snippet, array('items_per_page' => 10));
        
        if (!$this->snippets = $this->Snippet->find('all', $this->pagination_helper->getFindOptions($this->Snippet))){
            $this->flash_options = array('seconds_to_close' => 10);
            $this->flash['notice'] = $this->t('It seems like you don\'t have Snippets on your site. Please fill in the form below in order to create your first snippet.');
            $this->redirectTo(array('action' => 'add'));
        }
    }

    public function add()
    {
        (!empty($this->params['id'])) ? $this->redirectTo(array('action' => 'add', 'id' => NULL)) : null;
        
        $this->_getReadyForForm();
        if(!empty($this->params['snippet'])){
            $this->Snippet->setAttributes($this->params['snippet']);
            if ($this->Request->isPost() && $this->Snippet->save()){
                $this->flash['notice'] = $this->t('Snippet was successfully created.');
                $this->_redirectAfterSaving();
            }
        }
    }

    public function edit()
    {
        if (empty($this->params['id']) || empty($this->Snippet->id)){
            $this->flash['notice'] = $this->t('Invalid snippet or not found.');
            $this->redirectTo(array('action' => 'listing'));
        }
        
        $this->_getReadyForForm();
        $this->editags_helper->_registerEditagsHelperFuntions();
        
        if (!empty($this->params['snippet'])){
            $this->snippet->setAttributes($this->params['snippet']);
            if($this->Request->isPost() && $this->snippet->save()){
                $this->flash['notice'] = $this->t('Snippet was successfully updated.');
                $this->_redirectAfterSaving();
            }
        }
    }

    public function _getReadyForForm()
    {
        $this->Filters = array_merge(array($this->t(' -- none -- ')=>''),
        array_flip(EditamFilter::getAvailableFilters()));
    }

    public function _redirectAfterSaving()
    {
        $this->flash_options = array('seconds_to_close'=>5);
        $this->redirectTo(empty($this->params['continue_editing']) ?
                array('action' => 'listing') : array('action' => 'edit', 'id' => $this->Snippet->getId()));
    }
    
    public function destroy()
    {
        if (empty($this->params['id']) || empty($this->Snippet->id)){
            $this->flash['notice'] = $this->t('Invalid snippet or not found.');
            $this->redirectTo(array('action' => 'listing'));
        }
        
        if ($this->Request->isPost()){
            $this->flash['notice'] = ($this->snippet->destroy()) ?
                 $this->t('Snippet was successfully removed.') : $this->flash['notice'] = $this->t('Snippet could not be removed.');
             $this->redirectTo(array('action' => 'listing'));
        }
    }

}

