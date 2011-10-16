{?form_sent}
<p>Thank you for your feedback.</p>
{else}
{?form_errors}
<h2 class="error">OOOPS! THE EMAIL BELOW IS NOT VALID.</h2>
{end}

<p>Please fill in this form so we can get back to you.</p>

<form action="/{slug}" method="post">
  <div class="contact_form">
    <p>
      <label for="name">Name:</label>
      <input type="text" name="form[name]" value="{form-name?}" id="name" tabindex="1" />
    </p>
    <p>
      <label for="company">Company:</label>
      <input type="text" name="form[company]" value="{form-company?}" id="company" tabindex="2" />
    </p>
    <p>
      <label for="email">Email address:</label>
      <input type="text" name="form[email]" value="{form-email?}" id="email" tabindex="3" />
    </p>
    <p>
      <input type="submit" value="Submit" class="submit"  />
    </p>
  </div>
</form>

{end}