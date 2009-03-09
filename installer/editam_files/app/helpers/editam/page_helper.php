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

if(!defined('EDITAM_SHOW_DELETE_ON_PAGE_LISTING')){
	define('EDITAM_SHOW_DELETE_ON_PAGE_LISTING',true);
}

class PageHelper extends AkActionViewHelper
{
    var $expanded_ids = array();

    function cancel_link($url = array('action' => 'listing'))
    {
        if(!empty($this->_controller->Page->id)){
            $url['id'] = $this->_controller->Page->id;
        }
        return $this->_controller->url_helper->link_to($this->t('Cancel'),$url, array('class'=>'action'));
    }

    function save_button()
    {
        return '<input type="submit" value="'.$this->_controller->t('Save').'" class="primary" />';
    }

    function save_and_continue_button()
    {
        return '<input id="save_and_continue" type="button" '.
        ($this->_controller->Page->get('is_virtual')?'':'title="'.$this->t('Press shift while pressing for saving and adding child').'"').
        ' onmouseover="Page.saveAndContinueCaption()"
        onclick="Page.submitAndContinueEditing($(\'page_form\'));" value="'.$this->_controller->t('Save and continue editing').'" />';
    }

    function confirm_delete()
    {
        return '<input type="submit" value="'.$this->t('Delete').'" class="primary" /> ';
    }

    function link_to_show(&$record)
    {
        return $this->_controller->url_helper->link_to($this->_controller->t('Show'), array('action' => 'show', 'id' => $record->getId()));
    }

    function link_to_edit(&$record)
    {
        return $this->_controller->url_helper->link_to($this->_controller->t('Edit'), array('action' => 'edit', 'id' => $record->getId()));
    }

    function link_to_destroy(&$record)
    {
        return $this->_controller->url_helper->link_to($this->_controller->t('Click here to delete this page'), array('action' => 'destroy', 'id' => $record->getId()), array('class'=>'action destroy'));
    }

    function text_breadcrumb($Page)
    {
        if(!empty($Page->nested_set)){
            return join(' <span class="delimiter">&gt;</span> ',array_values($Page->collect($Page->nested_set->getSelfAndAncestors(),'slug','title')));
        }
    }

    function text_inherited_slug($Page)
    {
        return $Page->getInheritedSlug();
    }


    function reverse_nested_list($Pages, $display_links = true)
    {
        return $this->_get_nested_menu($this->_build_reverse_nested_array($Pages), $display_links);
    }

    function nested_list($Pages, $display_links = true)
    {
        return $this->_get_nested_menu($this->_build_nested_array($Pages), $display_links);
    }

    function _get_nested_menu($Nodes, $display_links = true, $list_for = null)
    {
        $result = '';
        foreach ($Nodes as $Node){
            if(isset($this->expanded_ids[$Node->id])){
                $Node->expanded = true;
            }
            $result .= $this->nested_list_item($Node, $display_links);
        }
        $list_id = is_numeric($list_for) ? 'children_for-'.$list_for : 'tree_root';
        return $this->_controller->tag_helper->content_tag('ul',$result, array('class'=>'page_nodes','id'=> $list_id));
    }

    function _build_reverse_nested_array($Pages, $recursing = false)
    {
        static $seen = array(), $level = 0;
        $result = array();
        if(is_array($Pages)){
            if(empty($this->expanded_ids) && !$recursing){
                $this->expanded_ids = $Pages[0]->collect($Pages,'id','id');
                array_pop($this->expanded_ids);
            }
            $Page = array_shift($Pages);
            if(isset($Page)){
                if(!isset($seen[$Page->id])){
                    $seen[$Page->id] = true;
                    foreach ($Page->nested_set->getSelfAndSiblings() as $m=>$Sibling){
                        $result[$m] = $Sibling;
                        if(!isset($Sibling->children) && $Sibling->id == $Page->id){
                            $result[$m]->children = $this->_build_reverse_nested_array($Pages, true);
                        }
                    }
                }
            }
        }
        return $result;
    }

