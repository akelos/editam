<div id="sidebar">
  <h1>_{Tasks}:</h1>
  <ul>
    <li><?= $url_helper->link_to($text_helper->translate('Back to overview'), array('action' => 'listing'))?></li>
    <li><?= $url_helper->link_to($text_helper->translate('Show this File'), array('action' => 'show', 'id'=>$file->getId()))?></li>
  </ul> 
</div>

<div id="content">
  <h1>_{Files}</h1>

  <?= $form_tag_helper->start_form_tag(array('action'=>'edit', 'id' => $file->getId())) ?>

  <div class="form">
    <h2>_{Editing File}</h2>
    <?=  $controller->renderPartial('form') ?>
  </div>

  <div id="operations">
    <?=$file_helper->save() ?> <?= $file_helper->cancel()?>
  </div>

  <?= $form_tag_helper->end_form_tag() ?>
</div>
