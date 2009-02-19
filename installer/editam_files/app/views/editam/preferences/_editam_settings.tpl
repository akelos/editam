<div id="editam_settings">
<%= start_form_tag {:action =>'setup'}, 
    :id => 'preferences_form' ,
    :method => 'post', 
    :enctype => 'multipart/form-data' %>
    
    {?SitePreferences}
        <h2>_{Site preferences}</h2>
        <div id="site_preferences" class="form">
            {loop SitePreferences}
                <%= render_preference SitePreference %>
            {end}
        </div>
    {end}
    
    <div id="operations">
        <%= save_button %>
    </div>
    
<%= end_form_tag %>
</div>