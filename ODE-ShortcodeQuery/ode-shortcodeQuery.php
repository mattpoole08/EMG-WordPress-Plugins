<?php
/*
Plugin Name: ODE - Shortcode queries
Plugin URI: none
Description: Generates teases from shortcode queries. Enables shortcodes in widgets.
Author: Ivar Vong
Version: 0.2
Author URI: http://ivarvong.com
License: CLOSED-SOURCE. COPYRIGHT IVAR VONG/OREGON DAILY EMERALD.
*/

function iv_timeago() {
	echo '<div class="timeago-wrapper"><time class="timeago" datetime="'.get_the_modified_time('c').'">Updated '.get_the_modified_time('F j, g:i a').'</time></div>';
}

/* run shortcode filters against widget text http://digwp.com/2010/03/shortcodes-in-widgets/ */
add_filter('widget_text', 'do_shortcode');

/* http://www.wprecipes.com/wordpress-shortcode-display-the-loop */
function sc_query($atts, $content = null) {
	extract(shortcode_atts(array(
		"query" => '',
		"render" => 'summaries',
		"skip" => '0',
		"img_width" => '430',
		"timestamp" => 'true'
	), $atts));	
	$wp_query = new WP_Query($query);

	remove_filter( 'the_excerpt', 'wpautop' );

	ob_start();
	?>

	<div class="article-tease-wrapper <?php echo $render?>">

	<?php if ($render == "headlines") { ?><ul><?php } ?>

	<?php while ($wp_query->have_posts()) : $wp_query->the_post();
	if ($skip > 0) {
		$skip--; // this allows a call to skip a few posts.
				 // ex: if they're going to be used in a different shortcode somewhere else
	} else { ?>
	<div class="article-tease">

		<?php // image, headline, byline, excerpt
			  if ($render == 'image') {  ?>
				<a href="<?php the_permalink() ?>" rel="bookmark"><?php the_post_thumbnail( array( $img_width,500 ) ); ?></a>
			<h3><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h3>
			<h6 class="byline" style="display: inline"><?php do_action( 'ode_author'); ?></h6>
			<?php if ($timestamp != 'false') { iv_timeago(); } ?>
			<p class="article-tease-excerpt">
				<?php the_excerpt(); ?>
			</p>
			
		<?php // headline, byline, excerpt, with thumbnail
			  } else if ($render == 'summaries') { ?>
			<h3><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h3>
			<h6 class="byline" style="display: inline"><span class="article-tease-author"><?php do_action( 'ode_author'); ?></span></h6>
			<?php if ($timestamp != 'false') { iv_timeago(); } ?>
			<?php //echo time_ago(); ?>
			<p class="article-tease-excerpt">
				<a href="<?php the_permalink() ?>" rel="bookmark">
					<span class="article-tease-thumbnail"><?php the_post_thumbnail( array(80,80) ); ?></span>
				</a>
				<?php the_excerpt(); ?>
			</p>
		
		<?php // just headlines
		      } else if ($render == 'headlines') { ?>
			<li>
				<a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a>
				<?php if ($timestamp != 'false') { iv_timeago(); } ?>
				<?php //echo the_category(); // this gives all the categories, we need to just filter for the parent categories... hmm.?>
			</li>
		
		
		<?php } else if ($render == 'phototease') {  ?>
				<a href="<?php the_permalink() ?>" rel="bookmark"><?php the_post_thumbnail( array($img_width,500) ); ?></a>
			
		<?php } else { ?>
			<p>That renderer is not supported. Please check your shortcode embed.</p>

		<?php } ?>
	</div> <!-- .article-tease -->		
	
	<?php 
	} // finish up the skip/noskip block
	endwhile; // finish 
	?>

	<?php if ($render == "headlines") { ?></ul><?php } ?>

	</div><!-- .article-tease-wrapper -->

	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode("db", "sc_query");


/* ------------------------------------ INFO BOX SHORTCODE --------------------------------------- */

function infobox_query($atts, $content = null) {
	extract(shortcode_atts(array(
		"query" => '',
		"render" => 'headlines',
		"title" => "Related stories",
		"skip" => '0',
		"img_width" => '180',
		"timestamp" => 'false',
		"align" => 'left'
	), $atts));	

	ob_start();
	?>
	<div class="infobox infobox-<?php echo $align ?>" style="display: inline; width: 200px; margin: 0px 15px 0px 0pt; padding: 0px; border: 0px solid rgb(204, 204, 204); float: <?php echo $align ?>; font-size: 13px; line-height: 16px;">
			<div class="infobox-header" style="width:100%; margin-bottom: 5px; border-bottom: 3px solid #CCC; border-top: 3px solid #CCC; font-size: 16px; color: #666;">
				<h3 style="margin:0;padding:0;"><?php echo $title; ?></h3>
			</div>	
			<div class="infobox-body" style="list-type: disc">
				<?php if ($content != '') { echo $content; } ?>
				<?php if ($query != '') { echo do_shortcode("[db query=" . $query . " render=\"$render\" timestamp=\"false\" img_width=\"$img_width\"]"); } ?>
			</div>	
	</div><!-- end infobox -->
	<?php
	$content = ob_get_contents();
	ob_end_clean();
	return $content;
}
add_shortcode("infobox", "infobox_query");
	
?>