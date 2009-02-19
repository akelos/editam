
var Page = {
    activePart:null,
    load: function(){
        Page.parts = $H();
        Page._removed_parts = $H();
        Page.loadParts();

        PAGE_IS_FIRST ? null : Page.frozen_slug = Page.frozen_slug_preview = ($('page_slug').value != $('page_title').value.toSlug());
        Page.frozen_breadcrumb = !($('page_breadcrumb').value == $('page_title').value);

        Event.observe($('page_title'), "keyup", Page.updateOptionalFields.bindAsEventListener($('page_title')));
        Event.observe($('page_title'), "keypress", Page.updateOptionalFields.bindAsEventListener($('page_title')));

    },
    updateOptionalFields: function(event) {
        PAGE_IS_FIRST ? null : Page.updateSlug(Event.element(event).value.toSlug());
        if(!Page.frozen_breadcrumb){
            $('page_breadcrumb').value = Event.element(event).value;
        }

    },

    updateSlug :function(value){
        if(PAGE_IS_FIRST){
            return;
        }
        if(!Page.frozen_slug){
            $('page_slug').value = value;
        }
        if(value != '' || $('parent_slug').innerHTML == ''){
            if(!Page.frozen_slug_preview){
                $('page_slug_preview').innerHTML = ($('parent_slug').innerHTML == '' && value != '/' ? '/' : '') + value;
            }
            $('url_for_page').show();
        }else{
            $('url_for_page').hide();
        }

        Page.frozen_slug_preview = Page.frozen_slug;

        if(Page.originalPageViewLink && Page.originalPageViewLink != Page.getFullUrl()){
            $('show_page_link').hide();
        }else{
            Page.showPageLink();
        }

    },

    showPageLink : function(){
        var url = Page.getFullUrl();
        if(url != ''){
            $('show_page_link').show();
            $('show_page_link').href = url;
            Page.originalPageViewLink = url;
        }
    },

    getFullUrl :function(){
        try{
            var result = !$('current_page_url') ? '' : $('current_page_url').textContent.replace(/^(\s*)|(\s*)$/gm, '');
        }catch(e){}
        return result;
    },

    loadParts: function(){
        $$('#tab_control ul.tabs li a').each(function(a) {
            if(matched = a.getAttribute('href').match(/[-_\w]+$/i )){
                Page.loadPart(id_of($(matched[0])));
            }
        });
    },
    loadPart: function (part_id){
        Page.parts[$('part_'+part_id+'_name').value] = parseInt(part_id);
        Page._last_part = parseInt(part_id);
    },
    unloadPart: function (part_name){
        Page._removed_parts[Page.parts[part_name]] = Page.parts[part_name];
        Page.parts.remove(part_name);
    },
    moreOptions: function(){
        new Effect.BlindDown('extra_fields', {duration: 0.3});
        $('hide_extra_fields').toggle();
        $('show_extra_fields').toggle();
        PAGE_IS_FIRST ? setTimeout("Field.focus($('page_breadcrumb'));", 300) : setTimeout("Field.focus($('page_slug'));", 300);
        return false;
    },
    lessOptions: function(){

        new Effect.BlindUp('extra_fields', {duration: 0.3, afterFinish:function(){$('hide_extra_fields').toggle();$('show_extra_fields').toggle();}});
        return false;
    },

    addPagePart: function(name, skip_prompt){
        name = name || '';
        var part_name = prompt(PAGE_NEW_PART_PROMPT, name);
        if(!Page._addPart(part_name)){
            if(part_name && confirm(PAGE_PART_ADD_ERROR+' "'+part_name+"\" "+
            PAGE_PART_BECAUSE+" \n"+Page._error+"\n "+PAGE_PART_RETRY)){
                Page.addPagePart(part_name);
            }
        }
        return false;
    },

    _addPart : function(part_name, skip_flash){
        if(part_name == null){
            return true;
        }

        if(Page.parts[part_name]){
            Page._error = PAGE_PART_DUPLICATED+' "'+part_name+'"';
            return false;
        }
        Page._insertNewPartHtml(Page._last_part+1,part_name);
        Page.loadPart(Page._last_part+1);
        if($$('#tab_control ul.tabs li a').size() == 8){
            $('add_tab').hide();
        }
        if(!skip_flash){
            flash(PAGE_PART_ADDED+' "'+part_name+'"', 2);
        }
        return true;
    },

    logFormFields: function(){
        Page.form = $('page_form').serialize();
        return true;
    },
    warnIfModified: function(event){
        if(Page.form != $('page_form').serialize()){
            event.returnValue = PAGE_UNSAVED_CHANGES_WARNING;
        }
    },

    removePagePart: function(part_id, confirmed){
        confirmed = confirmed || confirm(PAGE_PART_DELETE_CONFIRM+' "'+$('part_'+part_id+'_name').value+'"?');
        if(confirmed){
            Page.unloadPart($('part_'+part_id+'_name').value);
            Element.remove($('page_part_name-'+part_id).parentNode);
            Element.remove($('page_part-'+part_id));
            $$('#tab_control ul.tabs li.active').each(function(e) { e.removeClassName('active'); });
            $('page_part_name-1').parentNode.addClassName('active');
            $('page_part-1').show();
            Field.focus($('part_1_content'));
            $('add_tab').show();
            Page.loadParts();
        }
        return false;
    },

    handleKeyPress : function(event){

    },
    handleKeyDown : function(event){

        // control + S = save and continue
        if(event.ctrlKey && event.keyCode == 83){
            Event.stop(event);
            Page.submitAndContinueEditing($('page_form'));
        }
    },
    _insertNewPartHtml :function(id, name)
    {
        new Insertion.After(
        $('page_part_name-'+(id-1)).parentNode,
        Page._getPagePartHtmlForTab(id,name)
        );
        new Insertion.After(
        $('page_part-'+(id-1)),
        Page._getPagePartHtmlForBody(id,name)
        );
        $$('#tab_control ul.tabs li.active').each(function(e) { e.removeClassName('active'); });
        TabControl('tab_control',{current:'page_part-'+id});
        $('page_part-'+id).show();
        $('part_'+id+'_filter').lastValue = '';
        Event.observe($('part_'+id+'_filter'), 'change', Page.switchFilter, true);
    },
    _getPagePartHtmlForBody: function(id, name){
        return '<div id="page_part-'+id+'" class="tab_page">' +
        '    <div class="part_options">' +
        '        <input id="part_'+id+'_name" name="part['+id+'][name]" value="'+name+'" type="hidden">' +
        '        <p><label for="part_'+id+'_filter">'+PAGE_PART_FILTER_CAPTION+'<\/label> <select id="part_'+id+'_filter" name="part['+id+'][filter]" tabindex="5" class="part_filter">' +
        Page._getPagePartHtmlForFilterOptions() +
        '        <\/select><\/p><a href="\/admin\/page\/remove_part\/" class="action" onclick="Page.removePagePart('+id+');return false;">'+PAGE_PART_REMOVE+' '+name+'<\/a>' +
        '    <\/div>' +
        '    <textarea cols="60" id="part_'+id+'_content" name="part['+id+'][content]" rows="10" tabindex="7" class="content">' +
        '<\/textarea>' +
        '<\/div>';

    },

    _getPagePartHtmlForTab: function(id, name){
        return '<li class="page_tab"><a id="page_part_name-'+id+'" href="#page_part-'+id+'" name="page_part_name-'+id+'">'+name+'</a></li>';
    },

    _getPagePartHtmlForFilterOptions: function(){
        return '<option value="">-- none --<\/option>' +
        '<option value="textile"> Textile<\/option>' +
        '<option value="smartypants">Smartypants<\/option>' +
        '<option value="markdown">Markdown<\/option>';
    },

    updatePageViewLink: function(){
        Page.updateOptionalFields();
        $('page_title').value;

    },

    saveAndContinueCaption :function(){
        if(PAGE_IS_VIRTUAL){
            return;
        }
        if(Page.keyEvent && Page.keyEvent.shiftKey){
            $('save_and_continue').value = PAGE_SAVE_AND_ADD_CHILD;
        }else{
            $('save_and_continue').value = PAGE_SAVE_AND_CONTINUE;
        }

    },
    submitAndContinueEditing: function(form){
        var next_action = $('save_and_continue').value == PAGE_SAVE_AND_ADD_CHILD ? '&next=child' : '';
        form.action = form.action+'?continue_editing=1'+next_action+($(Page.activePart)?'#'+Page.activePart:'');
        Page.logFormFields();
        form.submit();
    },


    switchFilter: function(event){
        item = Event.element(event);
        var content_id = item.id.replace('filter','content');
        var from = item.lastValue || 'html';
        var to = item.value || 'html';
        var position = item.id.match(/_([0-9]+)_/).last();
        item.lastValue = item.value;

        if(Page.keyEvent && Page.keyEvent.shiftKey && from != to){
            item.disable();
            flash(PAGE_SWITCHING_FILTER);

            var content = $(content_id).value;

            new Ajax.Request(PAGE_CONVERT_CONTENT, {
                asynchronous:true,
                parameters:{'to':to,'from':from,'content':content},
                onFailure:function(request){
                    item.enable();
                    flash(PAGE_CANT_APPLY_FILTER, 6);
                },
                onSuccess:function(request){
                    item.enable();
                    $(content_id).value = request.responseText;
                    flash();
                }
            });
        }
        //function(e){console.log(event.element.value);console.log(e)}
    },

    
    
    

    switchBehaviour: function(event){
        Page.enableBehaviour(Event.element(event))
    },
    
    
    enableBehaviour: function(item){
        var from = item.lastValue || '';
        var to = item.value || '';
        if(from != to){
            item.disable();
            flash(PAGE_SWITCHING_BEHAVIOUR);
            new Ajax.Updater('behaviour_options', PAGE_SWITCH_BEHAVIOUR, {
                asynchronous:true,
                evalScripts: true,
                parameters:{ 'to':to,'from':from },
                onFailure:function(request){
                    item.enable();
                    flash(PAGE_CANT_CHANGE_BEHAVIOUR, 6);
                },
                onSuccess:function(request){
                    item.enable();
                    flash();
                }
            });
            item.lastValue = to;
        }
    },
    
    


    Tree : {
        _expanded_ids : new Array(),
        _draggables_enabled : false,
        current_item : null,
        from_parent : null,
        next_sibling : null,
        previous_sibling : null,
        insert : 'left',
        target: false,
        expand: function(id, button){
            if(!this._expanded_ids[id]){
                this._expanded_ids[id] = true;
                flash('Loading children pages...');
                Element.addClassName(button, 'spinner');
                new Ajax.Request(PAGE_LIST_CHILDREN_URL + id, {
                    asynchronous:true,
                    insertion:Insertion.Bottom,
                    onFailure:function(request){
                        flash('Error while loading children pages', 6);
                        Element.removeClassName(button, 'spinner');
                    },
                    onSuccess:function(request){
                        $('page-'+id).innerHTML = request.responseText;
                        flash();
                        Page.Tree.enable();
                    }
                });
            }else{
                $$('#page-'+id+' ul').first().show();
                Element.addClassName(button.id, 'collapse');
                Element.removeClassName(button.id, 'expand');
            }
        },
        collapse: function(id, button){
            this._expanded_ids[id] = true;
            $$('#page-'+id+' ul').first().hide();
            Element.addClassName(button.id, 'expand');
            Element.removeClassName(button.id, 'collapse');
        },
        expand_or_collapse: function(id, button){
            if(Element.hasClassName(button.id, 'collapse')){
                Page.Tree.collapse(id, button);
            }else{
                Page.Tree.expand(id, button);
            }
        },
        reorder: function(target, source){
            new Ajax.Request(PAGE_MOVE_NODE, {parameters:{'from':target,'pos':Page.Tree.insert,'to':source}, asynchronous:true,
            onFailure: function(){
                flash('Could not move Page. A page with the same slug as '+
                $$('#page-'+source+' span.page_title a').first().innerHTML
                +' exists inside '+$$('#page-'+id_of($('page-'+source).parentNode)+' span.page_title a').first().innerHTML, 12);
                if(Page.Tree.next_sibling){
                    $('children_for-'+id_of(Page.Tree.from_parent)).insertBefore($('page-'+source), Page.Tree.next_sibling);
                }else{
                    $('children_for-'+id_of(Page.Tree.from_parent)).appendChild($('page-'+source));
                }
                Page.Tree.enable();
                new Effect.Highlight($('page-'+source),{duration: 3,startcolor:'#ffcccc'});
            }
            });
        },
        enable:function(){
            if($('tree') && !Element.hasClassName($('tree'),'deleting')){
                var root = $$('li.sortable_pages').first();
                if(root != undefined){
                    Sortable.create(root, {tree:true,scroll:window,handle:'page_handler',greedy:false});
                }

            }
            Page.Tree.activateDraggables();
        },
        activateDraggables: function(){
            if(!Page.Tree_draggables_enabled){
                Page.Tree_draggables_enabled = true;
                Draggables.addObserver({
                    onStart: function(name, draggable, event) {
                        Page.Tree.current_item = draggable.element;
                        Page.Tree.from_parent = draggable.element.parentNode;
                        Page.Tree.next_sibling = draggable.element.nextSibling;
                        Page.Tree.previous_sibling = draggable.element.previousSibling;

                    },
                    onEnd: function(name, draggable, event) {
                        if(draggable.element.nextSibling){
                            Page.Tree.target = draggable.element.nextSibling;
                            Page.Tree.insert = 'right';
                        }else if(draggable.element.previousSibling){
                            Page.Tree.target = draggable.element.previousSibling;
                        }

                        if(Page.Tree.target && (
                        Page.Tree.next_sibling != draggable.element.nextSibling ||
                        Page.Tree.previous_sibling != draggable.element.previousSibling)){
                            Page.Tree.reorder(id_of(Page.Tree.target), id_of(Page.Tree.current_item));
                            if(Page.Tree.from_parent.childNodes.length == 0){
                                var parent_id = id_of(Page.Tree.from_parent.parentNode);
                                Element.removeClassName('page-'+parent_id, 'parent');
                                Element.remove($('expand_or_collapse-'+parent_id));
                                $$('#page-'+parent_id+' ul').each(function(item){Element.remove(item);});
                            }
                        }
                        Page.Tree.insert = 'left';
                        Page.Tree.target = false;
                    }
                });

                // This should handle dropping elements into nodes without children, but it doesn't do it yet
                //$$('li.page_node').each(function(item){Droppables.add(item, {accept:'page_node',hoverclass:'insert_child'});});
            }
        }
    }
}

