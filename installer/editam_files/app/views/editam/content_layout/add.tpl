<%= render :partial => 'script_constants' %>

<div id="tasks" class="tasks">
  <ul>
    <li><%= link_to _('Back to existing Layouts list'), :action => 'listing' %></li>
  </ul> 
</div>
  
    <%= start_form_tag {:action =>'add'}, :id => 'content_layout_form' %>
    <div class="form">
    <h2>_{Creating Layout}</h2>

    <%= render :partial => 'form' %>
   
  </div>

  <div id="operations">
    <%= save_button %> _{or} <%= save_and_continue_button %> <%= cancel_link %>
  </div>
  
  </form>
