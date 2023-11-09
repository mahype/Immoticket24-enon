<?php
/**
 * @package WPENON
 * @version 1.0.0
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

use Enev\Schema202302\Calculations\Helfer\Jahr;

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
	<p><?php printf( __( 'Hüllvolumen V<sub>e</sub>: %s m&sup3;', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->huellvolumen() ) ); ?></p>
	<p><?php printf( __( 'Hüllfäche<sub>e</sub>: %s m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->huellflaeche() ) ); ?></p>
	<p><?php printf( __( 'ave Verhältnis: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->ave_verhaeltnis() ) ); ?></p>
	<p><?php printf( __( 'Nutzfläche A<sub>N</sub>: %s m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->nutzflaeche() ) ); ?></p>
	<p><?php printf( __( 'Anzahl der Geschosse: %s', 'wpenon' ), $gebaeude->geschossanzahl() ); ?></p>
	<p><?php printf( __( 'Geschosshöhe: %s m', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->geschosshoehe() ) ); ?></p>
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
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->grundriss()->wand_laenge( $wand ) ); ?> m</td>
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
		<td><?php echo \WPENON\Util\Format::float( $wand->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo \WPENON\Util\Format::float( $wand->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo \WPENON\Util\Format::float( $wand->fx() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $wand->ht() ); ?> W/K</td>
	<td><?php echo \WPENON\Util\Format::float( $wand->daemmung() ); ?> m</td>
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
		<td><?php echo \WPENON\Util\Format::float( $fenster->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo \WPENON\Util\Format::float( $fenster->gwert() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $fenster->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo \WPENON\Util\Format::float( $fenster->fx() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $fenster->ht() ); ?> W/K</td>
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
		<td><?php echo \WPENON\Util\Format::float( $heizkoerpernische->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo \WPENON\Util\Format::float( $heizkoerpernische->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo \WPENON\Util\Format::float( $heizkoerpernische->fx() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $heizkoerpernische->ht() ); ?> W/K</td>
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
		<td><?php echo \WPENON\Util\Format::float( $rolladenkaesten->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo \WPENON\Util\Format::float( $rolladenkaesten->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo \WPENON\Util\Format::float( $rolladenkaesten->fx() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $rolladenkaesten->ht() ); ?> W/K</td>
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
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->dach()->flaeche() ); ?> m<sup>2</sup></td>
	<td><?php echo \WPENON\Util\Format::float( $gebaeude->dach()->hoehe() ); ?> m</td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->dach()->uwert() ); ?> W/(m<sup>2</sup>K)</td>    
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->dach()->fx() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->dach()->ht() ); ?> W/K</td>
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
		<td><?php echo \WPENON\Util\Format::float( $decke->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo \WPENON\Util\Format::float( $decke->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo \WPENON\Util\Format::float( $decke->fx() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $decke->ht() ); ?> W/K</td>
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
		<td><?php echo \WPENON\Util\Format::float( $boden->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo \WPENON\Util\Format::float( $boden->uwert() ); ?> W/(m<sup>2</sup>K)</td>    
		<td><?php echo \WPENON\Util\Format::float( $boden->fx() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $boden->ht() ); ?> W/K</td>
		</tr>
		<?php endforeach; ?>
	</table>
	

	<?php if ( $gebaeude->keller_vorhanden() ) : ?>
	<h3>Keller</h3>
	<p class="lead"><?php printf( __( 'Unterkellerung: %s;', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->keller()->anteil() ) ); ?></p>
	<p class="lead"><?php printf( __( 'Kellerfläche A<sub>K</sub>: %s m&sup2;', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->keller()->boden_flaeche() ) ); ?></p>
	<p class="lead"><?php printf( __( 'Kellerwandlänge U<sub>K</sub>: %s m;', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->keller()->wand_laenge() ) ); ?></p>
	<p class="lead"><?php printf( __( 'Kellerwandhöhe H<sub>K</sub>: %s m;', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->keller()->wand_hoehe() ) ); ?></p>
	<p class="lead"><?php printf( __( 'Kellervolumen V<sub>K</sub>: %s m&sup3;', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->keller()->volumen() ) ); ?></p>
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
		<td><?php echo \WPENON\Util\Format::float( $wand->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo \WPENON\Util\Format::float( $wand->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo \WPENON\Util\Format::float( $wand->fx() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $wand->ht() ); ?> W/K</td>
		</tr>
		<?php endforeach; ?>
		<?php foreach ( $gebaeude->bauteile()->filter( 'Kellerboden' )->alle() as $wand ) : ?>
		<tr>
		<td><?php echo $wand->name(); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $wand->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo \WPENON\Util\Format::float( $wand->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo \WPENON\Util\Format::float( $wand->fx() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $wand->ht() ); ?> W/K</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php endif; ?>

	<?php if ( $gebaeude->anbau_vorhanden() ) : ?>
	<h3>Anbau</h3>
	<p class="lead"><?php printf( __( 'Anbau Fläche: %s m&sup2; ', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->anbau()->grundriss()->flaeche() ) ); ?></p>
	<p class="lead"><?php printf( __( 'Anbau Volumen: %s m&sup2; ', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->anbau()->volumen() ) ); ?></p>
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
		<td><?php echo \WPENON\Util\Format::float( $wand->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo \WPENON\Util\Format::float( $wand->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo \WPENON\Util\Format::float( $wand->fx() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $wand->ht() ); ?> W/K</td>
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
		<td><?php echo \WPENON\Util\Format::float( $anbaufenster->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo \WPENON\Util\Format::float( $anbaufenster->uwert() ); ?> W/(m<sup>2</sup>K)</td>
		<td><?php echo \WPENON\Util\Format::float( $anbaufenster->fx() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $anbaufenster->ht() ); ?> W/K</td>
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
		<td><?php echo \WPENON\Util\Format::float( $boeden->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo \WPENON\Util\Format::float( $boeden->uwert() ); ?> W/(m<sup>2</sup>K)</td>    
		<td><?php echo \WPENON\Util\Format::float( $boeden->fx() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $boeden->ht() ); ?> W/K</td>
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
		<td><?php echo \WPENON\Util\Format::float( $boeden->flaeche() ); ?> m<sup>2</sup></td>
		<td><?php echo \WPENON\Util\Format::float( $boeden->uwert() ); ?> W/(m<sup>2</sup>K)</td>    
		<td><?php echo \WPENON\Util\Format::float( $boeden->fx() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $boeden->ht() ); ?> W/K</td>
		</tr>
		<?php endforeach; ?>
	</table>

	<?php endif; ?>

	<h3>Transmission</h3>

	<p><?php printf( __( 'Transmissionswärmekoeffizient Bauteile ht: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->bauteile()->ht() ) ); ?></p>
	<p><?php printf( __( 'Transmissionswärmekoeffizient Fenster hw: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->bauteile()->hw() ) ); ?></p>
	<p><?php printf( __( 'Transmissionswärmekoeffizient Gesamt ht<sub>ges</sub>: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->ht() ) ); ?></p>
	<p><?php printf( __( 'Tau: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->tau() ) ); ?></p>
	<p><?php printf( __( 'Maximaler Wärmestrom Q: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->q() ) ); ?></p>

	<hr />

	<h2>Bilanzierung</h2>

	<table>
	<tr>
		<th>Monat</th>    
		<th>P*H<sub>sink</sub></th>    
		<th>PH<sub>sink</sub></th>    
		<th>PH<sub>source</sub></th>
		<th>Qs</th>
		<th>Qh</th>
		<th>ßhm</th>
		<th>thm</th>
		<th>Q<sub>W,B</sub></th>
	</tr>
		<?php foreach ( $jahr->monate() as $monat ) : ?>
		<tr>
		<td><?php echo $monat->name(); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->psh_sink_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->ph_sink_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->ph_source_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->qs_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->qh_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->ßhm_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->thm_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->wasserversorgung()->QWB_monat( $monat->slug() ) ); ?></td>
		</tr>
		<?php endforeach; ?>
		<tr>
		<td><b>Gesamt</b></td>
		<td></td>
		<td></td>
		<td></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->qs() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->qh() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->ßhma() ); ?> (ßhma)</td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->thm() ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->wasserversorgung()->QWB() ); ?></td>
		</tr>
	</table>

	<h3>Korrekturfaktoren</h3>

	<table>
	<tr>
		<th>Monat</th>    
		<th>ym</th>
		<th>nm</th>
		<th>flna</th>
		<th>trl</th>
		<th>ith_rl</th>
		<th>k</th>
	</tr>
		<?php foreach ( $jahr->monate() as $monat ) : ?>
		<tr>
		<td><?php echo $monat->name(); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->ym_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->nm_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->flna_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->trl_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->ith_rl_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->k_monat( $monat->slug() ) ); ?></td>
		</tr>
		<?php endforeach; ?>
		<tr>
		<td><b>Gesamt</b></td>
		<td></td>
		<td></td>
		<td></td>
		<td></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->ith_rl() ); ?></td>
		<td></td>
		</tr>
	</table>

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
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->qi_prozesse_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->qi_wasser_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->qs_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->qi_heizung_monat( $monat->slug() ) ); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->qi_monat( $monat->slug() ) ); ?></td>
		</tr>
		<?php endforeach; ?>
		<tr>
		<td><b>Gesamt</b></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->qi_prozesse() ) ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->qi_wasser() ) ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->qs() ) ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->qi_heizung() ) ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->qi() ) ?></td>
		</tr>
	</table>

	<hr />

	<h2>Lüftung</h2>

	<h3>Bilanz-Innentemperatur</h3>
	<table>
	<tr>
		<th>Monat</th>    
		<th>Tage</th>    
		<th>Temperatur</th>
	</tr>
		<?php foreach ( $jahr->monate() as $monat ) : ?>
		<tr>
		<td><?php echo $monat->name(); ?></td>
		<td><?php echo $monat->tage(); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->bilanz_innentemperatur()->θih_monat( $monat->slug() ) ); ?></td>
		</tr>
		<?php endforeach; ?>
	</table>

	<h3>Mittlere Belastung</h3>
	<table>
	<tr>
		<th>Monat</th>    
		<th>Tage</th>    
		<th>Außentemperaturabhängige Belastung ßem1</th>
	</tr>
		<?php foreach ( $jahr->monate() as $monat ) : ?>
		<tr>
		<td><?php echo $monat->name(); ?></td>
		<td><?php echo $monat->tage(); ?></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->mittlere_belastung()->ßem1( $monat->slug() ) ); ?></td>
		</tr>
		<?php endforeach; ?>
	<tr>
		<td><strong>ßemMax</strong></td>
		<td></td>
		<td><?php echo \WPENON\Util\Format::float( $gebaeude->mittlere_belastung()->ßemMax() ); ?></td>
		</tr>
	</table>

	<h3>Luftwechsel</h3>

	<p><?php printf( __( 'Maximale Heizlast h<sub>max</sub>: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->luftwechsel()->h_max() ) ); ?></p>
	<p><?php printf( __( 'Maximale Heizlast spezifisch h<sub>max,spez</sub>: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->luftwechsel()->h_max_spezifisch() ) ); ?></p>
	<p><?php printf( __( 'Luftwechselrate n: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->luftwechsel()->n() ) ); ?></p>
	<p><?php printf( __( 'Luftechselvolumen h<sub>v</sub>: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->luftwechsel()->hv() ) ); ?></p>
	<p><?php printf( __( 'Gesamtluftwechselrate n<sub>0</sub>: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->luftwechsel()->n0() ) ); ?></p>
	<p><?php printf( __( 'Korrekturfakror f<sub>win,1</sub>: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->luftwechsel()->fwin1() ) ); ?></p>
	<p><?php printf( __( 'Korrekturfakror f<sub>win,2</sub>: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->luftwechsel()->fwin2() ) ); ?></p>

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
		<td><?php echo \WPENON\Util\Format::float( $uebergabesystem->ehce() ); ?></td>
	<?php endforeach; ?>	
	</table>

	<h3>Heizsystem</h3>

	<p><?php printf( __( 'Nutzbare Wärme fa<sub>h</sub>: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->heizsystem()->fa_h() ) ); ?></p>
	<p><?php printf( __( 'Aufwandszahl für freie Heizflächen ehce: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->heizsystem()->ehce() ) ); ?></p>	
	<p><?php printf( __( 'Mittlere Belastung bei Übergabe der Heizung ßhce: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->heizsystem()->ßhce() ) ); ?></p>
	<p><?php printf( __( 'Flächenbezogene leistung der Übergabe der Heizung qhce: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->heizsystem()->qhce() ) ); ?></p>
	<p><?php printf( __( 'ßhd: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->heizsystem()->ßhd() ) ); ?></p>
	<p><?php printf( __( 'fßd: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->heizsystem()->fßd() ) ); ?></p>
	<p><?php printf( __( 'ehd0: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->heizsystem()->ehd0() ) ); ?></p>
	<p><?php printf( __( 'ehd1: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->heizsystem()->ehd1() ) ); ?></p>
	<p><?php printf( __( 'ehd: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->heizsystem()->ehd() ) ); ?></p>
	<p><?php printf( __( 'ehd korrektur: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->heizsystem()->ehd_korrektur() ) ); ?></p>


	<h3>Wasserversorgung</h3>

	<p><?php printf( __( 'Nutzwärmebedarf für Trinkwasser Q<sub>w,b</sub>: %s kWh', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->wasserversorgung()->QWB() ) ); ?></p>
	<p><?php printf( __( 'Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen fh<sub>w</sub>: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->wasserversorgung()->fh_w() ) ); ?></p>
	<p><?php printf( __( 'Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen fh<sub>w</sub>: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->wasserversorgung()->fh_w() ) ); ?></p>
	<p><?php printf( __( 'Interne Wärmequelle infolge von Warmwasser Qi<sub>w</sub>: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->qi_wasser() ) ); ?></p>
	<p><?php printf( __( 'Jährlicher Nutzwaermebedarf für Trinkwasser (qwb): %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->wasserversorgung()->nutzwaermebedarf_trinkwasser() ) ); ?></p>
	<p><?php printf( __( 'Berechnung des monatlichen Wärmebedarfs für Warmwasser(QWB) für ein Jahr: %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->wasserversorgung()->QWB() ) ); ?></p>

	<h3>Pufferspeicher</h3>
	<p><?php printf( __( 'Zentrale Wassererwärmung über Erzeuger? %s', 'wpenon' ),  $gebaeude->wasserversorgung()->zentral() ? 'Ja' : 'Nein' ); ?>.</p>
	<p><?php printf( __( 'Nutzfläche: %s m<sup>2</sup>', 'wpenon' ),  \WPENON\Util\Format::float( $gebaeude->nutzflaeche() ) ); ?></p>
	<p><?php printf( __( 'Nutzfläche pro Wohneinheit: %s m<sup>2</sup>', 'wpenon' ),  \WPENON\Util\Format::float( $gebaeude->nutzflaeche_pro_wohneinheit() ) ); ?></p>
	<p><?php printf( __( 'Nutzwärmebedarf für Trinkwasser (qwb): %s kWh/(ma)', 'wpenon' ),  \WPENON\Util\Format::float( $gebaeude->wasserversorgung()->nutzwaermebedarf_trinkwasser() ) ); ?></p>
	<p><?php // printf( __( 'Nennleistung Pufferspeicher (pwn): %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->wasserversorgung()->pwn() ) ); ?></p>
	<p><?php // printf( __( 'Nennleistung Pufferspeicher (pwn): %s', 'wpenon' ), \WPENON\Util\Format::float( $gebaeude->wasserversorgung()->pwn() ) ); ?></p>


	<h3>Solar</h3>
	<?php echo \WPENON\Util\Format::float( $gebaeude->bauteile()->fenster()->qs() ); ?>	


	
</div>

