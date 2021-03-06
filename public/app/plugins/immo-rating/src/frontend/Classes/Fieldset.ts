import type Form from './Form';
import type HasFieldsetData from '../Interfaces/HasFieldsetData';
import type HasConditionData from '../Interfaces/HasConditionData';

import Field from './Field';
import type HasSubmissionData from '../Interfaces/HasSubmissionData';


/**
 * Class Fieldset.
 * 
 * @since 1.0.0
 */
export default class Fieldset implements HasFieldsetData {
    readonly form          : Form;
    readonly name          : string;
    readonly label         : string;
    readonly percentage    : number;
    readonly params        : [];  
    readonly classes       : [];
    readonly fieldsClasses : [];    
    readonly conditions    : HasConditionData;
    readonly submission    : HasSubmissionData;

    readonly nextFieldset: string;
    readonly prevFieldset: string;    

    readonly fields      : Field[] = [];
   
    /**
     * Initializing fieldset.
     * 
     * @param form Form object.
     * @param fieldset Fieldset data.
     * 
     * @since 1.0.0
     */
    public constructor( form: Form, fieldset: HasFieldsetData ) {
        this.form           = form;
        this.name           = fieldset.name;
        this.label          = fieldset.label;
        this.percentage     = fieldset.percentage;
        this.params         = undefined === fieldset.params ? []: fieldset.params;
        this.classes        = undefined === fieldset.classes ? []: fieldset.classes;
        this.fieldsClasses  = undefined === fieldset.fieldsClasses ? []: fieldset.fieldsClasses;
        this.conditions     = fieldset.conditions;
        this.submission     = fieldset.submission;

        this.nextFieldset = fieldset.nextFieldset;
        this.prevFieldset = fieldset.prevFieldset;
        
        fieldset.fields.forEach( field => {
            this.fields.push( new Field( this, field ) );
        });
    }

    public conditionsFullfilled() : boolean {
        if ( this.conditions === undefined ) {
            return true;
        }
        
        let fullfillments = [];

        this.conditions.forEach( ( condition: HasConditionData ) => {
            let fullfilled = false;
            let field = this.form.getField( condition.field );

            switch ( condition.operator ) {
                case '==':
                    fullfilled = condition.value === field.getValue();
                    break;
                case '!=':
                    fullfilled = condition.value !== field.getValue();
                    break;
                case '>':
                    fullfilled = condition.value !== field.getValue();
                    break;                    
                case '<':
                    fullfilled = condition.value !== field.getValue();
                    break;
                default:
                    throw new Error( 'Operator "' + condition.operator + '" does not exist.')                             
            }

            fullfillments.push( fullfilled );
        });

        

        return ! fullfillments.includes( false );
    }

    /**
     * Is there a submission to do?
     * 
     * @return True if there is a submissionto do, false if not.
     * 
     * @since 1.0.0
     */
    public hasSubmission(): boolean {
        return this.submission !== undefined;
    }

    /**
     * Get CSS classes.
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



    /**
     * Get CSS field classes.
     * 
     * @return String of CSS classes.
     * 
     * @since 1.0.0
     */
    public getFieldsClasses(): string {
        if ( this.fieldsClasses.length > 0  ) {
            return this.fieldsClasses.join(' ');
        }

        return '';
    }

    /**
     * Validate fieldset.
     * 
     * @return True on successful validation, false on errors.
     * 
     * @since 1.0.0
     */
    public validate () : boolean {
        let foundError = false;
        this.fields.forEach( ( field: Field, i ) => {
            if ( field.validate().length > 0 && ! foundError ) {
                foundError = true;                
            }
        });

        return foundError;
    }

    /**
     * Has fieldset validation errors.
     * 
     * @return True if fieldset has errors, false if not.
     * 
     * @since 1.0.0
     */
    public hasValidationErrors() : boolean {
        let foundError = false;
        this.fields.forEach( ( field: Field ) => {
            if( field.hasValidationErrors() && ! foundError ) {
                foundError = true;
            }
        });

        return foundError;
    }
}