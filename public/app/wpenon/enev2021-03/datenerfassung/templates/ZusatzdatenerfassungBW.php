<?php

require dirname( __FILE__ ) . '/DataEnevBW.php';

$data = new DataEnevBW( $energieausweis );

?><n1:GEG-Energieausweis xmlns:n1="https://energieausweis.dibt.de/schema/Kontrollsystem-GEG-2020_V1_0.xsd">
  <n1:Energieausweis-Daten Gesetzesgrundlage="GEG-2020" Rechtsstand-Grund="<?php echo $data->RechtsstandGrund(); ?>" Rechtsstand="2020-08-08">
    <n1:Registriernummer><?php echo $data->Registriernummer(); ?></n1:Registriernummer>
    <n1:Ausstellungsdatum><?php echo $data->Ausstellungsdatum(); ?></n1:Ausstellungsdatum>
    <n1:Bundesland><?php echo $data->Bundesland(); ?></n1:Bundesland>
    <n1:Postleitzahl><?php echo $data->PLZ(); ?></n1:Postleitzahl>
    <n1:Gebaeudeteil><?php echo $data->Gebauedeteil(); ?></n1:Gebaeudeteil>
    <n1:Baujahr-Gebaeude><?php echo $data->BaujahrGebaeude(); ?></n1:Baujahr-Gebaeude>
    <n1:Altersklasse-Gebaeude><?php echo $data->AltersklasseGebaeude(); ?><</n1:Altersklasse-Gebaeude>
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
    <n1:Kuehlungsart-passive-Kuehlung><?php echo $data->KuehlungsartPassiveKuehlung(); ?></n1:Kuehlungsart-passive-Kuehlung>
    <n1:Kuehlungsart-Strom><?php echo $data->KuehlungsartStrom(); ?></n1:Kuehlungsart-Strom>
    <n1:Kuehlungsart-Waerme><?php echo $data->KuehlungsartWaerme(); ?></n1:Kuehlungsart-Waerme>
    <n1:Kuehlungsart-gelieferte-Kaelte><?php echo $data->KuehlungsartGelieferteKaelte(); ?></n1:Kuehlungsart-gelieferte-Kaelte>
    <?php if ( $data->KlimaanlageVorhanden() ) : ?>
      <n1:Anzahl-Klimanlagen><?php echo $data->AnzahlKlimaanlagen(); ?></n1:Anzahl-Klimanlagen>
      <n1:Anlage-groesser-12kW-ohneGA><?php echo $data->AnlageGroesser12kWohneGA(); ?></n1:Anlage-groesser-12kW-ohneGA>
      <n1:Anlage-groesser-12kW-mitGA><?php echo $data->AnlageGroesser12kWmitGA(); ?></n1:Anlage-groesser-12kW-mitGA>
      <n1:Anlage-groesser-70kW><?php echo $data->AnlageGroesser70kW(); ?></n1:Anlage-groesser-70kW>
      <n1:Faelligkeitsdatum-Inspektion><?php echo $data->FaelligkeitsdatumInspektion(); ?></n1:Faelligkeitsdatum-Inspektion>
    <?php else: ?>
      <n1:Keine-inspektionspflichtige-Anlage>true</n1:Faelligkeitsdatum-Inspektion>
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
          <n1:Ausrichtung><?php echo $bauteil->Ausrichtung(); ?></n1:Ausrichtung>
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
        
        <n1:Heizungsanlage>
          <n1:Waermeerzeuger-Bauweise-4701>Standard-Heizkessel als Gas-Spezial-Heizkessel</n1:Waermeerzeuger-Bauweise-4701>
          <n1:Nennleistung>100</n1:Nennleistung>
          <n1:Waermeerzeuger-Baujahr>1989</n1:Waermeerzeuger-Baujahr>
          <n1:Anzahl-baugleiche>0</n1:Anzahl-baugleiche>
          <n1:Energietraeger>Bioöl</n1:Energietraeger>
          <n1:Primaerenergiefaktor>1.2</n1:Primaerenergiefaktor>
          <n1:Emissionsfaktor>1</n1:Emissionsfaktor>
        </n1:Heizungsanlage>
        
        <n1:Pufferspeicher-Nenninhalt>100</n1:Pufferspeicher-Nenninhalt>
        <n1:Heizkreisauslegungstemperatur>55/45</n1:Heizkreisauslegungstemperatur>
        <n1:Heizungsanlage-innerhalb-Huelle>true</n1:Heizungsanlage-innerhalb-Huelle>
        <n1:Trinkwarmwasseranlage>
          <n1:Trinkwarmwassererzeuger-Bauweise-4701>Elektro-Durchlauferhitzer</n1:Trinkwarmwassererzeuger-Bauweise-4701>
          <n1:Trinkwarmwassererzeuger-Baujahr>1989</n1:Trinkwarmwassererzeuger-Baujahr>
          <n1:Anzahl-baugleiche>0</n1:Anzahl-baugleiche>
        </n1:Trinkwarmwasseranlage>
        <n1:Trinkwarmwasserspeicher-Nenninhalt>50</n1:Trinkwarmwasserspeicher-Nenninhalt>
        <n1:Trinkwarmwasserverteilung-Zirkulation>true</n1:Trinkwarmwasserverteilung-Zirkulation>
        <n1:Vereinfachte-Datenaufnahme>false</n1:Vereinfachte-Datenaufnahme>
        <n1:spezifischer-Transmissionswaermeverlust-Ist>20</n1:spezifischer-Transmissionswaermeverlust-Ist>
        <n1:spezifischer-Transmissionswaermeverlust-Hoechstwert>30</n1:spezifischer-Transmissionswaermeverlust-Hoechstwert>
        <n1:angerechneter-lokaler-erneuerbarer-Strom>10</n1:angerechneter-lokaler-erneuerbarer-Strom>
        <n1:Innovationsklausel>false</n1:Innovationsklausel>
        <n1:Quartiersregelung>false</n1:Quartiersregelung>
        <n1:Primaerenergiebedarf-Hoechstwert-Bestand>1.4</n1:Primaerenergiebedarf-Hoechstwert-Bestand>
        <n1:Endenergiebedarf-Hoechstwert-Bestand>1.2</n1:Endenergiebedarf-Hoechstwert-Bestand>
        <n1:Treibhausgasemissionen-Hoechstwert-Bestand>1.5</n1:Treibhausgasemissionen-Hoechstwert-Bestand>
        <n1:Energietraeger-Liste>
          <n1:Energietraegerbezeichnung>Biogas</n1:Energietraegerbezeichnung>
          <n1:Primaerenergiefaktor>1.2</n1:Primaerenergiefaktor>
          <n1:Endenergiebedarf-Heizung-spezifisch>20</n1:Endenergiebedarf-Heizung-spezifisch>
          <n1:Endenergiebedarf-Kuehlung-Befeuchtung-spezifisch>10</n1:Endenergiebedarf-Kuehlung-Befeuchtung-spezifisch>
          <n1:Endenergiebedarf-Trinkwarmwasser-spezifisch>0</n1:Endenergiebedarf-Trinkwarmwasser-spezifisch>
          <n1:Endenergiebedarf-Beleuchtung-spezifisch>5</n1:Endenergiebedarf-Beleuchtung-spezifisch>
          <n1:Endenergiebedarf-Lueftung-spezifisch>6</n1:Endenergiebedarf-Lueftung-spezifisch>
          <n1:Endenergiebedarf-Energietraeger-Gesamtgebaeude-spezifisch>50</n1:Endenergiebedarf-Energietraeger-Gesamtgebaeude-spezifisch>
        </n1:Energietraeger-Liste>
        <n1:Endenergiebedarf-Waerme-AN>20</n1:Endenergiebedarf-Waerme-AN>
        <n1:Endenergiebedarf-Hilfsenergie-AN>10</n1:Endenergiebedarf-Hilfsenergie-AN>
        <n1:Endenergiebedarf-Gesamt>100</n1:Endenergiebedarf-Gesamt>
        <n1:Primaerenergiebedarf>20</n1:Primaerenergiebedarf>
        <n1:Energieeffizienzklasse>A+</n1:Energieeffizienzklasse>
        <n1:Art-der-Nutzung-erneuerbaren-Energie-1>feste Biomasse</n1:Art-der-Nutzung-erneuerbaren-Energie-1>
        <n1:Deckungsanteil-1>20</n1:Deckungsanteil-1>
        <n1:Anteil-der-Pflichterfuellung-1>10</n1:Anteil-der-Pflichterfuellung-1>
        <n1:verschaerft-nach-GEG-34>20</n1:verschaerft-nach-GEG-34>
        <n1:Anforderung-nach-GEG-16-unterschritten>1</n1:Anforderung-nach-GEG-16-unterschritten>
        <n1:spezifischer-Transmissionswaermeverlust-verschaerft>25</n1:spezifischer-Transmissionswaermeverlust-verschaerft>
        <n1:Sommerlicher-Waermeschutz>true</n1:Sommerlicher-Waermeschutz>
      </n1:Bedarfswerte-4108-4701>
    </n1:Wohngebaeude>
    <n1:Empfehlungen-moeglich>true</n1:Empfehlungen-moeglich>
    <n1:Modernisierung-Erweiterung-anzeigepflichtiges-Vorhaben>true</n1:Modernisierung-Erweiterung-anzeigepflichtiges-Vorhaben>
    <n1:Modernisierungsempfehlungen>
      <n1:Nummer>1</n1:Nummer>
      <n1:Bauteil-Anlagenteil>Fenster</n1:Bauteil-Anlagenteil>
      <n1:Massnahmenbeschreibung>ersetzen...</n1:Massnahmenbeschreibung>
      <n1:Modernisierungskombination>in Zusammenhang mit größerer Modernisierung</n1:Modernisierungskombination>
      <n1:Amortisation>ein Monat</n1:Amortisation>
      <n1:spezifische-Kosten>...</n1:spezifische-Kosten>
    </n1:Modernisierungsempfehlungen>
    <n1:Softwarehersteller-Programm-Version>Muster XML Bedarfsausweis 4108/4701 Nichtwohngebäude</n1:Softwarehersteller-Programm-Version>
  </n1:Energieausweis-Daten>
</n1:GEG-Energieausweis>