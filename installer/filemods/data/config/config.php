<?php
    $search_replace = array(
            array(
                "searched" => "/(\?\>)/",
                "detect_modified" => "/AK_EDITAM_PUBLIC_SITE_URL_SUFFIX/",
                "replaced" => "\ndefine('AK_EDITAM_PUBLIC_SITE_URL_SUFFIX','cms');\n$1"
            )
    );
?>