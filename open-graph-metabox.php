<?php
/*
Plugin Name: Open Graph Metabox
Plugin URI:  https://wordpress.org/plugins/open-graph-metabox/
Description: This plugin lets you set the Open Graph meta tags per post, page or custom post type.
Version:     1.4
Author:      Media-Enzo
Author URI:  http://media-enzo.nl
Text Domain: open-graph-metabox
*/



/*
 * Security check
 * Prevent direct access to the file.
 *
 * @since 1.4
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



/*
 * Open Graph Metabox
 * The main plugin
 *
 * @since 1.0
 */
class OpenGraphMetabox {

	private $og_types;

	function __construct() {

		$this->og_types = array(

			"Most used" => array(
				'blog'           => __( 'Blog', 'open-graph-metabox' ),
				'website'        => __( 'Website', 'open-graph-metabox' ),
				'article'        => __( 'Article', 'open-graph-metabox' )
			),

			"Activities" => array(
				'activity'       => __( 'Activity', 'open-graph-metabox' ),
				'sport'          => __( 'Sport', 'open-graph-metabox' )
			),

			"Businesses" => array(
				'bar'            => __( 'Bar', 'open-graph-metabox' ),
				'company'        => __( 'Company', 'open-graph-metabox' ),
				'cafe'           => __( 'Cafe', 'open-graph-metabox' ),
				'hotel'          => __( 'Hotel', 'open-graph-metabox' ),
				'restaurant'     => __( 'Restaurant', 'open-graph-metabox' )
			),

			"Groups" => array(
				'cause'          => __( 'Cause', 'open-graph-metabox' ),
				'sports_league'  => __( 'Sports league', 'open-graph-metabox' ),
				'sports_team'    => __( 'Sports team', 'open-graph-metabox' )
			),

			"Organizations" => array(
				'band'           => __( 'Band', 'open-graph-metabox' ),
				'government'     => __( 'Government', 'open-graph-metabox' ),
				'non_profit'     => __( 'Non_profit', 'open-graph-metabox' ),
				'school'         => __( 'School', 'open-graph-metabox' ),
				'university'     => __( 'University', 'open-graph-metabox' )
			),

			"People" => array(
				'actor'          => __( 'Actor', 'open-graph-metabox' ),
				'athlete'        => __( 'Athlete', 'open-graph-metabox' ),
				'author'         => __( 'Author', 'open-graph-metabox' ),
				'director'       => __( 'Director', 'open-graph-metabox' ),
				'musician'       => __( 'Musician', 'open-graph-metabox' ),
				'politician'     => __( 'Politician', 'open-graph-metabox' ),
				'public_figure'  => __( 'Public figure', 'open-graph-metabox' )
			),

			"Places" => array(
				'city'           => __( 'City', 'open-graph-metabox' ),
				'country'        => __( 'Country', 'open-graph-metabox' ),
				'landmark'       => __( 'Landmark', 'open-graph-metabox' ),
				'state_province' => __( 'State province', 'open-graph-metabox' )
			),

			"Products and Entertainment" => array(
				'album'          => __( 'Album', 'open-graph-metabox' ),
				'book'           => __( 'Book', 'open-graph-metabox' ),
				'drink'          => __( 'Drink', 'open-graph-metabox' ),
				'food'           => __( 'Food', 'open-graph-metabox' ),
				'game'           => __( 'Game', 'open-graph-metabox' ),
				'product'        => __( 'Product', 'open-graph-metabox' ),
				'song'           => __( 'Song', 'open-graph-metabox' ),
				'movie'          => __( 'Movie', 'open-graph-metabox' ),
				'tv_show'        => __( 'TV Show', 'open-graph-metabox' )
			)
		);

		add_action( 'plugins_loaded', array( &$this, 'load_textdomain' ) );

		add_action( 'admin_enqueue_scripts', array( &$this, 'register_admin_scripts' ) );
		add_action( 'admin_print_styles', array( &$this, 'register_admin_styles' ) );

		add_action( 'add_meta_boxes', array( &$this, 'create_meta_box' ) );
		add_action( 'save_post', array( &$this, 'save_metabox_data' ) );

		add_action( 'wp_head', array( &$this, 'generate_meta_tags' ) );

		add_action( 'admin_menu', array( &$this, 'create_menus' ));

	}


