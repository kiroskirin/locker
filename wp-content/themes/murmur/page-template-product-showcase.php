<?php
/**
 * Template Name: Product Showcase
 *
 * This is the product showcase template.
 *
 */

 get_header(); // Loads the header.php template. ?>

 	<?php if( hybrid_get_setting( 'murmur_site_description_extended' ) ) { ?>
 		<div id="site-description-extended">
 			<?php echo hybrid_get_setting( 'murmur_site_description_extended' ); ?>
 		</div><!-- #site-description-extended -->
 	<?php } ?>

 	<div id="content">

 		<?php do_atomic( 'open_content' ); // murmur_open_content ?>

 		<div class="hfeed">

			<?php

				$loop = new WP_Query( array(
					'order'        => 'DESC',
					'posts_per_page' => hybrid_get_setting( 'murmur_slides_number' ),
					'post_status'  => 'publish',
					'post_type'    => 'post',
				) );
			?>

 			<?php if ( $loop->have_posts() ) : ?>

 				<?php while ( $loop->have_posts() ) : $loop->the_post(); ?>

 					<?php do_atomic( 'before_entry' ); // murmur_before_entry ?>

 					<div id="post-<?php the_ID(); ?>" class="<?php hybrid_entry_class(); ?>">

 						<?php do_atomic( 'open_entry' ); // murmur_open_entry ?>

 						<?php echo apply_atomic_shortcode( 'entry_title', '[entry-title]' ); ?>

 						<div class="entry-content">

 							<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'murmur' ) ); ?>
 							<?php wp_link_pages( array( 'before' => '<p class="page-links">' . __( 'Pages:', 'murmur' ), 'after' => '</p>' ) ); ?>

 							<?php echo apply_atomic_shortcode( 'page_edit', '[entry-edit-link before="<p>" after="</p>"]'); ?>

 						</div><!-- .entry-content -->

 						<?php do_atomic( 'close_entry' ); // murmur_close_entry ?>

 					</div><!-- .hentry -->

 					<?php do_atomic( 'after_entry' ); // murmur_after_entry ?>

 					<?php do_atomic( 'after_singular' ); // murmur_after_singular ?>

 				<?php endwhile; ?>

 			<?php endif; ?>

 		</div><!-- .hfeed -->

 		<?php do_atomic( 'close_content' ); // murmur_close_content ?>

 	</div><!-- #content -->

 	<?php do_atomic( 'after_content' ); // murmur_after_content ?>

 <?php get_footer(); // Loads the footer.php template. ?>
