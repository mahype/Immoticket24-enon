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

if (!function_exists('wpenon_format_decimal')) {
	function wpenon_format_decimal($value) {
		if (array_key_exists('round', $_GET) && $_GET['round'] == 'false') {
			$value = str_replace('.', ',', $value);
			return $value;
		}

		$value = round($value, 2);
		$value = str_replace('.', ',', $value);

		return $value;
	}
}

?>

<style type="text/css">
	.referenzgebaeude {
		background-color: lightpink !important;
	}

	#calculation-details {
		background-color: lightblue;
		padding: 10px;
		border-radius: 5px;
	}

	#calculation-details h1,
	#calculation-details h2,
	#calculation-details h3,
	#calculation-details h4 {
		margin: 20px 0 20px 0;
	}

	#calculation-details p {
		margin: 0 0 10px 0;
	}

	#calculation-details table {
		width: 100%;
		margin: 0 0 10px 0;
	}

	#calculation-details table th {
		text-align: center;
		width: auto;
	}

	#calculation-details table th,
	td {
		padding: 5px 5px 5px 0;
	}

	#calculation-details table {
		border-collapse: collapse;
		width: auto;
		/* Tabelle passt sich der Breite der Inhalte an */
	}

	#calculation-details table th {
		font-weight: bold;
		text-align: left;
		/* TH fett */
	}

	#calculation-details table tr:nth-child(even) {
		background-color: #f2f2f2 !important;
		-webkit-print-color-adjust: exact;
		print-color-adjust: exact;
		/* Grau für gerade Zeilen */
	}

	#calculation-details table tr:nth-child(odd) {
		background-color: #ffffff;
		/* Weiß für ungerade Zeilen */
	}

	#calculation-details table th,
	#calculation-details table td {
		padding: 5px;
		border: 1px solid #ddd;
		/* Optionale Rahmen für bessere Lesbarkeit */
	}


	<?php if (current_user_can('edit_shop_payments')) : ?>@font-face {
		font-family: 'Zapf Humanist';
		src: url('/app/themes/jason/assets/fonts/Zapf-Humanist/zapf-humanist-601-bt.ttf') format('truetype');
	}

	@font-face {
		font-family: 'Zapf Humanist';
		font-weight: bold;
		src: url('/app/themes/jason/assets/fonts/Zapf-Humanist/zapf-humanist-601-bt-bold.ttf') format('truetype');
	}

	@media print {

		@page {
			margin: 2cm;
			/* Festlegen der Druckränder */
		}



		html {
			margin-top: 0 !important;
		}

		body {
			background-image: none;
			font-family: "Zapf Humanist" !important;
			margin: 0;
		}

		h1,
		h2,
		h3,
		h4,
		h5,
		h6 {
			color: black;
			font-weight: bold;
			text-transform: none;
		}

		#calculation-details h1:first-of-type {
			margin: 0 5px 0 0;
			font-size: 16px;
		}

		#calculation-details h2:first-of-type {
			margin-top: 0;
			font-size: 24px;
		}

		h3 {
			font-size: 28px;
		}

		h4 {
			font-size: 22px;
		}

		h5 {
			font-size: 18px;
		}

		header,
		footer,
		#wpadminbar,
		#hamburger-menu,
		.wp-block-post-title,
		.overview-meta,
		.overview-thumbnail,
		.access-box,
		.action-buttons,
		.calculations {
			display: none;
		}

		.wp-site-blocks {
			border: none;
		}

		.wp-block-spacer {
			display: none;
		}

		.is-style-group-main {
			padding: 0;
		}

		#calculation-details {
			background: none;
			padding: 0px;
			border-radius: 0px;
		}

		.print-only {
			display: block;
		}

		.no-print {
			display: none;
		}

		.no-wrap {
			white-space: nowrap;
		}

		.hyphenate {
			hyphens: auto;
			word-break: break-word;
		}

		.page-break {
			page-break-after: always;
		}

		.has-yellow-neon-to-green-mint-gradient-background {
			background: none !important;
		}

	}

	<?php endif; ?>
</style>

