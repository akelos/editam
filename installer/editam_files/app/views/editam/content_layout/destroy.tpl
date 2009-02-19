<%= render :partial => 'script_constants' %>

<div id="tasks" class="tasks">
  <ul>
    <li><%= link_to _("Edit the Layout: <strong>#{ContentLayout.name}</strong>"), {:action => 'edit', :id => ContentLayout.id} %></li>
    <li><%= link_to _('Back to existing Layout listing'), :action => 'listing' %></li>
  </ul> 
</div>
  
<h2> _{Are you sure you want to <span class="warn">definitely delete</span> the layout <strong>"%ContentLayout.name"</strong>?}</h2>
 
<%= start_form_tag :action => 'destroy', :id => ContentLayout.id %>
<div id="operations">
<%= confirm_delete %> _{or} <%= cancel_link %>
</div>
</form>