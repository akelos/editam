<? $capture_helper->content_for('head_after_scripts'); ?>
    {content_for_head_after_scripts?}

    <script type="text/javascript">
    // <![CDATA[
    var ThemeColors = {

        switchColor: function(color_item){
            ThemeColors.UnselectThemes();
            $('theme_color').value =  color_item.innerHTML;
            Element.addClassName(color_item, 'selected');
            document.body.style.backgroundColor= color_item.innerHTML;
        },

        UnselectThemes: function(){
            $$('ul.color_selection li').each(function(node){
                Element.removeClassName(node, 'selected');
            });
        }

    }
    // ]]>
    </script>
    <style type="text/css" media="screen">
         ul.color_selection li{
         float:left;
         height:20px;
         width:20px;
         margin:4px;
         border:2px solid #ccc;
         overflow:hidden;
         text-indent:30px;
         cursor:pointer;
        }
        
        ul.color_selection li.selected{
         border: 2px solid #f00;
        }
    </style>
<? $capture_helper->end(); ?>

<input id="theme_color" value="{Preference.value}" type="hidden" name="preferences[{Preference.id}]"/>
<?php 

$colors = empty($colors) ? array('#000','#333','#202','#805','#f07','#f70','#700','#830','#432','#f06040','#07f','#068','#024','#050') : $colors;

?>
<label for="theme_color">_{Select a color for the administration interface}</label><br />
<ul class="color_selection">
{loop colors}
    <?php $selected = $color == @$Preference->value; ?>
    <li style="background-color:{color}" onclick="ThemeColors.switchColor(this)" {?selected}class="selected"{end}>{color}</li>
{end}
</ul>

<div class="cls"></div>