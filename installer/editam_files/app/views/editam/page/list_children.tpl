<%= render :partial => 'script_constants' %>
<? $capture_helper->begin ('head_after_scripts'); ?>
<%= stylesheet_link_tag 'tree' %>
<? $capture_helper->end (); ?>
<%= nested_list_content ParentPage %>
{?Pages}
    <ul id="children_for-{ParentPage.id}">
    {loop Pages}
        <%= nested_list_item Page %>
    {end}
    </ul>
{end}