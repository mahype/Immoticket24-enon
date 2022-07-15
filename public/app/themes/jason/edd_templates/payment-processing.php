<div class="wp-block-create-block-block-wave-separator wave-1 has-yellow-neon-to-green-mint-gradient-background has-background" style="height:160px;margin-top:-160px">
    <svg viewBox="0 0 283.5 17" height="0" width="0">
        <clipPath id="wave-1" clipPathUnits="objectBoundingBox" transform="scale(0.00352733686067 0.058823529411765)">
            <path
                d="M0,0c11,3,31.4,6.9,55,8.7c38.5,2.9,40.7-2,78.3-0.6c59.5,2.2,91.3,9,125.3,8.7c22.7-0.2,24.9-2.5,24.9-2.5l0,2.8H0L0,0z" />
        </clipPath>
    </svg>
</div>

<div class="wp-block-group is-style-group-main has-yellow-neon-to-green-mint-gradient-background has-background" style="padding-top:20px">
		<p><?php printf( __( 'Dein Kauf wird ausgef&uuml;hrt. Diese Seite wird sich in 8 Sekunden automatisch aktualisieren. Sollte dies nicht der Fall sein bitte <a href="%s">hier</a> klicken..', 'enon' ), edd_get_success_page_uri() ); ?>	
		<span class="edd-cart-ajax"><i class="edd-icon-spinner edd-icon-spin"></i></span>
		<script type="text/javascript">setTimeout(function(){ window.location = '<?php echo edd_get_success_page_uri(); ?>'; }, 8000);</script>
</div>