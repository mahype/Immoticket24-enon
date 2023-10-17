<?php

class Grundriss {
    /**
     * Form des Grundrisses. Mögliche Werte: a, b, c, d.
     * 
     * @var string
     */
    protected string $form;

    /**
     * Längen der Wände.
     * 
     * @var array
     */
    protected array $waende;

    /**
     * Formen der Grundrisse.
     * 
     * @var array
     */
    protected array $formen = array(
		'a' => array(
			'a'   => array( true, 0 ),
			'b'   => array( true, 1 ),
			'c'   => array( 'a', 2 ),
			'd'   => array( 'b', 3 ),
			'fla' => array(
				array( 'a', 'b' ),
			),
		),
		'b' => array(
			'a'   => array( true, 0 ),
			'b'   => array( true, 1 ),
			'c'   => array( true, 2 ),
			'd'   => array( true, 3 ),
			'e'   => array( 'a - c', 2 ),
			'f'   => array( 'b - d', 3 ),
			'fla' => array(
				array( 'a', 'f' ),
				array( 'c', 'd' ),
			),
		),
		'c' => array(
			'a'   => array( true, 0 ),
			'b'   => array( true, 1 ),
			'c'   => array( true, 2 ),
			'd'   => array( true, 1 ),
			'e'   => array( true, 2 ),
			'f'   => array( 'd', 3 ),
			'g'   => array( 'a - c - e', 2 ),
			'h'   => array( 'b', 3 ),
			'fla' => array(
				array( 'a', 'b' ),
				array( 'd', 'e' ),
			),
		),
		'd' => array(
			'a'   => array( true, 0 ),
			'b'   => array( true, 1 ),
			'c'   => array( true, 2 ),
			'd'   => array( true, 3 ),
			'e'   => array( true, 2 ),
			'f'   => array( true, 1 ),
			'g'   => array( 'a - c - e', 2 ),
			'h'   => array( 'b - d + f', 3 ),
			'fla' => array(
				array( 'a', 'b - d' ),
				array( 'c', 'd' ),
				array( 'f', 'g' ),
			),
		),
	);

    /**
     * Himmelsrichtungen.
     * 
     * @var string[]
     */
    protected $himmelsrichtungen = array(
		's'  => __( 'Süden', 'wpenon' ),
		'so' => __( 'Südosten', 'wpenon' ),
		'o'  => __( 'Osten', 'wpenon' ),
		'no' => __( 'Nordosten', 'wpenon' ),
		'n'  => __( 'Norden', 'wpenon' ),
		'nw' => __( 'Nordwesten', 'wpenon' ),
		'w'  => __( 'Westen', 'wpenon' ),
		'sw' => __( 'Südwesten', 'wpenon' ),
	);

    /**
     * Konstruktor.
     * 
     * @param string $form Form a, b, c oder d.
     * @param string $ausrichtung Himmelsrichtung.
     */
    public function __construct( string $form, string $ausrichtung )
    {
        if( ! $this->form_existiert( $form ) ) {
            throw new Exception( 'Ungültige Form' );
        }

        if( ! $this->himmelsrichtung_existiert( $ausrichtung ) ) {
            throw new Exception( 'Ungültige Himmelsrichtung' );
        }

        $this->form = $form;        
    }
    
    /**
     * Prüft, ob die Form existiert.
     * 
     * @param string $form Name der Form (a, b, c, d).
     * @return bool True, wenn die Form existiert, sonst false.
     */
    public function form_existiert( string $form ): bool {
        return array_key_exists( $form, $this->formen );
    }

    /**
     * Prüft, ob die Himmelsrichtung existiert.
     * 
     * @param string $himmelsrichtung 
     * @return bool 
     */
    public function himmelsrichtung_existiert( string $himmelsrichtung ): bool {
        return array_key_exists( $himmelsrichtung, $this->himmelsrichtungen );
    }

    /**
     * Prüft, ob die Wand für die Form existiert.
     * 
     * @param string $wand Name der Wand (a, b, c, d, e, f, g, h).
     * @return bool True, wenn die Wand existiert, sonst false.
     */
    public function wand_existiert( string $wand ): bool {
        return array_key_exists( $wand, $this->formen[$this->form] );
    }

    /**
     * Prüft ob die länge der Wand gesetzt werden darf, oder automatisch berechnet wird.
     * 
     * @param string $wand Name der Wand (a, b, c, d, e, f, g, h).
     * @return bool True, wenn die Länge gesetzt werden darf, sonst false.
     */
    public function wandlaenge_einstellbar( string $wand ): bool {
        return $this->formen[$this->form][$wand][0] === true ? true : false;
    }

    /**
     * Gibt die Länge der Wand zurück.
     * @param string $wand 
     * @param float $length 
     * @return void 
     */
    public function setze_wandlaenge( string $wand, float $length ) {
        if( ! $this->wand_existiert( $wand ) ) {
            throw new Exception( 'Ungültige Wand' );
        }

        if( ! $this->wandlaenge_einstellbar( $wand ) ) {
            throw new Exception( 'Die Länge der Wand kann nicht gesetzt werden' );
        }

        $this->waende[$wand] = $length;
    }

    /**
     * Gibt die Form des Grundrisses zurück.
     * 
     * @return array
     */
    public function form(): array
    {
        return $this->formen[$this->form];
    }

    public function flaechenberechnungsformel(): array {
        return $this->formen[$this->form]['fla'];
    }
}