<?php
/*
Plugin Name: Update Settings Tool
Plugin URI: https://github.com/tiptoppress/update-settings-tool
Description: Update settings for all installed widgets
Author: TipTopPress
Version: 0.1
Author URI: http://tiptoppress.com
*/

namespace updateSettingsTool;

class uwo_widget extends \WP_Widget {

function __construct() {
	parent::__construct(
		'uwo_widget', 'Update Settings Tool', array( 'description' => 'Update settings for all installed widgets', ) 
	);
}

public function widget( $args, $instance ) {	

	$widget_option_name = "";
	$widget_metas_name = "";
	if ($instance['widget_name'] == 'cat_posts') {
		$widget_option_name = 'widget_category-posts';
		$widget_metas_name  = 'categoryPosts-shorcode';
	} else {
		$widget_option_name = 'widget_category-posts-pro';
		$widget_metas_name  = 'categoryPostsPro-shorcode';
	}

	/* get options
	/*
	/* tbl: wp_options
	/* widget_category-posts
	*/		
	$options = get_option( $widget_option_name ); 

	if ( ! empty( $options ) ) {
		foreach( $options as $key=>$cat_options) {
			if (is_array($cat_options)) {
				$cat_options_new = array_replace( $options[$key], array('thumb_w' => $instance['width'], 'thumb_h' => $instance['height']) );
			}
		}
		// update options
		update_option( $widget_option_name, $cat_options_new );
	}

	// shortcode in pages
	$page_list = get_pages();
	foreach ($page_list as $page) {	
		$pageId = $page->ID;

		/* get metas
		/*
		/* tbl: wp_postmeta
		/* categoryPosts-shorcode
		*/
		$metas = get_post_meta( $pageId, $widget_metas_name );

		if ( ! empty( $metas ) ) {			
			foreach( $metas['0'] as $key=>$cat_options) {					
				$cat_options_new = array_replace( $cat_options, array('thumb_w' => $instance['width'], 'thumb_h' => $instance['height']) );

				if ($key == '')
					$metas[0] = array_replace( $metas[0], array( '' => $cat_options_new ));
				else
					$metas[0] = array_replace( $metas[0], array( $key => $cat_options_new ));
			}

			// update metas
			update_post_meta( $pageId, $widget_metas_name, $metas['0'] );
		}
	}
}
		
// Widget Backend 
public function form( $instance ) {
	// Widget admin form
	?>
	<p>
		<label for="<?php echo $this->get_field_id("widget_name"); ?>">
			<?php 'Widget name to update:'; ?>
		</label>
		<select id="<?php echo $this->get_field_id("widget_name"); ?>" name="<?php echo $this->get_field_name("widget_name"); ?>">
			<option value="cat_posts" <?php selected($widget_name, 'cat_posts')?>>Category Posts Widget</option>
			<option value="cat_posts_pro" <?php selected($widget_name, 'cat_posts_pro')?>>Term and Category based Posts Widget</option>
		</select>
	</p>
	
	<p>
		<label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e( 'Width:' ); ?></label> 
		<input type="number" class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo esc_attr( $width ); ?>" />
	</p>
	<p>
		<label for="<?php echo $this->get_field_id( 'height' ); ?>"><?php _e( 'Height:' ); ?></label> 
		<input type="number" class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo esc_attr( $height ); ?>" />
	</p>
	<?php 
}
	
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
	return $new_instance;
}
} // Class uwo_widget ends here

// Register and load the widget
function uwo_load_widget() {
	\register_widget( __NAMESPACE__.'\uwo_widget' );
}
add_action( 'widgets_init', __NAMESPACE__.'\uwo_load_widget' );