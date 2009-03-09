<?=$active_record_helper->error_messages_for('snippet');?>

<fieldset>
    <label class="required" for="snippet_name">_{Name}</label>
    <?=$active_record_helper->input('snippet', 'name', array('tabindex' => '1'))?>
</fieldset>

<fieldset class="snippet_description">
    <label class="required" for="snippet_description">_{Description}</label>
    <?=$active_record_helper->input('snippet', 'description',  array('tabindex' => '3'))?>
</fieldset>

<fieldset class="snippet_content">
    <label class="required" for="snippet_content">_{Content}</label>
    <?=$active_record_helper->input('snippet', 'content',  array('tabindex' => '4'))?>
</fieldset>

<fieldset class="snippet_is_enabled inline">
    <label for="snippet_is_enabled">_{Enable snippet}</label>
    <?=$active_record_helper->input('snippet', 'is_enabled',  array('tabindex' => '5'))?>
</fieldset>