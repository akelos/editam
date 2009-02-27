
/*
window.onload = function(){
    if($('login_login')) Field.focus('login_login');
    if($('login_username')){
        Field.focus('login_username');
        Event.observe($('login_username'), 'keyup', Account.handleLoginUniquenessCheck, true);
    }
}
*/

var Account = {
    unavailable_logins: new Array(),
    available_names: new Array(),

    handleLoginUniquenessCheck: function(){
        var login = $('login_username').value;
        if(login.length > 3){
            if(Account.unavailable_logins.indexOf(login) == -1){
                if(Account.available_names.indexOf(login) == -1){
                    Account.checkIfLoginIsAvailable(login);
                }else{
                    Account.informLoginIsAvailable();
                }
            }else{
                Account.informLoginIsNotAvailable();
            }
        }
    },

    checkIfLoginIsAvailable: function(login){
        new Ajax.Request(LOGIN_CHECK_URL+'?login='+login, {
            method: 'get',
            onSuccess: function(transport) {
                if(transport.responseText == '1'){
                    Account.available_names.push(login);
                    Account.informLoginIsAvailable();
                }else{
                    Account.unavailable_logins.push(login);
                    Account.informLoginIsNotAvailable();
                }
            }
        });
    },

    informLoginIsAvailable: function(){
        $('login_check').hide();
    },

    informLoginIsNotAvailable: function(){
        $('login_check').show();
    }
}

// editam part from login.js


var OnloadLogin = {
    'clear_flash' : function(){
        flash();
    },
    'form_focus' : function(){
        Field.focus('login_username');
    },
    'cookie_check' : function(){
        new Ajax.Request(LOGIN_COOKIE_CHECK, {
            onFailure:function(request){
                flash(LOGIN_COOKIE_MESSAGE, 20);
            }
        });
    },
    'admin_plugin_routine' : function(){
    	if($('login_login')) Field.focus('login_login');
	    if($('login_username')){
	        Field.focus('login_username');
	        Event.observe($('login_username'), 'keyup', Account.handleLoginUniquenessCheck, true);
	    }
    }
}

Onload.push(OnloadLogin);


