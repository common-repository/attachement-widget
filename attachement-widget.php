<?php /*
Plugin Name:  Attachement Widget
Plugin URI:   http://wordpress.org/extend/plugins/attachement-widget/
Description:  Cr&eacute;e un widget qui fait appara&icirc;tre les attachements li&eacute;s &agrave; un article ou une page.
Version:      0.0.1
Author:       B&eacute;atrice et Thomas Faur&eacute;
Author URI:   http://whibe.com/

Copyright (C) 2010, B&eacute;atrice et Thomas Faur&eacute;
All rights reserved.*/

if ( ! function_exists( 'attachement_widget' ) ) :
function attachement_widget($args) {
	if(is_page() or is_single()){
		extract( $args ); // extract arguments
		$options = get_option('attachement_widget'); // get options
		global $post;
		global $wp_query;
		$post_id = $wp_query->post->ID;		//$post_id=the_ID();
		if(is_page()){
		$post_id = $GLOBALS['post']->ID;}
		    $argschildren = array(
				'post_type' => 'attachment',
				'numberposts' => -1,
				//'post_mime_type' => 'application/pdf',
				'post_parent' => $post_id,
				'orderby' => 'menu_order ID'
		    );
			//echo 'variable post_id '.$post_id.'<br />';			//echo 'the ID ';			//the_ID();			
		    $parent_permalink = get_permalink( $post_id);
		    $attachments = get_children($argschildren);
		    if ($attachments) {
			
				// TEST Y a-t-il des attachments autres que des images?
				$count=0;
				foreach ($attachments as $attachment) {
					if (substr($attachment->post_mime_type, 0, 5) != 'image'){$count++;}
				}
				if ($count){
					echo $before_widget;
					echo '<div><span>'.$before_title . $options['title1'];
					if($options['title2']!=''){echo '<br />'.$options['title2'];}
					echo $after_title.'</span>';
					foreach ($attachments as $attachment) {
						if (substr($attachment->post_mime_type, 0, 5) != 'image'){
						  ?>
							<table cellspacing="2px"><tr  align="left"><td style="padding-right:5px;"><a href="<?php echo wp_get_attachment_url($attachment->ID); ?>" target="_blank"><img src="<?php echo wp_mime_type_icon($attachment->ID);?>" width=25px/></a></td>
							<td><a href="<?php echo wp_get_attachment_url($attachment->ID); ?>" target="_blank"><?php echo $attachment->post_title; ?></a></td></tr></table>
						
						   <?php  
						}
				   }
					echo '</div><div id="attachement_widget_end"></div>';
					echo $after_widget;  
				}
			}
	}
}
endif;
function attachement_widget_control() {
	$options = $newoptions = get_option('attachement_widget'); // get options
  
	// set new options
	if( $_POST['attachement-widget-submit'] ) {
		$newoptions['title1'] = strip_tags( stripslashes($_POST['attachement-widget-title1']) );		
		$newoptions['title2'] = strip_tags( stripslashes($_POST['attachement-widget-title2']) );		
	}
  
	// update options if needed
	if( $options != $newoptions ) {
		$options = $newoptions;
		update_option('attachement_widget', $options);
	}
  
	// output  
	echo '<p>'._e('Title');
        echo '<input type="text" size=20 id="attachement-widget-title1" name="attachement-widget-title1" value="'.attribute_escape($options['title1']).'" />';
	echo '</p>';  
	echo '<p>'._e('Title').' 2';
        echo '<input type="text" size=20 id="attachement-widget-title2" name="attachement-widget-title2" value="'.attribute_escape($options['title2']).'" />';
	echo '</p>';  
	echo '<input type="hidden" name="attachement-widget-submit" id="attachement-widget-submit" value="1" />';
}

// activate and deactivate plugin
function attachement_activate() {
  // options, default values
  $options = array( 
    'widget' => array( 
      'title1' => 'Files attached',
      'title2' => 'to this post'
    )
  );
  
  // register option
  add_option( 'attachement_widget', $options['widget'] );  
  // activated
  return;
}

function attachement_deactivate() {
  // unregister option
  delete_option('attachement_widget');   
  // deactivated
  return;
}

// initialization
function attachement_init() {  
  // register widget
  $class['classname'] = 'attachement_widget';
  wp_register_sidebar_widget('attachement', __('Attachement'), 'attachement_widget', $class);
  wp_register_widget_control('attachement', __('Attachement'), 'attachement_widget_control', 'width=200&height=200');
  // initialization done
  return;  
}


// actions
add_action( 'activate_'.plugin_basename(__FILE__),   'attachement_activate' );
add_action( 'deactivate_'.plugin_basename(__FILE__), 'attachement_deactivate' );
add_action( 'init', 'attachement_init');
?>