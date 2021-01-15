'use strict';

var logColors = {
	error: 'background: #242424; color: red',
	warning: 'background: #242424; color: yellow;',
	info: 'background: #242424; color: cyan;',
	pass: 'background: #242424; color: green;',
	debug: 'background: #242424; color: orchid;',
	event: 'background: #242424; color: magenta;'
};

/**
 * Checks if the query string is available currently supports
 * log=error
 * log=info
 * log=all
 * Or you can use an array of values to display specific messages
 * ?log[]=info&log[]=error&log[]=warning
 *
 * @param string
 */
function checkUrlQueryString( string ) {
	var url = window.location.href,
		result = false;
	if ( -1 !== url.indexOf( `?log=${ string }` ) || -1 !== url.indexOf( `&log=${ string }` ) ||
		-1 !== url.indexOf( `?log[]=${ string }` ) || -1 !== url.indexOf( `&log[]=${ string }` ) ) {
		result = true;
	}
	return result;
}

function logMessage( logDetails ) {
	// Ensure logDetails message is present as its required.
	if ( typeof logDetails.message !== 'undefined' ) {

		if ( typeof logDetails.details !== 'undefined' ) {
			console.group();
		}
		if ( typeof logDetails.message === 'string' || logDetails.message instanceof String ) {
			console.log( `%c${ logDetails.message }`, logColors[ logDetails.type ] );
		} else {
			console.log( logDetails.message );
		}

		if ( typeof logDetails.details !== 'undefined' ) {
			console.log( logDetails.details );
			console.groupEnd();
		}

	} else {
		console.group();
		console.log( `%cIssue with use of log(), please ensure a object is passed to log(). in form of:`, logColors.error );
		console.log( {
			type: 'String either error, warning, info or pass',
			message: 'Message to be highlighted for details.',
			details: 'Mixed/Not Required'
		} );
		console.groupEnd();
	}
}

/**
 * Logger to log to console if query string is present in url.
 *
 * Message is displayed in correct color for type. Details can be a array, object,
 * Type can be one of error, warning, info or pass.
 *
 * @param logDetails = {type: 'string', message: 'string', details = 'mixed'}
 * logDetails.type        String either error, warning, info or pass.
 * logDetails.message    Message to be highlighted for details.
 * logDetails.details    Details.
 */
function log( logDetails = {} ) {
	// ensure that the query string is one of, error, warning, info, pass or all.
	if ( checkUrlQueryString( 'info' ) || checkUrlQueryString( 'pass' ) ||
		checkUrlQueryString( 'debug' ) || checkUrlQueryString( 'event' ) ||
		checkUrlQueryString( 'all' ) ) {

		if ( typeof console !== 'undefined' && console.log !== undefined ) {

			// Ensure only matching type logs get displayed.
			if ( typeof logDetails.type !== 'undefined' &&
				( checkUrlQueryString( logDetails.type ) || checkUrlQueryString( 'all' ) ) ) {
				// Error and warnings are handled differently, always displayed.
				if ( typeof logDetails.type !== 'undefined' && ( 'error' !== logDetails.type && 'warning' !== logDetails.type ) ) {
					// Log errors and warnings.
					logMessage( logDetails );
				}
			}
		}
	}

	if ( typeof logDetails.type !== 'undefined' && ( 'error' === logDetails.type || 'warning' === logDetails.type ) ) {
		// Log errors and warnings.
		logMessage( logDetails );
	}
}

