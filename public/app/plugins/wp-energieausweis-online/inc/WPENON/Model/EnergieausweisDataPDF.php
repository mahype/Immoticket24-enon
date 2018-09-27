<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

namespace WPENON\Model;

class EnergieausweisDataPDF extends \WPENON\Util\UFPDI {
  private $wpenon_title    = '';
  private $wpenon_type     = 'bw';
  private $wpenon_standard = 'enev2013';

  private $wpenon_img_path = '';
  private $wpenon_pdf_path = '';

  private $wpenon_width = 210;
  private $wpenon_height = 295;
  private $wpenon_margin_h = 0;
  private $wpenon_margin_v = 0;

  private $wpenon_energieausweis = null;
  private $wpenon_payment        = null;

  public function __construct( $title, $type, $standard ) {
    $this->wpenon_title    = $title;
    $this->wpenon_type     = $type;
    $this->wpenon_standard = $standard;

    $this->wpenon_img_path = WPENON_PATH . '/assets/img/pdf/';
    $this->wpenon_pdf_path = WPENON_PATH . '/assets/pdf/';

    $this->wpenon_colors = array(
      'text'        => array( 0, 0, 0 ),
      'boxbg'       => array( 228, 230, 240 ),
      'boxborder'   => array( 218, 218, 218 ),
      'checkboxbg'  => array( 223, 234, 231 ),
    );
    $this->wpenon_fonts = array(
      'default'     => array( 'Arial', '', 13, 8 ),
      'marker'      => array( 'Arial', 'B', 16, 8 ),
    );

    parent::__construct( 'P', 'mm', 'A4' );

    $this->SetTitle( $this->wpenon_title );
    $this->SetAutoPageBreak( false );
    $this->SetMargins( $this->wpenon_margin_h, $this->wpenon_margin_v, $this->wpenon_margin_h );
    $this->SetPageTextColor( 'text' );
    $this->SetPageFillColor( 'boxbg' );
    $this->SetPageDrawColor( 'boxborder' );
  }

  public function create( $energieausweis ) {
    if ( ! is_a( $energieausweis, '\WPENON\Model\Energieausweis' ) ) {
      return;
    }

    $this->energieausweis  = $energieausweis;
    $this->wpenon_type     = $this->energieausweis->wpenon_type;
    $this->wpenon_standard = $this->energieausweis->wpenon_standard;

    $this->setSourceFile( $this->wpenon_pdf_path . 'energieausweis_data_' . $this->wpenon_type . '.pdf' );
    $page_count = 'vw' === $this->wpenon_type ? 7 : 9;
    for ( $i = 0; $i < $page_count; $i++ ) {
      $this->addPage( 'P', array( $this->wpenon_width, $this->wpenon_height ) );
      $this->useTemplate( $this->importPage( $i + 1, '/MediaBox' ), $this->wpenon_margin_h, $this->wpenon_margin_v, $this->wpenon_width - 2 * $this->wpenon_margin_h );
      $this->renderPage( $i + 1 );
    }
  }

  public function finalize( $output_mode = 'I' ) {
    return $this->Output( $this->wpenon_title . '.pdf', $output_mode );
  }

