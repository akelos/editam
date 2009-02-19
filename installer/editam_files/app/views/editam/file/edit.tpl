<div id="sidebar">
  <h1>_{Tasks}:</h1>
  <ul>
    <li><?php  echo  $url_helper->link_to($text_helper->translate('Back to overview'), array('action' => 'listing'))?></li>
    <li><?php  echo  $url_helper->link_to($text_helper->translate('Show this File'), array('action' => 'show', 'id'=>$file->getId()))?></li>
  </ul> 
</div>

<div id="content">
  <h1>_{Files}</h1>

  <?php  echo  $form_tag_helper->start_form_tag(array('action'=>'edit', 'id' => $file->getId())) ?>

  <div class="form">
    <h2>_{Editing File}</h2>
    <?php  echo   $controller->renderPartial('form') ?>
  </div>

  <div id="operations">
    <?php  echo $file_helper->save() ?> <?php  echo  $file_helper->cancel()?>
  </div>

  <?php  echo  $form_tag_helper->end_form_tag() ?>
</div>
