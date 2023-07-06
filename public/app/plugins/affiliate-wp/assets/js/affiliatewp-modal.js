/**
 * AffiliateWp Modal Class.
 *
 * This is an abstraction layer for the modal object.
 *
 * @since 2.15.0
 */

'use strict';

/* eslint-disable no-console */
class AffiliateWPModal {

	/**
	 * Instance of the 3rd-party plugin.
	 *
	 * Private property, please do not use this directly.
	 *
	 * @since 2.15.0
	 *
	 * @type {null}
	 */
	_instance = null;

	/**
	 * Array of contents to be displayed.
	 *
	 * @since 2.15.0
	 *
	 * @type {Array}
	 */
	contents = [];

	/**
	 * Supported contents.
	 *
	 * @since 2.15.0
	 *
	 * @type {Array}
	 */
	supportedContents = ['image', 'video', 'html'];

	/**
	 * Plugin default settings.
	 *
	 * @type {Object}
	 */
	settings = {
		enableHashNavigation: true,
		slug: ''
	};

	/**
	 * Events in queue to be executed by plugin.
	 *
	 * @since 2.15.0
	 *
	 * @type {Object}
	 */
	eventQueue = {};

	/**
	 * Class constructor.
	 *
	 * @since 2.15.0
	 *
	 * @param {Array} contents Array of contents.
	 * @param {Object} settings Override modal settings.
	 */
	constructor( contents = [], settings = {} ) {

		// Prepare settings.
		this.updateSettings( settings );

		// Set all contents.
		this.setContents( contents );

		// Auto-start if a valid hash is provided.
		this.maybeStartFromHash();
	}

	/**
	 * Open the modal if instance was created before, otherwise initialize and open the modal.
	 *
	 * @since 2.15.0
	 */
	show() {

		// Fancybox does not have a show method when instantiating as a class, so we emulate the behavior, recreating everything.
		if ( this._instance && typeof this._instance.hasOwnProperty( 'destroy' ) ) {
			this._instance.destroy();
			this._instance = null;
		}

		// Initialize the modal for the first time and execute events.
		this._initializeModal();
		this._initializeCustomEvents();
	}

	/**
	 * Start from hash.
	 *
	 * @since 2.15.0
	 */
	maybeStartFromHash() {
		/* eslint-disable no-undef */
		// Fancybox has been loaded at this point, since affwp-modal it is one of it dependencies.
		const HashPlugin = Fancybox.Plugins.Hash;

		if ( HashPlugin && ! this._instance ) {
			const { hash, slug, index } = HashPlugin.parseURL();

			if ( hash && slug === this.settings.slug ) {
				this.settings.startIndex = index - 1;
				this.show();
			}
		}
		/* eslint-enable no-undef */
	}

	/**
	 * Set plugin settings.
	 * It will always merged with defaults.
	 *
	 * @param {Object} settings Modal settings.
	 * @return {Object} Return this object, making it chainable.
	 */
	updateSettings( settings ) {
		if ( typeof settings !== 'object' ) {
			console.error( 'Settings must be an object.' );
			return;
		}

		this.settings.enableHashNavigation = settings.enableHashNavigation;
		this.settings.slug = settings.slug;

		// Translate our setting to equivalent Fancybox settings.
		this.settings.Hash = settings.hasOwnProperty( 'enableHashNavigation' )
			? settings.enableHashNavigation
			: true;

		return this;
	}

	/**
	 * Set content to be exhibited.
	 *
	 * @since 2.15.0
	 *
	 * @param {Array} contents - Array of content objects.
	 *
	 * @typedef {Object} ContentObject
	 * @property {string} src - The source of the content. Can be one either an URL for an image or video, or HTML.
	 * 							You need to change the type property accordingly to the content source.
	 * @property {string} type - Type of the content. Accepts one of the values set on the supportedContents object.
	 *
	 * @return {Object} Return this object, making it chainable.
	 *
	 * @example
	 * // Example usage:
	 * setContents([
	 *   { src: 'path/to/content1', type: 'image' },
	 *   { src: 'https://youtube.com/hashForAVideo', type: 'video' },
	 *   { src: '<p>My text here</p>', type: 'html' }
	 * ]);
	 */
	setContents( contents = [] ) {

		// Check if is an array.
		if ( ! Array.isArray( contents ) ) {
			console.error( 'Content must be an array of objects.' );
			return; // Not an array, can not continue.
		}

		// Display errors if the required properties are not set properly.
		contents = contents.filter((item, index) => {
			if ( ! item.hasOwnProperty( 'src' ) ) {
				console.error( `Modal src not found for item index ${index}. Ignoring item...` );
				return false;
			}

			if ( ! item.hasOwnProperty( 'type' ) || ! this.supportedContents.includes( item.type ) ) {
				console.error( `Modal invalid type for item index ${index}. Ignoring item...` );
				return false;
			}

			return true;
		});

		this.contents = contents;

		return this;
	}

