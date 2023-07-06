/**
 * affiliatewp namespace.
 *
 * This file ensure that affwp is always set and loaded.
 * All vars loaded using wp_localize_script should point to this file, so we
 * can handle the data properly.
 *
 * @since 2.15.0
 */

'use strict';

/* eslint-disable no-console, no-unused-vars */
const affiliatewp = window.affiliatewp || {

	/**
	 * Check if the resource exists and attach to the affiliatewp object.
	 *
	 * @since 2.15.0
	 * @param {string} name The resource name to be attached.
	 * @param {*} resource A function, object or property.
	 * @param {boolean} destroyOriginalProperty Whether to remove from window object or not.
	 */
	attach( name, resource, destroyOriginalProperty = true ) {
		if ( this.hasOwnProperty( name ) ) {
			console.error( `Resource '${name}' is already registered in affiliatewp object.` );
			return; // Resource already exists.
		}

		// Assign to the affiliatewp instance.
		this[name] = resource;

		// Look for a property with the same name and destroy it, so we avoid duplicates.
		if ( destroyOriginalProperty && window.hasOwnProperty( name ) ) {
			delete window[name];
		}
	},

	/**
	 * Remove a resource (object, function, property) from affiliatewp object.
	 *
	 * @since 2.15.0
	 * @param {string} name The resource name to be removed.
	 */
	detach( name ) {
		if ( this.hasOwnProperty( name ) ) {
			const resource = this[name];
			delete this[name];
			return resource; // Return the resource, so it still can be assigned.
		}

		console.error( `Resource '${name}' not found in affiliatewp object.` );
	},

	/**
	 * Merge two objects. Similar to wp_parse_args() function.
	 *
	 * @param {Object} args Args to be merged/replace.
	 * @param {Object} defaults Default args.
	 * @return {Object} The new object.
	 */
	parseArgs( args , defaults = {} ) {
		if ( typeof args !== 'object' || typeof defaults !== 'object' ) {
			console.error( 'You must provide two valid objects' );
			return {};
		}

		return {
			...defaults,
			...args,
		};
	},

};

