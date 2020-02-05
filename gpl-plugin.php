<?php

/*
Plugin Name: Site Plugin for guidedprojectlearning.com
Description: Custom site specific widgets and shortcodes
Author: David Wadsworth
*/

function searchandsort_shortcodes_init(){
	add_shortcode('searchandsort', 'searchandsort_shortcode');	
}
add_action('init', 'searchandsort_shortcodes_init');

function gpl_widgets_init(){
	register_widget('gpl_custom_sidebar_widget');	
	register_widget('gpl_quote_and_link_widget');
	register_widget('gpl_featured_single_page_widget');
}
	

add_action( 'widgets_init', 'gpl_widgets_init' );


/*************************************************************
 * Search and Sort Shortcode
 *************************************************************/

function searchandsort_shortcode( $atts = [], $content = null, $tag = ' ' )
{

		$atts = array_change_key_case((array)$atts, CASE_LOWER);

		$searchandsort_atts = shortcode_atts([
												'title'  => '',
												'text'   => '',
												'number' => 10,
												'template'  => 'page-templates/project-description.php',
											], $atts, $tag);


		global $post;


		$title             = $searchandsort_atts['title'];
		$text              = $searchandsort_atts['text'];
		$number            = $searchandsort_atts['number'];

		$page_array = array();
		$pages      = get_pages();

		foreach ( $pages as $page ) {
			$page_id       = $page->ID;
			$template_name = get_post_meta( $page_id, '_wp_page_template', true );
			if ( $template_name ==  $searchandsort_atts['template'] ) {
				array_push( $page_array, $page_id );
			}
		}
		$get_featured_posts = new WP_Query( array(
			'posts_per_page' => $number,
			'post_type'      => array( 'page' ),
			'post__in'       => $page_array,
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'DESC' )
		) );
		?>

		<div>
			<div>
				<div class="tg-container">
					<div class="section-title-wrapper">
						<?php
						if ( ! empty( $title ) ) {
							echo $before_title . esc_html( $title ) . $after_title;
						}
						if ( ! empty( $text ) ) { ?> <h4
								class="sub-title"> <?php echo wp_kses_post( $text ); ?> </h4> <?php } ?>
					</div>
				</div>
				<div class="gpl-20px-spacer"></div>

				<?php if ( ! empty( $page_array ) ) : ?>
					<div class="Portfolio-content-wrapper clearfix">
						<?php
						while ( $get_featured_posts->have_posts() ) : $get_featured_posts->the_post(); ?>

							<div class="portfolio-images-wrapper">
								<?php
								// Get the full URI of featured image
								$image_popup_id  = get_post_thumbnail_id();
								$image_popup_url = wp_get_attachment_url( $image_popup_id ); ?>

								<div class="port-img">
									<?php if ( has_post_thumbnail() ) {
										the_post_thumbnail( 'spacious-portfolio-image' );

									} else {
										$image_popup_url = get_template_directory_uri() . '/images/placeholder.jpg';
										echo '<img src="' . esc_url( $image_popup_url ) . '">';
									} ?>
								</div>

								<a href="<?php the_permalink(); ?>" class="portfolio-hover">

									<div class="port-title-wrapper">
										<h4 class="port-title"><div
													title="<?php the_title_attribute(); ?>"><?php the_title(); ?></div>
										</h4>
									</div>
								</a>
							</div>
						<?php endwhile; ?>
					</div><!-- .Portfolio-content-wrapper -->
					<?php
					// Reset Post Data
					wp_reset_query();
				endif; ?>
			</div>
		</div><!-- .section-wrapper -->
	<?php
}

/*************************************************************
 * Custom Sidebar Widget
 *************************************************************/

class gpl_custom_sidebar_widget extends WP_Widget 
{

   function __construct() {
      $widget_ops = array( 'classname' => 'gpl_custom_sidebar_widget', 'description' => __( 'Display content in sidebar', 'spacious') );
      $control_ops = array( 'width' => 200, 'height' =>250 );
      parent::__construct( false,$name= __( 'GPL: Custom Siderbar', 'spacious' ), $widget_ops);
   }

