<div id="header">
  <h1>_{Website Details.}</h1>
</div>

<div id="main-content">
  <h1>_{Set your site details}</h1>
  
  <?= $form_tag_helper->start_form_tag(array('controller'=>'editam_setup','action'=>'site_details')) ?>

  <p>
    <label for='site_name'>_{Your Website name}</label>
    <input type='text' name='site_details[site_name]' id='site_name' value='{site_details-site_name?}' />
  </p>
  <p>  
    <label for='administrator_login'>_{Administrator login}</label>
    <input type='text' name='site_details[administrator_login]' id='administrator_login' value='{site_details-administrator_login?}' />
  </p>
  <p>
    <label for='administrator_password'>_{Administrator password}</label>
    <input type='password' name='site_details[administrator_password]' id='administrator_password' value='{site_details-administrator_password?}' /> <br />
    <label for='administrator_password_confirmation'>_{Password confirmation}</label>
    <input type='password' name='site_details[administrator_password_confirmation]' id='administrator_password_confirmation' value='{site_details-administrator_password_confirmation?}' />
  </p> 
    
  <p>  
    <label for='administrator_email'>_{Administrator email}</label>
    <input type='text' name='site_details[administrator_email]' id='administrator_email' value='{site_details-administrator_email?}' />
  </p>
  
   <input type="submit" value="_{Complete setup}" />
  
  </form>

</div>
