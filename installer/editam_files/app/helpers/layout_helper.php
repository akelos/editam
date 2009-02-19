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

class LayoutHelper extends AkActionViewHelper
{

    function language_switch_links($langs = array())
    {
        $langs = empty($langs) ? Ak::langs() : $langs;

        $links = array();
        foreach ($langs as $lang){
            if($lang != Ak::lang()){
                $links[] = $this->url_for_language_switch(Ak::locale('description', $lang), $lang);
            }
        }
        return $links;
    }
    
    function language_switch_list($langs = array(), $list_html_options = array(), $list_item_html_options = array())
    {
        $default_list_html_options = array('class'=>'language_switch user_actions');
        $list_html_options = array_merge($default_list_html_options, $list_html_options);
        
        $links = $this->language_switch_links($langs);
        $list = '';
        if(!empty($links)){
            $list_items = '';
            foreach ($links as $link){
            $list_items .= $this->_controller->tag_helper->content_tag('li',$link,$list_item_html_options);
            }
            $list = $this->_controller->tag_helper->content_tag('ul',$list_items,$list_html_options);
            
        }
        return $list;
    }

    function url_for_language_switch($description, $language, $html_options = array())
    {
        return $this->_controller->url_helper->link_to($description,
        $this->_controller->url_helper->modify_current_url(array('lang'=>$language), array('ak',AK_SESSION_NAME,'AK_SESSID','PHPSESSID','is_first'), false),
        $html_options);
    }

}

?>