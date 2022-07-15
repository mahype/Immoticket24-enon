/**
 * Class StickyElement
 * 
 * Needed to stick HTML elements on top of the page.
 * 
 * @since 2022-02-09
 */
 export default class HamburgerMenu {
    /**
     * Show Hamburger menu until this width of site content.
     * 
     * @var int
     * 
     * @since 2022-02-09
     */
    protected untilWidth: number;

    /**
     * Show Hamburger menu until this width of site content.
     * 
     * @var int
     * 
     * @since 2020-02-02
     */
    protected currentWidth: number;

    /**
     * Current offset in px.
     * 
     * @var HTMLElement
     * 
     * @since 2022-02-09
     */
    protected navElement: HTMLElement;

     /**
     * Hamburger element.
     * 
     * @var HTMLElement
     * 
     * @since 2022-02-09
     */
    protected hamburger: HTMLElement;

    protected hamburgerSVG = '<svg id="Layer_1" style="enable-background:new 0 0 32 32;" version="1.1" viewBox="0 0 32 32" width="32px" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path d="M4,10h24c1.104,0,2-0.896,2-2s-0.896-2-2-2H4C2.896,6,2,6.896,2,8S2.896,10,4,10z M28,14H4c-1.104,0-2,0.896-2,2  s0.896,2,2,2h24c1.104,0,2-0.896,2-2S29.104,14,28,14z M28,22H4c-1.104,0-2,0.896-2,2s0.896,2,2,2h24c1.104,0,2-0.896,2-2  S29.104,22,28,22z"/></svg>';

     /**
     * Initializing class
     * 
     * @param selector   HTML selector for navigation.
     * @param untilWidth Show Hamburger menu until this width of site content.
     * 
     * @since 2022-02-09
     */
    constructor( selector: string, untilWidth: number ) {
        this.navElement = document.querySelector( selector );
        this.untilWidth = untilWidth;
        this.currentWidth = document.documentElement.clientWidth;

        this.addHamburger();
        this.render();

        window.addEventListener( 'resize', () => {
            this.currentWidth = document.documentElement.clientWidth;
            this.render();  
        });          
    }

    /**
     * Adds hamburger menu HTML.
     * 
     * @since 2022-02-09
     */
    protected addHamburger() {
        this.hamburger = document.createElement('div');
        this.hamburger.setAttribute('id', 'hamburger-menu');

        this.hamburger.classList.add('hamburger-menu');
        this.hamburger.classList.add('hide-menu'); // Hide on init
        
        this.hamburger.innerHTML = this.hamburgerSVG;

        document.body.append( this.hamburger );

        this.hamburger.addEventListener( 'click', () => {
            this.toggleNav();
        });
    }

    protected render() {
        if( this.currentWidth <= this.untilWidth ) {
            this.showHamburger();
        } else {
            this.hideHamburger();  
        }
    }

    protected toggleNav() {
        this.isNav() ? this.slideOutNav() : this.slideInNav();
    }

    protected isNav() {
        return this.navElement.classList.contains('menu-in');
    }
    
    protected showNav() {
        this.navElement.classList.remove('hide-menu');
    }

    protected hideNav() {                
        this.navElement.classList.add('hide-menu');
    }

    protected slideInNav() {
        this.navElement.classList.add('menu-in');
        this.navElement.classList.remove('menu-out');
    }

    protected slideOutNav() {
        this.navElement.classList.remove('menu-in');
        this.navElement.classList.add('menu-out');
    }
    
    protected showHamburger() {        
        this.hamburger.classList.remove('hide-menu');
    }

    protected hideHamburger() {
        this.hamburger.classList.add('hide-menu');
    }
 }