    {?Pages}
        <ul id="children_for-{ParentPage.id}"> 
        {loop Pages}
            <li class="child {ParentPage.status} node-{Page.parent_id}-{Page.lft}-{Page.rgt}" id="page-{Page.id}">
            <%= link_to Page.title , :action => 'edit', :id => Page.id %>
            <%= link_to _('Edit'), :action => 'edit', :id => Page.id %> <%= link_to _('Add child'), :action => 'add_child', :parent_id => Page.id %>   <%= expand_tree_link Page %>  </li>
        {end}
        </ul>
    {end}