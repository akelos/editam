<%= render :partial => 'script_constants' %>

<div id="tasks" class="tasks">
  <ul>
    <li><%= link_to _("Edit the snippet: <strong>#{Snippet.name}</strong>"), {:action => 'edit', :id => Snippet.id} %></li>
    <li><%= link_to _('Back to existing Snippets list'), :action => 'listing' %></li>
  </ul> 
</div>
  
<h2> _{Are you sure you want to <span class="warn">definitely delete</span> the snippet <strong>"%Snippet.name"</strong>?}</h2>
 
<%= start_form_tag :action => 'destroy', :id => Snippet.id %>
<div id="operations">
<%= confirm_delete %> _{or} <%= cancel_link %>
</div>
</form>