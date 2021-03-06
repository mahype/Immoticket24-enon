<script lang="ts">
    import { createEventDispatcher } from 'svelte';
    import type Navigation from "../Classes/Navigation";

    const dispatch = createEventDispatcher();

    export let navigation: Navigation;

    $: showPrev = navigation.hasPrevFieldset();
    $: showNext = navigation.hasNextFieldset();
    $: showSend = navigation.hasSubmission();
    
    let loading = false;


    const navigate = ( direction: string ) => {
        switch ( direction ) {
            case 'prev': 
                navigation.prevFieldset();
                dispatch( 'navigate', navigation.form );
                break;
            case 'next':
                navigation.nextFieldset();                
                dispatch( 'navigate', navigation.form );
                break;
            case 'send':
                loading = true;
                navigation.submit()                    
                    .then( ( response ) => {
                        loading = false;                        
                        if( response.status === 200 ) {
                            navigation.form.sent = true;
                        }
                        
                        dispatch( 'navigate', navigation.form );
                    })
                    .catch ( ( error ) => {
                        loading = false;
                        dispatch( 'navigate', navigation.form );                        
                    });

                break;
        }
    }
</script>

<nav>
    {#if showPrev}
        <button on:click={ () => navigate('prev') }>&lt; Zurück</button>
    {/if}
    {#if showNext}
        <button on:click={ () => navigate('next') }>Weiter</button>
    {/if}
    {#if showSend}
        <button class:loading={loading} on:click={ () => navigate('send') }>Absenden</button>
    {/if}
</nav>

<style>
    .loading {
        background: url( 'images/loading.gif' ) no-repeat left;
    }
</style>