   function form( $instance ) {
      $defaults[ 'title' ] = '';
      $defaults[ 'title_page' ] = '';
      $defaults[ 'text' ] = '';
      $defaults[ 'number' ] = 4;
      $defaults[ 'sort_theme' ] = 'page-templates/project-description.php';
      $defaults[ 'show_section_1' ] = true;

      $defaults[ 'button_page' ] = '';
      $defaults[ 'button_text' ] = 'Book Now';
      $defaults[ 'show_section_2' ] = true;

      $defaults[ 'link_page_id_1' ] = ''; 
      $defaults[ 'link_page_id_2' ] = ''; 
      $defaults[ 'link_page_id_3' ] = ''; 
      $defaults[ 'show_section_3' ] = '';

      $instance = wp_parse_args( (array) $instance, $defaults );

      $title = esc_attr( $instance[ 'title' ] );
      $title_page = absint( $instance[ 'title_page' ] );
      $text = esc_textarea( $instance[ 'text' ] );
      $number = absint( $instance[ 'number' ] ); 
      $sortTheme = esc_attr( $instance[ 'sort_theme' ] );
      $show_section_1 = isset( $instance['show_section_1'] ) ? (bool) $instance['show_section_1'] : true;

      $button_page = absint( $instance[ 'button_page' ] );
      $button_text = esc_attr( $instance[ 'button_text' ] );
      $show_section_1 = isset( $instance['show_section_2'] ) ? (bool) $instance['show_section_2'] : true;

      $link_page_id_1 = absint( $instance[ 'link_page_id_1' ] );
      $link_page_id_2 = absint( $instance[ 'link_page_id_2' ] );
      $link_page_id_3 = absint( $instance[ 'link_page_id_3' ] );
      $show_section_3 = isset( $instance['show_section_3'] ) ? (bool) $instance['show_section_3'] : true;
      ?>

      <h3>Section 1</h3>

      <p>
         <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title: ', 'spacious' ); ?></label>
         <input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
      </p>

      <p>
	  	  <label for="<?php echo $this->get_field_id( 'title_page' ); ?>"><?php _e( 'Title Page Link: ', 'spacious' ); ?></label>
		  <?php wp_dropdown_pages( array( 'show_option_none' => ' ', 'name' => $this->get_field_name( 'title_page' ), 'selected'   => $instance[ 'title_page' ] ) ); ?>
	  </p>

       <p>
         <label for="<?php echo $this->get_field_id( 'text' ); ?>"><?php _e( 'Description: ', 'spacious' ); ?></label>
         <input id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" type="text" value="<?php echo $text; ?>" />
      </p>

      <p>
         <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of Pages to Display: ', 'spacious' ); ?></label>
         <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" />
      </p>

      <p>
      	<label for="<?php echo $this->get_field_id( 'sort_theme' ); ?>"><?php _e( 'Sorting Template: ', 'spacious' ); ?></label>
      	<select id="<?php echo $this->get_field_id('sort_theme'); ?>" name="<?php echo $this->get_field_name('sort_theme'); ?>" ><?php page_template_dropdown( $instance[ 'sort_theme' ]); ?></select>
      </p>

      <p><?php _e( 'Note: Create the page and select a template to be displayed in the sidebar', 'spacious' ); ?></p>

      <p>
      	<label for="<?php echo $this->get_field_id( 'show_section_1' ); ?>">Show Section 1: </label>
      	<input id="<?php echo $this->get_field_id('show_section_1'); ?>" name="<?php echo $this->get_field_name('show_section_1'); ?>" type="checkbox" <?php checked(isset($instance['show_section_1']) ? (bool) $instance['show_section_1'] : true); ?> />&nbsp;
      </p>

      <h3>Section 2</h3>

      <p>
	  	  <label for="<?php echo $this->get_field_id( 'button_page' ); ?>"><?php _e( 'Button Page: ', 'spacious' ); ?></label>
		  <?php wp_dropdown_pages( array( 'show_option_none' => ' ', 'name' => $this->get_field_name( 'button_page' ), 'selected'   => $instance[ 'button_page' ] ) ); ?>
	  </p>

	  <p>
         <label for="<?php echo $this->get_field_id( 'button_text' ); ?>"><?php _e( 'Button Text:', 'spacious' ); ?></label>
         <input id="<?php echo $this->get_field_id( 'button_text' ); ?>" name="<?php echo $this->get_field_name( 'button_text' ); ?>" type="text" value="<?php echo $button_text; ?>" />
      </p>
      <p>
      	<label for="<?php echo $this->get_field_id( 'show_section_2' ); ?>">Show Section 2: </label>
      	<input id="<?php echo $this->get_field_id('show_section_2'); ?>" name="<?php echo $this->get_field_name('show_section_2'); ?>" type="checkbox" <?php checked(isset($instance['show_section_2']) ? (bool) $instance['show_section_2'] : true); ?> />&nbsp;
      </p>

      <h3>Section 3</h3>
      
      <p>
	  	  <label for="<?php echo $this->get_field_id( 'link_page_id_1' ); ?>"><?php _e( 'Page Link 1: ', 'spacious' ); ?></label>
		  <?php wp_dropdown_pages( array( 'show_option_none' => ' ', 'name' => $this->get_field_name( 'link_page_id_1' ), 'selected'   => $instance[ 'link_page_id_1' ] ) ); ?>
	  </p>
	  <p>
	  	  <label for="<?php echo $this->get_field_id( 'link_page_id_2' ); ?>"><?php _e( 'Page Link 2: ', 'spacious' ); ?></label>
		  <?php wp_dropdown_pages( array( 'show_option_none' => ' ', 'name' => $this->get_field_name( 'link_page_id_2' ), 'selected'   => $instance[ 'link_page_id_2' ] ) ); ?>
	  </p>
	  <p>
	  	  <label for="<?php echo $this->get_field_id( 'link_page_id_3' ); ?>"><?php _e( 'Page Link 3: ', 'spacious' ); ?></label>
		  <?php wp_dropdown_pages( array( 'show_option_none' => ' ', 'name' => $this->get_field_name( 'link_page_id_3' ), 'selected'   => $instance[ 'link_page_id_3' ] ) ); ?>
	  </p>

      <p>
      	<label for="<?php echo $this->get_field_id( 'show_section_3' ); ?>">Show Section 3: </label>
      	<input id="<?php echo $this->get_field_id('show_section_3'); ?>" name="<?php echo $this->get_field_name('show_section_3'); ?>" type="checkbox" <?php checked(isset($instance['show_section_3']) ? (bool) $instance['show_section_3'] : true); ?> />&nbsp;
      </p>
      <?php
   }

