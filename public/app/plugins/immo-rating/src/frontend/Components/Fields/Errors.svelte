<script lang="ts">
    export let errors: [];
</script>

{#if errors !== undefined && errors.length > 0}
<div class="notices">
    <ul>
    {#each errors as error}
        <li>{error}</li>
    {/each}
    </ul>
</div>
{/if}