
var Snippet = {
    logFormFields: function(){
        Snippet.form = $('snippet_form').serialize();
    },
    warnIfModified: function(event){
        if(Snippet.form != $('snippet_form').serialize()){
            event.returnValue = SNIPPET_UNSAVED_CHANGES_WARNING;
        }
    },
    handleKeyDown : function(event){
        // control + S = save and continue
        if(event.ctrlKey && event.keyCode == 83){
            Event.stop(event);
            Snippet.submitAndContinueEditing($('snippet_form'));
        }
    },
    submitAndContinueEditing: function(form){
        form.action = form.action+'?continue_editing=1';
        Snippet.logFormFields();
        form.submit();
    }
}

var OnloadSnippet = {
'edit_mode' : function(){
    if($('snippet_form')){
        Snippet.logFormFields();
        Event.observe($('snippet_form'), 'submit', Snippet.logFormFields, true);
        Event.observe(window, 'beforeunload', Snippet.warnIfModified, true);
        
        Event.observe(window, 'keydown', function(e){Snippet.keyEvent = e;Snippet.handleKeyDown(e);}, true);
        Event.observe(window, 'keyup', function(e){Snippet.keyEvent = undefined;}, true);

        Field.focus('snippet_name');
    }
}
}

Onload.push(OnloadSnippet);