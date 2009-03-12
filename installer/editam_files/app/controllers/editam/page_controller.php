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

class Editam_PageController extends EditamController
{
    var $models = 'page,page_part,content_layout,snippet,editam_filter';
    
    var $controller_menu_options = array(
    'Pages'   => array('id' => 'page', 'url'=>array('controller'=>'page', 'action'=>'listing', 'module'=>'editam')),
    'Layouts'   => array('id' => 'content_layout', 'url'=>array('controller'=>'content_layout', 'module'=>'editam')),
    'Snippets'   => array('id' => 'snippet', 'url'=>array('controller'=>'snippet', 'action'=>'manage', 'module'=>'editam')),
    );
    var $controller_selected_tab = 'Pages';
    
    function __construct(){
    	parent::__construct();
    	$this->editam_public_site_url_suffix = AK_EDITAM_PUBLIC_SITE_URL_SUFFIX;
    }
    
    function index()
    {
        $this->redirectToAction('listing');
    }

    function listing()
    {
        if($this->Page){
            $this->Roots = $this->Page->nested_set->getRoots();
            if(empty($this->params['expand']) && empty($this->params['id']) && !empty($this->Roots[0])){
                $this->params['expand'] = $this->Roots[0]->id;
            }
            if (!empty($this->params['id']) && $this->Page->getId() == $this->params['id']){
                $this->Pages = $this->Page->nested_set->getSelfAndAncestors();
                $this->params['expand'] = count($this->Pages) == 1 && $this->Page->nested_set->isRoot() ? $this->Page->id : null;
            }
            if(!empty($this->params['expand']) && $Expand =& $this->Page->find($this->params['expand']) && $Expand->nested_set->countChildren()>0){
                $_child = $Expand->nested_set->getChildren();
                if(!empty($_child)){
                    $Child = array_shift($_child);
                    $this->Pages = $Child->nested_set->getSelfAndAncestors();
                }
            }
            $this->Pages = empty($this->Pages) ? $this->Roots : $this->Pages;
        }
        if(empty($this->Pages)){
            $this->flash['message'] = $this->t('It seems like you don\'t have pages on your site. Please fill in the form below in order to create your site homepage');
            $this->redirectTo(array('action'=>'add','is_first'=>true));
        }
    }

    function list_children()
    {
        $this->layout = false;
        if($this->Request->isAjax()){
            if($this->ParentPage =& $this->Page->find(@$this->params['id'])){
                $this->ParentPage->expanded = true;
                $this->Pages = $this->ParentPage->nested_set->getChildren();
            }else{
                $this->renderNothing(400);
            }
        }
    }

    function move()
    {
        if($this->Request->isAjax()){

            if($this->from =& $this->Page->find(@$this->params['from']) && $this->to =& $this->Page->find(@$this->params['to']) ){
                $this->renderNothing($this->to->moveBeside($this->from, @$this->params['pos']) ? 200 : 400);
            }else{
                $this->renderNothing(400);
            }
        }
    }

    function missing_page()
    {
    }

    function missing_home_page()
    {

    }

    function add()
    {
    }

    function add_child()
    {
        $parent_id = empty($this->params['page']['parent_id']) ? @$this->params['parent_id'] : $this->params['page']['parent_id'];
        if(!$this->ParentPage =& $this->Page->find($parent_id)){
            $this->flash['error'] = $this->t('Could not find the parent page for that will hold your new page.');
            $this->redirectToAction('listing');
            return ;
        }
        $this->params['page']['parent_id'] = $parent_id;
        $this->_getReadyForPageForm();
        $this->_save();
        $this->renderAction('add');
    }

    function edit()
    {
        if (empty($this->params['id']) || empty($this->Page) || $this->Page->isNewRecord()){
            $this->flash_options = array('seconds_to_close' => 10);
            $this->flash['error'] = $this->t('Could not find the page you tried to edit');
            $this->redirectToAction('listing');
            return ;
        }
        $this->_save();
        $this->_getReadyForPageForm();
    }


    function destroy()
    {
        if(!empty($this->params['id']) && $this->Page){
            $this->page =& $this->Page->find($this->params['id']);
            $this->Pages = array($this->page);
            if($this->Request->isPost()){
                $parent_id = $this->page->parent_id;
                $this->page->destroy();

                $this->flash_options = array('seconds_to_close'=>5);
                $this->flash['notice'] = $this->t('Page was successfully deleted');
                $this->redirectTo(array('action' => 'listing','expand'=>$parent_id));
            }
        }else{
            $this->redirectTo(array('action' => 'listing'));
        }
    }

