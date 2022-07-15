import HamburgerMenu from './HamburgerMenu';
import StickyElement from './StickyElement';

document.addEventListener( 'DOMContentLoaded', () => {
    // Page is in iframe > Do not load anything
    if ( window.location !== window.parent.location ) {
        return;
    }

    new StickyElement( '.sticky-header', 150 );
    new HamburgerMenu( 'header nav', 1079 );
});