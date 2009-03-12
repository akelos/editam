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

require(AK_APP_DIR.DS.'behaviours'.DS.'base_behaviour.php');

Ak::import('PagePart,ContentLayout,User');

class Page extends ActiveRecord
{
    var $_Behaviour;

    var $has_many = array(
    'parts'=>array(
    'dependent' => 'destroy',
    'class_name' => 'PagePart',
    'handler_name' => 'part'
    ));

    var $belongs_to = array(
    'layout' => array('class_name' => 'ContentLayout', 'primary_key_name' => 'layout_id'),
    'author' => array('class_name' => 'User', 'foreign_key' => 'created_by'),
    'editor' => array('class_name' => 'User', 'foreign_key' => 'updated_by')
    );

    var $acts_as = array('nested_set' => array('order' => 'virtual DESC, title ASC','scope' => "locale = ?"));

    function getLocale()
    {
        $default_lang = Ak::lang();
        $lang = !isset($this->_original_locale) || empty($this->locale) ? $default_lang : $this->locale;
        $locale = in_array($lang, Ak::langs()) ? $lang : $default_lang;
        $this->_original_locale = empty($this->_original_locale) ? $locale : $this->_original_locale;
        return $locale;
    }

    function forceSetLocale($locale)
    {
        $this->_forced_locale = $locale;
    }

    function isHomepage()
    {
        return !$this->isNewRecord() && $this->nested_set->isRoot();
    }

    function saveWithParts($parts = array(), $position = null)
    {
        $this->transactionStart();
        $this->parts = array();
        $this->part->load();
        foreach ($parts as $part){
            $this->part->create($part);
        }
        //$this->set('parent_id', null);
        $success = $this->save() && !$this->transactionHasFailed();

        if($success && !$this->isHomepage()){
            $this->nested_set->moveToChildOf($position);
        }

        if(!$success){
            $this->transactionFail();
        }
        $this->transactionComplete();
        return $success;
    }

    function updateWithParts($parts = array(), $position = null)
    {
        $this->part->load();
        //$this->layout->load();
        $this->transactionStart();

        foreach (array_keys($this->parts) as $k){
            if(!isset($parts[$k+1])){
                $this->parts[$k]->destroy();
            }
        }
        $success = true;
        foreach ($parts as $k=>$part){
            if(!$success){
                break;
            }
            if(empty($this->parts[$k-1])){
                if(!$this->part->create($part)){
                    $success = false;
                    $this->addErrorToBase($this->t('Unexpected errors on page part'));
                }
            }else{
                $this->parts[$k-1]->setAttributes($part);
            }
        }
        $success = $this->save() && !$this->transactionHasFailed();

        if(!empty($position) && $success && $this->parent_id != $position){
            $this->nested_set->moveToChildOf($position);
        }

        if(!$success){
            $this->transactionFail();
        }
        $this->transactionComplete();
        return $success;
    }


    function beforeSave()
    {
        if(!empty($this->_controller)){
            $this->initiateBehaviour($this->_controller);
        }else{
            $this->loadBehaviour();
        }
        
        $this->_updatePublishedAt();
        $this->_updateVirtual();
        return true;
    }

    function beforeSaveOnCreate()
    {
        $this->_updateLocale();
        return true;
    }


    function validate()
    {
        $this->validatesPresenceOf(array('title','slug','breadcrumb','status','created_by'));

        $this->_validateSlug();

        $this->validatesInclusionOf('behaviour',array_keys($this->getAvailableBehaviours()), 'inclusion', true);
        $this->validatesInclusionOf('status',array_keys($this->getAvailableStatuses()));

        $this->validatesInclusionOf('locale', Ak::langs());

        if(isset($this->_original_locale) && $this->_original_locale != $this->get('locale')){
            $this->addError('locale', $this->t('Page locale can\'t be changed once it has been set.'));
        }
    }

    function _validateSlug($parent_id = null)
    {
        $original_parent_id = $this->parent_id;
        $this->parent_id = !empty($parent_id) ? $parent_id : $this->parent_id;

        if(!$this->getErrorsOn('slug')){
            $this->validatesFormatOf('slug','/^([\w\d]+|[\w\d](?!.*(\-|\.){2,}.*)[\w\d\-_\.]*[\w\d]|\/)$/');
            if(!empty($this->parent_id)){
                $this->validatesUniquenessOf('slug', array('scope' => array('locale','parent_id'), 'message' => $this->t('Slug is already used in a page that shares the parent with this one')));
            }
        }
        $this->parent_id = $original_parent_id;
    }

    function validateOnCreate()
    {
        $this->validatesAssociated('parts');
    }

    function isPublished()
    {
        return !empty($this->status) && $this->status == 'published';
    }

