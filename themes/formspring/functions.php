<?php
//$theme['dir']
//$theme['url']
//$theme['name']

wp_enqueue_style("misamee-themed-form-$themeName", "{$themeData->themeUrl}css/misamee.themed.form.$themeName.css");
wp_enqueue_script('tooltipsy', "{$themeData->themeUrl}js/tooltipsy.min.js", array('jquery'), false, true);
wp_enqueue_script("misamee-themed-form-$themeName-js", "{$themeData->themeUrl}js/misamee.themed.form.$themeName.js", array('jquery'), false, true);
