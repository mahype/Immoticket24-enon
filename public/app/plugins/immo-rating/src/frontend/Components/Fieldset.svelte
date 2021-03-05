<script lang="ts">
    import { createEventDispatcher } from 'svelte';
    import { fade } from 'svelte/transition';

    import type Fieldset from '../Classes/Fieldset';

    import Text from './Fields/Text.svelte';
    import Textarea from './Fields/Textarea.svelte';
    import Range from './Fields/Range.svelte';
    import SelectChoice from './Fields/SelectChoice.svelte';
    import RadioChoice from './Fields/RadioChoice.svelte';
    import ImageChoice from './Fields/ImageChoice.svelte'; 
    import Percentage from './Percentage.svelte';
    
    export let fieldset: Fieldset;

    $: fields = fieldset.fields;

    const dispatch = createEventDispatcher();

    function update( form ) {        
        dispatch( 'update', form.detail );
    }

    let percentageStart: number;

    switch( fieldset.form.navigation.getLastAction() ) {
        case 'prev':
            percentageStart = fieldset.form.navigation.getNextFieldset().percentage;
            break;
        case 'next':
            percentageStart = fieldset.form.navigation.getPrevFieldset().percentage;
            break;
        default:
            percentageStart = 0;
            break;
    }

    let percentageCurrent = fieldset.form.navigation.getCurrentFieldset().percentage;
</script>

<fieldset class={fieldset.getClasses()}>
    <legend>{fieldset.label}</legend>
    <Percentage start={percentageStart} percentage={percentageCurrent} />
    <div class="fields {fieldset.getFieldsClasses()}" out:fade={{duration:500}} in:fade={{duration:500,delay:500}}>
        {#each fields as field}
            {#if field.type === 'Text'}
                <Text field={field} on:update={update} />
            {:else if field.type === 'TextArea'}
                <Textarea field={field} on:update={update}  />
            {:else if field.type === 'Range'}
                <Range field={field} on:update={update}  />
            {:else if field.type === 'SelectChoice'}
                <SelectChoice field={field} on:update={update} />
            {:else if field.type === 'RadioChoice'}
                <RadioChoice field={field} on:update={update} />
            {:else if field.type === 'ImageChoice'}
                <ImageChoice field={field} on:update={update} />
            {/if}
        {/each}
    </div>
</fieldset>

<style>
    fieldset {
        flex: 1 0 auto;
        overflow: hidden;
        position: relative;
        top: 0;
    }
</style>
