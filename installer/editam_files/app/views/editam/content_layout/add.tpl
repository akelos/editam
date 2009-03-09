<%= render :partial => 'script_constants' %>

<div id="content_menu">
    <ul class="menu">
        <li class="active"><%= link_to _('Creating new Layout'), :action => 'add' %></li>
        <li class="primary"><%= link_to _('Show available Layout'), :action => 'listing' %></li>
    </ul>
    <p class="information">_{.}</p>
</div>

<div class="content">  
    <%= start_form_tag {:action =>'add'}, :id => 'content_layout_form' %>
    <div class="form">
    <h1>_{Creating Layout}</h1>

    <%= render :partial => 'form' %>
   
  </div>

  <div id="operations">
    <%= save_button %> _{or} <%= save_and_continue_button %> <%= cancel_link %>
  </div>
  
  </form>
</div>