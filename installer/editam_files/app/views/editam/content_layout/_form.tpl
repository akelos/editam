<?=$active_record_helper->error_messages_for('content_layout');?>

<p>
    <label class="required" for="content_layout_name">_{Name}</label><br />
    <?=$active_record_helper->input('content_layout', 'name', array('tabindex' => '1'))?>
</p>

<p>
<%= link_to _('More options'), {}, 
:onclick => "$('extra_options').toggle();$('more_options').toggle();return false;", 
:tabindex => 2, 
:class => 'action',
:id => 'more_options' %>
    <div style="display:none;" id="extra_options">

    <labelfor="content_layout_content_type">_{Content type}</label><br />
    <?=$active_record_helper->input('content_layout', 'content_type')?>
    
    <%= link_to _('Less options'), {}, 
    :onclick => "$('extra_options').toggle();$('more_options').toggle();return false;", 
    :tabindex => 2, 
    :class => 'action' %>
    </div>
</p>
    
<p class="content_layout_content">
    <label class="required" for="content_layout_content">_{Layout code}</label><br />
    <?=$active_record_helper->input('content_layout', 'content',  array('tabindex' => '4'))?>
</p>
