<script lang="ts">
    import {createEventDispatcher} from 'svelte';

    import type Field from "../../Classes/Field";
    import Errors from "./Errors.svelte";
    
    export let field: Field;

    const dispatch = createEventDispatcher();
    let errors = [];

    const setValue = () => {      
        errors = field.validate();
        dispatch( 'update', field.fieldset.form );
    }
</script>

<section class="range {field.getClasses()}">
    <label for="{field.name}">
        {field.label}:  {field.value} {#if field.params.unit !== undefined}{field.params.unit}{/if}
    </label>
    <input name="{field.name}" type=range bind:value={field.value} min={field.params.min} max={field.params.max} step={field.params.step}  />    
    <Errors errors={errors} />
</section>
