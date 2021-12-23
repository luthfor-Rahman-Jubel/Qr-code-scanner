<?php  
/*
Plugin Name: Qr Code Scaner
Plugin URI: https://qucode.jubelahmed.com
Description: Qr Code Scane from any wordpress post
Version: 1.0
Author: Mohammed Jubel Ahmed
Author URI: https://github.com/luthfor-Rahman-Jubel/Qr-code-scanner
License: GPLv2 or Later
Text Domain: qrc-plugin
Domain Path: /lang/
 */
$qrc_countries = array(
    __('Bangladesh','qrc-plugin'),
    __('Pakistan','qrc-plugin'),
    __('Nepal','qrc-plugin'),
    __('Afganistan','qrc-plugin'),
    __('Maldivs','qrc-plugin'),
    __('India','qrc-plugin'),
    __('Srilanka','qrc-plugin')
);

function qrc_filter_init(){
    global $qrc_countries;
    $qrc_countries = apply_filters('qrc-countries',$qrc_countries);
}
add_action("init",'qrc_filter_init');


function qrc_load_textdomain(){
    load_plugin_textdomain("qrc-plugint",false,dirname(__FILE__)."/languages");

}
add_action("plugins_loaded","qrc_load_textdomain");


/*
function qrc_activation_hook(){
}
register_activation_hook(__FILE__,"qrc_activation_hook");

function qrc_deactivation_hook(){
    
}
register_deactivation_hook(__FILE__,"qrc_deactivation_hook");
*/
function qrc_display_qr_code($content){
    $current_post_id    = get_the_ID();
   
    $current_post_title = get_the_title($current_post_id);
    $current_post_url   = urlencode(get_the_permalink($current_post_id));
    $current_post_type  = get_post_type($current_post_id);
    //post type check hook
    $excluded_post_types = apply_filters('qrc_post_type',array());
        if(in_array($current_post_type,$excluded_post_types) ){
            return $content;
        }
    // dimension hook
    $height = get_option('qrc_height');
    $width  = get_option('qrc_width');
    $height = $height ? $height : 180;
    $width  = $width  ? $width : 180;
    /*
      $wrheight = get_option('qr_height');
      $wrwidth  = get_option('qr_width');
      $wrheight  = $wrheight ? $wrheight : 240;
      $wrwidth  = $wrwidth ? $wrwidth : 240;
        */

    $dimension = apply_filters('qrc_code_dimension',"{$height}x{$width}");
    // image attributes
    $image_attributes = apply_filters('qrc_image_attributes',null);
    $image_src = sprintf('https//:api.qrserver.com/v1/create-qr-code/?size=%s&ecc=L&qzone=1&data=%s',$dimension,$current_post_url);
    $content .= sprintf("<div class='qrcode'></div> <img %s src='%s' alt='%s'>",$image_attributes,$image_src,$current_post_title); 

return $content;
} 
add_filter("the_content","qrc_display_qr_code");
function qrc_admin_init(){
    add_settings_section('qrc_size',__('QR Code Size','qrc-plugin'),'qrc_size_callback','general');
    add_settings_field('qrc_height',__('QR Code Height','qrc-plugin'),'qrc_display_fields','general','qrc_size',array('qrc_height') );
    add_settings_field('qrc_width',__('QR Code width','qrc-plugin'),'qrc_display_fields','general','qrc_size',array('qrc_width') );
    add_settings_field('qrc_option',__('QR Code Option','qrc-plugin'),'qrc_display_fields','general','qrc_size',array('qrc_option') );
    add_settings_field('qrc_extra',__('QR Code Extra','qrc-plugin'),'qrc_display_fields','general','qrc_size',array('qrc_extra') );
    add_settings_field('qrc_select',__('QR Code Select','qrc-plugin'),'qrc_select_fields','general','qrc_size');
    add_settings_field('qrc_Checkbox',__('QR Code Checkbox','qrc-plugin'),'qrc_Checkboxgroup_fields','general','qrc_size');
    
    register_setting('general','qrc_height',array('sanitize_callback'=>'esc_attr'));
    register_setting('general','qrc_width',array('sanitize_callback'=>'esc_attr'));
    register_setting('general','qrc_option',array('sanitize_callback'=>'esc_attr'));
    register_setting('general','qrc_extra',array('sanitize_callback'=>'esc_attr'));
    register_setting('general','qrc_select',array('sanitize_callback'=>'esc_attr'));
    register_setting('general','qrc_Checkbox');
}
    function qrc_Checkboxgroup_fields(){
        global $qrc_countries;
        $option = get_option('qrc_Checkbox');

      foreach ($qrc_countries as $country) {
         $selected = '';
          if( is_array($option) && in_array($country,$option)){
            $selected = 'Checked';
          }
          printf('<input type="checkbox" name="qrc_Checkbox[]" value="%s" %s > %s <br>', $country,$selected,$country);
      }
    }


    function qrc_select_fields(){
        global $qrc_countries;
        $countriesoption = get_option('qrc_select');
        
        printf(' <select name="%s" id="%s">','qrc_select','qrc_select');
      foreach ($qrc_countries as $country) {
         $selected = '';
          if($countriesoption==$country){
            $selected = 'Selected';
          }

          printf('<option value="%s" %s > %s</option>', $country,$selected,$country);

      }
      echo "</select>";
    }


