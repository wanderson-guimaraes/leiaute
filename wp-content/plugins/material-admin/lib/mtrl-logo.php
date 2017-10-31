<?php
/**
 * @Package: WordPress Plugin
 * @Subpackage: Material - White Label WordPress Admin Theme Theme
 * @Since: Mtrl 1.0
 * @WordPress Version: 4.0 or above
 * This file is part of Material - White Label WordPress Admin Theme Theme Plugin.
 */
?>
<?php

function mtrl_logo($rettype = ""){
    global $mtrladmin;
       $mtrladmin = mtrladmin_network($mtrladmin);       

    $csstype = mtrl_dynamic_css_type();


    $str = "";
    if(isset($mtrladmin['enable-logo']) && $mtrladmin['enable-logo'] != "1" && $mtrladmin['enable-logo'] == "0" && !$mtrladmin['enable-logo']){

        // hide logo
        if($rettype != "1"){$str .= "<style type='text/css' data-display='hide' id='mtrl-admin-logo-hide'>";}
        $str .= "#adminmenuwrap .logo-overlay{display:none !important;} #adminmenuwrap:before, .folded #adminmenuwrap:before{display: none !important;} .auto-fold #adminmenuwrap:before{display: none !important;}  #adminmenu{margin-top:0px !important;}"; 
        if($rettype != "1"){$str .= "</style>";}

    } else {

        // show logo
        $logo = $logo_folded = "";

        //echo $csstype;
        if($csstype != "custom"){
                global $mtrl_color;

                $logo = str_replace("PLUGINURL",plugins_url('/', __FILE__).'..',$mtrl_color[$csstype]['logo']['url']);
                $logo_folded = str_replace("PLUGINURL",plugins_url('/', __FILE__).'..',$mtrl_color[$csstype]['logo_folded']['url']);
        }

        if($logo == ""){if(isset($mtrladmin['logo']['url'])){ $logo = trim($mtrladmin['logo']['url']); }}
        if($logo_folded == ""){if(isset($mtrladmin['logo_folded']['url'])){ $logo_folded = trim($mtrladmin['logo_folded']['url']); }}
        
        if($rettype != "1"){$str .= "<style type='text/css' data-display='show' data-csstype='".$csstype."' id='mtrl-admin-logo-show'>";}
        $str .= "#adminmenuwrap:before{background-image: url('".$logo."');} 
        .folded #adminmenuwrap:before{background-image: url('".$logo_folded."');} 
        .auto-fold #adminmenuwrap:before{background-image: url('".$logo_folded."');}"; 
        $str .= "@media only screen and (min-width: 960px){ .auto-fold #adminmenuwrap:before{background-image: url('".$logo."') !important;}} ";
        $str .= ".folded #adminmenuwrap:before , .menu-collapsed #adminmenuwrap:before{background-image: url('".$logo_folded."') !important;}";
        $str .= "@media only screen and (max-width: 782px){ body.menu-expanded #adminmenuwrap .logo-overlay, #adminmenuwrap:before, body.menu-expanded #adminmenuwrap:before{background-image: url('".$logo."') !important;}} ";


        if($rettype != "1"){$str .= "</style>";}
    }

    if($rettype != "1"){ echo $str;} else { return $str; }
}


function mtrl_favicon(){
?>

<?php global $mtrladmin; 

       $mtrladmin = mtrladmin_network($mtrladmin);       

?>

<?php if ($mtrladmin['favicon']['url']): ?>
    <link rel="shortcut icon" href="<?php echo $mtrladmin['favicon']['url']; ?>" type="image/x-icon" />
<?php endif; ?>

<?php if ($mtrladmin['iphone_icon']['url']): ?>
    <!-- For iPhone -->
    <link rel="apple-touch-icon-precomposed" href="<?php echo $mtrladmin['iphone_icon']['url']; ?>">
<?php endif; ?>

<?php if ($mtrladmin['iphone_icon_retina']['url']): ?>
    <!-- For iPhone 4 Retina display -->
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $mtrladmin['iphone_icon_retina']['url']; ?>">
<?php endif; ?>

<?php if ($mtrladmin['ipad_icon']['url']): ?>
    <!-- For iPad -->
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $mtrladmin['ipad_icon']['url']; ?>">
<?php endif; ?>

<?php if ($mtrladmin['ipad_icon_retina']['url']): ?>
    <!-- For iPad Retina display -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $mtrladmin['ipad_icon_retina']['url']; ?>">
<?php endif; ?>
<?php
}


function mtrl_logo_url(){

    global $mtrladmin;
       $mtrladmin = mtrladmin_network($mtrladmin);       

    $logourl = "";
    if(isset($mtrladmin['logo-url']) && trim($mtrladmin['logo-url']) != ""){
        $logourl = $mtrladmin['logo-url'];
        echo "<style type='text/css' id='mtrl-logo-url'> #adminmenuwrap .logo-overlay { cursor:hand;cursor:pointer; }</style>";
    }

    echo "<meta type='info' id='mtrl-logourl' data-value='".$logourl."'>";
}
?>