	function load_textdomain() {

		load_plugin_textdomain( 'open-graph-metabox' );

	}


	public function register_admin_styles() {

		wp_register_style( 'open-graph-metabox-admin-styles', plugins_url( 'open-graph-metabox/css/admin.css' ) );
		wp_enqueue_style( 'open-graph-metabox-admin-styles' );
		wp_enqueue_style( 'thickbox' );

	}


	public function register_admin_scripts() {

		wp_register_script( 'open-graph-metabox-admin-script', plugins_url( 'open-graph-metabox/js/admin.js' ) );
		wp_enqueue_script( 'open-graph-metabox-admin-script' );
		wp_enqueue_script( 'media-upload' );
		wp_enqueue_script( 'thickbox' );

	}


	public function create_menus() {

		add_options_page(
			__( 'Open Graph settings', 'open-graph-metabox' ),
			__( 'Open Graph settings', 'open-graph-metabox' ),
			'manage_options',
			'open-graph-metabox',
			array( &$this, 'display_settings_page' )
		);

	}


	public function create_meta_box() {

		$post_types = get_post_types();
		foreach ( $post_types as $post_type ) {
			add_meta_box(
				'open_graph_metabox',
				__( 'Open Graph', 'open-graph-metabox' ),
				array( &$this, 'render_meta_box'),
				$post_type
			);
		}

	}


	public function render_meta_box() {

		global $post;

		$default_title = get_option('_open_graph_title');
		$default_description = get_option('_open_graph_description');
		$default_type = get_option('_open_graph_type');
		$default_image = get_option('_open_graph_image');

		$open_graph_title = get_post_meta( $post->ID, 'open_graph_title', true );
		$open_graph_description = get_post_meta( $post->ID, 'open_graph_description', true );
		$open_graph_image = get_post_meta( $post->ID, 'open_graph_image', true );
		$open_graph_type = get_post_meta( $post->ID, 'open_graph_type', true );

		$open_graph_title = ($open_graph_title == "") ? $default_title : $open_graph_title;
		$open_graph_description = ($open_graph_description == "") ? $default_description : $open_graph_description;
		$open_graph_image = ($open_graph_image == "") ? $default_image : $open_graph_image;
		$open_graph_type = ($open_graph_type == "") ? $default_type : $open_graph_type;

		wp_nonce_field( plugin_basename( __FILE__ ), 'open_graph_metabox_noncename' );
		?>

			<p>
				<?php _e('Please provide optional Open Graph meta values to override the <a href="options-general.php?page=open-graph-metabox">defaults</a>.', 'open-graph-metabox'); ?>
			</p>
			<p>
				<?php _e("Open the <a href='https://developers.facebook.com/tools/debug/og/object?q=".urlencode(get_permalink())."' target='_blank'>Facebook Open Graph debugger</a> for this page."); ?>
			</p>

			<table class="form-table">

				<tr>
					<th><label for="open_graph_title"><?php _e('Title', 'open-graph-metabox'); ?></label></th>
					<td><input name="open_graph_title" id="open_graph_title" type="text" value="<?php echo $open_graph_title; ?>" class="large-text code" /></td>
				</tr>

				<tr>
					<th><label for="open_graph_description"><?php _e('Description', 'open-graph-metabox'); ?></label></th>
					<td><textarea class="large-text" rows="3" id="open_graph_description" name="open_graph_description"><?php echo $open_graph_description; ?></textarea></td>
				</tr>

				<tr>
					<th><label for="open_graph_image"><?php _e('Image', 'open-graph-metabox'); ?></label></th>
					<td>
						<input name="open_graph_image" id="open_graph_image_value" type="text" value="<?php echo $open_graph_image; ?>" class="normal-text code" />
						<input type="button" class="button open_graph_image" id="open_graph_image" value="<?php _e('Select image', 'open-graph-metabox'); ?>">
					</td>
				</tr>

				<tr>
					<th><label for="open_graph_type"><?php _e('Type', 'open-graph-metabox'); ?></label></th>
					<td>
						<select name="open_graph_type">

							<?php 
								foreach($this->og_types AS $group => $types) {

									echo '<optgroup label="'. $group .'">';

										if(sizeof($types) > 0 && is_array($types)) {
											foreach($types AS $type => $label) {
												$selected = ($open_graph_type == $type) ? "selected='selected'" : "";
												echo '<option '.$selected.' value="'.$type.'">'. $label .'</option>';
											}
										}

									echo '</optgroup>';

								}
							?>
						</select>
					</td>
				</tr>

			</table>

		<?php

	}


