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

/**
* Creates a basic website useful for demonstration purposes.
* This sample website should be feature rich for advances users
* but also easy to use for novice ones.
*/
class SampleWebsiteProfile
{
    var $Installer; 
    var $priority = 10;
    
    function install()
    {
        $this->_createDefaultHomePage();
    }

    function uninstall()
    {

    }

    function _createDefaultHomePage()
    {
        Ak::import(array('page', 'page_part', 'user', 'content_layout'));
        
        $User =& new User();
        $ContentLayout =& new ContentLayout();
        
        if (!$Admin =& $User->find(1) || !$Layout =& $ContentLayout->findFirstBy('name', 'Default')){
            return;
        }
        
        $Page =& new Page(array(
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