<?php

# Author Bermi Ferrer - MIT LICENSE

class Editam_ContentLayoutController extends EditamController
{
    public $admin_selected_tab = 'CMS';
    
    public $controller_menu_options = array(
    'Pages'   => array('id' => 'page', 'url'=>array('controller'=>'page', 'action'=>'listing', 'module'=>'editam')),
    'Layouts'   => array('id' => 'content_layout', 'url'=>array('controller'=>'content_layout', 'module'=>'editam')),
    'Snippets'   => array('id' => 'snippet', 'url'=>array('controller'=>'snippet', 'action'=>'manage', 'module'=>'editam')),
    'Preferences'   => array('id' => 'preferences', 'url'=>array('controller'=>'preferences', 'action'=>'setup', 'module'=>'editam'))
    );
    public $controller_selected_tab = 'Layouts';
    
    public function index()
    {
        $this->redirectToAction('listing');
    }

    public function listing()
    {
        $this->content_layout_pages = $this->pagination_helper->getPaginator($this->ContentLayout, array('items_per_page' => 10));
        
        if (!$this->content_layouts = $this->ContentLayout->find('all', $this->pagination_helper->getFindOptions($this->ContentLayout))){
            $this->flash_options = array('seconds_to_close' => 10);
            $this->flash['notice'] = $this->t('It seems like you don\'t have Layouts on your site. Please fill in the form below in order to create your first layout.');
            $this->redirectTo(array('action' => 'add'));
        }
    }

    public function add()
    {
        (!empty($this->params['id'])) ? $this->redirectTo(array('action' => 'add', 'id' => NULL)) : null;
        
        if (!empty($this->params['content_layout'])){
            $this->ContentLayout->setAttributes($this->params['content_layout']);
            if ($this->Request->isPost() && $this->ContentLayout->save()){
                $this->flash['notice'] = $this->t('Layout was successfully created.');
                $this->_redirectAfterSaving();
            }
        }
    }

    public function edit()
    {
        if (empty($this->params['id']) || empty($this->ContentLayout->id)){
            $this->flash['notice'] = $this->t('Invalid layout or not found.');
            $this->redirectTo(array('action' => 'listing'));
        }
        
        if(!empty($this->params['content_layout']) && !empty($this->params['id'])){
            $this->content_layout->setAttributes($this->params['content_layout']);
            if($this->Request->isPost() && $this->content_layout->save()){
                $this->flash['notice'] = $this->t('Layout was successfully updated.');
                $this->_redirectAfterSaving();
            }
        }
    }

    public function destroy()
    {
        if (empty($this->params['id']) || empty($this->ContentLayout->id)){
            $this->flash['notice'] = $this->t('Invalid layout or not found.');
            $this->redirectTo(array('action' => 'listing'));
        }
        
        if (!empty($this->params['id'])){
            $this->content_layout = $this->ContentLayout->find($this->params['id']);
            if($this->Request->isPost()){
                $this->content_layout->destroy();
                if($this->content_layout->reload()){
                    $this->flash['message'] = $this->t('Could not delete Layout. This layout might be in use by one or more pages');
                }else{
                    $this->flash['message'] = $this->t('Layout deleted successfully.');
                }
                
                $this->redirectTo(array('action' => 'listing'));
            }
        }
    }

    public function _redirectAfterSaving()
    {
        $this->flash_options = array('seconds_to_close'=>5);
        $this->redirectTo(
        empty($this->params['continue_editing']) ?
        array('action' => 'listing') :
        array('action' => 'edit', 'id' => $this->ContentLayout->getId()));
    }
}

