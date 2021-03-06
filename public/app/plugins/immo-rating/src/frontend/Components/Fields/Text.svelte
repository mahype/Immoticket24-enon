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

<section class="text {field.getClasses()}">
    <label>
        {field.label}
        <input type=text placeholder={field.placeholder} bind:value={field.value} on:blur={setValue} />
    </label>
    <Errors errors={errors} />
</section>
