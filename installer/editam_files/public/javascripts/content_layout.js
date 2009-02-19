
var ContentLayout = {
    logFormFields: function(){
        ContentLayout.form = $('content_layout_form').serialize();
    },
    warnIfModified: function(event){
        if(ContentLayout.form != $('content_layout_form').serialize()){
            event.returnValue = LAYOUT_UNSAVED_CHANGES_WARNING;
        }
    },
    handleKeyDown : function(event){
        // control + S = save and continue
        if(event.ctrlKey && event.keyCode == 83){
            Event.stop(event);
            ContentLayout.submitAndContinueEditing($('content_layout_form'));
        }
    },
    submitAndContinueEditing: function(form){
        form.action = form.action+'?continue_editing=1';
        ContentLayout.logFormFields();
        form.submit();
    }
}

var OnloadContentLayout = {
'edit_mode' : function(){
    if($('content_layout_form')){
        ContentLayout.logFormFields();
        Event.observe($('content_layout_form'), 'submit', ContentLayout.logFormFields, true);
        Event.observe(window, 'beforeunload', ContentLayout.warnIfModified, true);
        
        Event.observe(window, 'keydown', function(e){ContentLayout.keyEvent = e;ContentLayout.handleKeyDown(e);}, true);
        Event.observe(window, 'keyup', function(e){ContentLayout.keyEvent = undefined;}, true);

        Field.focus('content_layout_name');
    }
}
}

Onload.push(OnloadContentLayout);