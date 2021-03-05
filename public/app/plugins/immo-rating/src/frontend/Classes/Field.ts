import type HasFieldData from '../Interfaces/HasFieldData';
import type Fieldset from './Fieldset';
import Validation from './Validation';

/**
 * Field class.
 * 
 * @since 1.0.0
 */
export default class Field implements HasFieldData {
    readonly fieldset    : Fieldset;
    readonly name        : string;
    readonly type        : string;    
    readonly label       : string;
    readonly choices     : [];
    readonly params      : [];
    readonly classes     : [];
    readonly required    : boolean;
    readonly validations : [];

    private  value       : any;
    private  errors      : [] = [];

    /**
     * Initializing field.
     * 
     * @param name  Name of the field.
     * @param field Field object
     * 
     * @since 1.0.0
     */
    public constructor(
        fieldset : Fieldset,
        field    : Field
    ){
        this.fieldset     = fieldset;
        this.name         = field.name;
        this.type         = field.type;
        this.label        = field.label;
        this.choices      = field.choices === undefined ? []: field.choices;
        this.params       = field.params === undefined ? []: field.params;
        this.classes      = field.classes === undefined ? []: field.classes;
        this.required     = field.required === undefined ? false: true;        
        this.validations  = field.validations === undefined ? []: field.validations;

        this.value        = field.value;
    }

    /**
     * Set value of field.
     * 
     * @param value Value to set.
     * 
     * @since 1.0.0
     */
    public setValue( value: any ) {
        this.value = value;
    }

    /**
     * Get value of field.
     * 
     * @param value Value to set.
     * 
     * @since 1.0.0
     */
    public getValue() : any {
        return this.value;
    }

    /**
     * Does field have choices.
     * 
     * @return True if it has choices, false if not.
     * 
     * @since 1.0.0
     */
    public hasChoices() {
        return this.choices.length > 0;
    }

    /**
     * Add a CSS class to field.
     * 
     * @param className CSS class name.
     * 
     * @since 1.0.0
     */
    public addClass( className: string ) : void {
        this.classes.push( className );
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

    /**
     * Validate the field.
     * 
     * @return Array of errors, empty array on no errors.
     * 
     * @since 1.0.0
     */
    public validate() : [] {
        let validation = new Validation( this.value, this.validations, this.required );
        this.errors = validation.check();
        
        if ( this.errors.length > 0 ) {
            this.addClass( 'error' );
        } else {
            this.classes.forEach( ( value, i ) => {
                if ( value === 'error' ) this.classes.splice( i, 1 );
            });
        }

        return this.errors;
    }

    /**
     * Get validation errors.
     * 
     * @return Erros which occured while validating.
     * 
     * @since 1.0.0
     */
    public getValidationErors() : string[] {
        return this.errors;
    }

    /**
     * Has field validation errors.
     * 
     * @return True if field has errors, false if not.
     * 
     * @since 1.0.0
     */
    public hasValidationErrors() : boolean {
        if ( this.errors.length > 0 ) {            
            return true;
        }

        return false;
    }
}