<%= render :partial => 'script_constants' %>
<? $capture_helper->begin ('head_after_scripts'); ?>
<%= stylesheet_link_tag 'tree' %>
<? $capture_helper->end (); ?>

<div id="content_menu">
    <ul class="menu">
        <li><%= link_to _('Clear pages cache'), :action => 'clear_cache' %></li>
        <li class="active"><%= link_to _('Listing Pages'), :action => 'listing' %></li>
    </ul>
    <p class="information">_{.}</p>
</div>

<div class="content">    
    <h1>_{Listing available pages}</h1>
    <div id="tree">
       <%= reverse_nested_list Pages %>
    </div>
</div>