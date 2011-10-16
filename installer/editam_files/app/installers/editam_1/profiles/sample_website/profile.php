<?php

# Author Bermi Ferrer - MIT LICENSE

/**
* Creates a basic website useful for demonstration purposes.
* This sample website should be feature rich for advances users
* but also easy to use for novice ones.
*/
class SampleWebsiteProfile
{
    public $Installer; 
    public $priority = 10;
    
    public function install()
    {
        $this->_createDefaultHomePage();
    }

    public function uninstall()
    {

    }

    public function _createDefaultHomePage()
    {
        $User = new User();
        $ContentLayout = new ContentLayout();
        
        if (!$Admin =& $User->find(1) || !$Layout =& $ContentLayout->findFirstBy('name', 'Default')){
            return;
        }
        
        $Page = new Page(array(
            'title' => 'Welcome to your new Editam Site!', 
            'slug' => '/', 
            'breadcrumb' => 'Home', 
            'status' => 'published', 
            'layout_id' => $Layout->getId(), 
            'created_at' => Ak::getDate(), 
            'created_by' => $Admin->getId(), 
            'updated_at' => Ak::getDate(), 
            'updated_by' => $Admin->getId()));

        $Page->part->create(array(
            'name' => 'body', 
            'filter' => 'markdown',
            'content' => 
                "Please login <a href='{base}/admin'>login</a> using the username and password you provided when you installed Editam.

Editam is a content management platform based in these core principles and objectives

 * Simplicity
 * Usability
 * Findability
 * Standard awareness
 * Efficiency
 * Openness
 * Internationalization
 * Extendability
 * Compatibility 

Out of the box Editam has less features than other CMS on purpose, so you can start creating content immediately.

Editam is available open-source under the GPL3 license."
            ));
            
        $Page->part->create(array(
            'name' => 'sidebar', 
            'content' => 
'<div id="logo">
    <a href="/" title="_{Home page}"><img src="{base}/images/editam_light_bg.png" alt="Editam by Akelos Media S.L." /></a>
</div>

<ul class="sidebar_menu">
    <li><a title="Administrate your site" href="{base}/admin">Administrate your site</a></li>
    <li><a title="Development Site" href="http://trac.editam.com">Editam development site</a></li>
</ul>'
            ));
            
             
        $Page->part->create(array(
            'name' => 'footer', 
            'content' => 
                'Powered by <a href="http://www.editam.com">Editam</a>.'
            ));
            
        $Page->save();
    }

}

?>