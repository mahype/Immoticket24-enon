import Request from "./Request";

import type Form from "./Form";
import type Fieldset from "./Fieldset";

import type HasFieldsetData from "../Interfaces/HasFieldsetData";
import type HasFieldData from "../Interfaces/HasFieldData";

export default class Navigation {
    readonly form            : Form;
    readonly recentFieldsets : [] = [];
    private  currentFieldset : Fieldset;
    private  lastAction      : string;
    private  nonce           : string;

    /**
     * Initializing navigation.
     * 
     * @param form  Form object.
     * @param start Name of start fieldset.
     * @param nonce Nonce string;
     * 
     * @since 1.0.0
     */
    public constructor( form: Form, startFieldset: string, nonce: string ) {
        this.form  = form;
        this.nonce = nonce;
        this.setCurrentFieldset( startFieldset );
    }

    /**
     * Get last action.
     * 
     * @return Last action (prev or next).
     * 
     * @since 1.0.0
     */
    public getLastAction() : string {
        return this.lastAction;
    }

    /**
     * Set current fieldset.
     * 
     * @param name Name of fieldset.
     * 
     * @since 1.0.0
     */
    public setCurrentFieldset( name: string ) : Navigation {
        let currentFieldset = this.form.getFieldset( name );

        if ( currentFieldset === undefined ) {
            throw new Error( 'Cant set current fieldset to "' + name + '". Fieldset name does not exist.' );
        } else {
            this.currentFieldset = currentFieldset;
        }

        return this;
    }

    /**
     * Get current fieldset.
     * 
     * @return Current fieldset.
     * 
     * @since 1.0.0
     */
    public getCurrentFieldset() : Fieldset {
        return this.currentFieldset;
    }

    /**
     * Set previous fieldset.
     * 
     * @return Navigation object.
     * 
     * @since 1.0.0
     */
    public prevFieldset() : Navigation {
        if ( ! this.hasPrevFieldset() ) {
            return this;
        }
        this.lastAction = 'prev';

        this.setCurrentFieldset( this.recentFieldsets.pop() );

        return this;
    }
    
    /**
     * Set next fieldset.
     * 
     * @return Navigation object.
     * 
     * @since 1.0.0
     */
    public nextFieldset() : Navigation {
        this.currentFieldset.validate();
        
        if ( this.currentFieldset.hasValidationErrors() ) {
            return this;
        }
        
        let nextFieldset = this.getNextFieldset();

        if ( nextFieldset !== undefined ) {
            this.recentFieldsets.push( this.currentFieldset.name );
            this.lastAction = 'next';     
            
            this.setCurrentFieldset( nextFieldset.name );

            return this;
        }

        throw new Error('No next fieldset not found.');
    }

    /**
     * Is there a previous fieldset?
     * 
     * @return True if there is a previous fieldset, false if not.
     * 
     * @since 1.0.0
     */
    public hasPrevFieldset() : boolean {
        return this.recentFieldsets.length > 0
    }

    /**
     * Is there a next fieldset?
     * 
     * @return True if there is a previous fieldset, false if not.
     * 
     * @since 1.0.0
     */
    public hasNextFieldset() : boolean {
        if ( this.currentFieldset.nextFieldset !== undefined ) {
            return true;
        }

        let nextFieldset = this.getNextFieldset();
        if ( nextFieldset !== undefined ) {
            return true;
        }

        return false;
    }

    /**
     * Is there a submission to do?
     * 
     * @return True if there is a submissionto do, false if not.
     * 
     * @since 1.0.0
     */
    public hasSubmission () {
        return this.currentFieldset.hasSubmission();
    }

    private prepareData () {
        let fieldsets = this.form.fieldsets;
        let fieldsetsToSend = this.currentFieldset.submission.fieldsets;

        let data = [];

        fieldsets.forEach( ( fieldset: HasFieldsetData ) => {
            if( fieldsetsToSend.includes( fieldset.name ) ) {
                let fields = [];

                fieldset.fields.forEach( ( field: HasFieldData ) => {
                    fields.push({
                        label: field.label,
                        value: field.value
                    })
                });

                data.push({                    
                    label: fieldset.label,
                    fields: fields
                });
            }
        });

        return data;
    }

    public submit() : Promise {
        this.currentFieldset.validate();
        
        if ( this.currentFieldset.hasValidationErrors() ) {
            return new Promise( ( resolve, reject ) => {
                reject('validationError');
            });
        }
    
        let request = new Request(
            this.currentFieldset.submission.url,
            this.currentFieldset.submission.method,
            this.prepareData(),
            this.nonce
        );

        return request.send();
    }

    /**
     * Returns the next fieldset.
     * 
     * @return Next fieldset object.
     * 
     * @since 1.0.0
     */
    public getNextFieldset() : Fieldset {
        if ( this.currentFieldset.nextFieldset !== undefined ) {
            return this.form.getFieldset( this.currentFieldset.nextFieldset );
        }

        let nextFieldsets = this.getPossibleNextFieldsets();
        let nextFieldset: Fieldset;

        if ( nextFieldsets.length === 0 ) {
            return nextFieldset;
        }

        nextFieldsets.forEach( ( fieldset: Fieldset ) => {
            if ( fieldset.conditionsFullfilled() && nextFieldset === undefined ) {
                nextFieldset = fieldset;
            }
        });
        
        return nextFieldset;
    }

    /**
     * Returns the previous fieldset.
     * 
     * @return Previous fieldset object.
     * 
     * @since 1.0.0
     */
    public getPrevFieldset() : Fieldset {
        if ( ! this.hasPrevFieldset() ) {
            throw Error( 'There is no previous fieldset');
        }

        let preFieldsetName = this.recentFieldsets[ this.recentFieldsets.length -1 ];
        return this.form.getFieldset( preFieldsetName );    
    }

    /**
     * Returns a possible fieldsets. 
     * 
     * Possible fieldsets are all fieldsets which containing a prevFieldset, containing the current fieldset.
     * 
     * @return An array of Fieldsets.
     * 
     * @since 1.0.0
     */
    private getPossibleNextFieldsets() : Fieldset[] {
        let nextFieldsets = this.form.fieldsets.filter( ( fieldset: Fieldset ) => {
            return fieldset.prevFieldset === this.currentFieldset.name;
        });

        return nextFieldsets;
    }
}