<?php
/**
 * Murmur functions and definitions
 *
 * @package Murmur
 */

/* Load the Hybrid Core framework. */
require_once( trailingslashit ( get_template_directory() ) . 'library/hybrid.php' );
$theme = new Hybrid(); // Part of the framework.

/* Do theme setup on the 'after_setup_theme' hook. */
add_action( 'after_setup_theme', 'murmur_theme_setup' );

/**
 * Theme setup function.  This function adds support for theme features and defines the default theme
 * actions and filters.
 *
 * @since 0.1.0
 */
function murmur_theme_setup() {

	/* Get action/filter hook prefix */
	$prefix = hybrid_get_prefix(); // Part of the framework, cannot be changed or prefixed.

	/* Add theme settings */
	if ( is_admin() ) {
	    locate_template( 'functions/admin.php', true );
	}

	/* Add framework menus and sidebars */
	add_theme_support( 'hybrid-core-menus', array( 'primary', 'subsidiary' ) );
	add_theme_support( 'hybrid-core-sidebars', array(
		'primary',
		'subsidiary',
		'after-singular'
	) );

	/* Register additional widget areas */
	add_action( 'widgets_init', 'murmur_register_sidebars', 11 );

	/* Add framework features */
	add_theme_support( 'hybrid-core-widgets' );
	add_theme_support( 'hybrid-core-shortcodes' );
	add_theme_support( 'hybrid-core-template-hierarchy' );
	add_theme_support( 'hybrid-core-theme-settings', array( 'footer' ) );

	/* Add framework extensions and other features */
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'cleaner-gallery' );
	add_theme_support( 'get-the-image' );
	add_theme_support( 'loop-pagination' );
	add_theme_support( 'post-stylesheets' );
	add_theme_support( 'theme-layouts', array( '1c', '2c-r' ) );

	/* Load resources into the theme */
	add_action( 'wp_enqueue_scripts', 'murmur_resources' );

	/* Load theme fonts */
	add_action( 'wp_enqueue_scripts', 'murmur_fonts' );

	/* Modify excerpt more */
	add_filter( 'excerpt_more', 'murmur_new_excerpt_more' );

	/* Modify excerpt length */
	add_filter( 'excerpt_length', 'murmur_excerpt_length' );

	/* Register new image sizes. */
	add_action( 'init', 'murmur_register_image_sizes' );

	/* Set content width */
	hybrid_set_content_width( 560 );

	/* Embed width/height defaults */
	add_filter( 'embed_defaults', 'murmur_embed_defaults' );

	/* Edit post editor meta boxes. */
	add_action( 'do_meta_boxes', 'murmur_edit_meta_boxes' );

	/* Conditions to disable sidebars */
	add_filter( 'sidebars_widgets', 'murmur_disable_sidebars' );

	/* Modifies the main loop for selected templates */
	add_action( 'pre_get_posts', 'murmur_loops', 1 );

	/* Append taxonomy terms to post class */
	add_filter( 'post_class', 'murmur_post_class', 10, 3 );

	/* Print scripts on selected templates */
	add_action( 'wp_footer', 'murmur_footer_scripts' );

}

/**
 * Loads the theme scripts.
 *
 * @since 0.1
 */
function murmur_resources() {

	wp_enqueue_script( 'murmur-scripts', trailingslashit ( get_template_directory_uri() ) . 'js/murmur.js', array( 'jquery' ), '20120831', true );

	if ( post_type_exists( 'project' ) ) {

		if ( is_page_template( 'page-template-portfolio-showcase.php' ) || is_singular( 'project' ) ) {

			if ( !wp_script_is( 'sliders_flexslider', 'registered' ) ) {

				wp_enqueue_script( 'sliders_flexslider', trailingslashit ( get_template_directory_uri() ) . 'js/flexslider.js', array( 'jquery' ), 1.8, true );

				wp_enqueue_style( 'sliders_style', trailingslashit ( get_template_directory_uri() ) . 'css/sliders.css', false, 0.1, 'all' );

			} elseif ( wp_script_is( 'sliders_flexslider', 'registered' ) && !wp_script_is( 'sliders_flexslider', 'queue' ) ) {

				wp_enqueue_script( 'sliders_flexslider' );
				wp_enqueue_style( 'sliders_style' );
			}

		}

		if ( is_page_template( 'page-template-portfolio-showcase.php' ) ) {

			wp_enqueue_style( 'murmur_slider_portfolio_showcase', trailingslashit ( get_template_directory_uri() ) . 'css/murmur-slider-portfolio-showcase.css', false, '20120914', 'all' );

		}

		if ( is_singular( 'project' ) ) {

			wp_enqueue_style( 'murmur_slider_project', trailingslashit ( get_template_directory_uri() ) . 'css/murmur-slider-project.css', false, '20120914', 'all' );

		}

	}

}

/**
 * Loads theme fonts
 *
 * @since 0.2.0
 */
function murmur_fonts() {

	$font_uri = '//fonts.googleapis.com/css?family=Lato:400,700,400italic,700italic';
	wp_enqueue_style( 'sonorous-fonts', $font_uri, array(), null, 'screen' );

}

/**
 * Filters the excerpt more.
 *
 * @since 0.1
 */

function murmur_new_excerpt_more( $more ) {
	return '&#133;';
}

/**
 * Filters the excerpt length.
 *
 * @since 0.1
 */

function murmur_excerpt_length( $length ) {
	return 50;
}

/**
 * Registers additional image sizes.
 *
 * @since 0.1.0
 */
function murmur_register_image_sizes() {

	add_image_size( 'murmur-medium', 280, 169, true );
	add_image_size( 'murmur-sticky', 600, 369, true );

	if ( function_exists( 'dp_portfolio_setup' ) ) {
		add_image_size( 'dp-portfolio-wide', 920, 348, true );
		add_image_size( 'dp-portfolio-large', 620, 99999 );
		add_image_size( 'dp-portfolio-small', 215, 160, true );
	}

}

