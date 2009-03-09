<%= render :partial => 'script_constants' %>
<div id="content_menu">
    <ul class="menu">
        <li class="active"><%= link_to _('Creating Snippet'), :action => 'add' %></li>
        <li class="primary"<%= link_to _('Show available Snippet'), :action => 'listing' %></li>
    </ul>
    <p class="information">_{.}</p>
</div>

<div class="content"> 
    <%= start_form_tag {:action =>'add'}, :id => 'snippet_form' %>
    <div class="form">
    <h2>_{Creating Snippet}</h2>

    <%= render :partial => 'form' %>
   
  </div>

  <div id="operations">
    <%= save_button %> _{or} <%= save_and_continue_button %> <%= cancel_link %>
  </div>
  
  </form>
</div>