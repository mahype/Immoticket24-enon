import type HasFormData from '../Interfaces/HasFormData';
import type HasFieldsetData from '../Interfaces/HasFieldsetData';
import type Field from './Field';

import Fieldset from './Fieldset';
import Navigation from './Navigation';


/**
 * Class Form.
 * 
 * @since 1.0.0
 */
export default class Form {
    readonly name           : string;
    readonly classes        : [];
    readonly navigation     : Navigation;
    readonly fieldsets      : Fieldset[];
    public   sent = false;

    /**
     * Initializing form data.
     * 
     * @param formData Formdata from JSON file.
     * 
     * @since 1.0.0
     */
    public constructor( formData: HasFormData, nonce: string ) {
        this.name      = formData.name;
        this.classes   = formData.classes;
        this.fieldsets = [];
        
        formData.fieldsets.forEach( ( fieldset: HasFieldsetData ) => {
            this.fieldsets.push( new Fieldset( this, fieldset ) );
        });

        this.navigation = new Navigation( this, formData.start, nonce );
    }

    /**
     * Get a specific fieldset.
     * 
     * @param name Name of fieldset
     * @return Fieldset
     * 
     * @since 1.0.0
     */
    public getFieldset( name: string ) : Fieldset {
        let fieldsets = this.fieldsets.filter( ( fieldset: Fieldset ) => {
            return fieldset.name === name;
        });

        return fieldsets[0];
    }

    public getField( name: string ) : Field {
        let foundField: Field;

        this.fieldsets.forEach( ( fieldset: HasFieldsetData ) => {
            fieldset.fields.forEach( ( field: Field ) => {
                if( field.name === name ) {
                    foundField = field;
                }
            });
        });

        return foundField;
    }

    /**
     * Has form validation errors.
     * 
     * @return True if field has errors, false if not.
     * 
     * @since 1.0.0
     */
    public hasValidationErrors() : boolean {
        let hasValidationErrors = false;       
        this.fieldsets.forEach( ( fieldset: Fieldset ) => {
            if( fieldset.hasValidationErrors() ) {
                hasValidationErrors = true;
                return;
            }
        });

        return hasValidationErrors;
    }

    /**
     * Get CSS Classes.
     * 
     * @return String of CSS classes.
     * 
     * @since 1.0.0
     */
    public getClasses(): string {
        if ( this.classes.length > 0  ) {
            return this.classes.join(' ');
        }

        return '';
    }
}