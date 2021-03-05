<script lang="ts">  
    import {fly,fade} from 'svelte/transition';

    import type HasFormData from '../Interfaces/HasFormData';

    import Form from '../Classes/Form';
    import Fieldset from './Fieldset.svelte';
    import Navigation from './Navigation.svelte';
    import Footer from './Footer.svelte';

    export let formData: HasFormData;
    export let nonce: String;
    
    let form = new Form( formData, nonce );

    let update = ( e ) => {
        form = e.detail;
    }

    $: showNavbar = form.navigation.getCurrentFieldset().name !== 'start';
</script>
{#if ! form.sent }
<form name={form.name} class={form.getClasses()} on:submit|preventDefault out:fade={{duration:500}}>
    <div class="fieldsets">
        {#each form.fieldsets as fieldset}
            {#if fieldset.name === form.navigation.getCurrentFieldset().name }
                <Fieldset fieldset={fieldset} on:update={update} />
            {/if}
        {:else}
            JSON data failure.
        {/each}
    </div>
    {#if showNavbar}
        <Navigation navigation={form.navigation} on:navigate={update} />
    {:else}
        <Footer />
    {/if}
</form> 
{:else}
    <div class="thank-you" in:fly={{x:200,duration:500,delay:500}}><p><strong>Vielen Dank für Ihre Anfrage!</strong><br />Wir werden uns in Kürze bei Ihnen melden</p></div>
{/if}

<style>
    .thank-you {
        width:100%;
        text-align: center;
        padding: 245px;
    }
    .fieldsets {
        display:flex;
        overflow: hidden;
    }
</style>