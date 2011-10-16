        <div id="header">
          <h1>_{File handling settings.}</h1>
        </div>
        
        <p>_{Editam makes an extensive use of the file system for handling locales, cache, compiled templates...}</p>
        <p>_{The installer could not create a test file at <b>config/test_file.txt</b>, so you should check if the user that is running the web server has enough privileges to write files inside the installation directory.}</p>
        
        <p><?=$text_helper->translate('If you have made changes to the filesystem or web server, <a href="%ftp_url">click here to continue</a> or 
<a href="%url_skip">here to skip the filesystem setting</a></p>',
array(
    '%ftp_url'=>$url_helper->url_for(array('controller'=>'editam_setup','action'=>'configure_ftp_details','check'=>true)),
    '%url_skip'=>$url_helper->url_for(array('controller'=>'editam_setup','action'=>'configure_ftp_details','skip'=>true))
)); ?>



        <? if($EditamSetup->canUseFtpFileHandling()) : ?>
        
        <p>_{If you can't change the web server or file system permissions Editam has an alternate way to access the file system by using an FTP account that points to your application path.}</p>
        
        <div id="main-content">
          <h1>_{Please set your ftp connection details}</h1>
        
          <?= $form_tag_helper->start_form_tag(array('controller'=>'editam_setup','action'=>'configure_ftp_details')) ?>
   
          <label for='ftp_host'>_{FTP Host}</label>
                    <input type='text' name='ftp_host' id='ftp_host' value='{EditamSetup.ftp_host?}' />
                    
                <label for='ftp_path'>_{Application path from FTP initial path}</label>
                    <input type='text' name='ftp_path' id='ftp_path' value='{EditamSetup.ftp_path?}' />
                    
                <label for='ftp_user'>_{User}</label>
                    <input type='text' name='ftp_user' id='ftp_user' value='{EditamSetup.ftp_user?}' />
                    
                <label for='ftp_password'>_{Password}</label>
                    <input type='password' name='ftp_password' id='ftp_password' value='{EditamSetup.ftp_password?}' />
                    
                        
                <br />
                <br />
                
                <input type="submit" value="_{Continue}" />

            </form>
            
        </div>
        
        <? else : ?>
        
        <div class="important">
            <p>_{You don't have enabled FTP support into your PHP settings. When enabled 
            you can perform file handling functions using specified FTP account. 
            In order to use FTP functions with your PHP configuration, you should add the 
            --enable-ftp option when installing PHP.}
            </p>
        </div>
        
        <? endif; ?>