/**
 * Overwrites the default widths for embeds.  This is especially useful for making sure videos properly expand the full width on video pages.  This function overwrites what the $content_width variable handles with context-based widths.
 *
 * @since 0.1
 */
function murmur_embed_defaults( $args ) {

	$args['width'] = 560;

	if ( current_theme_supports( 'theme-layouts' ) ) {

		$layout = theme_layouts_get_layout();

		if ( 'layout-1c' == $layout ) {
			$args['width'] = 840;
		}

	}

	return $args;
}

/**
 * Disables sidebars based on templates.
 *
 * @since 0.1.0
 */
function murmur_disable_sidebars( $sidebars_widgets ) {

	global $wp_query;

	if( current_theme_supports( 'theme-layouts' ) && !is_admin() ) {

		if ( is_404() || is_archive() || is_home() || is_page_template( 'page-template-portfolio-showcase.php' ) || is_search() || is_singular( 'project' ) || 'layout-1c' == theme_layouts_get_layout() ) {
			$sidebars_widgets['primary'] = false;
		}

		if( is_404() || is_post_type_archive( 'team' ) ) {
			$sidebars_widgets['subsidiary'] = false;
			$sidebars_widgets['subsidiary-4c'] = false;
			$sidebars_widgets['subsidiary-5c'] = false;
		}

	}


	return $sidebars_widgets;
}

/**
 * Edit post editor meta boxes.
 *
 * @since 0.1
 */
function murmur_edit_meta_boxes() {
	/* Remove metaboxes */
	remove_meta_box( 'theme-layouts-post-meta-box', 'project', 'side' );
	remove_meta_box( 'theme-layouts-post-meta-box', 'team', 'side' );
}

/**
 * Modifies the loop for selected templates
 *
 * @since 0.1
 */

function murmur_loops( $query ) {

	if ( is_post_type_archive( 'project' ) ) {
		$query->query_vars['posts_per_page'] = 12;
		return;
	}

	if ( is_post_type_archive( 'team' ) ) {
		$query->query_vars['posts_per_page'] = 6;
		return;
	}

}

/**
 * Append taxonomy terms to post class.
 *
 * @since 0.1.0
 * @since 2010-07-10
 * @author Michael Fields
 * @link http://wordpress.mfields.org/2010/append-a-posts-taxonomy-terms-to-post-class/
 */
function murmur_post_class( $classes, $class, $ID ) {

	$taxonomy = 'dp_portfolio_role';

	$terms = get_the_terms( (int) $ID, $taxonomy );

	if( !empty( $terms ) ) {

		foreach( (array) $terms as $order => $term ) {
			if( !in_array( $term->slug, $classes ) )
				$classes[] = $term->slug;
		}
	}

	return $classes;
}

/**
 * Load scripts on selected templates
 *
 * @since 0.1
 */

function murmur_footer_scripts() {

	if ( is_page_template( 'page-template-portfolio-showcase.php' ) || is_singular( 'project' ) ) : ?>

		<script type='text/javascript'>
			var $j = jQuery.noConflict();

			$j(document).ready( function() {
				$j( '.flexslider' ).flexslider();

				// custom
				// $j( '.slide a').prop("href","#");
			});
		</script>

	<?php endif;

}

/**
 * Display attached images of the project post type if the DevPress Portfolio plugin is installed.
 *
 * @since 0.1.0
 */
function murmur_project_previews() {

	if ( !function_exists( 'dp_portfolio_setup' ) )
		return '';

	$children = array(
		'post_parent' => get_the_ID(),
		'post_status' => 'inherit',
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'order' => 'ASC',
		'orderby' => 'menu_order ID',
		'include' => '',
		'exclude' => '',
		'numberposts' => -1,
		'offset' => ''
	);

	/* Get image attachments. If none, return. */
	$attachments = get_children( $children );

	if ( empty( $attachments ) )
		return '';

	$out .= '<divzz id="dppp">';
	$out .= '<div class="sliders flexslider">';
	$out .= '<ul class="slides">';

	/* Loop through each attachment. */
	foreach ( $attachments as $id => $attachment ) {
		$img_url = wp_get_attachment_url($id);
		$out .= '<li><a href="' . $img_url .'" rel="lightbox">';
		$out .= wp_get_attachment_image( $id, 'dp-portfolio-large' );
		$out .= '</a></li>';
	}

	$out .= '</ul></div></div>';

	echo $out;
}

/**
 * Register additional sidebars.
 *
 * @since 0.1.0
 */
function murmur_register_sidebars() {

	$subsidiary_4 = array(
		'id' => 'subsidiary-4c',
		'name' => _x( 'Subsidiary 4 Columns', 'sidebar', 'murmur' ),
		'description' => __( 'A 4-column widget area loaded before the footer of the site.', 'murmur' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);

	$subsidiary_5 = array(
		'id' => 'subsidiary-5c',
		'name' => _x( 'Subsidiary 5 Columns', 'sidebar', 'murmur' ),
		'description' => __( 'A 5-column widget area loaded before the footer of the site.', 'murmur' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s widget-%2$s"><div class="widget-wrap widget-inside">',
		'after_widget' => '</div></div>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>'
	);

	register_sidebar( $subsidiary_4 );
	register_sidebar( $subsidiary_5 );

}

/**
 * Theme updater.
 *
 * @since 0.1.5
 */
function devpress_theme_updater() {
	require( get_template_directory() . '/library/updater/theme-updater.php' );
}
add_action( 'after_setup_theme', 'devpress_theme_updater' );