   function update( $new_instance, $old_instance ) {
      $instance = $old_instance;

      $instance[ 'title' ] = sanitize_text_field( $new_instance[ 'title' ] );
      $instance[ 'title_page' ] = absint( $new_instance[ 'title_page' ] );
      if ( current_user_can('unfiltered_html') )
         $instance[ 'text' ] =  $new_instance[ 'text' ];
      else
         $instance[ 'text' ] = stripslashes( wp_filter_post_kses( addslashes($new_instance[ 'text' ]) ) ); // wp_filter_post_kses() expects slashed
      $instance[ 'number' ] = absint( $new_instance[ 'number' ] );
      $instance[ 'sort_theme' ] = $new_instance[ 'sort_theme' ];
	  $instance[ 'show_section_1' ] = (bool) $new_instance['show_section_1'];

      $instance[ 'button_page' ] = absint( $new_instance[ 'button_page' ] );
      $instance[ 'button_text' ] = sanitize_text_field( $new_instance[ 'button_text' ] );
      $instance[ 'show_section_2' ] = (bool) $new_instance['show_section_2'];

      $instance[ 'link_page_id_1' ] = absint( $new_instance[ 'link_page_id_1' ] );
      $instance[ 'link_page_id_2' ] = absint( $new_instance[ 'link_page_id_2' ] );
      $instance[ 'link_page_id_3' ] = absint( $new_instance[ 'link_page_id_3' ] );
      $instance[ 'show_section_3' ] = (bool) $new_instance['show_section_3'];

      return $instance;
   }

