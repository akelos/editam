<%= render :partial => 'script_constants' %>
<? $capture_helper->begin ('head_after_scripts'); ?>
<%= stylesheet_link_tag 'tab_canvas' %>
<? $capture_helper->end (); ?>

<div id="tasks" class="tasks">
  <ul>
    <li><%= link_to _('Back to existing Pages list'), :action => 'listing', :id => Page.id %></li>
  </ul> 
</div>
  
    
{?ParentPage}
    
    <%= start_form_tag {:action =>'edit', :id => Page.id }, :id => 'page_form' %>
    <div class="form">
    <div id="parent_page_heading" onmouseover="$('parent_actions').show();" onmouseout="$('parent_actions').hide();">
    <h2>_{Editing Page below <strong>%ParentPage.breadcrumb</strong>} <span id="parent_actions" style="display:none"><%= admin_page_links ParentPage %></span></h2>
    
    </div>
    <%= hidden_field 'page', 'parent_id' %>

{else}

    <%= start_form_tag {:action =>'edit', :id => Page.id}, :id => 'page_form' %>
    <div class="form">
    <h2>{?is_homepage}_{Editing homepage}{else}_{Editing Page}{end}</h2>

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
    <div id="behaviour_options" class="cls"></div>
  </div>

  <div id="operations">
    <%= save_button %> _{or} <%= save_and_continue_button %> <%= cancel_link %>
  </div>
  
  {!is_homepage}
  <div id="delete">
  <%= link_to_destroy Page %>
  </div>
  {end}
  
  </form>
