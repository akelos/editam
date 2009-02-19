<div id="tasks" class="tasks">
  <ul>
    <li><%= link_to _('Create new Layout'), :action => 'add' %></li>
  </ul> 
</div>
    
<h2>_{Listing available Layouts}</h2>
<p class="information">
_{Use layouts to apply a visual look to a Web page. Layouts can contain special tags to include page content and other elements such as the header or footer.}
</p>

  {?content_layouts}
  <div class="listing">
  <table cellspacing="0" summary="_{Listing available Layout}">
      <thead>
        <tr>
          <th scope="col"><%= sortable_link 'description' %></th>
          <th colspan="2" scope="col"><span class="auraltext">_{Layout actions}</span></th>
        </tr>
      </thead>
      <tbody>
      {loop content_layouts}
        <tr {?content_layout_odd_position}class="odd"{end}>
          <td class="field"><?= $url_helper->link_to($content_layout->get('name'), array(
          'action'=>'edit', 'id'=>$content_layout->getId())) ?></td>
          <td class="operation"><?= $content_layout_helper->link_to_destroy($content_layout)?></td>
          <td class="operation"><?= $content_layout_helper->link_to_edit($content_layout)?></td>
        </tr>
      {end}
      </tbody>
    </table>
  </div>
  {end}
  
    {?content_layout_pages.links}
        <div id="LayoutPagination">
        <div id="paginationHeader"><?=translate('Showing page %page of %number_of_pages',array('%page'=>$content_layout_pages->getCurrentPage(),'%number_of_pages'=>$content_layout_pages->pages))?></div>
        {content_layout_pages.links?}
        </div>
    {end}

