<?php

require dirname( dirname( __FILE__ ) ) . '/data/DataEnevVW.php';

$data = new DataEnevVW( $energieausweis );

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
          <n1:Zeitraum>
            <n1:Startdatum>2018-01-01</n1:Startdatum>
            <n1:Enddatum>2018-12-31</n1:Enddatum>
            <n1:Verbrauchte-Menge>60979</n1:Verbrauchte-Menge>
            <n1:Energieverbrauch>20000</n1:Energieverbrauch>
            <n1:Energieverbrauchsanteil-Warmwasser-zentral>50</n1:Energieverbrauchsanteil-Warmwasser-zentral>
            <n1:Warmwasserwertermittlung>Rechenwert nach Heizkostenverordnung (Wohngebäude)</n1:Warmwasserwertermittlung>
            <n1:Energieverbrauchsanteil-Heizung>20</n1:Energieverbrauchsanteil-Heizung>
            <n1:Klimafaktor>1.24</n1:Klimafaktor>
            <n1:Verbrauchswert-kWh-Strom>20000</n1:Verbrauchswert-kWh-Strom>
          </n1:Zeitraum>
          <n1:Zeitraum>
            <n1:Startdatum>2019-01-01</n1:Startdatum>
            <n1:Enddatum>2019-12-31</n1:Enddatum>
            <n1:Verbrauchte-Menge>64003</n1:Verbrauchte-Menge>
            <n1:Energieverbrauch>20000</n1:Energieverbrauch>
            <n1:Energieverbrauchsanteil-Warmwasser-zentral>0</n1:Energieverbrauchsanteil-Warmwasser-zentral>
            <n1:Warmwasserwertermittlung>keine Warmwasserbereitung enthalten</n1:Warmwasserwertermittlung>
            <n1:Energieverbrauchsanteil-Heizung>20</n1:Energieverbrauchsanteil-Heizung>
            <n1:Klimafaktor>1.24</n1:Klimafaktor>
            <n1:Verbrauchswert-kWh-Strom>20000</n1:Verbrauchswert-kWh-Strom>
          </n1:Zeitraum>
          <n1:Zeitraum>
            <n1:Startdatum>2020-01-01</n1:Startdatum>
            <n1:Enddatum>2020-12-31</n1:Enddatum>
            <n1:Verbrauchte-Menge>51426</n1:Verbrauchte-Menge>
            <n1:Energieverbrauch>20000</n1:Energieverbrauch>
            <n1:Energieverbrauchsanteil-Warmwasser-zentral>0</n1:Energieverbrauchsanteil-Warmwasser-zentral>
            <n1:Warmwasserwertermittlung>keine Warmwasserbereitung enthalten</n1:Warmwasserwertermittlung>
            <n1:Energieverbrauchsanteil-Heizung>20</n1:Energieverbrauchsanteil-Heizung>
            <n1:Klimafaktor>1.24</n1:Klimafaktor>
            <n1:Verbrauchswert-kWh-Strom>20000</n1:Verbrauchswert-kWh-Strom>
          </n1:Zeitraum>
        </n1:Energietraeger>
        <?php endforeach; ?>
        <n1:Leerstandszuschlag-Heizung>
          <n1:kein-Leerstand>Kein längerer Leerstand Heizung zu berücksichtigen.</n1:kein-Leerstand>
        </n1:Leerstandszuschlag-Heizung>
        
        <n1:Leerstandszuschlag-Warmwasser>
          <n1:keine-Nutzung-von-WW>true</n1:keine-Nutzung-von-WW>
          <n1:kein-Leerstand>Kein längerer Leerstand Warmwasser zu berücksichtigen.</n1:kein-Leerstand>
        </n1:Leerstandszuschlag-Warmwasser>

        <n1:Warmwasserzuschlag>
          <n1:Startdatum>2018-04-01</n1:Startdatum>
          <n1:Enddatum>2018-06-30</n1:Enddatum>
          <n1:Primaerenergiefaktor>1.1</n1:Primaerenergiefaktor>
          <n1:Warmwasserzuschlag-kWh>21672</n1:Warmwasserzuschlag-kWh>
        </n1:Warmwasserzuschlag>
        
        <n1:Kuehlzuschlag>
          <n1:Startdatum>2018-07-01</n1:Startdatum>
          <n1:Enddatum>2018-07-15</n1:Enddatum>
          <n1:Gebaeudenutzflaeche-gekuehlt>100</n1:Gebaeudenutzflaeche-gekuehlt>
          <n1:Primaerenergiefaktor>1.3</n1:Primaerenergiefaktor>
          <n1:Kuehlzuschlag-kWh>600</n1:Kuehlzuschlag-kWh>
        </n1:Kuehlzuschlag>

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