  	function widget( $args, $instance ) {
  		extract( $args );
		extract( $instance );

		global $post;

		$title             = isset( $instance['title'] ) ? $instance['title'] : '';
		$title_page        = isset( $instance['title_page'] ) ? $instance['title_page'] : '';
		$text              = isset( $instance['text'] ) ? $instance['text'] : '';
		$number            = empty( $instance['number'] ) ? 4 : $instance['number'];
		$sort_theme        = isset( $instance['sort_theme'] ) ? $instance['sort_theme'] : '';
		$show_section_1    = $instance[ 'show_section_1' ] ? 'true' : 'false';

		$button_page       = isset( $instance['button_page'] ) ? $instance['button_page'] : '';
		$button_text       = isset( $instance['button_text'] ) ? $instance['button_text'] : '';
		$show_section_2    = $instance[ 'show_section_2' ] ? 'true' : 'false';

		$link_page_id_1        = isset( $instance['link_page_id_1'] ) ? $instance['link_page_id_1'] : '';
		$link_page_id_2        = isset( $instance['link_page_id_2'] ) ? $instance['link_page_id_2'] : '';
		$link_page_id_3        = isset( $instance['link_page_id_3'] ) ? $instance['link_page_id_3'] : '';
		$show_section_3    = $instance[ 'show_section_3' ] ? 'true' : 'false';


		$page_array        = array();
		$pages             = get_pages();
		$current_page_ID   = get_the_ID();

		foreach ( $pages as $page ) {
			$page_id       = $page->ID;
			$template_name = get_post_meta( $page_id, '_wp_page_template', true );
			if ( $template_name == $sort_theme ) {
				array_push( $page_array, $page_id );
			} 
		}


		$get_featured_posts = new WP_Query( array(
			'posts_per_page' => $number,
			'post_type'      => array( 'page' ),
			'post__in'       => $page_array,
			'orderby'        => 'rand',
			'post__not_in'   => array( $current_page_ID )
		) );
		?>
		<div>
			<div>
			<?php if( $show_section_1 == 'true' && $current_page_ID != $title_page) : ?>
				<div class="tg-container" id="gpl-sidebar-title">
					<a href="<?php echo get_page_link($title_page)?>">
						<?php
						if ( ! empty( $title ) ) {
							echo $before_title . esc_html( $title ) . $after_title;
						}
						if ( ! empty( $text ) ) { ?> <h4
								class="sub-title"> <?php echo wp_kses_post( $text ); ?> </h4> <?php } ?>
					</a>
				</div>

				<?php if ( ! empty( $page_array ) ) : ?>
					<div class="Portfolio-content-wrapper clearfix">
						

						<ul>

						<?php
						while ( $get_featured_posts->have_posts() ) : $get_featured_posts->the_post(); 

							$thumb_id = get_post_thumbnail_id(); // Get the featured image id.
							$img_url  = wp_get_attachment_url( $thumb_id ); // Get img URL.

							$image    = rpwe_resize($img_url, 45, 45, true );

							?>
								<li class="clearfix gpl-4px-spacer">

									<a href=" <?php the_permalink(); ?>"  rel="bookmark">
										<img class="rpwe-alignleft rpwe-thumb" src=" <?php echo $image; ?> " alt=" <?php get_the_title(); ?> ">
									</a>

									<div class="gpl-title">
										<a href=" <?php the_permalink(); ?>" title=" <?php the_title_attribute(); ?> " rel="bookmark" class="gpl-sidebar-title"> 
											<?php echo get_the_title();  ?>
										</a>
									</div>

									<div class="gpl-excerpt">
										<?php echo wp_trim_words( apply_filters( 'rpwe_excerpt', get_the_excerpt() ), 10,  '&hellip;' ); ?>
										<a href=" <?php the_permalink(); ?> " class="more-link" style="font-size: 10px"> 
											<?php echo __( 'Read More &raquo;', 'recent-posts-widget-extended' ) ?>		
										</a>
									</div>
									<div class="gpl-sidebar-spacer"></div>
								</li>

						<?php endwhile; ?>

						</ul>

					</div><!-- .Portfolio-content-wrapper -->
					<?php
					// Reset Post Data
					wp_reset_query();
				endif; 
			endif;
			if($show_section_2 == 'true') : ?>
				<a class="gpl-sidebar-bttn" href="<?php echo get_page_link($button_page)?>" ><?php echo esc_html($button_text); ?></a>
				<div class="gpl-sidebar-bttn-spacer"></div>
			<?php endif; 
			if($show_section_3 == 'true') : ?>
				<?php 
				if( $link_page_id_1 != $current_page_ID && $link_page_id_1 != '0' ) : ?>

					<div class= "gpl-sidebar-extra-container">
						<a class="gpl-sidebar-extra-link" href="<?php the_permalink($link_page_id_1); ?>" >
							<div class="gpl-sidebar-extra-img">
								<img src="<?php echo get_the_post_thumbnail_url($link_page_id_1, 'thumbnail1' ); ?>"/>
							</div>
							<div class="gpl-sidebar-extra-title-container">
								<div class="gpl-sidebar-extra-title">
									<?php echo get_the_title($link_page_id_1); ?>
								</div>
							</div>
						</a>
					</div>

				<?php endif; 
				if( $link_page_id_2 != $current_page_ID && $link_page_id_2 != '0' ) : ?>

					<div class= "gpl-sidebar-extra-container">

						<a class="gpl-sidebar-extra-link" href="<?php the_permalink($link_page_id_2); ?>" >

							<div class="gpl-sidebar-extra-img">
								<img src="<?php echo get_the_post_thumbnail_url($link_page_id_2, 'thumbnail2' ); ?>"/>
							</div>

							<div class="gpl-sidebar-extra-title-container">
								<div class="gpl-sidebar-extra-title">
									<?php echo get_the_title($link_page_id_2); ?>
								</div>
							</div>
						</a>
					</div>

				<?php endif; 
				if( $link_page_id_3 != $current_page_ID && $link_page_id_3 != '0' ) : ?>

					<div class= "gpl-sidebar-extra-container">

						<a class="gpl-sidebar-extra-link" href="<?php the_permalink($link_page_id_3); ?>" >

							<div class="gpl-sidebar-extra-img">
								<img src="<?php echo get_the_post_thumbnail_url($link_page_id_3, 'thumbnail3' ); ?>"/>
							</div>

							<div class="gpl-sidebar-extra-title-container">
								<div class="gpl-sidebar-extra-title">
									<?php echo get_the_title($link_page_id_3); ?>
								</div>
							</div>
						</a>
					</div>			
				<?php endif; 
				?>
			<?php endif;?>
			</div>
		</div><!-- .section-wrapper -->
		<?php
	}
}