	function save_metabox_data( $post_id ) {

	  // Check if it's not an autosave.
	  if ( wp_is_post_autosave( $post_id ) )
	      return;

	  // Check if it's not a revision.
	  if ( wp_is_post_revision( $post_id ) )
	      return;

	  // verify this came from the our screen and with proper authorization,
	  // because save_post can be triggered at other times
	  if ( !wp_verify_nonce( $_POST['open_graph_metabox_noncename'], plugin_basename( __FILE__ ) ) )
	      return;

	  // Check permissions
	  if ( 'page' == $_POST['post_type'] ) 
	  {
	    if ( !current_user_can( 'edit_page', $post_id ) )
	        return;
	  }
	  else
	  {
	    if ( !current_user_can( 'edit_post', $post_id ) )
	        return;
	  }

	  // OK, we're authenticated: we need to find and save the data

	  $open_graph_title = esc_attr($_POST['open_graph_title']);
	  $open_graph_description = esc_attr($_POST['open_graph_description']);
	  $open_graph_type = esc_attr($_POST['open_graph_type']);
	  $open_graph_image = esc_attr($_POST['open_graph_image']);

	  update_post_meta( $post_id, 'open_graph_title', $open_graph_title );
	  update_post_meta( $post_id, 'open_graph_description', $open_graph_description );
	  update_post_meta( $post_id, 'open_graph_type', $open_graph_type );
	  update_post_meta( $post_id, 'open_graph_image', $open_graph_image );

	}


	function generate_meta_tags() {
		
		global $post;
		
		$open_graph_title = get_post_meta( $post->ID, 'open_graph_title', true );
		$open_graph_description = get_post_meta( $post->ID, 'open_graph_description', true );
		$open_graph_image = get_post_meta( $post->ID, 'open_graph_image', true );
		$open_graph_type = get_post_meta( $post->ID, 'open_graph_type', true );

		if( is_front_page() || is_home() ) {
			$open_graph_title = get_option('_home_open_graph_title');
			$open_graph_description = get_option('_home_open_graph_description');
			$open_graph_type = get_option('_home_open_graph_type');
			$open_graph_image = get_option('_home_open_graph_image');
		}

		$facebook_app_id = get_option('_open_graph_app_id');
		$facebook_admins = get_option('_open_graph_facebook_admins');

		?>
		<!-- Open Graph tags generated by Open Graph Metabox for WordPress -->
		<meta property="og:url" content="<?php the_permalink(); ?>" />

		<?php if($open_graph_title && $open_graph_title != "") { ?>
			<meta property="og:title" content="<?php echo $open_graph_title; ?>" />
		<?php } else { ?>
			<meta property="og:title" content="<?php wp_title(""); ?>" />
		<?php } ?>

		<?php if($open_graph_description && $open_graph_description != "") { ?>
			<meta property="og:description" content="<?php echo $open_graph_description; ?>" />
		<?php } else { ?>
			<meta property="og:description" content="<?php echo strip_tags(get_the_excerpt()); ?>" />
		<?php } ?>

		<?php if($open_graph_image && $open_graph_image != "") { ?>
			<meta property="og:image" content="<?php echo $open_graph_image; ?>" />
		<?php } ?>

		<?php if($open_graph_type && $open_graph_type != "") { ?>
			<meta property="og:type" content="<?php echo $open_graph_type; ?>" />
		<?php } ?>

		<?php if($facebook_app_id && $facebook_app_id != "") { ?>
			<meta property="fb:app_id" content="<?php echo $facebook_app_id; ?>" />
		<?php } ?>

		<?php if($facebook_admins && $facebook_admins != "") { ?>
			<meta property="fb:admins" content="<?php echo $facebook_admins; ?>" />
		<?php } ?>

		<!-- /Open Graph tags generated by Open Graph Metabox for WordPress -->
		<?php

	}


