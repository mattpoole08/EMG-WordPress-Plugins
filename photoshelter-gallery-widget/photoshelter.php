<?php

/************************************************************************************
 * Plugin Name: PhotoShelter Gallery Widget
 * Plugin URI: http://graphpaperpress.com/photoshelter-wordpress-integration/
 * Description: A widget for showing your PhotoShelter galleries in your sidebar
 * Version: 1.4.1
 * Author: Thad Allender
 * Author URI: http://graphpaperpress.com
 ************************************************************************************/
 
// Load the widget
add_action( 'widgets_init', 'photoshelter_gallery_load_widget' );

// Register the widget
function photoshelter_gallery_load_widget() {
	register_widget( 'WP_Widget_PhotoShelter_Gallery' );
}

// Widget class that creates the settings, input form, update and display
class WP_Widget_PhotoShelter_Gallery extends WP_Widget {
	
	// Setup the widget
	function WP_Widget_PhotoShelter_Gallery() {
		$widget_ops = array('classname' => 'photoshelter-gallery-widget', 'description' => __('Display your latest PhotoShelter galleries'));
		$control_ops = array('width' => 400, 'height' => 350);
		$this->WP_Widget( 'photoshelter-gallery-widget', __('PhotoShelter Galleries', 'photoshelter-gallery-widget'), $widget_ops, $control_ops );
	}
	
	// Display settings for printing widget to screen
	function widget( $args, $instance ) {
		extract($args);
		//
		$title = apply_filters('widget_title', $instance['title'] );
		$id = apply_filters( 'widget_text', $instance['id'] );
		$show = apply_filters( 'widget_text', $instance['show'] );
		$img = apply_filters( 'widget_text', $instance['img'] );
		$img_size = apply_filters( 'widget_text', $instance['img_size'] );
		$desc = apply_filters( 'widget_text', $instance['desc'] );
		$count = apply_filters( 'widget_text', $instance['count'] );
		
		// Before widget (defined by themes)
		echo "\n" . $before_widget . "\n";

		// Display the widget title if one was input (before and after defined by themes)
		if ( $title ) {
			echo $before_title . $title . $after_title. "\n";
			echo "<ul class='photoshelter-gallery-widget'>" . "\n";
			if (!$id) {
				echo '<p class="error">Please add your PhotoShelter label to the PhotoShelter Gallery Widget.</p>';
			} else {
				$rssUrl = "http://".$id.".photoshelter.com/gallery-list?feed=xml";
				$rssXML = @file_get_contents($rssUrl);
				if($rssXML===FALSE) {
					echo '<p class="error">Either your PhotoShelter label is incorrect or you don\'t have any public galleries.</p>';
				} else { // Houston, we have an XML file, lets do some stuff
				
					// XML to array
					$rssArr = xmltoarray($rssXML);
					// galleries Array
					$galleries = $rssArr['galleries']['gallery'];
					$gallery_count = count($galleries);
					$show = ( $gallery_count > $show ) ? $show : $gallery_count;
					for( $i=0; $i<$show; $i++){
						$PSGalleries[] = $galleries[$i];
					}
					$imgUrl = "http://c.photoshelter.com/img-get/";
					$galleryUrl = "http://pa.photoshelter.com/gallery-list/U0000eXQfvyLgvtY?feed=xml";
					foreach( $PSGalleries as $PSGallery) {
						if ( !empty($rssUrl) && !empty($PSGallery['id']) ) {
						?>
						<li>
						<h6><a href='<?php echo "http://".$id.".photoshelter.com/gallery/".$PSGallery['name']."/".$PSGallery['id'];?>'><?php echo $PSGallery['name'];?></a></h6>
							  <?php if( $img == 1 ) { ?>
							  <a href='<?php echo "http://".$id.".photoshelter.com/gallery/".$PSGallery['name']."/".$PSGallery['id'];?>'><img width='<?php echo $img_size; ?>' src='<?php echo $imgUrl.$PSGallery['key_image'];?>' border='0'></img></a>
							  <?php } ?>
							  <?php if( $desc == 1 ) { ?><p><?php echo $PSGallery['description'];?></p><?php } ?>
							  <?php if( $count == 1 ) { ?><div class="imagecount">Photos: <?php echo $PSGallery['image_count'];?></div><?php } ?>
						</li>
						<?php
						} // End if there is a Gallery ID and xml feed the user-supplied isn't empty	
					} // End Foreach
				} // Ends search for valid XML file
			}
		} // Ends if there is no $id set
		echo "</ul>" . "\n";
		// After widget (defined by themes)
		echo $after_widget . "\n";
	}
	
	// Update the widget settings when saved
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['id'] = strip_tags($new_instance['id']);
		$instance['show'] = $new_instance['show'];
		$instance['img'] = isset($new_instance['img']);
		$instance['img_size'] = strip_tags($new_instance['img_size']);
		$instance['desc'] = isset($new_instance['desc']);
		$instance['count'] = isset($new_instance['count']);
		
