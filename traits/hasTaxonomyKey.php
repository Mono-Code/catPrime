<?php
trait hasTaxonomyKey{

    protected function taxonomy_key( $taxonomy_name ){

        if( !is_string( $taxonomy_name ) ){ $taxonomy_name = ''; }

        return sprintf( 'catprime_%s',

            $taxonomy_name

        );

    }

}