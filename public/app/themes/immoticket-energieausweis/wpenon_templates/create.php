<?php
/**
 * @package WPENON
 * @version 1.0.2
 * @author Felix Arntz <felix-arntz@leaves-webdesign.com>
 */
?>

<p>
	<?php _e( 'Geben Sie allgemeine Informationen für den Energieausweis ein, den Sie erstellen möchten.', 'wpenon' ); ?>
</p>
<p>
	<strong><?php _e( 'Achtung:', 'wpenon' ); ?></strong>
	<?php _e( 'Sie können die Adress-Angaben unten im Nachhinein nicht mehr ändern. Achten Sie also auf die Korrektheit Ihrer Eingaben.', 'wpenon' ); ?>
</p>
<p>
	<?php _e( 'Ihren Energieausweis erhalten Sie nach Bestellabschluss per Mail zugesendet.', 'wpenon' ); ?>
</p>

<?php wpenon_get_view()->displaySubTemplate( 'message-error', '', $data['errors'] ); ?>
<?php wpenon_get_view()->displaySubTemplate( 'message-warning', '', $data['warnings'] ); ?>

<form id="wpenon-generate-form" class="form-horizontal" role="form" action="<?php echo $data['action_url']; ?>" method="post" enctype="multipart/form-data" novalidate>
	<?php do_action( 'wpenon_form_start', $data ); ?>
	<?php wpenon_get_view()->displaySubTemplate( 'schematabs', '', $data['schema'] ); ?>

	<?php wpenon_get_view()->displaySubTemplate( 'schemafields', '', $data['additional'] ); ?>

	<?php do_action( 'immoticketenergieausweis_certificate_create_form_extra_fields', $data ); ?>

	<p class="text-right">
		<button type="submit" class="btn btn-primary"><?php _e( 'Energieausweis-Erstellung beginnen', 'wpenon' ); ?></button>
	</p>
	<?php do_action( 'wpenon_form_end', $data ); ?>
</form>
