<?php

class Catprime_Admin {

    use hasTaxonomyKey;

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() { }

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		if ( !apply_filters( 'apply_catprime', $this->apply_catprime() ) ){ return; }

		global $post;

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/catprime-admin.js', array( 'jquery' ), $this->version, false );

		wp_localize_script( $this->plugin_name, 'primary_cat_data', [
		    'post_id'       => $post->ID,
		    'endpoint'      => admin_url( 'admin-ajax.php' ),
            'taxonomies'    => $this->primary_cat_data( $post->ID ),
            'nonce'         => [
                'add' => wp_create_nonce( 'catprime_ajax_add' ),
                'delete'    => wp_create_nonce( 'catprime_ajax_remove' )
            ]
        ] );

	}

	protected function apply_catprime() {

	    if( isset( $_GET['action'] ) && $_GET['action'] === 'edit' ){

	        return true;

        }

        if( 'add' === ( get_current_screen() )->action ){

	        return true;

        }

	    return false;

    }

	protected function primary_cat_data( $post_id ) {

	    $data_values = [];

	    $post_meta = get_post_meta( $post_id );

        foreach( get_post_taxonomies( $post_id ) as $taxonomy_name ){

            $key = $this->taxonomy_key( $taxonomy_name );

            $data_values[ $taxonomy_name ] = ( isset( $post_meta[ $key ] ) )? $post_meta[ $key ][0] : null;

        }

	    return $data_values;

    }

    public function ajax_modify_primary_term() {

	    // Sanitize Request Values
        $cleaned_request = [];
        foreach( $_REQUEST as $key => $value ){

            $cleaned_request[ $key] = sanitize_text_field( $value );

        }

        // Verify Nonce
        $nonce_key = sprintf('catprime_ajax_%s', $cleaned_request['type'] );
        if( isset( $cleaned_request['nonce'] ) && !wp_verify_nonce( $cleaned_request['nonce'], $nonce_key ) ){ exit( 'Exit not available.' ); }

        // Handle Request
        if( isset( $cleaned_request['type'] ) ) {

            switch( $cleaned_request['type'] ) {

                case 'add':
                    update_post_meta( $cleaned_request['post_id'], $this->taxonomy_key( $cleaned_request['taxonomy'] ), $cleaned_request['term'] );
                    break;

                case 'remove':
                    delete_post_meta( $cleaned_request['post_id'], $this->taxonomy_key( $cleaned_request['taxonomy'] ) );
                    break;

            }

        }

        // return values based on response
        $result = json_encode([
            'type' => 'success',
            'data' => get_post_meta( $cleaned_request['post_id'], $this->taxonomy_key( $cleaned_request['taxonomy'] ) )
        ]);

        echo $result;

        die();

    }

    public function ajax_not_available() {

        wp_die( 'Service not available.' );

    }


}
