<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wpalchemists.com
 * @since      2.0.0
 *
 * @package    Rss_Image_Resize
 * @subpackage Rss_Image_Resize/admin
 * @author     Morgan Kay <morgan@wpalchemists.com>
 */
class Rss_Image_Resize_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_filter( 'the_content', array( $this, 'resize_feed_images' ) );

	}

	/**
	 * Create menu page.
	 *
	 * @since    2.0.0
	 */
	public function add_admin_menu() {

		add_options_page( __( 'RSS Image Resize', 'rss-image-resize' ), __( 'RSS Image Resize', 'rss-image-resize' ), 'manage_options', 'rss-image-resize', array( $this, 'options_page' ) );
	
	}

	/**
	 * Register settings.
	 *
	 * @since    2.0.0
	 */
	public function settings_init() { 

		register_setting(
			$this->plugin_name . '-options',
			$this->plugin_name . '-options',
			array( $this, 'validate_options' )
		);

		add_settings_section(
			$this->plugin_name . '-options',
			apply_filters( $this->plugin_name . '-display-section-title', __( 'RSS Image Resize Settings', 'rss-image-resize' ) ),
			array( $this, 'settings_section_callback' ),
			$this->plugin_name
		);

		add_settings_field( 
			'first-choice', 
			__( 'First Choice of Image Size', 'rss-image-resize' ), 
			array( $this, 'first_choice_render' ), 
			$this->plugin_name,
			$this->plugin_name . '-options'
		);

		add_settings_field( 
			'fallback', 
			__( 'Fallback Image Size if First Choice is Unavailable', 'rss-image-resize' ), 
			array( $this, 'fallback_render' ), 
			$this->plugin_name,
			$this->plugin_name . '-options'
		);

	}

	/**
	 * Display First Choice settings field.
	 *
	 * @since    2.0.0
	 */
	public function first_choice_render(  ) { 

		$options = get_option( $this->plugin_name . '-options' );
		$image_sizes = $this->get_list_of_image_sizes();
			if( $image_sizes ) { ?>
				<select name="<?php echo $this->plugin_name; ?>-options[first-choice]">
					<?php foreach( $image_sizes as $size ) { ?>
						<option value="<?php echo $size['name']; ?>" <?php selected( $options['first-choice'], $size['name'] ); ?>>
							<?php echo ucfirst( $size['name'] ) . ' (' . $size['width'] . ' x ' . $size['height'] . ')'; ?>
						</option>
					<?php } ?>
					<option value="original" <?php selected( $this->plugin_name . '-options[fallback]', 'original' ); ?>>
						<?php _e( 'Original Size', 'rss-image-resize' ); ?>
					</option>
				// <!-- </select> -->
			<?php } else {
				_e( 'We were unable to retrieve a list of image sizes.  Check your media settings.', 'rss-image-resize' );
			}

	}


	/**
	 * Display Fallback settings field.
	 *
	 * @since    2.0.0
	 */
	public function fallback_render(  ) { 

		$options = get_option( $this->plugin_name . '-options' );
		$image_sizes = $this->get_list_of_image_sizes();
			if( $image_sizes ) { ?>
				<select name="<?php echo $this->plugin_name; ?>-options[fallback]">
					<?php foreach( $image_sizes as $size ) { 
						if( isset( $size['default'] ) && 'default' == $size['default'] ) { ?>
							<option value="<?php echo $size['name']; ?>" <?php selected( $options['fallback'], $size['name'] ); ?>>
								<?php echo ucfirst( $size['name'] ) . ' (' . $size['width'] . ' x ' . $size['height'] . ')'; ?>
							</option>
						<?php } 
					} ?>
					<option value="original" <?php selected( $this->plugin_name . '-options[fallback]', 'original' ); ?>>
						<?php _e( 'Original Size', 'rss-image-resize' ); ?>
					</option>
				</select>
				<p><?php _e( 'If your first choice of image size is not available, what is your second choice?', 'wpaesm' ); ?></p>
			<?php } else {
				_e( 'We were unable to retrieve a list of image sizes.  Check your media settings.', 'rss-image-resize' );
			}

	}

	/**
	 * Could put extra HTML here.
	 *
	 * @since    2.0.0
	 */
	public function settings_section_callback(  ) { 

		// nothing to see here

	}

	/**
	 * Display the settings page.
	 *
	 * @since    2.0.0
	 */
	public function options_page(  ) { 

		?>
		<form action='options.php' method='post'>
			
			<h1><?php _e( 'RSS Image Resize', 'rss-image-resize' ); ?></h1>
			
			<?php
			settings_fields( 'rss-image-resize-options' );
			do_settings_sections( $this->plugin_name );
			submit_button();
			?>
			
		</form>
		<?php

	}

	/**
	 * Get a list of all of the registered image sizes to put in the dropdown menus
	 *
	 * @since    2.0.0
	 */
	private function get_list_of_image_sizes() {
		// see https://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes

		// return $sizes;
		global $_wp_additional_image_sizes;

        $sizes = array();
        $get_intermediate_image_sizes = get_intermediate_image_sizes();

        if( empty( $get_intermediate_image_sizes ) ) {
        	return false;
        }

        // Create the full array with sizes and crop info
        foreach( $get_intermediate_image_sizes as $_size ) {
            if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
        		$sizes[ $_size ]['name'] = $_size;
                $sizes[ $_size ]['width'] = get_option( $_size . '_size_w' );
                $sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
                $sizes[ $_size ]['default'] = 'default';

            } elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
                $sizes[ $_size ] = array( 
            		'name' => $_size,
                    'width' => $_wp_additional_image_sizes[ $_size ]['width'],
                    'height' => $_wp_additional_image_sizes[ $_size ]['height'],
                );
            }
        }

        
        return $sizes;
	}

	/**
	 * Sanitize input.
	 *
	 * @since    2.0.0
	 */
	public function validate_options( $input ) {
		$valid = array();
		$input['first-choice'] = strip_tags( $input['first-choice'] );
		$input['fallback'] = strip_tags( $input['fallback'] );
		return $input;
	} 

	/**
	 * Filter RSS content and resize the image.
	 *
	 * @since    2.0.0
	 */
	public function resize_feed_images( $content ) { 

		if ( is_feed() ) {
			$content = preg_replace_callback( '/<(img).*?>/i', array( $this, 'resize_image' ), $content );
		}

		return $content;
	}
	

	/**
	 * Replace the image with one of a different size.
	 *
	 * @since    2.0.0
	 * @return   string   HTML of resized image
	 */
	private function resize_image( $matches ) {

		// get the image URL
		$img_url = $this->get_image_url( $matches[0] );

		// verify that the URL is on this domain
		$upload_dir_paths = wp_upload_dir();
		if ( 0 !== strpos( $img_url, $upload_dir_paths['baseurl'] ) ) {
			// if not, wrap it in a div
			$return_value = $this->last_resort( $img_url );
			return $return_value;
		}

		// from the URL, get the attachment ID
		$attachment_id = $this->get_attachment_id( $img_url );

		// from the attachment ID, get the new image URL
		$options = get_option( $this->plugin_name . '-options' );
		$first_choice = $options['first-choice'];
		if( !isset( $first_choice ) || '' == $first_choice ) {
			$first_choice = 'medium';
		}
		$new_image = wp_get_attachment_image_src( $attachment_id, $first_choice );

		// verify that the new image URL exists
		if( $this->verify_existence( $new_image[0] ) ) {
			$return_value = '<img src="' . $new_image[0] . '">';
		} else {
			// try the next image size
			$second_choice = $options['fallback'];
			if( !isset( $second_choice ) || '' == $second_choice ) {
				$new_image = wp_get_attachment_image_src( $attachment_id, $second_choice );
			}
			// verify that the new image URL exists
			if( $this->verify_existence( $new_image[0] ) ) {
				$return_value = '<img src="' . $new_image[0] . '">';
			} else {
				// if that doesn't work, wrap it in a div
				$return_value = $this->last_resort( $img_url );
			}
		}

		return $return_value;

	}

	/**
	 * Extract the image URL.
	 *
	 * @since    2.0.0
	 * @return   string    URL of the image
	 */
	private function get_image_url( $html ) { 

		$doc = new DOMDocument();
		libxml_use_internal_errors( true );
		$doc->loadHTML( $html );
		$xpath = new DOMXPath( $doc );
		$imgs = $xpath->query( '//img' );

		for( $i=0; $i < $imgs->length; $i++ ) {
		    $img = $imgs->item( $i );
		    $src = $img->getAttribute( 'src' );
		}
		// in the future, might want to grab image classes and inline styles as well

		return $src;
	}

	/**
	 * Given the URL, get the attachment ID.
	 *
	 * @since    2.0.0
	 * @return   int     ID of the attachment
	 */
	private function get_attachment_id( $url ) {

		global $wpdb;
		$attachment_id = false;
	 
		// If there is no url, return.
		if ( '' == $url )
			return;
	 
		// Get the upload directory paths
		$upload_dir_paths = wp_upload_dir();
	 
		$url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $url );
	 
		// Remove the upload path base directory from the attachment URL
		$url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $url );

		// Get the attachment ID from the modified attachment URL
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $url ) );

		return $attachment_id;

	}

	/**
	 * Check whether a URL returns a 404.
	 *
	 * @since    2.0.0
	 * @return   boolean  Whether the URL exists or not
	 */
	private function verify_existence( $url ) {

		$file_headers = @get_headers( $url );

		if( false == strpos( $file_headers[0],'404' ) ) {
		    return true;
		} else {
		    return false;
		}

	}

	/**
	 * Extract the image URL.
	 *
	 * @since    2.0.0
	 * @return 	 string  HTML to put image in a div with inline style determining the size
	 */
	private function last_resort( $img_url ) { 

		$options = get_option( $this->plugin_name . '-options' );
		$first_choice = $options['first-choice'];
		$second_choice = $options['fallback'];

		// find the dimensions we need
		global $_wp_additional_image_sizes;

		if ( in_array( $first_choice, array( 'thumbnail', 'medium', 'large' ) ) ) {
            $width = get_option( $first_choice . '_size_w' );
            $height = get_option( $first_choice . '_size_h' );
        } elseif ( isset( $_wp_additional_image_sizes[ $first_choice ] ) ) {
            $width = $_wp_additional_image_sizes[ $first_choice ]['width'];
            $height = $_wp_additional_image_sizes[ $first_choice ]['height'];
        } elseif ( in_array( $second_choice, array( 'thumbnail', 'medium', 'large' ) ) ) {
            $width = get_option( $second_choice . '_size_w' );
            $height = get_option( $second_choice . '_size_h' );
        } elseif ( isset( $_wp_additional_image_sizes[ $second_choice ] ) ) {
            $width = $_wp_additional_image_sizes[ $second_choice ]['width'];
            $height = $_wp_additional_image_sizes[ $second_choice ]['height'];
		} else {
			$width = '300';
			$height = '300';
		}

		$html = '<div class="resized-image" style="width:' . $width . 'px;height:' . $height . 'px;"><img src="' . $img_url . '"></div>';

		return $html;
	}
}
