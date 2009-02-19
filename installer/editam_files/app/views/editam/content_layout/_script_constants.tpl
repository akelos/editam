<? $capture_helper->begin ('script'); ?>

var LAYOUT_UNSAVED_CHANGES_WARNING = '_{Your Layout has modifications and has not being saved}';

var USER_ID = <?=$credentials->get('id')?>;
var LANG = '{lang}';
var BASE_URL = '{site_url}';

<? $capture_helper->end (); ?>