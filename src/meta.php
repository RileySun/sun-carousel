<?php

Carousel::init(); //See init for function explanations

class Carousel {
	public static function init() {
		//Init
		add_action('init', array( __CLASS__, 'registerCarouselsPostType'));
		
		//Custom Post Columns & Sort
		add_filter('manage_edit-carousel_columns', array( __CLASS__, 'carouselMetaColumns'));
		add_filter('manage_edit-carousel_sortable_columns', array( __CLASS__, 'carouselMetaSortableColumns'));
		add_action('load-edit.php', array(__CLASS__, 'carouselMetaSortLoad'));
		add_action('manage_carousel_posts_custom_column', array( __CLASS__, 'carouselManageMetaColumns'));
		
		//Custom Post Edit Page
		add_action('add_meta_boxes', array( __CLASS__, 'carouselMetaBoxAdd'));
		add_action('save_post', array( __CLASS__, 'carouselMetaBoxSave'));
		
		//Enqueue Scripts
		add_action('admin_enqueue_scripts', array(__CLASS__, 'carouselEnqueAdminScripts'));
		add_action('wp_enqueue_scripts', array(__CLASS__, 'carouselEnqueScripts'));
		
		//Set Shortcode Widget
		add_shortcode('sun-carousel', array(__CLASS__, 'sunCarouselWidget'));
	}
	
	public static function registerCarouselsPostType() {
		$labels = array( 
			'name' => _x( 'Carousels', 'carousels' ),
			'singular_name' => _x( 'Carousel', 'carousel' ),
			'add_new' => _x( 'Add New', 'carousel' ),
			'add_new_item' => _x( 'Add New Carousel', 'carousel' ),
			'edit_item' => _x( 'Edit Carousel', 'carousel' ),
			'new_item' => _x( 'New Carousel', 'carousel' ),
			'view_item' => _x( 'View Carousel', 'carousel' ),
			'search_items' => _x( 'Search Carousel', 'carousel' ),
			'not_found' => _x( 'No carousel found', 'carousel' ),
			'not_found_in_trash' => _x( 'No carousel found in Trash', 'carousel' ),
			'parent_item_colon' => _x( 'Parent Carousel:', 'carousel' ),
			'menu_name' => _x( 'Carousels', 'carousels' ),
		);
		$args = array( 
			'labels' => $labels,
			'hierarchical' => false,
			'description' => 'Simple Carousel',
			'supports' => array('title'),
			//'taxonomies' => array( 'category', 'post_tag', 'page-category' ),
			'public' => false,
			'show_ui' => true,
			'show_in_menu' => true,
			'menu_icon' => 'dashicons-images-alt',
			'show_in_nav_menus' => true,
			'publicly_queryable' => true,
			'exclude_from_search' => true,
			'has_archive' => false,
			'query_var' => false,
			'can_export' => true,
			'rewrite' => false,
			'capability_type' => 'post'
		);
		register_post_type('carousel', $args);
	}
	
	public static function carouselMetaColumns($columns) {
		$columns = array(
			'title' => __('Title'),
			'id' => __('ID'),
			'type' => __('Type'),
			'controls' => __('Controls'),
			'style' => __('Style'),
			'size' => __('Image Size'),
			'shortcode' => __('Shortcode')
		);

		return $columns;
	}
	
	public static function carouselMetaSortableColumns($columns) {
		$columns['id'] = 'id';
		$columns['type'] = 'type';
		$columns['controls'] = 'controls';
		$columns['style'] = 'style';
		$columns['size'] = 'size';
		return $columns;
	}
	
	public static function carouselMetaSortLoad() {
		add_filter('request', array( __CLASS__, 'carouselMetaSortColumns'));
	}
	
	public static function carouselMetaSortColumns($vars) {
		if (isset($vars['post_type']) && $vars['post_type'] == 'carousel') {
			
			if (isset( $vars['orderby']) && $vars['orderby'] == 'type') {
				$vars = array_merge(
					$vars,
					array(
						'meta_key' => 'Carousel-Type',
						'orderby' => 'meta_value'
					)
				);
			}
			
			if (isset( $vars['orderby']) && $vars['orderby'] == 'controls') {
				$vars = array_merge(
					$vars,
					array(
						'meta_key' => 'Carousel-Controls',
						'orderby' => 'meta_value'
					)
				);
			}
			
			if (isset( $vars['orderby']) && $vars['orderby'] == 'style') {
				$vars = array_merge(
					$vars,
					array(
						'meta_key' => 'Carousel-Style',
						'orderby' => 'meta_value'
					)
				);
			}
			
			if (isset( $vars['orderby']) && $vars['orderby'] == 'size') {
				$vars = array_merge(
					$vars,
					array(
						'meta_key' => 'Carousel-Size',
						'orderby' => 'meta_value'
					)
				);
			}
		}
		
		return $vars;
	}
	
