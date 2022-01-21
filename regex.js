"use strict";

function checkModifiersOk() {

}

function makeMultiLine() {

}


function testRegex() {
	var sample = $('#sample').val();console.log(sample);
	if( $('#ws_trim').is(':checked') ) {
		sample = sample.trim();
	}

	$('.regexes li').each(function(index){
		var find = $('#find'+index).val();
		if( find != '' ) {
			var replace = $('#replace'+index).val();
			var modifiers = $('#modifiers'+index).val();

			try {
				var reg = new XRegExp( find , modifiers );
			} catch(e) {
				console.log('XRegExp error: ' + e.message );
			}
		}
	})

}

function setActiveTab( whichTab ) {

}

function showOutput() {

}

function getParent( el , tagname ) {
	if( el.tagName == tagname ) {
		return el;
	} else {
		el = el.parentNode;
		if( el == undefined ) {
			return false;
		} else {
			return getParent( el , tagname );
		}
	}
}

function extractIdIndex( id_ ) {
	
	var i_ = id_.length - 1;
	return id_[i_] / 1;
}


/**
 * @function convertFieldX() converts a field from input to textarea (or vice versa )
 *
 * @var integer fieldIdIndex the numeric suffix of the find/replace input ID
 *
 * @var string fieldType which field type should be used
 *	(textarea|input[text])
 */
function convertFieldX( fieldIdIndex , fieldType ) {
	if( typeof fieldIdIndex != 'number' || fieldIdIndex < 0 ) {
		return false;
	}
	if( typeof fieldType != 'string' || ( fieldType != 'input' && fieldType != 'textarea' ) ) {
		return false;
	}
	/**
	 * @var Element oldField The variable that will hold the old
	 *	Find/Replace textarea/input field
	 */
	var oldField;
	/**
	 * @var Element newField The variable that will hold the new
	 *	Find/Replace textarea/input field
	 */
	var newField;

	/**
	 * @var Element tmpParent The wrapper element object for the
	 *	field
	 */
	var tmpParent;

	/**
	 * @var array fieldId List of IDs of fields to be replaced.
	 */
	var fieldId = [ 'find' + fieldIdIndex , 'replace' + fieldIdIndex ];

	/**
	 * @var array attr List of attributes that need to be passed
	 *	from oldField to newField
	 */
	var attr = [ 'name' , 'class' , 'placeholder' ];

	/**
	 * @var integer a Index for top level FOR loop
	 */
	var a = 0;

	/**
	 * @var integer b Index for second level FOR loop
	 */
	var b = 0;
	for( a = 0 ; a < 2 ; a += 1 ) {
		// get the old element to be replaced
		oldField = document.getElementById(fieldId[a]);
		if( oldField != null ) {
			// Ffew !!! the old element exists
			if( oldField.tagName != fieldType ) {
				// We need to change the old element to the new type

				// create the new field and add the old field's
				// attributes
				newField = document.createElement(fieldType);

				// set the ID for the new field
				newField.setAttribute( 'id' , fieldId[a] );

				// set the other attributes for the new field
				for( b = 0 ; b < 3 ; b += 1 ){
					newField.setAttribute( attr[b] , oldField.getAttribute(attr[b]) );	
				}

				// set the value for the new field
				newField.value = oldField.value;


				// Get the old field's parent wrapper put the new
				// field into it. Then remove the old field
				tmpParent = oldField.parentElement;
				tmpParent.appendChild(newField);
				tmpParent.removeChild(oldField);
			}
		}
	}
}