/*************************************************************
 * Quote and Link Widget
 *************************************************************/

class gpl_quote_and_link_widget extends WP_Widget
{
	function __construct() {
      $widget_ops = array( 'classname' => 'gpl_quote_and_link', 'description' => __( 'Display a quote and button to page', 'spacious') );
      $control_ops = array( 'width' => 200, 'height' =>250 );
      parent::__construct( false,$name= __( 'GPL: Quote and Link', 'spacious' ), $widget_ops);
   }

	function form( $instance ) {
		$spacious_defaults[ 'text_main' ]       = '';
		$spacious_defaults[ 'text_additional' ] = '';
		$spacious_defaults[ 'button_text' ]     = '';
		$spacious_defaults[ 'button_url' ]      = '';
		$instance                               = wp_parse_args( (array) $instance, $spacious_defaults );
		$text_main                              = esc_textarea( $instance[ 'text_main' ] );
		$text_additional                        = esc_textarea( $instance[ 'text_additional' ] );
		$button_text                            = esc_attr( $instance[ 'button_text' ] );
		$button_url                             = esc_url( $instance[ 'button_url' ] );
		?>


		<?php _e( 'Quote Text', 'spacious' ); ?>
		<textarea class="widefat" rows="3" cols="20" id="<?php echo $this->get_field_id( 'text_main' ); ?>" name="<?php echo $this->get_field_name( 'text_main' ); ?>"><?php echo $text_main; ?></textarea>
		<p>
			<label for="<?php echo $this->get_field_id( 'button_text' ); ?>"><?php _e( 'Button Text:', 'spacious' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'button_text' ); ?>" name="<?php echo $this->get_field_name( 'button_text' ); ?>" type="text" value="<?php echo $button_text; ?>"/>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'button_url' ); ?>"><?php _e( 'Button Redirect Link:', 'spacious' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'button_url' ); ?>" name="<?php echo $this->get_field_name( 'button_url' ); ?>" type="text" value="<?php echo $button_url; ?>"/>
		</p>
		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance[ 'text_main' ] = $new_instance[ 'text_main' ];
		} else {
			$instance[ 'text_main' ] = stripslashes( wp_filter_post_kses( addslashes( $new_instance[ 'text_main' ] ) ) );
		} // wp_filter_post_kses() expects slashed

