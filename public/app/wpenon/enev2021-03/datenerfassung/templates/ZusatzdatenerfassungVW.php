<?php

require dirname( __FILE__ ) . '/DataEnevVW.php';

$data = new DataEnevVW( $energieausweis );

?><n1:GEG-Energieausweis xmlns:n1="https://energieausweis.dibt.de/schema/Kontrollsystem-GEG-2020_V1_0.xsd">
  <n1:Energieausweis-Daten Gesetzesgrundlage="GEG-2020" Rechtsstand-Grund="<?php echo $data->RechtsstandGrund(); ?>" Rechtsstand="2020-08-08">
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
    <n1:Erneuerbare-Verwendung><?php echo $data->ErneuerbareVerwendung(); ?><</n1:Erneuerbare-Verwendung>
    <n1:Lueftungsart-Fensterlueftung><?php echo $data->LueftungsartFensterlueftung(); ?></n1:Lueftungsart-Fensterlueftung>
    <n1:Lueftungsart-Schachtlueftung><?php echo $data->LueftungsartSchachtlueftung(); ?></n1:Lueftungsart-Schachtlueftung>
    <n1:Lueftungsart-Anlage-o-WRG><?php echo $data->LueftungsartAnlageOWRG(); ?></n1:Lueftungsart-Anlage-o-WRG>
    <n1:Lueftungsart-Anlage-m-WRG><?php echo $data->LueftungsartAnlageMWRG(); ?></n1:Lueftungsart-Anlage-m-WRG>
    <n1:Kuehlungsart-passive-Kuehlung><?php echo $data->KuehlungsartPassiveKuehlung(); ?></n1:Kuehlungsart-passive-Kuehlung>
    <n1:Kuehlungsart-Strom><?php echo $data->KuehlungsartStrom(); ?></n1:Kuehlungsart-Strom>
    <n1:Kuehlungsart-Waerme><?php echo $data->KuehlungsartWaerme(); ?></n1:Kuehlungsart-Waerme>
    <n1:Kuehlungsart-gelieferte-Kaelte><?php echo $data->KuehlungsartGelieferteKaelte(); ?></n1:Kuehlungsart-gelieferte-Kaelte>
    <n1:Anzahl-Klimanlagen>1</n1:Anzahl-Klimanlagen>
    <n1:Anlage-groesser-12kW-ohneGA>false</n1:Anlage-groesser-12kW-ohneGA>
    <n1:Anlage-groesser-12kW-mitGA>false</n1:Anlage-groesser-12kW-mitGA>
    <n1:Anlage-groesser-70kW>false</n1:Anlage-groesser-70kW>
    <n1:Faelligkeitsdatum-Inspektion>2022-01-01</n1:Faelligkeitsdatum-Inspektion>
    <n1:Treibhausgasemissionen>100</n1:Treibhausgasemissionen>
    <n1:Ausstellungsanlass>Sonstiges</n1:Ausstellungsanlass>
    <n1:Datenerhebung-Aussteller>false</n1:Datenerhebung-Aussteller>
    <n1:Datenerhebung-Eigentuemer>false</n1:Datenerhebung-Eigentuemer>
    <n1:Wohngebaeude>
      <n1:Gebaeudetyp>Einfamilienhaus</n1:Gebaeudetyp>
      <n1:Anzahl-Wohneinheiten>5</n1:Anzahl-Wohneinheiten>
      <n1:Gebaeudenutzflaeche>360</n1:Gebaeudenutzflaeche>
      <n1:Verbrauchswerte>
        <n1:Flaechenermittlung-AN-aus-Wohnflaeche>true</n1:Flaechenermittlung-AN-aus-Wohnflaeche>
        <n1:Wohnflaeche>300</n1:Wohnflaeche>
        <n1:Keller-beheizt>true</n1:Keller-beheizt>
        <n1:Energietraeger>
          <n1:Energietraeger-Verbrauch>Biogas, gebäudenah erzeugt in kWh Heizwert</n1:Energietraeger-Verbrauch>
          <n1:Unterer-Heizwert>1.00</n1:Unterer-Heizwert>
          <n1:Primaerenergiefaktor>1.1</n1:Primaerenergiefaktor>
          <n1:Emissionsfaktor>270</n1:Emissionsfaktor>
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
        <n1:Mittlerer-Endenergieverbrauch>198.6</n1:Mittlerer-Endenergieverbrauch>
        <n1:Mittlerer-Primaerenergieverbrauch>236</n1:Mittlerer-Primaerenergieverbrauch>
        <n1:Energieeffizienzklasse>F</n1:Energieeffizienzklasse>
      </n1:Verbrauchswerte>
    </n1:Wohngebaeude>
    <n1:Empfehlungen-moeglich>true</n1:Empfehlungen-moeglich>
    <n1:Modernisierung-Erweiterung-genehmigungspflichtiges-Vorhaben>true</n1:Modernisierung-Erweiterung-genehmigungspflichtiges-Vorhaben>
    <n1:Modernisierungsempfehlungen>
      <n1:Nummer>1</n1:Nummer>
      <n1:Bauteil-Anlagenteil>Fenster</n1:Bauteil-Anlagenteil>
      <n1:Massnahmenbeschreibung>ersetzen...</n1:Massnahmenbeschreibung>
      <n1:Modernisierungskombination>in Zusammenhang mit größerer Modernisierung</n1:Modernisierungskombination>
      <n1:Amortisation>ein Monat</n1:Amortisation>
      <n1:spezifische-Kosten>...</n1:spezifische-Kosten>
    </n1:Modernisierungsempfehlungen>    
    <n1:Softwarehersteller-Programm-Version>Muster XML Verbrauchsausweis Wohngebäude</n1:Softwarehersteller-Programm-Version>
  </n1:Energieausweis-Daten>
</n1:GEG-Energieausweis>
