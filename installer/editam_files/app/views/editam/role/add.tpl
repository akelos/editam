<div id="tasks" class="tasks">
  <ul>
    <li><%= link_to _('Back to existing Roles list'), :action => 'listing' %></li>
  </ul> 
</div>
  
    <%= start_form_tag {:action =>'add'}, :id => 'user_form' %>
    <div class="form">
    <h2>_{Creating Role}</h2>

    <%= render :partial => 'form' %>
   
  </div>

  <div id="operations">
    <%= save_button %> _{or} <%= cancel_link %>
  </div>
  
  </form>
