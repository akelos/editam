<%= render :partial => 'script_constants' %>
<? $capture_helper->begin ('head_after_scripts'); ?>
<%= stylesheet_link_tag 'tree' %>
<? $capture_helper->end (); ?>

<div id="content_menu">
    <ul class="menu">
	    <li class="primary"><%= link_to _('Edit Page'), {:action => 'edit', :id => Page.id} %></li>
	    {?Page.parent_id}
        <li class="active"><%= link_to _('Deleting Page'), :action => 'destroy', :id => Page.id %></li>
        {end}
	    <li><%= link_to _('Show available Page'), :action => 'listing', :id => Page.id %></li>
    </ul>
    <p class="information">_{.}</p>
</div>

<div class="content">
<h1>_{Deleting Page}</h1>
<p class="warning"><? if($Page->nested_set->countChildren() > 0) : ?>
 _{Are you sure you want to <span class="warn">definitely delete</span> the page <strong>"%Page.title"</strong> and all of the pages below it?}
 <? else: ?>
 _{Are you sure you want to <span class="warn">definitely delete</span> the page <strong>"%Page.title"</strong>?}
 <? endif; ?></p>
 
<%= start_form_tag :action => 'destroy', :id => page.id %>
<div id="tree" class="deleting">
<%= nested_list Pages, false %>
</div>
<div id="operations">
<%= confirm_delete %> _{or} <%= cancel_link %>
</div>
</form>
</div>