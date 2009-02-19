// Written by Sean Treadway 2006 sean@treadway.info
TabControl = function(control_id, options) {
    var id = "#" + control_id;
    $$(id+' ul.tabs li a').each(function(a) {
        var page = a.getAttribute('href').match(/[-_\w]+$/i)[0];

        if (page != options['current']) { $(page).hide() }
        else { $(a.parentNode).addClassName('active') }

        Event.observe(a, 'click', function(e) {
            $$(id+' ul.tabs li.active').each(function(e) { e.removeClassName('active'); })
            $$(id+' .tab_page[id!='+page+']').each(function(e) { e.hide() });
            $(a.parentNode).addClassName('active');
            $(page).show();
            Event.stop(e);
        });
    });
}