	public static function carouselManageMetaColumns($column) {
		global $post;
		
		switch($column) {
			case 'type':
				$type = get_post_meta($post->ID, "Carousel-Type", true);
				echo ($type != '') ? $type : 'Unset';
				break;
			case 'id':
				echo $post->ID;
				break;
			case 'controls':
				$controls = get_post_meta($post->ID, "Carousel-Controls", true);
				echo ($controls != '') ? $controls : 'Unset';
				break;
			case 'style':
				$style = get_post_meta($post->ID, "Carousel-Style", true);
				echo ($style != '') ? $style : 'Unset';
				break;
			case 'size':
				$style = get_post_meta($post->ID, "Carousel-Size", true);
				echo ($style != '') ? $style : 'Unset';
				break;
			case 'shortcode':
				$shortcode = '[sun-carousel id=&quot;'.$post->ID.'&quot;]';
				echo $shortcode;
				break;
			default:
		}
	}
	
	public static function carouselMetaBoxAdd() {
		add_meta_box( 'carousel', 'Carousel Settings', array( __CLASS__, 'carouselMetaBoxCall'), 'carousel', 'normal', 'high' );
	}
	
	public static function carouselMetaBoxCall($post) {
		wp_enqueue_style('sun-carousel-style');
		wp_enqueue_script('sun-carousel-js');
	
		$rawMeta = get_post_meta($post->ID, "Carousel-IMG", true);
		$type = get_post_meta($post->ID, "Carousel-Type", true);
		$controls = get_post_meta($post->ID, "Carousel-Controls", true);
		$style = get_post_meta($post->ID, "Carousel-Style", true);
		$size = get_post_meta($post->ID, "Carousel-Size", true);
		
		$carouselImages = json_decode($rawMeta);
		$imgs = '';
		forEach($carouselImages as &$imgID) {
			//$imageLocation = 'https://www.sun-sys.tk/Modules/Wordpress/wp-content/logo.png';
			$imgURL = wp_get_attachment_image_url($imgID, 'thumbnail');
			$imgs .= '
				<div class="Sun-Carousel-IMG-Container" data-id="'.$imgID.'">
					<IMG class="Sun-Carousel-IMG" src="'.$imgURL.'" />
					<div class="Sun-Carousel-IMG-Remove">X</div>
				</div>
			';
		}
		
		$checked = array(
			'Simple' => ($type == 'Simple') ? 'checked' : '',
			'Advanced' => ($type == 'Advanced') ? 'checked' : '',
			'Enabled' => ($controls == 'Enabled') ? 'checked' : '',
			'Disabled' => ($controls == 'Disabled') ? 'checked' : '',
			'Light' => ($style == 'Light') ? 'checked' : '',
			'Dark' => ($style == 'Dark') ? 'checked' : '',
			'Small' => ($size == 'Small') ? 'checked' : '',
			'Medium' => ($size == 'Medium') ? 'checked' : '',
			'Large' => ($size == 'Large') ? 'checked' : ''
		);
		
		//$shortcode = '[sun-carousel id=&quot;'.$post->ID.'&quot; type=&quot;'.$type.'&quot; controls=&quot;'.$controls.'&quot; style=&quot;'.$style.'&quot;]';
		$shortcode = '[sun-carousel id=&quot;'.$post->ID.'&quot;]';
		
		$HTML = '
			<div id="Sun-Carousel" class="Sun-Carousel" data-postID="'.$post->ID.'">
				<div class="Sun-Carousel-Gallery">
					<h2 class="Sun-Carousel-Title">Gallery:</h2>
					
					<div class="Sun-Carousel-Container">
						<div id="Sun-Carousel-Images" class="Sun-Carousel-Images">
							'.$imgs.'
							<div class="Sun-Carousel-IMG-Container">
								<IMG id="Sun-Carousel-Add" class="Sun-Carousel-Add" src="'.plugin_dir_url( __FILE__ ).'assets/add.svg" />
							</div>
						</div>
					</div>
					<input id="Sun-Carousel-Input" name="Carousel-IMG" type="hidden" value="'.json_encode($carouselImages).'" />
				</div>
				
				<h2 class="Sun-Carousel-Title">Settings:</h2>
				
				<div class="Sun-Carousel-Settings">
					<div class="Sun-Carousel-Setting">
						<h2 class="Sun-Carousel-Settings-Title">Type:</h2>
						<label class="Sun-Carousel-Label">Simple</label>
						<input class="Sun-Carousel-Radio" type="radio" name="Carousel-Type" '.$checked['Simple'].' value="Simple" id="Simple" />
						<label class="Sun-Carousel-Label">Advanced:<div class="Sun-Carousel-Future">coming soon</div></label>
						<input class="Sun-Carousel-Radio" type="radio" name="Carousel-Type" '.$checked['Advanced'].' value="Advanced" disabled />
					</div>
				
					<div class="Sun-Carousel-Setting">
						<h2 class="Sun-Carousel-Settings-Title">Controls:</h2>
						<label class="Sun-Carousel-Label">Enabled</label>
						<input class="Sun-Carousel-Radio" type="radio" name="Carousel-Controls" '.$checked['Enabled'].' value="Enabled" id="Enabled"  />
						<label class="Sun-Carousel-Label">Disabled</label>
						<input class="Sun-Carousel-Radio" type="radio" name="Carousel-Controls" '.$checked['Disabled'].' value="Disabled" />
					</div>
				
					<div class="Sun-Carousel-Setting">
						<h2 class="Sun-Carousel-Settings-Title">Style:</h2>
						<label class="Sun-Carousel-Label">Light</label>
						<input class="Sun-Carousel-Radio" type="radio" name="Carousel-Style" '.$checked['Light'].' value="Light" id="Light" />
						<label class="Sun-Carousel-Label">Dark</label>
						<input class="Sun-Carousel-Radio" type="radio" name="Carousel-Style" '.$checked['Dark'].' value="Dark" />
					</div>
					
					<div class="Sun-Carousel-End">
						<h2 class="Sun-Carousel-End-Title">Image Size:</h2>
						<label class="Sun-Carousel-End-Label">Small</label>
						<input class="Sun-Carousel-End-Radio" type="radio" name="Carousel-Size" '.$checked['Small'].' value="Small" id="Small" />
						<label class="Sun-Carousel-End-Label">Medium</label>
						<input class="Sun-Carousel-End-Radio" type="radio" name="Carousel-Size" '.$checked['Medium'].' value="Medium" id="Medium" />
						<label class="Sun-Carousel-End-Label">Large</label>
						<input class="Sun-Carousel-End-Radio" type="radio" name="Carousel-Size" '.$checked['Large'].' value="Large" />
					</div>
				</div>
				
				<h2 class="Sun-Carousel-Title">Shortcode:</h2>
				<div class="Sun-Carousel-Shortcode">
					<input id="Sun-Carousel-Shortcode-Input" class="Sun-Carousel-Shortcode-Input" type="text" value="'.$shortcode.'" ReadOnly />
					<img id="Sun-Carousel-Shortcode-Copy" class="Sun-Carousel-Shortcode-Copy" src="'.plugin_dir_url( __FILE__ ) . 'assets/copy.svg'.'" />
				</div>
				<div id="Sun-Carousel-Notif" class="Sun-Carousel-Notif">Shortcode copied to clipboard</div>
			</div>
		';
		echo $HTML;
	}
	
