jQuery.fn.wc_af_payments = function(){

	return this.each( function() {
    var $el 			= jQuery( this ),
      payments    = $el.data('payments');


    //console.log( payments );



    var repeater = SPACE_REPEATER( {
			$el				: $el,
      list_item_id	: 'wc-payment-item',
			btn_text		: '+ Add Payment',
      init	: function( repeater ){

				/*
				* INITIALIZE: CREATES THE UNLISTED LIST WHICH WILL TAKE CARE OF THE QUESTION, HIDDEN FIELD AND THE ADD BUTTON
				*/

				// ITERATE THROUGH EACH QUESTIONS IN THE DB
				jQuery.each( payments, function( i, payment ){

					if( payment['date'] != undefined && payment['amount'] != undefined ){
						repeater.addItem( payment );
					}
				});
			},
      addItem: function( repeater, $list_item, $closeButton, payment ){

        if( payment == undefined || payment['date'] == undefined ){
					payment = { date : '', amount : '' };
				}


        var $date = repeater.createField( {
					element	: 'input',
					attr	: {
            'type'        : 'date',
						'placeholder'	: 'Payment Date',
						'name'			: 'af_payments[' + repeater.count + '][date]',
            'value'			: payment['date'] ? payment['date'] : '',
					},
					append	: $list_item
				} );

				var $amount = repeater.createField( {
					element	: 'input',
					attr	: {
            'type'        : 'number',
						'placeholder'	: 'Payment  Amount',
						'name'			: 'af_payments[' + repeater.count + '][amount]',
            'value'			: payment['amount'] ? payment['amount'] : '',
					},
					append	: $list_item
				} );

        // HANDLE CLOSE BUTTON EVENT
				$closeButton.click( function( ev ){
					ev.preventDefault();
					if( confirm( 'Are you sure you want to remove this?' ) ){
						$list_item.remove();
					}
				});

      }
    } );
  } );

};

var AF_CUSTOMER = {

	setState: function( state_code ){
		jQuery( '#billing_state' ).val( state_code );
	},

	populateStates: function(){

		//console.log( 'populate' );

		var country_states = window.billing_meta_data.country_states;

		var country_code = AF_CUSTOMER.getSelectedCountry();

		var states = country_states[country_code];

		jQuery( '#billing_state' ).empty();

		for( var key in states ){
			var $option = jQuery( document.createElement( 'option' ) );
			$option.val( key );
			$option.html( states[ key ] );
			$option.appendTo( '#billing_state' );
		}

		//console.log( states );
	},

	getSelectedCountry: function(){
		return jQuery( '#billing_country' ).val();
	},

	init: function(){
		AF_CUSTOMER.populateStates();
		jQuery( '#billing_country' ).change( AF_CUSTOMER.populateStates );
		if( window.selectedState ){
			AF_CUSTOMER.setState( window.selectedState );
		}
	}
};



jQuery( document ).ready( function(){

  jQuery( '[data-behaviour~=wc-af-payments]' ).wc_af_payments();

	AF_CUSTOMER.init();




} );