		$instance[ 'button_text' ] = strip_tags( $new_instance[ 'button_text' ] );
		$instance[ 'button_url' ]  = esc_url_raw( $new_instance[ 'button_url' ] );

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );

		global $post;
		$text_main       = empty( $instance[ 'text_main' ] ) ? '' : $instance[ 'text_main' ];
		$text_additional = empty( $instance[ 'text_additional' ] ) ? '' : $instance[ 'text_additional' ];
		$button_text     = isset( $instance[ 'button_text' ] ) ? $instance[ 'button_text' ] : '';
		$button_url      = isset( $instance[ 'button_url' ] ) ? $instance[ 'button_url' ] : '#';

		echo $before_widget;
		?>
		<div class="gpl-quote-container clearfix">
			<div class="gpl-quote-text-container">
				<?php
				if ( ! empty( $text_main ) ) {
					?>
					<h3><?php echo esc_html( $text_main ); ?></h3>
					<?php
				}
				?>
			</div>
			<?php
			if ( ! empty( $button_text ) ) {
				?>
				<a class="gpl-quote-button" href="<?php echo $button_url; ?>" title="<?php echo esc_attr( $button_text ); ?>"><?php echo esc_html( $button_text ); ?></a>
				<?php
			}
			?>
		</div>
		<?php
		echo $after_widget;
	}
}



/*************************************************************
 * Single Page Widget
 *************************************************************/

class gpl_featured_single_page_widget extends WP_Widget {
	function __construct() {
		$widget_ops  = array(
			'classname'                   => 'gpl_featured_single_post',
			'description'                 => __( 'Display Featured Single Page', 'spacious' ),
			'customize_selective_refresh' => true,
		);
		$control_ops = array( 'width' => 200, 'height' => 250 );
		parent::__construct( false, $name = 'GPL: Featured Single Page', $widget_ops, $control_ops );
	}