	/**
	 * Initialize the modal plugin.
	 *
	 * Loads the 3rd-party modal instance.
	 *
	 * @since 2.15.0
	 */
	_initializeModal() {

		if ( ! Array.isArray( this.contents ) ) {
			return; // Must be an array.
		}

		if ( ! this.contents.length ) {
			return; // Empty array. Nothing to display.
		}

		// eslint-disable-next-line no-undef
		this._instance = Fancybox.show( this.contents, this.settings );
	}

	/**
	 * Initialize custom events for the modal.
	 *
	 * This method sets up custom event handlers for the modal using the `.on` method.
	 * It is currently designed to work with Fancybox, but may need modification if the modal library changes.
	 *
	 * @since 2.15.0
	 */
	_initializeCustomEvents() {
		if ( this._instance === null ) {
			return; // No instance found.
		}

		const customEvents = this._getCustomEvents();

		Object.entries( customEvents ).forEach( ( [ eventName, methodName ] ) => {
			if ( typeof this[ methodName ] === 'function' ) {
				this._instance.on( eventName, () => {
					this._executeEventQueue( eventName );
				});
			}
		});
	}

	/**
	 * Map events to custom event handler methods.
	 *
	 * This method maps modal events to corresponding custom event handler methods.
	 * The mapping is based on Fancybox methods. If the modal library changes,
	 * this mapping may need to be updated accordingly.
	 *
	 * @since 2.15.0
	 *
	 * @return {Object} - An object containing the mapped events and their corresponding custom event handler methods.
	 */
	_getCustomEvents() {
		return {
			close: 'onClose',
			init: 'onInit',
			beforeClose: 'onBeforeClose'
		};
	}


	/**
	 * Add events to the queue to be executed when requested.
	 *
	 * @since 2.15.0
	 *
	 * @param {string} eventName - Name of the event to be added to the queue.
	 * @param {Function} callback - Method to be executed for the event.
	 */
	_addToEventQueue( eventName, callback ) {
		if ( ! this.eventQueue[ eventName ] ) {
			this.eventQueue[ eventName ] = [];
		}
		this.eventQueue[ eventName ].push( callback );
	}

	/**
	 * Runs an event from the queue.
	 *
	 * @since 2.15.0
	 *
	 * @param {string} eventName - Name of the event to be executed.
	 */
	_executeEventQueue( eventName ) {
		const callbacks = this.eventQueue.hasOwnProperty( eventName )
			? this.eventQueue[ eventName ]
			: [];

		if ( Array.isArray( callbacks ) ) {
			callbacks.forEach( callback => {
				if ( typeof callback === 'function' ) {
					callback();
				}
			});
		}
	}

	/**
	 * Runs once the modal is initialized.
	 *
	 * @since 2.15.0
	 *
	 * @param {Function} callback Method to be executed.
	 * @return {Object} Return this object, making it chainable.
	 */
	onInit( callback ) {
		this._addToEventQueue( 'init', callback );
		return this;
	}

	/**
	 * Runs after closing the modal.
	 *
	 * @since 2.15.0
	 *
	 * @param {Function} callback Method to be executed.
	 * @return {Object} Return this object, making it chainable.
	 */
	onClose( callback ) {
		this._addToEventQueue( 'close', callback );
		return this;
	}
}

/* eslint-disable no-undef */
affiliatewp.attach( 'modal', function( content = [], settings = {} ) {
	return new AffiliateWPModal(
		content,
		affiliatewp.parseArgs( settings, {} )
	);
});
