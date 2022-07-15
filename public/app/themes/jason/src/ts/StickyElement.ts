/**
 * Class StickyElement
 * 
 * Needed to stick HTML elements on top of the page.
 * 
 * @since 2020-02-02
 */
 export default class StickyElement {
    /**
     * Current offset in px.
     * 
     * @var int
     * 
     * @since 2020-02-02
     */
    protected currentOffset: number;

    /**
     * Offset where the element has to stick.
     * 
     * @var int
     * 
     * @since 2020-02-02
     */
    protected elementOffset: number;

    /**
     * Padding used for internal calc.
     * 
     * @var number
     * 
     * @since 2020-02-02
     */
     protected padding: number;

    /**
     * Targeted HTML element.
     * 
     * @var int
     * 
     * @since 2020-02-02
     */
    protected element: HTMLElement;

    /**
     * Initializing class
     * 
     * @param selector HTML selector.
     * @param offset   Offset to top of the page.
     * 
     * @since 2020-02-02
     */
    constructor( selector: string, offset: number ) {
        this.elementOffset = offset;
        this.element = document.querySelector( selector );
        this.padding = parseInt( window.getComputedStyle( this.element ).paddingLeft );
        
        document.addEventListener( 'scroll', () => {
            this.currentOffset = window.scrollY;

            if ( this.currentOffset > this.elementOffset ) {
                this.activateSticky();          
            } else {
                this.deactivateSticky();
            }
        });
    }

    /**
     * Activate stickyness.
     * 
     * @since 2020-02-02
     */
    private activateSticky() {
        let width = this.element.parentElement.clientWidth;
        this.element.classList.add( 'sticky' );
        this.element.setAttribute( 'style', 'width:' + width + 'px; padding-left:' + this.padding + '; padding-right:' + this.padding );
    }

    /**
     * Deactivate stickyness.
     * 
     * @since 2020-02-02
     */
    private deactivateSticky() {
        this.element.classList.remove( 'sticky' );
        this.element.removeAttribute('style');
        this.element.setAttribute( 'style', 'padding-bottom:0px; padding-top:20px;' );
    }
}