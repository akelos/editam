<?=$active_record_helper->error_messages_for('page');?>

<fieldset>
    <label class="required" for="page_title">
    {?is_homepage}_{Website Title}{else}_{Page Title}{end}
    </label>
    <?=$active_record_helper->input('page', 'title', array('id'=>'page_title', 'tabindex' => '1'))?>
</fieldset>

{!is_homepage}
    
    <p id="url_for_page" {!Page.id}style="display:none;"{end}>
    _{Will publish to} 
    <span id="current_page_url">
    <span id="site_url">{site_url}<? echo DS ?>{editam_public_site_url_suffix}</span><span id="parent_slug">{?ParentPage}<%= text_inherited_slug ParentPage %>{end}</span><span id="page_slug_preview" class="page_slug_preview">{!ParentPage}<?=$page->slug=='/'?'':'/'?>{end}{page.slug?}</span> </span> 
    <a href="#" class="action" id="show_page_link" style="display:none">_{(show page)}</a>
    </p>

{else}

    <p id="url_for_page" {!Page.id}style="display:none;"{end}>
    _{Will publish to} 
    <span id="site_url">{site_url}{?is_multilingual}/{Page.locale?}{end}<?= DS ?>{editam_public_site_url_suffix}</span> 
    <a href="{site_url}{?is_multilingual}/{Page.locale?}{end}<?= DS ?>{editam_public_site_url_suffix}" class="action" id="show_page_link">_{(show page)}</a>
    </p>
    
{end}


<p id="show_extra_fields"><%= link_to _('Show extra page options'), {}, 
:onclick => "return Page.moreOptions();", 
:tabindex => 2, 
:class => 'action' %></p>

<div id="extra_fields" style="display:none;">
    {?is_homepage}
        <%=hidden_field_tag 'page[slug]', '/' %>
    {else}
    <fieldset>
        <label class="required" for="page_slug">_{Slug (URL short title)}</label>
        <?=$active_record_helper->input('page', 'slug', array('tabindex' => '3', 'id'=>'page_slug',
        'onkeypress'=>'Page.frozen_slug = true;Page.frozen_slug_preview = false;setTimeout("Page.updateSlug($(\'page_slug\').value.toSlug());", 1000);',
        'onblur'=>'this.value = this.value.toSlug();',
        ))?>
    </fieldset>    
    {end}
    
    <fieldset>
        <label class="required" for="page_breadcrumb">_{Breadcrumb}</label>
        <?=$active_record_helper->input('page', 'breadcrumb', array('tabindex' => '4', 'id'=>'page_breadcrumb','onkeypress'=>'Page.frozen_breadcrumb = true;'))?>
    </fieldset>
</div>

<p id="hide_extra_fields" style="display:none;"><%= link_to _('Hide extra page options'), {}, :onclick => "return Page.lessOptions();",  :onkeypress => "return Page.lessOptions();", :class => 'action' %></p>



<div id="tab_canvas">
<? 
  $tabs = '';
  $tab_pages = '';
  
?>{loop page.parts}<? 

    $tab_pages .= '<div id="page_part-'.$part_loop_counter.'" class="tab_page">';
    $tab_pages .= '<div class="part_options">';
    
    $tab_pages .= $form_tag_helper->hidden_field_tag("part[".($part_loop_counter)."][name]", $part->get('name'), array('id'=>"part_{$part_loop_counter}_name"));
        
    ?>{?Filters}<?

        $tab_pages .= '<p>
          <label for="'."part_".($part_loop_counter)."_filter".'">'.$controller->t('Filter').'</label>'.
          $form_tag_helper->select_tag("part[".($part_loop_counter)."][filter]", 
          $form_options_helper->options_for_select($Filters, $part->get('filter')), 
          array('id'=>"part_{$part_loop_counter}_filter", 'tabindex' => '5','class'=>'part_filter')).
        '</p>';
    
    ?>{end}<?
    
    $tab_pages .= ($part_loop_counter>1?$url_helper->link_to($controller->t('Remove %page_part', array('%page_part'=>$part->get('name'))), array('controller' => 'page', 'action' => 'remove_part', 'id'=>$part->id), array('class' => 'action', 'onclick' => 'Page.removePagePart('.$part_loop_counter.');return false;')):'');
    $tab_pages .= '</div>';
    
    $tab_pages .= $form_tag_helper->text_area_tag("part[".($part_loop_counter)."][content]", 
    $tag_helper->escape_once($part->get('content')), 
    array('id'=>"part_{$part_loop_counter}_content", 'cols'=>60,'rows'=>10,'tabindex' => 5+$part_loop_counter,'class' => 'content wysiwym_'.$part_loop_counter));
    
          
    $tab_pages .= "\n</div>";
    $tabs .= '<li class="page_tab"><a id="page_part_name-'.$part_loop_counter.'" href="#page_part-'.$part_loop_counter.'">'.$part->get('name').'</a></li>'."\n";

?>{end}
    
    <div id="tab_control">
        <ul class="tabs">
            {tabs}
            <li class="add_tab" id="add_tab"><%= link_to _('Add page part'), '#', :accesskey => 'n', :class => 'action', :onclick => 'Page.addPagePart();return false;' %></li>
        </ul>
        <div id="page_parts">
            {tab_pages}
        </div>
    </div>
</div>


