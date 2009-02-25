<?php
    $search_replace = array(
            array(
                "searched" => "/(\<input\s*type=\"submit\"[\W\w]*class=\"primary\"\s*\/\>)/",
                "detect_modified" => "/_\{or\}\s*\<a\s*href=\"\/\"\s*title=\"_\{[\w\s]*\}\"\>_\{Go to the public website\}\<\/a\>/",
                "replaced" => "$1\n      <br />\n      _{or} <a href=\"/\" title=\"_{Go to the public website}\">_{Go to the public website}</a>"
            ),
            array(
                "searched" => "/(\<label\W*for\W*login_username\W*id\W*login_username_label(\W*\w*){8}\>)/",
                "detect_modified" => "/\<label\W*for\W*login_username\W*id\W*login_username_label(\W*\w*){8}\>\s*\<br\s*\/\>/",
                "replaced" => "$1\n    <br />"
            ),
            array(
                "searched" => "/(\<label\W*for\W*login_password\W*id\W*login_password_label(\W*\w*){5}\>)/",
                "detect_modified" => "/\<label\W*for\W*login_password\W*id\W*login_password_label(\W*\w*){5}\>\s*\<br\s*\/\>/",
                "replaced" => "$1\n    <br />"
            ),
            array(
                "searched" => "/(\<div\s*id=\Woperations\W)/",
                "detect_modified" => "/\<div\s*id=\Woperations\W style=\Wpadding: 10px 0 0;\W/",
                "replaced" => "$1 style=\"padding: 10px 0 0;\""
            )
    );
?>