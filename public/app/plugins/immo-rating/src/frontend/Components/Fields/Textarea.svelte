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

<div class="textarea">
    <label>
        {field.label}
        <textarea placeholder={field.placeholder} bind:value={field.value} on:blur={setValue}></textarea>
    </label>
    <Errors errors={errors} />   
</div>
