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

class Editam_ContentLayoutController extends EditamController
{
    var $admin_selected_tab = 'CMS';
    
    var $controller_menu_options = array(
    'Pages'   => array('id' => 'page', 'url'=>array('controller'=>'page', 'action'=>'listing', 'module'=>'editam')),
    'Layouts'   => array('id' => 'content_layout', 'url'=>array('controller'=>'content_layout', 'module'=>'editam')),
    'Snippets'   => array('id' => 'snippet', 'url'=>array('controller'=>'snippet', 'action'=>'manage', 'module'=>'editam')),
    );
    var $controller_selected_tab = 'Layouts';
    
    function index()
    {
        $this->redirectToAction('listing');
    }

    function listing()
    {
        $this->content_layout_pages = $this->pagination_helper->getPaginator($this->ContentLayout, array('items_per_page' => 10));
        
        if (!$this->content_layouts = $this->ContentLayout->find('all', $this->pagination_helper->getFindOptions($this->ContentLayout))){
            $this->flash_options = array('seconds_to_close' => 10);
            $this->flash['notice'] = $this->t('It seems like you don\'t have Layouts on your site. Please fill in the form below in order to create your first layout.');
            $this->redirectTo(array('action' => 'add'));
        }
    }

    function add()
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

    function edit()
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

    function destroy()
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

    function _redirectAfterSaving()
    {
        $this->flash_options = array('seconds_to_close'=>5);
        $this->redirectTo(
        empty($this->params['continue_editing']) ?
        array('action' => 'listing') :
        array('action' => 'edit', 'id' => $this->ContentLayout->getId()));
    }
}

?>