	function form( $instance ) {
		$instance              = wp_parse_args( (array) $instance, array( 'page_id' => '', 'title' => '', 'disable_feature_image' => 0, 'image_position' => 'above', 'body_text' => '' ) );
		$title                 = esc_attr( $instance[ 'title' ] );
		$page_id               = absint( $instance[ 'page_id' ] );
		$disable_feature_image = $instance[ 'disable_feature_image' ] ? 'checked="checked"' : '';
		$image_position        = $instance[ 'image_position' ];
		$body_text 			   = esc_textarea( $instance[ 'text_main' ] );
		_e( 'Suitable for Home Top Sidebar, Home Bottom Left Sidebar and Side Sidbar.', 'spacious' );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'spacious' ); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>"/>
		</p>
		<p><?php _e( 'Displays the title of the Page if title input is empty.', 'spacious' ); ?></p>
		<p>
			<label for="<?php echo $this->get_field_id( 'body_text' ); ?>"><?php _e( 'Description:', 'spacious' ); ?></label>
			<textarea class="widefat" rows="3" cols="20" id="<?php echo $this->get_field_id( 'body_text' ); ?>" name="<?php echo $this->get_field_name( 'body_text' ); ?>"><?php echo $text_main; ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'page_id' ); ?>"><?php _e( 'Page', 'spacious' ); ?>:</label>
			<?php wp_dropdown_pages( array( 'name' => $this->get_field_name( 'page_id' ), 'selected' => $instance[ 'page_id' ] ) ); ?>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php echo $disable_feature_image; ?> id="<?php echo $this->get_field_id( 'disable_feature_image' ); ?>" name="<?php echo $this->get_field_name( 'disable_feature_image' ); ?>"/>
			<label for="<?php echo $this->get_field_id( 'disable_feature_image' ); ?>"><?php _e( 'Remove Featured image', 'spacious' ); ?></label>
		</p>

		<?php if ( $image_position == 'above' ) { ?>
			<p>
				<input type="radio" id="<?php echo $this->get_field_id( 'image_position' ); ?>" name="<?php echo $this->get_field_name( 'image_position' ); ?>" value="above" style="" checked/><?php _e( 'Show Image Before Title', 'spacious' ); ?>
				<br/>
				<input type="radio" id="<?php echo $this->get_field_id( 'image_position' ); ?>" name="<?php echo $this->get_field_name( 'image_position' ); ?>" value="below" style=""/><?php _e( 'Show Image After Title', 'spacious' ); ?>
				<br/>
			</p>
		<?php } else { ?>
			<p>
				<input type="radio" id="<?php echo $this->get_field_id( 'image_position' ); ?>" name="<?php echo $this->get_field_name( 'image_position' ); ?>" value="above" style=""/><?php _e( 'Show Image Before Title', 'spacious' ); ?>
				<br/>
				<input type="radio" id="<?php echo $this->get_field_id( 'image_position' ); ?>" name="<?php echo $this->get_field_name( 'image_position' ); ?>" value="below" style="" checked/><?php _e( 'Show Image After Title', 'spacious' ); ?>
				<br/>
			</p>
		<?php } ?>

		<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance                            = $old_instance;
		$instance[ 'title' ]                 = strip_tags( $new_instance[ 'title' ] );
		$instance[ 'page_id' ]               = absint( $new_instance[ 'page_id' ] );
		$instance[ 'disable_feature_image' ] = isset( $new_instance[ 'disable_feature_image' ] ) ? 1 : 0;
		$instance[ 'image_position' ]        = $new_instance[ 'image_position' ];
		if ( current_user_can( 'unfiltered_html' ) ) {
			$instance[ 'body_text' ] = $new_instance[ 'body_text' ];
		} else {
			$instance[ 'body_text' ] = stripslashes( wp_filter_post_kses( addslashes( $new_instance[ 'body_text' ] ) ) );
		} // wp_filter_post_kses() expects slashed

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		extract( $instance );
		global $post;
		$title                 = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] : '';
		$page_id               = isset( $instance[ 'page_id' ] ) ? $instance[ 'page_id' ] : '';
		$disable_feature_image = ! empty( $instance[ 'disable_feature_image' ] ) ? 'true' : 'false';
		$image_position        = isset( $instance[ 'image_position' ] ) ? $instance[ 'image_position' ] : 'above';
		$body_text    		   = empty( $instance[ 'body_text' ] ) ? '' : $instance[ 'body_text' ];

		if ( $page_id ) {
			$the_query = new WP_Query( 'page_id=' . $page_id );
			while ( $the_query->have_posts() ):$the_query->the_post();
				$page_name = get_the_title();

				$output = $before_widget;
				if ( $image_position == "below" ) {
					if ( $title ): $output .= $before_title . '<a href="' . get_permalink() . '" title="' . $title . '">' . $title . '</a>' . $after_title;
					else: $output .= $before_title . '<a href="' . get_permalink() . '" title="' . $page_name . '">' . $page_name . '</a>' . $after_title;
					endif;

				}
				if ( has_post_thumbnail() && $disable_feature_image != "true" ) {
					$output .= '<div class="service-image">' . get_the_post_thumbnail( $post->ID, 'featured', array( 'title' => esc_attr( $page_name ), 'alt' => esc_attr( $page_name ) ) ) . '</div>';
				}

				if ( $image_position == "above" ) {
					if ( $title ): $output .= $before_title . '<a href="' . get_permalink() . '" title="' . $title . '">' . $title . '</a>' . $after_title;
					else: $output .= $before_title . '<a href="' . get_permalink() . '" title="' . $page_name . '">' . $page_name . '</a>' . $after_title;
					endif;
				}
				if ($body_text): $output.= '<p>' . $body_text . '</p>';
				else: $output .= '<p>' . get_the_excerpt() . '</p>';
				endif;	
				$output .= '<a class="read-more" href="' . get_permalink() . '">' . __( 'Read more', 'spacious' ) . '</a>';
				$output .= $after_widget;
			endwhile;
			// Reset Post Data
			wp_reset_postdata();
			echo $output;
		}

	}
}