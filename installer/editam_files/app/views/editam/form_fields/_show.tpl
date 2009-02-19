<?php 
if (!empty($params['site_preference'][$Preference->id]['value'])) {
    $value = $params['site_preference'][$Preference->id]['value'];
}elseif (!empty($Preference->value)){
    $value = $Preference->value;
}
?>

<p class="show"><strong class="label"><%= translate Preference.title %>:</strong> {value?}</p>