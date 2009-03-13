<? $current_time = $date_helper->locale_date_time() ?>
<fieldset>
	<label for="site_preference-{Preference.id}"><%= t Preference.title %></label>
	<%= time_zone_select 'Preference', 'value', {}, {}, :id => "site_preference-#{Preference.id}", :name => "preferences[#{Preference.id}]" %>
</fieldset>