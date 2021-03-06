/**
 * Field data interface.
 * 
 * @since 1.0.0
 */
export default interface HasFieldData {
    readonly name        : string;    
    readonly type        : string;
    readonly label       : string;
    readonly choices     : [];
    readonly params      : [];
    readonly classes     : [];
    readonly required    : boolean;
    readonly validations : [];
}