(function( $ ) {
	'use strict';

	class CatPrime {

		constructor( post_id, taxonomy_name, primary, nonce ){

            this.post_id = post_id;
            this.taxonomy_name = taxonomy_name;
			this.primary = primary;
			this.nonce = nonce;

			this.retrieve_metabox();

			this.checkbox_updates();

			this.update_metabox();

		}

		retrieve_metabox() {

			let self = this;

			this.metabox = $(  '#taxonomy-' + self.taxonomy_name );

            $( self.metabox ).find( 'li' ).append( '<span class="catPrimeLink"></span>' );

		}

        submit_request( type, taxonomy, term ){

		    let self = this;

		    $.ajax({
                type: "post",
                dataType: "json",
                url: primary_cat_data.endpoint,
                data: {
                    post_id: self.post_id,
                    action: "catprime_modify_term",
                    taxonomy: taxonomy,
                    term: term,
                    type: type,
                    nonce: self.nonce[action]
                },
                success: function( response ){

                    if( response.type === "success" ) {

                        self.primary = response.data;

                        self.update_metabox();

                    } else {

                        alert( "Unable to change primary taxonomy." );

                    }

                }
            });

		}

        checkbox_updates() {

		    let self = this;

            $( this.metabox ).find( 'input[type=checkbox]' ).each( function() {

                $( this ).on('change', function () {

                    self.update_metabox();

                    if( $(this).val() == self.primary ){

                        self.submit_request( 'remove', self.taxonomy_name, self.primary );

                    }

                });

            } );
        }

		update_metabox() {

		    let self = this;

			$( this.metabox ).find( 'input[type=checkbox]' ).each( function() {

			    let markup = '',
                    action_link = $(this).closest('li').find('.catPrimeLink');

			    if( $(this).prop("checked") === true ){

                    let value = $(this).val(),
                        type = 'add';


                    markup = ' <span class="dashicons dashicons-star-empty"></span>';

                    if( $(this).val() == self.primary ){

                        type = 'remove';
                        markup = ' <span class="dashicons dashicons-star-filled"></span><small>Primary</small>';

                    }

                    // prevent multiple click events
                    action_link.off('click');

                    // define new click events
                    action_link.on('click', function(){
                        self.submit_request( type, self.taxonomy_name, value );
                    } );

                }

                // Provide markup indicating which term is the primary term
                action_link.html( markup );

			} );

		}

	}

    $( window ).load(function() {

        primary_cat_data.prototypes = [];

        $.each( primary_cat_data.taxonomies, function( key, value ) {

            primary_cat_data.prototypes.push( new CatPrime( primary_cat_data.post_id, key, value, primary_cat_data.nonce ) );

        } );

	} );

})( jQuery );
