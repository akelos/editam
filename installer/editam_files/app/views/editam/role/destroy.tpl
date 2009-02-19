<div id="tasks" class="tasks">
  <ul>
    <li><%= link_to _('Back to existing Roles list'), :action => 'listing' %></li>
    <li><%= link_to _('Edit this Role'), :action => 'edit', :id => role.id %></li>
  </ul> 
</div>

<div id="content">
    <h2>_{Are you sure you want to <span class="warn">definitely delete</span> the role <strong>"%role.name"</strong>?}</h2>
  
    <?= $form_tag_helper->start_form_tag(array('action' => 'destroy', 'id' => $role->getId())) ?>
        <div id="operations">
            <%= confirm_delete %> _{or} <%= cancel_link %>
        </div>
    <?= $form_tag_helper->end_form_tag() ?>
</div>
