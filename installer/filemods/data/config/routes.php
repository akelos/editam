<?php
    $search_replace = array(
            array(
                "searched" => "/(\\\$Map-\>connect\('\/', \w*\('controller'\W*page\W*action\W*index\'\){2};)/",
                "detect_modified" => "/\\\$Map-\>connect\('\/error\/404'(\W*\w*){6}\W*editam\'\){2};/",
                "replaced" => "\$Map->connect('/error/404', array('controller' => 'site', 'action' => 'not_found', 'module' => 'editam'));
\$Map->connect('/error/500', array('controller' => 'site', 'action' => 'error', 'module' => 'editam'));
\$Map->connect('/*url', array('controller' => 'site', 'action' => 'show_page', 'module' => 'editam'));\n"
            ),
            array(
                "searched" => "/(\<\?php\s*)/",
                "detect_modified" => "/\\\$Map-\>connect\('\/', \w*\('controller'\W*site\W*action\W*show_page\W*url\W*\/\W*module\W*editam\'\){2};/",
                "replaced" => "$1\$Map->connect('/', array('controller' => 'site', 'action' => 'show_page', 'url' => '/', 'module' => 'editam'));\n"
            ),
            array(
                "searched" => "/(\\\$Map-\>connect\('\/admin\/:controller*\/:action*\/:id',\s*array\('(\w*\W*){5}admin\'\){2};)/",
                "detect_modified" => "/\\\$Map-\>connect\('\/editam\/:controller*\/:action*\/:id',\s*array\('(\w*\W*){5}editam\'\){2};/",
                "replaced" => "$1\n\$Map->connect('%prefix%:controller/:action/:id', array('controller' => 'page', 'action' => 'listing', 'module' => 'editam'));\n"
            ),
            array(
                "searched" => "/(\\\$Map-\>\w*\W*admin\W*controller\W*action\W*id\W*array\W*controller\W*)dashboard(\W*action\W*index\W*module\W*admin'\){2};)/",
                "detect_modified" => "/(\\\$Map-\>\w*\W*admin\W*controller\W*action\W*id\W*array\W*controller\W*)users(\W*action\W*index\W*module\W*admin'\){2};)/",
                "replaced" => "$1users$2"
            )
    );
?>