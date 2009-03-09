<%= render :partial => 'script_constants' %>

<div id="content_menu">
    <ul class="menu">
        <li><%= link_to _('Create new Snippet'), :action => 'add' %></li>
        <li class="primary"><%= link_to _('Edit Snippet'), :action => 'edit', :id => Snippet.id %></li>
        <li class="active"><%= link_to _('Deleting Snippet'), :action => 'destroy', :id => Snippet.id %></li>
        <li><%= link_to _('Show available Snippet'), :action => 'listing' %></li>
    </ul>
    <p class="information">_{.}</p>
</div>

<div class="content">
<h1>_{Deleting Snippet}</h1>
<p class="warning"> _{Are you sure you want to definitely delete the snippet <strong>"%Snippet.name"</strong>?}</p>
 
<%= start_form_tag :action => 'destroy', :id => Snippet.id %>
<div id="operations">
<%= confirm_delete %> _{or} <%= cancel_link %>
</div>
</form>
</div>