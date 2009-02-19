<ul class="user_actions">
  <li><%= link_to _"logout", :controller => 'account', :action => 'logout', :module => null %></li>
  <li><%= link_to _"preferences", :controller => 'preferences', :action => 'setup', :module => 'editam' %></li>
</ul>
  <%= language_switch_list %>