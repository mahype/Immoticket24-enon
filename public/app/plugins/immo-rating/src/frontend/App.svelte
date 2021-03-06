<script lang="ts">    
    import Form from './Components/Form.svelte';
    import FormData from './FormData.json';

    export let nonce : String;
</script>

<Form formData={FormData} nonce={nonce} />