    function &getPart($name)
    {
        if(empty($this->parts)){
            $this->part->load();
        }
        $IndexedPart =& $this->_getPartIndexedAs($name);
        return $IndexedPart;
    }

    function &getInheritedPart($name)
    {
        if(!$Part =& $this->getPart($name)){
            if(!$ParentPages =& $this->getParentPages()){
                $false = false;
                return $false;
            }
            foreach (array_reverse(array_keys($ParentPages)) as $k){
                if($Part =& $ParentPages[$k]->getPart($name)){
                    $this->__indexed_parts[$name] =& $Part;
                    break;
                }
            }
        }

        return $Part;
    }

    function &getParentPages()
    {
        if(!isset($this->ParentPages)){
            $this->ParentPages =& $this->nested_set->getAncestors();
        }
        return $this->ParentPages;
    }


    function getFilteredPart($name, $use_inherited_if_unavailable = false)
    {
        $method = $use_inherited_if_unavailable ? 'getInheritedPart' : 'getPart';
        $Part =& $this->$method($name);
        return is_object($Part) ? EditamFilter::getFilteredContent($Part) : false;
    }

    function &_findInheritedPart($name)
    {
        $Part =& $this->part->find('first', array('conditions'=>array('name = ?', $name)));
    }

    function &_getPartIndexedAs($name)
    {
        empty($this->__indexed_parts[$name]) ? $this->_loadIndexedParts() : null;
        if(!isset($this->__indexed_parts[$name])){
            $false = false;
            return $false;
        }
        return $this->__indexed_parts[$name];
    }

    function _loadIndexedParts()
    {
        foreach (array_keys($this->parts) as $k){
            $this->__indexed_parts[$this->parts[$k]->get('name')] =& $this->parts[$k];
        }
    }


    function &getLayoutInstance()
    {
        if($this->layout->getType() != 'ContentLayout'){
            $this->layout->load(true);
            if($this->layout->getType() != 'ContentLayout'){
                if($this->nested_set->isRoot()){
                    $false = false;
                    return $false;
                }
                $Parent =& $this->nested_set->getParent();
                $Layout =& $Parent->getLayoutInstance();
                return $Layout;
            }
        }
        return $this->layout;
    }

    function getLayout()
    {
        $Layout =& $this->getLayoutInstance();
        return isset($Layout->content) ? $Layout->get('content') : false;
    }

    function getLayoutName()
    {
        if(empty($this->layout->name)){
            $this->layout->load();
        }
        if(empty($this->layout->name) && $parent =& $this->nested_set->getParent()){
            return $parent->getLayoutName();
        }else{
            return false;
        }
        return $this->layout->name;
    }

    function &findByUrl($url, $show_unpublished = false)
    {
        $Page =& $this->_Behaviour->findPageByUrl($url, $show_unpublished);
        return $Page;
    }

    function &_findByUrl($url_parts, $show_unpublished = false, $missing_page_mode = false)
    {
    	$sql = $this->_getSqlForUrlFinder($url_parts, $show_unpublished, $missing_page_mode);
    	$this->_db->addLimitAndOffset($sql, array('limit' => 1, 'offset' => null));
        if($result =& $this->findBySql($sql)){
            return $result[0];
        }
        $false = false;
        return $false; // Trick for avoiding pass by reference notices on PHP4
    }

    function _getSqlForUrlFinder($url_parts, $show_unpublished = false, $missing_page_mode = false)
    {
        $url_parts = is_array($url_parts) ? $url_parts : explode('/',$url_parts.'/');
        $url_parts = array_diff($url_parts,array(''));
        if(empty($url_parts[0]) || $url_parts[0] != '/'){
            array_unshift($url_parts, '/');
        }
        $total_url_parts = count($url_parts);
        $url_parts = array_reverse($url_parts);
        $sql = '';
        $locale = Ak::lang();

        $conditions = '';
        $joins = '';
        $table_alias = false;
        foreach ($url_parts as $position => $url_part){
            $previous_table_alias = $table_alias;
            $table_alias = 'pages'.$position;
            $position = $position +1;
            $condition = $missing_page_mode && $position ==  1 ? ' '.$table_alias.'.behaviour = "page_missing" ' :
            $table_alias.'.slug = '.$this->castAttributeForDatabase('slug',$url_part);
            $condition .= ' AND '.$table_alias.'.locale = '.$this->castAttributeForDatabase('locale',$locale).' ';
            if($position == 1){
                $select = 'SELECT '.$table_alias.'.* FROM pages AS '.$table_alias;
                $conditions .= ' WHERE '.$condition.($show_unpublished?'':' AND '.$table_alias.'.status = '.$this->castAttributeForDatabase('status','published'));
            }else{
                $joins .= ' INNER JOIN pages '.$table_alias.' ON '.$table_alias.'.id = '.$previous_table_alias.'.parent_id ';
                $conditions .= $condition.($show_unpublished ? '' : " AND $table_alias.status = 'published'");
            }
            if($position < $total_url_parts){
                $conditions .= ' AND ';
            }
        }
        return $select.$joins.$conditions;
    }

