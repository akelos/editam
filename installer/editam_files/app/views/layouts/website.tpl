<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{title}</title>
    <%= stylesheet_link_tag 'akelos' %>
    <%= stylesheet_link_tag 'editam' %>
    <%= stylesheet_for_current_controller %>
    <%= javascript_include_tag %>
    <%= javascript_include_tag 'editam' %>
    <%= javascript_for_current_controller %>
</head>
<body>
<div id="layout">

    <%= render :partial => 'menu' %>

      <%= flash %>
      {content_for_layout}
      
    <%= render :partial => 'side_bar' %>
    
    <? /* Please be gentle and link to the Editam site. */ ?>
    <p id="footer">
    <a href="http://editam.com">
        <%= image_tag 'powered_by_editam_dark_bg', {:size => '120x60', :title => translate('Powered by Editam CMS') } %> 
    </a>
    <? $date = date('Y');?>
    _{Powered by <a href="http://editam.com">the CMS Editam</a> and the <a href="http://www.akelos.org">PHP Framework</a> 
    <a href="http://akelos.org">Akelos</a>. © %date <a href="http://www.akelos.com">Akelos Media</a>.}
    </p>
    
</div>
</body>
</html>