<div id="editam_settings">
<%= start_form_tag {:action =>'setup'}, 
    :id => 'preferences_form' ,
    :method => 'post', 
    :enctype => 'multipart/form-data' %>
    
    {?SitePreferences}
        <h1>_{Site preferences}</h1>
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