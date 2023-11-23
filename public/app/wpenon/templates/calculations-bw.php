<?php
/**
 * @package WPENON
 * @version 1.0.0
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

use Enev\Schema202302\Calculations\Helfer\Jahr;

use function Enev\Schema202302\Calculations\Helfer\fum;

$gebaeude = $data['gebaeude'];

$jahr = new Jahr();

?>
<style type="text/css">
	.calculation-details {
	background-color: lightblue;
	padding: 10px;
	border-radius: 5px;
	}

	.calculation-details p {
	margin: 0 0 10px 0;
	}

	.calculation-details h2, 
	.calculation-details h3 {
	margin: 25px 0 25px 0;
	}

	.calculation-details table {
	width: 100%;
	margin: 0 0 10px 0;
	}

	.calculation-details table th {
	text-align: left;
	}

	.calculation-details table th, td {
	padding: 5px 5px 5px 0;
	}
</style>

<div class="calculation-details">
	<h2>Gebäude</h2>
	<p><?php printf( __( 'Baujahr: %s;', 'wpenon' ), $gebaeude->baujahr() ); ?></p>
	<p><?php printf( __( 'Hüllvolumen V<sub>e</sub>: %s m&sup3;', 'wpenon' ), str_replace('.',',', $gebaeude->huellvolumen() ) ); ?></p>
	<p><?php printf( __( 'Hüllvolumen (netto): %s m&sup3;', 'wpenon' ), str_replace('.',',', $gebaeude->huellvolumen_netto() ) ); ?></p>
	<p><?php printf( __( 'Hüllfäche<sub>e</sub>: %s m&sup2;', 'wpenon' ), str_replace('.',',', $gebaeude->huellflaeche() ) ); ?></p>
	<p><?php printf( __( 'ave Verhältnis: %s', 'wpenon' ), str_replace('.',',', $gebaeude->ave_verhaeltnis() ) ); ?></p>
	<p><?php printf( __( 'Nutzfläche A<sub>N</sub>: %s m&sup2;', 'wpenon' ), str_replace('.',',', $gebaeude->nutzflaeche() ) ); ?></p>
	<p><?php printf( __( 'Anzahl der Geschosse: %s', 'wpenon' ), $gebaeude->geschossanzahl() ); ?></p>
	<p><?php printf( __( 'Geschosshöhe: %s m', 'wpenon' ), str_replace('.',',', $gebaeude->geschosshoehe() ) ); ?></p>
	<p><?php printf( __( 'Anzahl der Wohnungen: %s', 'wpenon' ), $gebaeude->anzahl_wohnungen() ); ?></p>  
 

	<h3>Grundriss</h3>
	<p>Ausrichtung des Gebäudes: <?php echo $gebaeude->grundriss()->ausrichtung(); ?></p>
	<table>
	<tr>
		<th>Seite</th>    
		<th>Länge</th>
		<th>Ausrichtung</th>    
	</tr>
	<?php foreach ( $gebaeude->grundriss()->waende() as $wand ) : ?>
	<tr>
		<td><?php echo $wand; ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->grundriss()->wand_laenge( $wand ) ); ?> m</td>
		<td><?php echo $gebaeude->grundriss()->wand_himmelsrichtung( $wand ); ?></td>
	</tr>
	<?php endforeach; ?>
	</table>

	<hr />

	<h2>Bauteile</h2>

	<h3>Wände</h3>
	<table>
	<tr>
		<th>Bauteil</th>    
		<th>Fläche</th>
		<th>U-Wert</th>
		<th>Fx Faktor</th>
		<th>Transmissionswärmekoeffizient ht</th>
	<th>Dämmstärke</th>
	</tr>
	<?php foreach ( $gebaeude->bauteile()->waende()->alle() as $wand ) : ?>
	<tr>
		<td><?php echo $wand->name(); ?></td>
		<td><?php echo str_replace('.',',', $wand->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo str_replace('.',',', $wand->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo str_replace('.',',', $wand->fx() ); ?></td>
		<td><?php echo str_replace('.',',', $wand->ht() ); ?> W/K</td>
	<td><?php echo str_replace('.',',', $wand->daemmung() ); ?> m</td>
	</tr>
	<?php endforeach; ?>
	</table>

	<h3>Fenster</h3>
	<table>
	<tr>
		<th>Bauteil</th>    
		<th>Fläche</th>
		<th>G-Wert</th>
		<th>U-Wert</th>
		<th>Fx Faktor</th>
		<th>Transmissionswärmekoeffizient ht</th>
	</tr>
	<?php foreach ( $gebaeude->bauteile()->filter( 'Fenster' )->alle() as $fenster ) : ?>
	<tr>
		<td><?php echo $fenster->name(); ?></td>
		<td><?php echo str_replace('.',',', $fenster->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo str_replace('.',',', $fenster->gwert() ); ?></td>
		<td><?php echo str_replace('.',',', $fenster->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo str_replace('.',',', $fenster->fx() ); ?></td>
		<td><?php echo str_replace('.',',', $fenster->ht() ); ?> W/K</td>
	</tr>
	<?php endforeach; ?>
	</table>

	<h3>Heizköpernischen</h3>
	<?php if ( $gebaeude->bauteile()->filter( 'Heizkoerpernische' )->anzahl() > 0 ) : ?>
	<table>
	<tr>
		<th>Bauteil</th>    
		<th>Fläche</th>
		<th>U-Wert</th>
		<th>Fx Faktor</th>
		<th>Transmissionswärmekoeffizient ht</th>
	</tr>
		<?php foreach ( $gebaeude->bauteile()->filter( 'Heizkoerpernische' )->alle() as $heizkoerpernische ) : ?>
	<tr>
		<td><?php echo $heizkoerpernische->name(); ?></td>
		<td><?php echo str_replace('.',',', $heizkoerpernische->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo str_replace('.',',', $heizkoerpernische->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo str_replace('.',',', $heizkoerpernische->fx() ); ?></td>
		<td><?php echo str_replace('.',',', $heizkoerpernische->ht() ); ?> W/K</td>
	</tr>
	<?php endforeach; ?>
	</table>
	<?php else : ?>
	<p class="lead"><?php _e( 'Keine Heizkörpernischen vorhanden.', 'wpenon' ); ?></p>
	<?php endif; ?>

	<h3>Rolladenkästen</h3>
	<?php if ( $gebaeude->bauteile()->filter( 'Rolladenkasten' )->anzahl() > 0 ) : ?>
	<table>
	<tr>
		<th>Bauteil</th>    
		<th>Fläche</th>
		<th>U-Wert</th>
		<th>Fx Faktor</th>
		<th>Transmissionswärmekoeffizient ht</th>
	</tr>
		<?php foreach ( $gebaeude->bauteile()->filter( 'Rolladenkasten' )->alle() as $rolladenkaesten ) : ?>
	<tr>
		<td><?php echo $rolladenkaesten->name(); ?></td>
		<td><?php echo str_replace('.',',', $rolladenkaesten->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo str_replace('.',',', $rolladenkaesten->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo str_replace('.',',', $rolladenkaesten->fx() ); ?></td>
		<td><?php echo str_replace('.',',', $rolladenkaesten->ht() ); ?> W/K</td>
	</tr>
	<?php endforeach; ?>
	</table>
	<?php else : ?>
	<p class="lead"><?php _e( 'Keine Rolladenkästen vorhanden.', 'wpenon' ); ?></p>
	<?php endif; ?>	

	<?php if ( $gebaeude->dach_vorhanden() ) : ?>
	<h3>Dach</h3>
	<table>
	<tr>
		<th>Bauteil</th>    
		<th>Fläche</th>
	<th>Höhe</th>
		<th>U-Wert</th>
		<th>Fx Faktor</th>
		<th>Transmissionswärmekoeffizient ht</th>    
	</tr>
		<tr>
		<td><?php echo $gebaeude->dach()->name(); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->dach()->flaeche() ); ?> m<sup>2</sup></td>
	<td><?php echo str_replace('.',',', $gebaeude->dach()->hoehe() ); ?> m</td>
		<td><?php echo str_replace('.',',', $gebaeude->dach()->uwert() ); ?> W/(m<sup>2</sup>K)</td>    
		<td><?php echo str_replace('.',',', $gebaeude->dach()->fx() ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->dach()->ht() ); ?> W/K</td>
		</tr>
	</table>
	<?php else : ?>
	<h3>Decke</h3>
	<table>
	<tr>
		<th>Bauteil</th>    
		<th>Fläche</th>
		<th>U-Wert</th>
		<th>Fx Faktor</th>
		<th>Transmissionswärmekoeffizient ht</th>    
	</tr>
		<?php foreach ( $gebaeude->bauteile()->filter( 'Decke' )->alle() as $decke ) : ?>
		<tr>
		<td><?php echo $decke->name(); ?></td>
		<td><?php echo str_replace('.',',', $decke->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo str_replace('.',',', $decke->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo str_replace('.',',', $decke->fx() ); ?></td>
		<td><?php echo str_replace('.',',', $decke->ht() ); ?> W/K</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php endif; ?>

	<h3>Böden</h3>
	<table>
	<tr>
		<th>Bauteil</th>    
		<th>Fläche</th>
		<th>U-Wert</th>
		<th>Fx Faktor</th>
		<th>Transmissionswärmekoeffizient ht</th>    
	</tr>
		<?php foreach ( $gebaeude->bauteile()->filter( 'Boden' )->alle() as $boden ) : ?>
		<tr>
		<td><?php echo $boden->name(); ?></td>
		<td><?php echo str_replace('.',',', $boden->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo str_replace('.',',', $boden->uwert() ); ?> W/(m<sup>2</sup>K)</td>    
		<td><?php echo str_replace('.',',', $boden->fx() ); ?></td>
		<td><?php echo str_replace('.',',', $boden->ht() ); ?> W/K</td>
		</tr>
		<?php endforeach; ?>
	</table>
	

	<?php if ( $gebaeude->keller_vorhanden() ) : ?>
	<h3>Keller</h3>
	<p class="lead"><?php printf( __( 'Unterkellerung: %s;', 'wpenon' ), str_replace('.',',', $gebaeude->keller()->anteil() ) ); ?></p>
	<p class="lead"><?php printf( __( 'Kellerfläche A<sub>K</sub>: %s m&sup2;', 'wpenon' ), str_replace('.',',', $gebaeude->keller()->boden_flaeche() ) ); ?></p>
	<p class="lead"><?php printf( __( 'Kellerwandlänge U<sub>K</sub>: %s m;', 'wpenon' ), str_replace('.',',', $gebaeude->keller()->wand_laenge() ) ); ?></p>
	<p class="lead"><?php printf( __( 'Kellerwandhöhe H<sub>K</sub>: %s m;', 'wpenon' ), str_replace('.',',', $gebaeude->keller()->wand_hoehe() ) ); ?></p>
	<p class="lead"><?php printf( __( 'Kellervolumen V<sub>K</sub>: %s m&sup3;', 'wpenon' ), str_replace('.',',', $gebaeude->keller()->volumen() ) ); ?></p>
	<table>
	<tr>
		<th>Wand</th>    
		<th>Fläche</th>
		<th>U-Wert</th>
		<th>Fx Faktor</th>
		<th>Transmissionswärmekoeffizient ht</th>
	</tr>
		<?php foreach ( $gebaeude->bauteile()->filter( 'Kellerwand' )->alle() as $wand ) : ?>
		<tr>
		<td><?php echo $wand->name(); ?></td>
		<td><?php echo str_replace('.',',', $wand->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo str_replace('.',',', $wand->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo str_replace('.',',', $wand->fx() ); ?></td>
		<td><?php echo str_replace('.',',', $wand->ht() ); ?> W/K</td>
		</tr>
		<?php endforeach; ?>
		<?php foreach ( $gebaeude->bauteile()->filter( 'Kellerboden' )->alle() as $wand ) : ?>
		<tr>
		<td><?php echo $wand->name(); ?></td>
		<td><?php echo str_replace('.',',', $wand->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo str_replace('.',',', $wand->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo str_replace('.',',', $wand->fx() ); ?></td>
		<td><?php echo str_replace('.',',', $wand->ht() ); ?> W/K</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php endif; ?>

	<?php if ( $gebaeude->anbau_vorhanden() ) : ?>
	<h3>Anbau</h3>
	<p class="lead"><?php printf( __( 'Anbau Fläche: %s m&sup2; ', 'wpenon' ), str_replace('.',',', $gebaeude->anbau()->grundriss()->flaeche() ) ); ?></p>
	<p class="lead"><?php printf( __( 'Anbau Volumen: %s m&sup2; ', 'wpenon' ), str_replace('.',',', $gebaeude->anbau()->volumen() ) ); ?></p>
	<table>
	<tr>
		<th>Wand</th>    
		<th>Fläche</th>
		<th>U-Wert</th>
		<th>Fx Faktor</th>
		<th>Transmissionswärmekoeffizient ht</th>
	</tr>
		<?php foreach ( $gebaeude->bauteile()->filter( 'Anbauwand' )->alle() as $wand ) : ?>
		<tr>
		<td><?php echo $wand->name(); ?></td>
		<td><?php echo str_replace('.',',', $wand->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo str_replace('.',',', $wand->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo str_replace('.',',', $wand->fx() ); ?></td>
		<td><?php echo str_replace('.',',', $wand->ht() ); ?> W/K</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<table>
	<tr>
		<th>Fenster</th>    
		<th>Fläche</th>
		<th>U-Wert</th>
		<th>Fx Faktor</th>
		<th>Transmissionswärmekoeffizient ht</th>
	</tr>
		<?php foreach ( $gebaeude->bauteile()->filter( 'Anbaufenster' )->alle() as $anbaufenster ) : ?>
		<tr>
		<td><?php echo $anbaufenster->name(); ?></td>
		<td><?php echo str_replace('.',',', $anbaufenster->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo str_replace('.',',', $anbaufenster->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo str_replace('.',',', $anbaufenster->fx() ); ?></td>
		<td><?php echo str_replace('.',',', $anbaufenster->ht() ); ?> W/K</td>
		</tr>
		<?php endforeach; ?>
	</table>

	<h3>Anbauboden</h3>
	<table>
	<tr>
		<th>Bauteil</th>    
		<th>Fläche</th>
		<th>U-Wert</th>
		<th>Fx Faktor</th>
		<th>Transmissionswärmekoeffizient ht</th>    
	</tr>
		<?php foreach ( $gebaeude->bauteile()->filter( 'Anbauboden' )->alle() as $boeden ) : ?>
		<tr>
		<td><?php echo $boeden->name(); ?></td>
		<td><?php echo str_replace('.',',', $boeden->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo str_replace('.',',', $boeden->uwert() ); ?> W/(m<sup>2</sup>K)</td>    
		<td><?php echo str_replace('.',',', $boeden->fx() ); ?></td>
		<td><?php echo str_replace('.',',', $boeden->ht() ); ?> W/K</td>
		</tr>
		<?php endforeach; ?>
	</table>

	<h3>Anbaudecke</h3>
	<table>
	<tr>
		<th>Bauteil</th>    
		<th>Fläche</th>
		<th>U-Wert</th>
		<th>Fx Faktor</th>
		<th>Transmissionswärmekoeffizient ht</th>    
	</tr>
		<?php foreach ( $gebaeude->bauteile()->filter( 'Anbaudecke' )->alle() as $boeden ) : ?>
		<tr>
		<td><?php echo $boeden->name(); ?></td>
		<td><?php echo str_replace('.',',', $boeden->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo str_replace('.',',', $boeden->uwert() ); ?> W/(m<sup>2</sup>K)</td>    
		<td><?php echo str_replace('.',',', $boeden->fx() ); ?></td>
		<td><?php echo str_replace('.',',', $boeden->ht() ); ?> W/K</td>
		</tr>
		<?php endforeach; ?>
	</table>

	<?php endif; ?>

	<h3>Transmission</h3>

	
	<p><?php printf( __( 'Transmissionswärmekoeffizient Bauteile ht: %s', 'wpenon' ), str_replace('.',',', $gebaeude->bauteile()->ht() ) ); ?></p>
	<p><?php printf( __( 'Transmissionswärmekoeffizient Fenster hw: %s', 'wpenon' ), str_replace('.',',', $gebaeude->bauteile()->hw() ) ); ?></p>
	<p><?php printf( __( 'Wärmebrückenzuschlag (ht_wb): %s', 'wpenon' ), str_replace('.',',', $gebaeude->ht_wb() ) ); ?></p>
	<p><?php printf( __( 'Transmissionswärmekoeffizient Gesamt ht<sub>ges</sub>: %s', 'wpenon' ), str_replace('.',',', $gebaeude->ht_ges() ) ); ?></p>
	<p><?php printf( __( 'Wärmetransferkoeffizient des Gebäudes. (h ges): %s', 'wpenon' ), str_replace('.',',', $gebaeude->h_ges() ) ); ?></p>
	<p><?php printf( __( 'Tau: %s', 'wpenon' ), str_replace('.',',', $gebaeude->tau() ) ); ?></p>
	<p><?php printf( __( 'Maximaler Wärmestrom Q: %s', 'wpenon' ), str_replace('.',',', $gebaeude->q() ) ); ?></p>

	
	

	<h3>Luftwechsel</h3>

	<p><?php printf( __( 'Maximale Heizlast h<sub>max</sub>: %s', 'wpenon' ), str_replace('.',',', $gebaeude->luftwechsel()->h_max() ) ); ?></p>
	<p><?php printf( __( 'Maximale Heizlast spezifisch h<sub>max,spez</sub>: %s', 'wpenon' ), str_replace('.',',', $gebaeude->luftwechsel()->h_max_spezifisch() ) ); ?></p>
	<p><?php printf( __( 'Luftwechselrate n: %s', 'wpenon' ), str_replace('.',',', $gebaeude->luftwechsel()->n() ) ); ?></p>
	<p><?php printf( __( 'Luftechselvolumen h<sub>v</sub>: %s', 'wpenon' ), str_replace('.',',', $gebaeude->luftwechsel()->hv() ) ); ?></p>
	<p><?php printf( __( 'Gesamtluftwechselrate n<sub>0</sub>: %s', 'wpenon' ), str_replace('.',',', $gebaeude->luftwechsel()->n0() ) ); ?></p>
	<p><?php printf( __( 'Korrekturfakror f<sub>win,1</sub>: %s', 'wpenon' ), str_replace('.',',', $gebaeude->luftwechsel()->fwin1() ) ); ?></p>
	<p><?php printf( __( 'Korrekturfakror f<sub>win,2</sub>: %s', 'wpenon' ), str_replace('.',',', $gebaeude->luftwechsel()->fwin2() ) ); ?></p>
	<p><?php printf( __( 'n_anl: %s', 'wpenon' ), str_replace('.',',', $gebaeude->luftwechsel()->n_anl() ) ); ?></p>
	<p><?php printf( __( 'n_wrg: %s', 'wpenon' ), str_replace('.',',', $gebaeude->luftwechsel()->n_wrg() ) ); ?></p>

	

	<hr />

	<h2>Bilanzierung</h2>

	<h3>Interne Wärmequellen</h3>
	<table>
	<tr>
		<th>Monat</th>    
		<th>Qi<sub>p</sub></th>
		<th>Qi<sub>w</sub></th>
		<th>Qi<sub>s</sub></th>
		<th>Qi<sub>h</sub></th>
		<th>Qi</th>
	</tr>
		<?php foreach ( $jahr->monate() as $monat ) : ?>
		<tr>
		<td><?php echo $monat->name(); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->qi_prozesse_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->qi_wasser_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->qi_solar_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->qi_heizung_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->qi_monat( $monat->slug() ) ); ?></td>
		</tr>
		<?php endforeach; ?>
		<tr>
		<td><b>Gesamt</b></td>
		<td><?php echo str_replace('.',',', $gebaeude->qi_prozesse() ) ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->qi_wasser() ) ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->qi_solar() ) ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->qi_heizung() ) ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->qi() ) ?></td>
		</tr>
	</table>

	<h3>fum</h3>
	<table>
	<tr>
		<th>Monat</th>    
		<th>fum</th>
	</tr>
		<?php foreach ( $jahr->monate() as $monat ) : ?>
		<tr>
		<td><?php echo $monat->name(); ?></td>
		<td><?php echo str_replace('.',',', fum( $monat->slug() ) ); ?></td>
		</tr>
		<?php endforeach; ?>
	</table>

	<table>
	<tr>
		<th>Monat</th>    
		<th>P*H<sub>sink</sub></th>    
		<th>PH<sub>sink</sub></th>    
		<th>PH<sub>source</sub></th>
		<th>Qh</th>
		<th>Q<sub>W,B</sub></th>
	</tr>
		<?php foreach ( $jahr->monate() as $monat ) : ?>
		<tr>
		<td><?php echo $monat->name(); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->psh_sink_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->ph_sink_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->ph_source_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->qh_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->QWB_monat( $monat->slug() ) ); ?></td>
		</tr>
		<?php endforeach; ?>
		<tr>
		<td><b>Gesamt</b></td>
		<td></td>
		<td></td>
		<td></td>
		<td><?php echo str_replace('.',',', $gebaeude->qh() ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->QWB() ); ?></td>
		</tr>
	</table>

	<h3>Jahr Gesamt</h3>
	<table>
		<tr>
			<th>ßhma</th>
			<th>thm</th>
			<th>ith_rl</th>
			<th>Qi</th>
			<th>Q<sub>W,B</sub></th>
			<th>Qh</th>
		</tr>
		<tr>
			<td><?php echo str_replace('.',',', $gebaeude->ßhma() ); ?></td>
			<td><?php echo str_replace('.',',', $gebaeude->thm() ); ?></td>
			<td><?php echo str_replace('.',',', $gebaeude->ith_rl() ); ?></td>
			<td><?php echo str_replace('.',',', $gebaeude->qi() ); ?></td>
			<td><?php echo str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->QWB() ); ?></td>
			<td><?php echo str_replace('.',',', $gebaeude->qh() ); ?></td>
		</tr>
	</table>

	<h3>Korrekturfaktoren und wetere Werte</h3>

	<table>
	<tr>
		<th>Monat</th>
		<th>pi</th>    
		<th>ym</th>
		<th>nm</th>
		<th>flna</th>
		<th>trl</th>
		<th>ith_rl</th>
	</tr>
		<?php foreach ( $jahr->monate() as $monat ) : ?>
		<tr>
		<td><?php echo $monat->name(); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->pi_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->ym_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->nm_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->flna_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->trl_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->ith_rl_monat( $monat->slug() ) ); ?></td>
		</tr>
		<?php endforeach; ?>
		<tr>
		<td><b>Gesamt</b></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td><?php echo str_replace('.',',', $gebaeude->ith_rl() ); ?></td>
		</tr>
	</table>

	<table>
	<tr>
		<th>Monat</th>
		<th>k</th>
		<th>θih</th>
		<th>ßem1</th>
		<th>ßhm</th>
		<th>thm</th>
	</tr>
		<?php foreach ( $jahr->monate() as $monat ) : ?>
		<tr>
		<td><?php echo $monat->name(); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->k_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->bilanz_innentemperatur()->θih_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->mittlere_belastung()->ßem1( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->ßhm_monat( $monat->slug() ) ); ?></td>
		<td><?php echo str_replace('.',',', $gebaeude->thm_monat( $monat->slug() ) ); ?></td>
		</tr>
		<?php endforeach; ?>
		<tr>
		<td><b>Gesamt</b></td>
		<td></td>
		<td></td>
		<td><?php echo $gebaeude->mittlere_belastung()->ßemMax(); ?> (ßemMax)</td>
		<td><?php echo str_replace('.',',', $gebaeude->ßhma() ); ?> (ßhma)</td>
		<td><?php echo str_replace('.',',', $gebaeude->thm() ); ?></td>
		</tr>
	</table>	

	<hr />

	<h2>Heizsystem</h2>

	<h3>Heizungsanlage</h3>
	<table>
	<tr>
		<th>Heizungstyp</th>    
		<th>Energieträger</th>    
		<th>Auslegungstemperatur</th>
		<th>Anteil</th>
	</tr>
	<?php foreach ( $gebaeude->heizsystem()->heizungsanlagen()->alle() as $heizungsanlage ) : ?>
		<tr>
		<td><?php echo $heizungsanlage->typ(); ?></td>
		<td><?php echo $heizungsanlage->energietraeger(); ?></td>
		<td><?php echo $heizungsanlage->auslegungstemperaturen(); ?></td>
		<td><?php echo $heizungsanlage->prozentualer_anteil(); ?></td>
		</tr>
	<?php endforeach; ?>	
	</table>

	<h3>Übergabesystem</h3>
	<table>
	<tr>
		<th>Übergabetyp</th>
		<th>Auslegungstemperatur</th>
		<th>Anteil</th>
		<th>ehce</th>
	</tr>
	<?php foreach ( $gebaeude->heizsystem()->uebergabesysteme()->alle() as $uebergabesystem ) : ?>
		<tr>
		<td><?php echo $uebergabesystem->typ(); ?></td>
		<td><?php echo $uebergabesystem->auslegungstemperaturen(); ?></td>
		<td><?php echo $uebergabesystem->prozentualer_anteil(); ?></td>
		<td><?php echo str_replace('.',',', $uebergabesystem->ehce() ); ?></td>
	<?php endforeach; ?>	
	</table>

	<h3>Heizsystem</h3>

	<p><?php printf( __( 'Nutzbare Wärme fa<sub>h</sub>: %s', 'wpenon' ), $gebaeude->heizsystem()->fa_h() ); ?></p>
	<p><?php printf( __( 'Aufwandszahl für freie Heizflächen (ehce): %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->ehce() ) ); ?></p>	
	<p><?php printf( __( 'Mittlere Belastung bei Übergabe der Heizung (ßhce): %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->ßhce() ) ); ?></p>
	<p><?php printf( __( 'Flächenbezogene leistung der Übergabe der Heizung (qhce): %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->qhce() ) ); ?></p>
	<p><?php printf( __( 'ßhd: %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->ßhd() ) ); ?></p>
	<p><?php printf( __( 'fßd: %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->fßd() ) ); ?></p>
	<p><?php printf( __( 'ehd0: %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->ehd0() ) ); ?></p>
	<p><?php printf( __( 'ehd1: %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->ehd1() ) ); ?></p>
	<p><?php printf( __( 'ehd: %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->ehd() ) ); ?></p>
	<p><?php printf( __( 'ehd korrektur: %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->ehd_korrektur() ) ); ?></p>


	<?php if ( $gebaeude->heizsystem()->pufferspeicher_vorhanden() ) : ?>
	<h3>Pufferspeicher</h3>
	<p><?php printf( __( 'Nennleistung Pufferspeicher (pwn): %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->pufferspeicher()->pwn() ) ); ?></p>
	<p><?php printf( __( '(pn): %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->pufferspeicher()->pn() ) ); ?></p>
	<p><?php printf( __( 'Korrekturfaktor mittlere Belastung des Pufferspeichers fßhs: %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->pufferspeicher()->fßhs() ) ); ?></p>
	<p><?php printf( __( 'Mittlere Belastung für Speicherung ßhs: %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->pufferspeicher()->ßhs() ) ); ?></p>
	<p><?php printf( __( 'Korrekturfaktor für beliebige mittlere Berlastung und Laufzeit der Heizung fhs: %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->pufferspeicher()->fhs() ) ); ?></p>
	<p><?php printf( __( 'Berechnetes Volumen: %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->pufferspeicher()->volumen() ) ); ?></p>
	<p><?php printf( __( 'Volumen Pufferspeicher vs1: %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->pufferspeicher()->vs1() ) ); ?></p>
	<p><?php printf( __( 'Volumen Pufferspeicher vs2: %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->pufferspeicher()->vs2() ) ); ?></p>
	<p><?php printf( __( 'Wärmeabgabe Pufferspeicher (Qhs0Vs1): %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->pufferspeicher()->Qhs0Vs1() ) ); ?></p>
	<p><?php printf( __( 'Wärmeabgabe Pufferspeicher (Qhs0Vs2): %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->pufferspeicher()->Qhs0Vs2() ) ); ?></p>
	<p><?php printf( __( 'Wärmeabgabe Pufferspeicher Gesamt (Qhs): %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->pufferspeicher()->Qhs() ) ); ?></p>
	<p><?php printf( __( 'Aufwandszahl für Pufferspeicher (ehs): %s', 'wpenon' ), str_replace('.',',', $gebaeude->heizsystem()->pufferspeicher()->ehs() ) ); ?></p>
	<?php endif; ?>

	<h3>Trinkwarmwasseranlage</h3>
	
	<p><?php printf( __( 'Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen Faw: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Faw() ) ); ?></p>
	<p><?php printf( __( 'Nutzwärmebedarf für Trinkwasser qwb: %s kWh/(ma)', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->nutzwaermebedarf_trinkwasser() ) ); ?></p>	
	<p><?php printf( __( 'Q<sub>w,b</sub>: %s kWh', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->QWB() ) ); ?></p>
	<p><?php printf( __( 'Interne Wärmequelle infolge von Warmwasser Qi<sub>w</sub>: %s', 'wpenon' ), str_replace('.',',', $gebaeude->qi_wasser() ) ); ?></p>
	<p><?php printf( __( 'Jährlicher Nutzwaermebedarf für Trinkwasser (qwb): %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->nutzwaermebedarf_trinkwasser() ) ); ?></p>
	<p><?php printf( __( 'Berechnung des monatlichen Wärmebedarfs für Warmwasser(QWB) für ein Jahr: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->QWB() ) ); ?></p>

	<h4>Aufwandszahlen Trinkwarmwasser</h4>

	<p><?php printf( __( 'Zwischenwert für die Berechnung von ewd (ewd0): %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->ewd0() ) ); ?></p>
	<p><?php printf( __( 'Aufwandszahlen für die Verteilung von Trinkwarmwasser (ewd): %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->ewd() ) ); ?></p>
	<p><?php printf( __( 'Korrekturfaktor (fwb): %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->fwb() ) ); ?></p>
	<p><?php printf( __( 'Volumen Speicher 1 in Litern. (Vs01): %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Vs01() ) ); ?></p>
	<p><?php printf( __( 'Volumen Speicher 2 in Litern. (Vs02): %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Vs02() ) ); ?></p>
	<p><?php printf( __( 'Volumen Speicher 3 in Litern. (Vs03): %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Vs03() ) ); ?></p>
	<p><?php printf( __( 'Volumen Speicher Gesamt in Litern. (Vs0): %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Vs0() ) ); ?></p>
	<p><?php printf( __( 'Berechnung von Vsw1: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Vsw1() ) ); ?></p>
	<p><?php printf( __( 'Berechnung von Vsw2: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Vsw2() ) ); ?></p>	
	<p><?php printf( __( 'Berechnung von Qws01: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Qws01() ) ); ?></p>
	<p><?php printf( __( 'Berechnung von Qws02: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Qws02() ) ); ?></p>
	
	<?php if ( $gebaeude->trinkwarmwasseranlage()->solarthermie_vorhanden() ) : ?>
	<h4>Solarthermie</h4>
	<p><?php printf( __( 'Berechnung von Vsaux0: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Vsaux0() ) ); ?></p>
	<p><?php printf( __( 'Berechnung von Vssol0: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Vssol0() ) ); ?></p>
	<p><?php printf( __( 'Berechnung von Ac0: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Ac0() ) ); ?></p>
	<p><?php printf( __( 'Berechnung von Qwsola0: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Qwsola0() ) ); ?></p>	
	<br />
	<p><?php printf( __( 'Berechnung von Vsaux: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Vsaux() ) ); ?></p>
	<p><?php printf( __( 'Berechnung von Vssol: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Vssol() ) ); ?></p>
	<p><?php printf( __( 'Berechnung von Ac: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Ac() ) ); ?></p>
	<p><?php printf( __( 'Berechnung von Qwsola: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Qwsola() ) ); ?></p>
	<?php endif; ?>

	<br>

	<p><?php printf( __( 'Berechnung von Qws: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->Qws() ) ); ?></p>
	<p><?php printf( __( 'Berechnung von ews: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->ews() ) ); ?></p>
	<p><?php printf( __( 'Berechnung von keew: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->keew() ) ); ?></p>
	<p><?php printf( __( 'Berechnung von keeh: %s', 'wpenon' ), str_replace('.',',', $gebaeude->trinkwarmwasseranlage()->keeh() ) ); ?></p>
	
	
	

	
</div>

