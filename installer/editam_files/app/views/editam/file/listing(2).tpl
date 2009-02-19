<div id="sidebar">
  <h1>_{Tasks}:</h1>
  <ul>
    <li><?= $url_helper->link_to($text_helper->translate('Create new File'), array('action' => 'add'))?></li>
  </ul> 
</div>

<div id="content">
  <h1>_{Files}</h1>

  {?files}
  <div class="listing">
  <table cellspacing="0" summary="_{Listing available Files}">

  <tr>
    <? $content_columns = array_keys($File->getContentColumns()); ?>
    {loop content_columns?}
        <th scope="col"><?= $pagination_helper->sortable_link($content_column) ?></th>
    {end}
    <th colspan="3" scope="col"><span class="auraltext">_{Item actions}</span></th>
  </tr>

  {loop files?}
    <tr {?file_odd_position}class="odd"{end}>
    {loop content_columns?}
      <td class="field"><?= $file->get($content_column) ?></td>
    {end}
      <td class="operation"><?= $file_helper->link_to_show($file)?></td>
      <td class="operation"><?= $file_helper->link_to_edit($file)?></td>
      <td class="operation"><?= $file_helper->link_to_destroy($file)?></td>    
    </tr>
  {end}
   </table>
  </div>
  {end}
  
    {?file_pages.links}
        <div id="FilePagination">
        <div id="paginationHeader"><?=translate('Showing page %page of %number_of_pages',array('%page'=>$file_pages->getCurrentPage(),'%number_of_pages'=>$file_pages->pages))?></div>
        {file_pages.links?}
        </div>
    {end}
  
</div>