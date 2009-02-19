<div id="sidebar">
  <h1>_{Tasks}:</h1>
  <ul>
    <li><?= $url_helper->link_to($text_helper->translate('Edit this File'), array('action' => 'edit', 'id'=>$file->getId()))?></li>
    <li><?= $url_helper->link_to($text_helper->translate('Back to overview'), array('action' => 'listing'))?></li>
  </ul> 
</div>

<div id="content">
  <h1>_{Files}</h1>

  <div class="show">
    <? $content_columns = array_keys($File->getContentColumns()); ?>
    {loop content_columns}
      <label><?= $text_helper->translate($text_helper->humanize($content_column))?>:</label> <span class="static"><?= $file->get($content_column) ?></span><br />
    {end}
  </div>
</div>