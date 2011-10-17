var EditamInPlaceEditor =  Class.create({
    part_ids: {},
    initialize: function(){
        this._editable_parts = $$('.editable.hoverable');
        this._edit_buttons = $$('.edit-button');
        this.insertEditorHeader();
        this.observerParts();
    },

    observerParts: function(){
        this._editable_parts.extractIdentities('editable-part');
        this._edit_buttons.extractIdentities('edit-part');
        this._edit_buttons.each(function(item){
            item.observe('click', this.openEditor.bind(this));
        }.bind(this));
    },

    insertEditorHeader: function(){
        $(document.body).insert({top:$('editam-editor-header-template').innerHTML});
        $('editam-editor-enable').observe('click', this.enableEditor.bind(this));
        $('editam-editor-disable').observe('click', this.disableEditor.bind(this));
        this.disableEditor();
    },

    enableEditor: function(ev){
        $('editam-editor-enable').hide();
        $('editam-editor-disable').show();
        this._editable_parts.each(function(item){
            item.addClassName('editable');
        });
        ev.stop();
    },

    disableEditor: function(ev){
        $('editam-editor-disable').hide();
        $('editam-editor-enable').show();
        this._editable_parts.each(function(item){
            item.removeClassName('editable');
        });
        try{ev.stop();} catch(e){}
    },

    openEditor: function(ev){
        return;
        var item = ev.target;
        this.focusOnPart(item.identity);
        $('editable-part-'+item.identity).contentEditable = true;
        $$('.editam-part-menu').invoke('hide');
        ev.stop();
    },

    hideEditor: function(ev){
        $$('.editam-part-menu').invoke('show');
        ev.stop();
    },

    focusOnPart: function(part_id){

        $$('.editable').each(function(item){
            item.addClassName('disabled');
        });
        $$('#editable-part-'+part_id+',#editable-part-'+part_id+' *').each(function(item){
            item.removeClassName('disabled');
        });
    }

});

var b;
var OnloadEditamInPlaceEditor = {
    enable_editor: function(){
        b= new EditamInPlaceEditor();
    }
}

OnloadFull.push(OnloadEditamInPlaceEditor);
