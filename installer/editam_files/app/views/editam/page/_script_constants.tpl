<? $capture_helper->begin ('script'); ?>

var PAGE_SWITCHING_FILTER = '_{Switching filter}';
var PAGE_CANT_APPLY_FILTER = '_{Could not apply filter}';
var PAGE_SWITCHING_BEHAVIOR = '_{Switching Behavior}';
var PAGE_CANT_CHANGE_BEHAVIOR = '_{Could not change behavior}';
var PAGE_NEW_PART_PROMPT = '_{Please insert the new part name}';
var PAGE_PART_ADD_ERROR = '_{Could not create a part named}';
var PAGE_PART_BECAUSE = '_{because}';
var PAGE_PART_RETRY = '_{Would ou like to try again?}';
var PAGE_PART_DUPLICATED = '_{you already have a part named}';
var PAGE_PART_ADDED = '_{Added page part}';
var PAGE_SAVE_AND_CONTINUE = '_{Save and continue editing}';
var PAGE_SAVE_AND_ADD_CHILD = '_{Save and add child}';
var PAGE_PART_DELETE_CONFIRM = '_{Are you sure you want to delete the page part named}';
var PAGE_PART_FILTER_CAPTION = '_{Filter}';
var PAGE_PART_REMOVE = '_{Remove}';
var PAGE_IS_FIRST = {?is_homepage}true{else}false{end};
var PAGE_UNSAVED_CHANGES_WARNING = '_{Your page has modifications and has not being saved}';
var PAGE_LIST_CHILDREN_URL = '<%= url_for :controller => 'page', :action => 'list_children' %>';
var PAGE_MOVE_NODE = '<%= url_for :controller => 'page', :action => 'move' %>';
var PAGE_CONVERT_CONTENT = '<%= url_for :controller => 'page', :action => 'convert_content' %>';
var PAGE_SWITCH_BEHAVIOR = '<%= url_for :controller => 'page', :action => 'switch_behavior', :id => Page.id %>';
var PAGE_IS_VIRTUAL = {?Page.is_virtual}true{else}false{end};
var USER_ID = {CurrentUser.id};
var LANG = '{lang}';
var BASE_URL = '{site_url}';

<? $capture_helper->end (); ?>