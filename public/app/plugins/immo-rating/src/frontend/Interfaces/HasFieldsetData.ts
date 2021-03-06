import type Field from "../Classes/Field";
import type HasSubmissionData from "./HasSubmissionData";
import type HasConditionData from "./HasConditionData";

/**
 * Fieldset data interface.
 * 
 * @since 1.0.0
 */
export default interface HasFieldsetData {
    readonly name          : string;
    readonly label         : string;    
    readonly percentage    : number;
    readonly params        : [];
    readonly classes       : [];
    readonly fieldsClasses : [];
    readonly fields        : Field[];
    readonly conditions    : HasConditionData;    
    readonly submission    : HasSubmissionData;

    readonly nextFieldset  : string;
    readonly prevFieldset  : string;
}