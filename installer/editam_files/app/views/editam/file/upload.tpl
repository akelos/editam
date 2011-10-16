<script type="text/javascript">

    //parent.$('uploaded_files').innerHTML += '<?=$javascript_helper->escape_javascript(print_r($file->contents['name'][0], true))?>';

    if (parent.$('uploaded_files').innerHTML == ''){
        parent.$('uploaded_files').innerHTML ='Selected files:';
        
        uploaded_files_list = document.createElement("ul");
        uploaded_files_list.id = 'uploaded_files_list';
        parent.$('uploaded_files').appendChild(uploaded_files_list);
    }
    
    file_tmp_name = '<?=$javascript_helper->escape_javascript(print_r($file->contents['tmp_name'][0], true))?>';
    file_size = '<?=$javascript_helper->escape_javascript(print_r(round(($file->contents['size'][0])/1024,2), true))?>';
    file_type = '<?=$javascript_helper->escape_javascript(print_r($file->contents['type'][0], true))?>';
    file_name =     '<?=$javascript_helper->escape_javascript(print_r($file->contents['name'][0], true))?>';
    
    file_error = '<?=$javascript_helper->escape_javascript(print_r($file->contents['error'][0], true))?>';
    file_error = (file_error=='') ? 1:0;    
    file_error = (file_size<=0) ? 1:0;    
    
    if(!file_error){
        filesLI = document.createElement("li");    
        filesLI.id = file_name;
        filesLI.innerHTML = '<input type="checkbox" name="tmp_files[<?=$javascript_helper->escape_javascript(print_r($file->uploaded_file_id));?>]" checked value="'+file_name+'" /> '+file_name+ ' ('+file_type+') ('+file_size+' Kb) <span class="remove" onclick="remove_uploaded_file(\''+file_name+'\',\''+file_tmp_name+'\')">remove</span>';
        
        parent.$('uploaded_files_list').appendChild(filesLI);
        //parent.$('file_form').innerHTML += '<input type="hidden" value="<?=$javascript_helper->escape_javascript(print_r($file->contents['type'][0], true))?>" id="<?=$javascript_helper->escape_javascript(print_r($file->contents['tmp_name'][0], true))?>" name="<?=$javascript_helper->escape_javascript(print_r($file->contents['tmp_name'][0], true))?>" />';
    }
    else{
        setTimeout("alert('Invalid file');",500);
    }
    
    
    //parent.$('uploaded_files').innerHTML += '<div class="file_name"><?=//$javascript_helper->escape_javascript(print_r($file->contents['name'][0], true))?></div>';
</script>

