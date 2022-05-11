<?php
exec("/bin/bash /Backup_Extern/Backup_Script/wordpress_backup_script_bitplex.sh  2>&1", $out, $result);
echo "Returncode: " .$result ."<br>";
echo "Ausgabe des Scripts: " ."<br>";
echo "<pre>"; print_r($out);
