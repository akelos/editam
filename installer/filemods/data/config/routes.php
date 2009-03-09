<?php
    $search_replace = array(
            array(
                "searched" => "/(\<\?php\s*)/",
                "detect_modified" => "/\\\$Map-\>connect\('\/editam\/:controller*\/:action*\/:id',\s*array\('(\w*\W*){5}editam\'\){2};/",
                "replaced" => "$1\n\$Map->connect('/editam/:controller/:action/:id', array('controller' => 'page', 'action' => 'listing', 'module' => 'editam'));\n\$Map->connect('/'.AK_EDITAM_PUBLIC_SITE_URL_SUFFIX.'/error/404', array('controller' => 'site', 'action' => 'not_found', 'module' => 'editam'));
\$Map->connect('/'.AK_EDITAM_PUBLIC_SITE_URL_SUFFIX.'/error/500', array('controller' => 'site', 'action' => 'error', 'module' => 'editam'));
\$Map->connect('/'.AK_EDITAM_PUBLIC_SITE_URL_SUFFIX.'/*url', array('controller' => 'site', 'action' => 'show_page', 'module' => 'editam'));\n"
            )
    );
?>