$('document').ready(function(){
	$('input.modifiers').blur( function() {
		var current = false;
		var hasError = false;
		$('input.modifiers').each( function() {

			/**
			 * @var string flavour The regex engine flavour to be
			 *	used (as identified by modifiers)
			 */
			var flavour = 'vanilla';

			/**
			 * @var string modifiers The string value of the
			 *	modifiers for this regex Find/Replace pair.
			 */
			var modifiers = $(this).val();

			/**
			 * @var string errorMsg If there's a issue with the
			 *	modifiers this will advise what the issue is.
			 */
			var errorMsg = '';

			/**
			 * @var boolean skip Whether or not to skip parsing
			 *	the modifiers.
			 *
			 * If the modifiers are unchanged, then from the last
			 * time they were checked, there's no point in
			 * parsing them again.
			 */
			var skip = false;

			// Check if there are any modifiers. 
			// If so, check if anything has changed
			if( modifiers.length == 0 ) {
				// the modifiers have been removed
				$(this).data('regexModifiers',modifiers);
				// nothing else to do.
				return true;
			} else if( $(this).data('regexModifiers') == modifiers ) {
				// the modifiers have not changed

				flavour = $(this).data('regexFlavour');
				// don't bother processing the modifiers again
				skip = true;
			}


			/**
			 * @var string newModifiers The sanitised list of
			 *	modifiers. As each modifier is processed,
			 *	invalid modifiers are stripped. newModifiers
			 *	holds only the valid modifiers
			 */
			var newModifiers = '';


			/**
			 * @var boolean ecma Whether or not the Modifiers
			 *	are vanilla javascript only
			 */
			var ecma = false;

			/**
			 * @var string ecmaMs The javascript only modifiers
			 *	('g' & 'y') that may be present in the list
			 *	of user supplied modifiers
			 */
			var ecmaMs = '';


			/**
			 * @var boolean pcre Whether or not the modifiers
			 *	are PHP PCRE regex only
			 */
			var pcre = false;

			/**
			 * @var string ecmaMs The PHP PCRE only modifiers
			 *	('e', 'A', 'D', 'S', 'U', 'X', 'J' & 'u')
			 *	that may be present in the list of user
			 *	supplied modifiers
			 *
			 * NOTE: modifiers 's' & 'x' are available to both
			 *	 PHP PCRE and XRegExp, so are not included
			 *	 here
			 */
			var pcreMs = '';

			/**
			 * @var boolean xregex Whether or not the modifiers
			 *	are XRegExp specific
			 */
			var xregex = false;

			/**
			 * @var string xregexMs The XRegExp only modifier
			 *	('n') that may be present in the list of
			 *	user supplied modifiers
			 *
			 * NOTE: modifiers 's' & 'x' are available to both
			 *	 PHP PCRE and XRegExp, so are not included
			 *	 here
			 */
			var xregexMs = '';


			/**
			 * @var integer idIndex The index value used to suffix
			 *	all IDs in this regex Find/Replace block
			 */
			var idIndex = extractIdIndex( $(this).attr('id') );

			if( skip === false ) {
				var a = 0;
				// loop through the modifiers
				for( a = 0 ; a < modifiers.length ; a += 1 ) {
					switch(modifiers[a]) {
						// TODO: work out solution to possible conflict created by
						//	 's' & 'x' modifiers

						case 'i': // everything uses case insensitive
						case 'm': // theoretically everything uses [m]ultiline
							newModifiers += modifiers[a]; // this modifier is OK. add it to the list of keepers
							break;
						case 'g': // js only
						case 'y': // js only
							ecma = true;
							ecmaMs += modifiers[a];
							newModifiers += modifiers[a]; // this modifier is OK. add it to the list of keepers
							break;
						case 'n': // XRegExp only
							xregex = true;
							xregexMs += modifiers[a];

							newModifiers += modifiers[a]; // this modifier is OK. add it to the list of keepers
							break;
						case 's': // XRegExp & PHP PCRE
						case 'x': // XRegExp & PHP PCRE
							xregex = true;
							pcre = true;
							newModifiers += modifiers[a]; // this modifier is OK. add it to the list of keepers
							break;
						case 'e': // PHP PCRE only
						case 'A': // PHP PCRE only
						case 'D': // PHP PCRE only
						case 'S': // PHP PCRE only
						case 'U': // PHP PCRE only
						case 'X': // PHP PCRE only
						case 'J': // PHP PCRE only
						case 'u': // PHP PCRE only
							pcre = true;
							pcreMs += modifiers[a];
							newModifiers += modifiers[a]; // this modifier is OK. add it to the list of keepers
							break;
					}
				}
				if( newModifiers != modifiers ) {
					$(this).val(newModifiers);
				}

				if( pcre === true && pcreMs.length > 0 ) {
					if( ( ecma === true && ecmaMs.length > 0 ) || ( xregex === true && xregexMs.length > 0 ) ) {
						// ecma and pcre conflict we have an error
						flavour = false;
						hasError = true;
						errorMsg = 'You can\'t use modifiers for both PHP PCRE regular expressions and JavaScript regular expressions.';
					} else {
						// looks like you're trying to use PHP PCRE flavoured regexes
						flavour = 'pcre';
					}
				} else if( xregex === true ) {
					// OK  using advanced XRegExp modifiers
					flavour = 'XRegExp';
				} else {
					// just using plain old ecma regexes.
					flavour = 'ecma';
				}
			}


			if( flavour != false ) {
				if( current === false ) {
					// This is the first time we've got modifiers
					// so set current to the current regex engine
					current = flavour;
				} else if ( current != flavour ) {
					// This set of modifiers is different from the preceeding
					// modifiers. Lets see what's going on.
					if( current == 'ecma' && flavour == 'XRegExp') {
						// upgrade from vanilla JS regexp to XRegExp
						current == flavour;
//					} else if( current == 'XRegExp' && output == 'ecma') {
						// nothing to do here
					} else if( ( current == 'pcre' && ( flavour == 'ecma' || flavour == 'XRegExp') ) || ( ( current == 'ecma' || current == 'XRegExp') && flavour == 'pcre' ) ) {
						// Bugger!!! We have a conflict between regex engines.

						hasError = true;

						// add an error message to the end of the wrapping <LI>
						if( flavour == 'pcre' ) {
							errorMsg = 'The PHP PCRE modifiers used here conflict with the previous ' + current + ' JavaScript RegExp modifiers.';
						} else {
							errorMsg = 'The ' + flavour + ' modifiers used here conflict with the previous PHP PCRE modifiers.';
						}
					}
				}
			}

			// store the regex engine flavour to speed up future retesting
			$(this).data('regexFlavour',flavour);

			if( errorMsg != '' ) {
				// add error class to the modifiers input field
				$(this).addClass('error');

				// add error class to the wrapping <LI>
				$( '#regexp' + idIndex ).addClass('error')

				if( $( '#regexp' + idIndex + ' .error-msg' ) ) {
					if(  $(this).data('errorMsg') != errorMsg ) {
						// replace the existing error message
						$( '#regexp' + idIndex + ' .error-msg' ).text(errorMsg);
					}
				} else {
					// add an error message to the end of the wrapping <LI>
					$( '#regexp' + idIndex ).append( '<p class="error-msg">' + errorMsg + '</p>' );
				}

				// save the error message for later
				$(this).data('errorMsg',errorMsg);	
			} else {
				// remove error class to the modifiers input field
				$(this).removeClass('error');

				// remove error class on the wrapping <LI>
				idIndex = extractIdIndex( $(this).attr('id') );
				$( '#regexp' + idIndex ).removeClass('error');

				// remove the error message
				$( '#regexp' + idIndex + ' .error-msg' ).remove();

				// All the modifiers are good. Store them for later comparison
				$(this).data( 'regexModifiers' , newModifiers );
				$(this).data( 'errorMsg' , '' );
			}
		});

		if( hasError === true ) {
			// disable both the "Test" and the "Replace" buttons until this is resolved.
			$('#submitTest').attr('disabled','disabled');
			$('#submitReplace').attr('disabled','disabled');
		} else {
			// if there was an error but that error has now
			// been resolved, re-enable the submit buttons
			$('#submitTest').removeAttr('disabled');
			$('#submitReplace').removeAttr('disabled');
		}

	});

	$('#regexp0 .checkbox-check :checkbox').click(function(){

		/**
		 * @var integer idIndex The index value used to suffix
		 *	all IDs in this regex Find/Replace block
		 */
		var idIndex = extractIdIndex( $(this).attr('id') );


		if( $(this).is(':checked') === true ) {
			if( !$('#regexp' + idIndex).hasClass('has-textarea') ) {
				$('#regexp' + idIndex).addClass('has-textarea');
			}
			// convert the field into a textarea
			convertFieldX( idIndex , 'textarea' );
		} else {
			$('#regexp' + idIndex).removeClass('has-textarea');

			// convert the field into an input text field
			convertFieldX( idIndex , 'input' );
		}

	});

/*
	$('#submitTest').click(function(e){
		e.preventDefault();
		testRegex();
		setActiveTab(2);
		return false;
	});

	$('#submitReplace').click(function(e){
		e.preventDefault();
		testRegex().showOutput();
		return false;
	});
*/

	/**
	 * @function add_regex_pair() creates a new regex Find/Replace pair.
	 */
	$('#add_regex_pair').click(function(){

		// clone the last regex pair.
		$('.regexes li:last').clone(true,true).appendTo('.regexes');

		// update the all the bits to make it look like a new pair

		/**
		 * @var integer i The number of regex pairs now.
		 */
		var i = $('.regexes li').length;

		/**
		 * @var integer h The number of regex pairs before now
		 */
		var h = i - 1;

		/**
		 * @var integer g The index of the previous (or first) regex pair
		 */
		var g = h - 1;

		/**
		 * @var array attrs The list of attribute names that need to be updated
		 */
		var attrs = [ 'for' , 'id' , 'name' , 'title' , 'placeholder' ];

		/**
		 * @var integer a The index of items in the attrs array
		 */
		var a = 0;

		/**
		 * @var string oldAttr The origina value of the attribute
		 */
		var oldAttr = '';

		/**
		 * @var RegExp reg The regular expression used to update the attribute value
		 */
		var reg = new RegExp( g , 'g' );



		// update the ID of the <LI> wrapper for the regex pair
		$('.regexes li:last').attr( 'id' , 'regexp' + h );
		if( !$('.regexes li:last').hasClass('additional') ) {
			// add the "additional" class to the <LI>
			$('.regexes li:last').addClass('additional');
		}

		// loop through the attributes
		for( a = 0 ; a < attrs.length ; a += 1 ) {
			
			// loop through each Element that has this attribute
			$('.regexes li:last [' + attrs[a] + ']').each( function() {
				// extract the attribute value
				oldAttr = $(this).attr(attrs[a]);

				// check if the attribute needs to be updated
				if( oldAttr.indexOf(g) >= 0 ) {
					// change the attribute value then update the elment
					$( this ).attr( attrs[a] , oldAttr.replace( reg , h ) );
				}
			});

			if( attrs[a] == 'name' ) {
				// All the attributes from here on, are visable to
				// the viewer start numbering from 1 instead of 0
				// used for arrays
				reg = new RegExp( h , 'g' );
				// shift the value up 1
				g += 1;
				h += 1;
			}
		}
		// update the span values within the find/replace labels
		$('.regexes li:last label span').each( function(){
			$(this).text(h);
		});

		// remove previous modifiers
		$('#modifiers' + g ).val('');		

		// remove the hiding class from the "Remove" button
		$('label[for=remove_regex_pair]').removeClass('hiding');

		return false;
	});

	$('#remove_regex_pair').click(function(){

		var liCount = $('.regexes li').length;
		if( liCount > 1 ){
			$('.regexes li:last').remove();
			if( liCount == 2 ) {
				if( ! $('label[for=remove_regex_pair]').hasClass('hiding') ) {
					$('label[for=remove_regex_pair]').addClass('hiding');
				}
				$('#add_regex_pair').focus();
			} else {
				$('#remove_regex_pair').focus();
			}
		}
		return false;
	});

	$('#ws_trim').on('change',function(e){
		if( $(this).is(':checked') === true ) {
			$('#ws_trim_pos_before').removeAttr('disabled');
			$('#ws_trim_pos_after').removeAttr('disabled');
			$('#ws_trim_pos_before').parent().removeClass('disabled');
			$('#ws_trim_pos_after').parent().removeClass('disabled');
		} else {
			$('#ws_trim_pos_before').attr('disabled','disabled');
			$('#ws_trim_pos_after').attr('disabled','disabled');
			$('#ws_trim_pos_before').parent().addClass('disabled');
			$('#ws_trim_pos_after').parent().addClass('disabled');
		}
	});
});
