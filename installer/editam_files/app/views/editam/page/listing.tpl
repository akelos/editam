<%= render :partial => 'script_constants' %>
<? $capture_helper->begin ('head_after_scripts'); ?>
<%= stylesheet_link_tag 'tree' %>
<? $capture_helper->end (); ?>

<div id="tasks" class="tasks">
  <ul>
    <li><%= link_to _('Clear pages cache'), :action => 'clear_cache' %></li>
  </ul> 
</div>
    
<h2>_{Listing available pages}</h2>
<div id="tree">
<%= reverse_nested_list Pages %>
</div>