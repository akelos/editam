<div id="tasks" class="tasks">
  <ul>
    <li><%= link_to _('Create new Role'), :action => 'add' %></li>
    <li><%= link_to _('Manage Permission'), :controller => 'permission', :action => 'listing' %></li>
    <li><%= link_to _('Manage Users'), :controller => 'user', :action => 'listing' %></li>
  </ul> 
</div>

<h2>_{Listing available Roles}</h2>

{?roles}
    <div class="listing">
        <table cellspacing="0" summary="_{Listing available Roles}">
        <tbody>
            <tr class="heading">
                <th scope="col"><%= sortable_link 'name' %></th>
                <th scope="col"><%= sortable_link 'description' %></th>
                <th colspan="2" scope="col"><span class="auraltext">_{Role actions}</span></th>
            </tr>
            
            {loop roles}
                <tr {?role_odd_position}class="odd"{end}>
                    <td class="main field"><?= $url_helper->link_to($role->get('name'), array('action'=>'edit', 'id'=>$role->getId())) ?></td>
                    <td class="field">{?role.description}<?= $url_helper->link_to($role->get('description'), array('action'=>'edit', 'id'=>$role->getId())) ?> {end} </td>
                    <td class="operation"><?= $role_helper->link_to_destroy($role)?></td>
                    <td class="operation"><?= $role_helper->link_to_edit($role)?></td>
                </tr>
            {end}
        </tbody>
        </table>
    </div>
{end}

{?role_pages.links}
    <div id="RolePagination">
    <div id="paginationHeader"><?=translate('Showing page %page of %number_of_pages',array('%page'=>$role_pages->getCurrentPage(),'%number_of_pages'=>$role_pages->pages))?></div>
    {role_pages.links?}
    </div>
{end}