  private function renderPage( $index ) {
    switch ( $index ) {
      case 1:
        $this->SetPageFont( 'default' );
        $this->SetXY( 39, 79 );
        $this->WriteCell( $this->GetData( 'kontakt_name' ), 'L', 2, 63 );
        $this->SetXY( 39, 89.25 );
        $this->WriteCell( $this->GetData( 'kontakt_adresse_strassenr' ), 'L', 2, 63 );
        $this->SetXY( 39, 99.5 );
        $this->WriteCell( $this->GetData( 'kontakt_adresse_plz' ) . ' ' . $this->GetData( 'kontakt_adresse_ort' ), 'L', 2, 63 );
        $this->SetXY( 39, 109.75 );
        $this->WriteCell( $this->GetData( 'kontakt_telefon' ), 'L', 2, 63 );
        $this->SetXY( 39, 120 );
        $this->WriteCell( $this->GetData( 'wpenon_email' ), 'L', 2, 63 );
        $this->SetXY( 134.5, 79 );
        $this->WriteCell( $this->GetData( 'adresse_strassenr' ), 'L', 2, 63 );
        $this->SetXY( 134.5, 89.25 );
        $this->WriteCell( $this->GetData( 'adresse_plz' ) . ' ' . $this->GetData( 'adresse_ort' ), 'L', 2, 63 );
        $anlass = $this->GetData( 'anlass' );
        $this->CheckBox( 15, 160, true );
        $this->CheckBox( 101, 160, true );
        $this->CheckBox( 168, 160, true );
        switch ( $anlass ) {
          case 'modernisierung':
            $this->CheckBox( 15, 160 );
            break;
          case 'vermietung':
          case 'verkauf':
            $this->CheckBox( 101, 160 );
            break;
          case 'sonstiges':
          default:
            $this->CheckBox( 168, 160 );
            break;
        }
        $this->SetXY( 31, 182.6 );
        $this->WriteCell( $this->GetData( 'baujahr' ), 'L', 2, 71 );
        $this->SetXY( 156.3, 182.6 );
        $this->WriteCell( $this->GetData( 'wohnungen' ), 'L', 2, 41.2 );
        if ( 'vw' === $this->wpenon_type ) {
          $this->SetXY( 39, 193.3 );
          $this->WriteCell( $this->GetData( 'flaeche' ), 'L', 2, 151 );
          $gebaeudetyp = $this->GetData( 'gebaeudetyp' );
          $this->CheckBox( 15, 218, true );
          $this->CheckBox( 111, 218, true );
          $this->CheckBox( 15, 227, true );
          $this->CheckBox( 111, 227, true );
          $this->CheckBox( 15, 236, true );
          switch ( $gebaeudetyp ) {
            case 'freistehend':
              $this->CheckBox( 15, 218 );
              break;
            case 'doppelhaushaelfte':
              $this->CheckBox( 111, 218 );
              break;
            case 'reiheneckhaus':
              $this->CheckBox( 15, 227 );
              break;
            case 'reihenhaus':
              $this->CheckBox( 111, 227 );
              break;
            case 'sonstiges':
            default:
              $this->CheckBox( 15, 236 );
              break;
          }
          $gebaeudeteil = $this->GetData( 'gebaeudeteil' );
          $this->CheckBox( 15, 282, true );
          $this->CheckBox( 111, 282, true );
          switch ( $gebaeudeteil ) {
            case 'gemischt':
              $this->CheckBox( 111, 282 );
              break;
            case 'gesamt':
            default:
              $this->CheckBox( 15, 282 );
              break;
          }
        } else {
          $this->CheckBox( 15, 205, true );
          $this->CheckBox( 111, 205, true );
          $this->CheckBox( 15, 214, true );
          $this->CheckBox( 111, 214, true );
          $this->CheckBox( 15, 223, true );
          switch ( $gebaeudetyp ) {
            case 'freistehend':
              $this->CheckBox( 15, 205 );
              break;
            case 'doppelhaushaelfte':
              $this->CheckBox( 111, 205 );
              break;
            case 'reiheneckhaus':
              $this->CheckBox( 15, 214 );
              break;
            case 'reihenhaus':
              $this->CheckBox( 111, 214 );
              break;
            case 'sonstiges':
            default:
              $this->CheckBox( 15, 223 );
              break;
          }
          $gebaeudeteil = $this->GetData( 'gebaeudeteil' );
          $this->CheckBox( 15, 278, true );
          $this->CheckBox( 111, 278, true );
          switch ( $gebaeudeteil ) {
            case 'gemischt':
              $this->CheckBox( 111, 278 );
              break;
            case 'gesamt':
            default:
              $this->CheckBox( 15, 278 );
              break;
          }
        }
        break;
      case 2:
        $this->SetPageFont( 'default' );
        if ( 'vw' === $this->wpenon_type ) {
          $this->SetXY( 78, 70.3 );
          $this->WriteCell( $this->GetData( 'regenerativ_art' ), 'L', 2, 119.5 );
          $this->SetXY( 97, 96 );
          $this->WriteCell( $this->GetData( 'regenerativ_nutzung' ), 'L', 2, 100.5 );
          $this->CheckBox( 15, 111, ! $this->GetData( 'regenerativ_aktiv' ) );
          $wanddaemmung = $this->GetData( 'wand_daemmung' );
          if ( empty( $wanddaemmung ) ) {
            $wanddaemmung = 0;
          }
          $this->SetXY( 50, 144.5 );
          $this->WriteCell( $wanddaemmung, 'L', 2, 139.5 );
          $bodendaemmung = $this->GetData( 'boden_daemmung' );
          if ( empty( $bodendaemmung ) ) {
            $bodendaemmung = 0;
          }
          $this->SetXY( 50, 155.5 );
          $this->WriteCell( $bodendaemmung, 'L', 2, 139.5 );
          $dach = $this->GetData( 'dach' );
          $this->CheckBox( 15, 207, true );
          $this->CheckBox( 79, 207, true );
          $this->CheckBox( 142, 207, true );
          switch ( $dach ) {
            case 'beheizt':
              $this->CheckBox( 142, 207 );
              break;
            case 'unbeheizt':
              $this->CheckBox( 79, 207 );
              break;
            case 'nicht-vorhanden':
            default:
              $this->CheckBox( 15, 207 );
              break;
          }
          $dachdaemmung = $this->GetData( 'dach_daemmung' );
          if ( empty( $dachdaemmung ) || 'beheizt' !== $dach ) {
            $dachdaemmung = 0;
          }
          $this->SetXY( 47, 218.5 );
          $this->WriteCell( $dachdaemmung, 'L', 2, 142.5 );
          $keller = $this->GetData( 'keller' );
          $this->CheckBox( 15, 270, true );
          $this->CheckBox( 79, 270, true );
          $this->CheckBox( 142, 270, true );
          switch ( $keller ) {
            case 'beheizt':
              $this->CheckBox( 142, 270 );
              break;
            case 'unbeheizt':
              $this->CheckBox( 79, 270 );
              break;
            case 'nicht-vorhanden':
            default:
              $this->CheckBox( 15, 270 );
              break;
          }
          $kellerdaemmung = $this->GetData( 'keller_daemmung' );
          if ( empty( $kellerdaemmung ) || 'beheizt' !== $keller ) {
            $kellerdaemmung = 0;
          }
          $this->SetXY( 59, 275.5 );
          $this->WriteCell( $kellerdaemmung, 'L', 2, 130.5 );
        } else {
          $this->SetXY( 78, 50.3 );
          $this->WriteCell( $this->GetData( 'regenerativ_art' ), 'L', 2, 119.5 );
          $this->SetXY( 97, 74 );
          $this->WriteCell( $this->GetData( 'regenerativ_nutzung' ), 'L', 2, 100.5 );
          $this->CheckBox( 15, 93, ! $this->GetData( 'regenerativ_aktiv' ) );
          $grundriss_form = $this->GetData( 'grundriss_form' );
          $this->CheckBox( 47, 163.5, true );
          $this->CheckBox( 142, 163.5, true );
          $this->CheckBox( 47, 205, true );
          $this->CheckBox( 142, 205, true );
          switch ( $grundriss_form ) {
            case 'd':
              $this->CheckBox( 142, 205 );
              break;
            case 'c':
              $this->CheckBox( 47, 205 );
              break;
            case 'b':
              $this->CheckBox( 142, 163.5 );
              break;
            case 'a':
            default:
              $this->CheckBox( 47, 163.5 );
              break;
          }
          $grundriss_richtung = $this->GetData( 'grundriss_richtung' );
          $this->CheckBox( 15, 229, true );
          $this->CheckBox( 63, 229, true );
          $this->CheckBox( 111, 229, true );
          $this->CheckBox( 158, 229, true );
          $this->CheckBox( 15, 238, true );
          $this->CheckBox( 63, 238, true );
          $this->CheckBox( 111, 238, true );
          $this->CheckBox( 158, 238, true );
          switch ( $grundriss_richtung ) {
            case 'o':
              $this->CheckBox( 63, 229 );
              break;
            case 'w':
              $this->CheckBox( 111, 229 );
              break;
            case 's':
              $this->CheckBox( 158, 229 );
              break;
            case 'no':
              $this->CheckBox( 15, 238 );
              break;
            case 'so':
              $this->CheckBox( 63, 238 );
              break;
            case 'sw':
              $this->CheckBox( 111, 238 );
              break;
            case 'nw':
              $this->CheckBox( 158, 238 );
              break;
            case 'n':
            default:
              $this->CheckBox( 15, 229 );
              break;
          }
        }
        break;
      case 3:
        $this->SetPageFont( 'default' );
        if ( 'vw' === $this->wpenon_type ) {
          $fenster_bauart = $this->GetData( 'fenster_bauart' );
          $this->CheckBox( 15, 72, true );
          $this->CheckBox( 111, 72, true );
          $this->CheckBox( 15, 81, true );
          $this->CheckBox( 111, 81, true );
          $this->CheckBox( 15, 90, true );
          $this->CheckBox( 111, 90, true );
          $this->CheckBox( 15, 99, true );
          switch ( $fenster_bauart ) {
            case 'holzeinfach':
              $this->CheckBox( 15, 81 );
              break;
            case 'holzdoppelt':
              $this->CheckBox( 111, 72 );
              break;
            case 'kunststoff':
              $this->CheckBox( 111, 81 );
              break;
            case 'stahl':
              $this->CheckBox( 15, 90 );
              break;
            case 'waermedaemmglass':
              $this->CheckBox( 111, 90 );
              break;
            case 'waermedaemmglass2fach':
              $this->CheckBox( 15, 99 );
              break;
            case 'aluminium':
            default:
              $this->CheckBox( 15, 72 );
              break;
          }
          $this->SetXY( 55, 104.5 );
          $this->WriteCell( $this->GetData( 'fenster_baujahr' ), 'L', 2, 142.5 );
          $h_erzeugung = $this->GetData( 'h_erzeugung' );
          $this->CheckBox( 15, 165, true );
          $this->CheckBox( 111, 165, true );
          $this->CheckBox( 15, 174, true );
          $this->CheckBox( 111, 174, true );
          $this->CheckBox( 15, 183, true );
          $this->CheckBox( 111, 183, true );
          $this->CheckBox( 15, 192, true );
          $this->CheckBox( 111, 192, true );
          $this->CheckBox( 15, 201, true );
          $this->CheckBox( 111, 201, true );
          $this->CheckBox( 15, 210, true );
          $this->CheckBox( 111, 210, true );
          $this->CheckBox( 15, 219, true );
          $this->CheckBox( 111, 219, true );
          $this->CheckBox( 15, 228, true );
          $this->CheckBox( 111, 228, true );
          $this->CheckBox( 15, 237, true );
          switch ( $h_erzeugung ) {
            case 'niedertemperaturkessel':
              $this->CheckBox( 111, 165 );
              break;
            case 'brennwertkessel':
              $this->CheckBox( 15, 174 );
              break;
            case 'brennwertkesselverbessert':
              $this->CheckBox( 111, 174 );
              break;
            case 'fernwaerme':
              $this->CheckBox( 15, 183 );
              break;
            case 'waermepumpeluft':
              $this->CheckBox( 111, 183 );
              break;
            case 'waermepumpewasser':
              $this->CheckBox( 15, 192 );
              break;
            case 'waermepumpeerde':
              $this->CheckBox( 111, 192 );
              break;
            case 'kleinthermeniedertemperatur':
              $this->CheckBox( 15, 201 );
              break;
            case 'kleinthermebrennwert':
              $this->CheckBox( 111, 201 );
              break;
            case 'kohleholzofen':
              $this->CheckBox( 15, 210 );
              break;
            case 'gasraumheizer':
              $this->CheckBox( 111, 210 );
              break;
            case 'elektronachtspeicherheizung':
              $this->CheckBox( 15, 219 );
              break;
            case 'elektrodirektheizgeraet':
              $this->CheckBox( 111, 219 );
              break;
            case 'stueckholzfeuerung':
              $this->CheckBox( 15, 228 );
              break;
            case 'pelletfeuerung':
              $this->CheckBox( 111, 228 );
              break;
            case 'oelofenverdampfungsbrenner':
              $this->CheckBox( 15, 237 );
              break;
            case 'standardkessel':
            default:
              $this->CheckBox( 15, 165 );
              break;
          }
        } else {
          $anbau = $this->GetData( 'anbau' );
          $anbau_form = $this->GetData( 'anbau_form' );
          $this->CheckBox( 47, 82, true );
          $this->CheckBox( 142, 82, true );
          if ( $anbau ) {
            switch ( $anbau_form ) {
              case 'b':
                $this->CheckBox( 142, 82 );
                break;
              case 'a':
              default:
                $this->CheckBox( 47, 82 );
                break;
            }
          }
          $this->SetXY( 64, 88 );
          $this->WriteCell( $anbau ? $this->GetData( 'anbau_baujahr' ) : '', 'L', 2, 128.5 );
          $this->SetXY( 66, 134 );
          $this->WriteCell( $this->GetData( 'geschoss_zahl' ), 'L', 2, 131.5 );
          $this->SetXY( 46, 153 );
          $this->WriteCell( $this->GetData( 'geschoss_hoehe', true ), 'L', 2, 146.5 );
          $this->SetXY( 111, 166 );
          $this->WriteCell( $anbau ? $this->GetData( 'anbau_hoehe' ) : '', 'L', 2, 76.5 );
          $wand_bauart = $this->GetData( 'wand_bauart' );
          $this->CheckBox( 15, 220, true );
          $this->CheckBox( 110, 220, true );
          switch ( $wand_bauart ) {
            case 'holz':
              $this->CheckBox( 110, 220 );
              break;
            case 'massiv':
            default:
              $this->CheckBox( 15, 220 );
              break;
          }
          $this->SetXY( 33, 240 );
          $this->WriteCell( number_format_i18n( $this->GetData( 'wand_a_laenge' ), 2 ), 'L', 2, 58.5 );
          $this->CheckBox( 20, 253, ! $this->GetData( 'wand_a_nachbar' ) );
          $wand_a_daemmung = $this->GetData( 'wand_a_daemmung' );
          if ( empty( $wand_a_daemmung ) ) {
            $wand_a_daemmung = 0;
          }
          $this->SetXY( 41, 256 );
          $this->WriteCell( $wand_a_daemmung, 'L', 2, 48.5 );
          $this->SetXY( 128, 240 );
          $this->WriteCell( number_format_i18n( $this->GetData( 'wand_b_laenge' ), 2 ), 'L', 2, 58.5 );
          $this->CheckBox( 115, 253, ! $this->GetData( 'wand_b_nachbar' ) );
          $wand_b_daemmung = $this->GetData( 'wand_b_daemmung' );
          if ( empty( $wand_b_daemmung ) ) {
            $wand_b_daemmung = 0;
          }
          $this->SetXY( 136, 256 );
          $this->WriteCell( $wand_b_daemmung, 'L', 2, 48.5 );
        }
        break;
      case 4:
        $this->SetPageFont( 'default' );
        if ( 'vw' === $this->wpenon_type ) {
          $h_energietraeger = $this->GetData( 'h_energietraeger' );
          $this->CheckBox( 15, 48, true );
          $this->CheckBox( 52, 48, true );
          $this->CheckBox( 117, 48, true );
          $this->CheckBox( 153, 48, true );
          $this->CheckBox( 15, 57, true );
          $this->CheckBox( 52, 57, true );
          $this->CheckBox( 117, 57, true );
          $this->CheckBox( 153, 57, true );
          $this->CheckBox( 15, 66, true );
          $this->CheckBox( 52, 66, true );
          $this->CheckBox( 117, 66, true );
          $this->CheckBox( 153, 66, true );
          $this->CheckBox( 15, 75, true );
          $this->CheckBox( 52, 75, true );
          $this->CheckBox( 117, 75, true );
          $this->CheckBox( 15, 84, true );
          $this->CheckBox( 15, 93, true );
          $this->CheckBox( 15, 102, true );
          $this->CheckBox( 15, 111, true );
          switch ( $h_energietraeger ) {
            case 'heizoelbiooel':
              $this->CheckBox( 52, 48 );
              break;
            case 'biooel':
              $this->CheckBox( 117, 48 );
              break;
            case 'erdgas':
              $this->CheckBox( 153, 48 );
              break;
            case 'koks':
              $this->CheckBox( 15, 57 );
              break;
            case 'erdgasbiogas':
              $this->CheckBox( 52, 57 );
              break;
            case 'biogas':
              $this->CheckBox( 117, 57 );
              break;
            case 'fluessiggas':
              $this->CheckBox( 153, 57 );
              break;
            case 'steinkohle':
              $this->CheckBox( 15, 66 );
              break;
            case 'braunkohle':
              $this->CheckBox( 52, 66 );
              break;
            case 'stueckholz':
              $this->CheckBox( 117, 66 );
              break;
            case 'holzhackschnitzel':
              $this->CheckBox( 153, 66 );
              break;
            case 'holzpellets':
              $this->CheckBox( 15, 75 );
              break;
            case 'strom':
              $this->CheckBox( 52, 75 );
              break;
            case 'sonneneinstrahlung':
              $this->CheckBox( 117, 75 );
              break;
            case 'fernwaermehzwfossil':
              $this->CheckBox( 15, 84 );
              break;
            case 'fernwaermehzwregenerativ':
              $this->CheckBox( 15, 93 );
              break;
            case 'fernwaermekwkfossil':
            case 'fernwaermekwkfossilbio':
              $this->CheckBox( 15, 102 );
              break;
            case 'fernwaermekwkregenerativ':
              $this->CheckBox( 15, 111 );
              break;
            case 'heizoel':
            default:
              $this->CheckBox( 15, 48 );
              break;
          }
          $this->SetXY( 73, 118 );
          $this->WriteCell( $this->GetData( 'h_baujahr' ), 'L', 2, 124.5 );
          if ( $this->GetData( 'h2_info' ) ) {
            $this->CheckBox( 20, 148 );
            $this->CheckBox( 115, 148, true );
            $this->SetXY( 86, 154 );
            $this->WriteCell( $this->GetData( 'h2_erzeugung', true ), 'L', 2, 106.5 );
            $this->SetXY( 95, 165 );
            $this->WriteCell( $this->GetData( 'h2_baujahr' ), 'L', 2, 97.5 );
          } else {
            $this->CheckBox( 20, 148, true );
            $this->CheckBox( 115, 148 );
            $this->SetXY( 86, 154 );
            $this->WriteCell( '', 'L', 2, 106.5 );
            $this->SetXY( 95, 165.25 );
            $this->WriteCell( '', 'L', 2, 97.5 );
          }
          if ( $this->GetData( 'speicherung' ) ) {
            $this->CheckBox( 20, 200 );
            $this->CheckBox( 115, 200, true );
            $this->SetXY( 95, 205.5 );
            $this->WriteCell( $this->GetData( 'speicherung_baujahr' ), 'L', 2, 97.5 );
            $speicherung_standort = $this->GetData( 'speicherung_standort' );
            $this->CheckBox( 20, 233, 'innerhalb' !== $speicherung_standort );
            $this->CheckBox( 115, 233, 'ausserhalb' !== $speicherung_standort );
          } else {
            $this->CheckBox( 20, 200, true );
            $this->CheckBox( 115, 200 );
            $this->SetXY( 95, 205.5 );
            $this->WriteCell( '', 'L', 2, 97.5 );
            $this->CheckBox( 20, 233, true );
            $this->CheckBox( 115, 233, true );
          }
        } else {
          $grundriss_form = $this->GetData( 'grundriss_form' );
          $wand_a_laenge = $wand_b_laenge = $wand_c_laenge = $wand_d_laenge = $wand_e_laenge = $wand_f_laenge = $wand_g_laenge = $wand_h_laenge = 0.0;
          if ( function_exists( 'wpenon_immoticket24_get_grundriss_formen' ) ) {

            /* START COPY OF CALCULATIONS */
            $grundriss_formen = wpenon_immoticket24_get_grundriss_formen();
            if ( isset( $grundriss_formen[ $grundriss_form ] ) ) {
              $grundriss_form = $grundriss_formen[ $grundriss_form ];
            } else {
              $grundriss_form = $grundriss_formen['a'];
            }
            $flaechenberechnungsformel = $grundriss_form['fla'];
            unset( $grundriss_form['fla'] );

            $to_calculate = array();
            foreach ( $grundriss_form as $wand => $data ) {
              if ( $data[0] === true ) {
                $l_slug = 'wand_' . $wand . '_laenge';
                $$l_slug = $this->GetData( $l_slug );
              } else {
                $to_calculate[ $wand ] = $data;
              }
            }
            unset( $data );
            foreach ( $to_calculate as $wand => $data ) {
              $laenge = 0.0;
              $current_operator = '+';
              $formel = explode( ' ', $data[0] );
              foreach ( $formel as $t ) {
                switch ( $t ) {
                  case '+':
                  case '-':
                    $current_operator = $t;
                    break;
                  default:
                    $l_slug = 'wand_' . $t . '_laenge';
                    switch ( $current_operator ) {
                      case '+':
                        $laenge += $$l_slug;
                        break;
                      case '-':
                        $laenge -= $$l_slug;
                        break;
                      default:
                    }
                }
              }
              if ( $laenge > 0.0 ) {
                $l_slug = 'wand_' . $wand . '_laenge';
                $$l_slug = $laenge;
              }
            }
            unset( $data );
            unset( $to_calculate );
            /* END COPY OF CALCULATIONS */

          }
          $this->SetXY( 33, 23.5 );
          $this->WriteCell( number_format_i18n( $wand_c_laenge, 2 ), 'L', 2, 58.5 );
          $this->CheckBox( 20, 36.5, ! $this->GetData( 'wand_c_nachbar' ) );
          $wand_c_daemmung = $this->GetData( 'wand_c_daemmung' );
          if ( empty( $wand_c_daemmung ) ) {
            $wand_c_daemmung = 0;
          }
          $this->SetXY( 41, 41 );
          $this->WriteCell( $wand_c_daemmung, 'L', 2, 48.5 );
          $this->SetXY( 128, 23.5 );
          $this->WriteCell( number_format_i18n( $wand_d_laenge, 2 ), 'L', 2, 58.5 );
          $this->CheckBox( 115, 36.5, ! $this->GetData( 'wand_d_nachbar' ) );
          $wand_d_daemmung = $this->GetData( 'wand_d_daemmung' );
          if ( empty( $wand_d_daemmung ) ) {
            $wand_d_daemmung = 0;
          }
          $this->SetXY( 136, 41 );
          $this->WriteCell( $wand_d_daemmung, 'L', 2, 48.5 );
          $show_wand_e = $wand_e_laenge > 0;
          $this->SetXY( 33, 65 );
          $this->WriteCell( $show_wand_e ? number_format_i18n( $wand_e_laenge, 2 ) : '', 'L', 2, 58.5 );
          $this->CheckBox( 20, 78, ! $show_wand_e || ! $this->GetData( 'wand_e_nachbar' ) );
          $wand_e_daemmung = $this->GetData( 'wand_e_daemmung' );
          if ( empty( $wand_e_daemmung ) ) {
            $wand_e_daemmung = 0;
          }
          $this->SetXY( 41, 82.5 );
          $this->WriteCell( $show_wand_e ? $wand_e_daemmung : '', 'L', 2, 48.5 );
          $show_wand_f = $wand_f_laenge > 0;
          $this->SetXY( 128, 65 );
          $this->WriteCell( $show_wand_f ? number_format_i18n( $wand_f_laenge, 2 ) : '', 'L', 2, 58.5 );
          $this->CheckBox( 115, 78, ! $show_wand_f || ! $this->GetData( 'wand_f_nachbar' ) );
          $wand_f_daemmung = $this->GetData( 'wand_f_daemmung' );
          if ( empty( $wand_f_daemmung ) ) {
            $wand_f_daemmung = 0;
          }
          $this->SetXY( 136, 82.5 );
          $this->WriteCell( $show_wand_f ? $wand_f_daemmung : '', 'L', 2, 48.5 );
          $show_wand_g = $wand_g_laenge > 0;
          $this->SetXY( 33, 106.5 );
          $this->WriteCell( $show_wand_g ? number_format_i18n( $wand_g_laenge, 2 ) : '', 'L', 2, 58.5 );
          $this->CheckBox( 20, 119.5, ! $show_wand_g || ! $this->GetData( 'wand_g_nachbar' ) );
          $wand_g_daemmung = $this->GetData( 'wand_g_daemmung' );
          if ( empty( $wand_g_daemmung ) ) {
            $wand_g_daemmung = 0;
          }
          $this->SetXY( 41, 124 );
          $this->WriteCell( $show_wand_g ? $wand_g_daemmung : '', 'L', 2, 48.5 );
          $show_wand_h = $wand_h_laenge > 0;
          $this->SetXY( 128, 106.5 );
          $this->WriteCell( $show_wand_h ? number_format_i18n( $wand_h_laenge, 2 ) : '', 'L', 2, 58.5 );
          $this->CheckBox( 115, 119.5, ! $show_wand_h || ! $this->GetData( 'wand_h_nachbar' ) );
          $wand_h_daemmung = $this->GetData( 'wand_h_daemmung' );
          if ( empty( $wand_h_daemmung ) ) {
            $wand_h_daemmung = 0;
          }
          $this->SetXY( 136, 124 );
          $this->WriteCell( $show_wand_h ? $wand_h_daemmung : '', 'L', 2, 48.5 );
          $anbau = $this->GetData( 'anbau' );
          $anbauwand_bauart = $this->GetData( 'anbauwand_bauart' );
          $this->CheckBox( 20, 155, true );
          $this->CheckBox( 111, 155, true );
          if ( $anbau ) {
            switch ( $anbauwand_bauart ) {
              case 'holz':
                $this->CheckBox( 111, 155 );
                break;
              case 'massiv':
              default:
                $this->CheckBox( 20, 155 );
                break;
            }
          }
          $this->SetXY( 48, 158 );
          $this->WriteCell( $anbau ? $this->GetData( 'anbauwand_b_laenge', true ) : '', 'L', 2, 137.5 );
          $this->SetXY( 48, 170 );
          $this->WriteCell( $anbau ? $this->GetData( 'anbauwand_t_laenge', true ) : '', 'L', 2, 137.5 );
          $this->SetXY( 66, 181 );
          $this->WriteCell( $anbau ? $this->GetData( 'anbauwand_s1_laenge', true ) : '', 'L', 2, 119.5 );
          $this->SetXY( 66, 193 );
          $this->WriteCell( $anbau ? $this->GetData( 'anbauwand_s2_laenge', true ) : '', 'L', 2, 119.5 );
          $anbauwand_daemmung = $this->GetData( 'anbauwand_daemmung' );
          if ( empty( $anbauwand_daemmung ) ) {
            $anbauwand_daemmung = 0;
          }
          $this->SetXY( 68, 206 );
          $this->WriteCell( $anbau ? $anbauwand_daemmung : '', 'L', 2, 117.5 );
          $dach = $this->GetData( 'dach' );
          $this->CheckBox( 15, 260, true );
          $this->CheckBox( 79, 260, true );
          $this->CheckBox( 142, 260, true );
          switch ( $dach ) {
            case 'beheizt':
              $this->CheckBox( 142, 260 );
              break;
            case 'unbeheizt':
              $this->CheckBox( 79, 260 );
              break;
            case 'nicht-vorhanden':
            default:
              $this->CheckBox( 15, 260 );
              break;
          }
          $dach_form = $this->GetData( 'dach_form' );
          $this->CheckBox( 15, 283, true );
          $this->CheckBox( 79, 283, true );
          $this->CheckBox( 142, 283, true );
          if ( 'beheizt' === $dach ) {
            switch ( $dach_form ) {
              case 'walmdach':
                $this->CheckBox( 142, 283 );
                break;
              case 'satteldach':
                $this->CheckBox( 79, 283 );
                break;
              case 'pultdach':
              default:
                $this->CheckBox( 15, 283 );
                break;
            }
          }
        }
        break;
      case 5:
        $this->SetPageFont( 'default' );
        if ( 'vw' === $this->wpenon_type ) {
          $ww_info = $this->GetData( 'ww_info' );
          $this->CheckBox( 15, 81, true );
          $this->CheckBox( 111, 81, true );
          $this->CheckBox( 15, 91, true );
          switch ( $ww_info ) {
            case 'ww':
              $this->CheckBox( 111, 81 );
              break;
            case 'unbekannt':
              $this->CheckBox( 15, 91 );
              break;
            case 'h':
            case 'h2':
            case 'h3':
            default:
              $this->CheckBox( 15, 81 );
              break;
          }
          $this->CheckBox( 20, 129.5, true );
          $this->CheckBox( 115.5, 129.5, true );
          $this->CheckBox( 20, 138.5, true );
          $this->CheckBox( 115.5, 138.5, true );
          $this->CheckBox( 20, 147.5, true );
          $this->CheckBox( 115.5, 147.5, true );
          $this->CheckBox( 20, 156.5, true );
          $this->CheckBox( 115.5, 156.5, true );
          $this->CheckBox( 20, 165.5, true );
          $this->CheckBox( 115.5, 165.5, true );
          $this->CheckBox( 20, 174.5, true );
          $this->CheckBox( 115.5, 174.5, true );
          $this->CheckBox( 20, 183.5, true );
          if ( 'ww' === $ww_info ) {
            $ww_erzeugung = $this->GetData( 'ww_erzeugung' );
            switch ( $ww_erzeugung ) {
              case 'niedertemperaturkessel':
                $this->CheckBox( 115.5, 129.5 );
                break;
              case 'brennwertkessel':
                $this->CheckBox( 20, 138.5 );
                break;
              case 'brennwertkesselverbessert':
                $this->CheckBox( 115.5, 138.5 );
                break;
              case 'fernwaerme':
                $this->CheckBox( 20, 147.5 );
                break;
              case 'waermepumpeluft':
                $this->CheckBox( 115.5, 147.5 );
                break;
              case 'waermepumpewasser':
                $this->CheckBox( 20, 156.5 );
                break;
              case 'waermepumpeerde':
                $this->CheckBox( 115.5, 156.5 );
                break;
              case 'kleinthermeniedertemperatur':
                $this->CheckBox( 20, 165.5 );
                break;
              case 'kleinthermebrennwert':
                $this->CheckBox( 115.5, 165.5 );
                break;
              case 'dezentralkleinspeicher':
                $this->CheckBox( 20, 174.5 );
                break;
              case 'dezentralelektroerhitzer':
                $this->CheckBox( 115.5, 174.5 );
                break;
              case 'dezentralgaserhitzer':
                $this->CheckBox( 20, 183.5 );
                break;
              case 'standardkessel':
              default:
                $this->CheckBox( 20, 129.5 );
                break;
            }
          }
          $this->CheckBox( 20, 202, true );
          $this->CheckBox( 55, 202, true );
          $this->CheckBox( 119, 202, true );
          $this->CheckBox( 154, 202, true );
          $this->CheckBox( 20, 211, true );
          $this->CheckBox( 55, 211, true );
          $this->CheckBox( 119, 211, true );
          $this->CheckBox( 154, 211, true );
          $this->CheckBox( 20, 220, true );
          $this->CheckBox( 55, 220, true );
          $this->CheckBox( 119, 220, true );
          $this->CheckBox( 154, 220, true );
          $this->CheckBox( 20, 229, true );
          $this->CheckBox( 55, 229, true );
          $this->CheckBox( 119, 229, true );
          $this->CheckBox( 20, 238, true );
          $this->CheckBox( 20, 247, true );
          $this->CheckBox( 20, 256, true );
          $this->CheckBox( 20, 265, true );
          if ( 'ww' === $ww_info ) {
            $ww_energietraeger = $this->GetData( 'ww_energietraeger' );
            switch ( $ww_energietraeger ) {
              case 'heizoelbiooel':
                $this->CheckBox( 55, 202 );
                break;
              case 'biooel':
                $this->CheckBox( 119, 202 );
                break;
              case 'erdgas':
                $this->CheckBox( 154, 202 );
                break;
              case 'koks':
                $this->CheckBox( 20, 211 );
                break;
              case 'erdgasbiogas':
                $this->CheckBox( 55, 211 );
                break;
              case 'biogas':
                $this->CheckBox( 119, 211 );
                break;
              case 'fluessiggas':
                $this->CheckBox( 154, 211 );
                break;
              case 'steinkohle':
                $this->CheckBox( 20, 220 );
                break;
              case 'braunkohle':
                $this->CheckBox( 55, 220 );
                break;
              case 'stueckholz':
                $this->CheckBox( 119, 220 );
                break;
              case 'holzhackschnitzel':
                $this->CheckBox( 153, 220 );
                break;
              case 'holzpellets':
                $this->CheckBox( 20, 229 );
                break;
              case 'strom':
                $this->CheckBox( 55, 229 );
                break;
              case 'sonneneinstrahlung':
                $this->CheckBox( 117, 229 );
                break;
              case 'fernwaermehzwfossil':
                $this->CheckBox( 20, 238 );
                break;
              case 'fernwaermehzwregenerativ':
                $this->CheckBox( 20, 247 );
                break;
              case 'fernwaermekwkfossil':
              case 'fernwaermekwkfossilbio':
                $this->CheckBox( 20, 256 );
                break;
              case 'fernwaermekwkregenerativ':
                $this->CheckBox( 20, 265 );
                break;
              case 'heizoel':
              default:
                $this->CheckBox( 20, 202 );
                break;
            }
            $this->SetXY( 90, 270.5 );
            $this->WriteCell( $this->GetData( 'ww_baujahr' ), 'L', 2, 102.5 );
          } else {
            $this->SetXY( 90, 270.5 );
            $this->WriteCell( '', 'L', 2, 102.5 );
          }
        } else {
          $dach = $this->GetData( 'dach' );
          $dach_bauart = $this->GetData( 'dach_bauart' );
          $this->CheckBox( 15, 24, true );
          $this->CheckBox( 79, 24, true );
          if ( 'beheizt' === $dach ) {
            switch ( $dach_bauart ) {
              case 'holz':
                $this->CheckBox( 79, 24 );
                break;
              case 'massiv':
              default:
                $this->CheckBox( 15, 24 );
                break;
            }
          }
          if ( 'beheizt' === $dach ) {
            $dach_daemmung = $this->GetData( 'dach_daemmung' );
            if ( empty( $dach_daemmung ) ) {
              $dach_daemmung = 0;
            }
            $dach_hoehe = $this->GetData( 'dach_hoehe', true );
          } else {
            $dach_daemmung = '';
            $dach_hoehe = '';
          }
          $this->SetXY( 47, 36.5 );
          $this->WriteCell( $dach_daemmung, 'L', 2, 141.5 );
          $this->SetXY( 36, 49 );
          $this->WriteCell( $dach_hoehe, 'L', 2, 161.5 );
          $anbau = $this->GetData( 'anbau' );
          $anbaudach_bauart = $this->GetData( 'anbaudach_bauart' );
          $this->CheckBox( 20, 78, true );
          $this->CheckBox( 79, 78, true );
          if ( $anbau ) {
            switch ( $anbaudach_bauart ) {
              case 'holz':
                $this->CheckBox( 79, 78 );
                break;
              case 'massiv':
              default:
                $this->CheckBox( 20, 78 );
                break;
            }
          }
          $keller = $this->GetData( 'keller' );
          $this->CheckBox( 15, 128, true );
          $this->CheckBox( 79, 128, true );
          $this->CheckBox( 142, 128, true );
          switch ( $keller ) {
            case 'beheizt':
              $this->CheckBox( 142, 128 );
              break;
            case 'unbeheizt':
              $this->CheckBox( 79, 128 );
              break;
            case 'nicht-vorhanden':
            default:
              $this->CheckBox( 15, 128 );
              break;
          }
          if ( 'beheizt' === $keller ) {
            $keller_groesse = $this->GetData( 'keller_groesse' );
            $keller_hoehe = $this->GetData( 'keller_hoehe', true );
            $keller_daemmung = $this->GetData( 'keller_daemmung' );
            if ( empty( $keller_daemmung ) ) {
              $keller_daemmung = 0;
            }
          } else {
            $keller_groesse = '';
            $keller_hoehe = '';
            $keller_daemmung = '';
          }
          $this->SetXY( 47, 141 );
          $this->WriteCell( $keller_groesse, 'L', 2, 141.5 );
          $this->SetXY( 38, 159 );
          $this->WriteCell( $keller_hoehe, 'L', 2, 150.5 );
          $keller_bauart = $this->GetData( 'keller_bauart' );
          $this->CheckBox( 15, 186, true );
          $this->CheckBox( 79, 186, true );
          if ( 'beheizt' === $keller ) {
            switch ( $keller_bauart ) {
              case 'holz':
                $this->CheckBox( 79, 186 );
                break;
              case 'massiv':
              default:
                $this->CheckBox( 15, 186 );
                break;
            }
          }
          $this->SetXY( 59, 194 );
          $this->WriteCell( $keller_daemmung, 'L', 2, 129.5 );
          $boden_bauart = $this->GetData( 'boden_bauart' );
          $this->CheckBox( 15, 224, true );
          $this->CheckBox( 79, 224, true );
          switch ( $boden_bauart ) {
            case 'holz':
              $this->CheckBox( 79, 224 );
              break;
            case 'massiv':
            default:
              $this->CheckBox( 15, 224 );
              break;
          }
          $boden_daemmung = $this->GetData( 'boden_daemmung' );
          if ( empty( $boden_daemmung ) ) {
            $boden_daemmung = 0;
          }
          $this->SetXY( 51, 241 );
          $this->WriteCell( $boden_daemmung, 'L', 2, 137.5 );
          $anbauboden_bauart = $this->GetData( 'anbauboden_bauart' );
          $this->CheckBox( 20, 270, true );
          $this->CheckBox( 85, 270, true );
          if ( $anbau ) {
            switch ( $anbauboden_bauart ) {
              case 'holz':
                $this->CheckBox( 85, 270 );
                break;
              case 'massiv':
              default:
                $this->CheckBox( 20, 270 );
                break;
            }
          }
        }
        break;
      case 6:
        $this->SetPageFont( 'default' );
        if ( 'vw' === $this->wpenon_type ) {
          $l_info = $this->GetData( 'l_info' );
          $this->CheckBox( 15, 64, true );
          $this->CheckBox( 79, 64, true );
          $this->CheckBox( 142, 64, true );
          switch ( $l_info ) {
            case 'schacht':
              $this->CheckBox( 79, 64 );
              break;
            case 'anlage':
              $this->CheckBox( 142, 64 );
              break;
            case 'fenster':
            default:
              $this->CheckBox( 15, 64 );
              break;
          }
          $dichtheit = $this->GetData( 'dichtheit' );
          $this->CheckBox( 15, 83, true );
          $this->CheckBox( 79, 83, true );
          if ( $dichtheit ) {
            $this->CheckBox( 15, 83 );
          } else {
            $this->CheckBox( 79, 83 );
          }
          $klimafaktoren_datum = $this->GetData( 'verbrauch_zeitraum' );
          $zeitraum = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $klimafaktoren_datum, 0, false, 'data' ) . ' - ' . wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $klimafaktoren_datum, 2, true, 'data' );
          $this->SetXY( 71, 107 );
          $this->WriteCell( $zeitraum, 'L', 2, 126.5 );
          $start1 = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $klimafaktoren_datum, 0, false, 'data' );
          $end1   = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $klimafaktoren_datum, 0, true, 'data' );
          $this->SetXY( 46, 149.5 );
          $this->WriteCell( $start1, 'L', 2, 56 );
          $this->SetXY( 118, 149.5 );
          $this->WriteCell( $end1, 'L', 2, 79.5 );
          $verbrauch1_h = $this->GetData( 'verbrauch1_h', true ) . ' kWh';
          $this->SetXY( 72, 160 );
          $this->WriteCell( $verbrauch1_h, 'L', 2, 125.5 );
          $verbrauch1_ww = '-';
          if ( 'ww' === $this->GetData( 'ww_info' ) ) {
            $verbrauch1_ww = $this->GetData( 'verbrauch1_ww', true ) . ' kWh';
          }
          $this->SetXY( 79, 171 );
          $this->WriteCell( $verbrauch1_ww, 'L', 2, 118.5 );
          $verbrauch1_leerstand = $this->GetData( 'verbrauch1_leerstand', true );
          if ( empty( $verbrauch1_leerstand ) ) {
            $verbrauch1_leerstand = '0';
          }
          $this->SetXY( 59, 186 );
          $this->WriteCell( $verbrauch1_leerstand, 'L', 2, 138.5 );
        } else {
          $fenster_bauart = $this->GetData( 'fenster_bauart' );
          $this->CheckBox( 15, 43, true );
          $this->CheckBox( 111, 43, true );
          $this->CheckBox( 15, 52, true );
          $this->CheckBox( 111, 52, true );
          $this->CheckBox( 15, 61, true );
          $this->CheckBox( 111, 61, true );
          $this->CheckBox( 15, 70, true );
          switch ( $fenster_bauart ) {
            case 'kunststoff':
              $this->CheckBox( 15, 52 );
              break;
            case 'holzdoppelt':
              $this->CheckBox( 111, 43 );
              break;
            case 'stahl':
              $this->CheckBox( 111, 52 );
              break;
            case 'waermedaemmglass':
              $this->CheckBox( 15, 61 );
              break;
            case 'waermedaemmglass2fach':
              $this->CheckBox( 111, 61 );
              break;
            case 'holzeinfach':
              $this->CheckBox( 15, 70 );
              break;
            case 'aluminium':
            default:
              $this->CheckBox( 15, 43 );
              break;
          }
          $this->SetXY( 50, 75.5 );
          $this->WriteCell( $this->GetData( 'fenster_baujahr' ), 'L', 2, 147.5 );
          $heizkoerpernischen = $this->GetData( 'heizkoerpernischen' );
          $this->CheckBox( 15, 106, true );
          $this->CheckBox( 111, 106, true );
          switch ( $heizkoerpernischen ) {
            case 'vorhanden':
              $this->CheckBox( 111, 106 );
              break;
            case 'nicht-vorhanden':
            default:
              $this->CheckBox( 15, 106 );
              break;
          }
          $rollladenkaesten = $this->GetData( 'rollladenkaesten' );
          $this->CheckBox( 15, 128, true );
          $this->CheckBox( 111, 128, true );
          $this->CheckBox( 15, 137, true );
          $this->CheckBox( 111, 137, true );
          switch ( $rollladenkaesten ) {
            case 'innen_ungedaemmt':
              $this->CheckBox( 15, 137 );
              break;
            case 'aussen':
              $this->CheckBox( 111, 128 );
              break;
            case 'innen_gedaemmt':
              $this->CheckBox( 111, 137 );
              break;
            case 'nicht-vorhanden':
            default:
              $this->CheckBox( 15, 128 );
              break;
          }
          $h_erzeugung = $this->GetData( 'h_erzeugung' );
          $this->CheckBox( 15, 191, true );
          $this->CheckBox( 111, 191, true );
          $this->CheckBox( 15, 200, true );
          $this->CheckBox( 111, 200, true );
          $this->CheckBox( 15, 209, true );
          $this->CheckBox( 111, 209, true );
          $this->CheckBox( 15, 218, true );
          $this->CheckBox( 111, 218, true );
          $this->CheckBox( 15, 227, true );
          $this->CheckBox( 111, 227, true );
          $this->CheckBox( 15, 236, true );
          $this->CheckBox( 111, 236, true );
          $this->CheckBox( 15, 245, true );
          $this->CheckBox( 111, 245, true );
          $this->CheckBox( 15, 254, true );
          $this->CheckBox( 111, 254, true );
          $this->CheckBox( 15, 263, true );
          switch ( $h_erzeugung ) {
            case 'niedertemperaturkessel':
              $this->CheckBox( 111, 191 );
              break;
            case 'brennwertkessel':
              $this->CheckBox( 15, 200 );
              break;
            case 'brennwertkesselverbessert':
              $this->CheckBox( 111, 200 );
              break;
            case 'fernwaerme':
              $this->CheckBox( 15, 209 );
              break;
            case 'waermepumpeluft':
              $this->CheckBox( 111, 209 );
              break;
            case 'waermepumpewasser':
              $this->CheckBox( 15, 218 );
              break;
            case 'waermepumpeerde':
              $this->CheckBox( 111, 218 );
              break;
            case 'kleinthermeniedertemperatur':
              $this->CheckBox( 15, 227 );
              break;
            case 'kleinthermebrennwert':
              $this->CheckBox( 111, 227 );
              break;
            case 'kohleholzofen':
              $this->CheckBox( 15, 236 );
              break;
            case 'gasraumheizer':
              $this->CheckBox( 111, 236 );
              break;
            case 'elektronachtspeicherheizung':
              $this->CheckBox( 15, 245 );
              break;
            case 'elektrodirektheizgeraet':
              $this->CheckBox( 111, 245 );
              break;
            case 'stueckholzfeuerung':
              $this->CheckBox( 15, 254 );
              break;
            case 'pelletfeuerung':
              $this->CheckBox( 111, 254 );
              break;
            case 'oelofenverdampfungsbrenner':
              $this->CheckBox( 15, 263 );
              break;
            case 'standardkessel':
            default:
              $this->CheckBox( 15, 191 );
              break;
          }
        }
        break;
      case 7:
        $this->SetPageFont( 'default' );
        if ( 'vw' === $this->wpenon_type ) {
          $klimafaktoren_datum = $this->GetData( 'verbrauch_zeitraum' );
          $start2 = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $klimafaktoren_datum, 1, false, 'data' );
          $end2   = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $klimafaktoren_datum, 1, true, 'data' );
          $this->SetXY( 46, 44.5 );
          $this->WriteCell( $start2, 'L', 2, 56 );
          $this->SetXY( 118, 44.5 );
          $this->WriteCell( $end2, 'L', 2, 79.5 );
          $verbrauch2_h = $this->GetData( 'verbrauch2_h', true ) . ' kWh';
          $this->SetXY( 72, 55 );
          $this->WriteCell( $verbrauch2_h, 'L', 2, 125.5 );
          $verbrauch2_ww = '-';
          if ( 'ww' === $this->GetData( 'ww_info' ) ) {
            $verbrauch2_ww = $this->GetData( 'verbrauch2_ww', true ) . ' kWh';
          }
          $this->SetXY( 79, 66 );
          $this->WriteCell( $verbrauch2_ww, 'L', 2, 118.5 );
          $verbrauch2_leerstand = $this->GetData( 'verbrauch2_leerstand', true );
          if ( empty( $verbrauch2_leerstand ) ) {
            $verbrauch2_leerstand = '0';
          }
          $this->SetXY( 59, 81 );
          $this->WriteCell( $verbrauch2_leerstand, 'L', 2, 138.5 );
          $start3 = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $klimafaktoren_datum, 2, false, 'data' );
          $end3   = wpenon_immoticket24_get_klimafaktoren_zeitraum_date( $klimafaktoren_datum, 2, true, 'data' );
          $this->SetXY( 46, 107 );
          $this->WriteCell( $start3, 'L', 2, 56 );
          $this->SetXY( 118, 107 );
          $this->WriteCell( $end3, 'L', 2, 79.5 );
          $verbrauch3_h = $this->GetData( 'verbrauch3_h', true ) . ' kWh';
          $this->SetXY( 72, 117.5 );
          $this->WriteCell( $verbrauch3_h, 'L', 2, 125.5 );
          $verbrauch3_ww = '-';
          if ( 'ww' === $this->GetData( 'ww_info' ) ) {
            $verbrauch3_ww = $this->GetData( 'verbrauch3_ww', true ) . ' kWh';
          }
          $this->SetXY( 79, 128.5 );
          $this->WriteCell( $verbrauch3_ww, 'L', 2, 118.5 );
          $verbrauch3_leerstand = $this->GetData( 'verbrauch3_leerstand', true );
          if ( empty( $verbrauch3_leerstand ) ) {
            $verbrauch3_leerstand = '0';
          }
          $this->SetXY( 59, 143.5 );
          $this->WriteCell( $verbrauch3_leerstand, 'L', 2, 138.5 );
          $k_info = $this->GetData( 'k_info' );
          $this->CheckBox( 15, 181.5, true );
          $this->CheckBox( 103, 181.5, true );
          switch ( $k_info ) {
            case 'nicht_vorhanden':
              $this->CheckBox( 103, 181.5 );
              break;
            case 'vorhanden':
            default:
              $this->CheckBox( 15, 181.5 );
              break;
          }
          $this->SetXY( 20, 222 );
          $this->WriteCell( '', 'L', 2, 81.5 );
          $this->SetXY( 106, 222 );
          $this->WriteCell( '', 'L', 2, 81.5 );
        } else {
          $h_energietraeger = $this->GetData( 'h_energietraeger' );
          $this->CheckBox( 15, 24, true );
          $this->CheckBox( 52, 24, true );
          $this->CheckBox( 117, 24, true );
          $this->CheckBox( 153, 24, true );
          $this->CheckBox( 15, 33, true );
          $this->CheckBox( 52, 33, true );
          $this->CheckBox( 117, 33, true );
          $this->CheckBox( 153, 33, true );
          $this->CheckBox( 15, 42, true );
          $this->CheckBox( 52, 42, true );
          $this->CheckBox( 117, 42, true );
          $this->CheckBox( 153, 42, true );
          $this->CheckBox( 15, 51, true );
          $this->CheckBox( 52, 51, true );
          $this->CheckBox( 117, 51, true );
          $this->CheckBox( 15, 60, true );
          $this->CheckBox( 15, 69, true );
          $this->CheckBox( 15, 78, true );
          $this->CheckBox( 15, 87, true );
          switch ( $h_energietraeger ) {
            case 'heizoelbiooel':
              $this->CheckBox( 52, 24 );
              break;
            case 'biooel':
              $this->CheckBox( 117, 24 );
              break;
            case 'erdgas':
              $this->CheckBox( 153, 24 );
              break;
            case 'koks':
              $this->CheckBox( 15, 33 );
              break;
            case 'erdgasbiogas':
              $this->CheckBox( 52, 33 );
              break;
            case 'biogas':
              $this->CheckBox( 117, 33 );
              break;
            case 'fluessiggas':
              $this->CheckBox( 153, 33 );
              break;
            case 'steinkohle':
              $this->CheckBox( 15, 42 );
              break;
            case 'braunkohle':
              $this->CheckBox( 52, 42 );
              break;
            case 'stueckholz':
              $this->CheckBox( 117, 42 );
              break;
            case 'holzhackschnitzel':
              $this->CheckBox( 153, 42 );
              break;
            case 'holzpellets':
              $this->CheckBox( 15, 51 );
              break;
            case 'strom':
              $this->CheckBox( 52, 51 );
              break;
            case 'sonneneinstrahlung':
              $this->CheckBox( 117, 51 );
              break;
            case 'fernwaermehzwfossil':
              $this->CheckBox( 15, 60 );
              break;
            case 'fernwaermehzwregenerativ':
              $this->CheckBox( 15, 69 );
              break;
            case 'fernwaermekwkfossil':
            case 'fernwaermekwkfossilbio':
              $this->CheckBox( 15, 78 );
              break;
            case 'fernwaermekwkregenerativ':
              $this->CheckBox( 15, 87 );
              break;
            case 'heizoel':
            default:
              $this->CheckBox( 15, 24 );
              break;
          }
          $this->SetXY( 76, 93 );
          $this->WriteCell( $this->GetData( 'h_baujahr' ), 'L', 2, 121.5 );
          $this->SetXY( 87, 109 );
          $this->WriteCell( $this->GetData( 'verteilung_baujahr' ), 'L', 2, 110.5 );
          if ( $this->GetData( 'h2_info' ) ) {
            $this->CheckBox( 15, 135 );
            $this->CheckBox( 111, 135, true );
            $this->SetXY( 86, 141 );
            $this->WriteCell( $this->GetData( 'h2_erzeugung', true ), 'L', 2, 111.5 );
            $this->SetXY( 95, 152 );
            $this->WriteCell( $this->GetData( 'h2_baujahr' ), 'L', 2, 102.5 );
          } else {
            $this->CheckBox( 15, 135, true );
            $this->CheckBox( 111, 135 );
            $this->SetXY( 86, 141 );
            $this->WriteCell( '', 'L', 2, 111.5 );
            $this->SetXY( 95, 152 );
            $this->WriteCell( '', 'L', 2, 102.5 );
          }
          if ( $this->GetData( 'speicherung' ) ) {
            $this->CheckBox( 15, 179 );
            $this->CheckBox( 111, 179, true );
            $this->SetXY( 95, 185 );
            $this->WriteCell( $this->GetData( 'speicherung_baujahr' ), 'L', 2, 102.5 );
            $speicherung_standort = $this->GetData( 'speicherung_standort' );
            $this->CheckBox( 15, 212, 'innerhalb' !== $speicherung_standort );
            $this->CheckBox( 111, 212, 'ausserhalb' !== $speicherung_standort );
          } else {
            $this->CheckBox( 15, 179, true );
            $this->CheckBox( 111, 179 );
            $this->SetXY( 95, 185 );
            $this->WriteCell( '', 'L', 2, 102.5 );
            $this->CheckBox( 15, 212, true );
            $this->CheckBox( 111, 212, true );
          }
          $ww_info = $this->GetData( 'ww_info' );
          $this->CheckBox( 15, 266, true );
          $this->CheckBox( 111, 266, true );
          $this->CheckBox( 15, 275, true );
          switch ( $ww_info ) {
            case 'ww':
              $this->CheckBox( 111, 266 );
              break;
            case 'unbekannt':
              $this->CheckBox( 15, 275 );
              break;
            case 'h':
            case 'h2':
            case 'h3':
            default:
              $this->CheckBox( 15, 266 );
              break;
          }
        }
        break;
      case 8:
        $this->SetPageFont( 'default' );
        if ( 'vw' === $this->wpenon_type ) {

        } else {
          $ww_info = $this->GetData( 'ww_info' );
          $this->CheckBox( 20, 43, true );
          $this->CheckBox( 115.5, 43, true );
          $this->CheckBox( 20, 52, true );
          $this->CheckBox( 115.5, 52, true );
          $this->CheckBox( 20, 61, true );
          $this->CheckBox( 115.5, 61, true );
          $this->CheckBox( 20, 70, true );
          $this->CheckBox( 115.5, 70, true );
          $this->CheckBox( 20, 79, true );
          $this->CheckBox( 115.5, 79, true );
          $this->CheckBox( 20, 88, true );
          $this->CheckBox( 115.5, 88, true );
          $this->CheckBox( 20, 97, true );
          if ( 'ww' === $ww_info ) {
            $ww_erzeugung = $this->GetData( 'ww_erzeugung' );
            switch ( $ww_erzeugung ) {
              case 'niedertemperaturkessel':
                $this->CheckBox( 115.5, 43 );
                break;
              case 'brennwertkessel':
                $this->CheckBox( 20, 52 );
                break;
              case 'brennwertkesselverbessert':
                $this->CheckBox( 115.5, 52 );
                break;
              case 'fernwaerme':
                $this->CheckBox( 20, 61 );
                break;
              case 'waermepumpeluft':
                $this->CheckBox( 115.5, 61 );
                break;
              case 'waermepumpewasser':
                $this->CheckBox( 20, 70 );
                break;
              case 'waermepumpeerde':
                $this->CheckBox( 115.5, 70 );
                break;
              case 'kleinthermeniedertemperatur':
                $this->CheckBox( 20, 79 );
                break;
              case 'kleinthermebrennwert':
                $this->CheckBox( 115.5, 79 );
                break;
              case 'dezentralkleinspeicher':
                $this->CheckBox( 20, 88 );
                break;
              case 'dezentralelektroerhitzer':
                $this->CheckBox( 115.5, 88 );
                break;
              case 'dezentralgaserhitzer':
                $this->CheckBox( 20, 97 );
                break;
              case 'standardkessel':
              default:
                $this->CheckBox( 20, 43 );
                break;
            }
          }
          $this->CheckBox( 20, 115, true );
          $this->CheckBox( 55, 115, true );
          $this->CheckBox( 119, 115, true );
          $this->CheckBox( 154, 115, true );
          $this->CheckBox( 20, 124, true );
          $this->CheckBox( 55, 124, true );
          $this->CheckBox( 119, 124, true );
          $this->CheckBox( 154, 124, true );
          $this->CheckBox( 20, 133, true );
          $this->CheckBox( 55, 133, true );
          $this->CheckBox( 119, 133, true );
          $this->CheckBox( 154, 133, true );
          $this->CheckBox( 20, 142, true );
          $this->CheckBox( 55, 142, true );
          $this->CheckBox( 119, 142, true );
          $this->CheckBox( 20, 151, true );
          $this->CheckBox( 20, 160, true );
          $this->CheckBox( 20, 169, true );
          $this->CheckBox( 20, 178, true );
          if ( 'ww' === $ww_info ) {
            $ww_energietraeger = $this->GetData( 'ww_energietraeger' );
            switch ( $ww_energietraeger ) {
              case 'heizoelbiooel':
                $this->CheckBox( 55, 115 );
                break;
              case 'biooel':
                $this->CheckBox( 119, 115 );
                break;
              case 'erdgas':
                $this->CheckBox( 154, 115 );
                break;
              case 'koks':
                $this->CheckBox( 20, 124 );
                break;
              case 'erdgasbiogas':
                $this->CheckBox( 55, 124 );
                break;
              case 'biogas':
                $this->CheckBox( 119, 124 );
                break;
              case 'fluessiggas':
                $this->CheckBox( 154, 124 );
                break;
              case 'steinkohle':
                $this->CheckBox( 20, 133 );
                break;
              case 'braunkohle':
                $this->CheckBox( 55, 133 );
                break;
              case 'stueckholz':
                $this->CheckBox( 119, 133 );
                break;
              case 'holzhackschnitzel':
                $this->CheckBox( 153, 133 );
                break;
              case 'holzpellets':
                $this->CheckBox( 20, 142 );
                break;
              case 'strom':
                $this->CheckBox( 55, 142 );
                break;
              case 'sonneneinstrahlung':
                $this->CheckBox( 117, 142 );
                break;
              case 'fernwaermehzwfossil':
                $this->CheckBox( 20, 151 );
                break;
              case 'fernwaermehzwregenerativ':
                $this->CheckBox( 20, 160 );
                break;
              case 'fernwaermekwkfossil':
              case 'fernwaermekwkfossilbio':
                $this->CheckBox( 20, 169 );
                break;
              case 'fernwaermekwkregenerativ':
                $this->CheckBox( 20, 178 );
                break;
              case 'heizoel':
              default:
                $this->CheckBox( 20, 115 );
                break;
            }
            $this->SetXY( 90, 183 );
            $this->WriteCell( $this->GetData( 'ww_baujahr' ), 'L', 2, 102.5 );
          } else {
            $this->SetXY( 90, 183 );
            $this->WriteCell( '', 'L', 2, 102.5 );
          }
          $verteilung_versorgung = $this->GetData( 'verteilung_versorgung' );
          $this->CheckBox( 15, 228, true );
          $this->CheckBox( 111, 228, true );
          switch ( $verteilung_versorgung ) {
            case 'mit':
              $this->CheckBox( 111, 228 );
              break;
            case 'ohne':
            default:
              $this->CheckBox( 15, 228 );
              break;
          }
          $l_info = $this->GetData( 'l_info' );
          $this->CheckBox( 15, 265, true );
          $this->CheckBox( 79, 265, true );
          $this->CheckBox( 142, 265, true );
          switch ( $l_info ) {
            case 'schacht':
              $this->CheckBox( 79, 265 );
              break;
            case 'anlage':
              $this->CheckBox( 142, 265 );
              break;
            case 'fenster':
            default:
              $this->CheckBox( 15, 265 );
              break;
          }
          $dichtheit = $this->GetData( 'dichtheit' );
          $this->CheckBox( 15, 284, true );
          $this->CheckBox( 79, 284, true );
          if ( $dichtheit ) {
            $this->CheckBox( 15, 284 );
          } else {
            $this->CheckBox( 79, 284 );
          }
        }
        break;
      case 9:
        $this->SetPageFont( 'default' );
        if ( 'vw' === $this->wpenon_type ) {

        } else {
          $k_info = $this->GetData( 'k_info' );
          $this->CheckBox( 15, 34, true );
          $this->CheckBox( 103, 34, true );
          switch ( $k_info ) {
            case 'nicht_vorhanden':
              $this->CheckBox( 103, 34 );
              break;
            case 'vorhanden':
            default:
              $this->CheckBox( 15, 34 );
              break;
          }
          $this->SetXY( 20, 78 );
          $this->WriteCell( '', 'L', 2, 81.5 );
          $this->SetXY( 106, 78 );
          $this->WriteCell( '', 'L', 2, 81.5 );
        }
        break;
      default:
        break;
    }
  }

  public function GetData( $context, $formatted = false ) {
    $data = '';
    if ( $this->energieausweis !== null ) {
      if ( 0 === strpos( $context, 'kontakt_' ) ) {
        if ( null === $this->wpenon_payment ) {
          $this->wpenon_payment = $this->energieausweis->getPayment();
          if ( ! $this->wpenon_payment ) {
            $this->wpenon_payment = false;
          }
        }
        if ( ! $this->wpenon_payment ) {
          return '';
        }
        $context = str_replace( 'kontakt_', '', $context );
        switch ( $context ) {
          case 'name':
            $data = $this->wpenon_payment->user_info['first_name'] . ' ' . $this->wpenon_payment->user_info['last_name'];
            break;
          case 'adresse_strassenr':
            $data = ! empty( $this->wpenon_payment->user_info['address']['line1'] ) ? $this->wpenon_payment->user_info['address']['line1'] : '';
            break;
          case 'adresse_plz':
            $data = ! empty( $this->wpenon_payment->user_info['address']['zip'] ) ? $this->wpenon_payment->user_info['address']['zip'] : '';
            break;
          case 'adresse_ort':
            $data = ! empty( $this->wpenon_payment->user_info['address']['city'] ) ? $this->wpenon_payment->user_info['address']['city'] : '';
            break;
          case 'telefon':
            $customer_id = edd_get_payment_customer_id( $this->wpenon_payment->ID );
            if ( $customer_id ) {
              $customer_meta = \WPENON\Util\CustomerMeta::getCustomerMeta( $customer_id );
              $data = ! empty( $customer_meta['telefon'] ) ? $customer_meta['telefon'] : '';
            }
            break;
        }
        if ( ! empty( $data ) ) {
          $data = \WPENON\Util\Format::pdfEncode( $data );
        }
        return $data;
      }

      if ( $formatted ) {
        $formatted_context = 'formatted_' . $context;
        if ( isset( $this->energieausweis->$formatted_context ) ) {
          $data = $this->energieausweis->$formatted_context;
        }
      }

      if ( empty( $data ) && isset( $this->energieausweis->$context ) ) {
        $data = $this->energieausweis->$context;
      }
    }

    if ( ! empty( $data ) && ( $formatted || ! is_float( $data ) && ! is_int( $data ) ) ) {
      $data = \WPENON\Util\Format::pdfEncode( $data );
    } elseif ( empty( $data ) ) {
      $data = '';
    }

    return $data;
  }

  /**
   * UTILITY FUNCTIONS
   */
  
  public function CheckBox( $x, $y, $print_empty = false ) {
    $orig_font = $this->wpenon_current_font;
    $orig_x    = $this->GetX();
    $orig_y    = $this->GetY();

    $content = $print_empty ? '' : 'x';

    $this->SetPageFont( 'marker' );
    $this->SetPageFillColor( 'checkboxbg' );
    $this->SetXY( $x - 2.6, $y - 5.6 );
    parent::WriteCell( $content, 'C', 0, 8, null, true, false );

    $this->SetXY( $orig_x, $orig_y );
    $this->SetPageFont( $orig_font );
    $this->SetPageFillColor( 'boxbg' );
  }

  public function WriteCell( $text, $align, $ln, $width, $height = null, $fill = false, $border = false ) {
    parent::WriteCell( $text, $align, $ln, $width, $height, true, 1 );
  }

  public function WriteMultiCell( $text, $align, $ln, $width, $height = null, $fill = false, $line_count = 0, $border = false ) {
    parent::WriteMultiCell( $text, $align, $ln, $width, $height, true, $line_count, 1 );
  }
}
