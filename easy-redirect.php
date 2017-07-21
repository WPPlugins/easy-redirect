<?php
/*
	Plugin Name: Easy Redirect
	Plugin URI: http://rhygel.com
	Description: A Simple plugin for redirecting at a URL after a specific time and option to add description for the same.
	Version: 2.0
	Author: Wasim Akhtar
	Author URI: http://rhygel.com
	License: GPL2
*/
add_shortcode('redirect', 'rhy_redirect');


function rhy_add_style(){
        wp_enqueue_style( 'rhy_add_css', plugins_url('css/style.css', __FILE__) );
		wp_enqueue_script('jquery');
		}

add_action('wp_enqueue_scripts', 'rhy_add_style');


function rhy_redirect($attributes, $content='') {
	//apply shortcode on content
	$content = do_shortcode($content).'<div style="text-align:center;" id="time"></div>'; 
	//fetch raw url
	
	$url_attr = $attributes['url'];
	//replace braces with shortcode
	$raw_url_replaced = str_replace("{","[",$url_attr);
	$raw_url_finalreplaced = str_replace("}","]",$raw_url_replaced);
	//do shortcode
	$raw_url = do_shortcode($raw_url_finalreplaced);
	
	ob_start();
	
	//fetch attributes
	$redirect_url = (isset($raw_url) && !empty($raw_url))?esc_url($raw_url):""; 
	$redirect_time = (isset($attributes['time']) && !empty($attributes['time']))?esc_attr($attributes['time']):"0";
	$countdown = (isset($attributes['countdown']) && !empty($attributes['countdown']))?esc_attr($attributes['countdown']):"0";
	$delaytime = (isset($attributes['delaytime']) && !empty($attributes['delaytime']))?esc_attr($attributes['delaytime']):"0";
	$popup	= (isset($attributes['popup']) && !empty($attributes['popup']))?esc_attr($attributes['popup']):"0";
	//check if countdown is enabled
	if($countdown != 0){
		$content = $content.="<script type ='text/javascript'>function startTimer(duration, display) {
			var timer = duration, minutes, seconds;
			setInterval(function () {
	       			minutes = parseInt(timer / 60, 10);
	        		seconds = parseInt(timer % 60, 10);
	
	        		minutes = minutes < 10 ? '0' + minutes : minutes;
	        		seconds = seconds < 10 ? '0' + seconds : seconds;
				if(minutes == 00) {
					display.textContent = seconds;
				} else {
				
					display.textContent = minutes + ':' + seconds;
				}
				if (--timer < 0) {
					timer = duration;
				}
			}, 1000);
		}

		window.onload = function () {
    			var timeInterval = ".$redirect_time.",
    			display = document.querySelector('#time');
    			startTimer(timeInterval, display);
		};</script>";
	}
	//implement url redirect
	if(!empty($redirect_url)){
		$return = '<meta http-equiv="refresh" content="'.$redirect_time.'; url='.$redirect_url.'">';
	}
	//show popup
	if(!empty($content)){
		if($popup != 0 )
		{
		$return .= '<div class="rhy_overlay">
					<div class="rhy_popup">
					<div class="rhy_content">'.$content.'</div>
					</div>
					</div>';
		}else{
		$return .= $content;
		}
	}
	//delay time for popup
	if($delaytime != 0)
	{
		$delaytime1 = $delaytime*1000;
    echo '<script type="text/javascript">
			jQuery( document ).ready(function( $ ) {
			
			$(document).ready(function() {
			$(".notification").delay('.$delaytime1.').fadeIn(500);
			});
			
			});
          </script>';
	echo "<div class='notification' style='display:none'>".$return."</div>";
	}else{
	echo "<div class='notification' style='display:block'>".$return."</div>";	
	}
	return ob_get_clean();
}