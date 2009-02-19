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
    <title>{title} _{Login}</title>
    <%= stylesheet_link_tag 'editam' %>
    <%= stylesheet_for_current_controller %>
    <%= javascript_include_tag %>
    <%= javascript_include_tag 'editam' %>
    <script type="text/javascript">
    // <![CDATA[
    var LOGIN_COOKIE_CHECK = '<%= url_for :controller => 'login', :action => 'authenticate', :validate_cookies => 'true' %>';
    var LOGIN_COOKIE_MESSAGE = '_{You need to enable cookies in your browser in order to access the administration interface}';
    // ]]>
    </script>
    <%= javascript_for_current_controller %>
    
</head>
<body>
<div id="layout">

    <div id="canvas" class="login">
        <div id="site_name">
        {?logo}
            <%= image_tag logo %>
        {else}
            <h1>{title}</h1>
        {end}
        </div>
        <div id="login_dialog">
          <%= flash %>
          {content_for_layout}
          <div id="js_flash_message"><span id="js_flash">_{Javascript must be enabled in your browser in order to access the administration interface}</span></div>
        </div>
    </div>
</div>
</body>
</html>