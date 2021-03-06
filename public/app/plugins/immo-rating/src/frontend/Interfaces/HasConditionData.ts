/**
 * Condition data interface.
 * 
 * @since 1.0.0
 */
export default interface HasConditionData {
    readonly field: string;
    readonly value: any;
    readonly operator: string;
}