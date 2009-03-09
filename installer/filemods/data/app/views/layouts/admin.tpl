<?php
	$search_replace = array(
    		array(
    			"searched" => "/(\<\W*stylesheet_link_tag\W*module_name\W*admin.css\W*media\W*print\W*screen'\s*\%\>)/",
    			"detect_modified" => "/\<\W*stylesheet_link_tag\W*admin\W*admin.css\W*media\W*print\W*screen'\s*\%\>/",
    			"replaced" => '<%= stylesheet_link_tag "admin/admin.css", :media=>"print,screen" %>'
    		),
    		array(
                "searched" => "/(\<\W*stylesheet_link_tag\W*module_name\W*menu.css\W\s*\%\>)/",
                "detect_modified" => "/\<\W*stylesheet_link_tag\W*admin\W*menu.css\W\s*\%\>/",
                "replaced" => '<%= stylesheet_link_tag "admin/menu.css" %>'
            ),
            array(
                "searched" => "/(\<\%\=\W*stylesheet_for_current_controller\W*\%\>)/",
                "detect_modified" => "/\<\?\W*if\(\\\$module_name\s*==\s*\Weditam\W*stylesheet_link_tag\W*editam\/module.css/",
                "replaced" => "$1\n    <? if(\$module_name == 'editam'): ?>
        <%= stylesheet_link_tag \"editam/module.css\", :media=>'print,screen' %>
    <? endif; ?>\n"
            ),
            array(
                "searched" => "/(\<\%=\s*javascript_include_tag\s*\%\>)/",
                "detect_modified" => "/\<\?\W*if\(\\\$module_name\s*==\s*\Weditam\W*javascript_include_tag\W*protoculous/",
                "replaced" => "<? if(\$module_name == 'editam'): ?>
        <%= javascript_include_tag 'protoculous' %>
        <%= javascript_include_tag 'editam' %>
    <? else: ?>
        <%= javascript_include_tag %>
    <? endif; ?>"
            )
    	);
?>
