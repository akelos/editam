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

class Editam_SnippetController extends EditamController
{
//    var $app_helpers = 'editags,layout';
    
    var $selected_tab = 'CMS';
    
    var $controller_menu_options = array(
    'Pages'   => array('id' => 'page', 'url'=>array('controller'=>'page', 'action'=>'listing', 'module'=>'editam')),
    'Layouts'   => array('id' => 'content_layout', 'url'=>array('controller'=>'content_layout', 'module'=>'editam')),
    'Snippets'   => array('id' => 'snippet', 'url'=>array('controller'=>'snippet', 'action'=>'manage', 'module'=>'editam')),
    );
    var $controller_selected_tab = 'Snippets';

    var $flash_options = array('seconds_to_close' => 10);
	
    function manage(){
    	$this->redirectToAction('listing');	
    }
    
    function index()
    {
        $this->redirectToAction('listing');
    }

    function listing()
    {
        $this->snippet_pages = $this->pagination_helper->getPaginator($this->Snippet, array('items_per_page' => 10));
        
        if (!$this->snippets = $this->Snippet->find('all', $this->pagination_helper->getFindOptions($this->Snippet))){
            $this->flash_options = array('seconds_to_close' => 10);
            $this->flash['notice'] = $this->t('It seems like you don\'t have Snippets on your site. Please fill in the form below in order to create your first snippet.');
            $this->redirectTo(array('action' => 'add'));
        }
    }

    function add()
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

    function edit()
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

    function _getReadyForForm()
    {
        $this->Filters = array_merge(array($this->t(' -- none -- ')=>''),
        array_flip(EditamFilter::getAvailableFilters()));
    }

    function _redirectAfterSaving()
    {
        $this->flash_options = array('seconds_to_close'=>5);
        $this->redirectTo(empty($this->params['continue_editing']) ?
                array('action' => 'listing') : array('action' => 'edit', 'id' => $this->Snippet->getId()));
    }
    
    function destroy()
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

?>