    function _save()
    {
        $is_new_page = !(!empty($this->params['id']) && $this->Page->id == $this->params['id']);
        $this->is_homepage = !empty($this->params['is_first']) || !$is_new_page && $this->Page->isHomepage();

        if(!$this->is_homepage && empty($this->ParentPage)){
            if($this->ParentPage = $this->Page->nested_set->getParent()){
                $this->params['page']['parent_id'] = $this->ParentPage->getId();
            }
        }

        if(!empty($this->params['page'])){
            $this->Page->setAttributes($this->params['page']);
            if ($this->Request->isPost()){
                if(empty($this->params['locale'])){
                    $this->Page->set('locale',Ak::lang());
                }

                $this->Page->created_by = $this->CurrentUser->get('id');
                $this->Page->_controller =& $this;
                $method = $is_new_page ? 'saveWithParts' : 'updateWithParts';
                if($this->Page->{$method}($this->params['part'], empty($this->ParentPage) ? null : $this->ParentPage->getId())){
                    $this->flash_options = array('seconds_to_close'=>5);
                    $this->flash['notice'] = $this->t('Page was successfully %action',array('%action'=>$is_new_page ?'created':'updated'));
                    $this->redirectTo(
                    empty($this->params['continue_editing']) ?
                    array('action' => 'listing', 'id' => $this->Page->getId()) :

                    (
                    (empty($this->params['next']) || $this->params['next'] != 'child') ?
                    array('action' => 'edit', 'id' => $this->Page->getId()) :
                    array('action' => 'add_child', 'parent_id' => $this->Page->getId())
                    )
                    );
                }
            }
        }
    }

    function _getReadyForPageForm()
    {
        //$this->include_wysiwym = true;

        $this->Filters = array_merge(array($this->t(' -- none -- ')=>''),
        array_flip(EditamFilter::getAvailableFilters()));

        $this->Layouts = $this->ContentLayout->collect($this->ContentLayout->find(),'name','id');
        if(!empty($this->Layouts) && empty($this->is_homepage)){
            $this->Layouts = array_merge(array($this->t(' -- inherit -- ')=>''), $this->Layouts);
        }

        $this->Behaviours = array_merge(array($this->t(' -- none -- ')=>''),
        array_flip($this->page->getAvailableBehaviours()));

        $this->Statuses = array_flip($this->page->getAvailableStatuses());

        $this->index = 1;
        if($this->Page->isNewRecord()){
            empty($this->Page->parts) ?  $this->Page->part->add(new PagePart(array('name'=>'body'))) : null;
        }else{
            $this->Page->part->load();
            if(!empty($this->Page->parent_id)){
                $this->ParentPage =& $this->Page->find($this->Page->parent_id);
            }

        }
    }

    function convert_content()
    {
        $this->layout = false;

        if(!empty($this->params['content'])){
            $from = empty($this->params['from']) ? 'html' : $this->params['from'];
            $to = empty($this->params['to']) ? 'html' : $this->params['to'];
            $filters = array_keys(EditamFilter::getAvailableFilters());
            array_push($filters, 'html');
            if($from != $to && in_array($from, $filters) && in_array($to, $filters)){
                if($from != 'html'){
                    $converted = @Ak::convert($from, 'html', $this->params['content']);
                    if(empty($converted)){
                        $this->renderText($this->params['content']);
                        return ;
                    }else{
                        $this->params['content'] = $converted;
                    }
                }
                $converted = trim(@Ak::convert('html', $to, $this->params['content']));
                $converted = empty($converted) ? $this->params['content'] : $converted;
                $this->renderText($converted);
                return ;
            }
            $this->renderText($this->params['content']);
        }
        $this->renderNothing();

    }

    function switch_behaviour()
    {
        $this->layout = false;
        $to = @$this->params['to'];
        $from = @$this->params['from'];
        $available_behaviours = $this->Page->getAvailableBehaviours();

        $to = !empty($to) && !empty($available_behaviours[$to]) ? $to : '';
        $from = !empty($from) && !empty($available_behaviours[$from]) ? $from : '';

        $result = '';
        if($to != $from){
            if(!empty($from)){
                $from_class_name = AkInflector::camelize($from).'Behaviour';
                require_once(AK_APP_DIR.DS.'behaviours'.DS.AkInflector::underscore($from_class_name).'.php');
                $FromBehaviour =& new $from_class_name();
                $result .= $FromBehaviour->disable_behaviour_html($this);
            }
            if(!empty($to)){
                $to_class = AkInflector::camelize($to).'Behaviour';
                require_once(AK_APP_DIR.DS.'behaviours'.DS.AkInflector::underscore($to_class).'.php');
                $ToBehaviour =& new $to_class();
                $result .= $ToBehaviour->enable_behaviour_html($this);
            }
            $this->renderText($result, 200);
            return;
        }
        $this->renderNothing();
    }

    function clear_cache()
    {
        $this->Page->clearCachedPages();
        $this->flash_options = array('seconds_to_close' => 10);
        $this->flash['message'] = $this->t('Cache has been cleared successfully.');
        $this->redirectToAction('listing');
    }

}

?>
