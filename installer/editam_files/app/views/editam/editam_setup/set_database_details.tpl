        <div id="header">
          <h1>_{Database Configuration.}</h1>
        </div>
                
        <div id="main-content">
          <h1>_{Please set your database details}</h1>
        
          <?= $form_tag_helper->start_form_tag(array('controller'=>'editam_setup','action'=>'set_database_details')) ?>

          <? foreach ($environment_modes as $mode) : ?>
          
              <fieldset>
              <legend>_{Database Connection Details}</legend>
              
              <? if($EditamSetup->database_type != 'sqlite') : ?>
       
              <label for='{mode}_database_host'>_{Database Host}</label>
                        <input type='text' name='{mode}_database_host' id='{mode}_database_host' 
                        value='<?=$EditamSetup->getDatabaseHost($mode)?>' />
                        
                    <label for='{mode}_database_name'>_{Database name}</label>
                        <input type='text' name='{mode}_database_name' id='{mode}_database_name' 
                        value='<?=$EditamSetup->getDatabaseName($mode)?>' />
                        
                    <label for='{mode}_database_user'>_{User}</label>
                        <input type='text' name='{mode}_database_user' id='{mode}_database_user' 
                        value='<?=$EditamSetup->getDatabaseUser($mode)?>' />
                        
                    <label for='{mode}_database_password'>_{Password}</label>
                        <input type='password' name='{mode}_database_password' id='{mode}_database_password' 
                        value='<?=$EditamSetup->getDatabasePassword($mode)?>' />
                        
            <? else : ?>
           
              <label for='{mode}_database_name'>_{Database name}</label>
              <b>config/.ht-</b><input class="sqlite_database_name" type='text' 
                        name='{mode}_database_name' id='{mode}_database_name' 
                        value='<?=$EditamSetup->getDatabaseName($mode)?>' /><b>.sqlite</b>
            
            <? endif; ?>
                
            </fieldset>           
            <br />
            <br />
                
        <? endforeach; ?>
        
        
        <?php
        /**
         * @todo Database creation form. Requires extensive testing before 
         * making it into the setup process
         */
        if(false && $EditamSetup->database_type != 'sqlite') : ?>
        
        <fieldset>
            <legend>_{(optional) Try to create databases using the following privileged account:}</legend>
                <label for='admin_database_user'>_{DB admin user name}</label>
                    <input type='text' name='admin_database_user' id='admin_database_user' 
                    value='<?=$EditamSetup->getDatabaseAdminUser()?>' />
                    
                <label for='admin_database_password'>_{DB admin password}</label>
                    <input type='password' name='admin_database_password' id='admin_database_password' 
                    value='<?=$EditamSetup->getDatabaseAdminPassword()?>' />
        </fieldset>
        <br />
        <br />
        
        <? endif; ?>
                
                <input type="submit" value="_{Continue}" />

            </form>
            
        </div>
