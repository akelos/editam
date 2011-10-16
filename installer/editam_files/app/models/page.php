<?php

# Author Bermi Ferrer - MIT LICENSE

class Page extends ActiveRecord
{
    public $_Behavior;

    public $has_many = array(
    'parts'=>array(
    'dependent' => 'destroy',
    'class_name' => 'PagePart',
    'handler_name' => 'part'
    ));

    public $belongs_to = array(
    'layout' => array('class_name' => 'ContentLayout', 'primary_key_name' => 'layout_id'),
    'author' => array('class_name' => 'User', 'foreign_key' => 'created_by'),
    'editor' => array('class_name' => 'User', 'foreign_key' => 'updated_by')
    );

    public $acts_as = array('nested_set' => array('order' => 'virtual DESC, title ASC','scope' => "locale = ?"));

    public function getLocale()
    {
        $default_lang = Ak::lang();
        $lang = !isset($this->_original_locale) || empty($this->locale) ? $default_lang : $this->locale;
        $locale = in_array($lang, Ak::langs()) ? $lang : $default_lang;
        $this->_original_locale = empty($this->_original_locale) ? $locale : $this->_original_locale;
        return $locale;
    }

    public function forceSetLocale($locale)
    {
        $this->_forced_locale = $locale;
    }

    public function isHomepage()
    {
        return !$this->isNewRecord() && $this->nested_set->isRoot();
    }

    public function saveWithParts($parts = array(), $position = null)
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

    public function updateWithParts($parts = array(), $position = null)
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


    public function beforeSave()
    {
        if(!empty($this->_controller)){
            $this->initiateBehavior($this->_controller);
        }else{
            $this->loadBehavior();
        }
        
        $this->_updatePublishedAt();
        $this->_updateVirtual();
        return true;
    }

    public function beforeSaveOnCreate()
    {
        $this->_updateLocale();
        return true;
    }


    public function validate()
    {
        $this->validatesPresenceOf(array('title','slug','breadcrumb','status','created_by'));

        $this->_validateSlug();

        $this->validatesInclusionOf('behavior',array_keys($this->getAvailableBehaviors()), 'inclusion', true);
        $this->validatesInclusionOf('status',array_keys($this->getAvailableStatuses()));

        $this->validatesInclusionOf('locale', Ak::langs());

        if(isset($this->_original_locale) && $this->_original_locale != $this->get('locale')){
            $this->addError('locale', $this->t('Page locale can\'t be changed once it has been set.'));
        }
    }

    public function _validateSlug($parent_id = null)
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

    public function validateOnCreate()
    {
        $this->validatesAssociated('parts');
    }

    public function isPublished()
    {
        return !empty($this->status) && $this->status == 'published';
    }

    public function &getPart($name)
    {
        if(empty($this->parts)){
            $this->part->load();
        }
        $IndexedPart = $this->_getPartIndexedAs($name);
        return $IndexedPart;
    }

    public function &getInheritedPart($name)
    {
        if(!$Part = $this->getPart($name)){
            if(!$ParentPages = $this->getParentPages()){
                $false = false;
                return $false;
            }
            foreach (array_reverse(array_keys($ParentPages)) as $k){
                if($Part = $ParentPages[$k]->getPart($name)){
                    $this->__indexed_parts[$name] = $Part;
                    break;
                }
            }
        }

        return $Part;
    }

    public function &getParentPages()
    {
        if(!isset($this->ParentPages)){
            $this->ParentPages = $this->nested_set->getAncestors();
        }
        return $this->ParentPages;
    }


    public function getFilteredPart($name, $use_inherited_if_unavailable = false)
    {
        $method = $use_inherited_if_unavailable ? 'getInheritedPart' : 'getPart';
        $Part = $this->$method($name);
        return is_object($Part) ? EditamFilter::getFilteredContent($Part) : false;
    }

    public function &_findInheritedPart($name)
    {
        $Part = $this->part->find('first', array('default'=>false, 'conditions'=>array('name = ?', $name)));
    }

    public function &_getPartIndexedAs($name)
    {
        empty($this->__indexed_parts[$name]) ? $this->_loadIndexedParts() : null;
        if(!isset($this->__indexed_parts[$name])){
            $false = false;
            return $false;
        }
        return $this->__indexed_parts[$name];
    }

    public function _loadIndexedParts()
    {
        foreach (array_keys($this->parts) as $k){
            $this->__indexed_parts[$this->parts[$k]->get('name')] = $this->parts[$k];
        }
    }


    public function &getLayoutInstance()
    {
        if($this->layout->getType() != 'ContentLayout'){
            $this->layout->load(true);
            if($this->layout->getType() != 'ContentLayout'){
                if($this->nested_set->isRoot()){
                    $false = false;
                    return $false;
                }
                $Parent = $this->nested_set->getParent();
                $Layout = $Parent->getLayoutInstance();
                return $Layout;
            }
        }
        return $this->layout;
    }

    public function getLayout()
    {
        $Layout = $this->getLayoutInstance();
        return isset($Layout->content) ? $Layout->get('content') : false;
    }

    public function getLayoutName()
    {
        if(empty($this->layout->name)){
            $this->layout->load();
        }
        if(empty($this->layout->name) && $parent = $this->nested_set->getParent()){
            return $parent->getLayoutName();
        }else{
            return false;
        }
        return $this->layout->name;
    }

