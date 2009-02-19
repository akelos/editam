<div id="header">
	<h1>_{Language settings.}</h1>
</div>

<div id="main-content">
	<h1>_{Please set your language details}</h1>
	
	<?= $form_tag_helper->start_form_tag(array('controller'=>'editam_setup','action'=>'set_locales')) ?>
		<? $available_iso_locales = $EditamSetup->getAvailableLocales(); ?>
	
		<label for='locales'>_{2 letter ISO 639 language codes (separated by commas)}</label>
		
		<input type='text' name='locales' id='locales' value='{locales?}' />
		
		<br />
		<br />
		{?available_iso_locales}
			<label for='locales'>_{Editam currently supports the following ISO codes:}</label>
			<ul id="supported_iso_codes">
				{loop available_iso_locales}
					<li>{available_iso_locale} (<?= $text_helper->locale('description', $available_iso_locale) ?>)</li>
				{end}
			</ul>
		{end}
		
		
		<input type="submit" value="_{Continue}" />
	
	</form>

</div>
