<p id="form_destination_email_block">
    <label style="margin:0" for="form_destination_email">Deliver this form to:</label>
    <input type="text" id="form_destination_email" name="form_destination_email" value="<?=$FormBehaviour->getDestinationEmail()?>" />
</p>


<script type="text/javascript">

if(Page.parts['destination_email']){
    destination_position = Page.parts['destination_email'];
    destination_email = $('part_'+destination_position+'_content').value;
    $('form_destination_email').value = destination_email;
    Page.removePagePart(destination_position, 1);
}
if(Page.parts['subject']){
    Page.removePagePart(Page.parts['subject']);
}

Page.logFormFields();

function form_stop_observing(){
    Event.stopObserving(window, 'beforeunload', Page.warnIfModified, true);
}

Event.observe($('page_form'), 'submit', form_stop_observing, true);
Event.observe($('save_and_continue'), 'click', form_stop_observing, true);

</script> 