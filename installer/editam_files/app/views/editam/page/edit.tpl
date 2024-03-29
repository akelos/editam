<%= render :partial => 'script_constants' %>
<? $capture_helper->begin ('head_after_scripts'); ?>
<%= stylesheet_link_tag 'tab_canvas' %>
<? $capture_helper->end (); ?>

<div id="content_menu">
    <ul class="menu">
        <li class="active"><%= link_to _('Editing Page'), :action => 'edit', :id => Page.id %></li>
        {?Page.parent_id}
        <li><%= link_to _('Delete Page'), :action => 'destroy', :id => Page.id %></li>
        {end}
        <li class="primary"><%= link_to _('Back to existing Pages list'), :action => 'listing' %></li>
        
    </ul>
    <p class="information">_{.}</p>
</div>

<div class="content">
{?ParentPage}
    
    <%= start_form_tag {:action =>'edit', :id => Page.id }, :id => 'page_form' %>
    <div class="form">
    <div id="parent_page_heading" onmouseover="$('parent_actions').show();" onmouseout="$('parent_actions').hide();">
    <h1>_{Editing Page below <strong>%ParentPage.breadcrumb</strong>} <span id="parent_actions" style="display:none"><%= admin_page_links ParentPage %></span></h1>
    
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
      <p><label for="page_behavior">_{Behavior}</label>
        <%= select "page", "behavior", Behaviors %></p>
      <p><label for="page_status">_{Status}</label>
        <%= select "page", "status", Statuses %></p>
    </div>
    <div id="behavior_options" class="cls"></div>
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
</div>