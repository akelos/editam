<div id="tasks" class="tasks">
  <ul>
    <li><%= link_to _('Cancel update'), :controller => 'preferences' %></li>
  </ul> 
</div>
  
    <%= start_form_tag {:controller => 'editam_update',:action =>'confirm_update'}, :id => 'perform_update_form' %>
    <div class="form">
    <h2>_{Updating your Editam Installation}</h2>
    
    <%= render :partial => 'changelog' %>
    <%= render :partial => 'conformity' %>
    
    </div>
  <div id="operations">
      <%= render :partial => 'update_button' %>
  </div>
  
  </form>
