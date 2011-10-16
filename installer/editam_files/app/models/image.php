<?php

# Author Bermi Ferrer - MIT LICENSE

class Image
{
    /**
     * @param $tmp_file Array of details coming from an uploaded file
     * 
     * Allowed options are:
     * 
     * * name           Define a custom image file name
     * * output_path    Define a custom relative path to export, by default uploads to public images dir
     * * thumbnails     Defined thumbnails to create, by default an small thumbnail will be created
     * 
     * @return Uploaded file name or false
     */
    public function upload(&$tmp_file, $options = array())
    {
        $options['output_path'] = AK_PUBLIC_DIR.(empty($options['output_path']) ? $this->getImagesDir() : $options['output_path']);
        
        if (!empty($tmp_file['name']) && !empty($tmp_file['tmp_name']) && !empty($tmp_file['size']) && !empty($tmp_file['type']) && empty($tmp_file['error'])) {
            
            if (!file_exists($options['output_path'])) {
                Ak::make_dir($options['output_path']);
            }
            
            $path_info = pathinfo($options['output_path'].DS.$tmp_file['name']);

            $options['name'] = strtolower(!empty($options['name']) ? $options['name'].'.'.$path_info['extension'] : $tmp_file['name']);
            $options['output_path'] = $options['output_path'].DS.$options['name'];
            
            if (in_array(strtolower($path_info['extension']), $this->getAvailablePictureFormats())) {
                
                if ($this->ResizeAndSaveImage($tmp_file['tmp_name'], $options)){
                    
                    return $options['name'];
                }
            }
        }
        return false;
    }
    
    /**
     * @param $input_path Path to uploaded temp file
     * 
     * Needed options:
     * 
     * * output_path    Defined path to export
     * 
     * 
     * Allowed options are:
     * 
     * * thumbnails    Defined thumbnails to create, by default an small thumbnail will be created
     * 
     * @return True or False
     */
    public function ResizeAndSaveImage($input_path, $options = array())
    {
        if(!file_exists($input_path) || !is_readable($input_path)){
            trigger_error(Ak::t('Could not find or read the file located at %path', array('%path'=>$input_path)), E_USER_NOTICE);
            return false;
        }

        if (file_exists($input_path)) {
            $options['thumbnails'] = (!empty($options['thumbnails']) && is_array($options['thumbnails']) ) ? $options['thumbnails'] : (empty($options['resize']) ? $this->getThumbnailsPreferences() : array('image'=>array('size'=>$options['resize'])));

            if (count($options['thumbnails'])) {
                foreach ($options['thumbnails'] as $thumbnail_options) {
                    $Image = new AkImage($input_path);
                    $Image->transform('resize', $thumbnail_options);
                    $Image->save($options['output_path'], @$thumbnail_options['quality']);
                }
                return true;
            }
        }
        return false;
    }

    public function getImagesDir()
    {
        return DS.'images';
    }
    
    public function getThumbnailsPreferences()
    {
        return array(
            'small' => array(
                'size'=>'75x60',
                'quality'=>'79'
            )
        );
    }
    
    public function getAvailablePictureFormats()
    {
        return array(
            'gif',
            'jpg',
            'png',
            'jpeg'
        );
    }

}

