<?php
    $search_replace = array(
            array(
                "searched" => "/(\<input\s*type=\"submit\"[\W\w]*class=\"primary\"\s*\/\>)/",
                "detect_modified" => "/_\{or\}\s*\<a\s*href=\"\/\"\s*title=\"_\{[\w\s]*\}\"\>_\{Go to the public website\}\<\/a\>/",
                "replaced" => "$1\n      _{or} <a href=\"/\" title=\"_{Go to the public website}\">_{Go to the public website}</a>"
            )
    );
?>