    function _build_nested_array($Pages, $recursing = false)
    {
        static $seen = array(), $level = 0;
        $result = array();
        if(is_array($Pages)){
            while (!empty($Pages)) {
                $Page = array_shift($Pages);
                if(isset($Page)){
                    if(!isset($seen[$Page->id])){
                        $seen[$Page->id] = true;
                        $result[$level] = $Page;
                        $this_level = $level;
                        $level++;
                        if($Page->nested_set->countChildren()){
                            $result[$this_level]->children = $this->_build_nested_array($Page->nested_set->getChildren(), true);
                        }
                    }
                }
            }
        }
        return $result;
    }

    function nested_list_item($Page, $display_links = true)
    {
        return  $this->_controller->tag_helper->content_tag('li',
        $this->nested_list_content($Page, $display_links),
        array('id'=>"page-$Page->id",
        'class'=>($Page->nested_set->isRoot() ? 'sortable_pages ' : '').
        ($Page->nested_set->countChildren()>0?'parent':'').
        ($Page->is_virtual?' virtual ':'').
        " $Page->status node-$Page->parent_id-$Page->lft-$Page->rgt page_node"));
    }

    function nested_list_content($Page, $display_links = true)
    {

        return  ($display_links?$this->admin_expand_or_collapse_links($Page):'').
        '<span class="page_handler"> </span>'.
        '<span class="page_title '.$Page->behaviour.'">'.
        ($display_links ? $this->_controller->url_helper->link_to($Page->title, array('action' => 'edit', 'id' => $Page->id), array('class'=>$Page->status)) : $Page->title).
        '</span> '.
        (empty($Page->behaviour)?'':"<span class='page_behaviour $Page->behaviour'>(".$this->t(AkInflector::humanize($Page->behaviour)).')</span>').

        ($display_links?$this->admin_page_links($Page).'<span class="page_status '.$Page->status.'">'.$this->t($Page->status).'</span>':'').
        (!empty($Page->children)?$this->_get_nested_menu($Page->children, $display_links, $Page->getId()):'');
    }

    function admin_page_links($Page)
    {
        $url_text = $Page->is_virtual ? '' : $Page->getInheritedSlug(false, false);
        if(!empty($this->_controller->editam_public_site_url_suffix)){
        	$url_text= DS.$this->_controller->editam_public_site_url_suffix.$url_text;
        }
        $url = $this->_controller->site_url.str_replace('//','/', (EDITAM_IS_MULTILINGUAL?'/'.$Page->locale.'/':'').$url_text);
        return 
        // View link
        ($Page->is_virtual ? '': $this->_controller->url_helper->link_to($url_text,$url, array('class'=>'view action'))).' '.
        
        // Edit link
        $this->_controller->url_helper->link_to($this->t('Edit'), array('action' => 'edit', 'id' => $Page->id), array('class'=>'edit action')).' '.
        
        // Delete link is optional on listing
        (EDITAM_SHOW_DELETE_ON_PAGE_LISTING ? (empty($Page->parent_id)?'':$this->_controller->url_helper->link_to($this->t('Delete'), array('action' => 'destroy', 'id' => $Page->id), array('class'=>'delete action'))) : '').' '.
        
        // Add child link
        ($Page->is_virtual ? '':$this->_controller->url_helper->link_to($this->t('Add child'), array('action' => 'add_child', 'parent_id' => $Page->id),array('class'=>'add action'))).' ';
    }

    function admin_expand_or_collapse_links($Page)
    {
        $expand_or_collapse = empty($Page->expanded) ? 'expand' : 'collapse';
        if(!empty($Page->nested_set) && $Page->nested_set->countChildren() > 0){
            return $this->_controller->url_helper->link_to($this->_controller->t($expand_or_collapse),
            array('action' => 'listing', $expand_or_collapse => $Page->getId()),
            array('class'=>$expand_or_collapse.' expand_or_collapse','id'=>'expand_or_collapse-'.$Page->getId(),
            'onclick'=>'Page.Tree.expand_or_collapse('.$Page->getId().', this);return false;'));
        }
    }
}

?>