	public static function carouselMetaBoxSave($post_id) {
		$list = array('Carousel-IMG', 'Carousel-Type', 'Carousel-Controls', 'Carousel-Style', 'Carousel-Size');
	
		foreach ($list as &$listItem) {
			$new_meta_value = ( isset( $_POST[$listItem] ) ? $_POST[$listItem] : '' );
		
			$meta_key = $listItem;
			$meta_value = get_post_meta( $post_id, $meta_key, true );
		
			if ( $new_meta_value && '' == $meta_value ) {
				add_post_meta( $post_id, $meta_key, $new_meta_value, true );
			}
			elseif ( $new_meta_value && $new_meta_value != $meta_value ) {
				update_post_meta( $post_id, $meta_key, $new_meta_value );
			}
			elseif ( '' == $new_meta_value && $meta_value ) {
				delete_post_meta( $post_id, $meta_key, $new_meta_value );
			}
		}
	}
	
	public static function carouselEnqueAdminScripts($hook) {
		if ($hook == 'post-new.php' || $hook == 'post.php') {
			wp_register_style('sun-carousel-style', plugin_dir_url( __FILE__ ) . 'assets/meta.css', array(), '1.0.0', 'all' );
			wp_register_script('sun-carousel-js', plugin_dir_url( __FILE__ ) . 'assets/meta.js', array('jquery'), '1.0.0', 'all' );
			wp_enqueue_media();
			
		}
	}
	
	public static function carouselEnqueScripts($hook) {
		global $pagenow;
		if ($pagenow == 'index.php') {
			wp_register_style('sun-carousel-widget-simple-style', plugin_dir_url( __FILE__ ) . 'assets/widget-simple.css', array(), '1.0.0', 'all' );
			wp_register_script('sun-carousel-widget-simple-js', plugin_dir_url( __FILE__ ) . 'assets/widget-simple.js', array(), '1.0.0', 'all' );
			wp_register_style('sun-carousel-widget-advanced-style', plugin_dir_url( __FILE__ ) . 'assets/widget-advanced.css', array(), '1.0.0', 'all' );
			wp_register_script('sun-carousel-widget-advanced-js', plugin_dir_url( __FILE__ ) . 'assets/widget-advanced.js', array(), '1.0.0', 'all' );
		}
	}
	
	public static function sunCarouselWidget($atts = [], $content = null, $tag = '') {
		//normalize
		$atts = array_change_key_case((array) $atts, CASE_LOWER);
		$newAtts = shortcode_atts(array(
			'id' => 0,
            'type' => 'Basic',
            'controls' => true,
            'style' => 'Dark',
            'size' => 'Small'
		), $atts, $tag);
		
		//Scripts
		wp_enqueue_style('sun-carousel-widget-style');
		wp_enqueue_script('sun-carousel-widget-js');
		
		//Template
		include_once('widget.php');
		$carousel = new CarouselWidget($newAtts['id']);
		return $carousel->render();
	}	
}