    public function &findByUrl($url, $show_unpublished = false)
    {
        $Page = $this->_Behavior->findPageByUrl($url, $show_unpublished);
        return $Page;
    }

    public function &_findByUrl($url_parts, $show_unpublished = false, $missing_page_mode = false)
    {
        $sql = $this->_getSqlForUrlFinder($url_parts, $show_unpublished, $missing_page_mode);
        $this->_db->addLimitAndOffset($sql, array('limit' => 1, 'offset' => null));
        if($result = $this->findBySql($sql, array('default'=>false))){
            return $result[0];
        }
        $false = false;
        return $false; // Trick for avoiding pass by reference notices on PHP4
    }

    public function _getSqlForUrlFinder($url_parts, $show_unpublished = false, $missing_page_mode = false)
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
            $condition = $missing_page_mode && $position ==  1 ? ' '.$table_alias.'.behavior = "page_missing" ' :
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

    public function &findMissingPageForUrl($url)
    {
        $Page = $this->_Behavior->findMissingPageForUrl($url);
        return $Page;
    }

    /**
     * Callbacks
     */
    public function _updatePublishedAt()
    {
        if(empty($this->published_at) && $this->status == 'published'){
            $this->published_at = Ak::getDate();
        }
    }
    public function _updateVirtual()
    {
        $this->set('is_virtual', $this->isVirtual());
    }

    public function _updateLocale()
    {
        empty($this->locale) ? $this->set('locale',Ak::lang()) : null;
    }

    /**
     * Page behaviors
     */

    public function loadBehavior($behavior = null)
    {
        $behavior = empty($behavior) ? $this->behavior : $behavior;
        $this->behavior = !empty($behavior) && in_array($behavior, array_keys($this->getAvailableBehaviors())) ? $behavior : '';

        $this->_Behavior = $this->getBehaviorInstance();
    }

    public function initiateBehavior(&$controller)
    {
        $this->loadBehavior($this->get('behavior'));
        $this->_Behavior->init($controller);
    }

    public function &getBehaviorInstance()
    {
        $behavior_class = empty($this->behavior) ? 'BaseBehavior' : AkInflector::camelize($this->behavior).'Behavior';
        $Instance = new $behavior_class();
        return $Instance;
    }

    public function getUrl()
    {
        return $this->_Behavior->getPageUrl();
    }

    public function getInheritedSlug($force = false, $include_locale = EDITAM_IS_MULTILINGUAL)
    {
        if($force || empty($this->_inherited_slug)){
            if(!empty($this->nested_set) && !empty($this->lft) && !empty($this->rgt)){
                $this->_inherited_slug = str_replace('//','/',
                ($include_locale?'/'.$this->locale:'').'/'.trim(join('/',array_keys($this->collect($this->nested_set->getSelfAndAncestors(),'slug','title'))),'/').'/');
            }
        }
        return $this->_inherited_slug;
    }

    public function hasCache()
    {
        return $this->_Behavior->hasPageCache();
    }

    public function canUsePageCache()
    {
        return $this->_Behavior->canUsePageCache();
    }

    public function render()
    {
        return $this->_Behavior->renderPage();
    }

    public function isVirtual()
    {
        return $this->_Behavior->isPageVirtual();
    }

    public function process()
    {
        return $this->_Behavior->process();
    }

    public function getChildUrl()
    {
        return $this->_Behavior->getChildUrl();
    }

    /**
     * Page elements discovery functions
     */
    public function getAvailableBehaviors()
    {
        static $behaviors = array();
        if(empty($behaviors)){
            if(defined('EDITAM_AVAILABLE_BEHAVIORS')){
                foreach (Ak::toArray(EDITAM_AVAILABLE_BEHAVIORS) as $behavior){
                    $behaviors[$behavior] = $this->t($behavior);
                }
            }else{
                foreach (AkFileSystem::dir(AK_APP_DIR.DS.'behaviors', array('dirs'=>false)) as $file){
                    if(substr($file,-14) == '_behavior.php'){
                        $behavior = substr($file,0,-14);
                        if($behavior != 'base'){
                            $behaviors[$behavior] = $this->t($behavior);
                        }
                    }
                }
            }
            $behaviors = array_map(array('AkInflector','humanize'), $behaviors);
        }
        return $behaviors;
    }

    public function getAvailableStatuses()
    {
        return array(
        'published' => $this->t('Published'),
        'draft' => $this->t('Draft'),
        'reviewed' => $this->t('Reviewed'),
        'hidden' => $this->t('Hidden')
        );
    }

    public function moveBeside(&$Page, $position = 'left')
    {
        $method = $position == 'left' ? 'moveToRightOf' : 'moveToLeftOf';
        $this->_validateSlug($Page->parent_id);
        if(!$this->hasErrors()){
            $this->nested_set->$method($Page);
            return true;
        }
        return false;
    }


    public function clearCachedPages()
    {
        $Cache = Ak::cache();
        $Cache->init(EDITAM_CACHE_LIFE);
        $Cache->clean(AK_HOST);

    }

    public function clearOldPagesFromCache()
    {
        $Cache = Ak::cache();
        $Cache->init(EDITAM_CACHE_LIFE);
        $Cache->clean(AK_HOST, 'old');
    }
}
