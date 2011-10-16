<?php 

$value = '';

if (isset($params['site_preference'][$Preference->id]['value'])) {
    $value = $params['site_preference'][$Preference->id]['value'];
}elseif (isset($Preference->value)){
    $value = $Preference->value;
}
?>
<fieldset>
    <label for="site_preference-{Preference.id}"><%= translate Preference.title %></label>
    <input type="text" name="preferences[{Preference.id}]" id="site_preference-{Preference.id}" value="{value}" />
</fieldset>