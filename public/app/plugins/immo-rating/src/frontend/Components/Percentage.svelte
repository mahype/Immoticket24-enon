<script>
    import { tweened } from "svelte/motion";
    export let start;
    export let percentage;

    let height = '5px';
    let color  = '#41a62a';
    let bgColor = '#f3efe9';
    let text   = false;
    let textColor   = '#FFFFFF';

    const barSize = tweened(start, {delay:1000});
    $: barSize.set(percentage);
</script>

<div class="percentage" style="height: {height}; line-height: {height}; background-color:{bgColor};">
    <div class="percentage-bar" style="width:{$barSize}%; height: {height}; background-color: {color};" />
    {#if text === true}
        <div class="percentage-text" style="height: {height}: color {textColor};">{percentage}%</div>
    {/if}
</div>

<style>
    .percentage {
        position: relative;
        width: 100%;
        margin: 0;
        padding: 0;
        z-index: 1;
    }

    .percentage-bar {
        position: absolute;
        width: 0%;
        z-index: 2;
    }

    .percentage-text {
        position: relative;
        text-align: center;
        width: 100%;
        z-index: 3;
    }
</style>
