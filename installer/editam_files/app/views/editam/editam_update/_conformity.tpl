    <p class="important">
    _{Please be aware that this automated process <strong>can result in data loss</strong>.<br />
    Before performing this update you need to <strong>backup all your files and databases</strong>.}
    </p>

    <p class="inline confirmation">
    <%= check_box 'update','confirm', :onclick => "$('perform_update')[$('perform_update').disabled ? 'enable' : 'disable']();" %>
<label for="update_confirm">_{I confirm that I have performed a backup of my filesystem and database, and want to proceed with the automatic update under my responsability.}</label>
    </p>
    
    <%= hidden_field 'update', 'from', :value => update_details-from %>
    <%= hidden_field 'update', 'to', :value => update_details-to %>