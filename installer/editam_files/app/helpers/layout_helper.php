<?php

# Author Bermi Ferrer - MIT LICENSE

class LayoutHelper extends AkActionViewHelper
{
    public function language_switch_links($langs = array())
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
    
    public function language_switch_list($langs = array(), $list_html_options = array(), $list_item_html_options = array())
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

    public function url_for_language_switch($description, $language, $html_options = array())
    {
        return $this->_controller->url_helper->link_to($description,
        $this->_controller->url_helper->modify_current_url(array('lang'=>$language), array('ak',AK_SESSION_NAME,'AK_SESSID','PHPSESSID','is_first'), false),
        $html_options);
    }

}
