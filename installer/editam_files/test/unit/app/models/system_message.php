<?php

class SystemMessageTestCase extends  EditamUnitTest
{
    public function test_system_messages()
    {
        $Message = new SystemMessage(); 
        $Message->setAttributes(array(
        'value' => 'Security warning. You are using the default admin password.',
                    //Please <a href="/admin/user/edit/1/" class="action">change your password now.</a>',
        'message_key' => 'admin_default_password',
        'user_id' => 1
        ));
        $this->assertTrue($Message->save());
    }
}
