<?php 
if (!empty($params['site_preference'][$Preference->id]['value'])) {
    $value = $params['site_preference'][$Preference->id]['value'];
}elseif (!empty($Preference->value)){
    $value = $Preference->value;
}
?>

<fieldset>
    <label><%= translate Preference.title %>:</label>
    {value?}
</fieldset>