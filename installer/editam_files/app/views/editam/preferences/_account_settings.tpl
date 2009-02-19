<?=$active_record_helper->error_messages_for('user');?>

<%= start_form_tag {:action =>'user', :id => user.id}, :id => 'user_form' %>
    <h2>_{Account settings}</h2>
         <div class="form">
_{Name}:</td>
                <td width="70%"><?=$active_record_helper->input('user', 'name')?></td>
            </tr>

            <tr>
                <td align="right">_{Email}:</td>
                <td width="70%"><?=$active_record_helper->input('user', 'email')?></td>
            </tr>

            <tr>
                <td align="right">_{Login}:</td>
                <td width="70%"><?=$active_record_helper->input('user', 'login')?></td>
            </tr>

            <tr>
                <td align="right">_{Password}:</td>
                <td width="70%"><input id="user_password" name="user[password]" size="30" type="password" /></td>
            </tr>
            
            <tr>
                <td align="right">_{Password confirmation}:</td>
                <td width="70%"><input id="user_password_confirmation" name="user[password_confirmation]" size="30" type="password" /></td>
            </tr>

        </tbody>
        </table>
    </div>
    
    <div id="operations">
        <%= save_button %>
    </div>
<%= end_form_tag %>
