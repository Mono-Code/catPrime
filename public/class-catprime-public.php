<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       monocode.com
 * @since      1.0.0
 *
 * @package    Catprime
 * @subpackage Catprime/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Catprime
 * @subpackage Catprime/public
 * @author     Matthew Morrison <matt@monocode.com>
 */
class Catprime_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

    /**
     * Apply primary term filtering based off of custom query args
     *
     * @since 1.0.0
     * @param $query
     *
     */
	public function primary_filter( $query ){

	    // Check if filtering is applicable
	    if( is_admin() || $query->is_main_query() ){ return; }

	    if( !isset( $query->query[ 'primary_term' ] ) ){ return; }

	    // Keep filtering contained into an array to allow for contained relation logic
	    $primary_array = [];

	    // handle relation logic if applied
	    if( isset( $query->query[ 'primary_term' ]['relation'] ) ){

	        $primary_array['relation'] = $query->query[ 'primary_term' ]['relation'];

	        unset( $query->query[ 'primary_term' ]['relation'] );

        }

        // Assimilate custom args & translates custom args into meta query
	    foreach( $query->query[ 'primary_term' ] as $taxonomy => $terms ){

	        // Convert string to array
	        if( !is_array( $terms ) ){ $terms = [ $terms ]; }

	        // Assemble params
            $primary_array [] = [
                'key' => $this->taxonomy_key( $taxonomy ),
                'compare'   => 'IN',
                'value' => $terms
            ];

        }

        // Apply meta_query params
        $query->set( 'meta_query', $primary_array );

    }

}
