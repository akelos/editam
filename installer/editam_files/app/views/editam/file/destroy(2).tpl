<div id="sidebar">
  <h1>_{Tasks}:</h1>
  <ul>
    <li><?= $url_helper->link_to($text_helper->translate('Back to overview'), array('action' => 'listing'))?></li>
    <li><?= $url_helper->link_to($text_helper->translate('Show this File'), array('action' => 'show', 'id'=>$file->getId()))?></li>
  </ul> 
</div>


<div id="content">
  <h1>_{Files}</h1>

  <p>_{Are you sure you want to delete this File?}</p>
  <?= $form_tag_helper->start_form_tag(array('action' => 'destroy', 'id' => $file->getId())) ?>
  <?= $file_helper->confirm_delete() ?>
  <?= $form_tag_helper->end_form_tag() ?>
</div>
