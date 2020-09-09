
<style type="text/css">
    <?php
    if($style == "10"){
        echo
        '#'.$tableId.' .pricing-table-column-content .features li::before { background-color: ' .  $data['table_color'] . ' !important; }
         #'.$tableId.' .button { background-color: ' .  $data['table_color'] . ' !important; }
         #'.$tableId.' .pricing-table-column-content .price { color: ' .  $data['table_color'] . ' !important; }
         #'.$tableId.' .pricing-table-column-content:hover,
         #'.$tableId.' .pricing-table-column-content:hover .name {  
            border-color: ' .  $data['table_color'] . ';  -webkit-transition : border 500ms ease-out;
            -moz-transition : border 500ms ease-out;
            -o-transition : border 500ms ease-out;
            transition : border 500ms ease-out;
         }';

        if($most_popular === 'Y'){
            echo '#'.$tableId.' .pricing-table-column-content .popular span{ background-color: ' .  $data['table_color'] . ' !important; }';
        }
    }
    ?>
</style>