<? $index = !empty($params['index']) ? $params['index'] : (!empty($index) ? $index : 1); ?>

<div class="page" id="page-{index}>">
  <div class="part" id="part-{index}">
    <?= $form_tag_helper->hidden_field_tag("part[".($index - 1)."][name]", $part->get('name')) ?>

    {?Filters}
        <p>
          <label for="part[<?= $index - 1; ?>][filter_id]">_{Filter}</label>
          <?= $form_tag_helper->select_tag("part[".($index - 1)."][filter]", 
          $form_options_helper->options_for_select(array_merge(array($controller->t('------')=>''), $Filters))) ?>
        </p>
    {end}
    
    <div><?= $form_tag_helper->text_area_tag("part[".($index - 1)."][content]", $part->content, array('cols'=>120,'rows'=>40)) ?></div>
  </div>
</div>