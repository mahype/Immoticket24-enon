<script lang="ts">
    import {createEventDispatcher} from 'svelte';

    import type Field from '../../Classes/Field';
    import Errors from './Errors.svelte';

    export let field: Field;

    const dispatch = createEventDispatcher();

    $: errors = field.getValidationErors();

    const setValue = () => {      
        dispatch( 'update', field.fieldset.form );
    }
</script>

<legend>{field.label}</legend>

<div class="select-choice">
    <label>
        {field.label}
        <select bind:value={field.value} on:blur={setValue}>
            {#each field.choices as choice}
                <option value={choice.value}>{choice.label}</option>
            {/each}
        </select>
    </label>
    <Errors errors={errors} />
</div>