<%= render :partial => 'script_constants' %>

<div id="content_menu">
    <ul class="menu">
        <li><%= link_to _('Create new Layout'), :action => 'add' %></li>
        <li class="primary"><%= link_to _('Edit Layout'), :action => 'edit', :id => ContentLayout.id %></li>
        <li class="active"><%= link_to _('Deleting Layout'), :action => 'destroy', :id => ContentLayout.id %></li>
        <li><%= link_to _('Show available Layout'), :action => 'listing' %></li>
    </ul>
    <p class="information">_{.}</p>
</div>

<div class="content">
<h1>_{Deleting Layout}</h1>
<p class="warning"> _{Are you sure you want to definitely delete the layout <strong>"%ContentLayout.name"</strong>?}</p>
 
<%= start_form_tag :action => 'destroy', :id => ContentLayout.id %>

<dl>
	<dt>_{Name}:</dt>
	<dd>{ContentLayout.name}</dd>
	{?ContentLayout.content_type}
	<dt>_{Content Type}:</dt>
	<dd>{ContentLayout.content_type}</dd>
	{end}
</dl>

<div id="operations">
<%= confirm_delete %> _{or} <%= cancel_link %>
</div>
</form>
</div>