<div id="content_menu">
    <ul class="menu">
        <li><%= link_to _('Create new Snippet'), :action => 'add' %></li>
        <li class="active"><%= link_to _('Listing Snippet'), :action => 'listing' %></li>
    </ul>
    <p class="information">_{.}</p>
</div>

<div class="content">
<h1>_{Listing available Snippets}</h1>
<p class="information">
_{Snippets are generally small pieces of content which are included in other pages or layouts.}
</p>

  {?snippets}
  <div class="listing">
  <table cellspacing="0" summary="_{Listing available Snippets}">

  <tr>
    <th scope="col"><%= sortable_link 'description' %></th>
    <th colspan="2" scope="col"><span class="auraltext">_{Snippet actions}</span></th>
  </tr>

  {loop snippets}
    <tr {?snippet_odd_position}class="odd"{end}>
        <td class="field">
            <?= $url_helper->link_to($snippet->get('description'), array(
                'action'=>'edit', 'id'=>$snippet->getId()), 
                array('title' => $text_helper->t('Edit snippet: %snippet', array('%snippet' => $snippet->name)))) ?>
        </td>
        <td class="operation"><?= $snippet_helper->link_to_destroy($snippet)?></td>
        <td class="operation"><?= $snippet_helper->link_to_edit($snippet)?></td>
    </tr>
  {end}
   </table>
  </div>
  {end}
  
    {?snippet_pages.links}
        <div id="SnippetPagination">
        <div id="paginationHeader"><?=translate('Showing page %page of %number_of_pages',array('%page'=>$snippet_pages->getCurrentPage(),'%number_of_pages'=>$snippet_pages->pages))?></div>
        {snippet_pages.links?}
        </div>
    {end}
</div>
