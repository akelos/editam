<%= render :partial => 'script_constants' %>

<div id="content_menu">
    <ul class="menu">
        <li><%= link_to _('Create new Layout'), :action => 'add' %></li>
        <li class="active"><%= link_to _('Editing Layout'), :action => 'edit', :id => ContentLayout.id %></li>
        <li><%= link_to _('Delete Layout'), :action => 'destroy', :id => ContentLayout.id %></li>
        <li class="primary"><%= link_to _('Show available Layout'), :action => 'listing' %></li>
    </ul>
    <p class="information">_{.}</p>
</div>

<div class="content">
    <%= start_form_tag {:action =>'edit', :id => ContentLayout.id}, :id => 'content_layout_form' %>
    <div class="form">
    <h1>_{Editing Layout}</h1>

    <%= render :partial => 'form' %>
   
  </div>

  <div id="operations">
    <%= save_button %> _{or} <%= save_and_continue_button %> <%= cancel_link %>
  </div>
  
  <div id="delete">
  <%= link_to_destroy ContentLayout %>
  </div>
  
  </form>
</div>