<?php
    $search_replace = array(
            array(
                "searched" => "/(var\s*\\\$controller_selected_tab\s*=\s*'Accounts';)/",
                "detect_modified" => "/var\s*\\\$selected_tab\s*=\s*'Manage Users';/",
                "replaced" => "var \$selected_tab = 'Manage Users';\n    $1"
            )
    );
?>