<?php

# Author Bermi Ferrer - MIT LICENSE

class CorePreferences
{

    public function _getValidEditamAdminColors()
    {
        return array('#000','#333','#202','#805','#f07','#f70','#700','#830','#432','#f06040','#07f','#068','#024','#050');
    }

    public function setEditamAdminColor(&$Preference, $value)
    {
        $Preference->value = $value;
        $Preference->validatesInclusionOf('value', $this->_getValidEditamAdminColors());
        if(!$Preference->hasErrors()){
            if($value != Editam::settings_for('core','editam_admin_color')){
                $css_path = AK_PUBLIC_DIR.DS.'stylesheets'.DS.'editam.css';
                $css = AkFileSystem::file_get_contents($css_path);
                $css = preg_replace('/(background-color:\s*)(#[a-f0-9]{3,6};)(\s*\/\*\s*Editam theme switcher)/','$1'.$value.';$3',$css);
                $css = AkFileSystem::file_put_contents($css_path, $css);
            }
        }
    }

    public function setSiteLanguages(&$Preference, $value)
    {
        $Preference->value = $value;
        $Preference->validatesPresenceOf('value');
        if(!$Preference->hasErrors()){
            $new_langs = $value;
            $config_file = $new_config_file = AkFileSystem::file_get_contents(AK_CONFIG_DIR.DS.'config.php');

            if(preg_match_all('/define[\r\n\s\t]*\([\r\n\s\t]*(\'|")AK_([A-Z_]+)_LOCALES(\'|")[\r\n\s\t]*,[\r\n\s\t]*(\'|")([a-zA-Z_\- ,;:\|]+)(\'|")[\r\n\s\t]*\)[\r\n\s\t]*;/', $config_file, $matches)){
                foreach ($matches[0] as $k => $setting){
                    $new_setting = str_replace($matches[4][$k].$matches[5][$k].$matches[6][$k], $matches[4][$k].$new_langs.$matches[6][$k], $setting);
                    $new_config_file = str_replace($setting,$new_setting, $new_config_file);
                }
            }
            if($config_file != $new_config_file){
                AkFileSystem::file_put_contents(AK_CONFIG_DIR.DS.'config.php', $new_config_file);
                
                foreach (Ak::toArray($new_langs) as $lang){
                    $lang = Ak::sanitize_include($lang,'paranoid');
                    $locale_file = AK_CONFIG_DIR.DS.'locales'.DS.$lang.'.php';
                    if(!file_exists($locale_file) && !empty($base_locale_file)){
                        $base_locale = preg_replace('/(\$locale\[[\r\n\s\t]*[\'"]description[\'"][\r\n\s\t]*][\r\n\s\t]*=[\r\n\s\t]*[\'"])(.+)([\'"][\r\n\s\t]*;)/','$1'.$lang.'$3', AkFileSystem::file_get_contents($base_locale_file));
                        AkFileSystem::file_put_contents($locale_file, $base_locale);
                    }elseif (empty($base_locale_file)){
                        $base_locale_file = $locale_file;
                    }
                }
            }
        }
    }

    public function setAdministratorEmail(&$Preference, $value)
    {
        $Preference->value = $value;
        !empty($Preference->value) ? $Preference->validatesFormatOf('value', AK_EMAIL_REGULAR_EXPRESSION, Ak::t('Invalid email address', null, 'site_preference')) : null;
    }

    public function getEditamAdminColorFormView(&$Preference)
    {
        return 'core/color_selection';
    }

    public function getEditamAdminColor(&$Preference)
    {
        return empty($Preference->value) ? '#805' : $Preference->value;
    }


    public function getLogoFormView(&$Preference)
    {
        return 'core/logo_selection';
    }

    public function setLogo(&$Preference, $value)
    {
        if (empty($value['error'])) {
            $this->_removeLogo($Preference);
            if(is_array($value)){
                $Image = new Image();

                if ($image_name = $Image->upload($value, array(
                'name' => 'logo',
                'output_path' => $this->_getLogoUploadPath(),
                'resize' => '400x80'
                ))){
                    $Preference->value = $this->_getLogoUploadPath().'/'.$image_name;
                }
            }
        }elseif (is_string($value) && $value == 'delete'){
            $this->_removeLogo($Preference);
            $Preference->value = '';
        }
    }

    public function _getLogoUploadPath()
    {
        return '/images/editam';
    }


    public function _hasLogo(&$Preference)
    {
        if (!empty($Preference->value) && file_exists(AK_PUBLIC_DIR.$this->_getLogoUploadPath().DS.$Preference->value)) {
            return true;
        }
        return false;
    }

    public function _removeLogo(&$Preference)
    {
        if (!$this->_hasLogo($Preference)) {
            return true;
        }
        return (@AkFileSystem::file_delete(AK_PUBLIC_DIR.$this->_getLogoUploadPath().DS.Ak::sanitize_include($Preference->value))) ? true : false;
    }

    public function getNewUserRolesFormView(&$Preference)
    {
        return 'core/role_selection';
    }

    public function setNewUserRoles(&$Preference, $values)
    {
        $Preference->value = '';

        if (is_array($values) && $values = @array_keys(@array_diff($values, array(0)))) {
            $Preference->value = @join(',', $values);
        }
    }


    public function getEditamVersionFormView(&$Preference)
    {
        return 'show';
    }

    // Theme
    public function getThemeFormView(&$Preference)
    {
        return 'core/theme_selection';
    }

    public function getTimeZoneFormView(&$Preferences)
    {
        return 'core/time_zone';
    }
}
