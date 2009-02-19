{?Roles}
    <label id="new_user_roles" class="block">_{Select default roles for newly created users}:</label>
    <?
    $preference_values = (!empty($Preference->value) && strstr($Preference->value, ',')) ? explode(',', $Preference->value) : array(@$Preference->value);
    $selected_roles = array();
    
    if (!empty($preference_values)) {
    	foreach ($preference_values as $preference_id) {
    		$selected_roles[$preference_id] = true;
    	}
    }
    ?>
    {loop Roles}
        <p class="inline">
            <? $is_checked = (!empty($params['preferences'][$Preference->id][$Role->getId()])) ? true : ((!empty($selected_roles[$Role->getId()])) ? true : false); ?>
        
            <input type="hidden" value="0" name="preferences[{Preference.id}][{Role.id}]"/>
            <input type="checkbox" id="preferences-{Preference.id}-{Role.id}" name="preferences[{Preference.id}][{Role.id}]" {?is_checked}checked="checked"{end} />
            
            <label for="preferences-{Preference.id}-{Role.id}">
                {Role.name}
                {?Role.description}, <span class="information">{Role.description}</span>{end}
            </label>
        </p>
    {end}
{end}