<? $capture_helper->content_for('head_after_scripts'); ?>
    {content_for_head_after_scripts?}

    <script type="text/javascript">
    // <![CDATA[
    var EditamLogo = {

        remove: function(){
            $('site_preference_files_logo').value = 'delete';
            $('current_logo').hide();
            $('upload_new_logo').hide();
            $('logo_deletion_warning').show();
        },
        cancelRemove: function(){
            $('logo_deletion_warning').hide();
            $('upload_new_logo').show();
            $('current_logo').show();
            $('site_preference_files_logo').value = '';
        },

        selectImage: function(){
            $('upload_new_logo').hide();
            $('site_preference_files_logo').setAttribute('type', 'file');
            $('site_preference_logo_container').show();
            $('current_logo').hide();
        },

        cancelUpload: function(){
            $('site_preference_logo_container').hide();
            $('upload_new_logo').show();
            $('site_preference_files_logo').setAttribute('type', 'hidden');
            $('site_preference_files_logo').value = '';
            $('current_logo').show();
        }

    }
    // ]]>
    </script>
<? $capture_helper->end(); ?>

<label for="site_preference_files_logo">_{Editam admin logo}</label><br />
    <div id="current_logo" class="current_logo">
    {?Preference.value}
        <%= image_tag Preference.value, :class => 'logo', :id => 'logo_preview' %>
        <%= image_tag 'trash.gif' %>
        <a href="#" class="action" onclick="EditamLogo.remove();">  
            _{Delete logo}
        </a>
{end}
</div>
    
<p style="display: none;" class="information" id="logo_deletion_warning">
_{Logo will be deleted}         
<a href="#" class="action" onclick="EditamLogo.cancelRemove();">
        _{Cancel removing current logo.}
    </a>
</p>
    
<div id="upload_new_logo">
    <%= image_tag 'plus' %>
    <a href="#" class="action" onclick="EditamLogo.selectImage()">
        {?Preference.value}
            _{Select another image for the Editam admin interface}
        {else}
            _{Upload an image for customizing your Editam administration interface}
        {end}
        
    </a>
</div>
<div style="display: none;" id="site_preference_logo_container">
<label for="site_preference_files_logo">_{Upload an image for customizing your Editam administration interface}</label><br />
    <input type="hidden" name="preferences[{Preference.id}]" id="site_preference_files_logo" />
    <p class="information">
        _{The image will be resized in order to fit into the layout.}         
        <a href="#" class="action" onclick="EditamLogo.cancelUpload();">
        _{Cancel logo uploading.}
    </a>
    </p>
</div>
