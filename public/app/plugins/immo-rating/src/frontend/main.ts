import App from './App.svelte';
import './styles.scss';

let element = document.querySelector('#immorating');
let nonce = element.dataset.nonce;

const app = new App({
	target: document.querySelector('#immorating'),
	props: {
        nonce
	}
});

export default app;