		return $instance;
	}

	
	// Displays the widget settings controls on the widget panel.
	// Make use of the get_field_id() and get_field_name() function
	// when creating your form elements. This handles the confusing stuff.
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'PhotoShelter label' => '','show' => '' ,
		 'img' => '' , 'img_size' => '310' , 'desc' => '' , 'count' => '' ) );
		$title = strip_tags($instance['title']);
		
		if ( ! empty( $instance['id'] ) )
		    $id = strip_tags( $instance['id'] );
		else
		    $id = null;	

		$img_size = strip_tags($instance['img_size']);
		$show = $instance['show'];
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('<strong>Title of this Widget:</strong>'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" /></p>
		<p><label for="<?php echo $this->get_field_id('id'); ?>"><?php _e('<strong>Enter your PhotoShelter label here:</strong>'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('id'); ?>" name="<?php echo $this->get_field_name('id'); ?>" type="text" value="<?php echo esc_attr($id); ?>" /></p>
		
		<p><?php _e('How many items would you like to display?.'); ?>
		<select class="widefat" name="<?php echo $this->get_field_name('show'); ?>" id="<?php echo $this->get_field_id('show'); ?>" style="display:inline;width:auto">
		<?php for($i=1;$i<=15;$i++) { ?>
		<option value="<?php echo $i;?>"<?php echo ($i == esc_attr($show) ) ? 'selected="selected"' : ''; ?> ><?php echo $i;?></option>
		<?php } ?>
		</select>
		</p>
		
		<p><input id="<?php echo $this->get_field_id('img'); ?>" name="<?php echo $this->get_field_name('img'); ?>" type="checkbox" <?php checked($instance['img']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('img'); ?>"><?php _e('Display gallery thumbnail?'); ?></label></p>
		<p><label for="<?php echo $this->get_field_id('img_size'); ?>"><?php _e('<strong>Maximum thumbnail width in pixels:</strong>'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('img_size'); ?>" name="<?php echo $this->get_field_name('img_size'); ?>" type="text" value="<?php echo esc_attr($img_size); ?>" /></p>

		<p><input id="<?php echo $this->get_field_id('desc'); ?>" name="<?php echo $this->get_field_name('desc'); ?>" type="checkbox" <?php checked($instance['desc']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('desc'); ?>"><?php _e('Display gallery description?'); ?></label></p>
		<p><input id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="checkbox" <?php checked($instance['count']); ?> />&nbsp;<label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Display gallery image count?'); ?></label></p>
<?php
	}
}

// Add some CSS
function photoshelter_gallery_widget_css() {
  
	echo "
	<style type='text/css'>
		#sidebar ul.photoshelter-gallery-widget, .photoshelter-gallery-widget {list-style:none;list-style-position:inside;margin:0 0 1em 0;padding:0;border:none}
		#sidebar ul.photoshelter-gallery-widget li, .photoshelter-gallery-widget li {display:block;margin:0;padding:0;background:none;border:none}
		#sidebar ul.photoshelter-gallery-widget li a img, .photoshelter-gallery-widget li a img {margin:0;padding:0}
		#sidebar ul.photoshelter-gallery-widget li a, .photoshelter-gallery-widget li a {background:none;border:none;padding:0}
		#sidebar ul.photoshelter-gallery-widget li a:hover, .photoshelter-gallery-widget li a:hover {background:none;}
		#sidebar ul.photoshelter-gallery-widget h6, .photoshelter-gallery-widget h6 {margin:1em 0;}
		#footer ul.photoshelter-gallery-widget h6 a {color:#999}
		#footer ul.photoshelter-gallery-widget h6 a:hover {color:#ccc}
		#sidebar ul.photoshelter-gallery-widget .imagecount, .photoshelter-gallery-widget .imagecount { text-align:right; font-style:italic; font-size:.9em; color:#ccc}
		.error {background: #FFF6BF; padding: 3px 6px;}
	</style>
	";
}

// Adds CSS to head
add_action('wp_head', 'photoshelter_gallery_widget_css');

// XML to array
function xmltoarray($xml) 
{ 
	$array  = (array)(simplexml_load_string($xml)); 
	foreach ($array as $key=>$item)
	{ 
		$array[$key]  =  structtoarray((array)$item); 
	} 
	return formatarray($array); 
} 

function formatarray($data)
{
	if(is_array($data))
	{
		foreach ($data as $key=>$value)
		{ 
			if(count($data)=='1')
			{
				if(!is_array($value))
				{    
					return $value;
				}
			}
			$array[$key] = formatarray($value);
		}
	}
	else
	{
		return $data;
	}

	if ( isset( $array ) )
		return $array;
}

function structtoarray($item) 
{ 
	if(!is_string($item)) 
	{ 
		$item = (array)$item; 
		foreach ($item as $key=>$val)
		{ 
			$item[$key]  = structtoarray($val); 
		}
	} 
	return $item;
}

?>