( function( $ ) {

	var postTypeCSVExporter = {

		_form: undefined,
		_submitButton: undefined,
		_defaultButtonMessage: undefined,
		_submitButtonMessage: undefined,
		_pageMessageContainer: undefined,
		_isCompiling: false,
		_csvData: [],

		/**
		 * Initialize
		 *
		 * @param formID
		 * @param pageMessageID
		 */
		init( formID, pageMessageID ) {

			this._form = $( formID );
			if ( this.form().length ) {

				// Gather elements that are needed for process.
				this._submitButton = this.form().find( 'button[type="submit"]' );
				this._defaultButtonMessage = this.submitButton().html();

				// Page message elements.
				this.setPageMessageContainer( $( pageMessageID ) );
				this.setPageMessageContainer( $( pageMessageID ) );

				if ( $.fn.datepicker ) {
					this.form().find( '.csv-export-date-picker' ).datepicker( {
						dateFormat: 'yy-mm-dd'
					} );
				}

				// Add submit functionality.
				this.onSubmit();
			} else {
				log( {
					type: 'error',
					message: 'CSV Export form is not on this page, form missing or this script should not be enqueued.',
				} );
			}
		},

		/**
		 * Submit functionality for this form.
		 */
		onSubmit() {
			var exporter = this;
			this.form().submit( function( e ) {
				e.preventDefault();
				if ( ! exporter.isCompiling() ) {
					// Set to compiling to prevent additional clicks.
					exporter.setIsCompiling( true );

					// Request data.
					exporter.requestCSVExport();
				}
			} );
		},

		formToJSON( elements ) {

			return [].reduce.call( elements, function( data, element ) {

				data[ element.name ] = element.value;
				return data;
			} );
		},

		/**
		 * Request data to build csv.
		 */
		requestCSVExport() {

			var formData = new FormData( this.form()[ 0 ] ),
				exporter = this,
				data = {
					action: postTypeCSVExporterConfig.action,
					wp_nonce: postTypeCSVExporterConfig.ajax_nonce,
					post_type: postTypeCSVExporterConfig.post_type,
				};

			// Add form data to the data object being sent.
			formData.forEach( function( value, key ) {
				data[ key ] = value;
			} );

			// Make sure the data is empty before proceeding.
			exporter.emptyCsvData();

			// Button Messages.
			exporter.setSubmitButtonMessage( 'Exporting...' );
			exporter.updateSubmitButton();

			console.log( data);

			$.post( ajaxurl, data, function( response ) {

				if ( response.success ) {
					// Build csv data.
					exporter.addCsvData( response.data.headers );
					for ( var id in response.data.rows ) {
						exporter.addCsvData( response.data.rows[ id ] );
					}
					exporter.exportToCsv( response.data.filename, exporter.csvData() );

					// Return button message to default.
					exporter.setIsCompiling( false );
					exporter.updateSubmitButton();

					// Display page message.
					exporter.updatePageMessage( 'Successful Export', 'success' );

					log( {
						type: 'info',
						message: 'Successfully generated export.',
					} );
				} else {
					// Return button message to default.
					exporter.setIsCompiling( false );
					exporter.updateSubmitButton();

					// Display page message.
					if ( typeof response.message !== 'undefined' ) {
						exporter.updatePageMessage( response.message, 'error' );
					} else {
						exporter.updatePageMessage( 'An unexpected error has occurred, please try again.', 'error' );
					}

					log( {
						type: 'error',
						message: 'Error generate export failed, unsuccessful.',
						details: {
							response: response,
						}
					} );
				}

			} ).fail( function( xhr, status, error ) {
				// Return button message to default.
				exporter.setIsCompiling( false );
				exporter.updateSubmitButton();

				// Display page message.
				exporter.updatePageMessage( 'An unexpected error has occurred, please try again.', 'error' );
				log( {
					type: 'error',
					message: 'Error generate export failed, failed connection.',
					details: {
						error: error,
						data: data,
					}
				} );
			} );
		},

		/**
		 * Update the button message and disable/enable.
		 */
		updateSubmitButton() {
			if ( this.isCompiling() ) {
				this.submitButton().html( this.submitButtonMessage() );
				this.submitButton().prop( 'disabled', true );
			} else {
				this.submitButton().html( this.defaultButtonMessage() );
				this.submitButton().prop( 'disabled', false );
			}
		},

		/**
		 * Update the page message and bing functionality.
		 *
		 * @param message The content of the message
		 * @param type The type of message.
		 */
		updatePageMessage( message, type ) {
			if ( this.pageMessageContainer().length ) {
				this.pageMessageContainer().html( this.pageMessageHtml( message, type ) );
				this.makeNoticesDismissible();
			}
		},

		/**
		 * HTML in the format of WP admin message banner.
		 *
		 * @param message The content of the message
		 * @param type The type of message.
		 *
		 * @returns {string}
		 */
		pageMessageHtml( message, type ) {
			var className = '';
			if ( 'error' === type ) {
				className = 'error notice notice-error is-dismissible';
			} else {
				className = 'updated notice notice-success is-dismissible';
			}

			return '<div id="message" class="' + className + '"><p>' + message + '</p></div>';
		},

		/**
		 * Derived from WP makeNoticesDismissible() that is outside of this scope.
		 */
		makeNoticesDismissible() {
			$( '.notice.is-dismissible' ).each( function() {
				var $el = $( this ),
					$button = $( '<button type="button" class="notice-dismiss"><span class="screen-reader-text"></span></button>' ),
					btnText = 'Dismiss';

				// Ensure plain text.
				$button.find( '.screen-reader-text' ).text( btnText );
				$button.on( 'click.wp-dismiss-notice', function( e ) {
					e.preventDefault();
					$el.fadeTo( 100, 0, function() {
						$el.slideUp( 100, function() {
							$el.remove();
						} );
					} );
				} );

				$el.append( $button );
			} );
		},

		/**
		 * Add CSV data to array for latter use.
		 * @param value
		 */
		addCsvData( value ) {
			this.csvData().push( value );
		},

		/**
		 * Empty for new export.
		 */
		emptyCsvData() {
			this._csvData = [];
		},

		/**
		 * Exporting function that builds the csv file.
		 *
		 * @param filename
		 * @param rows
		 */
		exportToCsv( filename, rows ) {

			var processRow = function( row ) {
				var finalVal = '';
				for ( var j = 0; j < row.length; j++ ) {
					var innerValue = row[ j ] === null ? '' : row[ j ].toString();
					if ( row[ j ] instanceof Date ) {
						innerValue = row[ j ].toLocaleString();
					}
					;
					var result = innerValue.replace( /"/g, '""' );
					if ( result.search( /("|,|\n)/g ) >= 0 ) {
						result = '"' + result + '"';
					}
					if ( j > 0 ) {
						finalVal += ',';
					}
					finalVal += result;
				}
				return finalVal + '\n';
			};

			var csvFile = '';

			for ( var i = 0; i < rows.length; i++ ) {
				csvFile += processRow( rows[ i ] );
			}

			var blob = new Blob( [ csvFile ], { type: 'text/csv;charset=utf-8;' } );
			if ( navigator.msSaveBlob ) { // IE 10+
				navigator.msSaveBlob( blob, filename );
			} else {
				var link = document.createElement( "a" );
				if ( link.download !== undefined ) { // feature detection
					// Browsers that support HTML5 download attribute
					var url = URL.createObjectURL( blob );
					link.setAttribute( "href", url );
					link.setAttribute( "download", filename );
					link.style.visibility = 'hidden';
					document.body.appendChild( link );
					link.click();
					document.body.removeChild( link );
				}
			}
		},

		/**
		 * Setters
		 */
		setIsCompiling( value ) {
			this._isCompiling = value;
		},
		setSubmitButtonMessage( value ) {
			this._submitButtonMessage = value;
		},
		setPageMessageContainer( value ) {
			this._pageMessageContainer = value;
		},

		/**
		 * Getters
		 */
		form() {
			return this._form;
		},

		submitButton() {
			return this._submitButton;
		},
		submitButtonMessage() {
			return this._submitButtonMessage;
		},
		defaultButtonMessage() {
			return this._defaultButtonMessage;
		},
		pageMessageContainer() {
			return this._pageMessageContainer;
		},
		isCompiling() {
			return this._isCompiling;
		},
		csvData() {
			return this._csvData;
		}
	};

	$( document ).ready( function() {
		window.postTypeCSVExporter = postTypeCSVExporter;
		postTypeCSVExporter.init( '#post-type-csv-exporter-form', '#page-message' );
	} );
} )( jQuery );
