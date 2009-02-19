<%= render :partial => 'script_constants' %>
<? $capture_helper->begin ('head_after_scripts'); ?>
<%= stylesheet_link_tag 'tree' %>
<? $capture_helper->end (); ?>

<div id="tasks" class="tasks">
  <ul>
    <li><%= link_to _("Show the <b>#{Page.breadcrumb}</b> page"), {:action => 'show', :id => Page.id}, :target => 'blank' %></li>
    <li><%= link_to _("Edit <b>#{Page.breadcrumb}</b>"), {:action => 'edit', :id => Page.id} %></li>
    <li><%= link_to _('Back page listing'), :action => 'listing', :id => Page.id %></li>
  </ul> 
</div>
  
<h2><? if($Page->nested_set->countChildren() > 0) : ?>
 _{Are you sure you want to <span class="warn">definitely delete</span> the page <strong>"%Page.title"</strong> and all of the pages below it?}
 <? else: ?>
 _{Are you sure you want to <span class="warn">definitely delete</span> the page <strong>"%Page.title"</strong>?}
 <? endif; ?></h2>
 
<%= start_form_tag :action => 'destroy', :id => page.id %>
<div id="tree" class="deleting">
<%= nested_list Pages, false %>
</div>
<div id="operations">
<%= confirm_delete %> _{or} <%= cancel_link %>
</div>
</form>