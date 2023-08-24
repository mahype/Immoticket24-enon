<?php

if( ! defined( 'ABSPATH' ) ) exit;

require_once dirname( dirname( __FILE__ ) ) . '/data/DataEnevVW.php';

if( defined('GEG_XSD') ) {
  $xsd = GEG_XSD;
} else {
  $xsd = 'https://energieausweis.dibt.de/schema/Kontrollsystem-GEG-2020_V1_0.xsd';
}

if( defined('GEG_XSD_VERSION') ) {
  $version = GEG_XSD_VERSION;
} else {
  $version = 'GEG-2020';
}

$data = new DataEnevVW( $energieausweis );

?><n1:GEG-Energieausweis xmlns:n1="<?php echo $xsd; ?>">
  <n1:Energieausweis-Daten Gesetzesgrundlage="<?php echo $version; ?>" Rechtsstand-Grund="Ausweisausstellung (bei Verbrauchsausweisen und alle anderen Fälle)" Rechtsstand="2020-08-08">
    <n1:AddressCustomer>
      <n1:Vorname><?php echo $data->get_certificate_buyer_first_name() ?></n1:Vorname>
      <n1:Nachname><?php echo $data->get_certificate_buyer_last_name() ?></n1:Nachname>
      <n1:Addresse1><?php echo $data->get_certificate_buyer_address_1() ?></n1:Addresse1>
      <n1:Addresse2><?php echo $data->get_certificate_buyer_address_2() ?></n1:Addresse2>
      <n1:Postleitzahl><?php echo $data->get_certificate_buyer_zip() ?></n1:Postleitzahl>
      <n1:Ort><?php echo $data->get_certificate_buyer_city() ?></n1:Ort>
      <n1:Bundesland><?php echo $data->Bundesland(); ?></n1:Bundesland>
      <n1:Land><?php echo $data->get_certificate_buyer_country() ?></n1:Land>
      <n1:EMail><?php echo $data->get_certificate_buyer_email() ?></n1:EMail>
      <n1:Telefon><?php echo $data->get_certificate_buyer_phone() ?></n1:Telefon>      
    </n1:AddressCustomer>
    <n1:Gebauedefoto><?php echo $data->Gebauedefoto(); ?></n1:Gebauedefoto>
    <n1:Registriernummer><?php echo $data->Registriernummer(); ?></n1:Registriernummer>
    <n1:Ausstellungsdatum><?php echo $data->Ausstellungsdatum(); ?></n1:Ausstellungsdatum>    
    <n1:Bundesland><?php echo $data->Bundesland(); ?></n1:Bundesland>
    <n1:Postleitzahl><?php echo $data->PLZ(); ?></n1:Postleitzahl>
    <n1:Gebaeudeteil><?php echo $data->Gebauedeteil(); ?></n1:Gebaeudeteil>
    <n1:Baujahr-Gebaeude><?php echo $data->BaujahrGebaeude(); ?></n1:Baujahr-Gebaeude>
    <n1:Altersklasse-Gebaeude><?php echo $data->AltersklasseGebaeude(); ?></n1:Altersklasse-Gebaeude>
    <n1:Baujahr-Waermeerzeuger><?php echo $data->BaujahrWaermeerzeuger(); ?></n1:Baujahr-Waermeerzeuger>
    <n1:Altersklasse-Waermeerzeuger><?php echo $data->AltersklasseWaermeerzeuger(); ?></n1:Altersklasse-Waermeerzeuger>
    <n1:wesentliche-Energietraeger-Heizung><?php echo $data->WesentlicheEnergietraegerHeizung(); ?></n1:wesentliche-Energietraeger-Heizung>
    <n1:wesentliche-Energietraeger-Warmwasser><?php echo $data->WesentlicheEnergietraegerWarmWasser(); ?></n1:wesentliche-Energietraeger-Warmwasser>
    <n1:Erneuerbare-Art><?php echo $data->ErneuerbareArt(); ?></n1:Erneuerbare-Art>
    <n1:Erneuerbare-Verwendung><?php echo $data->ErneuerbareVerwendung(); ?></n1:Erneuerbare-Verwendung>
    <n1:Lueftungsart-Fensterlueftung><?php echo $data->LueftungsartFensterlueftung(); ?></n1:Lueftungsart-Fensterlueftung>
    <n1:Lueftungsart-Schachtlueftung><?php echo $data->LueftungsartSchachtlueftung(); ?></n1:Lueftungsart-Schachtlueftung>
    <n1:Lueftungsart-Anlage-o-WRG><?php echo $data->LueftungsartAnlageOWRG(); ?></n1:Lueftungsart-Anlage-o-WRG>
    <n1:Lueftungsart-Anlage-m-WRG><?php echo $data->LueftungsartAnlageMWRG(); ?></n1:Lueftungsart-Anlage-m-WRG>
    <n1:Kuehlungsart-passive-Kuehlung>false</n1:Kuehlungsart-passive-Kuehlung>
    <n1:Kuehlungsart-Strom><?php echo $data->KuehlungsartStrom(); ?></n1:Kuehlungsart-Strom>
    <n1:Kuehlungsart-Waerme>false</n1:Kuehlungsart-Waerme>
    <n1:Kuehlungsart-gelieferte-Kaelte>false</n1:Kuehlungsart-gelieferte-Kaelte>
    <?php if ( $data->KlimaanlageVorhanden() ) : ?>
      <n1:Anzahl-Klimanlagen><?php echo $data->AnzahlKlimaanlagen(); ?></n1:Anzahl-Klimanlagen>
      <n1:Anlage-groesser-12kW-ohneGA><?php echo $data->AnlageGroesser12kWohneGA(); ?></n1:Anlage-groesser-12kW-ohneGA>
      <n1:Anlage-groesser-12kW-mitGA><?php echo $data->AnlageGroesser12kWmitGA(); ?></n1:Anlage-groesser-12kW-mitGA>
      <n1:Anlage-groesser-70kW><?php echo $data->AnlageGroesser70kW(); ?></n1:Anlage-groesser-70kW>
      <n1:Faelligkeitsdatum-Inspektion><?php echo $data->FaelligkeitsdatumInspektion(); ?></n1:Faelligkeitsdatum-Inspektion>
    <?php else: ?>
      <n1:Keine-inspektionspflichtige-Anlage>true</n1:Keine-inspektionspflichtige-Anlage>
    <?php endif; ?>
    <n1:Treibhausgasemissionen><?php echo $data->Treibhausgasemissionen(); ?></n1:Treibhausgasemissionen>
    <n1:Ausstellungsanlass><?php echo $data->Ausstellungsanlass(); ?></n1:Ausstellungsanlass>
    <n1:Datenerhebung-Aussteller><?php echo $data->DatenerhebungAussteller(); ?></n1:Datenerhebung-Aussteller>
    <n1:Datenerhebung-Eigentuemer><?php echo $data->DatenerhebungEigentuemer(); ?></n1:Datenerhebung-Eigentuemer>
    <n1:Wohngebaeude>
      <n1:Gebaeudetyp><?php echo $data->Gebaeudetyp(); ?></n1:Gebaeudetyp>
      <n1:Anzahl-Wohneinheiten><?php echo $data->AnzahlWohneinheiten(); ?></n1:Anzahl-Wohneinheiten>
      <n1:Gebaeudenutzflaeche><?php echo $data->Gebaeudenutzflaeche(); ?></n1:Gebaeudenutzflaeche>
      <n1:Verbrauchswerte>
        <n1:Flaechenermittlung-AN-aus-Wohnflaeche>true</n1:Flaechenermittlung-AN-aus-Wohnflaeche>
        <n1:Wohnflaeche><?php echo $data->Wohnflaeche(); ?></n1:Wohnflaeche>
        <n1:Keller-beheizt><?php echo $data->KellerBeheizt(); ?></n1:Keller-beheizt>      
        <?php foreach( $data->Energietraeger() AS $energietraeger ): ?>
        <n1:Energietraeger>
          <n1:Energietraeger-Verbrauch><?php echo $energietraeger->EnergietraegerVerbrauch(); ?></n1:Energietraeger-Verbrauch>
          <n1:Unterer-Heizwert><?php echo $energietraeger->UntererHeizwert(); ?></n1:Unterer-Heizwert>
          <n1:Primaerenergiefaktor><?php echo $energietraeger->Primaerenergiefaktor(); ?></n1:Primaerenergiefaktor>
          <n1:Emissionsfaktor><?php echo $energietraeger->Emissionsfaktor(); ?></n1:Emissionsfaktor>
          <?php for( $i = 0; $i < 3; $i++ ): ?>
          <n1:Zeitraum>
            <n1:Startdatum><?php echo $energietraeger->Startdatum( $i ); ?></n1:Startdatum>
            <n1:Enddatum><?php echo $energietraeger->Enddatum( $i ); ?></n1:Enddatum>
            <n1:Verbrauchte-Menge><?php echo $energietraeger->VerbrauchteMenge( $i ); ?></n1:Verbrauchte-Menge>
            <n1:Energieverbrauch><?php echo $energietraeger->Energieverbrauch( $i ); ?></n1:Energieverbrauch>
            <n1:Energieverbrauchsanteil-Warmwasser-zentral><?php echo $energietraeger->EnergieverbrauchsanteilWarmwasserZentral( $i ); ?></n1:Energieverbrauchsanteil-Warmwasser-zentral>
            <n1:Warmwasserwertermittlung><?php echo $energietraeger->Warmwasserwertermittlung(); ?></n1:Warmwasserwertermittlung>
            <n1:Energieverbrauchsanteil-Heizung><?php echo $energietraeger->EnergieverbrauchsanteilHeizung( $i ); ?></n1:Energieverbrauchsanteil-Heizung>
            <n1:Klimafaktor><?php echo $energietraeger->Klimafaktor( $i ); ?></n1:Klimafaktor>
          </n1:Zeitraum>
          <?php endfor; ?>
        </n1:Energietraeger>
        <?php endforeach; ?>
        <n1:Leerstandszuschlag-Heizung>
          <?php if( $data->LeerstandszuschlagHeizung() > 0 ): ?>
              <n1:Leerstandszuschlag-nach-Bekanntmachung>
                <n1:Leerstandsfaktor><?php echo $data->Leerstandsfaktor(); ?></n1:Leerstandsfaktor>
                <n1:Startdatum><?php echo $data->Startdatum(); ?></n1:Startdatum>
                <n1:Enddatum><?php echo $data->Enddatum(); ?></n1:Enddatum>
                <n1:Leerstandszuschlag-kWh><?php echo $data->LeerstandszuschlagHeizung(); ?></n1:Leerstandszuschlag-kWh>
                <n1:Primaerenergiefaktor><?php echo $data->Primaerenergiefaktor(); ?></n1:Primaerenergiefaktor>
              </n1:Leerstandszuschlag-nach-Bekanntmachung>
            <n1:Zuschlagsfaktor><?php echo $data->Zuschlagsfaktor(); ?></n1:Zuschlagsfaktor>
            <n1:witterungsbereinigter-Endenergieverbrauchsanteil-fuer-Heizung><?php echo $data->LeerstandszuschlagHeizung(); ?></n1:witterungsbereinigter-Endenergieverbrauchsanteil-fuer-Heizung>
          <?php else: ?>
            <n1:kein-Leerstand>Kein längerer Leerstand Heizung zu berücksichtigen.</n1:kein-Leerstand>
          <?php endif; ?>
        </n1:Leerstandszuschlag-Heizung>
        
        <n1:Leerstandszuschlag-Warmwasser>
          <n1:keine-Nutzung-von-WW>false</n1:keine-Nutzung-von-WW>
         <?php if( $data->LeerstandszuschlagWarmWasser() > 0 ): ?>
          <n1:Leerstandszuschlag-nach-Bekanntmachung>
                <n1:Leerstandsfaktor><?php echo $data->Leerstandsfaktor(); ?></n1:Leerstandsfaktor>
                <n1:Startdatum><?php echo $data->Startdatum(); ?></n1:Startdatum>
                <n1:Enddatum><?php echo $data->Enddatum(); ?></n1:Enddatum>
                <n1:Leerstandszuschlag-kWh><?php echo $data->LeerstandszuschlagWarmWasser(); ?></n1:Leerstandszuschlag-kWh>
                <n1:Primaerenergiefaktor><?php echo $data->Primaerenergiefaktor(); ?></n1:Primaerenergiefaktor>
          </n1:Leerstandszuschlag-nach-Bekanntmachung>
          <?php else: ?>
            
            <n1:kein-Leerstand>Kein längerer Leerstand Warmwasser zu berücksichtigen.</n1:kein-Leerstand>
          <?php endif; ?>
        </n1:Leerstandszuschlag-Warmwasser>
        
        <?php if( $data->Warmwasserzuschlag() > 0 ): ?>
        <n1:Warmwasserzuschlag>
          <n1:Startdatum><?php echo $data->Startdatum(); ?></n1:Startdatum>
          <n1:Enddatum><?php echo $data->Enddatum(); ?></n1:Enddatum>
          <n1:Primaerenergiefaktor><?php echo $data->WarmwasserPrimaerenergiefaktor(); ?></n1:Primaerenergiefaktor>
          <n1:Warmwasserzuschlag-kWh><?php echo $data->Warmwasserzuschlag(); ?></n1:Warmwasserzuschlag-kWh>
        </n1:Warmwasserzuschlag>
        <?php endif; ?>
        
        <?php if( $data->Kuehlzuschlag() > 0 ): ?>
        <n1:Kuehlzuschlag>
          <n1:Startdatum><?php echo $data->Startdatum(); ?></n1:Startdatum>
          <n1:Enddatum><?php echo $data->Enddatum(); ?></n1:Enddatum>
          <n1:Gebaeudenutzflaeche-gekuehlt><?php echo $data->GebaeudenutzflaecheGekuehlt(); ?></n1:Gebaeudenutzflaeche-gekuehlt>
          <n1:Primaerenergiefaktor><?php echo $data->KuehlerPrimaerenergiefaktor(); ?></n1:Primaerenergiefaktor>
          <n1:Kuehlzuschlag-kWh><?php echo $data->Kuehlzuschlag(); ?></n1:Kuehlzuschlag-kWh>
        </n1:Kuehlzuschlag>
        <?php endif; ?>

        <n1:Mittlerer-Endenergieverbrauch><?php echo $data->MittlererEndenergieverbrauch(); ?></n1:Mittlerer-Endenergieverbrauch>
        <n1:Mittlerer-Primaerenergieverbrauch><?php echo $data->MittlererPrimaerenergieverbrauch(); ?></n1:Mittlerer-Primaerenergieverbrauch>
        <n1:Energieeffizienzklasse><?php echo $data->Energieeffizienzklasse(); ?></n1:Energieeffizienzklasse>

      </n1:Verbrauchswerte>
    </n1:Wohngebaeude>
    <n1:Empfehlungen-moeglich><?php echo $data->EmpfehlungenMoeglich(); ?></n1:Empfehlungen-moeglich>
    <n1:Keine-Modernisierung-Erweiterung-Vorhaben>true</n1:Keine-Modernisierung-Erweiterung-Vorhaben>
    <?php if(  $data->EmpfehlungenMoeglich() == 'true' ): ?>
      <?php foreach( $data->Modernisierungsempfehlungen() AS $key => $modernisierungsempfehlung ): ?>  
      <n1:Modernisierungsempfehlungen>
        <n1:Nummer><?php echo $key + 1; ?></n1:Nummer>
        <n1:Bauteil-Anlagenteil><?php echo $modernisierungsempfehlung->BauteilAnlagenteil(); ?></n1:Bauteil-Anlagenteil>
        <n1:Massnahmenbeschreibung><?php echo $modernisierungsempfehlung->Massnahmenbeschreibung(); ?></n1:Massnahmenbeschreibung>
        <n1:Modernisierungskombination><?php echo $modernisierungsempfehlung->Modernisierungskombination(); ?></n1:Modernisierungskombination>
      </n1:Modernisierungsempfehlungen>
      <?php endforeach; ?>
    <?php endif; ?>
  </n1:Energieausweis-Daten>
</n1:GEG-Energieausweis>