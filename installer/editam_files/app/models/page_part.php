<?php

# Author Bermi Ferrer - MIT LICENSE

class PagePart extends ActiveRecord
{
    public $belongs_to = 'page';

    public function validate()
    {
        $this->validatesPresenceOf('name');
        $this->validatesInclusionOf('filter', array_keys(EditamFilter::getAvailableFilters()), 'inclusion', true);
    }
}
