<?php
/*
Plugin Name: ListGallery
Description: A nice way to build galleries with a shortcode.
Version: 1.0
Author: Ivar Vong
*/

//orderby=menu_order will sort by the magical menu order.

add_shortcode('listgallery','listgallery_shortcode');
function listgallery_shortcode($atts, $content=null) {

	extract(shortcode_atts(array(
		'orderby' => 'modified',
		'order' => 'DSC',
		'post_mime_type' => 'image',
		'captionstyle' => 'title'
	), $atts) );

	global $post;
	$photos = get_children( array('post_parent' => $post->ID, 
	                              'post_status' => 'inherit', 
	                              'post_type' => 'attachment', 
	                              'post_mime_type' => $post_mime_type, 
	                              'order' => $order, 
	                              'orderby' => $orderby) );

	$galleryStr = "
	<style type=\"text/css\">	
	.listgallery-caption {
		margin: 0;
		padding: 0;
		margin-top: -6px;
	}
	.listgallery-picture {
		margin: 0;
		padding: 0;
		margin-bottom: 17px;
	}
	</style>
	";
	

	$galleryStr .= '<div class="listgallery-wrapper">'."\n";

	if ($photos) {
		foreach ($photos as $photo) {

			$galleryStr .= '<div class="listgallery-picture">'."\n";

			$galleryStr .= wp_get_attachment_image($photo->ID, 'original')."\n";

			if ($captionstyle == 'title') {
				$galleryStr .= '<div class="listgallery-caption">'.$photo->post_title."</div>\n";
			}
			if ($captionstyle == 'metadata') {
				$metadata = wp_get_attachment_metadata($photo->ID);
				$galleryStr .= '<div class="listgallery-caption">'.$metadata["image_meta"]["title"]."</div>\n";
			}
			
			$galleryStr .= "</div> <!-- close listgallery-picture -->\n";
	
		}
	}

	$galleryStr .= "</div> <!-- close listgallery-wrapper -->\n\n";

	//return "";//print_r($results);
	return $galleryStr;
}


/*
// Dmitri code
// IPTC Description 
add_filter('attachment_fields_to_edit', 'iptc_description',11,2);
function iptc_description($fields, $post){
  if($post->post_type == "attachment") {
    $image_path = $post->guid;

    $size = getimagesize ( $image_path, $info);
    if(is_array($info)) {
        $iptc = iptcparse($info["APP13"]);
        foreach (array_keys($iptc) as $s) {
            $c = count ($iptc[$s]);
            for ($i=0; $i <$c; $i++) {
	      		if($s == "2#120") {
					//echo $s.' = '.$iptc[$s][$i].'<br>';
					$fields['post_content']['value'] = $iptc[$s][$i];		
	      		}
            }
        }
    }
  }    
  return $fields;
}
 
// IPTC Credit Line 
add_filter('attachment_fields_to_edit', 'iptc_credit_line',11,2);
function iptc_credit_line($fields, $post){
 
  if($post->post_type == "attachment"){
    $image_path = $post->guid;

    $size = getimagesize ($image_path, $info);
    if(is_array($info)) {
        $iptc = iptcparse($info["APP13"]);
        foreach (array_keys($iptc) as $s) {
            $c = count ($iptc[$s]);
            for ($i=0; $i <$c; $i++) {
				if($s == "2#110"){
					//echo $s.' = '.$iptc[$s][$i].'<br>';
					$fields['post_excerpt']['value'] = $iptc[$s][$i];		
	      		}
            }
        }
    }
  }    
  return $fields;
}
*/
