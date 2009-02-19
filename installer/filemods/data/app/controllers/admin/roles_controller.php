<?php
    $search_replace = array(
            array(
                "searched" => "/(var\s*\\\$admin_selected_tab\s*=\s*'Manage Users';)/",
                "detect_modified" => "/var\s*\\\$selected_tab\s*=\s*'Manage Users';/",
                "replaced" => "var \$selected_tab = 'Manage Users';\n\n"
            )
    );
?>