<div class="calculation-details">
	<h3>Gebäude</h3>
	<p><?php printf(__('Baujahr: %s', 'wpenon'), $gebaeude->baujahr()); ?></p>
	<p><?php printf(__('Hüllvolumen V<sub>e</sub>: %s m&sup3;', 'wpenon'), wpenon_format_decimal($gebaeude->huellvolumen())); ?></p>
	<p><?php printf(__('Hüllvolumen (netto): %s m&sup3;', 'wpenon'), wpenon_format_decimal($gebaeude->huellvolumen_netto())); ?></p>
	<p><?php printf(__('Hüllfäche<sub>e</sub>: %s m&sup2;', 'wpenon'), wpenon_format_decimal($gebaeude->huellflaeche())); ?></p>
	<p><?php printf(__('ave Verhältnis: %s', 'wpenon'), wpenon_format_decimal($gebaeude->ave_verhaeltnis())); ?></p>
	<p><?php printf(__('Nutzfläche A<sub>N</sub>: %s m&sup2;', 'wpenon'), wpenon_format_decimal($gebaeude->nutzflaeche())); ?></p>
	<p><?php printf(__('Anzahl der Geschosse: %s', 'wpenon'), $gebaeude->geschossanzahl()); ?></p>
	<p><?php printf(__('Geschosshöhe: %s m', 'wpenon'), wpenon_format_decimal($gebaeude->geschosshoehe())); ?></p>
	<p><?php printf(__('Anzahl der Wohnungen: %s', 'wpenon'), $gebaeude->anzahl_wohnungen()); ?></p>
	<p><?php printf(__('Einfamilienhaus: %s', 'wpenon'), $gebaeude->ist_einfamilienhaus() ? 'Ja' : 'Nein'); ?></p>


	<h4>Grundriss</h4>
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
				<td><?php echo wpenon_format_decimal($gebaeude->grundriss()->wand_laenge($wand)); ?> m</td>
				<td><?php echo $gebaeude->grundriss()->wand_himmelsrichtung($wand); ?></td>
			</tr>
		<?php endforeach; ?>
	</table>

	<h3>Bauteile</h3>

	<h4>Wände</h4>
	<table>
		<tr>
			<th>Bauteil</th>
			<th>Fläche</th>
			<th>U-Wert</th>
			<th>Dämmung</th>
			<th class="no-wrap">Fx Faktor</th>
			<th>Transmissionswärme<br />-koeffizient ht</th>
		</tr>
		<?php foreach ($gebaeude->bauteile()->waende()->alle() as $wand) : ?>
			<tr>
				<td class="no-wrap"><?php echo $wand->name(); ?></td>
				<td class="no-wrap"><?php echo wpenon_format_decimal($wand->flaeche()); ?> m<sup>2</sup></td>
				<td class="no-wrap"><?php echo wpenon_format_decimal($wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
				<td><?php echo wpenon_format_decimal($wand->daemmung()); ?> cm</td>
				<td><?php echo wpenon_format_decimal($wand->fx()); ?></td>
				<td><?php echo wpenon_format_decimal($wand->ht()); ?> W/K</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<h4>Fenster</h4>
	<table>
		<tr>
			<th>Bauteil</th>
			<th>Fläche</th>
			<th>G-Wert</th>
			<th>U-Wert</th>
			<th>Fx Faktor</th>
			<th>Transmissionswärme<br />-koeffizient ht</th>
		</tr>
		<?php foreach ($gebaeude->bauteile()->filter('Fenster')->alle() as $fenster) : ?>
			<tr>
				<td><?php echo $fenster->name(); ?></td>
				<td><?php echo wpenon_format_decimal($fenster->flaeche()); ?> m<sup>2</sup></td>
				<td><?php echo wpenon_format_decimal($fenster->gwert()); ?></td>
				<td><?php echo wpenon_format_decimal($fenster->uwert()); ?> W/(m<sup>2</sup>K)</td>
				<td><?php echo wpenon_format_decimal($fenster->fx()); ?></td>
				<td><?php echo wpenon_format_decimal($fenster->ht()); ?> W/K</td>
			</tr>
		<?php endforeach; ?>
	</table>

	<h4>Heizköpernischen</h4>
	<?php if ($gebaeude->bauteile()->filter('Heizkoerpernische')->anzahl() > 0) : ?>
		<table>
			<tr>
				<th>Bauteil</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärme<br />-koeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Heizkoerpernische')->alle() as $heizkoerpernische) : ?>
				<tr>
					<td><?php echo $heizkoerpernische->name(); ?></td>
					<td><?php echo wpenon_format_decimal($heizkoerpernische->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo wpenon_format_decimal($heizkoerpernische->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo wpenon_format_decimal($heizkoerpernische->fx()); ?></td>
					<td><?php echo wpenon_format_decimal($heizkoerpernische->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else : ?>
		<p class="lead"><?php _e('Keine Heizkörpernischen vorhanden.', 'wpenon'); ?></p>
	<?php endif; ?>

	<h4>Rolladenkästen</h4>
	<?php if ($gebaeude->bauteile()->filter('Rolladenkasten')->anzahl() > 0) : ?>
		<table>
			<tr>
				<th>Bauteil</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärme<br />-koeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Rolladenkasten')->alle() as $rolladenkaesten) : ?>
				<tr>
					<td><?php echo $rolladenkaesten->name(); ?></td>
					<td><?php echo wpenon_format_decimal($rolladenkaesten->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo wpenon_format_decimal($rolladenkaesten->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo wpenon_format_decimal($rolladenkaesten->fx()); ?></td>
					<td><?php echo wpenon_format_decimal($rolladenkaesten->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php else : ?>
		<p class="lead"><?php _e('Keine Rolladenkästen vorhanden.', 'wpenon'); ?></p>
	<?php endif; ?>

	<?php if ($gebaeude->dach_vorhanden()) : ?>
		<h4>Dach</h4>
		<table>
			<tr>
				<th>Bauteil</th>
				<th>Fläche</th>
				<th>Höhe</th>
				<th>U-Wert</th>
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärme<br />-koeffizient ht</th>
			</tr>
			<tr>
				<td><?php echo $gebaeude->dach()->name(); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->dach()->flaeche()); ?> m<sup>2</sup></td>
				<td><?php echo wpenon_format_decimal($gebaeude->dach()->hoehe()); ?> m</td>
				<td><?php echo wpenon_format_decimal($gebaeude->dach()->uwert()); ?> W/(m<sup>2</sup>K)</td>
				<td><?php echo wpenon_format_decimal($gebaeude->dach()->daemmung()); ?> cm</td>
				<td><?php echo wpenon_format_decimal($gebaeude->dach()->fx()); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->dach()->ht()); ?> W/K</td>
			</tr>
		</table>
	<?php else : ?>
		<h4>Decke</h4>
		<table>
			<tr>
				<th>Bauteil</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärme<br />-koeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Decke')->alle() as $decke) : ?>
				<tr>
					<td><?php echo $decke->name(); ?></td>
					<td><?php echo wpenon_format_decimal($decke->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo wpenon_format_decimal($decke->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo wpenon_format_decimal($decke->daemmung()); ?> cm</td>
					<td><?php echo wpenon_format_decimal($decke->fx()); ?></td>
					<td><?php echo wpenon_format_decimal($decke->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>

	<h4>Böden</h4>
	<table>
		<tr>
			<th>Bauteil</th>
			<th>Fläche</th>
			<th>U-Wert</th>
			<th>Dämmung</th>
			<th>Fx Faktor</th>
			<th>Transmissionswärme<br />-koeffizient ht</th>
		</tr>
		<?php foreach ($gebaeude->bauteile()->filter('Boden')->alle() as $boden) : ?>
			<tr>
				<td><?php echo $boden->name(); ?></td>
				<td><?php echo wpenon_format_decimal($boden->flaeche()); ?> m<sup>2</sup></td>
				<td><?php echo wpenon_format_decimal($boden->uwert()); ?> W/(m<sup>2</sup>K)</td>
				<td><?php echo wpenon_format_decimal($boden->daemmung()); ?> cm</td>
				<td><?php echo wpenon_format_decimal($boden->fx()); ?></td>
				<td><?php echo wpenon_format_decimal($boden->ht()); ?> W/K</td>
			</tr>
		<?php endforeach; ?>
	</table>


	<?php if ($gebaeude->keller_vorhanden()) : ?>
		<h4>Keller</h4>
		<p class="lead"><?php printf(__('Unterkellerung: %s', 'wpenon'), wpenon_format_decimal($gebaeude->keller()->anteil())); ?>%</p>
		<p class="lead"><?php printf(__('Kellerfläche A<sub>K</sub>: %s m&sup2;', 'wpenon'), wpenon_format_decimal($gebaeude->keller()->boden_flaeche())); ?></p>
		<p class="lead"><?php printf(__('Kellerwandlänge U<sub>K</sub>: %s m', 'wpenon'), wpenon_format_decimal($gebaeude->keller()->wand_laenge())); ?></p>
		<p class="lead"><?php printf(__('Kellerwandhöhe H<sub>K</sub>: %s m', 'wpenon'), wpenon_format_decimal($gebaeude->keller()->wand_hoehe())); ?></p>
		<p class="lead"><?php printf(__('Kellervolumen V<sub>K</sub>: %s m&sup3;', 'wpenon'), wpenon_format_decimal($gebaeude->keller()->volumen())); ?></p>
		<table>
			<tr>
				<th>Wand</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärme<br />-koeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Kellerwand')->alle() as $wand) : ?>
				<tr>
					<td><?php echo $wand->name(); ?></td>
					<td><?php echo wpenon_format_decimal($wand->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo wpenon_format_decimal($wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo wpenon_format_decimal($wand->daemmung()); ?> cm</td>
					<td><?php echo wpenon_format_decimal($wand->fx()); ?></td>
					<td><?php echo wpenon_format_decimal($wand->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
			<?php foreach ($gebaeude->bauteile()->filter('Kellerboden')->alle() as $wand) : ?>
				<tr>
					<td><?php echo $wand->name(); ?></td>
					<td><?php echo wpenon_format_decimal($wand->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo wpenon_format_decimal($wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo wpenon_format_decimal($wand->daemmung()); ?> cm</td>
					<td><?php echo wpenon_format_decimal($wand->fx()); ?></td>
					<td><?php echo wpenon_format_decimal($wand->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>
	<?php endif; ?>

	<?php if ($gebaeude->anbau_vorhanden()) : ?>
		<h3>Anbau</h3>
		<p class="lead"><?php printf(__('Anbau Fläche: %s m&sup2; ', 'wpenon'), wpenon_format_decimal($gebaeude->anbau()->grundriss()->flaeche())); ?></p>
		<p class="lead"><?php printf(__('Anbau Volumen: %s m&sup2; ', 'wpenon'), wpenon_format_decimal($gebaeude->anbau()->volumen())); ?></p>
		<table>
			<tr>
				<th>Wand</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärme<br />-koeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Anbauwand')->alle() as $wand) : ?>
				<tr>
					<td><?php echo $wand->name(); ?></td>
					<td><?php echo wpenon_format_decimal($wand->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo wpenon_format_decimal($wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo wpenon_format_decimal($wand->daemmung()); ?> cm</td>
					<td><?php echo wpenon_format_decimal($wand->fx()); ?></td>
					<td><?php echo wpenon_format_decimal($wand->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>
		<table>
			<tr>
				<th>Fenster</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärme<br />-koeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Anbaufenster')->alle() as $anbaufenster) : ?>
				<tr>
					<td><?php echo $anbaufenster->name(); ?></td>
					<td><?php echo wpenon_format_decimal($anbaufenster->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo wpenon_format_decimal($anbaufenster->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo wpenon_format_decimal($anbaufenster->fx()); ?></td>
					<td><?php echo wpenon_format_decimal($anbaufenster->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>

		<h4>Anbauboden</h4>
		<table>
			<tr>
				<th>Bauteil</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärme<br />-koeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Anbauboden')->alle() as $boeden) : ?>
				<tr>
					<td><?php echo $boeden->name(); ?></td>
					<td><?php echo wpenon_format_decimal($boeden->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo wpenon_format_decimal($boeden->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo wpenon_format_decimal($boeden->daemmung()); ?> cm</td>
					<td><?php echo wpenon_format_decimal($boeden->fx()); ?></td>
					<td><?php echo wpenon_format_decimal($boeden->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>

		<h4>Anbaudecke</h4>
		<table>
			<tr>
				<th>Bauteil</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärme<br />-koeffizient ht</th>
			</tr>
			<?php foreach ($gebaeude->bauteile()->filter('Anbaudecke')->alle() as $decke) : ?>
				<tr>
					<td><?php echo $boeden->name(); ?></td>
					<td><?php echo wpenon_format_decimal($decke->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo wpenon_format_decimal($decke->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo wpenon_format_decimal($decke->daemmung()); ?> cm</td>
					<td><?php echo wpenon_format_decimal($decke->fx()); ?></td>
					<td><?php echo wpenon_format_decimal($decke->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>

	<?php endif; ?>

	<h4>Transmission</h4>


	<p><?php printf(__('Transmissionswärmekoeffizient Bauteile ht: %s', 'wpenon'), wpenon_format_decimal($gebaeude->bauteile()->ht())); ?></p>
	<p><?php printf(__('Transmissionswärmekoeffizient Fenster hw: %s', 'wpenon'), wpenon_format_decimal($gebaeude->bauteile()->hw())); ?></p>
	<p><?php printf(__('Wärmebrückenzuschlag (ht_wb): %s', 'wpenon'), wpenon_format_decimal($gebaeude->ht_wb())); ?></p>
	<p><?php printf(__('Transmissionswärmekoeffizient Gesamt ht<sub>ges</sub>: %s', 'wpenon'), wpenon_format_decimal($gebaeude->ht_ges())); ?></p>
	<p><?php printf(__('Wärmetransferkoeffizient des Gebäudes. (h ges): %s', 'wpenon'), wpenon_format_decimal($gebaeude->h_ges())); ?></p>
	<p><?php printf(__('Tau: %s', 'wpenon'), wpenon_format_decimal($gebaeude->tau())); ?></p>
	<p><?php printf(__('Maximaler Wärmestrom Q: %s', 'wpenon'), wpenon_format_decimal($gebaeude->q())); ?></p>

	<h4>Lüftung</h4>

	<p><?php printf(__('Lueftungssystem: %s', 'wpenon'), $gebaeude->lueftung()->lueftungssystem()); ?></p>
	<p><?php printf(__('Bedarfsgeführt: %s', 'wpenon'), $gebaeude->lueftung()->ist_bedarfsgefuehrt() ? 'Ja' : 'Nein'); ?></p>
	<p><?php printf(__('Gebäudedichtheit: %s', 'wpenon'), $gebaeude->lueftung()->gebaeudedichtheit()); ?></p>

	<?php if (method_exists($gebaeude->lueftung(), 'wirkungsgrad')) : ?>
		<p><?php printf(__('Wirkungsgrad: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->wirkungsgrad())); ?></p>
	<?php endif; ?>

	<p><?php printf(__('Luftechselvolumen h<sub>v</sub>: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->hv())); ?></p>
	<p><?php printf(__('Maximale Heizlast h<sub>max</sub>: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->h_max())); ?></p>
	<p><?php printf(__('Maximale Heizlast spezifisch h<sub>max,spez</sub>: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->h_max_spezifisch())); ?></p>

	<h4>Luftwechsel Werte</h4>
	<p><?php printf(__('Luftwechselrate n: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->luftwechsel()->n())); ?></p>
	<p><?php printf(__('Gesamtluftwechselrate n<sub>0</sub>: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->luftwechsel()->n0())); ?></p>
	<p><?php printf(__('Korrekturfakror f<sub>win,1</sub>: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->luftwechsel()->fwin1())); ?></p>
	<p><?php printf(__('Korrekturfakror f<sub>win,2</sub>: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->luftwechsel()->fwin2())); ?></p>
	<p><?php printf(__('n_anl: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->luftwechsel()->n_anl())); ?></p>
	<p><?php printf(__('n_wrg: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->luftwechsel()->n_wrg())); ?></p>

	<h2>Bilanzierung</h2>

	<h4>Interne Wärmequellen</h4>
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
				<td><?php echo wpenon_format_decimal($gebaeude->qi_prozesse_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->qi_wasser_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->qi_solar_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->qi_heizung_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->qi_monat($monat->slug())); ?></td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td><b>Gesamt</b></td>
			<td><?php echo wpenon_format_decimal($gebaeude->qi_prozesse()); ?></td>
			<td><?php echo wpenon_format_decimal($gebaeude->qi_wasser()); ?></td>
			<td><?php echo wpenon_format_decimal($gebaeude->qi_solar()); ?></td>
			<td><?php echo wpenon_format_decimal($gebaeude->qi_heizung()); ?></td>
			<td><?php echo wpenon_format_decimal($gebaeude->qi()); ?></td>
		</tr>
	</table>

	<h4>fum</h4>
	<table>
		<tr>
			<th>Monat</th>
			<th>fum</th>
		</tr>
		<?php foreach ($jahr->monate() as $monat) : ?>
			<tr>
				<td><?php echo $monat->name(); ?></td>
				<td><?php echo wpenon_format_decimal(fum($monat->slug())); ?></td>
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
				<td><?php echo wpenon_format_decimal($gebaeude->psh_sink_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->ph_sink_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->ph_source_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->QWB_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->qh_monat($monat->slug())); ?></td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td><b>Gesamt</b></td>
			<td></td>
			<td></td>
			<td></td>
			<td><?php echo wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->QWB()); ?></td>
			<td><?php echo wpenon_format_decimal($gebaeude->qh()); ?></td>
		</tr>
	</table>

	<h4>Jahr Gesamt</h4>
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
			<td><?php echo wpenon_format_decimal($gebaeude->ßhma()); ?></td>
			<td><?php echo wpenon_format_decimal($gebaeude->thm()); ?></td>
			<td><?php echo wpenon_format_decimal($gebaeude->ith_rl()); ?></td>
			<td><?php echo wpenon_format_decimal($gebaeude->qi()); ?></td>
			<td><?php echo wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->QWB()); ?></td>
			<td><?php echo wpenon_format_decimal($gebaeude->qh()); ?></td>
		</tr>
	</table>

	<h4>Korrekturfaktoren und weitere Werte</h4>

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
				<td><?php echo wpenon_format_decimal($gebaeude->ph_source_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->ym_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->nm_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->flna_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->trl_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->ith_rl_monat($monat->slug())); ?></td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td><b>Gesamt</b></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td><?php echo wpenon_format_decimal($gebaeude->ith_rl()); ?></td>
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
				<td><?php echo wpenon_format_decimal($gebaeude->k_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->bilanz_innentemperatur()->θih_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->mittlere_belastung()->ßem1($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->ßhm_monat($monat->slug())); ?></td>
				<td><?php echo wpenon_format_decimal($gebaeude->thm_monat($monat->slug())); ?></td>
			</tr>
		<?php endforeach; ?>
		<tr>
			<td><b>Gesamt</b></td>
			<td></td>
			<td></td>
			<td><?php echo $gebaeude->mittlere_belastung()->ßemMax(); ?> (ßemMax)</td>
			<td><?php echo wpenon_format_decimal($gebaeude->ßhma()); ?> (ßhma)</td>
			<td><?php echo wpenon_format_decimal($gebaeude->thm()); ?></td>
		</tr>
	</table>

	<h3>Heizsystem</h3>

	<?php $i = 1; ?>
	<?php foreach ($gebaeude->heizsystem()->heizungsanlagen()->alle() as $heizungsanlage) : ?>
		<h4><?php echo 'Heizungsanlage ' . $i++; ?></h4>
		<?php if ($heizungsanlage->kategorie() === 'konventioneller_kessel') : ?>
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
				<?php if (method_exists($heizungsanlage, 'fbaujahr')) : ?>
					<tr>
						<td>fbaujahr</td>
						<td><?php echo $heizungsanlage->fbaujahr(); ?></td>
					</tr>
				<?php endif; ?>
				<tr>
					<td>ßhg</td>
					<td><?php echo $heizungsanlage->ßhg(); ?></td>
				</tr>
				<tr>
					<td>fegt</td>
					<td><?php echo $heizungsanlage->fegt(); ?></td>
				</tr>
				<tr>
					<td>ehg</td>
					<td><?php echo $heizungsanlage->ehg(); ?></td>
				</tr>
				<tr>
					<td>ewg0</td>
					<td><?php echo $heizungsanlage->ewg0(); ?></td>
				</tr>
				<tr>
					<td>ewg</td>
					<td><?php echo $heizungsanlage->ewg(); ?></td>
				</tr>
				<tr>
					<td>fco2 (CO2 Emissiomnsfaktor)</td>
					<td><?php echo $heizungsanlage->fco2(); ?></td>
				</tr>
				<tr>
					<td>fp (Primärenergiefaktor)</td>
					<td><?php echo $heizungsanlage->fp(); ?></td>
				</tr>
			</table>
			<h5>Hilfsenergie</h5>
			<table>
				<tr>
					<td>tpwn0</td>
					<td><?php echo $heizungsanlage->twpn0(); ?></td>
				</tr>
				<tr>
					<td>tpwn</td>
					<td><?php echo $heizungsanlage->twpn(); ?></td>
				</tr>
				<tr>
					<td>fphgaux</td>
					<td><?php echo $heizungsanlage->fphgaux(); ?></td>
				</tr>
				<tr>
					<td>Phgaux</td>
					<td><?php echo $heizungsanlage->Phgaux(); ?></td>
				</tr>
				<tr>
					<td>fpwgaux</td>
					<td><?php echo $heizungsanlage->fpwgaux(); ?></td>
				</tr>
				<tr>
					<td>Pwgaux</td>
					<td><?php echo $heizungsanlage->Pwgaux(); ?></td>
				</tr>
				<tr>
					<td>Whg</td>
					<td><?php echo $heizungsanlage->Whg(); ?></td>
				</tr>
				<tr>
					<td>Wwg</td>
					<td><?php echo $heizungsanlage->Wwg(); ?></td>
				</tr>
			</table>

			<h5>Weitere Werte</h5>
			<table>
				<tr>
					<td>MCO2</td>
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
					<td>fco2 (CO2 Emissiomnsfaktor)</td>
					<td><?php echo $heizungsanlage->fco2(); ?></td>
				</tr>
				<tr>
					<td>fp (Primärenergiefaktor)</td>
					<td><?php echo $heizungsanlage->fp(); ?></td>
				</tr>
			</table>

			<h5>Hilfsenergie</h5>

			<table>
				<tr>
					<td>Whg</td>
					<td><?php echo $heizungsanlage->Whg(); ?></td>
				</tr>
				<tr>
					<td>Wwg</td>
					<td><?php echo $heizungsanlage->Wwg(); ?></td>
				</tr>
			</table>

			<h5>Weitere Werte</h5>
			<table>
				<tr>
					<td>MCO2</td>
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
					<td>fco2 (CO2 Emissiomnsfaktor)</td>
					<td><?php echo $heizungsanlage->fco2(); ?></td>
				</tr>
				<tr>
					<td>fp (Primärenergiefaktor)</td>
					<td><?php echo $heizungsanlage->fp(); ?></td>
				</tr>
			</table>

			<h5>Hilfsenergie</h5>

			<table>
				<tr>
					<td>Whg</td>
					<td><?php echo $heizungsanlage->Whg(); ?></td>
				</tr>
				<tr>
					<td>Wwg</td>
					<td><?php echo $heizungsanlage->Wwg(); ?></td>
				</tr>
			</table>

			<h5>Weitere Werte</h5>
			<table>
				<tr>
					<td>MCO2</td>
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
					<td>fco2 (CO2 Emissiomnsfaktor)</td>
					<td><?php echo $heizungsanlage->fco2(); ?></td>
				</tr>
				<tr>
					<td>fp (Primärenergiefaktor)</td>
					<td><?php echo $heizungsanlage->fp(); ?></td>
				</tr>
			</table>

			<h5>Hilfsenergie</h5>

			<table>
				<tr>
					<td>Whg</td>
					<td><?php echo $heizungsanlage->Whg(); ?></td>
				</tr>
				<tr>
					<td>Wwg</td>
					<td><?php echo $heizungsanlage->Wwg(); ?></td>
				</tr>
			</table>

			<h5>Weitere Werte</h5>
			<table>
				<tr>
					<td>MCO2</td>
					<td><?php echo $heizungsanlage->MCO2(); ?></td>
				</tr>
			</table>
		<?php endif; ?>

	<?php endforeach; ?>

	<h4>Übergabesystem</h4>
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
				<td><?php echo wpenon_format_decimal($uebergabesystem->ehce()); ?></td>
			<?php endforeach; ?>
	</table>

	<h4>Heizsystem</h4>

	<?php if (method_exists($gebaeude, 'fa_h')) : ?>
		<p><?php printf(__('Nutzbare Wärme fa<sub>h</sub>: %s', 'wpenon'), $gebaeude->fa_h()); ?></p>
	<?php endif; ?>
	<p><?php printf(__('Mittlere Belastung bei Übergabe der Heizung (ßhce): %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->ßhce())); ?></p>
	<p><?php printf(__('Flächenbezogene leistung der Übergabe der Heizung (qhce): %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->qhce())); ?></p>
	<p><?php printf(__('ßhd: %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->ßhd())); ?></p>
	<?php if (method_exists($gebaeude->heizsystem(), 'f_hydr')) : ?>
		<p><?php printf(__('fhydr: %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->f_hydr())); ?></p>
	<?php endif; ?>
	<p><?php printf(__('fßd: %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->fßd())); ?></p>
	<p><?php printf(__('ehd0: %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->ehd0())); ?></p>
	<p><?php printf(__('ehd1: %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->ehd1())); ?></p>
	<p><?php printf(__('ehd: %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->ehd())); ?></p>
	<p><?php printf(__('ehd korrektur: %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->ehd_korrektur())); ?></p>
	<p><?php printf(__('ßhs: %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->ßhs())); ?></p>

	<p><?php printf(__('Nennleistung Pufferspeicher (pwn): %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->pwn())); ?></p>
	<p><?php printf(__('(pn): %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->pn())); ?></p>

	<?php if ($gebaeude->heizsystem()->pufferspeicher_vorhanden()) : ?>
		<h4>Pufferspeicher</h4>
		<p><?php printf(__('Korrekturfaktor mittlere Belastung des Pufferspeichers fßhs: %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->pufferspeicher()->fßhs())); ?></p>
		<p><?php printf(__('Mittlere Belastung für Speicherung ßhs: %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->ßhs())); ?></p>
		<p><?php printf(__('Korrekturfaktor für beliebige mittlere Berlastung und Laufzeit der Heizung fhs: %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->pufferspeicher()->fhs())); ?></p>
		<p><?php printf(__('Berechnetes Volumen: %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->pufferspeicher()->volumen())); ?></p>
		<p><?php printf(__('Volumen Pufferspeicher vs1: %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->pufferspeicher()->vs1())); ?></p>
		<p><?php printf(__('Volumen Pufferspeicher vs2: %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->pufferspeicher()->vs2())); ?></p>
		<p><?php printf(__('Wärmeabgabe Pufferspeicher (Qhs0Vs1): %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->pufferspeicher()->Qhs0Vs1())); ?></p>
		<p><?php printf(__('Wärmeabgabe Pufferspeicher (Qhs0Vs2): %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->pufferspeicher()->Qhs0Vs2())); ?></p>
		<p><?php printf(__('Wärmeabgabe Pufferspeicher Gesamt (Qhs): %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->pufferspeicher()->Qhs())); ?></p>
		<p><?php printf(__('Aufwandszahl für Pufferspeicher (ehs): %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->ehs())); ?></p>
	<?php else : ?>
		<p><?php printf(__('Aufwandszahl für Pufferspeicher (ehs):  %s', 'wpenon'), wpenon_format_decimal($gebaeude->heizsystem()->ehs())); ?></p>
	<?php endif; ?>

	<h4>Trinkwarmwasseranlage</h4>

	<?php if (method_exists($gebaeude, 'fa_w')) : ?>
		<p><?php printf(__('Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen fa<sub>w</sub>: %s', 'wpenon'), wpenon_format_decimal($gebaeude->fa_w())); ?></p>
	<?php endif; ?>

	<p><?php printf(__('Nutzwärmebedarf für Trinkwasser qwb: %s kWh/(ma)', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->nutzwaermebedarf_trinkwasser())); ?></p>
	<p><?php printf(__('Q<sub>w,b</sub>: %s kWh', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->QWB())); ?></p>
	<p><?php printf(__('Interne Wärmequelle infolge von Warmwasser Qi<sub>w</sub>: %s', 'wpenon'), wpenon_format_decimal($gebaeude->qi_wasser())); ?></p>
	<p><?php printf(__('Jährlicher Nutzwaermebedarf für Trinkwasser (qwb): %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->nutzwaermebedarf_trinkwasser())); ?></p>
	<p><?php printf(__('Berechnung des monatlichen Wärmebedarfs für Warmwasser(QWB) für ein Jahr: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->QWB())); ?></p>

	<h4>Aufwandszahlen Trinkwarmwasser</h4>

	<p><?php printf(__('Zwischenwert für die Berechnung von ewd (ewce): %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->ewce())); ?></p>
	<p><?php printf(__('Zwischenwert für die Berechnung von ewd (ewd0): %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->ewd0())); ?></p>
	<p><?php printf(__('Aufwandszahlen für die Verteilung von Trinkwarmwasser (ewd): %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->ewd())); ?></p>
	<p><?php printf(__('Korrekturfaktor (fwb): %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->fwb())); ?></p>
	<p><?php printf(__('Volumen Speicher 1 in Litern. (Vs01): %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Vs01())); ?></p>
	<p><?php printf(__('Volumen Speicher 2 in Litern. (Vs02): %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Vs02())); ?></p>
	<p><?php printf(__('Volumen Speicher 3 in Litern. (Vs03): %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Vs03())); ?></p>
	<p><?php printf(__('Volumen Speicher Gesamt in Litern. (Vs0): %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Vs0())); ?></p>
	<p><?php printf(__('Berechnung von Vsw1: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Vsw1())); ?></p>
	<p><?php printf(__('Berechnung von Vsw2: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Vsw2())); ?></p>
	<p><?php printf(__('Berechnung von Qws01: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Qws01())); ?></p>
	<p><?php printf(__('Berechnung von Qws02: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Qws02())); ?></p>

	<?php if ($gebaeude->trinkwarmwasseranlage()->solarthermie_vorhanden()) : ?>
		<h4>Solarthermie</h4>
		<p><?php printf(__('Berechnung von Vsaux0: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Vsaux0())); ?></p>
		<p><?php printf(__('Berechnung von Vssol0: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Vssol0())); ?></p>
		<p><?php printf(__('Berechnung von Ac0: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Ac0())); ?></p>
		<p><?php printf(__('Berechnung von Qwsola0: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Qwsola0())); ?></p>
		<br />
		<p><?php printf(__('Berechnung von Vsaux: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Vsaux())); ?></p>
		<p><?php printf(__('Berechnung von Vssol: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Vssol())); ?></p>
		<p><?php printf(__('Berechnung von Ac: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Ac())); ?></p>
		<p><?php printf(__('Berechnung von fAc: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->fAc())); ?></p>
		<p><?php printf(__('Berechnung von fQsola: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->fQsola())); ?></p>
		<p><?php printf(__('Berechnung von Qwsola: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Qwsola())); ?></p>
	<?php endif; ?>

	<br>

	<p><?php printf(__('Berechnung von Qws: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->Qws())); ?></p>
	<p><?php printf(__('Berechnung von ews: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->ews())); ?></p>
	<p><?php printf(__('Berechnung von keew: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->keew())); ?></p>
	<p><?php printf(__('Berechnung von keeh: %s', 'wpenon'), wpenon_format_decimal($gebaeude->trinkwarmwasseranlage()->keeh())); ?></p>

	<?php if ($gebaeude->photovoltaik_anlage_vorhanden()) : ?>
		<h4>Photovoltaik</h4>
		<p><?php printf(__('Richtung: %s', 'wpenon'), wpenon_format_decimal($gebaeude->photovoltaik_anlage()->richtung())); ?></p>
		<p><?php printf(__('Neigung: %s', 'wpenon'), wpenon_format_decimal($gebaeude->photovoltaik_anlage()->neigung())); ?></p>
		<p><?php printf(__('Fläche: %s', 'wpenon'), wpenon_format_decimal($gebaeude->photovoltaik_anlage()->flaeche())); ?></p>
		<p><?php printf(__('Baujahr: %s', 'wpenon'), wpenon_format_decimal($gebaeude->photovoltaik_anlage()->baujahr())); ?></p>
		<p><?php printf(__('QfprodPV: %s', 'wpenon'), wpenon_format_decimal($gebaeude->photovoltaik_anlage()->QfprodPV())); ?></p>
		<p><?php printf(__('WfPVHP: %s', 'wpenon'), wpenon_format_decimal($gebaeude->photovoltaik_anlage()->WfPVHP())); ?></p>
		<p><?php printf(__('Pvans: %s', 'wpenon'), wpenon_format_decimal($gebaeude->photovoltaik_anlage()->Pvans($gebaeude->Qfstrom()))); ?></p>
	<?php endif; ?>

	<h3>Hilfsenergie</h3>

	<p><?php printf(__('pg: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->pg())); ?></p>

	<h4>Bestimmung der Hilfsenergie_Übergabe Wce</h4>

	<p><?php printf(__('WHce: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->WHce())); ?></p>
	<p><?php printf(__('Wc: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Wc())); ?></p>
	<p><?php printf(__('Wrvce: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Wrvce())); ?></p>
	<p><?php printf(__('Wwce: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Wwce())); ?></p>
	<p><?php printf(__('WsolPumpece: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->WsolPumpece())); ?></p>
	<p><?php printf(__('WsolPumpeg: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->WsolPumpeg())); ?></p>

	<h4>Bestimmung der Hilfsenergie_Verteilung Wd</h4>

	<p><?php printf(__('fgeoHzg: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->fgeoHzg())); ?></p>
	<p><?php printf(__('fblHzg: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->fblHzg())); ?></p>
	<p><?php printf(__('fgeoTWW: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->fgeoTWW())); ?></p>
	<p><?php printf(__('fblTWW: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->fblTWW())); ?></p>
	<p><?php printf(__('LcharHzg: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->LcharHzg())); ?></p>
	<p><?php printf(__('LcharTWW: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->LcharTWW())); ?></p>
	<p><?php printf(__('BcarHzg: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->BcarHzg())); ?></p>
	<p><?php printf(__('BcarWW: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->BcarWW())); ?></p>
	<p><?php printf(__('LmaxHzg: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->LmaxHzg())); ?></p>
	<p><?php printf(__('LmaxTWW: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->LmaxTWW())); ?></p>

	<h4>Berechnung der Hilfsenergie_Verteilung Heizung Whd , Rohrnetzberechnung</h4>

	<p><?php printf(__('TERMp: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->TERMp())); ?></p>
	<p><?php printf(__('Vstr: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Vstr())); ?></p>
	<p><?php printf(__('PhydrHzg: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->PhydrHzg())); ?></p>
	<p><?php printf(__('fe: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->fe())); ?></p>
	<p><?php printf(__('TERMpumpe: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->TERMpumpe())); ?></p>
	<p><?php printf(__('fint: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->fint())); ?></p>

	<h4>Berechnung der Hilfsenergie für Heizsysteme</h4>

	<p><?php printf(__('Wrvd: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Wrvd())); ?></p>
	<p><?php printf(__('Lv: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Lv())); ?></p>
	<p><?php printf(__('Ls: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Ls())); ?></p>
	<p><?php printf(__('Pwda: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Pwda())); ?></p>
	<p><?php printf(__('PhydrTWW: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->PhydrTWW())); ?></p>
	<p><?php printf(__('z: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->z())); ?></p>
	<p><?php printf(__('Wwd: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Wwd())); ?></p>
	<p><?php printf(__('WsolPumped: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->WsolPumped())); ?></p>
	<p><?php printf(__('Whs: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Whs())); ?></p>
	<p><?php printf(__('tpu: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->tpu())); ?></p>
	<p><?php printf(__('Vws: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Vws())); ?></p>
	<p><?php printf(__('Wws0: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Wws0())); ?></p>
	<p><?php printf(__('Wws: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Wws())); ?></p>
	<p><?php printf(__('Whd: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Whd())); ?></p>

	<h4>Berechnung der Hilfsenergie für Lüftung</h4>

	<p><?php printf(__('fbaujahr: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->fbaujahr())); ?></p>
	<p><?php printf(__('fgr_exch: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->fgr_exch())); ?></p>
	<p><?php printf(__('fsup_decr: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->fsup_decr())); ?></p>
	<p><?php printf(__('fbetrieb: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->fbetrieb())); ?></p>
	<?php if (method_exists($gebaeude->lueftung(), 'strom_art')) : ?>
		<p><?php printf(__('Strom Art: %s', 'wpenon'), $gebaeude->lueftung()->strom_art()); ?></p>
	<?php endif; ?>
	<p><?php printf(__('Wfan0: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->Wfan0())); ?></p>
	<p><?php printf(__('Wc: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->Wc())); ?></p>
	<p><?php printf(__('Wpre_h: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->Wpre_h())); ?></p>
	<p><?php printf(__('fsystem: %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->fsystem())); ?></p>
	<p><?php printf(__('Wrvg (Gesamt): %s', 'wpenon'), wpenon_format_decimal($gebaeude->lueftung()->Wrvg())); ?></p>

	<h4>Berechnung der Hilfsenergie für Solarthermie</h4>
	<p><?php printf(__('WsolPumpece: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->WsolPumpece())); ?></p>
	<p><?php printf(__('WsolPumped: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->WsolPumped())); ?></p>
	<p><?php printf(__('WsolPumpes: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->WsolPumpes())); ?></p>
	<p><?php printf(__('WsolPumpe: %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->WsolPumpe())); ?></p>

	<h4>Hilfsenergie Endergebnisse</h4>
	<p><?php printf(__('Wh (Hilfsenergie Heizsystem): %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Wh())); ?></p>
	<p><?php printf(__('Ww (Hilfsenergie Warmwasser): %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Ww())); ?></p>
	<p><?php printf(__('Wrv (Hilfsenergie Lüftung): %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Wrv())); ?></p>
	<p><?php printf(__('WsolPumpe (Hilfsenergie Solarpumpe): %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->WsolPumpe())); ?></p>
	<p><?php printf(__('Wges (Hilfsenergie Gesamt): %s', 'wpenon'), wpenon_format_decimal($gebaeude->hilfsenergie()->Wges())); ?></p>

	<h3>Endenergie</h3>

	<p><?php printf(__('Qfhges: %s', 'wpenon'), wpenon_format_decimal($gebaeude->Qfhges())); ?></p>
	<p><?php printf(__('Qfwges: %s', 'wpenon'), wpenon_format_decimal($gebaeude->Qfwges())); ?></p>
	<p><?php printf(__('Qfgesamt: %s', 'wpenon'), wpenon_format_decimal($gebaeude->Qfgesamt())); ?></p>
	<p><?php printf(__('Qpges: %s', 'wpenon'), wpenon_format_decimal($gebaeude->Qpges())); ?></p>
	<p><?php printf(__('Qfstrom: %s', 'wpenon'), wpenon_format_decimal($gebaeude->Qfstrom())); ?></p>
	<p><?php printf(__('Qf (Endenergie): %s', 'wpenon'), wpenon_format_decimal($gebaeude->Qf())); ?></p>

	<h4>Vergleichswerte</h4>

	<p><?php printf(__('Qp (Primärenergie): %s', 'wpenon'), wpenon_format_decimal($gebaeude->Qp())); ?></p>
	<p><?php printf(__('Ht\': %s', 'wpenon'), wpenon_format_decimal($gebaeude->ht_strich())); ?></p>

	<h3>CO2</h3>
	<p><?php printf(__('CO2 Emissionen in Kg: %s', 'wpenon'), wpenon_format_decimal($gebaeude->MCO2())); ?></p>
	<p><?php printf(__('CO2 Emissionen in Kg/m2: %s', 'wpenon'), wpenon_format_decimal($gebaeude->MCO2a())); ?></p>

</div>
<?php if (($anlass === 'modernisierung' || $anlass === 'sonstiges') && isset($referenzgebaeude)) : ?>

	<div class="calculation-details referenzgebaeude">
		<h2>Referenzgebaeude</h2>
		<p><?php printf(__('Baujahr: %s;', 'wpenon'), $referenzgebaeude->baujahr()); ?></p>
		<p><?php printf(__('Hüllvolumen V<sub>e</sub>: %s m&sup3;', 'wpenon'), wpenon_format_decimal($referenzgebaeude->huellvolumen())); ?></p>
		<p><?php printf(__('Hüllvolumen (netto): %s m&sup3;', 'wpenon'), wpenon_format_decimal($referenzgebaeude->huellvolumen_netto())); ?></p>
		<p><?php printf(__('Hüllfäche<sub>e</sub>: %s m&sup2;', 'wpenon'), wpenon_format_decimal($referenzgebaeude->huellflaeche())); ?></p>
		<p><?php printf(__('ave Verhältnis: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->ave_verhaeltnis())); ?></p>
		<p><?php printf(__('Nutzfläche A<sub>N</sub>: %s m&sup2;', 'wpenon'), wpenon_format_decimal($referenzgebaeude->nutzflaeche())); ?></p>
		<p><?php printf(__('Anzahl der Geschosse: %s', 'wpenon'), $referenzgebaeude->geschossanzahl()); ?></p>
		<p><?php printf(__('Geschosshöhe: %s m', 'wpenon'), wpenon_format_decimal($referenzgebaeude->geschosshoehe())); ?></p>
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
					<td><?php echo wpenon_format_decimal($referenzgebaeude->grundriss()->wand_laenge($wand)); ?> m</td>
					<td><?php echo $referenzgebaeude->grundriss()->wand_himmelsrichtung($wand); ?></td>
				</tr>
			<?php endforeach; ?>
		</table>

		<h2>Bauteile</h2>

		<h3>Wände</h3>
		<table>
			<tr>
				<th>Bauteil</th>
				<th>Fläche</th>
				<th>U-Wert</th>
				<th>Dämmung</th>
				<th>Fx Faktor</th>
				<th>Transmissionswärme<br />-koeffizient ht</th>
			</tr>
			<?php foreach ($referenzgebaeude->bauteile()->waende()->alle() as $wand) : ?>
				<tr>
					<td><?php echo $wand->name(); ?></td>
					<td><?php echo wpenon_format_decimal($wand->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo wpenon_format_decimal($wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo wpenon_format_decimal($wand->daemmung()); ?> cm</td>
					<td><?php echo wpenon_format_decimal($wand->fx()); ?></td>
					<td><?php echo wpenon_format_decimal($wand->ht()); ?> W/K</td>
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
				<th>Transmissionswärme<br />-koeffizient ht</th>
			</tr>
			<?php foreach ($referenzgebaeude->bauteile()->filter('Fenster')->alle() as $fenster) : ?>
				<tr>
					<td><?php echo $fenster->name(); ?></td>
					<td><?php echo wpenon_format_decimal($fenster->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo wpenon_format_decimal($fenster->gwert()); ?></td>
					<td><?php echo wpenon_format_decimal($fenster->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo wpenon_format_decimal($fenster->fx()); ?></td>
					<td><?php echo wpenon_format_decimal($fenster->ht()); ?> W/K</td>
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
					<th>Transmissionswärme<br />-koeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Heizkoerpernische')->alle() as $heizkoerpernische) : ?>
					<tr>
						<td><?php echo $heizkoerpernische->name(); ?></td>
						<td><?php echo wpenon_format_decimal($heizkoerpernische->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo wpenon_format_decimal($heizkoerpernische->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo wpenon_format_decimal($heizkoerpernische->fx()); ?></td>
						<td><?php echo wpenon_format_decimal($heizkoerpernische->ht()); ?> W/K</td>
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
					<th>Transmissionswärme<br />-koeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Rolladenkasten')->alle() as $rolladenkaesten) : ?>
					<tr>
						<td><?php echo $rolladenkaesten->name(); ?></td>
						<td><?php echo wpenon_format_decimal($rolladenkaesten->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo wpenon_format_decimal($rolladenkaesten->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo wpenon_format_decimal($rolladenkaesten->fx()); ?></td>
						<td><?php echo wpenon_format_decimal($rolladenkaesten->ht()); ?> W/K</td>
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
					<th>Transmissionswärme<br />-koeffizient ht</th>
				</tr>
				<tr>
					<td><?php echo $referenzgebaeude->dach()->name(); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->dach()->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->dach()->hoehe()); ?> m</td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->dach()->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->dach()->daemmung()); ?> cm</td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->dach()->fx()); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->dach()->ht()); ?> W/K</td>
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
					<th>Transmissionswärme<br />-koeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Decke')->alle() as $decke) : ?>
					<tr>
						<td><?php echo $decke->name(); ?></td>
						<td><?php echo wpenon_format_decimal($decke->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo wpenon_format_decimal($decke->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo wpenon_format_decimal($decke->daemmung()); ?> cm</td>
						<td><?php echo wpenon_format_decimal($decke->fx()); ?></td>
						<td><?php echo wpenon_format_decimal($decke->ht()); ?> W/K</td>
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
				<th>Transmissionswärme<br />-koeffizient ht</th>
			</tr>
			<?php foreach ($referenzgebaeude->bauteile()->filter('Boden')->alle() as $boden) : ?>
				<tr>
					<td><?php echo $boden->name(); ?></td>
					<td><?php echo wpenon_format_decimal($boden->flaeche()); ?> m<sup>2</sup></td>
					<td><?php echo wpenon_format_decimal($boden->uwert()); ?> W/(m<sup>2</sup>K)</td>
					<td><?php echo wpenon_format_decimal($boden->daemmung()); ?> cm</td>
					<td><?php echo wpenon_format_decimal($boden->fx()); ?></td>
					<td><?php echo wpenon_format_decimal($boden->ht()); ?> W/K</td>
				</tr>
			<?php endforeach; ?>
		</table>


		<?php if ($referenzgebaeude->keller_vorhanden()) : ?>
			<h3>Keller</h3>
			<p class="lead"><?php printf(__('Unterkellerung: %s;', 'wpenon'), wpenon_format_decimal($referenzgebaeude->keller()->anteil())); ?></p>
			<p class="lead"><?php printf(__('Kellerfläche A<sub>K</sub>: %s m&sup2;', 'wpenon'), wpenon_format_decimal($referenzgebaeude->keller()->boden_flaeche())); ?></p>
			<p class="lead"><?php printf(__('Kellerwandlänge U<sub>K</sub>: %s m;', 'wpenon'), wpenon_format_decimal($referenzgebaeude->keller()->wand_laenge())); ?></p>
			<p class="lead"><?php printf(__('Kellerwandhöhe H<sub>K</sub>: %s m;', 'wpenon'), wpenon_format_decimal($referenzgebaeude->keller()->wand_hoehe())); ?></p>
			<p class="lead"><?php printf(__('Kellervolumen V<sub>K</sub>: %s m&sup3;', 'wpenon'), wpenon_format_decimal($referenzgebaeude->keller()->volumen())); ?></p>
			<table>
				<tr>
					<th>Wand</th>
					<th>Fläche</th>
					<th>U-Wert</th>
					<th>Dämmung</th>
					<th>Fx Faktor</th>
					<th>Transmissionswärme<br />-koeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Kellerwand')->alle() as $wand) : ?>
					<tr>
						<td><?php echo $wand->name(); ?></td>
						<td><?php echo wpenon_format_decimal($wand->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo wpenon_format_decimal($wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo wpenon_format_decimal($wand->daemmung()); ?> cm</td>
						<td><?php echo wpenon_format_decimal($wand->fx()); ?></td>
						<td><?php echo wpenon_format_decimal($wand->ht()); ?> W/K</td>
					</tr>
				<?php endforeach; ?>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Kellerboden')->alle() as $wand) : ?>
					<tr>
						<td><?php echo $wand->name(); ?></td>
						<td><?php echo wpenon_format_decimal($wand->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo wpenon_format_decimal($wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo wpenon_format_decimal($wand->daemmung()); ?> cm</td>
						<td><?php echo wpenon_format_decimal($wand->fx()); ?></td>
						<td><?php echo wpenon_format_decimal($wand->ht()); ?> W/K</td>
					</tr>
				<?php endforeach; ?>
			</table>
		<?php endif; ?>

		<?php if ($referenzgebaeude->anbau_vorhanden()) : ?>
			<h3>Anbau</h3>
			<p class="lead"><?php printf(__('Anbau Fläche: %s m&sup2; ', 'wpenon'), wpenon_format_decimal($referenzgebaeude->anbau()->grundriss()->flaeche())); ?></p>
			<p class="lead"><?php printf(__('Anbau Volumen: %s m&sup2; ', 'wpenon'), wpenon_format_decimal($referenzgebaeude->anbau()->volumen())); ?></p>
			<table>
				<tr>
					<th>Wand</th>
					<th>Fläche</th>
					<th>U-Wert</th>
					<th>Dämmung</th>
					<th>Fx Faktor</th>
					<th>Transmissionswärme<br />-koeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Anbauwand')->alle() as $wand) : ?>
					<tr>
						<td><?php echo $wand->name(); ?></td>
						<td><?php echo wpenon_format_decimal($wand->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo wpenon_format_decimal($wand->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo wpenon_format_decimal($wand->daemmung()); ?> cm</td>
						<td><?php echo wpenon_format_decimal($wand->fx()); ?></td>
						<td><?php echo wpenon_format_decimal($wand->ht()); ?> W/K</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<table>
				<tr>
					<th>Fenster</th>
					<th>Fläche</th>
					<th>U-Wert</th>
					<th>Fx Faktor</th>
					<th>Transmissionswärme<br />-koeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Anbaufenster')->alle() as $anbaufenster) : ?>
					<tr>
						<td><?php echo $anbaufenster->name(); ?></td>
						<td><?php echo wpenon_format_decimal($anbaufenster->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo wpenon_format_decimal($anbaufenster->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo wpenon_format_decimal($anbaufenster->fx()); ?></td>
						<td><?php echo wpenon_format_decimal($anbaufenster->ht()); ?> W/K</td>
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
					<th>Transmissionswärme<br />-koeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Anbauboden')->alle() as $boeden) : ?>
					<tr>
						<td><?php echo $boeden->name(); ?></td>
						<td><?php echo wpenon_format_decimal($boeden->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo wpenon_format_decimal($boeden->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo wpenon_format_decimal($boeden->daemmung()); ?> cm</td>
						<td><?php echo wpenon_format_decimal($boeden->fx()); ?></td>
						<td><?php echo wpenon_format_decimal($boeden->ht()); ?> W/K</td>
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
					<th>Transmissionswärme<br />-koeffizient ht</th>
				</tr>
				<?php foreach ($referenzgebaeude->bauteile()->filter('Anbaudecke')->alle() as $decke) : ?>
					<tr>
						<td><?php echo $boeden->name(); ?></td>
						<td><?php echo wpenon_format_decimal($decke->flaeche()); ?> m<sup>2</sup></td>
						<td><?php echo wpenon_format_decimal($decke->uwert()); ?> W/(m<sup>2</sup>K)</td>
						<td><?php echo wpenon_format_decimal($decke->daemmung()); ?> cm</td>
						<td><?php echo wpenon_format_decimal($decke->fx()); ?></td>
						<td><?php echo wpenon_format_decimal($decke->ht()); ?> W/K</td>
					</tr>
				<?php endforeach; ?>
			</table>

		<?php endif; ?>

		<h3>Transmission</h3>


		<p><?php printf(__('Transmissionswärmekoeffizient Bauteile ht: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->bauteile()->ht())); ?></p>
		<p><?php printf(__('Transmissionswärmekoeffizient Fenster hw: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->bauteile()->hw())); ?></p>
		<p><?php printf(__('Wärmebrückenzuschlag (ht_wb): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->ht_wb())); ?></p>
		<p><?php printf(__('Transmissionswärmekoeffizient Gesamt ht<sub>ges</sub>: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->ht_ges())); ?></p>
		<p><?php printf(__('Wärmetransferkoeffizient des Gebäudes. (h ges): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->h_ges())); ?></p>
		<p><?php printf(__('Tau: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->tau())); ?></p>
		<p><?php printf(__('Maximaler Wärmestrom Q: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->q())); ?></p>

		<h3>Lüftung</h3>

		<p><?php printf(__('Lueftungssystem: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->lueftungssystem())); ?></p>
		<p><?php printf(__('Bedarfsgeführt: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->ist_bedarfsgefuehrt() ? 'Ja' : 'Nein')); ?></p>
		<p><?php printf(__('Gebäudedichtheit: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->gebaeudedichtheit())); ?></p>
		<p><?php printf(__('Wirkungsgrad: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->wirkungsgrad())); ?></p>
		<p><?php printf(__('Luftechselvolumen h<sub>v</sub>: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->hv())); ?></p>
		<p><?php printf(__('Maximale Heizlast h<sub>max</sub>: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->h_max())); ?></p>
		<p><?php printf(__('Maximale Heizlast spezifisch h<sub>max,spez</sub>: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->h_max_spezifisch())); ?></p>

		<h4>Luftwechsel Werte</h4>
		<p><?php printf(__('Luftwechselrate n: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->luftwechsel()->n())); ?></p>
		<p><?php printf(__('Gesamtluftwechselrate n<sub>0</sub>: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->luftwechsel()->n0())); ?></p>
		<p><?php printf(__('Korrekturfakror f<sub>win,1</sub>: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->luftwechsel()->fwin1())); ?></p>
		<p><?php printf(__('Korrekturfakror f<sub>win,2</sub>: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->luftwechsel()->fwin2())); ?></p>
		<p><?php printf(__('n_anl: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->luftwechsel()->n_anl())); ?></p>
		<p><?php printf(__('n_wrg: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->luftwechsel()->n_wrg())); ?></p>

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
					<td><?php echo wpenon_format_decimal($referenzgebaeude->qi_prozesse_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->qi_wasser_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->qi_solar_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->qi_heizung_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->qi_monat($monat->slug())); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td><b>Gesamt</b></td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->qi_prozesse()); ?></td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->qi_wasser()); ?></td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->qi_solar()); ?></td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->qi_heizung()); ?></td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->qi()); ?></td>
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
					<td><?php echo wpenon_format_decimal(fum($monat->slug())); ?></td>
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
					<td><?php echo wpenon_format_decimal($referenzgebaeude->psh_sink_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->ph_sink_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->ph_source_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->QWB_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->qh_monat($monat->slug())); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td><b>Gesamt</b></td>
				<td></td>
				<td></td>
				<td></td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->QWB()); ?></td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->qh()); ?></td>
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
				<td><?php echo wpenon_format_decimal($referenzgebaeude->ßhma()); ?></td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->thm()); ?></td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->ith_rl()); ?></td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->qi()); ?></td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->QWB()); ?></td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->qh()); ?></td>
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
					<td><?php echo wpenon_format_decimal($referenzgebaeude->ph_source_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->ym_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->nm_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->flna_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->trl_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->ith_rl_monat($monat->slug())); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td><b>Gesamt</b></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->ith_rl()); ?></td>
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
					<td><?php echo wpenon_format_decimal($referenzgebaeude->k_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->bilanz_innentemperatur()->θih_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->mittlere_belastung()->ßem1($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->ßhm_monat($monat->slug())); ?></td>
					<td><?php echo wpenon_format_decimal($referenzgebaeude->thm_monat($monat->slug())); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td><b>Gesamt</b></td>
				<td></td>
				<td></td>
				<td><?php echo $referenzgebaeude->mittlere_belastung()->ßemMax(); ?> (ßemMax)</td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->ßhma()); ?> (ßhma)</td>
				<td><?php echo wpenon_format_decimal($referenzgebaeude->thm()); ?></td>
			</tr>
		</table>

		<h2>Heizsystem</h2>

		<?php $i = 1; ?>
		<?php foreach ($referenzgebaeude->heizsystem()->heizungsanlagen()->alle() as $heizungsanlage) : ?>
			<h3><?php echo 'Heizungsanlage ' . $i++; ?></h3>
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
						<th>fbaujahr</th>
						<td><?php echo $heizungsanlage->fbaujahr(); ?></td>
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
					<td><?php echo wpenon_format_decimal($uebergabesystem->ehce()); ?></td>
				<?php endforeach; ?>
		</table>

		<h3>Heizsystem</h3>

		<p><?php printf(__('Nutzbare Wärme fa<sub>h</sub>: %s', 'wpenon'), $referenzgebaeude->fa_h()); ?></p>
		<p><?php printf(__('Mittlere Belastung bei Übergabe der Heizung (ßhce): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->ßhce())); ?></p>
		<p><?php printf(__('Flächenbezogene leistung der Übergabe der Heizung (qhce): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->qhce())); ?></p>
		<p><?php printf(__('ßhd: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->ßhd())); ?></p>
		<p><?php printf(__('fhydr: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->f_hydr())); ?></p>
		<p><?php printf(__('fßd: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->fßd())); ?></p>
		<p><?php printf(__('ehd0: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->ehd0())); ?></p>
		<p><?php printf(__('ehd1: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->ehd1())); ?></p>
		<p><?php printf(__('ehd: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->ehd())); ?></p>
		<p><?php printf(__('ehd korrektur: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->ehd_korrektur())); ?></p>
		<p><?php printf(__('ßhs: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->ßhs())); ?></p>

		<p><?php printf(__('Nennleistung Pufferspeicher (pwn): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->pwn())); ?></p>
		<p><?php printf(__('(pn): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->pn())); ?></p>



		<?php if ($referenzgebaeude->heizsystem()->pufferspeicher_vorhanden()) : ?>
			<h3>Pufferspeicher</h3>
			<p><?php printf(__('Korrekturfaktor mittlere Belastung des Pufferspeichers fßhs: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->pufferspeicher()->fßhs())); ?></p>
			<p><?php printf(__('Mittlere Belastung für Speicherung ßhs: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->ßhs())); ?></p>
			<p><?php printf(__('Korrekturfaktor für beliebige mittlere Berlastung und Laufzeit der Heizung fhs: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->pufferspeicher()->fhs())); ?></p>
			<p><?php printf(__('Berechnetes Volumen: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->pufferspeicher()->volumen())); ?></p>
			<p><?php printf(__('Volumen Pufferspeicher vs1: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->pufferspeicher()->vs1())); ?></p>
			<p><?php printf(__('Volumen Pufferspeicher vs2: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->pufferspeicher()->vs2())); ?></p>
			<p><?php printf(__('Wärmeabgabe Pufferspeicher (Qhs0Vs1): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->pufferspeicher()->Qhs0Vs1())); ?></p>
			<p><?php printf(__('Wärmeabgabe Pufferspeicher (Qhs0Vs2): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->pufferspeicher()->Qhs0Vs2())); ?></p>
			<p><?php printf(__('Wärmeabgabe Pufferspeicher Gesamt (Qhs): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->pufferspeicher()->Qhs())); ?></p>
			<p><?php printf(__('Aufwandszahl für Pufferspeicher (ehs): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->ehs())); ?></p>
		<?php else : ?>
			<p><?php printf(__('Aufwandszahl für Pufferspeicher (ehs):  %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->heizsystem()->ehs())); ?></p>
		<?php endif; ?>

		<h3>Trinkwarmwasseranlage</h3>

		<p><?php printf(__('Anteils nutzbarer Wärme von Trinkwassererwärmungsanlagen fa<sub>w</sub>: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->fa_w())); ?></p>
		<p><?php printf(__('Nutzwärmebedarf für Trinkwasser qwb: %s kWh/(ma)', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->nutzwaermebedarf_trinkwasser())); ?></p>
		<p><?php printf(__('Q<sub>w,b</sub>: %s kWh', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->QWB())); ?></p>
		<p><?php printf(__('Interne Wärmequelle infolge von Warmwasser Qi<sub>w</sub>: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->qi_wasser())); ?></p>
		<p><?php printf(__('Jährlicher Nutzwaermebedarf für Trinkwasser (qwb): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->nutzwaermebedarf_trinkwasser())); ?></p>
		<p><?php printf(__('Berechnung des monatlichen Wärmebedarfs für Warmwasser(QWB) für ein Jahr: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->QWB())); ?></p>

		<h4>Aufwandszahlen Trinkwarmwasser</h4>

		<p><?php printf(__('Zwischenwert für die Berechnung von ewd (ewce): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->ewce())); ?></p>
		<p><?php printf(__('Zwischenwert für die Berechnung von ewd (ewd0): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->ewd0())); ?></p>
		<p><?php printf(__('Aufwandszahlen für die Verteilung von Trinkwarmwasser (ewd): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->ewd())); ?></p>
		<p><?php printf(__('Korrekturfaktor (fwb): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->fwb())); ?></p>
		<p><?php printf(__('Volumen Speicher 1 in Litern. (Vs01): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Vs01())); ?></p>
		<p><?php printf(__('Volumen Speicher 2 in Litern. (Vs02): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Vs02())); ?></p>
		<p><?php printf(__('Volumen Speicher 3 in Litern. (Vs03): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Vs03())); ?></p>
		<p><?php printf(__('Volumen Speicher Gesamt in Litern. (Vs0): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Vs0())); ?></p>
		<p><?php printf(__('Berechnung von Vsw1: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Vsw1())); ?></p>
		<p><?php printf(__('Berechnung von Vsw2: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Vsw2())); ?></p>
		<p><?php printf(__('Berechnung von Qws01: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Qws01())); ?></p>
		<p><?php printf(__('Berechnung von Qws02: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Qws02())); ?></p>

		<?php if ($referenzgebaeude->trinkwarmwasseranlage()->solarthermie_vorhanden()) : ?>
			<h4>Solarthermie</h4>
			<p><?php printf(__('Berechnung von Vsaux0: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Vsaux0())); ?></p>
			<p><?php printf(__('Berechnung von Vssol0: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Vssol0())); ?></p>
			<p><?php printf(__('Berechnung von Ac0: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Ac0())); ?></p>
			<p><?php printf(__('Berechnung von Qwsola0: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Qwsola0())); ?></p>
			<br />
			<p><?php printf(__('Berechnung von Vsaux: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Vsaux())); ?></p>
			<p><?php printf(__('Berechnung von Vssol: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Vssol())); ?></p>
			<p><?php printf(__('Berechnung von Ac: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Ac())); ?></p>
			<p><?php printf(__('Berechnung von fAc: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->fAc())); ?></p>
			<p><?php printf(__('Berechnung von fQsola: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->fQsola())); ?></p>
			<p><?php printf(__('Berechnung von Qwsola: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Qwsola())); ?></p>
		<?php endif; ?>

		<br>

		<p><?php printf(__('Berechnung von Qws: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->Qws())); ?></p>
		<p><?php printf(__('Berechnung von ews: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->ews())); ?></p>
		<p><?php printf(__('Berechnung von keew: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->keew())); ?></p>
		<p><?php printf(__('Berechnung von keeh: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->trinkwarmwasseranlage()->keeh())); ?></p>

		<?php if ($referenzgebaeude->photovoltaik_anlage_vorhanden()) : ?>
			<h3>Photovoltaik</h3>
			<p><?php printf(__('Richtung: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->photovoltaik_anlage()->richtung())); ?></p>
			<p><?php printf(__('Neigung: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->photovoltaik_anlage()->neigung())); ?></p>
			<p><?php printf(__('Fläche: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->photovoltaik_anlage()->flaeche())); ?></p>
			<p><?php printf(__('Baujahr: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->photovoltaik_anlage()->baujahr())); ?></p>
			<p><?php printf(__('QfprodPV: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->photovoltaik_anlage()->QfprodPV())); ?></p>
			<p><?php printf(__('WfPVHP: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->photovoltaik_anlage()->WfPVHP())); ?></p>
			<p><?php printf(__('Pvans: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->photovoltaik_anlage()->Pvans($referenzgebaeude->Qfstrom()))); ?></p>
		<?php endif; ?>

		<h4>Hilfsenergie</h4>

		<p><?php printf(__('pg: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->pg())); ?></p>

		<h5>Bestimmung der Hilfsenergie_Übergabe Wce</h5>

		<p><?php printf(__('WHce: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->WHce())); ?></p>
		<p><?php printf(__('Wc: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Wc())); ?></p>
		<p><?php printf(__('Wrvce: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Wrvce())); ?></p>
		<p><?php printf(__('Wwce: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Wwce())); ?></p>
		<p><?php printf(__('WsolPumpece: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->WsolPumpece())); ?></p>
		<p><?php printf(__('WsolPumpeg: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->WsolPumpeg())); ?></p>

		<h5>Bestimmung der Hilfsenergie_Verteilung Wd</h5>

		<p><?php printf(__('fgeoHzg: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->fgeoHzg())); ?></p>
		<p><?php printf(__('fblHzg: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->fblHzg())); ?></p>
		<p><?php printf(__('fgeoTWW: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->fgeoTWW())); ?></p>
		<p><?php printf(__('fblTWW: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->fblTWW())); ?></p>
		<p><?php printf(__('LcharHzg: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->LcharHzg())); ?></p>
		<p><?php printf(__('LcharTWW: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->LcharTWW())); ?></p>
		<p><?php printf(__('BcarHzg: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->BcarHzg())); ?></p>
		<p><?php printf(__('BcarWW: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->BcarWW())); ?></p>
		<p><?php printf(__('LmaxHzg: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->LmaxHzg())); ?></p>
		<p><?php printf(__('LmaxTWW: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->LmaxTWW())); ?></p>

		<h5>Berechnung der Hilfsenergie_Verteilung Heizung Whd , Rohrnetzberechnung</h5>

		<p><?php printf(__('TERMp: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->TERMp())); ?></p>
		<p><?php printf(__('Vstr: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Vstr())); ?></p>
		<p><?php printf(__('PhydrHzg: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->PhydrHzg())); ?></p>
		<p><?php printf(__('fe: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->fe())); ?></p>
		<p><?php printf(__('TERMpumpe: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->TERMpumpe())); ?></p>
		<p><?php printf(__('fint: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->fint())); ?></p>

		<h5>Berechnung der Hilfsenergie für Heizsysteme</h5>

		<p><?php printf(__('Wrvd: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Wrvd())); ?></p>
		<p><?php printf(__('Lv: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Lv())); ?></p>
		<p><?php printf(__('Ls: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Ls())); ?></p>
		<p><?php printf(__('Pwda: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Pwda())); ?></p>
		<p><?php printf(__('PhydrTWW: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->PhydrTWW())); ?></p>
		<p><?php printf(__('z: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->z())); ?></p>
		<p><?php printf(__('Wwd: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Wwd())); ?></p>
		<p><?php printf(__('WsolPumped: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->WsolPumped())); ?></p>
		<p><?php printf(__('Whs: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Whs())); ?></p>
		<p><?php printf(__('tpu: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->tpu())); ?></p>
		<p><?php printf(__('Vws: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Vws())); ?></p>
		<p><?php printf(__('Wws0: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Wws0())); ?></p>
		<p><?php printf(__('Wws: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Wws())); ?></p>
		<p><?php printf(__('Whd: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Whd())); ?></p>

		<h5>Berechnung der Hilfsenergie für Lüftung</h5>

		<p><?php printf(__('fbaujahr: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->fbaujahr())); ?></p>
		<p><?php printf(__('fgr_exch: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->fgr_exch())); ?></p>
		<p><?php printf(__('fsup_decr: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->fsup_decr())); ?></p>
		<p><?php printf(__('fbetrieb: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->fbetrieb())); ?></p>
		<?php if (method_exists($referenzgebaeude->lueftung(), 'strom_art')) : ?>
			<p><?php printf(__('Strom Art: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->strom_art())); ?></p>
		<?php endif; ?>
		<p><?php printf(__('Wfan0: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->Wfan0())); ?></p>
		<p><?php printf(__('Wc: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->Wc())); ?></p>
		<p><?php printf(__('Wpre_h: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->Wpre_h())); ?></p>
		<p><?php printf(__('fsystem: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->fsystem())); ?></p>
		<p><?php printf(__('Wrvg (Gesamt): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->lueftung()->Wrvg())); ?></p>

		<h5>Berechnung der Hilfsenergie für Solarthermie</h5>
		<p><?php printf(__('WsolPumpece: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->WsolPumpece())); ?></p>
		<p><?php printf(__('WsolPumped: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->WsolPumped())); ?></p>
		<p><?php printf(__('WsolPumpes: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->WsolPumpes())); ?></p>
		<p><?php printf(__('WsolPumpe: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->WsolPumpe())); ?></p>

		<h5>Hilfsenergie Endergebnisse</h5>
		<p><?php printf(__('Wh (Hilfsenergie Heizsystem): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Wh())); ?></p>
		<p><?php printf(__('Ww (Hilfsenergie Warmwasser): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Ww())); ?></p>
		<p><?php printf(__('Wrv (Hilfsenergie Lüftung): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Wrv())); ?></p>
		<p><?php printf(__('WsolPumpe (Hilfsenergie Solarpumpe): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->WsolPumpe())); ?></p>
		<p><?php printf(__('Wges (Hilfsenergie Gesamt): %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->hilfsenergie()->Wges())); ?></p>

		<h3>Endenergie</h3>

		<p><?php printf(__('Qfhges: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->Qfhges())); ?></p>
		<p><?php printf(__('Qfwges: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->Qfwges())); ?></p>
		<p><?php printf(__('Qfgesamt: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->Qfgesamt())); ?></p>
		<p><?php printf(__('Qpges: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->Qpges())); ?></p>
		<p><?php printf(__('Qfstrom: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->Qfstrom())); ?></p>
		<p><?php printf(__('Qf: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->Qf())); ?></p>
		<p><?php printf(__('Qp: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->Qp())); ?></p>

		<h3>CO2</h3>
		<p><?php printf(__('CO2 Emissionen in Kg: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->MCO2())); ?></p>
		<p><?php printf(__('CO2 Emissionen in Kg/m2: %s', 'wpenon'), wpenon_format_decimal($referenzgebaeude->MCO2a())); ?></p>

		<h4>Vergleichswerte</h4>

		<p><?php printf(__('Qp (Primärenergie): %s', 'wpenon'), wpenon_format_decimal($data['Qp_ref'])); ?></p>
		<p><?php printf(__('Ht\': %s', 'wpenon'), wpenon_format_decimal($data['ht_strich_ref'])); ?></p>

	</div>

<?php endif; ?>