<div id="tasks" class="tasks">
  <ul>
    <li><%= link_to _('Cancel update'), :controller => 'preferences' %></li>
  </ul> 
</div>
  
    <%= start_form_tag {:controller => 'editam_update',:action =>'confirm_update'}, :id => 'perform_update_form' %>
    <div class="form">
    <h2>_{Updating your Editam Installation}</h2>

    <p class="important">
    _{There are some conflics that need to be fixed before updating 
    your Editam system from version <strong>%update_details-from</strong> to <strong>%update_details-to</strong>.}
    </p>
    
    <div id="update_conflicts">
        <h3>_{Please review these conflicts before performing the update.}</h3>
        <? $counter = 0 ?>
        {loop conflicts}
            <h4><%= translate conflict_loop_key %></h4>
            
            <? $conflict_key = md5($conflict_loop_key); ?>
            <? $conflicted_files = $conflict; ?>
            <ul>
             {loop conflicted_files}
                <? $counter++ ?>
                <li class="inline">
                <%= check_box "update[conflicts]", conflicted_file_loop_key, :id => "conflicts_#{counter}" %> 
                <label for="conflicts_{counter}" class="filename">{conflicted_file}</label></li>
             {end}
            </ul>
        {end}
    </div>

    <%= render :partial => 'changelog' %>
    <%= render :partial => 'conformity' %>
    
    </div>
  <div id="operations">
      <%= render :partial => 'update_button' %>
  </div>
  
  </form>
