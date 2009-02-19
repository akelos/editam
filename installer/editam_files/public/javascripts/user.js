
var User = {
    logFormFields: function(){
        User.form = $('user_form').serialize();
    },
    warnIfModified: function(event){
        if(User.form != $('user_form').serialize()){
            event.returnValue = SNIPPET_UNSAVED_CHANGES_WARNING;
        }
    },
    handleKeyDown : function(event){
        // control + S = save and continue
        if(event.ctrlKey && event.keyCode == 83){
            Event.stop(event);
            User.submitAndContinueEditing($('user_form'));
        }
    },
    submitAndContinueEditing: function(form){
        form.action = form.action+'?continue_editing=1';
        User.logFormFields();
        form.submit();
    }
}

var OnloadUser = {
'edit_mode' : function(){
    if($('user_form')){
        User.logFormFields();
        $('user_password').value = '';
        $('user_password_confirmation').value = '';
        Event.observe($('user_form'), 'submit', User.logFormFields, true);
        Event.observe(window, 'beforeunload', User.warnIfModified, true);
        
        Event.observe(window, 'keydown', function(e){User.keyEvent = e;User.handleKeyDown(e);}, true);
        Event.observe(window, 'keyup', function(e){User.keyEvent = undefined;}, true);

        Field.focus('user_name');
    }
}
}

Onload.push(OnloadUser);