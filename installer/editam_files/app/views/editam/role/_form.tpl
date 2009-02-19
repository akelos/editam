<?=$active_record_helper->error_messages_for('role');?>

<p>
    <label for="role_name">_{Name}</label><br />
    <?=$active_record_helper->input('role', 'name', array('tabindex' => '1'))?>
</p>

<p>
    <label for="role_description">_{Description}</label><br />
    <?=$active_record_helper->input('role', 'description', array('rows'=>4, 'cols'=>60, 'tabindex' => '2'))?>
</p>