	public function display_settings_page() {

		$default_title = get_option('_open_graph_title');
		$default_description = get_option('_open_graph_description');
		$default_type = get_option('_open_graph_type');
		$default_image = get_option('_open_graph_image');

		$home_title = get_option('_home_open_graph_title');
		$home_description = get_option('_home_open_graph_description');
		$home_type = get_option('_home_open_graph_type');
		$home_image = get_option('_home_open_graph_image');

		$facebook_app_id = get_option('_open_graph_app_id'); // fb:app_id tag
		$facebook_admins = get_option('_open_graph_facebook_admins'); // fb:admins tag

		if(isset($_POST['save_defaults'])) {

			$default_title = esc_attr($_POST['open_graph_title']);
			$default_description = esc_attr($_POST['open_graph_description']);
			$default_type = esc_attr($_POST['open_graph_type']);
			$default_image = esc_attr($_POST['open_graph_image']);

			$home_title = esc_attr($_POST['home_open_graph_title']);
			$home_description = esc_attr($_POST['home_open_graph_description']);
			$home_type = esc_attr($_POST['home_open_graph_type']);
			$home_image = esc_attr($_POST['home_open_graph_image']);

			$facebook_app_id = esc_attr($_POST['open_graph_app_id']);
			$facebook_admins = esc_attr($_POST['open_graph_facebook_admins']);

			update_option('_open_graph_title', $default_title);
			update_option('_open_graph_description', $default_description);
			update_option('_open_graph_type', $default_type);
			update_option('_open_graph_image', $default_image);

			update_option('_home_open_graph_title', $home_title);
			update_option('_home_open_graph_description', $home_description);
			update_option('_home_open_graph_type', $home_type);
			update_option('_home_open_graph_image', $home_image);

			update_option('_open_graph_app_id', $facebook_app_id);
			update_option('_open_graph_facebook_admins', $facebook_admins);

			echo "<div class='updated'><p>".__('Settings updated successfully.', 'open-graph-metabox') ."</p></div>";

		}

		?>
		<div class="wrap">
			<h1><?php _e('Set Open Graph defaults', 'open-graph-metabox'); ?></h1>
			
			<form name="form" method="post">
				<h2><?php _e('Post and page defaults', 'open-graph-metabox'); ?></h2>
				<p><?php _e('Complete this form with the Open Graph defaults you wish to set automatically when posting or editing a post.', 'open-graph-metabox'); ?></p>

				<table class="form-table">

					<tr>
						<th><label for="open_graph_title"><?php _e('Title', 'open-graph-metabox'); ?></label></th>
						<td><input name="open_graph_title" id="open_graph_title" type="text" value="<?php echo $default_title; ?>" class="regular-text" /> (<?php _e('When empty the default title will be used.', 'open-graph-metabox'); ?>)</td>
					</tr>

					<tr>
						<th><label for="open_graph_description"><?php _e('Description', 'open-graph-metabox'); ?></label></th>
						<td><textarea name="open_graph_description" id="open_graph_description" type="text" class="large-text"><?php echo $default_description; ?></textarea> (<?php _e('When empty the default excerpt will be used.', 'open-graph-metabox'); ?>)</td>
					</tr>

					<tr>
						<th><label for="open_graph_image"><?php _e('Image', 'open-graph-metabox'); ?></label></th>
						<td>
							<input name="open_graph_image" id="open_graph_image_value" type="text" value="<?php echo $default_image; ?>" class="normal-text code" />
							<input type="button" class="button open_graph_image" id="open_graph_image" value="<?php _e('Select image', 'open-graph-metabox'); ?>">
						</td>
					</tr>

					<tr>
						<th><label for="open_graph_type"><?php _e('Type', 'open-graph-metabox'); ?></label></th>
						<td>
							<select name="open_graph_type">
								<?php 
									foreach($this->og_types AS $group => $types) { 

										echo '<optgroup label="'. $group .'">';

											if(sizeof($types) > 0 && is_array($types)) {
												foreach($types AS $type => $label) {
													$selected = ($default_type == $type) ? "selected='selected'" : "";
													echo '<option '.$selected.' value="'.$type.'">'. $label .'</option>';
												}
											}

										echo '</optgroup>';

									}
								?>
							</select>
						</td>
					</tr>

					<tr>
						<th><label for="open_graph_app_id"><?php _e('Facebook App ID', 'open-graph-metabox'); ?></label></th>
						<td><input name="open_graph_app_id" id="open_graph_app_id" type="text" value="<?php echo $facebook_app_id; ?>" class="regular-text" /> <?php _e("Let's the Facebook scraper associate the meta data with your app (fb:app_id)", 'open-graph-metabox'); ?></td>
					</tr>

					<tr>
						<th><label for="open_graph_facebook_admins"><?php _e('Facebook Admins', 'open-graph-metabox'); ?></label></th>
						<td><input name="open_graph_facebook_admins" id="open_graph_facebook_admins" type="text" value="<?php echo $facebook_admins; ?>" class="regular-text" /> <?php _e("Let's Facebook associate admins for the website (fb:admins), multiple ID's seperated by , (comma)", 'open-graph-metabox'); ?></td>
					</tr>

				</table>

				<p class="submit">
					<input type="submit" name="save_defaults" id="submit" class="button button-primary" value="<?php _e('Save settings', 'open-graph-metabox'); ?>"  />
				</p>

				<h2><?php _e('Front-page defaults', 'open-graph-metabox'); ?></h2>
				<p><?php _e('These settings are applied to the frontpage of the website.', 'open-graph-metabox'); ?></p>

				<table class="form-table">

					<tr>
						<th><label for="home_open_graph_title"><?php _e('Title', 'open-graph-metabox'); ?></label></th>
						<td><input name="home_open_graph_title" id="home_open_graph_title" type="text" value="<?php echo $home_title; ?>" class="regular-text" /> (<?php _e('When empty the default title will be used.', 'open-graph-metabox'); ?>)</td>
					</tr>

					<tr>
						<th><label for="home_open_graph_description"><?php _e('Description', 'open-graph-metabox'); ?></label></th>
						<td><textarea name="home_open_graph_description" id="home_open_graph_description" type="text" class="large-text"><?php echo $home_description; ?></textarea> (<?php _e('When empty the default excerpt will be used.', 'open-graph-metabox'); ?>)</td>
					</tr>

					<tr>
						<th><label for="home_open_graph_image"><?php _e('Image', 'open-graph-metabox'); ?></label></th>
						<td>
							<input name="home_open_graph_image" id="home_open_graph_image_value" type="text" value="<?php echo $home_image; ?>" class="normal-text code" />
							<input type="button" class="button open_graph_image" id="home_open_graph_image" value="<?php _e('Select image', 'open-graph-metabox'); ?>">
						</td>
					</tr>

					<tr>
						<th><label for="open_graph_type"><?php _e('Type', 'open-graph-metabox'); ?></label></th>
						<td>
							<select name="open_graph_type">
								<?php 
									foreach($this->og_types AS $group => $types) { 

										echo '<optgroup label="'. $group .'">';

											if(sizeof($types) > 0 && is_array($types)) {
												foreach($types AS $type => $label) {
													$selected = ($default_type == $type) ? "selected='selected'" : "";
													echo '<option '.$selected.' value="'.$type.'">'. $label .'</option>';
												}
											}

										echo '</optgroup>';

									}
								?>
							</select>
						</td>
					</tr>

				</table>

				<p class="submit">
					<input type="submit" name="save_defaults" id="submit" class="button button-primary" value="<?php _e('Save settings', 'open-graph-metabox'); ?>"  />
				</p>

			</form>
		</div>
		<?php

	}
  
} // end class

new OpenGraphMetabox();