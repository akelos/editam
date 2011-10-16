<?php

# Author Bermi Ferrer - MIT LICENSE

class PageMissingBehavior extends BaseBehavior
{
    public $name = 'Page Missing';
    public $description =
    'The Page Missing behavior is used to create a "File Not Found" error
page in the event that a page is not found among a page\'s children.

To create a "File Not Found" error page for an entire Web site, create
a page that is a child of the root page and assign it the Missing Page
behavior.';

    public function isPageVirtual()
    {
        return true;
    }

    public function canUsePageCache()
    {
        return false;
    }

    public function getPageHeaders()
    {
        return array('Status' => 404);
    }
    
}
