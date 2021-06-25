<?php

require dirname( dirname( __FILE__ ) ) . '/data/DataEnevBW.php';

$data = new DataEnevBW( $energieausweis );

?><?xml version="1.0" encoding="UTF-8"?>
<n1:GEG-Energieausweis xmlns:n1="https://energieausweis.dibt.de/schema/Kontrollsystem-GEG-2020_V1_0.xsd">
  <n1:Energieausweis-Daten Gesetzesgrundlage="GEG-2020" Rechtsstand-Grund="Ausweisausstellung (bei Verbrauchsausweisen und alle anderen Fälle)" Rechtsstand="2020-08-08">
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
      <n1:Bedarfswerte-4108-4701>
        <n1:Wohngebaeude-Anbaugrad><?php $data->WohngebaeudeAnbaugrad();?></n1:Wohngebaeude-Anbaugrad>
        <n1:Bruttovolumen><?php $data->Bruttovolumen();?></n1:Bruttovolumen>
        <n1:durchschnittliche-Geschosshoehe><?php $data->DurchschnittlicheGeschosshoehe();?></n1:durchschnittliche-Geschosshoehe>
        <?php foreach( $data->BauteileOpak() AS $bauteil ): ?>
        <n1:Bauteil-Opak>
          <n1:Flaechenbezeichnung><?php echo $bauteil->Flaechenbezeichnung(); ?></n1:Flaechenbezeichnung>
          <n1:Flaeche><?php echo $bauteil->Flaeche(); ?></n1:Flaeche>
          <n1:U-Wert><?php echo $bauteil->Uwert(); ?></n1:U-Wert>
          <?php if( $bauteil->Ausrichtung() !== false ): ?>
          <n1:Ausrichtung><?php echo $bauteil->Ausrichtung(); ?></n1:Ausrichtung>
          <?php endif; ?>
          <n1:grenztAn><?php echo $bauteil->GrenztAn(); ?></n1:grenztAn>
          <n1:Glasdach-Lichtband-Lichtkuppel>false</n1:Glasdach-Lichtband-Lichtkuppel>
          <n1:Vorhangfassade>false</n1:Vorhangfassade>
        </n1:Bauteil-Opak>
        <?php endforeach; ?>  
        <?php foreach( $data->BauteileTransparent() AS $bauteil ): ?>
        <n1:Bauteil-Transparent>
          <n1:Flaechenbezeichnung><?php echo $bauteil->Flaechenbezeichnung(); ?></n1:Flaechenbezeichnung>
          <n1:Flaeche><?php echo $bauteil->Flaeche(); ?></n1:Flaeche>
          <n1:U-Wert><?php echo $bauteil->Uwert(); ?></n1:U-Wert>
          <n1:g-Wert><?php echo $bauteil->GWert(); ?></n1:g-Wert>
          <n1:Ausrichtung><?php echo $bauteil->Ausrichtung(); ?></n1:Ausrichtung>
          <n1:Glasdach-Lichtband-Lichtkuppel>false</n1:Glasdach-Lichtband-Lichtkuppel>
          <n1:Vorhangfassade>false</n1:Vorhangfassade>
        </n1:Bauteil-Transparent>
        <?php endforeach; ?>
        <?php foreach( $data->BauteileTransparent() AS $bauteil ): ?>
        <n1:Bauteil-Dach>
          <n1:Flaechenbezeichnung><?php echo $bauteil->Flaechenbezeichnung(); ?></n1:Flaechenbezeichnung>
          <n1:Flaeche><?php echo $bauteil->Flaeche(); ?></n1:Flaeche>
          <n1:U-Wert><?php echo $bauteil->Uwert(); ?></n1:U-Wert>
        </n1:Bauteil-Dach>
        <?php endforeach; ?>
        <n1:Waermebrueckenzuschlag><?php echo $data->Waermebrueckenzuschlag(); ?></n1:Waermebrueckenzuschlag>
        <n1:Transmissionswaermeverlust><?php echo $data->Transmissionswaermeverlust(); ?></n1:Transmissionswaermeverlust>
        <n1:Luftdichtheit><?php echo $data->Luftdichtheit(); ?></n1:Luftdichtheit>
        <n1:Lueftungswaermeverlust><?php echo $data->Lueftungswaermeverlust(); ?></n1:Lueftungswaermeverlust>
        <n1:Solare-Waermegewinne><?php echo $data->SolareWaermegewinne(); ?></n1:Solare-Waermegewinne>
        <n1:Interne-Waermegewinne><?php echo $data->InterneWaermegewinne(); ?></n1:Interne-Waermegewinne>
        <?php foreach( $data->Heizungsanlagen() AS $heizungsanlage ): ?>
        <n1:Heizungsanlage>
          <n1:Waermeerzeuger-Bauweise-4701><?php echo $heizungsanlage->WaermeerzeugerBauweise4701(); ?></n1:Waermeerzeuger-Bauweise-4701>
          <n1:Nennleistung><?php echo $heizungsanlage->Nennleistung(); ?></n1:Nennleistung>
          <n1:Waermeerzeuger-Baujahr><?php echo $heizungsanlage->WaermeerzeugerBaujahr(); ?></n1:Waermeerzeuger-Baujahr>
          <n1:Anzahl-baugleiche><?php echo $heizungsanlage->AnzahlBaugleiche(); ?></n1:Anzahl-baugleiche>
          <n1:Energietraeger><?php echo $heizungsanlage->Energietraeger(); ?></n1:Energietraeger>
          <n1:Primaerenergiefaktor><?php echo $heizungsanlage->WaermeerzeugerBauweise4701(); ?></n1:Primaerenergiefaktor>
          <n1:Emissionsfaktor><?php echo $heizungsanlage->Emissionsfaktor(); ?></n1:Emissionsfaktor>
        </n1:Heizungsanlage>
        <?php endforeach; ?>
        <n1:Pufferspeicher-Nenninhalt><?php echo $data->PufferspeicherNenninhalt(); ?></n1:Pufferspeicher-Nenninhalt>
        <n1:Heizkreisauslegungstemperatur><?php echo $data->Heizkreisauslegungstemperatur(); ?></n1:Heizkreisauslegungstemperatur>
        <n1:Heizungsanlage-innerhalb-Huelle><?php echo $data->HeizungsanlageInnerhalbHuelle(); ?></n1:Heizungsanlage-innerhalb-Huelle>        
        <?php foreach( $data->Trinkwasseranlagen() AS $trinkwasseranlage ): ?>
        <n1:Trinkwarmwasseranlage>
          <n1:Trinkwarmwassererzeuger-Bauweise-4701><?php echo $trinkwasseranlage->TrinkwarmwassererzeugerBauweise4701(); ?></n1:Trinkwarmwassererzeuger-Bauweise-4701>
          <n1:Trinkwarmwassererzeuger-Baujahr><?php echo $trinkwasseranlage->TrinkwarmwassererzeugerBaujahr(); ?></n1:Trinkwarmwassererzeuger-Baujahr>
          <n1:Anzahl-baugleiche><?php echo $trinkwasseranlage->AnzahlBaugleiche(); ?></n1:Anzahl-baugleiche>
        </n1:Trinkwarmwasseranlage>
        <?php endforeach; ?>
        <n1:Trinkwarmwasserspeicher-Nenninhalt><?php echo $data->TrinkwarmwasserspeicherNenninhalt(); ?></n1:Trinkwarmwasserspeicher-Nenninhalt>
        <n1:Trinkwarmwasserverteilung-Zirkulation><?php echo $data->TrinkwarmwasserverteilungZirkulation(); ?></n1:Trinkwarmwasserverteilung-Zirkulation>
        <n1:Vereinfachte-Datenaufnahme><?php echo $data->VereinfachteDatenaufnahme(); ?></n1:Vereinfachte-Datenaufnahme>
        <n1:spezifischer-Transmissionswaermeverlust-Ist><?php echo $data->SpezifischerTransmissionswaermeverlustIst(); ?></n1:spezifischer-Transmissionswaermeverlust-Ist>
        <n1:Innovationsklausel>false</n1:Innovationsklausel>
        <n1:Quartiersregelung>false</n1:Quartiersregelung>
        <n1:Primaerenergiebedarf-Hoechstwert-Bestand><?php echo $data->PrimaerenergiebedarfHoechstwertBestand(); ?></n1:Primaerenergiebedarf-Hoechstwert-Bestand>
        <n1:Endenergiebedarf-Hoechstwert-Bestand><?php echo $data->EndenergiebedarfHoechstwertBestand(); ?></n1:Endenergiebedarf-Hoechstwert-Bestand>
        <n1:Treibhausgasemissionen-Hoechstwert-Bestand><?php echo $data->TreibhausgasemissionenHoechstwertBestand(); ?></n1:Treibhausgasemissionen-Hoechstwert-Bestand>      
        <n1:Endenergiebedarf-Waerme-AN><?php echo $data->EndenergiebedarfWaermeAN(); ?></n1:Endenergiebedarf-Waerme-AN>
        <n1:Endenergiebedarf-Hilfsenergie-AN><?php echo $data->EndenergiebedarfHilfsenergieAN(); ?></n1:Endenergiebedarf-Hilfsenergie-AN>
        <n1:Endenergiebedarf-Gesamt><?php echo $data->EndenergiebedarfGesamt(); ?></n1:Endenergiebedarf-Gesamt>
        <n1:Primaerenergiebedarf><?php echo $data->Primaerenergiebedarf(); ?></n1:Primaerenergiebedarf>
        <n1:Energieeffizienzklasse><?php echo $data->Energieeffizienzklasse(); ?></n1:Energieeffizienzklasse>
        <n1:Nicht-verschaerft-nach-GEG-34>true</n1:Nicht-verschaerft-nach-GEG-34>
      </n1:Bedarfswerte-4108-4701>
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