<?php
exec("/bin/bash /www/htdocs/w012900a/bin/wordpress_backup_script_bitplex.sh  2>&1", $out, $result);
echo "Returncode: " .$result ."<br>";
echo "Ausgabe des Scripts: " ."<br>";
echo "<pre>"; print_r($out);

mail("sven@awesome.ug", "Backup Test", print_r( $out, true ) );