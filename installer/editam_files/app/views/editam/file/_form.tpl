<?php  echo $active_record_helper->error_messages_for('file');?>

    <p>
        <label for="file_mime_type">_{Mime type}</label><br />
        <?php  echo $active_record_helper->input('file', 'mime_type')?>
    </p>

    <p>
        <label for="file_name">_{Name}</label><br />
        <?php  echo $active_record_helper->input('file', 'name')?>
    </p>

    <p>
        <label for="file_data">_{Data}</label><br />
        <input type="file" id="file_data" name="file[data][]" />   
    </p>

    <p>
        <label for="file_size">_{Size}</label><br />
        <?php  echo $active_record_helper->input('file', 'size')?>
    </p>

    <p>
        <label for="file_is_public">_{Is public}</label><br />
        <?php  echo $active_record_helper->input('file', 'is_public')?>
    </p>

    <p>
        <label for="file_locale">_{Locale}</label><br />
        <?php  echo $active_record_helper->input('file', 'locale')?>
    </p>
    