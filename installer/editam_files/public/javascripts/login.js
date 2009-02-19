

var OnloadLogin = {
    'clear_flash' : function(){
        flash();
    },
    'form_focus' : function(){
        Field.focus('user_login');
    },
    'cookie_check' : function(){
        new Ajax.Request(LOGIN_COOKIE_CHECK, {
            onFailure:function(request){
                flash(LOGIN_COOKIE_MESSAGE, 20);
            }
        });
    }
}

Onload.push(OnloadLogin);
