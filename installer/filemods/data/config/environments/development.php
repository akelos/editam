<?php
    $search_replace = array(
            array(
                "searched" => "/(define\('AK_ENABLE_STRICT_XHTML_VALIDATION',\s*\w*\);.*)/",
                "detect_modified" => "/define\('EDITAM_SITE_THEME'/",
                "replaced" => "$1\n\ndefine('EDITAM_SITE_THEME', '#003366');
define('EDITAM_CACHE_LIFE', 60*5);
define('EDITAM_CACHE_ENABLED', false);
define('EDITAM_COMPRESS_OUTPUT', true);
define('EDITAM_SHOW_DELETE_ON_PAGE_LISTING', false);
define('EDITAM_SITE_NAME', 'Editam website');
define('EDITAM_CACHE_PREFERENCES_ON_SESSION', false);


define('AK_CACHE_HANDLER', 1); // 1 file based, 2 database based
define('AK_ACTION_CONTROLLER_DEFAULT_REQUEST_TYPE', 'web_request');
define('AK_ACTION_CONTROLLER_DEFAULT_ACTION', 'index');\n\n"
            )
    );
?>