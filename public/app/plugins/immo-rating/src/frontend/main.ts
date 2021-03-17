import App from './App.svelte';
import './styles.scss';

let nonce;
let element = document.querySelector('#immorating');

console.log( "element" );
console.log( element );

if ( element !== null ) {
	let nonce = element.dataset.nonce;	

	const app = new App({
		target: element,
		props: {
			nonce
		}
	});

	
}

export default app;