<?php

$current_controller = $controller->getUrlizedControllerName();
$current_action = @$controller->params['action'];

?>
{loop menu_options}
{?menu_option-admin_only}
<?
	
	/*
	 * @todo : replace credentials.is_admin
	 * 
  {!credentials.is_admin}
    <? continue; ?>
  {end}
	*/
?>
{end}

<li id="{menu_option-id}_link" class="tab{?menu_option_is_last} last{end}{?menu_option_is_first} first{end}<?=
(@$selected_tab == $menu_option_loop_key || 
  $current_controller == $menu_option['url']['controller'] && (empty($menu_option['url']['action']) || $current_action == $menu_option['url']['action']) 
  ? ' active' : '')
?>"><a href="<%= url_for menu_option-url %>"><%= translate menu_option_loop_key %></a></li>
{end}

{?content_for_controller_menu}
  {content_for_controller_menu}
{else}
  {?controller_menu_options}
  <h2>{_controller_name}</h2>
  <%= controller_menu %>
  {end}
{end}