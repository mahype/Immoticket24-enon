/**
 * Validation methods
 * 
 * @since 1.0.0
 */
export default class ValidationMedhods {
    /**
     * Is value of tye number?
     * 
     * @param value Value which have to be checked.
     * @return boolean True if is of type number, false if not.
     * 
     * @since 1.0.0
     */
    static number( value: any ) {
        if ( typeof value === 'number' || value instanceof Number ) {
            return true;
        }

        return false;
    }

    /**
     * Is value of tye string?
     * 
     * @param value Value which have to be checked.
     * @return boolean True if is of type string, false if not.
     * 
     * @since 1.0.0
     */
    static string( value: any ) {
        if ( typeof value === 'string' || value instanceof String ) {
            return true;
        }

        return false;
    }

    /**
     * Is value of tye email?
     * 
     * @param value Value which have to be checked.
     * @return boolean True if is of type email, false if not.
     * 
     * @since 1.0.0
     */
    static email( value: string ) {
        const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test( String( value ).toLowerCase() );
    }

    /**
     * Is number not under min?
     * 
     * @param value Value which have to be checked.
     * @param min Max number.
     * 
     * @return boolean True if number not under min.
     * 
     * @since 1.0.0
     */
    static min( value: number, min: number ) {
        return ! ( value < min );
    }

    /**
     * Is number not over max?
     * 
     * @param value Value which have to be checked.
     * @param min Min number.
     * 
     * @return boolean True if number not over max.
     * 
     * @since 1.0.0
     */
    static max( value: number, max: number ) {
        return ! ( value > max );
    }

    /**
     * Is string not under min length?
     * 
     * @param value Value which have to be checked.
     * @param min Max number of chars.
     * 
     * @return boolean True if string length is not under min length.
     * 
     * @since 1.0.0
     */
    static minLength( value: string, min: number ) {
        return ! ( value.length < min );
    }

    /**
     * Is string not over max length?
     * 
     * @param value Value which have to be checked.
     * @param min Max number of chars.
     * 
     * @return boolean True if string length is not over max length.
     * 
     * @since 1.0.0
     */
    static maxLength( value: string, max: number ) {
        return ! ( value.length > max );
    }

    /**
     * Is value empty?
     * 
     * @param value Value which have to be checked.
     * @return boolean True if is empty, false if not.
     * 
     * @since 1.0.0
     */
    static empty( value: any ) : boolean {
        if( value === undefined || value.trim() === '' ) {
            return true;
        }
        return false;
    }

    static inArray( value: any , values = [] as any[] ) : boolean {
        return values.includes( value );
    }   
}