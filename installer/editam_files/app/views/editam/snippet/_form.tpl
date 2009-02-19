<?=$active_record_helper->error_messages_for('snippet');?>

<p>
    <label class="required" for="snippet_name">_{Name}</label><br />
    <?=$active_record_helper->input('snippet', 'name', array('tabindex' => '1'))?>
</p>

<p class="snippet_description">
    <label class="required" for="snippet_description">_{Description}</label><br />
    <?=$active_record_helper->input('snippet', 'description',  array('tabindex' => '3'))?>
</p>

<p class="snippet_content">
    <label class="required" for="snippet_content">_{Content}</label><br />
    <?=$active_record_helper->input('snippet', 'content',  array('tabindex' => '4'))?>
</p>

<p class="snippet_is_enabled inline">
    <?=$active_record_helper->input('snippet', 'is_enabled',  array('tabindex' => '5'))?>
    <label for="snippet_is_enabled">_{Enable snippet}</label>
</p>