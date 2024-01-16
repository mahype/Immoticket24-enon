<?php

if( ! defined( 'ABSPATH' ) ) exit;

if( defined( 'WPENON_DEBUG' ) && WPENON_DEBUG === true )
{
    $id   = WPENON_DIBT_DEBUG_USER;
    $pass = md5( WPENON_DIBT_DEBUG_PASSWORD );
} else {
    $id   = WPENON_DIBT_USER;
    $pass = md5( WPENON_DIBT_PASSWORD );
}

$art = $energieausweis->mode == 'b' ? 'Energiebedarfsausweis' : 'Energieverbrauchsausweis';

?><root xmlns="https://energieausweis.dibt.de/schema/SchemaDatenErfassung.xsd">
    <Authentifizierung>
        <Aussteller_ID_DIBT><?php echo $id; ?></Aussteller_ID_DIBT>
        <Aussteller_PWD_DIBT><?php echo $pass; ?></Aussteller_PWD_DIBT>
    </Authentifizierung>
    <EnEV-Nachweis>
        <Ausstellungsdatum><?php echo date('Y-m-d' ); ?></Ausstellungsdatum>
        <Bundesland><?php echo $energieausweis->adresse_bundesland; ?></Bundesland>
        <Postleitzahl><?php echo $energieausweis->adresse_plz; ?></Postleitzahl>
    </EnEV-Nachweis>
    <Energieausweis-Daten>
        <Gebaeudeart>Wohngebäude</Gebaeudeart>
        <Art><?php echo $art; ?></Art>
        <Neubau>0</Neubau>
    </Energieausweis-Daten>
</root>