<?php
/* ------------------------------
 *      XMLSF Admin CLASS
 * ------------------------------ */
 
 class XMLSF_Admin extends XMLSitemapFeed {

	/**
	* SETTINGS
	*/

	// add our FancyBox Media Settings Section on Settings > Media admin page
	// TODO get a donation button in there and refer to support forum !
	public function privacy_settings_section() {
		echo '<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=ravanhagen%40gmail%2ecom&item_name=XML%20Sitemap%20Feeds&item_number='.XMLSF_VERSION.'&no_shipping=0&tax=0&charset=UTF%2d8&currency_code=EUR" title="'.sprintf(__('Donate to keep the free %s plugin development & support going!','easy-fancybox'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).'"><img src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" style="border:none;float:left;margin:4px 10px 0 0" alt="'.sprintf(__('Donate to keep the free %s plugin development & support going!','easy-fancybox'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).'" width="92" height="26" /></a>'.sprintf(__('These settings control the XML Sitemaps generated by the %s plugin.','xml-sitemap-feed'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).'<br/>';
		echo ('1' == get_option('blog_public')) ? sprintf(__('XML Sitemaps will be disabled automatically when you check the option %1$s at %2$s above.','xml-sitemap-feed'),'<strong>'.__('Discourage search engines from indexing this site').'</strong>','<strong>'.__('Search Engine Visibility').'</strong>') : '<span style="color: red" class="error">'.sprintf(__('XML Sitemaps are disabled because you have checked the option %1$s at %2$s above.','xml-sitemap-feed'),'<strong>'.__('Discourage search engines from indexing this site').'</strong>','<strong>'.__('Search Engine Visibility').'</strong>').'</span>';
		echo '</p>
    <script type="text/javascript">
        jQuery( document ).ready( function() {
            jQuery( "input[name=\'blog_public\']" ).on( \'change\', function() {
			jQuery("#xmlsf_sitemaps input").each(function() {
			  var $this = jQuery(this);
			  $this.attr("disabled") ? $this.removeAttr("disabled") : $this.attr("disabled", "disabled");
			});
            });
        });
    </script>';
/*		echo '
   <script type="text/javascript">
       jQuery( document ).ready( function() {
             jQuery( "#xmlsf_sitemaps_index" ).on( \'change\', function() {
			jQuery("#xmlsf_post_types input:not([type=\'hidden\']),#xmlsf_post_types select,#xmlsf_taxonomies input:not([type=\'hidden\']),#xmlsf_ping input").each(function() {
			  var $this = jQuery(this);
			  $this.attr("disabled") ? $this.removeAttr("disabled") : $this.attr("disabled", "disabled");
			});
            });
        });
    </script>';*/
	}
	
	public function sitemaps_settings_field() {
		$options = parent::get_sitemaps();
		$disabled = ('1' == get_option('blog_public')) ? false : true;

		echo '<fieldset id="xmlsf_sitemaps"><legend class="screen-reader-text">'.__('XML Sitemaps','xml-sitemap-feed').'</legend>
			<p><label><input type="checkbox" name="xmlsf_sitemaps[sitemap]" id="xmlsf_sitemaps_index" value="'.XMLSF_NAME.'" '.checked(isset($options['sitemap']), true, false).' '.disabled($disabled, true, false).' /> '.__('Regular XML Sitemaps','xml-sitemap-feed').'</label>';
		if (isset($options['sitemap']))
			echo '<span class="description">&nbsp; &ndash; &nbsp;<a href="'.trailingslashit(get_bloginfo('url')). ( ('' == get_option('permalink_structure')) ? '?feed=sitemap' : $options['sitemap'] ) .'" target="_blank">'.__('View').'</a></span>';

		echo '</p>
			<p><label><input type="checkbox" name="xmlsf_sitemaps[sitemap-news]" id="xmlsf_sitemaps_news" value="'.XMLSF_NEWS_NAME.'" '.checked(isset($options['sitemap-news']), true, false).' '.disabled($disabled, true, false).' /> '.__('Google News Sitemap','xml-sitemap-feed').'</label>';
		if (isset($options['sitemap-news']))
			echo '<span class="description">&nbsp; &ndash; &nbsp;<a href="'.trailingslashit(get_bloginfo('url')). ( ('' == get_option('permalink_structure')) ? '?feed=sitemap-news' : $options['sitemap-news'] ) .'" target="_blank">'.__('View').'</a></span>';
		echo '</p>
		</fieldset>';
	}

	public function post_types_settings_field() {
		$options = parent::get_post_types();
		$defaults = parent::defaults('post_types');
		$do_note = false;
		echo '<fieldset id="xmlsf_post_types"><legend class="screen-reader-text">'.__('Include post types','xml-sitemap-feed').'</legend>
			';
		foreach ( get_post_types(array('public'=>true),'objects') as $post_type ) {
			// skip unallowed post types
			if (in_array($post_type->name,parent::disabled_post_types()))
				continue;
				
			$count = wp_count_posts( $post_type->name );
			
			echo '
				<p><input type="hidden" name="xmlsf_post_types['.
				$post_type->name.'][name]" value="'.
				$post_type->name.'" />';

			echo '
				<label><input type="checkbox" name="xmlsf_post_types['.
				$post_type->name.'][active]" id="xmlsf_post_types_'.
				$post_type->name.'" value="1" '.
				checked( !empty($options[$post_type->name]["active"]), true, false).' /> '.
				$post_type->label.'</label> ('.
				$count->publish.')';
			
			if (!empty($options[$post_type->name]['active'])) {
				
				echo ' &nbsp;&ndash;&nbsp; <span class="description"><a id="xmlsf_post_types_'.$post_type->name.'_link" href="#xmlsf_post_types_'.$post_type->name.'_settings">'.__('Settings').'</a></span></p>
    <script type="text/javascript">
        jQuery( document ).ready( function() {
            jQuery("#xmlsf_post_types_'.$post_type->name.'_settings").hide();
            jQuery("#xmlsf_post_types_'.$post_type->name.'_link").click( function(event) {
            		event.preventDefault();
			jQuery("#xmlsf_post_types_'.$post_type->name.'_settings").toggle("slow");
	    });
        });
    </script>
    				<ul style="margin-left:18px" id="xmlsf_post_types_'.$post_type->name.'_settings">';

				
				if ( isset($defaults[$post_type->name]['archive']) ) {
					$archives = array (
								'yearly' => __('Year','xml-sitemap-feed'),
								'monthly' => __('Month','xml-sitemap-feed') 
								);
					$archive = !empty($options[$post_type->name]['archive']) ? $options[$post_type->name]['archive'] : $defaults[$post_type->name]['archive'];
					echo ' 
					<li><label>'.__('Split by','xml-sitemap-feed').' <select name="xmlsf_post_types['.
						$post_type->name.'][archive]" id="xmlsf_post_types_'.
						$post_type->name.'_archive">
						<option value="">'.__('None').'</option>';
					foreach ($archives as $value => $translation)
						echo '
						<option value="'.$value.'" '.
						selected( $archive == $value, true, false).
						'>'.$translation.'</option>';
					echo '</select>
					</label> <span class="description"> '.__('Split by year if you experience errors or slow sitemaps. In very rare cases, split by month is needed.','xml-sitemap-feed').'</span></li>';
				}

				$priority_val = !empty($options[$post_type->name]['priority']) ? $options[$post_type->name]['priority'] : $defaults[$post_type->name]['priority'];
				echo ' 
					<li><label>'.__('Priority','xml-sitemap-feed').' <input type="number" step="0.1" min="0.1" max="0.9" name="xmlsf_post_types['.
					$post_type->name.'][priority]" id="xmlsf_post_types_'.
					$post_type->name.'_priority" value="'.$priority_val.'" class="small-text"></label> <span class="description">'.__('Priority can be overridden on individual posts. *','xml-sitemap-feed').'</span></li>';

				echo '
					<li><label><input type="checkbox" name="xmlsf_post_types['.
					$post_type->name.'][dynamic_priority]" value="1" '.
					checked( !empty($options[$post_type->name]['dynamic_priority']), true, false).' /> '.__('Automatically adjusts Priority according to relative age and comment count.','xml-sitemap-feed').'</label> <span class="description">'.__('Sticky posts will not be subject to reduction by age. Individual posts with fixed Priority will always keep that value.','xml-sitemap-feed').'</span></li>';
					
				echo '
					<li><label><input type="checkbox" name="xmlsf_post_types['.
					$post_type->name.'][update_lastmod_on_comments]" value="1" '.
					checked( !empty($options[$post_type->name]["update_lastmod_on_comments"]), true, false).' /> '.__('Update Lastmod and Changefreq on comments.','xml-sitemap-feed').'</label> <span class="description">'.__('Set this if discussion on your site warrants reindexation upon each new comment.','xml-sitemap-feed').'</li>';
										
				if (isset($defaults[$post_type->name]['tags'])) :
				echo '
					<li><fieldset id="xmlsf_post_types_tags"><legend><strong>'.__('Include:','xml-sitemap-feed').' </strong></legend>
						<ul style="margin-left:18px">';

					$image = !empty($options[$post_type->name]['tags']['image']) ? $options[$post_type->name]['tags']['image'] : $defaults[$post_type->name]['tags']['image'];
					echo ' 
					<li><label>'.__('Image tags for','xml-sitemap-feed').' <select name="xmlsf_post_types['.
						$post_type->name.'][tags][image]">
						<option value="no">'.__('None').'</option>
						<option value="featured" '.
						selected( $image == "featured", true, false).
						'>'.__('Featured Image').'</option>
						<option value="attached" '.
						selected( $image == "attached", true, false).
						'>'.__('Attached images','xml-sitemap-feed').'</option>
						';
					echo '</select></label></li>';

					if (isset($defaults[$post_type->name]['tags']['news'])) {
						echo '
							<li><label><input type="checkbox" name="xmlsf_post_types['.
						$post_type->name.'][tags][news]" value="1" '.
						checked( !empty($options[$post_type->name]['tags']['news']), true, false).' /> '.__('Google News tags','xml-sitemap-feed').'</label> <span class="description">'.__('Only set when your site has been or will soon be accepted by Google News. **','xml-sitemap-feed').'</span></li>';
					}

				echo '
						</ul>
					</fieldset></li>';
				endif;
					
				echo '
					</ul>';
			} else {
				echo '</p>';
			}

		}

		echo '
		<p class="description">'.__('* Priority settings do not affect ranking in search results in any way. They are only meant to suggest search engines which URLs to index first. Once a URL has been indexed, its Priority becomes meaningless until its Lastmod is updated.','xml-sitemap-feed').' <a href="#xmlsf_post_types_note_1_more" id="xmlsf_post_types_note_1_link">'.__('(more...)').'</a> <span id="xmlsf_post_types_note_1_more">'.__('Maximum Priority (1.0) is reserved for the front page, individual posts and, when allowed, posts with high comment count.','xml-sitemap-feed').' '.__('Priority values are taken as relative values. Setting all to the same (high) value is pointless.','xml-sitemap-feed').'</span></p>
<script type="text/javascript">
jQuery( document ).ready( function() {
    jQuery("#xmlsf_post_types_note_1_more").hide();
    jQuery("#xmlsf_post_types_note_1_link").click( function(event) {
	event.preventDefault();
	jQuery("#xmlsf_post_types_note_1_link").hide();
	jQuery("#xmlsf_post_types_note_1_more").show("slow");
    });
});
</script>';
		echo '
		<p class="description">'.sprintf(__('** Google recommends using a seperate news sitemap. You can do this by checking the option %1$s at %2$s above.','xml-sitemap-feed'),'<strong>'.__('Google News Sitemap','xml-sitemap-feed').'</strong>','<strong>'.__('Enable XML sitemaps','xml-sitemap-feed').'</strong>').'</p>';
		echo '
		</fieldset>';
	}

	public function taxonomies_settings_field() {
		$options = parent::get_taxonomies();
		$active = parent::get_option('post_types');
		$output = '';

		foreach ( get_taxonomies(array('public'=>true),'objects') as $taxonomy ) {

			$skip = true;
			foreach ( $taxonomy->object_type as $post_type)
				if (!empty($active[$post_type]['active']) && $active[$post_type]['active'] == '1')
					$skip = false; 
			if ($skip) continue; // skip if none of the associated post types are active
			
			$count = wp_count_terms( $taxonomy->name );
			$output .= '
				<label><input type="checkbox" name="xmlsf_taxonomies['.
				$taxonomy->name.']" id="xmlsf_taxonomies_'.
				$taxonomy->name.'" value="'.
				$taxonomy->name.'"'.
				checked(in_array($taxonomy->name,$options), true, false).' /> '.
				$taxonomy->label.'</label> ('.
				$count.') ';

//			if ( in_array($taxonomy->name,$options) && empty($taxonomy->show_tagcloud) )
//				echo '<span class="description error" style="color: red">'.__('This taxonomy type might not be suitable for public use. Please check the urls in the taxonomy sitemap.','xml-sitemap-feed').'</span>';

			$output .= '
				<br />';
		}
		
		if ($output) {
			echo '
		<fieldset id="xmlsf_taxonomies"><legend class="screen-reader-text">'.__('Include taxonomies','xml-sitemap-feed').'</legend>
			';

			echo $output;

			echo '
			<p class="description">'.__('It is generally not recommended to include taxonomy pages, unless their content brings added value. For example, when you use category descriptions with information that is not present elsewhere on your site or if taxonomy pages list posts with an excerpt that is different from, but complementary to the post content. In these cases you might consider including certain taxonomies. Otherwise, you might even consider disallowing indexation to prevent a possible duplicate content penalty. You can do this by adding specific robots.txt rules below.','xml-sitemap-feed');
			echo '</p>
		</fieldset>';
		} else {
			echo '
		<p style="color: red" class="error">'.__('No taxonomies available for the currently included post types.','xml-sitemap-feed').'</p>';
		}
	}

	public function ping_settings_field() {
		$options = parent::get_ping();
		$pings = parent::get_pings();
		// search engines
		$se = array(
			'google' => array (
				'name' => __('Google','xml-sitemap-feed'),
				'uri' => 'http://www.google.com/webmasters/tools/ping?sitemap='
				),
			'bing' => array (
				'name' => __('Bing','xml-sitemap-feed'),
				'uri' => 'http://www.bing.com/ping?sitemap='
				)
			);

		echo '
		<fieldset id="xmlsf_ping"><legend class="screen-reader-text">'.__('Ping on Publish','xml-sitemap-feed').'</legend>
			';
		foreach ( $se as $name => $values ) {

			echo '
				<input type="hidden" name="xmlsf_ping['.
				$name.'][uri]" value="'.
				$values['uri'].'" />';

			echo '
				<label><input type="checkbox" name="xmlsf_ping['.
				$name.'][active]" id="xmlsf_ping_'.
				$name.'" value="1"'.
				checked( !empty($options[$name]["active"]), true, false).' /> '.
				$values['name'].'</label>';
			
			echo ' <span class="description">';
			if (isset($pings[$name]))
				foreach ((array)$pings[$name] as $pretty => $time)
					echo sprintf(__('Successfully pinged for %1$s on %2$s GMT.','xml-sitemap-feed'),$pretty, $time).' ';
			echo '</span><br />';
		}

		echo '
		</fieldset>';
	}

	public function robots_settings_field() {
		echo '<label>'.sprintf(__('Rules to append to the %s generated by WordPress.','xml-sitemap-feed'),'<a href="'.trailingslashit(get_bloginfo('url')).'robots.txt" target="_blank">robots.txt</a>').'<br /><textarea name="xmlsf_robots" id="xmlsf_robots" class="large-text" cols="50" rows="5" />'.esc_attr( parent::get_robots() ).'</textarea></label>
		<p class="description"><span style="color: red" class="error">'.__('Only add rules here when you know what you are doing, otherwise you might break search engine access to your site.','xml-sitemap-feed').'</span><br />'.__('These rules will not have effect when you are using a static robots.txt file.','xml-sitemap-feed').'</p>';
	}

	public function reset_settings_field() {

		echo '
		<label><input type="checkbox" name="xmlsf_sitemaps[reset]" value="1" /> '.
				__('Clear all XML Sitemap Feed options from the database and start fresh with the default settings.','xml-sitemap-feed').'</label>';
		echo '
		<p class="description">'.sprintf(__('Disabling and reenabling the %s plugin will have the same effect.','xml-sitemap-feed'),__('XML Sitemap & Google News Feeds','xml-sitemap-feed')).'</p>';
	}

	//sanitize callback functions
	
	public function sanitize_robots_settings($new) {
		return trim(strip_tags($new));
	}
	
	public function sanitize_sitemaps_settings($new) {
		$old = parent::get_sitemaps();
		if (isset($new['reset']) && $new['reset'] == '1') // if reset is checked, set transient to clear all settings
			set_transient('xmlsf_clear_settings','');
		elseif ($old != $new) // when sitemaps are added or removed, set transient to flush rewrite rules
			set_transient('xmlsf_flush_rewrite_rules','');
		return $new;
	}
	
	public function sanitize_post_types_settings( $new = array() ) {
		$old = parent::get_post_types();
		$defaults = parent::defaults('post_types');
		$sanitized = $new;
		$flush = false;
		
		foreach ($new as $post_type => $settings) {

			// when post types are (de)activated, set transient to flush rewrite rules
			if ( ( !empty($old[$post_type]['active']) && empty($settings['active']) ) || ( empty($old[$post_type]['active']) && !empty($settings['active']) ) )
				$flush = true;

			if ( isset($settings['priority']) && is_numeric($settings['priority']) ) {
				if ($settings['priority'] <= 0)
					$sanitized[$post_type]['priority'] = '0.1';
				elseif ($settings['priority'] >= 1)
					$sanitized[$post_type]['priority'] = '0.9';
			} else {
				$sanitized[$post_type]['priority'] = $defaults[$post_type]['priority'];
			}
		}
		
		if ($flush)
			set_transient('xmlsf_flush_rewrite_rules','');

		return $sanitized;
	}

	public function sanitize_taxonomies_settings($new) {
		$old = parent::get_taxonomies();
		if ($old != $new) // when taxonomy types are added or removed, set transient to flush rewrite rules
			set_transient('xmlsf_flush_rewrite_rules','');
		return $new;
	}

	public function sanitize_ping_settings($new) {
		return $new;
	}
	
	public function add_action_link( $links ) {
		$settings_link = '<a href="' . admin_url('options-reading.php') . '#xmlsf">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link ); 
		return $links;
	}

	/**
	* META BOX
	*/

	/* Adds a box to the side column */
	public function add_meta_box() 
	{
		// Only include metaboxes on post types that are included
		foreach (parent::get_post_types() as $post_type) 
			if (isset($post_type["active"]))
				add_meta_box(
				    'xmlsf_section',
				    __( 'XML Sitemap', 'xml-sitemap-feed' ),
				    array($this,'meta_box'),
				    $post_type['name'],
				    'side'
				);
	}

	public function meta_box($post) 
	{
		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'xmlsf_sitemap_nonce' );

		// The actual fields for data entry
		// Use get_post_meta to retrieve an existing value from the database and use the value for the form
		$value = get_post_meta( $post->ID, '_xmlsf_exclude', true );
		echo '<!-- '.$value.' -->';
		echo '<p><label><input type="checkbox" name="xmlsf_exclude" id="xmlsf_exclude" value="1"'.checked(!empty($value), true, false).' > ';
		_e('Exclude from XML Sitemap','xml-sitemap-feed');
		echo '</label></p>';

		echo '<p><label>';
		_e('Priority','xml-sitemap-feed');
		echo ' <input type="number" step="0.1" min="0" max="1" name="xmlsf_priority" id="xmlsf_priority" value="';
		echo get_post_meta( $post->ID, '_xmlsf_priority', true );
		echo '" class="small-text"></label> <span class="description">';
		printf(__('Leave empty for automatic Priority as configured on %1$s > %2$s.','xml-sitemap-feed'),__('Settings'),__('Reading'));
		echo '</span></p>';
	}
  
	/* When the post is saved, save our meta data */
	function save_metadata( $post_id ) 
	{
		if ( !isset($post_id) )
			$post_id = (int)$_REQUEST['post_ID'];

		if ( !current_user_can( 'edit_post', $post_id ) || !isset($_POST['xmlsf_sitemap_nonce']) || !wp_verify_nonce($_POST['xmlsf_sitemap_nonce'], plugin_basename( __FILE__ )) )
			return;

		// _xmlsf_priority
		if ( isset($_POST['xmlsf_priority']) && $_POST['xmlsf_priority'] != '' && is_numeric($_POST['xmlsf_priority']) ) {
			if ($_POST['xmlsf_priority'] <= 0)
				update_post_meta($post_id, 'priority', '0');
			elseif ($_POST['xmlsf_priority'] >= 1)
				update_post_meta($post_id, '_xmlsf_priority', '1');
			else
					update_post_meta($post_id, '_xmlsf_priority', $_POST['xmlsf_priority']);
		} else {
			delete_post_meta($post_id, '_xmlsf_priority');
		}
		
		// _xmlsf_exclude
		if ( isset($_POST['xmlsf_exclude']) && $_POST['xmlsf_exclude'] != '' ) {
			update_post_meta($post_id, '_xmlsf_exclude', $_POST['xmlsf_exclude']);
		} else {
			delete_post_meta($post_id, '_xmlsf_exclude');
		}
		
	}

	/**
	* CONSTRUCTOR
	*/

	function __construct() {
		
		// SETTINGS
		add_settings_section('xmlsf_main_section', '<a name="xmlsf"></a>'.__('XML Sitemaps','xml-sitemap-feed'), array($this,'privacy_settings_section'), 'reading');
		// sitemaps
		register_setting('reading', 'xmlsf_sitemaps', array($this,'sanitize_sitemaps_settings') );
		add_settings_field('xmlsf_sitemaps', __('Enable XML sitemaps','xml-sitemap-feed'), array($this,'sitemaps_settings_field'), 'reading', 'xmlsf_main_section');

		$sitemaps = parent::get_sitemaps();
		if (isset($sitemaps['sitemap'])) {
			// post_types
			register_setting('reading', 'xmlsf_post_types', array($this,'sanitize_post_types_settings') );
			add_settings_field('xmlsf_post_types', __('Include post types','xml-sitemap-feed'), array($this,'post_types_settings_field'), 'reading', 'xmlsf_main_section');
			// taxonomies
			register_setting('reading', 'xmlsf_taxonomies', array($this,'sanitize_taxonomies_settings') );
			add_settings_field('xmlsf_taxonomies', __('Include taxonomies','xml-sitemap-feed'), array($this,'taxonomies_settings_field'), 'reading', 'xmlsf_main_section');
			// pings
			register_setting('reading', 'xmlsf_ping', array($this,'sanitize_ping_settings') );
			add_settings_field('xmlsf_ping', __('Ping on Publish','xml-sitemap-feed'), array($this,'ping_settings_field'), 'reading', 'xmlsf_main_section');
		}
		
		//robots only when permalinks are set
		if(''!=get_option('permalink_structure')) {
			register_setting('reading', 'xmlsf_robots', array($this,'sanitize_robots_settings') );
			add_settings_field('xmlsf_robots', __('Additional robots.txt rules','xml-sitemap-feed'), array($this,'robots_settings_field'), 'reading', 'xmlsf_main_section');
		}

		add_settings_field('xmlsf_reset', __('Reset XML sitemaps','xml-sitemap-feed'), array($this,'reset_settings_field'), 'reading', 'xmlsf_main_section');
		
		// POST META BOX
		add_action( 'add_meta_boxes', array($this,'add_meta_box') );
		add_action( 'save_post', array($this,'save_metadata') );
	
		// ACTION LINK
		add_filter('plugin_action_links_' . XMLSF_PLUGIN_BASENAME, array($this, 'add_action_link') );
	}

}

/* ----------------------
*      INSTANTIATE
* ---------------------- */

if ( class_exists('XMLSitemapFeed') )
	$xmlsf_admin = new XMLSF_Admin();

