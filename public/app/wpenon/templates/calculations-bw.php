<?php

/**
 * @package WPENON
 * @version 1.0.0
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */

require_once __DIR__ . '/helfer.php';

$gebaeude = $data['gebaeude']; #

if (isset($data['referenzgebaeude'])) {
	$referenzgebaeude = $data['referenzgebaeude'];
}

$anlass = $data['anlass'];

$jahr = new Jahr();

?>

<style type="text/css">
	.referenzgebaeude {
		background-color: lightpink !important;
	}

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

	.calculation-details table th,
	td {
		padding: 5px 5px 5px 0;
	}
</style>

<div class="calculation-details">
	<h2>Gebäude</h2>
	<p><?php printf(__('Baujahr: %s;', 'wpenon'), $gebaeude->baujahr()); ?></p>
	<p><?php printf(__('Hüllvolumen V<sub>e</sub>: %s m&sup3;', 'wpenon'), str_replace('.', ',', $gebaeude->huellvolumen())); ?></p>
	<p><?php printf(__('Hüllvolumen (netto): %s m&sup3;', 'wpenon'), str_replace('.', ',', $gebaeude->huellvolumen_netto())); ?></p>
	<p><?php printf(__('Hüllfäche<sub>e</sub>: %s m&sup2;', 'wpenon'), str_replace('.', ',', $gebaeude->huellflaeche())); ?></p>
	<p><?php printf(__('ave Verhältnis: %s', 'wpenon'), str_replace('.', ',', $gebaeude->ave_verhaeltnis())); ?></p>
	<p><?php printf(__('Nutzfläche A<sub>N</sub>: %s m&sup2;', 'wpenon'), str_replace('.', ',', $gebaeude->nutzflaeche())); ?></p>
	<p><?php printf(__('Anzahl der Geschosse: %s', 'wpenon'), $gebaeude->geschossanzahl()); ?></p>
	<p><?php printf(__('Geschosshöhe: %s m', 'wpenon'), str_replace('.', ',', $gebaeude->geschosshoehe())); ?></p>
	<p><?php printf(__('Anzahl der Wohnungen: %s', 'wpenon'), $gebaeude->anzahl_wohnungen()); ?></p>
	<p><?php printf(__('Einfamilienhaus: %s', 'wpenon'), $gebaeude->ist_einfamilienhaus() ? 'Ja' : 'Nein'); ?></p>


	<h3>Grundriss</h3>
	<p>Ausrichtung des Gebäudes: <?php echo $gebaeude->grundriss()->ausrichtung(); ?></p>
	<table>
		<tr>
			<th>Seite</th>
			<th>Länge</th>
			<th>Ausrichtung</th>
		</tr>
		<?php foreach ($gebaeude->grundriss()->waende() as $wand) : ?>
			<tr>
				<td><?php echo $wand; ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->grundriss()->wand_laenge($wand)); ?> m</td>
				<td><?php echo $gebaeude->grundriss()->wand_himmelsrichtung($wand); ?></td>
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
			<th>Dämmung</th>
			<th>Fx Faktor</th>
			<th>Transmissionswärmekoeffizient ht</th>
		</tr>
		<?php foreach ($gebaeude->bauteile()->waende()->alle() as $wand) : ?>
			<tr>
				<td><?php echo $wand->name(); ?></td>
				<td><?php echo str_replace('.', ',', $wand->flaeche()); ?> m<sup>2</sup></td>
				<td><?php echo str_replace('.', ',', $wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
				<td><?php echo str_replace('.', ',', $wand->daemmung()); ?> cm</td>
				<td><?php echo str_replace('.', ',', $wand->fx()); ?></td>
				<td><?php echo str_replace('.', ',', $wand->ht()); ?> W/K</td>
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
		<?php foreach ($gebaeude->bauteile()->filter('Fenster')->alle() as $fenster) : ?>
			<tr>
				<td><?php echo $fenster->name(); ?></td>
				<td><?php echo str_replace('.', ',', $fenster->flaeche()); ?> m<sup>2</sup></td>
				<td><?php echo str_replace('.', ',', $fenster->gwert()); ?></td>
				<td><?php echo str_replace('.', ',', $fenster->uwert()); ?> W/(m<sup>2</sup>K)</td>
				<td><?php echo str_replace('.', ',', $fenster->fx()); ?></td>
				<td><?php echo str_replace('.', ',', $fenster->ht()); ?> W/K</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<h3>Heizköpernischen</h3>
	<?php if ($gebaeude->bauteile()->filter('Heizkoerpernische')->anzahl() > 0) : ?>
		<table>
			<tr>
				<th>Bauteil</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärmekoeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Heizkoerpernische')->alle() as $heizkoerpernische) : ?>
				<tr>
					<td><?php echo $heizkoerpernische->name(); ?></td>
					<td><?php echo str_replace('.', ',', $heizkoerpernische->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo str_replace('.', ',', $heizkoerpernische->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo str_replace('.', ',', $heizkoerpernische->fx()); ?></td>
					<td><?php echo str_replace('.', ',', $heizkoerpernische->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else : ?>
		<p class="lead"><?php _e('Keine Heizkörpernischen vorhanden.', 'wpenon'); ?></p>
	<?php endif; ?>

	<h3>Rolladenkästen</h3>
	<?php if ($gebaeude->bauteile()->filter('Rolladenkasten')->anzahl() > 0) : ?>
		<table>
			<tr>
				<th>Bauteil</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärmekoeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Rolladenkasten')->alle() as $rolladenkaesten) : ?>
				<tr>
					<td><?php echo $rolladenkaesten->name(); ?></td>
					<td><?php echo str_replace('.', ',', $rolladenkaesten->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo str_replace('.', ',', $rolladenkaesten->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo str_replace('.', ',', $rolladenkaesten->fx()); ?></td>
					<td><?php echo str_replace('.', ',', $rolladenkaesten->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else : ?>
		<p class="lead"><?php _e('Keine Rolladenkästen vorhanden.', 'wpenon'); ?></p>
	<?php endif; ?>

	<?php if ($gebaeude->dach_vorhanden()) : ?>
		<h3>Dach</h3>
		<table>
			<tr>
				<th>Bauteil</th>
				<th>Fläche</th>
				<th>Höhe</th>
				<th>U-Wert</th>
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärmekoeffizient ht</th>
			</tr>
			<tr>
				<td><?php echo $gebaeude->dach()->name(); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->dach()->flaeche()); ?> m<sup>2</sup></td>
				<td><?php echo str_replace('.', ',', $gebaeude->dach()->hoehe()); ?> m</td>
				<td><?php echo str_replace('.', ',', $gebaeude->dach()->uwert()); ?> W/(m<sup>2</sup>K)</td>
				<td><?php echo str_replace('.', ',', $gebaeude->dach()->daemmung()); ?> cm</td>
				<td><?php echo str_replace('.', ',', $gebaeude->dach()->fx()); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->dach()->ht()); ?> W/K</td>
			</tr>
		</table>
	<?php else : ?>
		<h3>Decke</h3>
		<table>
			<tr>
				<th>Bauteil</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärmekoeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Decke')->alle() as $decke) : ?>
				<tr>
					<td><?php echo $decke->name(); ?></td>
					<td><?php echo str_replace('.', ',', $decke->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo str_replace('.', ',', $decke->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo str_replace('.', ',', $decke->daemmung()); ?> cm</td>
					<td><?php echo str_replace('.', ',', $decke->fx()); ?></td>
					<td><?php echo str_replace('.', ',', $decke->ht()); ?> W/K</td>
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
			<th>Dämmung</th>
			<th>Fx Faktor</th>
			<th>Transmissionswärmekoeffizient ht</th>
		</tr>
		<?php foreach ($gebaeude->bauteile()->filter('Boden')->alle() as $boden) : ?>
			<tr>
				<td><?php echo $boden->name(); ?></td>
				<td><?php echo str_replace('.', ',', $boden->flaeche()); ?> m<sup>2</sup></td>
				<td><?php echo str_replace('.', ',', $boden->uwert()); ?> W/(m<sup>2</sup>K)</td>
				<td><?php echo str_replace('.', ',', $boden->daemmung()); ?> cm</td>
				<td><?php echo str_replace('.', ',', $boden->fx()); ?></td>
				<td><?php echo str_replace('.', ',', $boden->ht()); ?> W/K</td>
			</tr>
		<?php endforeach; ?>
	</table>


	<?php if ($gebaeude->keller_vorhanden()) : ?>
		<h3>Keller</h3>
		<p class="lead"><?php printf(__('Unterkellerung: %s;', 'wpenon'), str_replace('.', ',', $gebaeude->keller()->anteil())); ?></p>
		<p class="lead"><?php printf(__('Kellerfläche A<sub>K</sub>: %s m&sup2;', 'wpenon'), str_replace('.', ',', $gebaeude->keller()->boden_flaeche())); ?></p>
		<p class="lead"><?php printf(__('Kellerwandlänge U<sub>K</sub>: %s m;', 'wpenon'), str_replace('.', ',', $gebaeude->keller()->wand_laenge())); ?></p>
		<p class="lead"><?php printf(__('Kellerwandhöhe H<sub>K</sub>: %s m;', 'wpenon'), str_replace('.', ',', $gebaeude->keller()->wand_hoehe())); ?></p>
		<p class="lead"><?php printf(__('Kellervolumen V<sub>K</sub>: %s m&sup3;', 'wpenon'), str_replace('.', ',', $gebaeude->keller()->volumen())); ?></p>
		<table>
			<tr>
				<th>Wand</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärmekoeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Kellerwand')->alle() as $wand) : ?>
				<tr>
					<td><?php echo $wand->name(); ?></td>
					<td><?php echo str_replace('.', ',', $wand->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo str_replace('.', ',', $wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo str_replace('.', ',', $wand->daemmung()); ?> cm</td>
					<td><?php echo str_replace('.', ',', $wand->fx()); ?></td>
					<td><?php echo str_replace('.', ',', $wand->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
			<?php foreach ($gebaeude->bauteile()->filter('Kellerboden')->alle() as $wand) : ?>
				<tr>
					<td><?php echo $wand->name(); ?></td>
					<td><?php echo str_replace('.', ',', $wand->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo str_replace('.', ',', $wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo str_replace('.', ',', $wand->daemmung()); ?> cm</td>
					<td><?php echo str_replace('.', ',', $wand->fx()); ?></td>
					<td><?php echo str_replace('.', ',', $wand->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>

	<?php if ($gebaeude->anbau_vorhanden()) : ?>
		<h3>Anbau</h3>
		<p class="lead"><?php printf(__('Anbau Fläche: %s m&sup2; ', 'wpenon'), str_replace('.', ',', $gebaeude->anbau()->grundriss()->flaeche())); ?></p>
		<p class="lead"><?php printf(__('Anbau Volumen: %s m&sup2; ', 'wpenon'), str_replace('.', ',', $gebaeude->anbau()->volumen())); ?></p>
		<table>
			<tr>
				<th>Wand</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärmekoeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Anbauwand')->alle() as $wand) : ?>
				<tr>
					<td><?php echo $wand->name(); ?></td>
					<td><?php echo str_replace('.', ',', $wand->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo str_replace('.', ',', $wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo str_replace('.', ',', $wand->daemmung()); ?> cm</td>
					<td><?php echo str_replace('.', ',', $wand->fx()); ?></td>
					<td><?php echo str_replace('.', ',', $wand->ht()); ?> W/K</td>
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
			<?php foreach ($gebaeude->bauteile()->filter('Anbaufenster')->alle() as $anbaufenster) : ?>
				<tr>
					<td><?php echo $anbaufenster->name(); ?></td>
					<td><?php echo str_replace('.', ',', $anbaufenster->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo str_replace('.', ',', $anbaufenster->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo str_replace('.', ',', $anbaufenster->fx()); ?></td>
					<td><?php echo str_replace('.', ',', $anbaufenster->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>

		<h3>Anbauboden</h3>
		<table>
			<tr>
				<th>Bauteil</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärmekoeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Anbauboden')->alle() as $boeden) : ?>
				<tr>
					<td><?php echo $boeden->name(); ?></td>
					<td><?php echo str_replace('.', ',', $boeden->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo str_replace('.', ',', $boeden->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo str_replace('.', ',', $boeden->daemmung()); ?> cm</td>
					<td><?php echo str_replace('.', ',', $boeden->fx()); ?></td>
					<td><?php echo str_replace('.', ',', $boeden->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>

		<h3>Anbaudecke</h3>
		<table>
			<tr>
				<th>Bauteil</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärmekoeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Anbaudecke')->alle() as $decke) : ?>
				<tr>
					<td><?php echo $boeden->name(); ?></td>
					<td><?php echo str_replace('.', ',', $decke->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo str_replace('.', ',', $decke->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo str_replace('.', ',', $decke->daemmung()); ?> cm</td>
					<td><?php echo str_replace('.', ',', $decke->fx()); ?></td>
					<td><?php echo str_replace('.', ',', $decke->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>

	<?php endif; ?>

	<h3>Transmission</h3>


	<p><?php printf(__('Transmissionswärmekoeffizient Bauteile ht: %s', 'wpenon'), str_replace('.', ',', $gebaeude->bauteile()->ht())); ?></p>
	<p><?php printf(__('Transmissionswärmekoeffizient Fenster hw: %s', 'wpenon'), str_replace('.', ',', $gebaeude->bauteile()->hw())); ?></p>
	<p><?php printf(__('Wärmebrückenzuschlag (ht_wb): %s', 'wpenon'), str_replace('.', ',', $gebaeude->ht_wb())); ?></p>
	<p><?php printf(__('Transmissionswärmekoeffizient Gesamt ht<sub>ges</sub>: %s', 'wpenon'), str_replace('.', ',', $gebaeude->ht_ges())); ?></p>
	<p><?php printf(__('Wärmetransferkoeffizient des Gebäudes. (h ges): %s', 'wpenon'), str_replace('.', ',', $gebaeude->h_ges())); ?></p>
	<p><?php printf(__('Tau: %s', 'wpenon'), str_replace('.', ',', $gebaeude->tau())); ?></p>
	<p><?php printf(__('Maximaler Wärmestrom Q: %s', 'wpenon'), str_replace('.', ',', $gebaeude->q())); ?></p>

	<h3>Lüftung</h3>

	<p><?php printf(__('Lueftungssystem: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->lueftungssystem())); ?></p>
	<p><?php printf(__('Bedarfsgeführt: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->ist_bedarfsgefuehrt() ? 'Ja' : 'Nein')); ?></p>
	<p><?php printf(__('Gebäudedichtheit: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->gebaeudedichtheit())); ?></p>
	<p><?php printf(__('Wirkungsgrad: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->wirkungsgrad())); ?></p>
	<p><?php printf(__('Luftechselvolumen h<sub>v</sub>: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->hv())); ?></p>
	<p><?php printf(__('Maximale Heizlast h<sub>max</sub>: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->h_max())); ?></p>
	<p><?php printf(__('Maximale Heizlast spezifisch h<sub>max,spez</sub>: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->h_max_spezifisch())); ?></p>

	<h4>Luftwechsel Werte</h4>
	<p><?php printf(__('Luftwechselrate n: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->luftwechsel()->n())); ?></p>
	<p><?php printf(__('Gesamtluftwechselrate n<sub>0</sub>: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->luftwechsel()->n0())); ?></p>
	<p><?php printf(__('Korrekturfakror f<sub>win,1</sub>: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->luftwechsel()->fwin1())); ?></p>
	<p><?php printf(__('Korrekturfakror f<sub>win,2</sub>: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->luftwechsel()->fwin2())); ?></p>
	<p><?php printf(__('n_anl: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->luftwechsel()->n_anl())); ?></p>
	<p><?php printf(__('n_wrg: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->luftwechsel()->n_wrg())); ?></p>

	<hr />

	<h2>Bilanzierung</h2>

	<h3>Interne Wärmequellen</h3>
	<table>
		<tr>
			<th>Monat</th>
			<th>Qi<sub>p</sub> (kWh)</th>
			<th>Qi<sub>w</sub> (kWh)</th>
			<th>Qi<sub>s</sub> (kWh)</th>
			<th>Qi<sub>h</sub> (kWh)</th>
			<th>Qi<sub>ges</sub> (kWh)</th>
		</tr>
		<?php foreach ($jahr->monate() as $monat) : ?>
			<tr>
				<td><?php echo $monat->name(); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->qi_prozesse_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->qi_wasser_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->qi_solar_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->qi_heizung_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->qi_monat($monat->slug())); ?></td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td><b>Gesamt</b></td>
			<td><?php echo str_replace('.', ',', $gebaeude->qi_prozesse()); ?></td>
			<td><?php echo str_replace('.', ',', $gebaeude->qi_wasser()); ?></td>
			<td><?php echo str_replace('.', ',', $gebaeude->qi_solar()); ?></td>
			<td><?php echo str_replace('.', ',', $gebaeude->qi_heizung()); ?></td>
			<td><?php echo str_replace('.', ',', $gebaeude->qi()); ?></td>
		</tr>
	</table>

	<h3>fum</h3>
	<table>
		<tr>
			<th>Monat</th>
			<th>fum</th>
		</tr>
		<?php foreach ($jahr->monate() as $monat) : ?>
			<tr>
				<td><?php echo $monat->name(); ?></td>
				<td><?php echo str_replace('.', ',', fum($monat->slug())); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>

	<table>
		<tr>
			<th>Monat</th>
			<th>P*H<sub>sink</sub> (W)</th>
			<th>PH<sub>sink</sub> (W)</th>
			<th>PH<sub>source</sub> (W)</th>
			<th>Q<sub>w,b</sub> (kWh)</th>
			<th>Q<sub>h,b</sub> (kWh)</th>
		</tr>
		<?php foreach ($jahr->monate() as $monat) : ?>
			<tr>
				<td><?php echo $monat->name(); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->psh_sink_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->ph_sink_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->ph_source_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->QWB_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->qh_monat($monat->slug())); ?></td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td><b>Gesamt</b></td>
			<td></td>
			<td></td>
			<td></td>
			<td><?php echo str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->QWB()); ?></td>
			<td><?php echo str_replace('.', ',', $gebaeude->qh()); ?></td>
		</tr>
	</table>

	<h3>Jahr Gesamt</h3>
	<table>
		<tr>
			<th>ßhma</th>
			<th>thm</th>
			<th>ith_rl</th>
			<th>Qi<sub>ges</sub> (kWh)</th>
			<th>Q<sub>w,b</sub> (kWh)</th>
			<th>Q<sub>h,b</sub> (kWh)</th>
		</tr>
		<tr>
			<td><?php echo str_replace('.', ',', $gebaeude->ßhma()); ?></td>
			<td><?php echo str_replace('.', ',', $gebaeude->thm()); ?></td>
			<td><?php echo str_replace('.', ',', $gebaeude->ith_rl()); ?></td>
			<td><?php echo str_replace('.', ',', $gebaeude->qi()); ?></td>
			<td><?php echo str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->QWB()); ?></td>
			<td><?php echo str_replace('.', ',', $gebaeude->qh()); ?></td>
		</tr>
	</table>

	<h3>Korrekturfaktoren und wetere Werte</h3>

	<table>
		<tr>
			<th>Monat</th>
			<th>P<sub>h+w+str+p,source</sub></th>
			<th>ym</th>
			<th>nm</th>
			<th>flna</th>
			<th>trl</th>
			<th>ith_rl</th>
		</tr>
		<?php foreach ($jahr->monate() as $monat) : ?>
			<tr>
				<td><?php echo $monat->name(); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->ph_source_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->ym_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->nm_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->flna_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->trl_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->ith_rl_monat($monat->slug())); ?></td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td><b>Gesamt</b></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td><?php echo str_replace('.', ',', $gebaeude->ith_rl()); ?></td>
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
		<?php foreach ($jahr->monate() as $monat) : ?>
			<tr>
				<td><?php echo $monat->name(); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->k_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->bilanz_innentemperatur()->θih_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->mittlere_belastung()->ßem1($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->ßhm_monat($monat->slug())); ?></td>
				<td><?php echo str_replace('.', ',', $gebaeude->thm_monat($monat->slug())); ?></td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td><b>Gesamt</b></td>
			<td></td>
			<td></td>
			<td><?php echo $gebaeude->mittlere_belastung()->ßemMax(); ?> (ßemMax)</td>
			<td><?php echo str_replace('.', ',', $gebaeude->ßhma()); ?> (ßhma)</td>
			<td><?php echo str_replace('.', ',', $gebaeude->thm()); ?></td>
		</tr>
	</table>

	<hr />

	<h2>Heizsystem</h2>

	<h3>Heizungsanlage</h3>
	<?php $i = 1; ?>
	<?php foreach ($gebaeude->heizsystem()->heizungsanlagen()->alle() as $heizungsanlage) : ?>
		<h4><?php echo 'Heizungsanlage ' . $i++; ?></h4>
		<?php if ($heizungsanlage->kategorie() === 'konventioneller_kessel') : ?>
			<table>
				<tr>
					<th>Kategorie</th>
					<td><?php echo $heizungsanlage->kategorie(); ?></td>
				</tr>
				<tr>
					<th>Heizungstyp</th>
					<td><?php echo $heizungsanlage->typ(); ?></td>
				</tr>
				<tr>
					<th>Energieträger</th>
					<td><?php echo $heizungsanlage->energietraeger(); ?></td>
				</tr>
				<tr>
					<th>Anteil</th>
					<td><?php echo $heizungsanlage->prozentualer_anteil(); ?></td>
				</tr>
				<tr>
					<th>eg0</th>
					<td><?php echo $heizungsanlage->eg0(); ?></td>
				</tr>
				<tr>
					<th>fbj</th>
					<td><?php echo $heizungsanlage->fbj(); ?></td>
				</tr>
				<tr>
					<th>ßhg</th>
					<td><?php echo $heizungsanlage->ßhg(); ?></td>
				</tr>
				<tr>
					<th>fegt</th>
					<td><?php echo $heizungsanlage->fegt(); ?></td>
				</tr>
				<tr>
					<th>ehg</th>
					<td><?php echo $heizungsanlage->ehg(); ?></td>
				</tr>
				<tr>
					<th>ewg0</th>
					<td><?php echo $heizungsanlage->ewg0(); ?></td>
				</tr>
				<tr>
					<th>ewg</th>
					<td><?php echo $heizungsanlage->ewg(); ?></td>
				</tr>
				<tr>
					<th>fco2 (CO2 Emissiomnsfaktor)</th>
					<td><?php echo $heizungsanlage->fco2(); ?></td>
				</tr>
				<tr>
					<th>fp (Primärenergiefaktor)</th>
					<td><?php echo $heizungsanlage->fp(); ?></td>
				</tr>
			</table>
			<h5>Hilfsenergie</h5>
			<table>
				<tr>
					<th>tpwn0</th>
					<td><?php echo $heizungsanlage->twpn0(); ?></td>
				</tr>
				<tr>
					<th>tpwn</th>
					<td><?php echo $heizungsanlage->twpn(); ?></td>
				</tr>
				<tr>
					<th>fphgaux</th>
					<td><?php echo $heizungsanlage->fphgaux(); ?></td>
				</tr>
				<tr>
					<th>Phgaux</th>
					<td><?php echo $heizungsanlage->Phgaux(); ?></td>
				</tr>
				<tr>
					<th>fpwgaux</th>
					<td><?php echo $heizungsanlage->fpwgaux(); ?></td>
				</tr>
				<tr>
					<th>Pwgaux</th>
					<td><?php echo $heizungsanlage->Pwgaux(); ?></td>
				</tr>
				<tr>
					<th>Whg</th>
					<td><?php echo $heizungsanlage->Whg(); ?></td>
				</tr>
				<tr>
					<th>Wwg</th>
					<td><?php echo $heizungsanlage->Wwg(); ?></td>
				</tr>
			</table>

			<h5>Weitere Werte</h5>
			<table>
				<tr>
					<th>MCO2</th>
					<td><?php echo $heizungsanlage->MCO2(); ?></td>
				</tr>
			</table>

		<?php elseif ($heizungsanlage->kategorie() === 'waermepumpe') : ?>
			<table>
				<tr>
					<td>Kategorie</td>
					<td><?php echo $heizungsanlage->kategorie(); ?></td>
				</tr>
				<tr>
					<td>Heizungstyp</td>
					<td><?php echo $heizungsanlage->typ(); ?></td>
				</tr>
				<tr>
					<td>Energieträger</td>
					<td><?php echo $heizungsanlage->energietraeger(); ?></td>
				</tr>
				<tr>
					<td>Anteil</td>
					<td><?php echo $heizungsanlage->prozentualer_anteil(); ?></td>
				</tr>
				<tr>
					<td>θva</td>
					<td><?php echo $heizungsanlage->θva(); ?></td>
				</tr>
				<tr>
					<td>θvl</td>
					<td><?php echo $heizungsanlage->θvl(); ?></td>
				</tr>
				<tr>
					<td>COPtk -7</td>
					<td><?php echo $heizungsanlage->COPtk_7(); ?></td>
				</tr>
				<tr>
					<td>COPtk 2</td>
					<td><?php echo $heizungsanlage->COPtk2(); ?></td>
				</tr>
				<tr>
					<td>COPtk 7</td>
					<td><?php echo $heizungsanlage->COPtk7(); ?></td>
				</tr>
				<tr>
					<td>W -7</td>
					<td><?php echo $heizungsanlage->W_7(); ?></td>
				</tr>
				<tr>
					<td>W 2</td>
					<td><?php echo $heizungsanlage->W2(); ?></td>
				</tr>
				<tr>
					<td>W 7</td>
					<td><?php echo $heizungsanlage->W7(); ?></td>
				</tr>
				<tr>
					<td>e gsamt (ehg)</td>
					<td><?php echo $heizungsanlage->eh_ges(); ?></td>
				</tr>
				<tr>
					<td>Qfhges</td>
					<td><?php echo $heizungsanlage->Qfhges(); ?></td>
				</tr>
				<tr>
					<td>ewg</td>
					<td><?php echo $heizungsanlage->ewg(); ?></td>
				</tr>
				<tr>
					<th>fco2 (CO2 Emissiomnsfaktor)</th>
					<td><?php echo $heizungsanlage->fco2(); ?></td>
				</tr>
				<tr>
					<th>fp (Primärenergiefaktor)</th>
					<td><?php echo $heizungsanlage->fp(); ?></td>
				</tr>
			</table>

			<h5>Hilfsenergie</h5>

			<table>
				<tr>
					<th>Whg</th>
					<td><?php echo $heizungsanlage->Whg(); ?></td>
				</tr>
				<tr>
					<th>Wwg</th>
					<td><?php echo $heizungsanlage->Wwg(); ?></td>
				</tr>
			</table>

			<h5>Weitere Werte</h5>
			<table>
				<tr>
					<th>MCO2</th>
					<td><?php echo $heizungsanlage->MCO2(); ?></td>
				</tr>
			</table>

		<?php elseif ($heizungsanlage->kategorie() === 'fernwaerme') : ?>
			<table>
				<tr>
					<td>Kategorie</td>
					<td><?php echo $heizungsanlage->kategorie(); ?></td>
				</tr>
				<tr>
					<td>Heizungstyp</td>
					<td><?php echo $heizungsanlage->typ(); ?></td>
				</tr>
				<tr>
					<td>Energieträger</td>
					<td><?php echo $heizungsanlage->energietraeger(); ?></td>
				</tr>
				<tr>
					<td>Anteil</td>
					<td><?php echo $heizungsanlage->prozentualer_anteil(); ?></td>
				</tr>
				<tr>
					<td>eg0</td>
					<td><?php echo $heizungsanlage->eg0(); ?></td>
				</tr>
				<tr>
					<td>ßhg</td>
					<td><?php echo $heizungsanlage->ßhg(); ?></td>
				</tr>
				<tr>
					<td>fiso</td>
					<td><?php echo $heizungsanlage->fiso(); ?></td>
				</tr>
				<tr>
					<td>ftemp</td>
					<td><?php echo $heizungsanlage->ftemp(); ?></td>
				</tr>
				<tr>
					<td>ehg</td>
					<td><?php echo $heizungsanlage->ehg(); ?></td>
				</tr>
				<tr>
					<td>ewg</td>
					<td><?php echo $heizungsanlage->ewg(); ?></td>
				</tr>
				<tr>
					<th>fco2 (CO2 Emissiomnsfaktor)</th>
					<td><?php echo $heizungsanlage->fco2(); ?></td>
				</tr>
				<tr>
					<th>fp (Primärenergiefaktor)</th>
					<td><?php echo $heizungsanlage->fp(); ?></td>
				</tr>
			</table>

			<h5>Hilfsenergie</h5>

			<table>
				<tr>
					<th>Whg</th>
					<td><?php echo $heizungsanlage->Whg(); ?></td>
				</tr>
				<tr>
					<th>Wwg</th>
					<td><?php echo $heizungsanlage->Wwg(); ?></td>
				</tr>
			</table>

			<h5>Weitere Werte</h5>
			<table>
				<tr>
					<th>MCO2</th>
					<td><?php echo $heizungsanlage->MCO2(); ?></td>
				</tr>
			</table>

		<?php elseif ($heizungsanlage->kategorie() === 'dezentral') : ?>
			<table>
				<tr>
					<td>Kategorie</td>
					<td><?php echo $heizungsanlage->kategorie(); ?></td>
				</tr>
				<tr>
					<td>Heizungstyp</td>
					<td><?php echo $heizungsanlage->typ(); ?></td>
				</tr>
				<tr>
					<td>Energieträger</td>
					<td><?php echo $heizungsanlage->energietraeger(); ?></td>
				</tr>
				<tr>
					<td>Anteil</td>
					<td><?php echo $heizungsanlage->prozentualer_anteil(); ?></td>
				</tr>
				<tr>
					<td>ehg</td>
					<td><?php echo $heizungsanlage->ehg(); ?></td>
				</tr>
				<tr>
					<td>ewg</td>
					<td><?php echo $heizungsanlage->ewg(); ?></td>
				</tr>
				<tr>
					<th>fco2 (CO2 Emissiomnsfaktor)</th>
					<td><?php echo $heizungsanlage->fco2(); ?></td>
				</tr>
				<tr>
					<th>fp (Primärenergiefaktor)</th>
					<td><?php echo $heizungsanlage->fp(); ?></td>
				</tr>
			</table>

			<h5>Hilfsenergie</h5>

			<table>
				<tr>
					<th>Whg</th>
					<td><?php echo $heizungsanlage->Whg(); ?></td>
				</tr>
				<tr>
					<th>Wwg</th>
					<td><?php echo $heizungsanlage->Wwg(); ?></td>
				</tr>
			</table>

			<h5>Weitere Werte</h5>
			<table>
				<tr>
					<th>MCO2</th>
					<td><?php echo $heizungsanlage->MCO2(); ?></td>
				</tr>
			</table>
		<?php endif; ?>

	<?php endforeach; ?>

	<h3>Übergabesystem</h3>
	<table>
		<tr>
			<th>Übergabetyp</th>
			<th>Auslegungstemperatur</th>
			<th>Anteil</th>
			<th>ehce</th>
		</tr>
		<?php foreach ($gebaeude->heizsystem()->uebergabesysteme()->alle() as $uebergabesystem) : ?>
			<tr>
				<td><?php echo $uebergabesystem->typ(); ?></td>
				<td><?php echo $uebergabesystem->auslegungstemperaturen(); ?></td>
				<td><?php echo $uebergabesystem->prozentualer_anteil(); ?></td>
				<td><?php echo str_replace('.', ',', $uebergabesystem->ehce()); ?></td>
			<?php endforeach; ?>
	</table>

	<h3>Heizsystem</h3>

	<p><?php printf(__('Nutzbare Wärme fa<sub>h</sub>: %s', 'wpenon'), $gebaeude->heizsystem()->fa_h()); ?></p>
	<p><?php printf(__('Mittlere Belastung bei Übergabe der Heizung (ßhce): %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->ßhce())); ?></p>
	<p><?php printf(__('Flächenbezogene leistung der Übergabe der Heizung (qhce): %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->qhce())); ?></p>
	<p><?php printf(__('ßhd: %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->ßhd())); ?></p>
	<p><?php printf(__('fßd: %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->fßd())); ?></p>
	<p><?php printf(__('ehd0: %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->ehd0())); ?></p>
	<p><?php printf(__('ehd1: %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->ehd1())); ?></p>
	<p><?php printf(__('ehd: %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->ehd())); ?></p>
	<p><?php printf(__('ehd korrektur: %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->ehd_korrektur())); ?></p>
	<p><?php printf(__('ßhs: %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->ßhs())); ?></p>

	<p><?php printf(__('Nennleistung Pufferspeicher (pwn): %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->pwn())); ?></p>
	<p><?php printf(__('(pn): %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->pn())); ?></p>



	<?php if ($gebaeude->heizsystem()->pufferspeicher_vorhanden()) : ?>
		<h3>Pufferspeicher</h3>
		<p><?php printf(__('Korrekturfaktor mittlere Belastung des Pufferspeichers fßhs: %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->pufferspeicher()->fßhs())); ?></p>
		<p><?php printf(__('Mittlere Belastung für Speicherung ßhs: %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->ßhs())); ?></p>
		<p><?php printf(__('Korrekturfaktor für beliebige mittlere Berlastung und Laufzeit der Heizung fhs: %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->pufferspeicher()->fhs())); ?></p>
		<p><?php printf(__('Berechnetes Volumen: %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->pufferspeicher()->volumen())); ?></p>
		<p><?php printf(__('Volumen Pufferspeicher vs1: %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->pufferspeicher()->vs1())); ?></p>
		<p><?php printf(__('Volumen Pufferspeicher vs2: %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->pufferspeicher()->vs2())); ?></p>
		<p><?php printf(__('Wärmeabgabe Pufferspeicher (Qhs0Vs1): %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->pufferspeicher()->Qhs0Vs1())); ?></p>
		<p><?php printf(__('Wärmeabgabe Pufferspeicher (Qhs0Vs2): %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->pufferspeicher()->Qhs0Vs2())); ?></p>
		<p><?php printf(__('Wärmeabgabe Pufferspeicher Gesamt (Qhs): %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->pufferspeicher()->Qhs())); ?></p>
		<p><?php printf(__('Aufwandszahl für Pufferspeicher (ehs): %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->ehs())); ?></p>
	<?php else : ?>
		<p><?php printf(__('Aufwandszahl für Pufferspeicher (ehs):  %s', 'wpenon'), str_replace('.', ',', $gebaeude->heizsystem()->ehs())); ?></p>
	<?php endif; ?>

	<h3>Trinkwarmwasseranlage</h3>

	<p><?php printf(__('Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen Faw: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Faw())); ?></p>
	<p><?php printf(__('Nutzwärmebedarf für Trinkwasser qwb: %s kWh/(ma)', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->nutzwaermebedarf_trinkwasser())); ?></p>
	<p><?php printf(__('Q<sub>w,b</sub>: %s kWh', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->QWB())); ?></p>
	<p><?php printf(__('Interne Wärmequelle infolge von Warmwasser Qi<sub>w</sub>: %s', 'wpenon'), str_replace('.', ',', $gebaeude->qi_wasser())); ?></p>
	<p><?php printf(__('Jährlicher Nutzwaermebedarf für Trinkwasser (qwb): %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->nutzwaermebedarf_trinkwasser())); ?></p>
	<p><?php printf(__('Berechnung des monatlichen Wärmebedarfs für Warmwasser(QWB) für ein Jahr: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->QWB())); ?></p>

	<h4>Aufwandszahlen Trinkwarmwasser</h4>

	<p><?php printf(__('Zwischenwert für die Berechnung von ewd (ewce): %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->ewce())); ?></p>
	<p><?php printf(__('Zwischenwert für die Berechnung von ewd (ewd0): %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->ewd0())); ?></p>
	<p><?php printf(__('Aufwandszahlen für die Verteilung von Trinkwarmwasser (ewd): %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->ewd())); ?></p>
	<p><?php printf(__('Korrekturfaktor (fwb): %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->fwb())); ?></p>
	<p><?php printf(__('Volumen Speicher 1 in Litern. (Vs01): %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Vs01())); ?></p>
	<p><?php printf(__('Volumen Speicher 2 in Litern. (Vs02): %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Vs02())); ?></p>
	<p><?php printf(__('Volumen Speicher 3 in Litern. (Vs03): %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Vs03())); ?></p>
	<p><?php printf(__('Volumen Speicher Gesamt in Litern. (Vs0): %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Vs0())); ?></p>
	<p><?php printf(__('Berechnung von Vsw1: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Vsw1())); ?></p>
	<p><?php printf(__('Berechnung von Vsw2: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Vsw2())); ?></p>
	<p><?php printf(__('Berechnung von Qws01: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Qws01())); ?></p>
	<p><?php printf(__('Berechnung von Qws02: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Qws02())); ?></p>

	<?php if ($gebaeude->trinkwarmwasseranlage()->solarthermie_vorhanden()) : ?>
		<h4>Solarthermie</h4>
		<p><?php printf(__('Berechnung von Vsaux0: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Vsaux0())); ?></p>
		<p><?php printf(__('Berechnung von Vssol0: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Vssol0())); ?></p>
		<p><?php printf(__('Berechnung von Ac0: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Ac0())); ?></p>
		<p><?php printf(__('Berechnung von Qwsola0: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Qwsola0())); ?></p>
		<br />
		<p><?php printf(__('Berechnung von Vsaux: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Vsaux())); ?></p>
		<p><?php printf(__('Berechnung von Vssol: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Vssol())); ?></p>
		<p><?php printf(__('Berechnung von Ac: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Ac())); ?></p>
		<p><?php printf(__('Berechnung von fAc: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->fAc())); ?></p>
		<p><?php printf(__('Berechnung von fQsola: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->fQsola())); ?></p>
		<p><?php printf(__('Berechnung von Qwsola: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Qwsola())); ?></p>
	<?php endif; ?>

	<br>

	<p><?php printf(__('Berechnung von Qws: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->Qws())); ?></p>
	<p><?php printf(__('Berechnung von ews: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->ews())); ?></p>
	<p><?php printf(__('Berechnung von keew: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->keew())); ?></p>
	<p><?php printf(__('Berechnung von keeh: %s', 'wpenon'), str_replace('.', ',', $gebaeude->trinkwarmwasseranlage()->keeh())); ?></p>

	<?php if ($gebaeude->photovoltaik_anlage_vorhanden()) : ?>
		<h3>Photovoltaik</h3>
		<p><?php printf(__('Richtung: %s', 'wpenon'), str_replace('.', ',', $gebaeude->photovoltaik_anlage()->richtung())); ?></p>
		<p><?php printf(__('Neigung: %s', 'wpenon'), str_replace('.', ',', $gebaeude->photovoltaik_anlage()->neigung())); ?></p>
		<p><?php printf(__('Fläche: %s', 'wpenon'), str_replace('.', ',', $gebaeude->photovoltaik_anlage()->flaeche())); ?></p>
		<p><?php printf(__('Baujahr: %s', 'wpenon'), str_replace('.', ',', $gebaeude->photovoltaik_anlage()->baujahr())); ?></p>
		<p><?php printf(__('QfprodPV: %s', 'wpenon'), str_replace('.', ',', $gebaeude->photovoltaik_anlage()->QfprodPV())); ?></p>
		<p><?php printf(__('WfPVHP: %s', 'wpenon'), str_replace('.', ',', $gebaeude->photovoltaik_anlage()->WfPVHP())); ?></p>
		<p><?php printf(__('Pvans: %s', 'wpenon'), str_replace('.', ',', $gebaeude->photovoltaik_anlage()->Pvans($gebaeude->Qfstrom()))); ?></p>
	<?php endif; ?>

	<h4>Hilfsenergie</h4>

	<p><?php printf(__('pg: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->pg())); ?></p>

	<h5>Bestimmung der Hilfsenergie_Übergabe Wce</h5>

	<p><?php printf(__('WHce: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->WHce())); ?></p>
	<p><?php printf(__('Wc: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Wc())); ?></p>
	<p><?php printf(__('Wrvce: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Wrvce())); ?></p>
	<p><?php printf(__('Wwce: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Wwce())); ?></p>
	<p><?php printf(__('WsolPumpece: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->WsolPumpece())); ?></p>
	<p><?php printf(__('WsolPumpeg: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->WsolPumpeg())); ?></p>

	<h5>Bestimmung der Hilfsenergie_Verteilung Wd</h5>

	<p><?php printf(__('fgeoHzg: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->fgeoHzg())); ?></p>
	<p><?php printf(__('fblHzg: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->fblHzg())); ?></p>
	<p><?php printf(__('fgeoTWW: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->fgeoTWW())); ?></p>
	<p><?php printf(__('fblTWW: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->fblTWW())); ?></p>
	<p><?php printf(__('LcharHzg: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->LcharHzg())); ?></p>
	<p><?php printf(__('LcharTWW: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->LcharTWW())); ?></p>
	<p><?php printf(__('BcarHzg: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->BcarHzg())); ?></p>
	<p><?php printf(__('BcarWW: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->BcarWW())); ?></p>
	<p><?php printf(__('LmaxHzg: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->LmaxHzg())); ?></p>
	<p><?php printf(__('LmaxTWW: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->LmaxTWW())); ?></p>

	<h5>Berechnung der Hilfsenergie_Verteilung Heizung Whd , Rohrnetzberechnung</h5>

	<p><?php printf(__('TERMp: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->TERMp())); ?></p>
	<p><?php printf(__('Vstr: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Vstr())); ?></p>
	<p><?php printf(__('PhydrHzg: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->PhydrHzg())); ?></p>
	<p><?php printf(__('fe: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->fe())); ?></p>
	<p><?php printf(__('TERMpumpe: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->TERMpumpe())); ?></p>
	<p><?php printf(__('fint: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->fint())); ?></p>

	<h5>Berechnung der Hilfsenergie für Heizsysteme</h5>

	<p><?php printf(__('Wrvd: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Wrvd())); ?></p>
	<p><?php printf(__('Lv: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Lv())); ?></p>
	<p><?php printf(__('Ls: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Ls())); ?></p>
	<p><?php printf(__('Pwda: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Pwda())); ?></p>
	<p><?php printf(__('PhydrTWW: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->PhydrTWW())); ?></p>
	<p><?php printf(__('z: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->z())); ?></p>
	<p><?php printf(__('Wwd: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Wwd())); ?></p>
	<p><?php printf(__('WsolPumped: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->WsolPumped())); ?></p>
	<p><?php printf(__('Whs: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Whs())); ?></p>
	<p><?php printf(__('tpu: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->tpu())); ?></p>
	<p><?php printf(__('Vws: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Vws())); ?></p>
	<p><?php printf(__('Wws0: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Wws0())); ?></p>
	<p><?php printf(__('Wws: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Wws())); ?></p>
	<p><?php printf(__('Whd: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Whd())); ?></p>

	<h5>Berechnung der Hilfsenergie für Lüftung</h5>

	<p><?php printf(__('fbaujahr: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->fbaujahr())); ?></p>
	<p><?php printf(__('fgr_exch: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->fgr_exch())); ?></p>
	<p><?php printf(__('fsup_decr: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->fsup_decr())); ?></p>
	<p><?php printf(__('fbetrieb: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->fbetrieb())); ?></p>
	<p><?php printf(__('Wfan0: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->Wfan0())); ?></p>
	<p><?php printf(__('Wc: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->Wc())); ?></p>
	<p><?php printf(__('Wpre_h: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->Wpre_h())); ?></p>
	<p><?php printf(__('fsystem: %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->fsystem())); ?></p>
	<p><?php printf(__('Wrvg (Gesamt): %s', 'wpenon'), str_replace('.', ',', $gebaeude->lueftung()->Wrvg())); ?></p>

	<h5>Berechnung der Hilfsenergie für Solarthermie</h5>
	<p><?php printf(__('WsolPumpece: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->WsolPumpece())); ?></p>
	<p><?php printf(__('WsolPumped: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->WsolPumped())); ?></p>
	<p><?php printf(__('WsolPumpes: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->WsolPumpes())); ?></p>
	<p><?php printf(__('WsolPumpe: %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->WsolPumpe())); ?></p>

	<h5>Hilfsenergie Endergebnisse</h5>
	<p><?php printf(__('Wh (Hilfsenergie Heizsystem): %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Wh())); ?></p>
	<p><?php printf(__('Ww (Hilfsenergie Warmwasser): %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Ww())); ?></p>
	<p><?php printf(__('Wrv (Hilfsenergie Lüftung): %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Wrv())); ?></p>
	<p><?php printf(__('WsolPumpe (Hilfsenergie Solarpumpe): %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->WsolPumpe())); ?></p>
	<p><?php printf(__('Wges (Hilfsenergie Gesamt): %s', 'wpenon'), str_replace('.', ',', $gebaeude->hilfsenergie()->Wges())); ?></p>

	<h3>Endenergie</h3>

	<p><?php printf(__('Qfhges: %s', 'wpenon'), str_replace('.', ',', $gebaeude->Qfhges())); ?></p>
	<p><?php printf(__('Qfwges: %s', 'wpenon'), str_replace('.', ',', $gebaeude->Qfwges())); ?></p>
	<p><?php printf(__('Qfgesamt: %s', 'wpenon'), str_replace('.', ',', $gebaeude->Qfgesamt())); ?></p>
	<p><?php printf(__('Qpges: %s', 'wpenon'), str_replace('.', ',', $gebaeude->Qpges())); ?></p>
	<p><?php printf(__('Qfstrom: %s', 'wpenon'), str_replace('.', ',', $gebaeude->Qfstrom())); ?></p>
	<p><?php printf(__('Qf (Endenergie): %s', 'wpenon'), str_replace('.', ',', $gebaeude->Qf())); ?></p>

	<h4>Vergleichswerte</h4>

	<p><?php printf(__('Qp (Primärenergie): %s', 'wpenon'), str_replace('.', ',', $gebaeude->Qp())); ?></p>
	<p><?php printf(__('Ht\': %s', 'wpenon'), str_replace('.', ',', $gebaeude->ht_strich())); ?></p>

	<h3>CO2</h3>
	<p><?php printf(__('CO2 Emissionen in Kg: %s', 'wpenon'), str_replace('.', ',', $gebaeude->MCO2())); ?></p>
	<p><?php printf(__('CO2 Emissionen in Kg/m2: %s', 'wpenon'), str_replace('.', ',', $gebaeude->MCO2a())); ?></p>

</div>
<?php if (($anlass === 'modernisierung' || $anlass === 'sonstiges') && isset($referenzgebaeude)) : ?>

	<div class="calculation-details referenzgebaeude">
		<h2>Referenzgebaeude</h2>
		<p><?php printf(__('Baujahr: %s;', 'wpenon'), $referenzgebaeude->baujahr()); ?></p>
		<p><?php printf(__('Hüllvolumen V<sub>e</sub>: %s m&sup3;', 'wpenon'), str_replace('.', ',', $referenzgebaeude->huellvolumen())); ?></p>
		<p><?php printf(__('Hüllvolumen (netto): %s m&sup3;', 'wpenon'), str_replace('.', ',', $referenzgebaeude->huellvolumen_netto())); ?></p>
		<p><?php printf(__('Hüllfäche<sub>e</sub>: %s m&sup2;', 'wpenon'), str_replace('.', ',', $referenzgebaeude->huellflaeche())); ?></p>
		<p><?php printf(__('ave Verhältnis: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->ave_verhaeltnis())); ?></p>
		<p><?php printf(__('Nutzfläche A<sub>N</sub>: %s m&sup2;', 'wpenon'), str_replace('.', ',', $referenzgebaeude->nutzflaeche())); ?></p>
		<p><?php printf(__('Anzahl der Geschosse: %s', 'wpenon'), $referenzgebaeude->geschossanzahl()); ?></p>
		<p><?php printf(__('Geschosshöhe: %s m', 'wpenon'), str_replace('.', ',', $referenzgebaeude->geschosshoehe())); ?></p>
		<p><?php printf(__('Anzahl der Wohnungen: %s', 'wpenon'), $referenzgebaeude->anzahl_wohnungen()); ?></p>
		<p><?php printf(__('Einfamilienhaus: %s', 'wpenon'), $referenzgebaeude->ist_einfamilienhaus() ? 'Ja' : 'Nein'); ?></p>


		<h3>Grundriss</h3>
		<p>Ausrichtung des Gebäudes: <?php echo $referenzgebaeude->grundriss()->ausrichtung(); ?></p>
		<table>
			<tr>
				<th>Seite</th>
				<th>Länge</th>
				<th>Ausrichtung</th>
			</tr>
			<?php foreach ($referenzgebaeude->grundriss()->waende() as $wand) : ?>
				<tr>
					<td><?php echo $wand; ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->grundriss()->wand_laenge($wand)); ?> m</td>
					<td><?php echo $referenzgebaeude->grundriss()->wand_himmelsrichtung($wand); ?></td>
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
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärmekoeffizient ht</th>
			</tr>
			<?php foreach ($referenzgebaeude->bauteile()->waende()->alle() as $wand) : ?>
				<tr>
					<td><?php echo $wand->name(); ?></td>
					<td><?php echo str_replace('.', ',', $wand->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo str_replace('.', ',', $wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo str_replace('.', ',', $wand->daemmung()); ?> cm</td>
					<td><?php echo str_replace('.', ',', $wand->fx()); ?></td>
					<td><?php echo str_replace('.', ',', $wand->ht()); ?> W/K</td>
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
			<?php foreach ($referenzgebaeude->bauteile()->filter('Fenster')->alle() as $fenster) : ?>
				<tr>
					<td><?php echo $fenster->name(); ?></td>
					<td><?php echo str_replace('.', ',', $fenster->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo str_replace('.', ',', $fenster->gwert()); ?></td>
					<td><?php echo str_replace('.', ',', $fenster->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo str_replace('.', ',', $fenster->fx()); ?></td>
					<td><?php echo str_replace('.', ',', $fenster->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>

		<h3>Heizköpernischen</h3>
		<?php if ($referenzgebaeude->bauteile()->filter('Heizkoerpernische')->anzahl() > 0) : ?>
			<table>
				<tr>
					<th>Bauteil</th>
					<th>Fläche</th>
					<th>U-Wert</th>
					<th>Fx Faktor</th>
					<th>Transmissionswärmekoeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Heizkoerpernische')->alle() as $heizkoerpernische) : ?>
					<tr>
						<td><?php echo $heizkoerpernische->name(); ?></td>
						<td><?php echo str_replace('.', ',', $heizkoerpernische->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo str_replace('.', ',', $heizkoerpernische->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo str_replace('.', ',', $heizkoerpernische->fx()); ?></td>
						<td><?php echo str_replace('.', ',', $heizkoerpernische->ht()); ?> W/K</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php else : ?>
			<p class="lead"><?php _e('Keine Heizkörpernischen vorhanden.', 'wpenon'); ?></p>
		<?php endif; ?>

		<h3>Rolladenkästen</h3>
		<?php if ($referenzgebaeude->bauteile()->filter('Rolladenkasten')->anzahl() > 0) : ?>
			<table>
				<tr>
					<th>Bauteil</th>
					<th>Fläche</th>
					<th>U-Wert</th>
					<th>Fx Faktor</th>
					<th>Transmissionswärmekoeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Rolladenkasten')->alle() as $rolladenkaesten) : ?>
					<tr>
						<td><?php echo $rolladenkaesten->name(); ?></td>
						<td><?php echo str_replace('.', ',', $rolladenkaesten->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo str_replace('.', ',', $rolladenkaesten->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo str_replace('.', ',', $rolladenkaesten->fx()); ?></td>
						<td><?php echo str_replace('.', ',', $rolladenkaesten->ht()); ?> W/K</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php else : ?>
			<p class="lead"><?php _e('Keine Rolladenkästen vorhanden.', 'wpenon'); ?></p>
		<?php endif; ?>

		<?php if ($referenzgebaeude->dach_vorhanden()) : ?>
			<h3>Dach</h3>
			<table>
				<tr>
					<th>Bauteil</th>
					<th>Fläche</th>
					<th>Höhe</th>
					<th>U-Wert</th>
					<th>Dämmung</th>
					<th>Fx Faktor</th>
					<th>Transmissionswärmekoeffizient ht</th>
				</tr>
				<tr>
					<td><?php echo $referenzgebaeude->dach()->name(); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->dach()->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->dach()->hoehe()); ?> m</td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->dach()->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->dach()->daemmung()); ?> cm</td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->dach()->fx()); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->dach()->ht()); ?> W/K</td>
				</tr>
			</table>
		<?php else : ?>
			<h3>Decke</h3>
			<table>
				<tr>
					<th>Bauteil</th>
					<th>Fläche</th>
					<th>U-Wert</th>
					<th>Dämmung</th>
					<th>Fx Faktor</th>
					<th>Transmissionswärmekoeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Decke')->alle() as $decke) : ?>
					<tr>
						<td><?php echo $decke->name(); ?></td>
						<td><?php echo str_replace('.', ',', $decke->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo str_replace('.', ',', $decke->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo str_replace('.', ',', $decke->daemmung()); ?> cm</td>
						<td><?php echo str_replace('.', ',', $decke->fx()); ?></td>
						<td><?php echo str_replace('.', ',', $decke->ht()); ?> W/K</td>
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
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärmekoeffizient ht</th>
			</tr>
			<?php foreach ($referenzgebaeude->bauteile()->filter('Boden')->alle() as $boden) : ?>
				<tr>
					<td><?php echo $boden->name(); ?></td>
					<td><?php echo str_replace('.', ',', $boden->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo str_replace('.', ',', $boden->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo str_replace('.', ',', $boden->daemmung()); ?> cm</td>
					<td><?php echo str_replace('.', ',', $boden->fx()); ?></td>
					<td><?php echo str_replace('.', ',', $boden->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>


		<?php if ($referenzgebaeude->keller_vorhanden()) : ?>
			<h3>Keller</h3>
			<p class="lead"><?php printf(__('Unterkellerung: %s;', 'wpenon'), str_replace('.', ',', $referenzgebaeude->keller()->anteil())); ?></p>
			<p class="lead"><?php printf(__('Kellerfläche A<sub>K</sub>: %s m&sup2;', 'wpenon'), str_replace('.', ',', $referenzgebaeude->keller()->boden_flaeche())); ?></p>
			<p class="lead"><?php printf(__('Kellerwandlänge U<sub>K</sub>: %s m;', 'wpenon'), str_replace('.', ',', $referenzgebaeude->keller()->wand_laenge())); ?></p>
			<p class="lead"><?php printf(__('Kellerwandhöhe H<sub>K</sub>: %s m;', 'wpenon'), str_replace('.', ',', $referenzgebaeude->keller()->wand_hoehe())); ?></p>
			<p class="lead"><?php printf(__('Kellervolumen V<sub>K</sub>: %s m&sup3;', 'wpenon'), str_replace('.', ',', $referenzgebaeude->keller()->volumen())); ?></p>
			<table>
				<tr>
					<th>Wand</th>
					<th>Fläche</th>
					<th>U-Wert</th>
					<th>Dämmung</th>
					<th>Fx Faktor</th>
					<th>Transmissionswärmekoeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Kellerwand')->alle() as $wand) : ?>
					<tr>
						<td><?php echo $wand->name(); ?></td>
						<td><?php echo str_replace('.', ',', $wand->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo str_replace('.', ',', $wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo str_replace('.', ',', $wand->daemmung()); ?> cm</td>
						<td><?php echo str_replace('.', ',', $wand->fx()); ?></td>
						<td><?php echo str_replace('.', ',', $wand->ht()); ?> W/K</td>
					</tr>
				<?php endforeach; ?>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Kellerboden')->alle() as $wand) : ?>
					<tr>
						<td><?php echo $wand->name(); ?></td>
						<td><?php echo str_replace('.', ',', $wand->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo str_replace('.', ',', $wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo str_replace('.', ',', $wand->daemmung()); ?> cm</td>
						<td><?php echo str_replace('.', ',', $wand->fx()); ?></td>
						<td><?php echo str_replace('.', ',', $wand->ht()); ?> W/K</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php endif; ?>

		<?php if ($referenzgebaeude->anbau_vorhanden()) : ?>
			<h3>Anbau</h3>
			<p class="lead"><?php printf(__('Anbau Fläche: %s m&sup2; ', 'wpenon'), str_replace('.', ',', $referenzgebaeude->anbau()->grundriss()->flaeche())); ?></p>
			<p class="lead"><?php printf(__('Anbau Volumen: %s m&sup2; ', 'wpenon'), str_replace('.', ',', $referenzgebaeude->anbau()->volumen())); ?></p>
			<table>
				<tr>
					<th>Wand</th>
					<th>Fläche</th>
					<th>U-Wert</th>
					<th>Dämmung</th>
					<th>Fx Faktor</th>
					<th>Transmissionswärmekoeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Anbauwand')->alle() as $wand) : ?>
					<tr>
						<td><?php echo $wand->name(); ?></td>
						<td><?php echo str_replace('.', ',', $wand->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo str_replace('.', ',', $wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo str_replace('.', ',', $wand->daemmung()); ?> cm</td>
						<td><?php echo str_replace('.', ',', $wand->fx()); ?></td>
						<td><?php echo str_replace('.', ',', $wand->ht()); ?> W/K</td>
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
				<?php foreach ($referenzgebaeude->bauteile()->filter('Anbaufenster')->alle() as $anbaufenster) : ?>
					<tr>
						<td><?php echo $anbaufenster->name(); ?></td>
						<td><?php echo str_replace('.', ',', $anbaufenster->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo str_replace('.', ',', $anbaufenster->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo str_replace('.', ',', $anbaufenster->fx()); ?></td>
						<td><?php echo str_replace('.', ',', $anbaufenster->ht()); ?> W/K</td>
					</tr>
				<?php endforeach; ?>
			</table>

			<h3>Anbauboden</h3>
			<table>
				<tr>
					<th>Bauteil</th>
					<th>Fläche</th>
					<th>U-Wert</th>
					<th>Dämmung</th>
					<th>Fx Faktor</th>
					<th>Transmissionswärmekoeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Anbauboden')->alle() as $boeden) : ?>
					<tr>
						<td><?php echo $boeden->name(); ?></td>
						<td><?php echo str_replace('.', ',', $boeden->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo str_replace('.', ',', $boeden->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo str_replace('.', ',', $boeden->daemmung()); ?> cm</td>
						<td><?php echo str_replace('.', ',', $boeden->fx()); ?></td>
						<td><?php echo str_replace('.', ',', $boeden->ht()); ?> W/K</td>
					</tr>
				<?php endforeach; ?>
			</table>

			<h3>Anbaudecke</h3>
			<table>
				<tr>
					<th>Bauteil</th>
					<th>Fläche</th>
					<th>U-Wert</th>
					<th>Dämmung</th>
					<th>Fx Faktor</th>
					<th>Transmissionswärmekoeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Anbaudecke')->alle() as $decke) : ?>
					<tr>
						<td><?php echo $boeden->name(); ?></td>
						<td><?php echo str_replace('.', ',', $decke->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo str_replace('.', ',', $decke->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo str_replace('.', ',', $decke->daemmung()); ?> cm</td>
						<td><?php echo str_replace('.', ',', $decke->fx()); ?></td>
						<td><?php echo str_replace('.', ',', $decke->ht()); ?> W/K</td>
					</tr>
				<?php endforeach; ?>
			</table>

		<?php endif; ?>

		<h3>Transmission</h3>


		<p><?php printf(__('Transmissionswärmekoeffizient Bauteile ht: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->bauteile()->ht())); ?></p>
		<p><?php printf(__('Transmissionswärmekoeffizient Fenster hw: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->bauteile()->hw())); ?></p>
		<p><?php printf(__('Wärmebrückenzuschlag (ht_wb): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->ht_wb())); ?></p>
		<p><?php printf(__('Transmissionswärmekoeffizient Gesamt ht<sub>ges</sub>: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->ht_ges())); ?></p>
		<p><?php printf(__('Wärmetransferkoeffizient des Gebäudes. (h ges): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->h_ges())); ?></p>
		<p><?php printf(__('Tau: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->tau())); ?></p>
		<p><?php printf(__('Maximaler Wärmestrom Q: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->q())); ?></p>

		<h3>Lüftung</h3>

		<p><?php printf(__('Lueftungssystem: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->lueftungssystem())); ?></p>
		<p><?php printf(__('Bedarfsgeführt: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->ist_bedarfsgefuehrt() ? 'Ja' : 'Nein')); ?></p>
		<p><?php printf(__('Gebäudedichtheit: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->gebaeudedichtheit())); ?></p>
		<p><?php printf(__('Wirkungsgrad: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->wirkungsgrad())); ?></p>
		<p><?php printf(__('Luftechselvolumen h<sub>v</sub>: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->hv())); ?></p>
		<p><?php printf(__('Maximale Heizlast h<sub>max</sub>: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->h_max())); ?></p>
		<p><?php printf(__('Maximale Heizlast spezifisch h<sub>max,spez</sub>: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->h_max_spezifisch())); ?></p>

		<h4>Luftwechsel Werte</h4>
		<p><?php printf(__('Luftwechselrate n: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->luftwechsel()->n())); ?></p>
		<p><?php printf(__('Gesamtluftwechselrate n<sub>0</sub>: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->luftwechsel()->n0())); ?></p>
		<p><?php printf(__('Korrekturfakror f<sub>win,1</sub>: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->luftwechsel()->fwin1())); ?></p>
		<p><?php printf(__('Korrekturfakror f<sub>win,2</sub>: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->luftwechsel()->fwin2())); ?></p>
		<p><?php printf(__('n_anl: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->luftwechsel()->n_anl())); ?></p>
		<p><?php printf(__('n_wrg: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->luftwechsel()->n_wrg())); ?></p>

		<hr />

		<h2>Bilanzierung</h2>

		<h3>Interne Wärmequellen</h3>
		<table>
			<tr>
				<th>Monat</th>
				<th>Qi<sub>p</sub> (kWh)</th>
				<th>Qi<sub>w</sub> (kWh)</th>
				<th>Qi<sub>s</sub> (kWh)</th>
				<th>Qi<sub>h</sub> (kWh)</th>
				<th>Qi<sub>ges</sub> (kWh)</th>
			</tr>
			<?php foreach ($jahr->monate() as $monat) : ?>
				<tr>
					<td><?php echo $monat->name(); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->qi_prozesse_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->qi_wasser_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->qi_solar_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->qi_heizung_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->qi_monat($monat->slug())); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td><b>Gesamt</b></td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->qi_prozesse()); ?></td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->qi_wasser()); ?></td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->qi_solar()); ?></td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->qi_heizung()); ?></td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->qi()); ?></td>
			</tr>
		</table>

		<h3>fum</h3>
		<table>
			<tr>
				<th>Monat</th>
				<th>fum</th>
			</tr>
			<?php foreach ($jahr->monate() as $monat) : ?>
				<tr>
					<td><?php echo $monat->name(); ?></td>
					<td><?php echo str_replace('.', ',', fum($monat->slug())); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>

		<table>
			<tr>
				<th>Monat</th>
				<th>P*H<sub>sink</sub> (W)</th>
				<th>PH<sub>sink</sub> (W)</th>
				<th>PH<sub>source</sub> (W)</th>
				<th>Q<sub>w,b</sub> (kWh)</th>
				<th>Q<sub>h,b</sub> (kWh)</th>
			</tr>
			<?php foreach ($jahr->monate() as $monat) : ?>
				<tr>
					<td><?php echo $monat->name(); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->psh_sink_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->ph_sink_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->ph_source_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->QWB_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->qh_monat($monat->slug())); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td><b>Gesamt</b></td>
				<td></td>
				<td></td>
				<td></td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->QWB()); ?></td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->qh()); ?></td>
			</tr>
		</table>

		<h3>Jahr Gesamt</h3>
		<table>
			<tr>
				<th>ßhma</th>
				<th>thm</th>
				<th>ith_rl</th>
				<th>Qi<sub>ges</sub> (kWh)</th>
				<th>Q<sub>w,b</sub> (kWh)</th>
				<th>Q<sub>h,b</sub> (kWh)</th>
			</tr>
			<tr>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->ßhma()); ?></td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->thm()); ?></td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->ith_rl()); ?></td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->qi()); ?></td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->QWB()); ?></td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->qh()); ?></td>
			</tr>
		</table>

		<h3>Korrekturfaktoren und wetere Werte</h3>

		<table>
			<tr>
				<th>Monat</th>
				<th>P<sub>h+w+str+p,source</sub></th>
				<th>ym</th>
				<th>nm</th>
				<th>flna</th>
				<th>trl</th>
				<th>ith_rl</th>
			</tr>
			<?php foreach ($jahr->monate() as $monat) : ?>
				<tr>
					<td><?php echo $monat->name(); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->ph_source_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->ym_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->nm_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->flna_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->trl_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->ith_rl_monat($monat->slug())); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td><b>Gesamt</b></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->ith_rl()); ?></td>
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
			<?php foreach ($jahr->monate() as $monat) : ?>
				<tr>
					<td><?php echo $monat->name(); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->k_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->bilanz_innentemperatur()->θih_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->mittlere_belastung()->ßem1($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->ßhm_monat($monat->slug())); ?></td>
					<td><?php echo str_replace('.', ',', $referenzgebaeude->thm_monat($monat->slug())); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td><b>Gesamt</b></td>
				<td></td>
				<td></td>
				<td><?php echo $referenzgebaeude->mittlere_belastung()->ßemMax(); ?> (ßemMax)</td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->ßhma()); ?> (ßhma)</td>
				<td><?php echo str_replace('.', ',', $referenzgebaeude->thm()); ?></td>
			</tr>
		</table>

		<hr />

		<h2>Heizsystem</h2>

		<h3>Heizungsanlage</h3>
		<?php $i = 1; ?>
		<?php foreach ($referenzgebaeude->heizsystem()->heizungsanlagen()->alle() as $heizungsanlage) : ?>
			<h4><?php echo 'Heizungsanlage ' . $i++; ?></h4>
			<?php if ($heizungsanlage->kategorie() === 'konventioneller_kessel') : ?>
				<table>
					<tr>
						<th>Kategorie</th>
						<td><?php echo $heizungsanlage->kategorie(); ?></td>
					</tr>
					<tr>
						<th>Heizungstyp</th>
						<td><?php echo $heizungsanlage->typ(); ?></td>
					</tr>
					<tr>
						<th>Energieträger</th>
						<td><?php echo $heizungsanlage->energietraeger(); ?></td>
					</tr>
					<tr>
						<th>Anteil</th>
						<td><?php echo $heizungsanlage->prozentualer_anteil(); ?></td>
					</tr>
					<tr>
						<th>eg0</th>
						<td><?php echo $heizungsanlage->eg0(); ?></td>
					</tr>
					<tr>
						<th>fbj</th>
						<td><?php echo $heizungsanlage->fbj(); ?></td>
					</tr>
					<tr>
						<th>ßhg</th>
						<td><?php echo $heizungsanlage->ßhg(); ?></td>
					</tr>
					<tr>
						<th>fegt</th>
						<td><?php echo $heizungsanlage->fegt(); ?></td>
					</tr>
					<tr>
						<th>ehg</th>
						<td><?php echo $heizungsanlage->ehg(); ?></td>
					</tr>
					<tr>
						<th>ewg0</th>
						<td><?php echo $heizungsanlage->ewg0(); ?></td>
					</tr>
					<tr>
						<th>ewg</th>
						<td><?php echo $heizungsanlage->ewg(); ?></td>
					</tr>
					<tr>
						<th>fco2 (CO2 Emissiomnsfaktor)</th>
						<td><?php echo $heizungsanlage->fco2(); ?></td>
					</tr>
					<tr>
						<th>fp (Primärenergiefaktor)</th>
						<td><?php echo $heizungsanlage->fp(); ?></td>
					</tr>
				</table>
				<h5>Hilfsenergie</h5>
				<table>
					<tr>
						<th>tpwn0</th>
						<td><?php echo $heizungsanlage->twpn0(); ?></td>
					</tr>
					<tr>
						<th>tpwn</th>
						<td><?php echo $heizungsanlage->twpn(); ?></td>
					</tr>
					<tr>
						<th>fphgaux</th>
						<td><?php echo $heizungsanlage->fphgaux(); ?></td>
					</tr>
					<tr>
						<th>Phgaux</th>
						<td><?php echo $heizungsanlage->Phgaux(); ?></td>
					</tr>
					<tr>
						<th>fpwgaux</th>
						<td><?php echo $heizungsanlage->fpwgaux(); ?></td>
					</tr>
					<tr>
						<th>Pwgaux</th>
						<td><?php echo $heizungsanlage->Pwgaux(); ?></td>
					</tr>
					<tr>
						<th>Whg</th>
						<td><?php echo $heizungsanlage->Whg(); ?></td>
					</tr>
					<tr>
						<th>Wwg</th>
						<td><?php echo $heizungsanlage->Wwg(); ?></td>
					</tr>
				</table>

				<h5>Weitere Werte</h5>
				<table>
					<tr>
						<th>MCO2</th>
						<td><?php echo $heizungsanlage->MCO2(); ?></td>
					</tr>
				</table>

			<?php elseif ($heizungsanlage->kategorie() === 'waermepumpe') : ?>
				<table>
					<tr>
						<td>Kategorie</td>
						<td><?php echo $heizungsanlage->kategorie(); ?></td>
					</tr>
					<tr>
						<td>Heizungstyp</td>
						<td><?php echo $heizungsanlage->typ(); ?></td>
					</tr>
					<tr>
						<td>Energieträger</td>
						<td><?php echo $heizungsanlage->energietraeger(); ?></td>
					</tr>
					<tr>
						<td>Anteil</td>
						<td><?php echo $heizungsanlage->prozentualer_anteil(); ?></td>
					</tr>
					<tr>
						<td>θva</td>
						<td><?php echo $heizungsanlage->θva(); ?></td>
					</tr>
					<tr>
						<td>θvl</td>
						<td><?php echo $heizungsanlage->θvl(); ?></td>
					</tr>
					<tr>
						<td>COPtk -7</td>
						<td><?php echo $heizungsanlage->COPtk_7(); ?></td>
					</tr>
					<tr>
						<td>COPtk 2</td>
						<td><?php echo $heizungsanlage->COPtk2(); ?></td>
					</tr>
					<tr>
						<td>COPtk 7</td>
						<td><?php echo $heizungsanlage->COPtk7(); ?></td>
					</tr>
					<tr>
						<td>W -7</td>
						<td><?php echo $heizungsanlage->W_7(); ?></td>
					</tr>
					<tr>
						<td>W 2</td>
						<td><?php echo $heizungsanlage->W2(); ?></td>
					</tr>
					<tr>
						<td>W 7</td>
						<td><?php echo $heizungsanlage->W7(); ?></td>
					</tr>
					<tr>
						<td>e gsamt (ehg)</td>
						<td><?php echo $heizungsanlage->eh_ges(); ?></td>
					</tr>
					<tr>
						<td>Qfhges</td>
						<td><?php echo $heizungsanlage->Qfhges(); ?></td>
					</tr>
					<tr>
						<td>ewg</td>
						<td><?php echo $heizungsanlage->ewg(); ?></td>
					</tr>
					<tr>
						<th>fco2 (CO2 Emissiomnsfaktor)</th>
						<td><?php echo $heizungsanlage->fco2(); ?></td>
					</tr>
					<tr>
						<th>fp (Primärenergiefaktor)</th>
						<td><?php echo $heizungsanlage->fp(); ?></td>
					</tr>
				</table>

				<h5>Hilfsenergie</h5>

				<table>
					<tr>
						<th>Whg</th>
						<td><?php echo $heizungsanlage->Whg(); ?></td>
					</tr>
					<tr>
						<th>Wwg</th>
						<td><?php echo $heizungsanlage->Wwg(); ?></td>
					</tr>
				</table>

				<h5>Weitere Werte</h5>
				<table>
					<tr>
						<th>MCO2</th>
						<td><?php echo $heizungsanlage->MCO2(); ?></td>
					</tr>
				</table>

			<?php elseif ($heizungsanlage->kategorie() === 'fernwaerme') : ?>
				<table>
					<tr>
						<td>Kategorie</td>
						<td><?php echo $heizungsanlage->kategorie(); ?></td>
					</tr>
					<tr>
						<td>Heizungstyp</td>
						<td><?php echo $heizungsanlage->typ(); ?></td>
					</tr>
					<tr>
						<td>Energieträger</td>
						<td><?php echo $heizungsanlage->energietraeger(); ?></td>
					</tr>
					<tr>
						<td>Anteil</td>
						<td><?php echo $heizungsanlage->prozentualer_anteil(); ?></td>
					</tr>
					<tr>
						<td>eg0</td>
						<td><?php echo $heizungsanlage->eg0(); ?></td>
					</tr>
					<tr>
						<td>ßhg</td>
						<td><?php echo $heizungsanlage->ßhg(); ?></td>
					</tr>
					<tr>
						<td>fiso</td>
						<td><?php echo $heizungsanlage->fiso(); ?></td>
					</tr>
					<tr>
						<td>ftemp</td>
						<td><?php echo $heizungsanlage->ftemp(); ?></td>
					</tr>
					<tr>
						<td>ehg</td>
						<td><?php echo $heizungsanlage->ehg(); ?></td>
					</tr>
					<tr>
						<td>ewg</td>
						<td><?php echo $heizungsanlage->ewg(); ?></td>
					</tr>
					<tr>
						<th>fco2 (CO2 Emissiomnsfaktor)</th>
						<td><?php echo $heizungsanlage->fco2(); ?></td>
					</tr>
					<tr>
						<th>fp (Primärenergiefaktor)</th>
						<td><?php echo $heizungsanlage->fp(); ?></td>
					</tr>
				</table>

				<h5>Hilfsenergie</h5>

				<table>
					<tr>
						<th>Whg</th>
						<td><?php echo $heizungsanlage->Whg(); ?></td>
					</tr>
					<tr>
						<th>Wwg</th>
						<td><?php echo $heizungsanlage->Wwg(); ?></td>
					</tr>
				</table>

				<h5>Weitere Werte</h5>
				<table>
					<tr>
						<th>MCO2</th>
						<td><?php echo $heizungsanlage->MCO2(); ?></td>
					</tr>
				</table>

			<?php elseif ($heizungsanlage->kategorie() === 'dezentral') : ?>
				<table>
					<tr>
						<td>Kategorie</td>
						<td><?php echo $heizungsanlage->kategorie(); ?></td>
					</tr>
					<tr>
						<td>Heizungstyp</td>
						<td><?php echo $heizungsanlage->typ(); ?></td>
					</tr>
					<tr>
						<td>Energieträger</td>
						<td><?php echo $heizungsanlage->energietraeger(); ?></td>
					</tr>
					<tr>
						<td>Anteil</td>
						<td><?php echo $heizungsanlage->prozentualer_anteil(); ?></td>
					</tr>
					<tr>
						<td>ehg</td>
						<td><?php echo $heizungsanlage->ehg(); ?></td>
					</tr>
					<tr>
						<td>ewg</td>
						<td><?php echo $heizungsanlage->ewg(); ?></td>
					</tr>
					<tr>
						<th>fco2 (CO2 Emissiomnsfaktor)</th>
						<td><?php echo $heizungsanlage->fco2(); ?></td>
					</tr>
					<tr>
						<th>fp (Primärenergiefaktor)</th>
						<td><?php echo $heizungsanlage->fp(); ?></td>
					</tr>
				</table>

				<h5>Hilfsenergie</h5>

				<table>
					<tr>
						<th>Whg</th>
						<td><?php echo $heizungsanlage->Whg(); ?></td>
					</tr>
					<tr>
						<th>Wwg</th>
						<td><?php echo $heizungsanlage->Wwg(); ?></td>
					</tr>
				</table>

				<h5>Weitere Werte</h5>
				<table>
					<tr>
						<th>MCO2</th>
						<td><?php echo $heizungsanlage->MCO2(); ?></td>
					</tr>
				</table>
			<?php endif; ?>

		<?php endforeach; ?>

		<h3>Übergabesystem</h3>
		<table>
			<tr>
				<th>Übergabetyp</th>
				<th>Auslegungstemperatur</th>
				<th>Anteil</th>
				<th>ehce</th>
			</tr>
			<?php foreach ($referenzgebaeude->heizsystem()->uebergabesysteme()->alle() as $uebergabesystem) : ?>
				<tr>
					<td><?php echo $uebergabesystem->typ(); ?></td>
					<td><?php echo $uebergabesystem->auslegungstemperaturen(); ?></td>
					<td><?php echo $uebergabesystem->prozentualer_anteil(); ?></td>
					<td><?php echo str_replace('.', ',', $uebergabesystem->ehce()); ?></td>
				<?php endforeach; ?>
		</table>

		<h3>Heizsystem</h3>

		<p><?php printf(__('Nutzbare Wärme fa<sub>h</sub>: %s', 'wpenon'), $referenzgebaeude->heizsystem()->fa_h()); ?></p>
		<p><?php printf(__('Mittlere Belastung bei Übergabe der Heizung (ßhce): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->ßhce())); ?></p>
		<p><?php printf(__('Flächenbezogene leistung der Übergabe der Heizung (qhce): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->qhce())); ?></p>
		<p><?php printf(__('ßhd: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->ßhd())); ?></p>
		<p><?php printf(__('fßd: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->fßd())); ?></p>
		<p><?php printf(__('ehd0: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->ehd0())); ?></p>
		<p><?php printf(__('ehd1: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->ehd1())); ?></p>
		<p><?php printf(__('ehd: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->ehd())); ?></p>
		<p><?php printf(__('ehd korrektur: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->ehd_korrektur())); ?></p>
		<p><?php printf(__('ßhs: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->ßhs())); ?></p>

		<p><?php printf(__('Nennleistung Pufferspeicher (pwn): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->pwn())); ?></p>
		<p><?php printf(__('(pn): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->pn())); ?></p>



		<?php if ($referenzgebaeude->heizsystem()->pufferspeicher_vorhanden()) : ?>
			<h3>Pufferspeicher</h3>
			<p><?php printf(__('Korrekturfaktor mittlere Belastung des Pufferspeichers fßhs: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->pufferspeicher()->fßhs())); ?></p>
			<p><?php printf(__('Mittlere Belastung für Speicherung ßhs: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->ßhs())); ?></p>
			<p><?php printf(__('Korrekturfaktor für beliebige mittlere Berlastung und Laufzeit der Heizung fhs: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->pufferspeicher()->fhs())); ?></p>
			<p><?php printf(__('Berechnetes Volumen: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->pufferspeicher()->volumen())); ?></p>
			<p><?php printf(__('Volumen Pufferspeicher vs1: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->pufferspeicher()->vs1())); ?></p>
			<p><?php printf(__('Volumen Pufferspeicher vs2: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->pufferspeicher()->vs2())); ?></p>
			<p><?php printf(__('Wärmeabgabe Pufferspeicher (Qhs0Vs1): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->pufferspeicher()->Qhs0Vs1())); ?></p>
			<p><?php printf(__('Wärmeabgabe Pufferspeicher (Qhs0Vs2): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->pufferspeicher()->Qhs0Vs2())); ?></p>
			<p><?php printf(__('Wärmeabgabe Pufferspeicher Gesamt (Qhs): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->pufferspeicher()->Qhs())); ?></p>
			<p><?php printf(__('Aufwandszahl für Pufferspeicher (ehs): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->ehs())); ?></p>
		<?php else : ?>
			<p><?php printf(__('Aufwandszahl für Pufferspeicher (ehs):  %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->heizsystem()->ehs())); ?></p>
		<?php endif; ?>

		<h3>Trinkwarmwasseranlage</h3>

		<p><?php printf(__('Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen Faw: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Faw())); ?></p>
		<p><?php printf(__('Nutzwärmebedarf für Trinkwasser qwb: %s kWh/(ma)', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->nutzwaermebedarf_trinkwasser())); ?></p>
		<p><?php printf(__('Q<sub>w,b</sub>: %s kWh', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->QWB())); ?></p>
		<p><?php printf(__('Interne Wärmequelle infolge von Warmwasser Qi<sub>w</sub>: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->qi_wasser())); ?></p>
		<p><?php printf(__('Jährlicher Nutzwaermebedarf für Trinkwasser (qwb): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->nutzwaermebedarf_trinkwasser())); ?></p>
		<p><?php printf(__('Berechnung des monatlichen Wärmebedarfs für Warmwasser(QWB) für ein Jahr: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->QWB())); ?></p>

		<h4>Aufwandszahlen Trinkwarmwasser</h4>

		<p><?php printf(__('Zwischenwert für die Berechnung von ewd (ewce): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->ewce())); ?></p>
		<p><?php printf(__('Zwischenwert für die Berechnung von ewd (ewd0): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->ewd0())); ?></p>
		<p><?php printf(__('Aufwandszahlen für die Verteilung von Trinkwarmwasser (ewd): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->ewd())); ?></p>
		<p><?php printf(__('Korrekturfaktor (fwb): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->fwb())); ?></p>
		<p><?php printf(__('Volumen Speicher 1 in Litern. (Vs01): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Vs01())); ?></p>
		<p><?php printf(__('Volumen Speicher 2 in Litern. (Vs02): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Vs02())); ?></p>
		<p><?php printf(__('Volumen Speicher 3 in Litern. (Vs03): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Vs03())); ?></p>
		<p><?php printf(__('Volumen Speicher Gesamt in Litern. (Vs0): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Vs0())); ?></p>
		<p><?php printf(__('Berechnung von Vsw1: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Vsw1())); ?></p>
		<p><?php printf(__('Berechnung von Vsw2: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Vsw2())); ?></p>
		<p><?php printf(__('Berechnung von Qws01: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Qws01())); ?></p>
		<p><?php printf(__('Berechnung von Qws02: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Qws02())); ?></p>

		<?php if ($referenzgebaeude->trinkwarmwasseranlage()->solarthermie_vorhanden()) : ?>
			<h4>Solarthermie</h4>
			<p><?php printf(__('Berechnung von Vsaux0: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Vsaux0())); ?></p>
			<p><?php printf(__('Berechnung von Vssol0: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Vssol0())); ?></p>
			<p><?php printf(__('Berechnung von Ac0: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Ac0())); ?></p>
			<p><?php printf(__('Berechnung von Qwsola0: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Qwsola0())); ?></p>
			<br />
			<p><?php printf(__('Berechnung von Vsaux: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Vsaux())); ?></p>
			<p><?php printf(__('Berechnung von Vssol: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Vssol())); ?></p>
			<p><?php printf(__('Berechnung von Ac: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Ac())); ?></p>
			<p><?php printf(__('Berechnung von fAc: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->fAc())); ?></p>
			<p><?php printf(__('Berechnung von fQsola: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->fQsola())); ?></p>
			<p><?php printf(__('Berechnung von Qwsola: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Qwsola())); ?></p>
		<?php endif; ?>

		<br>

		<p><?php printf(__('Berechnung von Qws: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->Qws())); ?></p>
		<p><?php printf(__('Berechnung von ews: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->ews())); ?></p>
		<p><?php printf(__('Berechnung von keew: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->keew())); ?></p>
		<p><?php printf(__('Berechnung von keeh: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->trinkwarmwasseranlage()->keeh())); ?></p>

		<?php if ($referenzgebaeude->photovoltaik_anlage_vorhanden()) : ?>
			<h3>Photovoltaik</h3>
			<p><?php printf(__('Richtung: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->photovoltaik_anlage()->richtung())); ?></p>
			<p><?php printf(__('Neigung: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->photovoltaik_anlage()->neigung())); ?></p>
			<p><?php printf(__('Fläche: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->photovoltaik_anlage()->flaeche())); ?></p>
			<p><?php printf(__('Baujahr: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->photovoltaik_anlage()->baujahr())); ?></p>
			<p><?php printf(__('QfprodPV: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->photovoltaik_anlage()->QfprodPV())); ?></p>
			<p><?php printf(__('WfPVHP: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->photovoltaik_anlage()->WfPVHP())); ?></p>
			<p><?php printf(__('Pvans: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->photovoltaik_anlage()->Pvans($referenzgebaeude->Qfstrom()))); ?></p>
		<?php endif; ?>

		<h4>Hilfsenergie</h4>

		<p><?php printf(__('pg: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->pg())); ?></p>

		<h5>Bestimmung der Hilfsenergie_Übergabe Wce</h5>

		<p><?php printf(__('WHce: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->WHce())); ?></p>
		<p><?php printf(__('Wc: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Wc())); ?></p>
		<p><?php printf(__('Wrvce: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Wrvce())); ?></p>
		<p><?php printf(__('Wwce: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Wwce())); ?></p>
		<p><?php printf(__('WsolPumpece: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->WsolPumpece())); ?></p>
		<p><?php printf(__('WsolPumpeg: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->WsolPumpeg())); ?></p>

		<h5>Bestimmung der Hilfsenergie_Verteilung Wd</h5>

		<p><?php printf(__('fgeoHzg: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->fgeoHzg())); ?></p>
		<p><?php printf(__('fblHzg: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->fblHzg())); ?></p>
		<p><?php printf(__('fgeoTWW: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->fgeoTWW())); ?></p>
		<p><?php printf(__('fblTWW: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->fblTWW())); ?></p>
		<p><?php printf(__('LcharHzg: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->LcharHzg())); ?></p>
		<p><?php printf(__('LcharTWW: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->LcharTWW())); ?></p>
		<p><?php printf(__('BcarHzg: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->BcarHzg())); ?></p>
		<p><?php printf(__('BcarWW: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->BcarWW())); ?></p>
		<p><?php printf(__('LmaxHzg: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->LmaxHzg())); ?></p>
		<p><?php printf(__('LmaxTWW: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->LmaxTWW())); ?></p>

		<h5>Berechnung der Hilfsenergie_Verteilung Heizung Whd , Rohrnetzberechnung</h5>

		<p><?php printf(__('TERMp: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->TERMp())); ?></p>
		<p><?php printf(__('Vstr: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Vstr())); ?></p>
		<p><?php printf(__('PhydrHzg: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->PhydrHzg())); ?></p>
		<p><?php printf(__('fe: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->fe())); ?></p>
		<p><?php printf(__('TERMpumpe: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->TERMpumpe())); ?></p>
		<p><?php printf(__('fint: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->fint())); ?></p>

		<h5>Berechnung der Hilfsenergie für Heizsysteme</h5>

		<p><?php printf(__('Wrvd: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Wrvd())); ?></p>
		<p><?php printf(__('Lv: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Lv())); ?></p>
		<p><?php printf(__('Ls: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Ls())); ?></p>
		<p><?php printf(__('Pwda: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Pwda())); ?></p>
		<p><?php printf(__('PhydrTWW: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->PhydrTWW())); ?></p>
		<p><?php printf(__('z: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->z())); ?></p>
		<p><?php printf(__('Wwd: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Wwd())); ?></p>
		<p><?php printf(__('WsolPumped: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->WsolPumped())); ?></p>
		<p><?php printf(__('Whs: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Whs())); ?></p>
		<p><?php printf(__('tpu: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->tpu())); ?></p>
		<p><?php printf(__('Vws: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Vws())); ?></p>
		<p><?php printf(__('Wws0: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Wws0())); ?></p>
		<p><?php printf(__('Wws: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Wws())); ?></p>
		<p><?php printf(__('Whd: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Whd())); ?></p>

		<h5>Berechnung der Hilfsenergie für Lüftung</h5>

		<p><?php printf(__('fbaujahr: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->fbaujahr())); ?></p>
		<p><?php printf(__('fgr_exch: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->fgr_exch())); ?></p>
		<p><?php printf(__('fsup_decr: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->fsup_decr())); ?></p>
		<p><?php printf(__('fbetrieb: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->fbetrieb())); ?></p>
		<p><?php printf(__('Wfan0: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->Wfan0())); ?></p>
		<p><?php printf(__('Wc: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->Wc())); ?></p>
		<p><?php printf(__('Wpre_h: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->Wpre_h())); ?></p>
		<p><?php printf(__('fsystem: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->fsystem())); ?></p>
		<p><?php printf(__('Wrvg (Gesamt): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->lueftung()->Wrvg())); ?></p>

		<h5>Berechnung der Hilfsenergie für Solarthermie</h5>
		<p><?php printf(__('WsolPumpece: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->WsolPumpece())); ?></p>
		<p><?php printf(__('WsolPumped: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->WsolPumped())); ?></p>
		<p><?php printf(__('WsolPumpes: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->WsolPumpes())); ?></p>
		<p><?php printf(__('WsolPumpe: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->WsolPumpe())); ?></p>

		<h5>Hilfsenergie Endergebnisse</h5>
		<p><?php printf(__('Wh (Hilfsenergie Heizsystem): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Wh())); ?></p>
		<p><?php printf(__('Ww (Hilfsenergie Warmwasser): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Ww())); ?></p>
		<p><?php printf(__('Wrv (Hilfsenergie Lüftung): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Wrv())); ?></p>
		<p><?php printf(__('WsolPumpe (Hilfsenergie Solarpumpe): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->WsolPumpe())); ?></p>
		<p><?php printf(__('Wges (Hilfsenergie Gesamt): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->hilfsenergie()->Wges())); ?></p>

		<h3>Endenergie</h3>

		<p><?php printf(__('Qfhges: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->Qfhges())); ?></p>
		<p><?php printf(__('Qfwges: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->Qfwges())); ?></p>
		<p><?php printf(__('Qfgesamt: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->Qfgesamt())); ?></p>
		<p><?php printf(__('Qpges: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->Qpges())); ?></p>
		<p><?php printf(__('Qfstrom: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->Qfstrom())); ?></p>
		<p><?php printf(__('Qf: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->Qf())); ?></p>
		<p><?php printf(__('Qp: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->Qp())); ?></p>

		<h3>CO2</h3>
		<p><?php printf(__('CO2 Emissionen in Kg: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->MCO2())); ?></p>
		<p><?php printf(__('CO2 Emissionen in Kg/m2: %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->MCO2a())); ?></p>

		<h4>Vergleichswerte</h4>

		<p><?php printf(__('Qp (Primärenergie): %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->Qp())); ?></p>
		<p><?php printf(__('Ht\': %s', 'wpenon'), str_replace('.', ',', $referenzgebaeude->ht_strich())); ?></p>

	</div>

<?php endif; ?>