function qrc_size_callback(){
    echo "<p>".__('QR Code posts to QR code Plugin and you can resize It','qrc-plugin')."</p>";
}

function qrc_display_fields($args){
    $option = get_option($args[0]);
    printf("<input type='text' id='%s' name='%s' value='%s' >",$args[0],$args[0],$option);
}
/*
function qrc_display_height(){
    $height = get_option('qrc_height');
    printf("<input type='text' id='%s' name='%s' value='%s' >", 'qrc_height','qrc_height',$height); 
}
function qrc_display_width(){
    $width = get_option('qrc_width');
    printf("<input type='text' id='%s' name='%s' value='%s' >", 'qrc_width','qrc_width',$width); 
}
*/

add_action("admin_init",'qrc_admin_init');


function qrc_radmin_init(){

    add_settings_section('qr_size',__('QR Code Size','qrc-plugin'),'qr_size_callback','writing');
    add_settings_field('qr_height',__('QR Plugin Hieght','qrc-plugin'),'qr_display_height','writing','qr_size');
    add_settings_field('qr_width',__('QR Plugin Width','qrc-plugin'),'qr_display_width','writing','qr_size');
    add_settings_field('qr_wseleck',__('QR Plugin Country Select','qrc-plugin'),'qr_wselect_calback','writing','qr_size');
    add_settings_field('qr_wcheckbox',__('QR Plugin Checkbox','qrc-plugin'),'qr_wrcheckbox_clback','writing','qr_size');
    register_setting('writing','qr_height',array('sanitize_callback'=>'esc_attr'));
    register_setting('writing','qr_width',array('sanitize_callback'=>'esc_attr'));
    register_setting('writing','qr_wseleck',array('sanitize_callback'=>'esc_attr'));
    register_setting('writing','qr_wcheckbox');
}

function qr_wselect_calback(){
    $option = get_option('qr_wseleck');
    $countries = array(
        'None',
        'Bangladesh',
        'Pakistan',
        'India',
        'Nepal',
        'Afganistan',
        'Maldivs',
        'Srilanka'    
    );
    printf('<select name="%s" id="%s">','qr_wseleck','qr_wseleck');
    foreach ($countries as $country) {
        $selected = '';
        if($option==$country){
            $selected = "selected";
        }
        printf(' <option vlaue="%s" %s>%s</option>',$country,$selected,$country);
    }
   echo "</select>"; 
}


function qr_wrcheckbox_clback(){
    $option = get_option('qr_wcheckbox');
    $countries = array(
        'None',
        'Bangladesh',
        'Pakistan',
        'India',
        'Nepal',
        'Afganistan',
        'Maldivs',
        'Srilanka'
    );

    foreach ($countries as $country) {
        $selected = '';
        if(is_array($option) && in_array($country,$option)){
            $selected = 'checked';
        }
        printf('<input type="checkbox" name="qr_wcheckbox[]" value="%s" %s > %s <br>',$country,$selected,$country);
    }
}


function qr_size_callback(){
    echo "<p>".__('QR Code posts size to QR code Plugin and you can do It','qrc-plugin')."</p>";
}

function qr_display_height(){
    $wrheight = get_option('qr_height');
    printf(" <input type='text' id='%s' name='%s' value='%s' >",'qr_height','qr_height',$wrheight);
}
function qr_display_width(){
    $wrwidth = get_option('qr_width');
    printf(" <input type='text' id='%s' name='%s' value='%s' >",'qr_width','qr_width',$wrwidth);
}

add_action("admin_init",'qrc_radmin_init');





?>