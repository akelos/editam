<%= render :partial => 'script_constants' %>

<div id="content_menu">
    <ul class="menu">
        <li><%= link_to _('Create new Snippet'), :action => 'add' %></li>
        <li class="active"><%= link_to _('Editing Snippet'), :action => 'edit', :id => Snippet.id %></li>
        <li><%= link_to _('Delete Snippet'), :action => 'destroy', :id => Snippet.id %></li>
        <li class="primary"><%= link_to _('Show available Snippet'), :action => 'listing' %></li>
    </ul>
    <p class="information">_{.}</p>
</div>

<div class="content">
    <%= start_form_tag {:action =>'edit', :id => Snippet.id}, :id => 'snippet_form' %>
    <div class="form">
    <h1>_{Editing Snippet}</h1>

    <%= render :partial => 'form' %>
   
  </div>

  <div id="operations">
    <%= save_button %> _{or} <%= save_and_continue_button %> <%= cancel_link %>
  </div>
  
  <div id="delete">
  <%= link_to_destroy Snippet %>
  </div>
  
  </form>
</div>