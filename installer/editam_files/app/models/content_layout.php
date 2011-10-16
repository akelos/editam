<?php

# Author Bermi Ferrer - MIT LICENSE

        
class ContentLayout extends ActiveRecord
{
    public $has_many = array('pages'=>array('foreign_key'=>'layout_id'));
    
    public function validate()
    {
        $this->validatesPresenceOf(array('name','content'));
        $this->validatesUniquenessOf('name');
    }

    public function beforeSave()
    {
        return $this->validatesEditagsField('content', false);
    }
    
    public function beforeDestroy()
    {
        return $this->page->count() == 0;
    }
}
