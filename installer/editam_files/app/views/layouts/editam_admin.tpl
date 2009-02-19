<hidden>
// +----------------------------------------------------------------------+
// Editam is a content management platform developed by Akelos Media, S.L.|
// Copyright (C) 2006 - 2007 Akelos Media, S.L.                           |
//                                                                        |
// This program is free software; you can redistribute it and/or modify   |
// it under the terms of the GNU General Public License version 3 as      |
// published by the Free Software Foundation.                             |
//                                                                        |
// This program is distributed in the hope that it will be useful, but    |
// WITHOUT ANY WARRANTY; without even the implied warranty of             |
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                   |
// See the GNU General Public License for more details.                   |
//                                                                        |
// You should have received a copy of the GNU General Public License      |
// along with this program; if not, see http://www.gnu.org/licenses or    |
// write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth |
// Floor, Boston, MA 02110-1301 USA.                                      |
//                                                                        |
// You can contact Akelos Media, S.L. headquarters at                     |
// C/ Pasodoble Amparito Roca, 6, 46240 - Carlet (Valencia) - Spain       |
// or at email address contact@akelos.com.                                |
//                                                                        |
// The interactive user interfaces in modified source and object code     |
// versions of this program must display Appropriate Legal Notices, as    |
// required under Section 5 of the GNU General Public License version 3.  |
//                                                                        |
// In accordance with Section 7(b) of the GNU General Public License      |
// version 3, these Appropriate Legal Notices must retain the display of  |
// the "Powered by Editam" logo. If the display of the logo is not        |
// reasonably feasible for technical reasons, the Appropriate Legal       |
// Notices must display the words "Powered by Editam".                    |
// +----------------------------------------------------------------------+
</hidden><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{title}, Editam Content Management Platform</title>
        
    {content_for_head?}
    
    <hidden>
    Including the theme color as a parameter of the CSS avoids browser catching of the CSS when switching colors
    </hidden>
    <?
		/*
		 * @todo : replace #f70 with <%= settings_for 'core','editam_admin_color' %>
		 */
    ?>
    <link href="{site_url}/stylesheets/editam.css?theme=#f70" media="screen" rel="Stylesheet" type="text/css" />
    
    <%= stylesheet_for_current_controller %>
    
    {content_for_head_after_styles?}
    
    <%= javascript_include_tag 'protoculous' %>

    <%= javascript_include_tag 'editam' %>
    <%= javascript_for_current_controller %>
    
    <script type="text/javascript">
    // <![CDATA[
    {content_for_script?}
    // ]]>
    </script>
    
    {content_for_head_after_scripts?}
    
    <!--[if lt IE 7]>
    <style type="text/css">img, div { behavior: url('{site_url}/stylesheets/iepngfix.htc'); }</style>
    <%= stylesheet_link_tag 'editam-ie' %>
    <![endif]-->
    
</head>
<body>
<!--[if lt IE 7]>
<div style="width:90%;padding:15px;margin:15px;font-size:12px;background-color:#ff0;color:#000;">
<p>_{Editam <strong>administration interface</strong> uses <a href="http://www.webstandards.org/">Web Standards</a> to enhance your experience while managing your site. <br />Unfortunately you are running an old version of Internet Explorer which doesn't play well with standards and can make your Editam admin behave in strange ways. <br />Please update to <a href="http://www.getfirefox.com">Firefox</a> or <a href="http://www.microsoft.com/windows/products/winfamily/ie/default.mspx">Internet Explorer 7</a>, to improve your web experience.}</p>
</div>
<![endif]-->
<div id="js_flash_message"><span id="js_flash">_{Loading...}</span></div>
<div id="layout">
    <div id="site_name">
        {?logo}
            <%= image_tag logo %>
        {else}
            <h1>{title}</h1>
        {end}
    </div>
    
    <%= render :partial => 'editam_admin/user_menu' %>
    <div class="cls"></div>
    <div id="canvas">
      <div id="menu">
          <ul>
            <%= render :partial => 'editam_admin/menu' %>
          </ul>
      </div>
      <%= flash %>
      {content_for_layout}
      <div class="cls"></div>
    </div>
    <div class="cls"></div>
    <%= render :partial => 'editam_admin/footer' %>
</div>
</body>
</html>