// Written by Sean Treadway 2006 sean@treadway.info
TabControl = function(control_id, options) {
    var id = "#" + control_id;
    $$(id+' ul.tabs li a').each(function(a) {
        matched = a.getAttribute('href').match(/[-_\w]+$/i);
        if(!matched){
            return;
        }
        var page = matched[0];
        if (page != options['current']) {
            $(page).hide();
        } else {
            $(a.parentNode).addClassName('active');
            Page.activePart = page;
        }

        Event.observe(a, 'click', function(e) {
            $$(id+' ul.tabs li.active').each(function(e) { e.removeClassName('active'); });
            $$(id+' .tab_page[id!='+page+']').each(function(e) { e.hide() });
            $(a.parentNode).addClassName('active');
            $(page).show();
            Page.activePart = page;
            var index = id_of($(page));
            if($('part_'+index+'_content')){
                Field.focus($('part_'+index+'_content'));
            }
            Event.stop(e);
        });
    });
}


var OnloadPage = {
'edit_mode' : function(){

    if($('page_form')){
        Page.logFormFields();
        Event.observe($('page_form'), 'submit', Page.logFormFields, true);
        Event.observe(window, 'beforeunload', Page.warnIfModified, true);

        Event.observe(window, 'keypress', function(e){Page.handleKeyPress(e);}, true);
        Event.observe(window, 'keydown', function(e){Page.keyEvent = e;Page.handleKeyDown(e);}, true);
        Event.observe(window, 'keyup', function(e){Page.keyEvent = undefined;}, true);

        $$('.part_filter').each(function(item){
            item.lastValue = item.value;
            Event.observe(item, 'change', Page.switchFilter, true);
        });

        Page.enableBehaviour($('page_behaviour'));
        Event.observe($('page_behaviour'), 'change', Page.switchBehaviour, true);

        TabControl('tab_control', {
            current:
            (
            document.location.href.match(
            /#([-_\w]+)$/
            ) || []).last() || 'page_part-1'
        });

        $$('#page_form textarea').each(function(item){
            new ResizeableTextarea(item);
        });

        Page.showPageLink();

        Field.focus('page_title');
    }
},
'tree_listing' : function(){
    Page.Tree.enable();
},
'page_handler' : function(){
    if($('page_title')){
        Page.load();
    }
}
}

Onload.push(OnloadPage);