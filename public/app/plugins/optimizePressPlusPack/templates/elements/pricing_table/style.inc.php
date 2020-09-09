
<style type="text/css">
    <?php
	    if($style == "9"){
	        echo
	        '#'.$id.' .button { background-color: ' .  $data['button_color'] . ' !important; }
	         #'.$id.' .price { background-color: ' .  $data['price_color'] . ' !important; } 
	         #'.$id.' .popular .name { background-color: ' .  $data['button_color'] . ' !important; }';
	    } else if ($style == '11') {
	    	echo
	        '#'.$id.' .submit-table a { background-color: ' .  $data['button_color'] . ' !important; }
	         #'.$id.' .pricing-table-column-content .popular { background-color: ' .  $data['button_color'] . ' !important; }';
	    }
    ?>
</style>