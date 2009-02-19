<div id="tasks" class="tasks">
  <ul>
    <li><%= link_to _('Back to existing Roles list'), :action => 'listing' %></li>
    <li><%= link_to _('Remove this Role'), :action => 'destroy', :id => role.id %></li>
  </ul> 
</div>
  
    <%= start_form_tag {:action =>'edit', :id => role.id}, :id => 'user_form' %>
    <div class="form">
    <h2>_{Editing Role}</h2>

    <%= render :partial => 'form' %>
   
  </div>

  <div id="operations">
    <%= save_button %> _{or} <%= cancel_link %>
  </div>
  
  </form>
