<%= render :partial => 'script_constants' %>
<? $capture_helper->begin ('head_after_scripts'); ?>
<%= javascript_include_tag 'file_uploader' %>
<? $capture_helper->end (); ?>
<div id="tasks" class="tasks">
  <ul>
    <li><%= link_to _('Back to existing existing assets Listing'), :action => 'listing' %></li>
  </ul> 
</div>
  
    <%= start_form_tag {:action =>'add'}, :id => 'file_form', 'multipart' => true %>
    <div class="form">
    <h2>_{Adding assets to your library}</h2>

    <%= render :partial => 'form' %>
   
  </div>

  <div id="operations">
    <%= save_button %> _{or}  <%= cancel_link %>
  </div>
  
  </form>