    function &findMissingPageForUrl($url)
    {
        $Page =& $this->_Behaviour->findMissingPageForUrl($url);
        return $Page;
    }

    /**
     * Callbacks
     */
    function _updatePublishedAt()
    {
        if(empty($this->published_at) && $this->status == 'published'){
            $this->published_at = Ak::getDate();
        }
    }
    function _updateVirtual()
    {
        $this->set('is_virtual', $this->isVirtual());
    }

    function _updateLocale()
    {
        empty($this->locale) ? $this->set('locale',Ak::lang()) : null;
    }

    /**
     * Page behaviours
     */

    function loadBehaviour($behaviour = null)
    {
        $behaviour = empty($behaviour) ? $this->behaviour : $behaviour;
        $this->behaviour = !empty($behaviour) && in_array($behaviour, array_keys($this->getAvailableBehaviours())) ? $behaviour : '';

        $this->_Behaviour =& $this->getBehaviourInstance();
    }

    function initiateBehaviour(&$controller)
    {
        $this->loadBehaviour($this->get('behaviour'));
        $this->_Behaviour->init($controller);
    }

    function &getBehaviourInstance()
    {
        $behaviour_class = empty($this->behaviour) ? 'BaseBehaviour' : AkInflector::camelize($this->behaviour).'Behaviour';
        require_once(AK_APP_DIR.DS.'behaviours'.DS.AkInflector::underscore($behaviour_class).'.php');
        $Instance =& new $behaviour_class();
        return $Instance;
    }

    function getUrl()
    {
        return $this->_Behaviour->getPageUrl();
    }

    function getInheritedSlug($force = false, $include_locale = EDITAM_IS_MULTILINGUAL)
    {
        if($force || empty($this->_inherited_slug)){
            if(!empty($this->nested_set) && !empty($this->lft) && !empty($this->rgt)){
                $this->_inherited_slug = str_replace('//','/',
                ($include_locale?'/'.$this->locale:'').'/'.trim(join('/',array_keys($this->collect($this->nested_set->getSelfAndAncestors(),'slug','title'))),'/').'/');
            }
        }
        return $this->_inherited_slug;
    }

    function hasCache()
    {
        return $this->_Behaviour->hasPageCache();
    }

    function canUsePageCache()
    {
        return $this->_Behaviour->canUsePageCache();
    }

    function render()
    {
        return $this->_Behaviour->renderPage();
    }

    function isVirtual()
    {
        return $this->_Behaviour->isPageVirtual();
    }

    function process()
    {
        return $this->_Behaviour->process();
    }

    function getChildUrl()
    {
        return $this->_Behaviour->getChildUrl();
    }

    /**
     * Page elements discovery functions
     */
    function getAvailableBehaviours()
    {
        static $behaviours = array();
        if(empty($behaviours)){
            if(defined('EDITAM_AVAILABLE_BEHAVIOURS')){
                foreach (Ak::toArray(EDITAM_AVAILABLE_BEHAVIOURS) as $behaviour){
                    $behaviours[$behaviour] = $this->t($behaviour);
                }
            }else{
                foreach (Ak::dir(AK_APP_DIR.DS.'behaviours', array('dirs'=>false)) as $file){
                    if(substr($file,-14) == '_behaviour.php'){
                        $behaviour = substr($file,0,-14);
                        if($behaviour != 'base'){
                            $behaviours[$behaviour] = $this->t($behaviour);
                        }
                    }
                }
            }
            $behaviours = array_map(array('AkInflector','humanize'), $behaviours);
        }
        return $behaviours;
    }

    function getAvailableStatuses()
    {
        return array(
        'published' => $this->t('Published'),
        'draft' => $this->t('Draft'),
        'reviewed' => $this->t('Reviewed'),
        'hidden' => $this->t('Hidden')
        );
    }

    function moveBeside(&$Page, $position = 'left')
    {
        $method = $position == 'left' ? 'moveToRightOf' : 'moveToLeftOf';
        $this->_validateSlug($Page->parent_id);
        if(!$this->hasErrors()){
            $this->nested_set->$method($Page);
            return true;
        }
        return false;
    }


    function clearCachedPages()
    {
        $Cache =& Ak::cache();
        $Cache->init(EDITAM_CACHE_LIFE);
        $Cache->clean(AK_HOST);

    }

    function clearOldPagesFromCache()
    {
        $Cache =& Ak::cache();
        $Cache->init(EDITAM_CACHE_LIFE);
        $Cache->clean(AK_HOST, 'old');
    }
}



?>
