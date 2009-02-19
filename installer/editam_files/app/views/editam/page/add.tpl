<%= render :partial => 'script_constants' %>
<? $capture_helper->begin ('head_after_scripts'); ?>
<%= stylesheet_link_tag 'tab_canvas' %>
<? $capture_helper->end (); ?>

{?is_homepage}
      <%= start_form_tag {:action =>'add'}, :id => 'page_form' %>
    
      <div class="form">
        <h2>_{Creating the Home Page}</h2>
{else}
    <div id="tasks" class="tasks">
      <ul>
        <li>
        {?ParentPage}
        <%= link_to _('Back to existing Pages list'), :action => 'listing', :id => ParentPage.id %>
        {else}
        <%= link_to _('Back to existing Pages list'), :action => 'listing' %>
        {end}
        </li>
      </ul> 
    </div>
      
      {?ParentPage}
      
        <%= start_form_tag {:action =>'add_child', :parent_id => ParentPage.id }, :id => 'page_form' %>
        <div class="form">

        <div id="parent_page_heading" onmouseover="$('parent_actions').show();" onmouseout="$('parent_actions').hide();">
        <h2>_{Inserting a new Page below <strong>%ParentPage.breadcrumb</strong>} <span id="parent_actions" style="display:none"><%= admin_page_links ParentPage %></span></h2>
        </div>
        
        <%= hidden_field 'page', 'parent_id' %>
        
      {else}
      
        <%= start_form_tag {:action =>'add'}, :id => 'page_form' %>
        <div class="form">
        <h2>_{Creating a new Page}</h2>
        
      {end}
{end}

    <%= render :partial => 'form' %>
    
    <div class="page_options">
    {?Layouts}
      <p><label for="page_layout_id">_{Layout}</label>
        <%= select "page", "layout_id", Layouts %></p>
    {end}
      <p><label for="page_behaviour">_{Behaviour}</label>
        <%= select "page", "behaviour", Behaviours %></p>
      <p><label for="page_status">_{Status}</label>
        <%= select "page", "status", Statuses %></p>
    </div>
   
  </div>

  <div id="operations">
    <%= save_button %> _{or} <%= save_and_continue_button %> {!is_homepage}<%= cancel_link %>{end}
  </div>

  </form>
