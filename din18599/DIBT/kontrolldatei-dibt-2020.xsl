<?xml version="1.0" encoding="UTF-8"?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema"
  xmlns:n1="https://energieausweis.dibt.de/schema/Kontrollsystem-GEG-2020_V1_0.xsd"
  targetNamespace="https://energieausweis.dibt.de/schema/Kontrollsystem-GEG-2020_V1_0.xsd"
  elementFormDefault="qualified" attributeFormDefault="unqualified">
  <xs:annotation>
    <xs:documentation xml:lang="DE"> Die Verwendung des speziellen Namespace ermöglicht die
      eindeutige Versionszuordnung. Ausgabestand: 02.06.2021 Hinzugefügt:
      Treibhausgasemissionen-Zusaetzliche-Verbrauchsdaten, Wert'Dezentrale Kraft-Wärme-Kopplung,
      Systeme mit Brennstoffzellen' bei Trinkwarmwassererzeuger-Typ-18599-enum
      Dokumentationsanpassung bei: Primaerenergiebedarf-Hoechstwert-Bestand,
      Endenergiebedarf-Hoechstwert-Bestand, Treibhausgasemissionen-Hoechstwert-Bestand,
      Modernisierung-Erweiterung-anzeigepflichtiges-Vorhaben,
      Modernisierung-Erweiterung-genehmigungspflichtiges-Vorhaben,
      angerechneter-lokaler-erneuerbarer-Strom </xs:documentation>
  </xs:annotation>
  <xs:element name="GEG-Energieausweis" type="n1:GEG-Daten" />
  <xs:complexType name="GEG-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block besteht aus drei Komponenten: Dem Block mit den
        gebäudebezogenen Daten (nur relevant zwischen Energieausweis-Berechnungssoftware und
        Druckapplikation), dem Block mit den anonymen Energieausweis-Daten sowie dem Block mit der
        Prüfsumme, die nach Erhalt und Eintragung der Registriernummer über den Block der anonymen
        Daten (ohne die gebäudebezogenen Daten!) gebildet wird. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Gebaeudebezogene-Daten" type="n1:Gebaeudebez-Daten" minOccurs="0"
        maxOccurs="1" />
      <xs:element name="Energieausweis-Daten" type="n1:anonyme-GEG-Daten" minOccurs="1"
        maxOccurs="1" />
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="Gebaeudebez-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Block, der alle Daten für die Ausstellung des
        Energieausweises entsprechend GEG enthält, die dem Datenschutz unterliegen oder freiwillig
        sind, und daher für das Kontrollsystem nicht relevant sind. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Projektbezeichnung-Aussteller" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Interne Bezeichnung oder Nummer beim Aussteller, anhand
            derer er die Daten/Unterlagen zu diesem Energieausweis wiederfinden kann. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{0,1024}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Gebaeudeadresse-Strasse-Nr" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Straße und Hausnummer der Gebäudeadresse zur Angabe im
            Energieausweis. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{1,60}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Gebaeudeadresse-Postleitzahl" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Vollständige Postleitzahl der Gebäudeadresse zur Angabe
            im Energieausweis. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="()|(\d{5})" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Gebaeudeadresse-Ort" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Ortsangabe der Gebäudeadresse zur Angabe im
            Energieausweis. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{1,35}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Ausstellervorname" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Vorname des Energieausweis-Ausstellers wie bei der
            Ausstelleranmeldung hinterlegt. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{1,60}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Ausstellername" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Familienname des Energieausweis-Ausstellers wie bei der
            Ausstelleranmeldung hinterlegt. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{1,60}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Aussteller-Bezeichnung" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Firmierung und/oder Berufsbezeichnung des
            Energieausweis-Ausstellers. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{0,60}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Aussteller-Strasse-Nr" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Straße und Hausnummer des Energieausweis-Ausstellers. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{0,60}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Aussteller-PLZ" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Postleitzahl des Energieausweis-Ausstellers (ggfs. bei
            Ausstellern aus dem Ausland mit Landeszennzeichnung usw.) </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{0,5}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Aussteller-Ort" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Ort des Energieausweis-Ausstellers. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{0,35}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Zusatzinfos-beigefuegt" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Kreuzchen, ob dem Energieausweis (freiwillige)
            zusätzliche Informationen zur energetischen Qualität beigefügt sind. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Angaben-erhaeltlich" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Hinweis, wo genauere Angaben zu den Empfehlungen ggfs.
            erhältlich sind. Pflichtangabe, Default-Wert ist (wenn Modernisierungsempfehlungen
            möglich sind) die GEG-Infoseite des BBSR bzw. (wenn keine Modernisierungsempfehlungen
            möglich sind, z.B. nach Modernisierung oder bei Neubau) "Angabe hier nicht relevant",
            weitere / alternative Hinweise sind dem Energieausweis-Aussteller überlassen. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{5,200}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Ergaenzdende-Erlaeuterungen" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Freitext-Platz für zusätzliche Erläuterungen zum gesamten
            Energieausweis. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:maxLength value="2500" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="NWG-Diagramm-Daten" type="n1:NWG-Aushang-Daten" minOccurs="0" maxOccurs="1" />
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="anonyme-GEG-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Block, der alle Daten für die Ausstellung des
        Energieausweises entsprechend GEG enthält, außer den dem Datenschutz unterliegenden und den
        freiwilligen Angaben. Nach Erhalt und Eintragung der Registriernummer muss dieser Block für
        die Kontrolle bereitgehalten werden. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Registriernummer" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Offizielle Registriernummer des Energieausweises; solange
            sie noch nicht beantragt ist, bleibt der Inhalt leer, wenn sie beantragt ist aber noch
            nicht übermittelt wurde, wird zwar das Datum des Antrags ggfs. im vorläufigen
            Energieausweis eingedruckt, das Feld bleibt hier aber trotzdem leer. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="[A-Z]{2}\-20\d{2}\-\d{9}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Ausstellungsdatum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Ausstellungsdatum des Energieausweises, angegeben als
            YYYY-MM-DD. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:date">
            <xs:minInclusive value="2000-01-01" />
            <xs:maxInclusive value="2100-01-01" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Bundesland" type="n1:Bundesland-enum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Bundesland, in dem das Gebäude des Energieausweises
            steht. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Postleitzahl" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Postleitzahl des Gebäudestandortes, aus
            Datenschutzgründen eingekürzt. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="\d{3}XX" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Gebaeudeteil" type="n1:Gebaeudeteil-enum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Ergänzende Angabe bei Wohnteilen oder Nichtwohnteilen von
            gemischt genutzten Gebäuden gemäß §79 Abs. 2 GEG; bei Einzelgebäude Angabe "Ganzes
            Gebäude". </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Baujahr-Gebaeude" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Baujahr des Gebäudes und ggfs. Hinweise auf nachträgliche
            Umbauten, Erweiterungen, Modernisierungen usw. . </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="[\w].{1,64}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Altersklasse-Gebaeude" type="n1:Altersklasse-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Altersklasse der ursprünglichen Errichtung des Gebäudes </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Baujahr-Waermeerzeuger" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Baujahr des/der Wärmeerzeuger(s) und ggfs. Hinweise auf
            nachträgliche bauliche Veränderung des/der Wärmeerzeuger(s). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="[\w].{1,117}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Altersklasse-Waermeerzeuger" type="n1:Altersklasse-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Altersklasse der ältesten der energetisch wesentlichsten
            Komponenten der Wärmeerzeugung. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="wesentliche-Energietraeger-Heizung" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wesentliche Energieträger für die Heizung. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="[\w].{1,94}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="wesentliche-Energietraeger-Warmwasser" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wesentliche Energieträger für die Warmwasserbereitung. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="[\w].{1,94}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Erneuerbare-Art" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Art(en) der erneuerbaren Energie(n), die eingesetzt
            wird/werden; wenn nicht gegeben bitte "keine" eintragen. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="[\w].{1,63}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Erneuerbare-Verwendung" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Verwendung der erneuerbaren Energie(n), die eingesetzt
            wird/werden; wenn nicht gegeben bitte "keine" eintragen. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="[\w].{1,74}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Lueftungsart-Fensterlueftung" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Art der Lüftung des Gebäudes: Fensterlüftung. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Lueftungsart-Schachtlueftung" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Art der Lüftung des Gebäudes: Schachtlüftung. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Lueftungsart-Anlage-o-WRG" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Art der Lüftung des Gebäudes: Lüftungsanlage ohne
            Wärmerückgewinnung. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Lueftungsart-Anlage-m-WRG" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Art der Lüftung des Gebäudes: Lüftungsanlage mit
            Wärmerückgewinnung. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Kuehlungsart-passive-Kuehlung" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Art der Kühlung des Gebäudes: Passive Kühlung. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Kuehlungsart-Strom" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Art der Kühlung des Gebäudes: Kühlung aus Strom. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Kuehlungsart-Waerme" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Art der Kühlung des Gebäudes: Kühlung aus Wärme. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Kuehlungsart-gelieferte-Kaelte" type="xs:boolean" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Art der Kühlung des Gebäudes: Gelieferte Kälte. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:choice>
        <xs:element name="Keine-inspektionspflichtige-Anlage" type="xs:boolean" minOccurs="1"
          maxOccurs="1" fixed="true">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Keine inspektionspflichtige Klimaanlage. </xs:documentation>
          </xs:annotation>
        </xs:element>
        <xs:sequence>
          <xs:element name="Anzahl-Klimanlagen" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Anzahl inspektionspflichtiger Klimaanlagen. </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:int">
                <xs:minInclusive value="1" />
                <xs:maxInclusive value="1000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
          <xs:element name="Anlage-groesser-12kW-ohneGA" type="xs:boolean" minOccurs="1"
            maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Klimaanlage größer als 12 kW ohne Gebäudeautomation
                (inspektionspflichtig). </xs:documentation>
            </xs:annotation>
          </xs:element>
          <xs:element name="Anlage-groesser-12kW-mitGA" type="xs:boolean" minOccurs="1"
            maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Klimaanlage größer als 12 kW mit Gebäudeautomation
                (inspektionspflichtig). </xs:documentation>
            </xs:annotation>
          </xs:element>
          <xs:element name="Anlage-groesser-70kW" type="xs:boolean" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Klimaanlage größer als 70 kW, nach DIN SPEC 15240 zu
                bewerten. </xs:documentation>
            </xs:annotation>
          </xs:element>
          <xs:element name="Faelligkeitsdatum-Inspektion" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Nächstes Fälligkeitsdatum der Inspektion. </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:date">
                <xs:minInclusive value="2000-01-01" />
                <xs:maxInclusive value="2100-01-01" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
        </xs:sequence>
      </xs:choice>
      <xs:element name="Treibhausgasemissionen" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Pflichtangabe der Treibhausgasemissionen in kg als CO2
            Äquivalent/(m²a) für Bedarfsausweise, Verbrauchsausweise und Aushänge (gemäß GEG § 85
            (2), (3) und (6) bzw. nach Berechnungsregeln gem. Anlage 9). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="8" />
            <xs:minInclusive value="-100000" />
            <xs:maxInclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Ausstellungsanlass" type="n1:Anlass-enum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anlass der Ausstellung des Energieausweises. Treffen
            mehrere zu, so ist das Kreuz in der Rangfolge Neubau vor Modernisierung vor
            Vermietung/Verkauf vor Aushangpflicht zu sezten. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Datenerhebung-Aussteller" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Die Datenerhebung erfolgte teilweise oder ganz durch den
            Aussteller. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Datenerhebung-Eigentuemer" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Die Datenerhebung erfolgte teilweise oder ganz durch den
            Eigentümer. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:choice>
        <xs:element name="Wohngebaeude" type="n1:Wohngebaeude-Daten" />
        <xs:element name="Nichtwohngebaeude" type="n1:Nichtwohngebaeude-Daten" />
      </xs:choice>
      <xs:element name="Empfehlungen-moeglich" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wenn Empfehlungen möglich sind, ist das Kreuz im
            Energieausweis entsprechend zu setzen, und es muss im Tag "Modernisierungsempfehlungen"
            mindestens eine Modernisierungsempfehlungen gegeben werden. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:choice>
        <xs:element name="Keine-Modernisierung-Erweiterung-Vorhaben" type="xs:boolean" minOccurs="1"
          maxOccurs="1">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Der Austellungsanlass ist anders als
              "Modernisierung-Erweiterung", also: Neubau, Vermietung-Verkauf, Aushangspflicht oder
              sonstiges. </xs:documentation>
          </xs:annotation>
        </xs:element>
        <xs:element name="Modernisierung-Erweiterung-anzeigepflichtiges-Vorhaben" type="xs:boolean"
          minOccurs="1" maxOccurs="1">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Austellungsanlass Modernisierung (140% Nachweis) nach §
              50 (1). </xs:documentation>
          </xs:annotation>
        </xs:element>
        <xs:element name="Modernisierung-Erweiterung-genehmigungspflichtiges-Vorhaben"
          type="xs:boolean" minOccurs="1" maxOccurs="1">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Austellungsanlass Modernisierung (140% Nachweis) nach §
              50 (1). </xs:documentation>
          </xs:annotation>
        </xs:element>
      </xs:choice>
      <xs:element name="Modernisierungsempfehlungen" type="n1:Modernisierungszeile" minOccurs="0"
        maxOccurs="30">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Die Modernisierungsempfehlungen - wenn entsprechend Tag
            "Empfehlungen-moeglich" welche angegeben werden, sind hier nach Bauteilen aufgelistet
            anzugeben. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Softwarehersteller-Programm-Version" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe, mit welcher Software die Berechnungen erfolgten
            (Herstellername, Programmbezeichnung, Programm-Version). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{0,1024}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
    <xs:attribute name="Gesetzesgrundlage" type="n1:Gesetzesgrundlage-enum" use="required">
      <xs:annotation>
        <xs:documentation xml:lang="DE"> Bezeichnet die Gesetzesgrundlage (z.B. GEG-Version), nach
          der Nachweis geführt wurde. </xs:documentation>
      </xs:annotation>
    </xs:attribute>
    <xs:attribute name="Rechtsstand" type="xs:date" use="required">
      <xs:annotation>
        <xs:documentation xml:lang="DE"> Entsprechend der Angabe des Rechtsstand-Datums für entweder
          - den Bauantrag bei genehmigungspflichtigen Vorhaben (Ausstellungsanlässe Neubau und
          Modernisierung (über 140% Verbesserung der Endeenergie)) oder - die Bauanzeige bei
          anzeigepflichtigen Vorhaben (Ausstellungsanlässe Neubau und Modernisierung (bis 140%
          Verbesserung der Endenergie ) oder - den Baubeginn (Ausstellungsanlass Modernisierung)
          oder - bei Wunsch des Bauherrn das neue Recht für alle oben genannten Fälle anzuwenden,
          das Datum der Wunschäußerung bei der zuständigen Stelle oder - die Ausweisausstellung (bei
          Verbrauchsausweisen und alle anderen Fälle). </xs:documentation>
      </xs:annotation>
    </xs:attribute>
    <xs:attribute name="Rechtsstand-Grund" type="n1:Rechtsstand-Grund-enum" use="required">
      <xs:annotation>
        <xs:documentation xml:lang="DE"> Bezeichnet die GEG-Version, nach der der Nachweis geführt
          wurde; es sollten nur die Jahreszahlen des Inkrafttretens der jeweiligen GEG-Version
          verwendet werden. </xs:documentation>
      </xs:annotation>
    </xs:attribute>
  </xs:complexType>
  <xs:simpleType name="Gesetzesgrundlage-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="GEG-2020" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Rechtsstand-Grund-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration
        value="Bauantrag bei genehmigungspflichtigen Vorhaben (Ausstellungsanlässe Neubau und Modernisierung)" />
      <xs:enumeration
        value="Bauanzeige bei anzeigepflichtigen Vorhaben (Ausstellungsanlässe Neubau und Modernisierung)" />
      <xs:enumeration value="Baubeginn (Ausstellungsanlass Modernisierung)" />
      <xs:enumeration value="Wunsch des Bauherrn neues Recht anzuwenden (gemäß GEG § 111 Absatz 3)" />
      <xs:enumeration value="Ausweisausstellung (bei Verbrauchsausweisen und alle anderen Fälle)" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Bundesland-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Baden-Württemberg" />
      <xs:enumeration value="Bayern" />
      <xs:enumeration value="Berlin" />
      <xs:enumeration value="Brandenburg" />
      <xs:enumeration value="Bremen" />
      <xs:enumeration value="Hamburg" />
      <xs:enumeration value="Hessen" />
      <xs:enumeration value="Mecklenburg-Vorpommern" />
      <xs:enumeration value="Niedersachsen" />
      <xs:enumeration value="Nordrhein-Westfalen" />
      <xs:enumeration value="Rheinland-Pfalz" />
      <xs:enumeration value="Saarland" />
      <xs:enumeration value="Sachsen" />
      <xs:enumeration value="Sachsen-Anhalt" />
      <xs:enumeration value="Schleswig-Holstein" />
      <xs:enumeration value="Thüringen" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Gebaeudeteil-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Ganzes Gebäude" />
      <xs:enumeration value="Teil des Wohngebäudes" />
      <xs:enumeration value="Teil des Nichtwohngebäudes" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Altersklasse-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="bis 1918" />
      <xs:enumeration value="1919...1948" />
      <xs:enumeration value="1949...1957" />
      <xs:enumeration value="1958...1968" />
      <xs:enumeration value="1969...1978" />
      <xs:enumeration value="1979...1983" />
      <xs:enumeration value="1984...1994" />
      <xs:enumeration value="1995...2002" />
      <xs:enumeration value="2003...2009" />
      <xs:enumeration value="2010...2016" />
      <xs:enumeration value="ab 2017" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Anlass-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Neubau" />
      <xs:enumeration value="Modernisierung-Erweiterung" />
      <xs:enumeration value="Vermietung-Verkauf" />
      <xs:enumeration value="Aushangpflicht" />
      <xs:enumeration value="Sonstiges" />
    </xs:restriction>
  </xs:simpleType>
  <xs:complexType name="Wohngebaeude-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält im Falle eines Wohngebäudes alle
        weiteren energetisch relevanten Daten. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Gebaeudetyp" type="n1:Gebaeudetyp-enum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Grundsätzlicher Gebäudetypus des Gebäudes bzw.
            Gebäudeteils; die Angaben EFH mit ELW und Zweifamilienhaus sind gleichwertig und können
            je nach ortsüblichem Gebrauch oder Bauhistorie usw. verwendet werden. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Anzahl-Wohneinheiten" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anzahl der Wohneinheiten im Gebäude/Gebäudeteil. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Gebaeudenutzflaeche" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Energetische Nutzfläche AN des Gebäudes/Gebäudeteils
            (ganze Quadratmeter) (Gebäudenutzfläche AN). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:choice>
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Dieser Block enthält entweder Verbrauchsdaten oder
            Bedarfswerte; beim Bedarfsausweis können zusätzliche Verbrauchsangaben mit angeführt
            werden. </xs:documentation>
        </xs:annotation>
        <xs:element name="Verbrauchswerte" type="n1:Wohngebaeude-Verbrauchs-Daten" />
        <xs:element name="Bedarfswerte-easy" type="n1:Wohngebaeude-Bedarfs-Daten-easy" />
        <xs:element name="Bedarfswerte-4108-4701" type="n1:Wohngebaeude-Bedarfs-Daten-4108-4701" />
        <xs:element name="Bedarfswerte-18599" type="n1:Wohngebaeude-Bedarfs-Daten-18599" />
      </xs:choice>
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="Gebaeudetyp-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Einfamilienhaus" />
      <xs:enumeration value="Zweifamilienhaus" />
      <xs:enumeration value="Mehrfamilienhaus" />
      <xs:enumeration value="Wohnteil gemischt genutztes Gebäude" />
      <xs:enumeration value="Sonstiges" />
    </xs:restriction>
  </xs:simpleType>
  <xs:complexType name="Wohngebaeude-Verbrauchs-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält im Falle eines
        Wohngebäude-Verbrauchsausweises die Verbrauchsdaten. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Flaechenermittlung-AN-aus-Wohnflaeche" type="xs:boolean" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Ermittlung der Energiebezugsfläche aus der Wohnfläche mit
            amtlichem Umrechnungsfaktor nach § 82 GEG. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Wohnflaeche" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wohnfläche des Gebäudes/Gebäudeteils (ganze
            Quadratmeter), nur erforderlich bei Ermittlung der energ. Nutzfläche aus der Wohnfläche. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="1000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Keller-beheizt" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wenn die Energiebezugsfläche aus der Wohnfläche mit
            amtlichem Umrechnungsfaktor ermittelt wurde, Angabe, ob Ein-/Zweifamilienhaus mit
            beheiztem Keller (und daher erhöhter Umrechnungsfaktor) oder nicht. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:boolean" />
        </xs:simpleType>
      </xs:element>
      <xs:element name="Energietraeger" type="n1:Energietraeger-Daten" minOccurs="1" maxOccurs="8">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Jeweiliger Energieträger mit zugehörigen Verbrauchsdaten </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Leerstandszuschlag-Heizung" type="n1:Leerstandszuschlag-Heizung-Daten">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Leerstandzuschlag für Heizung gemäß GEG § 82 und
            gemeinsamen Bekanntmachung des Bundesministeriums für Wirtschaft und Energie und des
            Bundesministerium für Umwelt, Naturschutz, Bau und Reaktorsicherheit. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Leerstandszuschlag-Warmwasser" type="n1:Leerstandszuschlag-Warmwasser-Daten">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Leerstandzuschlag für Warmwasser gemäß GEG § 82 und
            gemeinsamen Bekanntmachung des Bundesministeriums für Wirtschaft und Energie und des
            Bundesministerium für Umwelt, Naturschutz, Bau und Reaktorsicherheit. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Warmwasserzuschlag" type="n1:Warmwasserzuschlag-Daten" minOccurs="0"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Im Fall dezentraler Warmwasserbereitung erforderliche
            Angaben für den Zuschlag zur Einfügung in die Verbauchsliste des Energieausweises gemäß
            GEG § 82 und gemeinsamen Bekanntmachung des Bundesministeriums für Wirtschaft und
            Energie und des Bundesministerium für Umwelt, Naturschutz, Bau und Reaktorsicherheit; in
            der Tabelle ist statt des Energieträgers das Wort "Warmwasserzuschlag" einzusetzen, die
            Felder Anteil Kälte, Anteil Heizung und Klimafaktor bleiben in dieser Zeile leer. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Kuehlzuschlag" type="n1:Kuehlzuschlag-Daten" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Im Falle eines maschinell gekühlten Wohngebäudes
            erforderliche Angaben für den Zuschlag zur Einfügung in die Verbrauchsliste des
            Energieausweises; in der Tabelle ist statt des Energieträgers das Wort "Kühlzuschlag"
            einzusetzen, die Felder Anteil Warmwasser, Anteil Heizung und Klimafaktor bleiben in
            dieser Zeile leer. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Mittlerer-Endenergieverbrauch" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiekennwert in kWh/m²a bezogen auf die
            Energiebezugsfläche (Mittlerer Endenergieverbrauch: e(Strich)_Vb,12mth). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Mittlerer-Primaerenergieverbrauch" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiekennwert in kWh/m²a bezogen auf die
            Energiebezugsfläche. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Energieeffizienzklasse" type="n1:Energieeffizienzklasse-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Energieeffizienzklasse des Gebäudes. </xs:documentation>
        </xs:annotation>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="Energietraeger-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Block, der einen Energieträger mit seinen einzelnen
        Verbrauchsperioden enthält. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:choice>
        <xs:element name="Energietraeger-Verbrauch" type="n1:Energietraeger-Verbrauch-enum"
          minOccurs="1" maxOccurs="1">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Bezeichnung und Verbrauchsmengen-Einheit des
              Energieträgers, zu dem die anschliessenden Verbrauchswerte gehören. </xs:documentation>
          </xs:annotation>
        </xs:element>
        <xs:element name="Sonstiger-Energietraeger-Verbrauch" minOccurs="1" maxOccurs="1">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Bezeichnung und Verbrauchsmengen-Einheit des
              Energieträgers, zu dem die anschliessenden Verbrauchswerte gehören, wenn ein nicht
              aufgelisteter Energieträger vorliegt. </xs:documentation>
          </xs:annotation>
          <xs:simpleType>
            <xs:restriction base="xs:string">
              <xs:pattern value="[\w].{1,39}" />
            </xs:restriction>
          </xs:simpleType>
        </xs:element>
      </xs:choice>
      <xs:element name="Unterer-Heizwert" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Umrechnung der erfassten verbrauchten Menge des
            Energieträgers in Energieverbrauch in kWh_Heizwert, wenn schon in kWh_Heizwert
            angegeben, dann "1,0" (Unterer Heizwert: H_i). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:minInclusive value="0" />
            <xs:totalDigits value="6" />
            <xs:maxInclusive value="2500" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Primaerenergiefaktor" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Verwendeter Primärenergiefaktor des Energieträgers
            (Primärenergiefaktor: f_p) entsprechend Anlage 4 GEG. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="3" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Emissionsfaktor" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Emissionsfaktor für den jeweils eingesetzen Energieträger
            zur Umrechnung in Treibhausemissionen gemäß Anlage 9 des GEG. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Zeitraum" type="n1:Zeitraum-Daten" minOccurs="1" maxOccurs="40" />
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="Energietraeger-Verbrauch-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Heizöl in Liter" />
      <xs:enumeration value="Heizöl in kWh Heizwert" />
      <xs:enumeration value="Heizöl in kWh Brennwert" />
      <xs:enumeration value="Erdgas in m³" />
      <xs:enumeration value="Erdgas in kWh Heizwert" />
      <xs:enumeration value="Erdgas in kWh Brennwert" />
      <xs:enumeration value="Flüssiggas in m³ gasförmig" />
      <xs:enumeration value="Flüssiggas in Liter flüssig" />
      <xs:enumeration value="Flüssiggas in kg" />
      <xs:enumeration value="Steinkohle in kg" />
      <xs:enumeration value="Steinkohle in kWh Heizwert" />
      <xs:enumeration value="Braunkohle in kg" />
      <xs:enumeration value="Braunkohle in kWh Heizwert" />
      <xs:enumeration value="Biogas in m³" />
      <xs:enumeration value="Biogas in kWh Heizwert" />
      <xs:enumeration value="Biogas in kWh Brennwert" />
      <xs:enumeration value="Biogas, gebäudenah erzeugt in m³" />
      <xs:enumeration value="Biogas, gebäudenah erzeugt in kWh Heizwert" />
      <xs:enumeration value="Biogas, gebäudenah erzeugt in kWh Brennwert" />
      <xs:enumeration value="biogenes Flüssiggas in m³ gasförmig" />
      <xs:enumeration value="biogenes Flüssiggas in Liter flüssig" />
      <xs:enumeration value="biogenes Flüssiggas in kg" />
      <xs:enumeration value="Bioöl in Liter" />
      <xs:enumeration value="Bioöl in kWh Heizwert" />
      <xs:enumeration value="Bioöl in kWh Brennwert" />
      <xs:enumeration value="Bioöl, gebäudenah erzeugt in Liter" />
      <xs:enumeration value="Bioöl, gebäudenah erzeugt in kWh Heizwert" />
      <xs:enumeration value="Bioöl, gebäudenah erzeugt in kWh Brennwert" />
      <xs:enumeration value="Holz in Raummeter" />
      <xs:enumeration value="Holz in kg" />
      <xs:enumeration value="Holz in kWh Heizwert" />
      <xs:enumeration value="Holz in kWh Brennwert" />
      <xs:enumeration value="Holz in Schüttraummeter" />
      <xs:enumeration value="Strom netzbezogen in kWh" />
      <xs:enumeration value="Strom gebäudenah erzeugt (aus Photovoltaik, Windkraft) in kWh" />
      <xs:enumeration value="Verdrängungsstrommix für KWK in kWh" />
      <xs:enumeration value="Wärme (Erdwärme, Geothermie, Solarthermie, Umgebungswärme) in kWh" />
      <xs:enumeration value="Kälte (Erdkälte, Umgebungskälte) in kWh" />
      <xs:enumeration value="Abwärme aus Prozessen (prod) in kWh" />
      <xs:enumeration value="Abwärme aus Prozessen (out) in kWh" />
      <xs:enumeration value="Wärme aus KWK, gebäudeintegriert oder gebäudenah in kWh" />
      <xs:enumeration value="Wärme aus Verbrennung von Siedlungsabfällen in kWh" />
      <xs:enumeration
        value="Nah-/Fernwärme aus KWK, fossiler Brennstoff (Stein-/Braunkohle) bzw. Energieträger in kWh" />
      <xs:enumeration
        value="Nah-/Fernwärme aus KWK, fossiler Brennstoff (Gasförmige und flüssige Brennstoffe) bzw. Energieträger in kWh" />
      <xs:enumeration
        value="Nah-/Fernwärme aus KWK, erneuerbarer Brennstoff bzw. Energieträger in kWh" />
      <xs:enumeration
        value="Nah-/Fernwärme aus Heizwerken, fossiler Brennstoff (Stein-/Braunkohle) bzw. Energieträger in kWh" />
      <xs:enumeration
        value="Nah-/Fernwärme aus Heizwerken, fossiler Brennstoff (Gasförmige und flüssige Brennstoffe) bzw. Energieträger in kWh" />
      <xs:enumeration
        value="Nah-/Fernwärme aus Heizwerken, erneuerbarer Brennstoff bzw. Energieträger in kWh" />
    </xs:restriction>
  </xs:simpleType>
  <xs:complexType name="Zeitraum-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Block, der eine einzelne Verbrauchsperiode enthält. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Startdatum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anfangsdatum der Periode, angegeben als YYYY-MM-DD. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:date">
            <xs:minInclusive value="2000-01-01" />
            <xs:maxInclusive value="2100-01-01" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Enddatum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Enddatum der Periode, angegeben als YYYY-MM-DD </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:date">
            <xs:minInclusive value="2000-01-01" />
            <xs:maxInclusive value="2100-01-01" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Verbrauchte-Menge" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Erfasste verbrauchte Menge in der zum ausgewählten
            Energieträger gehörigen Einheit, brutto einschliesslich eventuell enthaltenem Warmwasser
            und ggf. enthaltene thermisch erzeugte Kälte bei Nichtwohngebäude (Verbrauchte Menge:
            B_Vg,Zeitabschnitt). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="1000000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Energieverbrauch" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Verbrauchswert in kWh Heizwert als Einheit, brutto
            einschliesslich eventuell enthaltenem Warmwasser und ggf. enthaltene thermisch erzeugte
            Kälte bei Nichtwohngebäude (Energieverbrauch: E_Vg,Zeitabschnitt). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="1000000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Energieverbrauchsanteil-Warmwasser-zentral" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Im Verbrauchswert enthaltener Wert für zentrale
            Warmwasserbereitung (ggfs. 0 wenn keine Warmwasserbereitung über diesen Energieträger)
            in kWh (Energieverbrauchsanteil für zentrale Warmwasserbereitung: E_VWW,Zeitabschnitt). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Warmwasserwertermittlung" type="n1:Warmwasserwertermittlung-enum"
        minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Art, wie der Warmwasserwert ggfs. aus Messungen oder
            Berechnungen ermittelt wurde. Bei dezentraler Warmwasserbereitung in Wohngebäuden ist
            die Auswahl "Pauschale für dezentrale..." auszuwählen und das entsprechende
            Korrekturverfahren anzuwenden (siehe Element Warmwasserzuschlag-Daten). Die Auswahl
            "keine Warmwasserbereitung enthalten" ist bei Wohngebäuden zu wählen, wenn ein anderer
            ebenfalls gelisteter Energieträger die zentrale Warmwasserbereitung versorgt. Bei
            Nichtwohngebäuden ist sie zu wählen, wenn die Warmwasserbereitung im Stromverbrauch
            erfasst ist oder generell kein Warmwasser mit gebäudetechnischen Anlagen im Gebäude
            erzeugt wird. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Energieverbrauchsanteil-thermisch-erzeugte-Kaelte" minOccurs="0"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Im Verbrauchswert ggf. enthaltener Wert für thermisch
            erzeugte Kälte (nur bei Nichtwohngebäuden) in kWh. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Energieverbrauchsanteil-Heizung" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Im Verbrauchswert enthaltener Wert für Heizung in kWh. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Klimafaktor" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Klimafaktor dieser Periode entsprechend Gebäudestandort
            (Klimafaktor: f_Klima). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="3" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Verbrauchswert-kWh-Strom" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Verbrauchswert Strom in kWh zum Betreiben des jeweiligen
            Wärmeerzeugers (Pumpen etc.), sofern dieser ermittelt wird. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="Warmwasserzuschlag-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Angabe des Warmwasserzuschlags für dezentrale
        Trinkwarmwasserbereitung in Wohngebäuden </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Startdatum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anfangsdatum des Zeitraums, in dem dezentrale
            Trinkwarmwasserbereitung bestand, angegeben als YYYY-MM-DD. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:date">
            <xs:minInclusive value="2000-01-01" />
            <xs:maxInclusive value="2100-01-01" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Enddatum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Enddatum des Zeitraums, in dem dezentrale
            Trinkwarmwasserbereitung bestand, angegeben als YYYY-MM-DD. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:date">
            <xs:minInclusive value="2000-01-01" />
            <xs:maxInclusive value="2100-01-01" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Primaerenergiefaktor" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiefaktor (entsprechend Anlage 4 GEG) des
            wesentlichen Energieträgers für die Beheizung, damit das dezentrale Warmwasser als
            virtueller Mehrverbrauch einer fiktiven zentralen Warmwasserbereitung verrechnet werden
            kann (Primärenergiefaktor: f_p). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="3" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Warmwasserzuschlag-kWh" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Zuschlagswert Warmwasser in kWh für den betroffenen
            Zeitraum, in der Tabelle einzusetzen in die Spalten "Energieverbrauch" und "Anteil
            Warmwasser". </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="Kuehlzuschlag-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Angaben für den Verbrauchs-Zuschlag für gekühlte Wohngebäude. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Startdatum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anfangsdatum des Zeitraums, in dem die Einrichtung zur
            Kühlung des Gebäudes bestand, angegeben als YYYY-MM-DD. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:date">
            <xs:minInclusive value="2000-01-01" />
            <xs:maxInclusive value="2100-01-01" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Enddatum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Enddatum des Zeitraums, in dem die Einrichtung zur
            Kühlung des Gebäudes bestand, angegeben als YYYY-MM-DD. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:date">
            <xs:minInclusive value="2000-01-01" />
            <xs:maxInclusive value="2100-01-01" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Gebaeudenutzflaeche-gekuehlt" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Energiebezugsfläche AN des Gebäudes/Gebäudeteils, der
            maschinell gekühlt wird (ganze Quadratmeter); die Angabe erscheint nicht im
            Energieausweis, ist aber für die Zuschlagsberechnung relevant. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="1000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Primaerenergiefaktor" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiefaktor (entsprechend Anlage 4 GEG) Strom für
            die Wohnungskühlung (Primärenergiefaktor: f_p). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="3" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Kuehlzuschlag-kWh" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Zuschlagswert für Kühlenergie in kWh für den betroffenen
            Zeitraum, in der Tabelle einzusetzen in die Spalte "Energieverbrauch". </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="Leerstandszuschlag-Heizung-Daten">
    <xs:choice>
      <xs:element name="kein-Leerstand">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Im dem Energieausweis zugrunde liegenden Zeitraum hat
            keine Nutzungseinheit so lange leer gestanden, dass eine Leerstandskorrektur Heizung
            erforderlich ist. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="Kein längerer Leerstand Heizung zu berücksichtigen." />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:sequence>
        <xs:element name="Leerstandszuschlag-nach-Bekanntmachung"
          type="n1:Leerstandszuschlag-Bekanntmachung-Daten">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Im dem Energieausweis zugrunde liegenden Zeitraum ist
              Leerstand Heizung aufgetreten, es wurde eine Leerstandskorrektur entsprechend der
              Bekanntmachung der Regeln für Energieverbrauchskennwerte vorgenommen. </xs:documentation>
          </xs:annotation>
        </xs:element>
        <xs:element name="Zuschlagsfaktor" minOccurs="1" maxOccurs="1">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Wohngebäude: Zuschlagsfaktor f(eVhb, 12mth) nach der
              Formel f(eVhb, 12mth) = - 0,0028 * eVhb, 12mth + 0,9147 Nichtwohngebäude:
              Zuschlagsfaktor f(eVhb, 12mth) nach der Formel f(eVhb, 12mth) = - 0,0083 * eVhb, 12mth
              + 1,3982 gemäß GEG § 82 und gemeinsamen Bekanntmachung des Bundesministeriums für
              Wirtschaft und Energie und des Bundesministerium für Umwelt, Naturschutz, Bau und
              Reaktorsicherheit. </xs:documentation>
          </xs:annotation>
          <xs:simpleType>
            <xs:restriction base="xs:decimal">
              <xs:fractionDigits value="2" />
              <xs:totalDigits value="3" />
              <xs:minInclusive value="0" />
              <xs:maxExclusive value="10" />
            </xs:restriction>
          </xs:simpleType>
        </xs:element>
        <xs:element name="witterungsbereinigter-Endenergieverbrauchsanteil-fuer-Heizung"
          minOccurs="1" maxOccurs="1">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Witterungsbereinigter Endenergieverbrauchsanteil für
              Heizung eVhb, 12mth ist Teil der Formel für Berechnung der Zuschlagsfaktor, Einheit in
              kWh/m²a; gemäß GEG § 82 und gemeinsamer Bekanntmachung des Bundesministeriums für
              Wirtschaft und Energie und des Bundesministerium für Umwelt, Naturschutz, Bau und
              Reaktorsicherheit. </xs:documentation>
          </xs:annotation>
          <xs:simpleType>
            <xs:restriction base="xs:decimal">
              <xs:fractionDigits value="2" />
              <xs:totalDigits value="7" />
              <xs:minInclusive value="0" />
              <xs:maxExclusive value="100000" />
            </xs:restriction>
          </xs:simpleType>
        </xs:element>
      </xs:sequence>
    </xs:choice>
  </xs:complexType>
  <xs:complexType name="Leerstandszuschlag-Warmwasser-Daten">
    <xs:sequence>
      <xs:element name="keine-Nutzung-von-WW" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> true: Warmwasser ist nicht vorhanden (nur bei NWG), oder
            dezentrale elektrische Warmwasserbereitung ist beim Stromanteil mit erfasst, die
            Leerstandskorrektur erfolgt in diesem Fall zusammen mit den übrigen elektrischen
            Verbrauchsanteilen. In diesem Fall ist "kein-Leerstand" anzugeben. false: Einer der
            anderen Fälle hat Gültigkeit (kein Leerstand oder Leerstand nach Bekanntmachung). </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:choice>
        <xs:element name="kein-Leerstand">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Im dem Energieausweis zugrunde liegenden Zeitraum hat
              keine Nutzungseinheit so lange leer gestanden, dass eine Leerstandskorrektur
              Warmwasser erforderlich ist. </xs:documentation>
          </xs:annotation>
          <xs:simpleType>
            <xs:restriction base="xs:string">
              <xs:pattern value="Kein längerer Leerstand Warmwasser zu berücksichtigen." />
            </xs:restriction>
          </xs:simpleType>
        </xs:element>
        <xs:element name="Leerstandszuschlag-nach-Bekanntmachung"
          type="n1:Leerstandszuschlag-Bekanntmachung-Daten">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Im dem Energieausweis zugrunde liegenden Zeitraum ist
              Leerstand Warmwasser aufgetreten, es wurde eine Leerstandskorrektur entsprechend der
              Bekanntmachung der Regeln für Energieverbrauchskennwerte vorgenommen. </xs:documentation>
          </xs:annotation>
        </xs:element>
      </xs:choice>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="Leerstandszuschlag-thermisch-erzeugte-Kaelte-Daten">
    <xs:sequence>
      <xs:choice>
        <xs:element name="kein-Leerstand">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Im dem Energieausweis zugrunde liegenden Zeitraum hat
              keine Nutzungseinheit so lange leer gestanden, dass eine Leerstandskorrektur thermisch
              erzeugter Kälte erforderlich ist. </xs:documentation>
          </xs:annotation>
          <xs:simpleType>
            <xs:restriction base="xs:string">
              <xs:pattern
                value="Kein längerer Leerstand thermisch erzeugte Kälte zu berücksichtigen." />
            </xs:restriction>
          </xs:simpleType>
        </xs:element>
        <xs:element name="Leerstandszuschlag-nach-Bekanntmachung"
          type="n1:Leerstandszuschlag-Bekanntmachung-Daten">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Im dem Energieausweis zugrunde liegenden Zeitraum ist
              Leerstand für thermisch erzeugte Kälte aufgetreten, es wurde eine Leerstandskorrektur
              entsprechend der Bekanntmachung der Regeln für Energieverbrauchskennwerte vorgenommen. </xs:documentation>
          </xs:annotation>
        </xs:element>
      </xs:choice>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="Leerstandszuschlag-Strom-Daten">
    <xs:choice>
      <xs:element name="kein-Leerstand">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Im dem Energieausweis zugrunde liegenden Zeitraum hat
            keine Nutzungseinheit so lange leer gestanden, dass eine Leerstandskorrektur Strom
            erforderlich ist. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="Kein längerer Leerstand Strom zu berücksichtigen." />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Leerstandszuschlag-nach-Bekanntmachung"
        type="n1:Leerstandszuschlag-Bekanntmachung-Daten">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Im dem Energieausweis zugrunde liegenden Zeitraum ist
            Leerstand Strom aufgetreten, es wurde eine Leerstandskorrektur entsprechend der
            Bekanntmachung der Regeln für Energieverbrauchskennwerte vorgenommen. </xs:documentation>
        </xs:annotation>
      </xs:element>
    </xs:choice>
  </xs:complexType>
  <xs:complexType name="Leerstandszuschlag-Bekanntmachung-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Daten für die Leerstandskorrektur entsprechend der
        Bekanntmachung der Regeln für Energieverbrauchskennwerte. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Leerstandsfaktor" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Leerstandsfaktor bezogen auf die anteilige
            Gebäudenutzfläche AN und den Zeitanteil innerhalb des gesamten aufgeführten
            Verbrauchszeitraums: f_leer. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="3" />
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="0.99" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Startdatum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anfangsdatum des Gesamt-Zeitraums, der dem Energieausweis
            zugrunde liegt, angegeben als YYYY-MM-DD. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:date">
            <xs:minInclusive value="2000-01-01" />
            <xs:maxInclusive value="2100-01-01" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Enddatum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Enddatum des Gesamt-Zeitraums, der dem Energieausweis
            zugrunde liegt, angegeben als YYYY-MM-DD. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:date">
            <xs:minInclusive value="2000-01-01" />
            <xs:maxInclusive value="2100-01-01" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Leerstandszuschlag-kWh" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Je nach Betrachtung von "Heizung", "Warmwasser", oder
            "Strom" bei NWG, ist der Leerstandszuschlag für den Energieverbrauchsanteil für: -
            Heizung in kWh: delta_E_Vh in der Tabelle in die Spalte "Anteil Heizung" einzusetzen -
            zentrale Warmwasserbereitung in kWh: delta_E_VWW in der Tabelle in die Spalte "Anteil
            Warmwasser" einzusetzen - Strom in kWh: delta_E_Vs in der Tabelle in die Spalte
            "Energieverbrauch Strom" einzusetzen </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Primaerenergiefaktor" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiefaktor (entsprechend Anlage 4 GEG) des
            wesentlichen Energieträgers: f_p. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="3" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="Warmwasserwertermittlung-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="direkter Messwert Wärmemenge" />
      <xs:enumeration value="Pauschale für dezentrale Warmwasserbereitung (Wohngebäude)" />
      <xs:enumeration value="Rechenwert nach Heizkostenverordnung (Wohngebäude)" />
      <xs:enumeration value="Rechenwert nach GEG / DIN V 18599 (Nichtwohngebäude)" />
      <xs:enumeration value="Rechenwert nach Heizkostenverordnung (Nichtwohngebäude)" />
      <xs:enumeration value="Pauschale 5 % Warmwasserbereitung (Nichtwohngebäude)" />
      <xs:enumeration value="Pauschale 50 % Warmwasserbereitung (Nichtwohngebäude)" />
      <xs:enumeration value="monatsweise Erfassung Wärmeverbrauch Sommer (Nichtwohngebäude)" />
      <xs:enumeration value="sonstige Ermittlung des Verbrauchsanteils der Warmwasserbereitung" />
      <xs:enumeration value="keine Warmwasserbereitung enthalten" />
    </xs:restriction>
  </xs:simpleType>
  <xs:complexType name="Wohngebaeude-Bedarfs-Daten-easy">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält im Falle eines Wohngebäudes, für welches
        das vereinfachte Nachweisverfahren/ Modellgebäudeverfahren nach § 31 GEG, nach vorgegebenen
        Maßgaben in Anlage 5 (informell EnEV easy) die erforderlichen Bedarfswerte. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Wohngebaeude-Anbaugrad" type="n1:Wohngebaeude-Anbaugrad-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anbaugrad des Gebäudes bzw. Gebäudeteils an (beheizte)
            Nachbargebäude. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Anzahl-Geschosse" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anzahl beheizter Geschosse im Gebäude (inkl. Keller, wenn
            beheizt). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Geschoss-Bruttogeschossflaechenumfang" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Der beheizte Bruttogeschossflächenumfang des ersten
            Geschosses in m (Bruttogeschossflächenumfang: u). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Geschoss-Bruttogeschossflaeche" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Die beheizte Bruttogeschossfläche des ersten Geschosses
            in m² (Bruttogeschossfläche: A_G). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Dach-Bruttogeschossflaechenumfang" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Der beheizte Bruttogeschossflächenumfang des
            Dachgeschosses in m (Bruttogeschossflächenumfang: u). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Dach-Bruttogeschossflaeche" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Die beheizte Bruttogeschossfläche des Dachgeschosses in
            m² (Bruttogeschossfläche: A_G). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Aufsummierte-Bruttogeschossflaeche" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Die aufsummierte Bruttogeschossfläche des Gebäudes über
            alle Geschosse (einschließlich Dachgeschoss, jedoch bei Gebäuden mit zwei oder mehr
            beheizten Geschossen werden nur 80% A_G des Dachgeschosses angerechnet, wenn die
            mittlere Dachgeschosshöhe kleiner ist als 2,5 m) in m² (Bruttogeschossfläche: A_GS). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Mittlere-Geschosshoehe" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Mittlere Geschosshöhe über alle Geschosse (einschließlich
            Dachgeschoss) in m. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="3" />
            <xs:totalDigits value="5" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Kompaktheit" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anforderung u² ≤ 20*AG über alle Geschosse erfüllt? </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Deckungsgleichheit" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Die beheizten Bruttogeschossflächen aller Geschosse sind
            ohne Vor- oder Rücksprünge deckungsgleich; nur das oberste Geschoss weist ggf. eine
            kleinere Bruttogeschossfläche auf. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Fensterflaechenanteil-Nordost-Nord-Nordwest" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Die Fensterfläche der in nördliche Richtung orientierten
            Fenster des Gebäudes in Prozent. Diese Fläche ist nicht größer als der Mittelwert der
            Fensterflächen anderer Orientierungen (vgl. GEG § 31, Anlage 5, Modellgebäudeverfahren). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Fensterflaechenanteil-Gesamt" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe des Fensterflächenanteiles bezogen auf die
            Fassadenfläche des gesamten Gebäudes in Prozent. Der Höchstwert für zweiseitig angebaute
            Gebäude: 35 %, ansonsten 30 %. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Dach-transparente-Bauteile-Fensterflaechenanteil" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Flächenanteil von Dachflächenfenstern, Lichtkuppeln und
            ähnliche transparente Bauteile der gesamten Dachfläche in Prozent. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="1" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Spezielle-Fenstertueren-Flaechenanteil" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Spezielle Fenstertüren-Flächenanteil der Fassade des
            gesamten Gebäudes in Prozent. Der Höchstwert ist 4,5 %, bei zweiseitig angebauten
            Gebäuden 5,5 %. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="1" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Außentueren-Flaechenanteil" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Außentüren-Flächenanteil bezogen auf die Fassadenfläche
            des gesamten Gebäudes in m², max. 2,7% bei Ein- und Zweifamilienhäusern, ansonsten 1,5
            %. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="1" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Keine-Anlage-zur-Kuehlung" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Das Gebäude ist nicht mit einer Anlage zur Raumkühlung
            (Klimaanlage) ausgestattet. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Anforderung-Waermebruecken-erfuellt" type="xs:boolean" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Die Wärmebrücken, die im Rahmen rechnerischer Nachweise
            zu berücksichtigen wären, sind so ausgeführt, dass sie mindestens gleichwertig zu den
            Musterlösungen nach DIN 4108 Beiblatt 2 sind. (§ 24 GEG über Fälle, in denen auf
            Gleichwertigkeitsnachweise verzichtet werden kann, bleibt unberührt) </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Gebaeudedichtheit" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Dichtheit des Gebäudes nach § 26 GEG erfolgreich geprüft
            (n_50-Wert)? </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Heiz-Warmwassersystem" type="n1:Heizwaermeerzeuger-Typ-easy-enum"
        minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angaben zum Heiz- und Warmwassersystem nach Anlage 5
            Tabelle 1 bis 3 GEG. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Lueftungsanlagenanforderungen" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Lüftungsanlagenanforderungen gemäß Anlage 5 GEG erfüllt? </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Waermeschutz-Variante" type="n1:Waermeschutz-Variante-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angewendete Wärmeschutzvariante nach Anlage 5 Tabelle 1
            bis 3 GEG. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Endenergiebedarf" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf in kWh/m²a bezogen auf die energetische
            Nutzfläche. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="6" />
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="1000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Energieeffizienzklasse" type="n1:Energieeffizienzklasse-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Energieeffizienzklasse des Gebäudes. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Primaerenergiebedarf-Ist-Wert" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiebedarf-Ist-Wert in kWh/m²a bezogen auf die
            energetische Nutzfläche. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="6" />
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="1000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Primaerenergiebedarf-Anforderungswert" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiebedarf-Anforderungswert in kWh/m²a bezogen
            auf die energetische Nutzfläche. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="6" />
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="1000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Energetische-Qualitaet-Ist-Wert" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Energetische Qualität Gebäudehülle HT´ in W/m²K,
            Ist-Wert. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="3" />
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="1.0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Energetische-Qualitaet-Anforderungs-Wert" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Energetische Qualität Gebäudehülle HT´ in W/m²K,
            Anforderungs-Wert. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="3" />
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="1.0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Sommerlicher-Waermeschutz" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Das Gebäude erfüllt die Voraussetzungen, unter denen der
            sommerliche Wärmeschutz auch ohne rechnerischen Nachweis als erfüllt gilt (neue
            Normungsverweise). aa) Beim kritischen Raum (Raum mit der höchsten Wärmeeinstrahlung im
            Sommer) beträgt der Fensterflächenanteil bezogen auf die Grundfläche dieses Raums nicht
            mehr als 35 vom Hundert, bb) sämtliche Fenster in Ost-, Süd- oder Westorientierung
            (inkl. derer eines eventuellen Glasvorbaus) sind mit außenliegenden
            Sonnenschutzvorrichtun-gen mit einem Abminderungsfaktor FC ≤ 0,30 ausgestattet. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Abminderung-Sonnenschutz" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Abminderungsfaktor der verwendeten
            Sonnenschutzvorrichtung für Fenster mit Ost-, Süd- oder Westorientierung. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="3" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Art-der-Nutzung-erneuerbaren-Energie-1"
        type="n1:Art-der-Nutzung-erneuerbaren-Energie-enum" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Es wird die erste Angabe im Energieausweis zur Nutzung
            erneuerbarer Energien nach GEG Abschnit 4 § 34 erwartet, relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Deckungsanteil-1" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Deckungsanteil in % für erste Angabe zur Nutzung
            erneuerbarer Energien nach GEG Abschnitt 4 § 34 (Neubau), relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Anteil-der-Pflichterfuellung-1" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Pflichterfüllungsanteil in % für die erste Angabe zur
            Nutzung erneuerbarer Energien nach GEG. (§ 34: Die prozentualen Anteile der
            tatsächlichen Nutzung der einzelnen Maßnahmen im Verhältnis der jeweils nach den § 35
            bis § 45 vorgesehenen Nutzung müssen in der Summe mindestens 100 Prozent Erfüllungsgrad
            ergeben.) </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="999" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Art-der-Nutzung-erneuerbaren-Energie-2"
        type="n1:Art-der-Nutzung-erneuerbaren-Energie-enum" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Es wird die zweite Angabe im Energieausweis zur Nutzung
            erneuerbarer Energien nach GEG Abschnit 4 § 34 erwartet, relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Deckungsanteil-2" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Deckungsanteil in % für zweite Angabe Angabe zur Nutzung
            erneuerbarer Energien nach GEG Abschnitt 4 § 34 (Neubau), relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Anteil-der-Pflichterfuellung-2" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Pflichterfüllungsanteil in % für die zweite Angabe zur
            Nutzung erneuerbarer Energien nach GEG. (§ 34: Die prozentualen Anteile der
            tatsächlichen Nutzung der einzelnen Maßnahmen im Verhältnis der jeweils nach den § 35
            bis § 45 vorgesehenen Nutzung müssen in der Summe mindestens 100 Prozent Erfüllungsgrad
            ergeben.) </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="999" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="Heizwaermeerzeuger-Typ-easy-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration
        value="Kessel für feste Biomasse, Pufferspeicher und zentrale Trinkwassererwärmung mit mech. Lüftungsanlage mit Wärmerückgewinnung" />
      <xs:enumeration
        value="Kessel für feste Biomasse, Pufferspeicher und zentrale Trinkwassererwärmung" />
      <xs:enumeration
        value="Brennwertgerät zur Verfeuerung von Erdgas oder leichtem Heizöl, Solaranlage zur zentralen Trinkwassererwärmung, Lüftungsanlage mit Wärmerückgewinnung" />
      <xs:enumeration
        value="Brennwertgerät zur Verfeuerung von Erdgas oder leichtem Heizöl, Solaranlage zur zentralen Trinkwassererwärmung und Heizungsunterstützung (Kombianlage), Pufferspeicher, Lüftungsanlage mit Wärmerückgewinnung" />
      <xs:enumeration
        value="Nah-/Fernwärmeversorgung oder lokale Kraft-Wärme-Kopplung, zentrale Trinkwassererwärmung" />
      <xs:enumeration
        value="Nah-/Fernwärmeversorgung oder lokale Kraft-Wärme-Kopplung, zentrale Trinkwassererwärmung, Lüftungsanlage mit Wärmerückgewinnung" />
      <xs:enumeration
        value="Luft-Wasser-Wärmepumpe, zentrale Trinkwassererwärmung, Lüftungsanlage mit Wärmerückgewinnung" />
      <xs:enumeration value="Luft-Wasser-Wärmepumpe, zentrale Trinkwassererwärmung" />
      <xs:enumeration value="Luft-Wasser-Wärmepumpe, dezentrale Trinkwassererwärmung" />
      <xs:enumeration
        value="Luft-Wasser-Wärmepumpe, dezentrale Trinkwassererwärmung, Lüftungsanlage mit Wärmerückgewinnung" />
      <xs:enumeration
        value="Wasser-Wasser-Wärmepumpe, zentrale Trinkwassererwärmung, Lüftungsanlage mit Wärmerückgewinnung" />
      <xs:enumeration value="Wasser-Wasser-Wärmepumpe, zentrale Trinkwassererwärmung" />
      <xs:enumeration
        value="Sole-Wasser-Wärmepumpe, zentrale Trinkwassererwärmung, Lüftungsanlage mit Wärmerückgewinnung" />
      <xs:enumeration value="Sole-Wasser-Wärmepumpe, zentrale Trinkwassererwärmung" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Waermeschutz-Variante-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Wärmeschutz-Variante A" />
      <xs:enumeration value="Wärmeschutz-Variante B" />
      <xs:enumeration value="Wärmeschutz-Variante C" />
      <xs:enumeration value="Wärmeschutz-Variante D" />
    </xs:restriction>
  </xs:simpleType>
  <xs:complexType name="Wohngebaeude-Bedarfs-Daten-4108-4701">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält im Falle eines Wohngebäudes mit Nachweis
        nach DIN V 4108 / DIN V 4701 die Bedarfswerte. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Wohngebaeude-Anbaugrad" type="n1:Wohngebaeude-Anbaugrad-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anbaugrad des Gebäudes bzw. Gebäudeteils an (beheizte)
            Nachbargebäude. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Bruttovolumen" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Beheiztes Gebäudevolumen des Gebäudes/Gebäudeteils (ganze
            Kubikmeter) (Bruttovolumen: V_e). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="1000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="durchschnittliche-Geschosshoehe" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Die durchschnittliche Geschosshöhe hG des Gebäudes (§ 25
            GEG bzw. DIN V 18599-1: 2018-09) liegt im Anwendungsbereich zwischen 2,5 bis 3,0 m
            (Durchschnittliche Geschosshöhe: h_G). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Bauteil-Opak" type="n1:Bauteil-Opak-Daten" minOccurs="1" maxOccurs="10000" />
      <xs:element name="Bauteil-Transparent" type="n1:Bauteil-Transparent-Daten" minOccurs="0"
        maxOccurs="10000" />
      <xs:element name="Bauteil-Dach" type="n1:Bauteil-Dach-Daten" minOccurs="0" maxOccurs="10000" />
      <xs:element name="Waermebrueckenzuschlag" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wert des Wärmebrückenzuschlags für die Gebäudehülle
            (Wärmebrückenzuschlag: delta_U_WB). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="3" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="-0.999" />
            <xs:maxInclusive value="0.999" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Transmissionswaermeverlust" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Transmissionswärmeverlust in kWh/a
            (Transmissionswärmeverlust: Q_T). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Luftdichtheit" type="n1:Luftdichtheit-4701-enum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Luftdichtheit der Gebäudehülle. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Lueftungswaermeverlust" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Lüftungswärmeverlust in kWh/a (Lüftungswärmeverlust:
            Q_V). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Solare-Waermegewinne" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Summe der solaren Gewinne in kWh/a (Solare Wärmegewinne:
            Q_S). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Interne-Waermegewinne" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Summe der internen Gewinne in kWh/a (Interne
            Wärmegewinne: Q_I). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Heizungsanlage" type="n1:Heizungsanlage-Daten" minOccurs="1" maxOccurs="200">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angaben zum / zu den Wärmeerzeuger(n); ist ein Gebäude
            ausschließlich passiv solar beheizt (Fenstereinstrahlung), ist als Wärmeerzeuger
            "Sonstiges" anzugeben. Ein Hinweis im Erläuterungsfeld auf Seite 4 des Energieausweises
            ist für diesen Fall zu empfehlen. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Pufferspeicher-Nenninhalt" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Volumen eines oder mehrerer ggfs. vorhandenen
            Heizungs-Pufferspeicher(s) (keiner = 0) in Liter (Pufferspeicher-Nenninhalt: V). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Heizkreisauslegungstemperatur" type="n1:Heizkreisauslegungstemperatur-enum"
        minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Temperaturniveau der Heizungsverteilung Vorlauf/Rücklauf,
            bzw. Angabe von Luftheizsystem oder ausschliesslicher Beheizung über
            Einzelraumheizgeräte; anzugeben ist die Temperatur des höchsten Kreises, bei krummen
            Werten ist die nach Vorlauftemperatur nächst höhere Auswahl anzugeben. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Heizungsanlage-innerhalb-Huelle" type="xs:boolean" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> TRUE, wenn alle Wärmeerzeuger innerhalb der thermischen
            Gebäudehülle stehen. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Trinkwarmwasseranlage" type="n1:Trinkwarmwasseranlage-Daten" minOccurs="1"
        maxOccurs="200">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angaben zu dem / den Warmwassererzeuger(n). </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Trinkwarmwasserspeicher-Nenninhalt" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Volumen eines ggfs. vorhandenen Warmwasserspeichers (kein
            Speicher = 0), bzw. Summe der Volumina bei mehreren Speichern, in Liter
            (Trinkwarmwasserspeicher-Nenninhalt: V). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Trinkwarmwasserverteilung-Zirkulation" type="xs:boolean" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Ist zur Warmwasserverteilung eine Trinkwasser-seitige
            Zirkulation vorhanden? </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Vereinfachte-Datenaufnahme" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wurden Regeln zur vereinfachten Datenaufnahme nach § 50
            (4) GEG bzw. Bekanntmachungen angewendet? </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="spezifischer-Transmissionswaermeverlust-Ist" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Spezifischer auf die wärmeübertragende Umfassungsfläche
            bezogener Transmissionswärmeverlust HT', Ist-Wert des Gebäudes, in W/m²K (spezifischer
            Transmissionswärmeverlust: H_T(Strich)). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="spezifischer-Transmissionswaermeverlust-Hoechstwert" minOccurs="0"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Spezifischer auf die wärmeübertragende Umfassungsfläche
            bezogener Transmissionswärmeverlust HT', Anforderungswert, in W/m²K, nur bei Neubauten
            und wesentlichen Modernisierungen/Erweiterungen (spezifischer Transmissionswärmeverlust:
            H_T(Strich)). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="angerechneter-lokaler-erneuerbarer-Strom" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Größe des Abzugs (in kWh/a m2) bei der Primärenergie bzw.
            bei der Endenergie für den gebäudenah erzeugten Strom aus erneuerbarer Energie nach der
            entsprechenden Bilanzierungsregel (vgl. GEG § 23 (2) und (3) bzw. GEG §23 (4)) </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Innovationsklausel" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe ob Innovationsklausel gemäß § 103 (1) GEG 2020
            angewendet wurde. (Alternative Anforderungen: Treibhausgasemissionen, Höchstwert der
            Endenergiebedarfs + Transmissionswärmeverlust (nur für Neubau und WG) +
            Wärmedurchgangskoeffizienten der wärmeübertragenden Umfassungsfläche (nur für Neubau und
            NWG) - Aussetzung der Hauptanforderungen (Neubau § 10, Bestand § 50)) </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Quartiersregelung" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe ob Quartiersregelung gemäß § 103 (3) -
            Gesamtbilanzierung für Wärmeversorgung zusammenhängender Gebäude - zutreffend ist. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Primaerenergiebedarf-Hoechstwert-Bestand" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergie-Anforderungswert Modernisierter Altbau in
            kWh/m²a bezogen auf die energetische Nutzfläche, nur bei Ausstellungsanlass
            Modernisierung. Wenn entsprechend dem Ausstellungsanlass kein Wert zu übermitteln ist,
            kann eine 0 eingetragen werden, da dieser Wert dann nicht relevant ist. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:choice>
        <xs:sequence>
          <xs:element name="Endenergiebedarf-Hoechstwert-Bestand" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Endenergie-Anforderungswert Modernisierter Altbau in
                kWh/m²a bezogen auf die energetische Nutzfläche, nur bei Ausstellungsanlass
                Modernisierung. Wenn entsprechend dem Ausstellungsanlass kein Wert zu übermitteln
                ist, kann eine 0 eingetragen werden, da dieser Wert dann nicht relevant ist. </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="7" />
                <xs:minInclusive value="0" />
                <xs:maxExclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
          <xs:element name="Treibhausgasemissionen-Hoechstwert-Bestand" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Anforderungswert für Treibhausgasemissionen
                Modernisierter Altbau in kg/m²a bezogen auf die energetische Nutzfläche, nur bei
                Ausstellungsanlass Modernisierung. Wenn entsprechend dem Ausstellungsanlass kein
                Wert zu übermitteln ist, kann eine 0 eingetragen werden, da dieser Wert dann nicht
                relevant ist. </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="8" />
                <xs:minInclusive value="-100000" />
                <xs:maxInclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
        </xs:sequence>
        <xs:sequence>
          <xs:element name="Primaerenergiebedarf-Hoechstwert-Neubau" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Primärenergie-Anforderungswert Neubau (Kennwert des
                Referenzgebäudes, ab 2016 mit Berücksichtigung des entspr. Faktors) in kWh/m²a
                bezogen auf die energetische Nutzfläche, bei Neubau im Energieausweis einzutragen.
                (Primärenergiebedarf: Q_p,Ref). </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="7" />
                <xs:minInclusive value="0" />
                <xs:maxExclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
          <xs:element name="Endenergiebedarf-Hoechstwert-Neubau" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Endenergiebedarf-Anforderungswert Neubau (Kennwert
                des Referenzgebäudes, ab 2016 mit Berücksichtigung des entspr. Faktors) in kWh/m²a
                bezogen auf die energetische Nutzfläche. </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="7" />
                <xs:minInclusive value="0" />
                <xs:maxExclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
          <xs:element name="Treibhausgasemissionen-Hoechstwert-Neubau" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Anforderungswert für Treibhausgasemissionen Neubau
                (Kennwert des Referenzgebäudes) in kg/m²a bezogen auf die energetische Nutzfläche. </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="8" />
                <xs:minInclusive value="-100000" />
                <xs:maxInclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
        </xs:sequence>
      </xs:choice>
      <xs:element name="Energietraeger-Liste" type="n1:Endenergie-Energietraeger-Daten"
        minOccurs="1" maxOccurs="10" />
      <xs:element name="Endenergiebedarf-Waerme-AN" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiekennwert Wärme in kWh/m²a bezogen auf die
            energetische Nutzfläche (Endenergiebedarf-Wärme: q_WE,E). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="6" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Hilfsenergie-AN" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiekennwert Hilfsenergie in kWh/m²a bezogen auf
            die energetische Nutzfläche (Endenergiebedarf-Hilfsenergie-AN: q_HE,E). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="6" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Gesamt" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiekennwert Gesamt in kWh/m²a bezogen auf die
            energetische Nutzfläche. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="6" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Primaerenergiebedarf" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiekennwert in kWh/m²a bezogen auf die
            energetische Nutzfläche (Primärenergiebedarf: q_p). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Energieeffizienzklasse" type="n1:Energieeffizienzklasse-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Energieeffizienzklasse des Gebäudes. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Art-der-Nutzung-erneuerbaren-Energie-1"
        type="n1:Art-der-Nutzung-erneuerbaren-Energie-enum" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Es wird die erste Angabe im Energieausweis zur Nutzung
            erneuerbarer Energien nach GEG Abschnit 4 § 34 erwartet, relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Deckungsanteil-1" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Deckungsanteil in % für erste Angabe zur Nutzung
            erneuerbarer Energien nach GEG Abschnitt 4 § 34 (Neubau), relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Anteil-der-Pflichterfuellung-1" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Pflichterfüllungsanteil in % für die erste Angabe zur
            Nutzung erneuerbarer Energien nach GEG. (§ 34: Die prozentualen Anteile der
            tatsächlichen Nutzung der einzelnen Maßnahmen im Verhältnis der jeweils nach den § 35
            bis § 45 vorgesehenen Nutzung müssen in der Summe mindestens 100 Prozent Erfüllungsgrad
            ergeben.) </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="999" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Art-der-Nutzung-erneuerbaren-Energie-2"
        type="n1:Art-der-Nutzung-erneuerbaren-Energie-enum" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Es wird die zweite Angabe im Energieausweis zur Nutzung
            erneuerbarer Energien nach GEG Abschnit 4 § 34 erwartet, relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Deckungsanteil-2" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Deckungsanteil in % für zweite Angabe Angabe zur Nutzung
            erneuerbarer Energien nach GEG Abschnitt 4 § 34 (Neubau), relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Anteil-der-Pflichterfuellung-2" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Pflichterfüllungsanteil in % für die zweite Angabe zur
            Nutzung erneuerbarer Energien nach GEG. (§ 34: Die prozentualen Anteile der
            tatsächlichen Nutzung der einzelnen Maßnahmen im Verhältnis der jeweils nach den § 35
            bis § 45 vorgesehenen Nutzung müssen in der Summe mindestens 100 Prozent Erfüllungsgrad
            ergeben.) </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="999" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="verschaerft-nach-GEG-45-eingehalten" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Der gemäß § 45 GEG um diese Prozentzahl verschärfte
            Anforderungswert (15 % Unterschreitung Transmissionswärmeverlust) als Maßnahme zur
            Einsparung von Energie ist eingehalten. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:boolean" />
        </xs:simpleType>
      </xs:element>
      <xs:choice>
        <xs:element name="nicht-verschaerft-nach-GEG-34" type="xs:boolean" minOccurs="1"
          maxOccurs="1" fixed="true">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Keine Maßnahmen nach § 45 in Verbindung mit $ 34. </xs:documentation>
          </xs:annotation>
        </xs:element>
        <xs:sequence>
          <xs:element name="verschaerft-nach-GEG-34" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Die in Verbindung mit § 34 GEG Maßnahmen nach § 45 in
                Kombination zur Nutzung erneuerbarer Energien zur Deckung des Wärme- und
                Kälteenergiebedarfs sind eingehalten (Anteil der Pflichterfüllung in %). </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:int">
                <xs:minInclusive value="0" />
                <xs:maxInclusive value="100" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
          <xs:element name="Anforderung-nach-GEG-16-unterschritten" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Die Anforderung nach $ 16 GEG wurde unterschritten
                (in %). </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:int">
                <xs:minInclusive value="0" />
                <xs:maxInclusive value="100" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
        </xs:sequence>
      </xs:choice>
      <xs:element name="spezifischer-Transmissionswaermeverlust-verschaerft" minOccurs="0"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Nach GEG § 45 verschärfter Anforderungswert für den
            spezifischen auf die wärmeübertragende Umfassungsfläche bezogenen
            Transmissionswärmeverlust HT' in W/m²K
            (spezifischer-Transmissionswärmeverlust-verschärft: H_T(Strich)). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Sommerlicher-Waermeschutz" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Sind die Anforderungen an den sommerlichen Wärmeschutz
            eingehalten? Relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:boolean" />
        </xs:simpleType>
      </xs:element>
      <xs:element name="Treibhausgasemissionen-Zusaetzliche-Verbrauchsdaten" minOccurs="0"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe der Treibhausgasemissionen in kg als CO2
            Äquivalent/(m²a); nur bei kombinierten Energieausweisen (Bedarf/Verbrauch), zusammen mit
            der Übermittlung der zusätzlichen Verbrauchsdaten. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="8" />
            <xs:minInclusive value="-100000" />
            <xs:maxInclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Zusaetzliche-Verbrauchsdaten" type="n1:Wohngebaeude-Verbrauchs-Daten"
        minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Bei Energieausweisen auf Bedarfsbasis können neben den
            Bedarfsangaben zusätzlich Angaben zum Verbrauch im Energieausweis dargestellt werden,
            die dann hier entsprechend integriert werden. Die Ermittlung der Fläche AN aus der
            Wohnfläche darf in den Wohngebäude-Verbrauchs-Daten dann nicht angekreuzt sein, da die
            Fläche nach einem für Bedarfsausweise zulässigen Verfahren ermittelt worden sein muss. </xs:documentation>
        </xs:annotation>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="Wohngebaeude-Anbaugrad-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="freistehend" />
      <xs:enumeration value="einseitig angebaut" />
      <xs:enumeration value="zweiseitig angebaut" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Luftdichtheit-4701-enum">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Luftdichtheit nach DIN V 4701 undicht : gemäß GEG/DIN normal
        : alle übrigen Gebäude geprüft : erfolgreich geprüft nach § 26 GEG </xs:documentation>
    </xs:annotation>
    <xs:restriction base="xs:string">
      <xs:enumeration value="undicht" />
      <xs:enumeration value="normal" />
      <xs:enumeration value="geprüft" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Luftdichtheit-18599-enum">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Luftdichtheit nach DIN V 18599 </xs:documentation>
    </xs:annotation>
    <xs:restriction base="xs:string">
      <xs:enumeration value="Gebäudekategorie I" />
      <xs:enumeration value="Gebäudekategorie II" />
      <xs:enumeration value="Gebäudekategorie III" />
      <xs:enumeration value="Gebäudekategorie IV" />
    </xs:restriction>
  </xs:simpleType>
  <xs:complexType name="Heizungsanlage-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Angaben zum jeweiligen Wärmeerzeuger </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:choice minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Dieser Block enthält entweder die Beizeichung für die
            Waermeerzeuger Bauweise nach DIN V 18599 oder DIN V 4701-10. </xs:documentation>
        </xs:annotation>
        <xs:element name="Waermeerzeuger-Bauweise-18599" type="n1:Heizwaermeerzeuger-Typ-18599-enum" />
        <xs:element name="Waermeerzeuger-Bauweise-4701" type="n1:Heizwaermeerzeuger-Typ-4701-enum" />
      </xs:choice>
      <xs:element name="Nennleistung" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Nennleistung des Wärmeerzeugers in kW, falls aus
            technischen Gründen keine Angabe der Nennleistung möglich, 0 angeben. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Waermeerzeuger-Baujahr" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Baujahr dieses Wärmeerzeugers oder Jahr der massgeblichen
            letzten baulichen Veränderung des Wärmeerzeugers. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:gYear">
            <xs:minInclusive value="1800" />
            <xs:maxInclusive value="2100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Anzahl-baugleiche" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anzahl ggfs. baugleich vorhandener Geräte
            (Mehrfach-Kessel, Einzelraumheizer usw.), bei nur einem Gerät Angabe "1". </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Energietraeger" type="n1:Energietraeger-enum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> vom Wärmeerzeuger verwendeter Energieträger </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Primaerenergiefaktor" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Verwendeter Primärenergiefaktor (nicht erneuerbarer
            Anteil) des Energieträgers (Primärenergiefaktor: f_p) entsprechend Anlage 4 GEG. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="3" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Emissionsfaktor" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Emissionsfaktor für den jeweils eingesetzen Energieträger
            zur Umrechnung in Treibhausemissionen gemäß Anlage 9 des GEG. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="Heizwaermeerzeuger-Typ-18599-enum">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Geräte-Grundtyp des Wärmeerzeugers nach DIN V 18599. </xs:documentation>
    </xs:annotation>
    <xs:restriction base="xs:string">
      <xs:enumeration value="Standard-Heizkessel als Umstell-/Wechselbrandkessel" />
      <xs:enumeration
        value="Standard-Heizkessel als Feststoffkessel (fossiler und biogener Brennstoff)" />
      <xs:enumeration value="Standard-Heizkessel als Gas-Spezial-Heizkessel" />
      <xs:enumeration
        value="Standard-Heizkessel als Gebläsekessel (fossiler und biogener Brennstoff)" />
      <xs:enumeration value="Standard-Heizkessel als Gebläsekessel mit Brennertausch" />
      <xs:enumeration value="Standard-Heizkessel als Pelletkessel" />
      <xs:enumeration value="Standard-Heizkessel als Hackschnitzelkessel" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Gas-Spezial-Heizkessel" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Umlaufwasserheizer" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Kombikessel KSp" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Kombikessel DL" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Gebläsekessel" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Gebläsekessel mit Brennertausch" />
      <xs:enumeration value="Brennwertkessel (Pellet)" />
      <xs:enumeration value="Brennwertkessel (Öl/Gas)" />
      <xs:enumeration value="Brennwertkessel (Öl, Gas), verbessert" />
      <xs:enumeration value="Fern-/Nahwärme" />
      <xs:enumeration value="Dezentrale KWK-Systeme, motorische Systeme" />
      <xs:enumeration value="Dezentrale KWK-Systeme, Systeme mit Brennstoffzellen" />
      <xs:enumeration value="Elektrisch angetriebene Luft/Wasser-Heizungswärmepumpe" />
      <xs:enumeration value="Elektrisch angetriebene Wasser/Wasser-Heizungswärmepumpe" />
      <xs:enumeration value="Elektrisch angetriebene Sole/Wasser-Heizungswärmepumpe" />
      <xs:enumeration value="Elektrisch angetriebene Abluft/Wasser-Heizungswärmepumpe" />
      <xs:enumeration value="Gasmotorisch angetriebene Luft/Wasser-Heizungswärmepumpe" />
      <xs:enumeration value="Elektrisch angetriebene Außenluft/Raumluft-Heizungswärmepumpe" />
      <xs:enumeration value="Sorptions-Gaswärmepumpe" />
      <xs:enumeration value="Gasraumheizer, schornsteingebunden" />
      <xs:enumeration value="Gasraumheizer, Außenwand-Gerät" />
      <xs:enumeration value="Dezentrale Einzelfeuerstätten" />
      <xs:enumeration value="Dezentrale Einzelfeuerstätten, hydraulisch eingebunden" />
      <xs:enumeration value="Ölbefeuerter Einzelofen mit Verdampfungsbrenner" />
      <xs:enumeration value="Kachelofen" />
      <xs:enumeration value="Kohlebefeuerter eisener Ofen" />
      <xs:enumeration value="Dezentrale Hallenheizung - indirekte Abgasabfuhr - Hellstrahler" />
      <xs:enumeration value="Dezentrale Hallenheizung - direkte Abgasabfuhr - Hellstrahler" />
      <xs:enumeration value="Dezentrale Hallenheizung - direkte Abgasabfuhr - Dunkelstrahler" />
      <xs:enumeration value="Dezentrale Hallenheizung - direkte Abgasabfuhr - Warmlufterzeuger" />
      <xs:enumeration value="Dezentral elektrisch beheizte Speicherheizung" />
      <xs:enumeration value="Dezentrale elektrische Direktheizung" />
      <xs:enumeration value="Zentral elektrisch beheizte Wärmeerzeuger" />
      <xs:enumeration
        value="Solaranlagen zur Trinkwassererwärmung und Heizungsunterstützung (Solare Kombianlagen)" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Heizwaermeerzeuger-Typ-4701-enum">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Geräte-Grundtyp des Wärmeerzeugers nach DIN V 4701-10. </xs:documentation>
    </xs:annotation>
    <xs:restriction base="xs:string">
      <xs:enumeration value="Umstell-/Wechselbrandkessel" />
      <xs:enumeration value="Feststoffkessel" />
      <xs:enumeration value="Standard-Heizkessel als Gas-Spezial-Heizkessel" />
      <xs:enumeration value="Standard-Heizkessel als Gebläsekessel" />
      <xs:enumeration value="Standard-Heizkessel als Gebläsekessel mit Brennertausch" />
      <xs:enumeration value="Standard-Heizkessel (ab 1995)" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Gas-Spezial-Heizkessel" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Umlaufwasserheizer" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Gebläsekessel" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Gebläsekessel mit Brennertausch" />
      <xs:enumeration value="Niedertemperatur-Heizkessel (ab 1995)" />
      <xs:enumeration value="Brennwertkessel (bis 1994)" />
      <xs:enumeration value="Brennwertkessel (ab 1995)" />
      <xs:enumeration value="Brennwertkessel-verbessert" />
      <xs:enumeration value="Biomasse-Wärmeerzeuger" />
      <xs:enumeration value="Fern-/Nahwärme" />
      <xs:enumeration value="Dezentrale Kraft-Wärme-Kopplung" />
      <xs:enumeration value="Elektrisch betriebene Luft/Wasser-Heizungswärmepumpe" />
      <xs:enumeration value="easy_Elektro-Wärmepumpe-Luft-TWW-dezentral">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Diese Auswahl darf nur für dieses Anlagensystem beim
            vereinfachten Tabellenverfahren für Wohngebäude-Neubauten (EnEV easy) verwendet werden. </xs:documentation>
        </xs:annotation>
      </xs:enumeration>
      <xs:enumeration value="Elektrisch betriebene Wasser/Wasser-Heizungswärmepumpe" />
      <xs:enumeration value="Elektrisch betriebene Sole/Wasser-Heizungswärmepumpe" />
      <xs:enumeration value="Elektrisch betriebene Abluft/Wasser-Heizungswärmepumpe" />
      <xs:enumeration value="Gasraumheizer, schornsteingebunden" />
      <xs:enumeration value="Gasraumheizer, Außenwand-Gerät" />
      <xs:enumeration value="Ölbefeuerter Einzelofen" />
      <xs:enumeration value="Kachelofen" />
      <xs:enumeration value="Kohlebefeuerter eisener Ofen" />
      <xs:enumeration value="Dezentrale Elektro-Speicherheizung" />
      <xs:enumeration value="Dezentrales elektrisches Direktheizgerät" />
      <xs:enumeration value="Zentral elektrisch beheizte Wärmeerzeuger" />
      <xs:enumeration value="Solare Heizungsunterstützung" />
      <xs:enumeration value="Sonstiges" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Energietraeger-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Heizöl" />
      <xs:enumeration value="Erdgas" />
      <xs:enumeration value="Flüssiggas" />
      <xs:enumeration value="Steinkohle" />
      <xs:enumeration value="Braunkohle" />
      <xs:enumeration value="Biogas" />
      <xs:enumeration value="Biogas, gebäudenah erzeugt" />
      <xs:enumeration value="biogenes Flüssiggas" />
      <xs:enumeration value="Bioöl" />
      <xs:enumeration value="Bioöl, gebäudenah erzeugt" />
      <xs:enumeration value="Holz" />
      <xs:enumeration value="Strom netzbezogen" />
      <xs:enumeration value="Strom gebäudenah erzeugt (aus Photovoltaik, Windkraft)" />
      <xs:enumeration value="Verdrängungsstrommix für KWK" />
      <xs:enumeration value="Wärme (Erdwärme, Geothermie, Solarthermie, Umgebungswärme)" />
      <xs:enumeration value="Kälte (Erdkälte, Umgebungskälte)" />
      <xs:enumeration value="Abwärme aus Prozessen (prod)" />
      <xs:enumeration value="Abwärme aus Prozessen (out)" />
      <xs:enumeration value="Wärme aus KWK, gebäudeintegriert oder gebäudenah" />
      <xs:enumeration value="Wärme aus Verbrennung von Siedlungsabfällen" />
      <xs:enumeration
        value="Nah-/Fernwärme aus KWK, fossiler Brennstoff (Stein-/Braunkohle) bzw. Energieträger" />
      <xs:enumeration
        value="Nah-/Fernwärme aus KWK, fossiler Brennstoff (Gasförmige und flüssige Brennstoffe) bzw. Energieträger" />
      <xs:enumeration value="Nah-/Fernwärme aus KWK, erneuerbarer Brennstoff bzw. Energieträger" />
      <xs:enumeration
        value="Nah-/Fernwärme aus Heizwerken, fossiler Brennstoff (Stein-/Braunkohle) bzw. Energieträger" />
      <xs:enumeration
        value="Nah-/Fernwärme aus Heizwerken, fossiler Brennstoff (Gasförmige und flüssige Brennstoffe) bzw. Energieträger" />
      <xs:enumeration
        value="Nah-/Fernwärme aus Heizwerken, erneuerbarer Brennstoff bzw. Energieträger" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Heizkreisauslegungstemperatur-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="90/70" />
      <xs:enumeration value="70/55" />
      <xs:enumeration value="55/45" />
      <xs:enumeration value="45/35" />
      <xs:enumeration value="35/28" />
      <xs:enumeration value="Warmluftheizung" />
      <xs:enumeration value="nur Einzelraum-Heizgeräte" />
    </xs:restriction>
  </xs:simpleType>
  <xs:complexType name="Trinkwarmwasseranlage-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Angaben zum jeweiligen Warmwassererzeuger. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:choice minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Dieser Block enthält entweder die Bezeichung für die
            Wärmeerzeuger Bauweise nach DIN V 18599 oder nach DIN V 4701-10. </xs:documentation>
        </xs:annotation>
        <xs:element name="Trinkwarmwassererzeuger-Bauweise-18599"
          type="n1:Trinkwarmwassererzeuger-Typ-18599-enum" />
        <xs:element name="Trinkwarmwassererzeuger-Bauweise-4701"
          type="n1:Trinkwarmwassererzeuger-Typ-4701-enum" />
      </xs:choice>
      <xs:element name="Trinkwarmwassererzeuger-Baujahr" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Baujahr dieses Trinkwarmwassererzeugers oder Jahr der
            massgeblichen letzten baulichen Veränderung des Trinkwarmwassererzeugers. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:gYear">
            <xs:minInclusive value="1800" />
            <xs:maxInclusive value="2100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Anzahl-baugleiche" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anzahl ggfs. baugleich vorhandener Geräte (mehrfach
            vorhandene Untertischgeräte usw.), bei nur einem Gerät Angabe "1". </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="Trinkwarmwassererzeuger-Typ-18599-enum">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Geräte-Grundtyp des Warmwassererzeugers nach DIN V 18599. </xs:documentation>
    </xs:annotation>
    <xs:restriction base="xs:string">
      <xs:enumeration value="Standard-Heizkessel als Umstell-/Wechselbrandkessel" />
      <xs:enumeration
        value="Standard-Heizkessel als Feststoffkessel (fossiler und biogener Brennstoff)" />
      <xs:enumeration value="Standard-Heizkessel als Gas-Spezial-Heizkessel" />
      <xs:enumeration
        value="Standard-Heizkessel als Gebläsekessel (fossiler und biogener Brennstoff)" />
      <xs:enumeration value="Standard-Heizkessel als Gebläsekessel mit Brennertausch" />
      <xs:enumeration value="Standard-Heizkessel als Pelletkessel" />
      <xs:enumeration value="Standard-Heizkessel als Hackschnitzelkessel" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Gas-Spezial-Heizkessel" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Umlaufwasserheizer" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Kombikessel KSp" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Kombikessel DL" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Gebläsekessel" />
      <xs:enumeration value="Niedertemperatur-Heizkessel als Gebläsekessel mit Brennertausch" />
      <xs:enumeration value="Brennwertkessel (Pellet)" />
      <xs:enumeration value="Brennwertkessel (Öl/Gas)" />
      <xs:enumeration value="Brennwertkessel (Öl, Gas), verbessert" />
      <xs:enumeration value="Gas-Durchlauferhitzer" />
      <xs:enumeration value="Fern-/Nahwärme" />
      <xs:enumeration value="Dezentrale Kraft-Wärme-Kopplung, motorische Systeme" />
      <xs:enumeration value="Dezentrale Kraft-Wärme-Kopplung, Systeme mit Brennstoffzellen" />
      <xs:enumeration value="Dezentrale Einzelfeuerstätten, hydraulisch eingebunden" />
      <xs:enumeration value="Elektrisch angetriebene Kellerluft/Wasser-Wärmepumpe" />
      <xs:enumeration value="Elektrisch angetriebene Luft/Wasser-Wärmepumpe" />
      <xs:enumeration value="Elektrisch angetriebene Abluft/Trinkwasser-Wärmepumpe" />
      <xs:enumeration value="Elektrisch angetriebene Abluft/Zuluft-Trinkwasser-Wärmepumpe" />
      <xs:enumeration value="Elektrisch angetriebene Kellerluft/Trinkwasser-Wärmepumpe" />
      <xs:enumeration value="Elektrisch angetriebene Wasser/Wasser-Wärmepumpe" />
      <xs:enumeration value="Elektrisch angetriebene Sole/Wasser-Wärmepumpe" />
      <xs:enumeration value="Elektrisch angetriebene Abluft/Wasser-Wärmepumpe" />
      <xs:enumeration value="Gasmotorisch angetriebene Luft/Wasser-Wärmepumpe" />
      <xs:enumeration value="Sorptions-Gaswärmepumpe" />
      <xs:enumeration value="Direkt beheizter Trinkwarmwasserspeicher (Gas)" />
      <xs:enumeration value="Elektro-Durchlauferhitzer" />
      <xs:enumeration value="Elektrisch beheizter Trinkwarmwasserspeicher" />
      <xs:enumeration value="Solaranlagen zur Trinkwassererwärmung" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Trinkwarmwassererzeuger-Typ-4701-enum">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Geräte-Grundtyp des Warmwassererzeugers DIN V 4701. </xs:documentation>
    </xs:annotation>
    <xs:restriction base="xs:string">
      <xs:enumeration value="über Heizungsanlage beheizter Speicher" />
      <xs:enumeration value="Elektro-Speicher" />
      <xs:enumeration value="Direkt beheizter Trinkwarmwasserspeicher (Gas)" />
      <xs:enumeration value="Elektro-Durchlauferhitzer" />
      <xs:enumeration value="Solare Trinkwarmwasserbereitung">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Diese Bauweise ist nur auszuwählen, wenn die Solaranlage
            nicht auch der Heizungsunterstützung dient, sondern ausschliesslich die
            Warmwasserbereitung versorgt. </xs:documentation>
        </xs:annotation>
      </xs:enumeration>
      <xs:enumeration value="Sonstiges" />
    </xs:restriction>
  </xs:simpleType>
  <xs:complexType name="Wohngebaeude-Bedarfs-Daten-18599">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält im Falle eines Wohngebäudes mit Nachweis
        nach DIN V 18599 die Bedarfswerte. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Wohngebaeude-Anbaugrad" type="n1:Wohngebaeude-Anbaugrad-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anbaugrad des Gebäudes bzw. Gebäudeteils an (beheizte)
            Nachbargebäude. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Bruttovolumen" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Beheiztes Gebäudevolumen des Gebäudes/Gebäudeteils (ganze
            Kubikmeter) (Bruttovolumen: V_e). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="1000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="durchschnittliche-Geschosshoehe" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Durchschnittliche Geschosshöhe hG des
            Gebäudes/Gebäudeteils in m zur Festlegung, über welche Formel (Anl. 1 Nr. 1.3.3 Satz 1
            oder 2) die Nutzfläche AN bestimmt wurde (Durchschnittliche Geschosshöhe: h_G). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Bauteil-Opak" type="n1:Bauteil-Opak-Daten" minOccurs="1" maxOccurs="10000" />
      <xs:element name="Bauteil-Transparent" type="n1:Bauteil-Transparent-Daten" minOccurs="0"
        maxOccurs="10000" />
      <xs:element name="Bauteil-Dach" type="n1:Bauteil-Dach-Daten" minOccurs="0" maxOccurs="10000" />
      <xs:element name="Waermebrueckenzuschlag" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wert des Wärmebrückenzuschlags für die Gebäudehülle
            (Wärmebrückenzuschlag: delta_U_WB). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="3" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="-0.999" />
            <xs:maxInclusive value="0.999" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Transmissionswaermesenken" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Transmissionswärmesenken in kWh/a
            (Transmissionswärmesenken: Q_T). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Luftdichtheit" type="n1:Luftdichtheit-18599-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Luftdichtheit der Gebäudehülle. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Lueftungswaermesenken" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Lüftungswärmesenken in kWh/a (Lüftungswärmesenken: Q_V). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Waermequellen-durch-solare-Einstrahlung" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Summe der Wärmequellen durch solare Einstrahlung in kWh/a
            (Wärmequellen durch solare Einstrahlung: Q_S). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Interne-Waermequellen" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Summe der inneren Gewinne in kWh/a (Interne-Wärmequellen:
            Q_I,source) Info: Interne Wärmequellen nach DIN V 18599 berücksichtigen die ungeregelten
            Wärmequellen aus der Anlagentechnik nicht. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Heizsystem" type="n1:Heizungsanlage-Daten" minOccurs="1" maxOccurs="200">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angaben zum / zu den Wärmeerzeuger(n); ist ein Gebäude
            ausschließlich passiv solar beheizt (Fenstereinstrahlung), ist als Wärmeerzeuger
            "Sonstiges" anzugeben. Ein Hinweis im Erläuterungsfeld auf Seite 4 des Energieausweises
            ist für diesen Fall zu empfehlen. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Pufferspeicher-Nenninhalt" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Volumen eines ggfs. vorhandenen Heizungs-Pufferspeichers
            (keiner =0) in Liter (Pufferspeicher-Nenninhalt: V_s). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Auslegungstemperatur" type="n1:Heizkreisauslegungstemperatur-enum"
        minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Temperaturniveau der Heizungsverteilung Vorlauf/Rücklauf,
            bzw. Angabe von Luftheizsystem oder ausschliesslicher Beheizung über
            Einzelraumheizgeräte; anzugeben ist die Temperatur des höchsten Kreises, bei krummen
            Werten ist die nach Vorlauftemperatur nächst höhere Auswahl anzugeben. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Heizsystem-innerhalb-Huelle" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> TRUE, wenn alle Wärmeerzeuger innerhalb der thermischen
            Gebäudehülle stehen. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Warmwasserbereitungssystem" type="n1:Trinkwarmwasseranlage-Daten"
        minOccurs="1" maxOccurs="200">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angaben zu dem / den Warmwassererzeuger(n). </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Trinkwarmwasserspeicher-Nenninhalt" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Volumen eines ggfs. vorhandenen Warmwasserspeichers (kein
            Speicher = 0) bzw. Summe der Volumina bei mehreren Speichern, in Liter
            (Trinkwarmwasserspeicher-Nenninhalt: V_s). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Trinkwarmwasserverteilung-Zirkulation" type="xs:boolean" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Ist zur Warmwasserverteilung eine Trinkwasser-seitige
            Zirkulation vorhanden? </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Vereinfachte-Datenaufnahme" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wurden Regeln zur vereinfachten Datenaufnahme nach § 50
            (4) GEG bzw. Bekanntmachungen angewendet? </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="spezifischer-Transmissionswaermetransferkoeffizient-Ist" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Spezifischer auf die wärmeübertragende Umfassungsfläche
            bezogener Transmissionswärmeverlust HT', Ist-Wert des Gebäudes, in W/m²K (spezifischer
            Transmissionswärmetransferkoeffizient: H_T(Strich)). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="spezifischer-Transmissionswaermetransferkoeffizient-Hoechstwert"
        minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Spezifischer auf die wärmeübertragende Umfassungsfläche
            bezogener Transmissionswärmeverlust HT', Anforderungswert, in W/m²K, nur bei Neubau und
            wesentlicher Modernisierung/Erweiterungen (spezifischer
            Transmissionswärmetransferkoeffizient: H_T(Strich)). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="angerechneter-lokaler-erneuerbarer-Strom" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Größe des Abzugs (in kWh/a m2) bei der Primärenergie bzw.
            bei der Endenergie für den gebäudenah erzeugten Strom aus erneuerbarer Energie nach der
            entsprechenden Bilanzierungsregel (vgl. GEG § 23 (2) und (3) bzw. GEG §23 (4)) </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Innovationsklausel" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe ob Innovationsklausel gemäß § 103 (1) GEG 2020
            angewendet wurde. (Alternative Anforderungen: Treibhausgasemissionen, Höchstwert der
            Endenergiebedarfs + Transmissionswärmeverlust (nur für Neubau und WG) +
            Wärmedurchgangskoeffizienten der wärmeübertragenden Umfassungsfläche (nur für Neubau und
            NWG) - Aussetzung der Hauptanforderungen (Neubau § 10, Bestand § 50)) </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Quartiersregelung" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe ob Quartiersregelung gemäß § 103 (3) -
            Gesamtbilanzierung für Wärmeversorgung zusammenhängender Gebäude - zutreffend ist. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Primaerenergiebedarf-Hoechstwert-Bestand" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergie-Anforderungswert Modernisierter Altbau in
            kWh/m²a bezogen auf die energetische Nutzfläche, nur bei Ausstellungsanlass
            Modernisierung. Wenn entsprechend dem Ausstellungsanlass kein Wert zu übermitteln ist,
            kann eine 0 eingetragen werden, da dieser Wert dann nicht relevant ist. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:choice>
        <xs:sequence>
          <xs:element name="Endenergiebedarf-Hoechstwert-Bestand" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Endenergie-Anforderungswert Modernisierter Altbau in
                kWh/m²a bezogen auf die energetische Nutzfläche, nur bei Ausstellungsanlass
                Modernisierung. Wenn entsprechend dem Ausstellungsanlass kein Wert zu übermitteln
                ist, kann eine 0 eingetragen werden, da dieser Wert dann nicht relevant ist. </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="7" />
                <xs:minInclusive value="0" />
                <xs:maxExclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
          <xs:element name="Treibhausgasemissionen-Hoechstwert-Bestand" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Anforderungswert für Treibhausgasemissionen
                Modernisierter Altbau in kg/m²a bezogen auf die energetische Nutzfläche, nur bei
                Ausstellungsanlass Modernisierung. Wenn entsprechend dem Ausstellungsanlass kein
                Wert zu übermitteln ist, kann eine 0 eingetragen werden, da dieser Wert dann nicht
                relevant ist. </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="8" />
                <xs:minInclusive value="-100000" />
                <xs:maxInclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
        </xs:sequence>
        <xs:sequence>
          <xs:element name="Primaerenergiebedarf-Hoechstwert-Neubau" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Primärenergie-Anforderungswert Neubau (Kennwert des
                Referenzgebäudes, ab 2016 mit Berücksichtigung des entspr. Faktors) in kWh/m²a
                bezogen auf die energetische Nutzfläche, bei Neubau im Energieausweis einzutragen.
                (Primärenergiebedarf: Q_p,Ref). </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="7" />
                <xs:minInclusive value="0" />
                <xs:maxExclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
          <xs:element name="Endenergiebedarf-Hoechstwert-Neubau" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Endenergiebedarf-Anforderungswert Neubau (Kennwert
                des Referenzgebäudes, ab 2016 mit Berücksichtigung des entspr. Faktors) in kWh/m²a
                bezogen auf die energetische Nutzfläche. </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="7" />
                <xs:minInclusive value="0" />
                <xs:maxExclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
          <xs:element name="Treibhausgasemissionen-Hoechstwert-Neubau" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Anforderungswert für Treibhausgasemissionen Neubau
                (Kennwert des Referenzgebäudes) in kg/m²a bezogen auf die energetische Nutzfläche. </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="8" />
                <xs:minInclusive value="-100000" />
                <xs:maxInclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
        </xs:sequence>
      </xs:choice>
      <xs:element name="Energietraeger-Liste" type="n1:Endenergie-Energietraeger-Daten"
        minOccurs="1" maxOccurs="10" />
      <xs:element name="Endenergiebedarf-Waerme-AN" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiekennwert Wärme in kWh/m²a bezogen auf die
            energetische Nutzfläche (Endenergiebedarf-Wärme: Q_f). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="6" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Hilfsenergie-AN" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiekennwert Hilfsenergie in kWh/m²a bezogen auf
            die energetische Nutzfläche (Endenergiebedarf-Hilfsenergie: W_f). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="6" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Gesamt" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiekennwert Gesamt in kWh/m²a bezogen auf die
            energetische Nutzfläche. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="6" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Primaerenergiebedarf-AN" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiekennwert in kWh/m²a bezogen auf die
            energetische Nutzfläche (Primärenergiebedarf: Q_p). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Energieeffizienzklasse" type="n1:Energieeffizienzklasse-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Energieeffizienzklasse des Gebäudes. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Art-der-Nutzung-erneuerbaren-Energie-1"
        type="n1:Art-der-Nutzung-erneuerbaren-Energie-enum" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Es wird die erste Angabe im Energieausweis zur Nutzung
            erneuerbarer Energien nach GEG Abschnit 4 § 34 erwartet, relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Deckungsanteil-1" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Deckungsanteil in % für erste Angabe zur Nutzung
            erneuerbarer Energien nach GEG Abschnitt 4 § 34 (Neubau), relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Anteil-der-Pflichterfuellung-1" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Pflichterfüllungsanteil in % für die erste Angabe zur
            Nutzung erneuerbarer Energien nach GEG. (§ 34: Die prozentualen Anteile der
            tatsächlichen Nutzung der einzelnen Maßnahmen im Verhältnis der jeweils nach den § 35
            bis § 45 vorgesehenen Nutzung müssen in der Summe mindestens 100 Prozent Erfüllungsgrad
            ergeben.) </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="999" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Art-der-Nutzung-erneuerbaren-Energie-2"
        type="n1:Art-der-Nutzung-erneuerbaren-Energie-enum" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Es wird die zweite Angabe im Energieausweis zur Nutzung
            erneuerbarer Energien nach GEG Abschnit 4 § 34 erwartet, relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Deckungsanteil-2" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Deckungsanteil in % für zweite Angabe Angabe zur Nutzung
            erneuerbarer Energien nach GEG Abschnitt 4 § 34 (Neubau), relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Anteil-der-Pflichterfuellung-2" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Pflichterfüllungsanteil in % für die zweite Angabe zur
            Nutzung erneuerbarer Energien nach GEG. (§ 34: Die prozentualen Anteile der
            tatsächlichen Nutzung der einzelnen Maßnahmen im Verhältnis der jeweils nach den § 35
            bis § 45 vorgesehenen Nutzung müssen in der Summe mindestens 100 Prozent Erfüllungsgrad
            ergeben.) </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="999" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="verschaerft-nach-GEG-45-eingehalten" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Der gemäß § 45 GEG um diese Prozentzahl verschärfte
            Anforderungswert (15 % Unterschreitung Transmissionswärmeverlust) als Maßnahme zur
            Einsparung von Energie ist eingehalten. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:boolean" />
        </xs:simpleType>
      </xs:element>
      <xs:choice>
        <xs:element name="nicht-verschaerft-nach-GEG-34" type="xs:boolean" minOccurs="1"
          maxOccurs="1" fixed="true">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Keine Maßnahmen nach § 45 in Verbindung mit $ 34. </xs:documentation>
          </xs:annotation>
        </xs:element>
        <xs:sequence>
          <xs:element name="verschaerft-nach-GEG-34" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Die in Verbindung mit § 34 GEG Maßnahmen nach § 45 in
                Kombination zur Nutzung erneuerbarer Energien zur Deckung des Wärme- und
                Kälteenergiebedarfs sind eingehalten (Anteil der Pflichterfüllung in %). </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:int">
                <xs:minInclusive value="0" />
                <xs:maxInclusive value="100" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
          <xs:element name="Anforderung-nach-GEG-16-unterschritten" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Die Anforderung nach $ 16 GEG wurde unterschritten
                (in %). </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:int">
                <xs:minInclusive value="0" />
                <xs:maxInclusive value="100" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
        </xs:sequence>
      </xs:choice>
      <xs:element name="spezifischer-Transmissionswaermetransferkoeffizient-verschaerft"
        minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Nach EEWärmeG verschärfter Anforderungswert für den
            spezifischen auf die wärmeübertragende Umfassungsfläche bezogenen
            Transmissionswärmeverlust HT' in W/m²K
            (spezifischer-Transmissionswärmetransferkoeffizient-verschärft: H_T(Strich)). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Sommerlicher-Waermeschutz" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Sind die Anforderungen an den sommerlichen Wärmeschutz
            eingehalten? Relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:boolean" />
        </xs:simpleType>
      </xs:element>
      <xs:element name="Treibhausgasemissionen-Zusaetzliche-Verbrauchsdaten" minOccurs="0"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe der Treibhausgasemissionen in kg als CO2
            Äquivalent/(m²a); nur bei kombinierten Energieausweisen (Bedarf/Verbrauch), zusammen mit
            der Übermittlung der zusätzlichen Verbrauchsdaten. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="8" />
            <xs:minInclusive value="-100000" />
            <xs:maxInclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Zusaetzliche-Verbrauchsdaten" type="n1:Wohngebaeude-Verbrauchs-Daten"
        minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Bei Energieausweisen auf Bedarfsbasis können neben den
            Bedarfsangaben zusätzlich Angaben zum Verbrauch im Energieausweis dargestellt werden,
            die dann hier entsprechend integriert werden. Die Ermittlung der Fläche AN aus der
            Wohnfläche darf in den Wohngebäude-Verbrauchs-Daten dann nicht angekreuzt sein, da die
            Fläche nach einem für Bedarfsausweise zulässigen Verfahren ermittelt worden sein muss. </xs:documentation>
        </xs:annotation>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="Nichtwohngebaeude-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält im Falle eines Nichtwohngebäudes alle
        weiteren energetisch relevanten Daten. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Hauptnutzung-Gebaeudekategorie" type="n1:Nutzung-Gebaeudekategorie-enum"
        minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Pflichtangabe der Hauptnutzungsart des Gebäudes. Die
            Auswahl der Hauptnutzung-Gebaeudekategorie ergibt sich aus der "Bekanntmachung der
            Regeln für Energieverbrauchswerte und der Vergleichswerte im Nichtwohngebäudebestand"
            unter der Anlage 1 Tabelle 1 Teilenergiekennwerte (TEK) nach Gebäudekategorien. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Hauptnutzung-Gebaeudekategorie-Sonstiges-Beschreibung" minOccurs="0"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Sollte keine der 52 Gebäudekategorien laut Bekanntmachung
            für die Hauptnutzung zutreffend sein, muss in
            Hauptnutzung-Gebaeudekategorie-Sonstiges-Beschreibung eine alternative Bezeichnung
            eingetragen werden. Für die Seite 3 im Energieverbrauchsausweis Nichtwohngebäude darf
            die Gebäudekategorie '53:Sonstiges' nicht verwendet werden. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{1,84}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Nettogrundflaeche" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Nettogrundfläche als energetische Nutzfläche eines
            Gebäudes/Gebäudeteils nach DIN V 18599: 2018-09 (ganze Quadratmeter) (Nettogrundfläche:
            A_NGF). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="1000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:choice>
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Dieser Block enthält entweder Verbrauchs- oder
            Bedarfswerte; beim Bedarfsausweis können zusätzliche Verbrauchsangaben angeführt werden. </xs:documentation>
        </xs:annotation>
        <xs:element name="Verbrauchswerte-NWG" type="n1:Nichtwohngebaeude-Verbrauchs-Daten" />
        <xs:element name="Bedarfswerte-NWG" type="n1:Nichtwohngebaeude-Bedarfs-Daten" />
      </xs:choice>
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="Energieeffizienzklasse-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="A+" />
      <xs:enumeration value="A" />
      <xs:enumeration value="B" />
      <xs:enumeration value="C" />
      <xs:enumeration value="D" />
      <xs:enumeration value="E" />
      <xs:enumeration value="F" />
      <xs:enumeration value="G" />
      <xs:enumeration value="H" />
    </xs:restriction>
  </xs:simpleType>
  <xs:complexType name="Nichtwohngebaeude-Verbrauchs-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält im Falle eines
        Nichtwohngebäude-Verbrauchsausweises die Verbrauchsdaten. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Energietraeger" type="n1:Energietraeger-Daten" minOccurs="1" maxOccurs="8">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Jeweiliger Energieträger mit zugehörigen Verbrauchsdaten. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Warmwasser-enthalten" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Ist als TRUE anzugeben, wenn mindestens einer der (ggfs.
            mehreren) Heizenergieträger auch zur Warmwasserbereitung dient, die entspr. Mengen sind
            in der Verbrauchsliste anzugeben, entspricht dem Kreuzchenfeld im
            Energieausweis-Nichtwohngebäude, Verbrauchsseite, direkt links unter der Verbrauchsskala
            Endenergieverbrauch Wärme. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Kuehlung-enthalten" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Ist als TRUE anzugeben, wenn mindestens einer der (ggfs.
            mehreren) Heizenergieträger auch zur Kühlung dient, die entspr. Mengen sind in der
            Verbrauchsliste anzugeben, entspricht dem Kreuzchenfeld im
            Energieausweis-Nichtwohngebäude, Verbrauchsseite, direkt links unter der Verbrauchsskala
            Endenergieverbrauch Kühlung. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Strom-Daten">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Es ist anzugeben, was im Stromverbrauch alles enthalten
            ist, entsprechend der Kreuzchenfelder im Energieausweis sowie zusätzlich
            Verbrauchsperioden-Strom. </xs:documentation>
        </xs:annotation>
        <xs:complexType>
          <xs:sequence>
            <xs:element name="Stromverbrauch-enthaelt-Zusatzheizung" type="xs:boolean" minOccurs="1"
              maxOccurs="1">
              <xs:annotation>
                <xs:documentation xml:lang="DE"> Angaben, was der Stromverbrauch für
                  Funktionalitäten umfasst: Zusatzheizung. </xs:documentation>
              </xs:annotation>
            </xs:element>
            <xs:element name="Stromverbrauch-enthaelt-Warmwasser" type="xs:boolean" minOccurs="1"
              maxOccurs="1">
              <xs:annotation>
                <xs:documentation xml:lang="DE"> Angaben, was der Stromverbrauch für
                  Funktionalitäten umfasst: Warmwasserbereitung. </xs:documentation>
              </xs:annotation>
            </xs:element>
            <xs:element name="Stromverbrauch-enthaelt-Lueftung" type="xs:boolean" minOccurs="1"
              maxOccurs="1">
              <xs:annotation>
                <xs:documentation xml:lang="DE"> Angaben, was der Stromverbrauch für
                  Funktionalitäten umfasst: Lüftung. </xs:documentation>
              </xs:annotation>
            </xs:element>
            <xs:element name="Stromverbrauch-enthaelt-Beleuchtung" type="xs:boolean" minOccurs="1"
              maxOccurs="1">
              <xs:annotation>
                <xs:documentation xml:lang="DE"> Angaben, was der Stromverbrauch für
                  Funktionalitäten umfasst: eingebaute Beleuchtung. </xs:documentation>
              </xs:annotation>
            </xs:element>
            <xs:element name="Stromverbrauch-enthaelt-Kuehlung" type="xs:boolean" minOccurs="1"
              maxOccurs="1">
              <xs:annotation>
                <xs:documentation xml:lang="DE"> Angaben, was der Stromverbrauch für
                  Funktionalitäten umfasst: Kühlung </xs:documentation>
              </xs:annotation>
            </xs:element>
            <xs:element name="Stromverbrauch-enthaelt-Sonstiges" type="xs:boolean" minOccurs="1"
              maxOccurs="1">
              <xs:annotation>
                <xs:documentation xml:lang="DE"> Angaben, was der Stromverbrauch für
                  Funktionalitäten umfasst: Sonstiges. </xs:documentation>
              </xs:annotation>
            </xs:element>
            <xs:element name="Zeitraum-Strom" type="n1:Zeitraum-Strom-Daten" minOccurs="1"
              maxOccurs="40" />
          </xs:sequence>
        </xs:complexType>
      </xs:element>
      <xs:element name="Leerstandszuschlag-Heizung" type="n1:Leerstandszuschlag-Heizung-Daten">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Im dem Energieausweis zugrunde liegenden Zeitraum ist
            Leerstand aufgetreten, es wurde eine Leerstandskorrektur entsprechend der Bekanntmachung
            der Regeln für Energieverbrauchskennwerte vorgenommen. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Leerstandszuschlag-Warmwasser" type="n1:Leerstandszuschlag-Warmwasser-Daten">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Im dem Energieausweis zugrunde liegenden Zeitraum ist
            Leerstand aufgetreten, es wurde eine Leerstandskorrektur entsprechend der Bekanntmachung
            der Regeln für Energieverbrauchskennwerte vorgenommen. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Leerstandszuschlag-thermisch-erzeugte-Kaelte"
        type="n1:Leerstandszuschlag-thermisch-erzeugte-Kaelte-Daten">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Im dem Energieausweis zugrunde liegenden Zeitraum ist
            Leerstand aufgetreten, es wurde eine Leerstandskorrektur entsprechend der Bekanntmachung
            der Regeln für Energieverbrauchskennwerte vorgenommen. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Leerstandszuschlag-Strom" type="n1:Leerstandszuschlag-Strom-Daten">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Im dem Energieausweis zugrunde liegenden Zeitraum ist
            Leerstand aufgetreten, es wurde eine Leerstandskorrektur entsprechend der Bekanntmachung
            der Regeln für Energieverbrauchskennwerte vorgenommen. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Nutzung-Gebaeudekategorie" type="n1:Nutzung-Gebaeudekategorie-Daten"
        minOccurs="1" maxOccurs="5">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe der Nutzung des Gebäudes in Reihenfolge ihres
            Anteils der Energiebezugsfläche. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Endenergieverbrauch-Waerme" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiekennwert Heizung und zentrales Warmwasser in
            kWh/m²a bezogen auf die energetische Nutzfläche (Endenergieverbrauch Wärme:
            e(Strich)_Vb,12mth). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergieverbrauch-Strom" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiekennwert Strom in kWh/m²a bezogen auf die
            energetische Nutzfläche (Endenergieverbrauch Strom: e(Strich)_Vs,12mth). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergieverbrauch-Waerme-Vergleichswert" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergievergleichswert Heizung und zentrales Warmwasser
            in kWh/m²a bezogen auf die energetische Nutzfläche, entsprechend den anteiligen
            Nutzungen. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergieverbrauch-Strom-Vergleichswert" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergievergleichswert Strom in kWh/m²a bezogen auf die
            energetische Nutzfläche, entsprechend den anteiligen Nutzungen. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Primaerenergieverbrauch" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiekennwert in kWh/m²a bezogen auf die
            energetische Nutzfläche. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="Zeitraum-Strom-Daten">
    <xs:annotation>
      <xs:documentation> Umfasst den allgemeinen Stromverbrauch im Gebäude. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Startdatum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anfangsdatum der Periode, angegeben als YYYY-MM-DD. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:date">
            <xs:minInclusive value="2000-01-01" />
            <xs:maxInclusive value="2100-01-01" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Enddatum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Enddatum der Periode, angegeben als YYYY-MM-DD. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:date">
            <xs:minInclusive value="2000-01-01" />
            <xs:maxInclusive value="2100-01-01" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Energieverbrauch-Strom" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Verbrauchswert Strom in kWh. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Energieverbrauchsanteil-elektrisch-erzeugte-Kaelte" minOccurs="0"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Im Verbrauchswert enthaltener Wert für elektrisch
            erzeugte Kälte in kWh. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="Nutzung-Gebaeudekategorie-Daten">
    <xs:sequence>
      <xs:element name="Gebaeudekategorie" type="n1:Nutzung-Gebaeudekategorie-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe der Nutzung des Gebäudes in Reihenfolge ihres
            Anteils der Energiebezugsfläche. Die Auswahl der Nutzung-Gebaeudekategorie ergibt sich
            aus der "Bekanntmachung der Regeln für Energieverbrauchswerte und der Vergleichswerte im
            Nichtwohngebäudebestand" unter der Anlage 1 Tabelle 1 Teilenergiekennwerte (TEK) nach
            Gebäudekategorien. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Flaechenanteil-Nutzung" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Prozentualer Anteil dieser Nutzung an der gesamten
            Nettogrundfläche des Gebäudes. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Vergleichswert-Waerme" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe zum zugehörigen Vergleichswert Wärme der
            angegebenen Nutzungskategorie des Gebäudes. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="1" />
            <xs:totalDigits value="6" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Vergleichswert-Strom" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe zum zugehörigen Vergleichswert Strom der
            angegebenen Nutzungskategorie des Gebäudes. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="1" />
            <xs:totalDigits value="6" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="Nutzung-Gebaeudekategorie-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="1:Verwaltungsgebäude (allgemein)" />
      <xs:enumeration value="2:Parlaments- und Gerichtsgebäude" />
      <xs:enumeration value="3:Ministerien u. Ämter u. Behörden" />
      <xs:enumeration value="4:Polizeidienstgebäude" />
      <xs:enumeration value="5:Gebäude für öffentliche Bereitschaftsdienste" />
      <xs:enumeration value="6:Feuerwehrdienstgebäude" />
      <xs:enumeration value="7:Bürogebäude" />
      <xs:enumeration value="8:Bürogebäude - überwiegend Großraumbüros" />
      <xs:enumeration value="9:Bankgebäude" />
      <xs:enumeration value="10:Hochschule und Forschung (allgemein)" />
      <xs:enumeration value="11:Gebäude für Lehre" />
      <xs:enumeration value="12:Institute für Lehre und Forschung" />
      <xs:enumeration value="13:Gebäude für Forschung ohne Lehre" />
      <xs:enumeration value="14:Laborgebäude" />
      <xs:enumeration value="15:Gesundheitswesen (allgemein)" />
      <xs:enumeration value="16:Krankenhäuser (ohne Forschung und Lehre)" />
      <xs:enumeration
        value="17:Krankenhäuser (ohne Forschung und Lehre) und teilstationäre Versorgung" />
      <xs:enumeration value="18:Medizinische Einrichtungen für nicht stationäre Versorgung" />
      <xs:enumeration value="19:Gebäude für Reha, Kur und Genesung" />
      <xs:enumeration value="20:Bildungseinrichtungen (allgemein)" />
      <xs:enumeration value="21:Schulen" />
      <xs:enumeration value="22:Kinderbetreuungseinrichtungen" />
      <xs:enumeration value="23:Kultureinrichtungen (allgemein)" />
      <xs:enumeration value="24:Bibliotheken / Archive" />
      <xs:enumeration value="25:Ausstellungsgebäude" />
      <xs:enumeration value="26:Veranstaltungsgebäude" />
      <xs:enumeration value="27:Gemeinschafts- / Gemeindehäuser" />
      <xs:enumeration value="28:Opern / Theater" />
      <xs:enumeration value="29:Sporteinrichtungen (allgemein)" />
      <xs:enumeration value="30:Sporthallen" />
      <xs:enumeration value="31:Fitnessstudios" />
      <xs:enumeration value="32:Schwimmhallen" />
      <xs:enumeration value="33:Gebäude für Sportaußenanlagen" />
      <xs:enumeration value="34:Verpflegungseinrichtungen (allgemein)" />
      <xs:enumeration value="35:Beherbergungsstätten (allgemein)" />
      <xs:enumeration value="36:Hotels / Pensionen" />
      <xs:enumeration value="37:Jugendherbergen u. Ferienhäuser" />
      <xs:enumeration value="38:Gaststätten" />
      <xs:enumeration value="39:Mensen u. Kantinen" />
      <xs:enumeration value="40:Gewerbliche und industrielle Gebäude (allgemein)" />
      <xs:enumeration
        value="41:Gewerbliche und industrielle Gebäude - schwere Arbeit, stehende Tätigkeit" />
      <xs:enumeration
        value="42:Gewerbliche und industrielle Gebäude - Mischung aus leichter u. schwerer Arbeit" />
      <xs:enumeration
        value="43:Gewerbliche und industrielle Gebäude - leichte Arbeit, überwiegend sitzende Tätigkeit" />
      <xs:enumeration value="44:Gebäude für Lagerung" />
      <xs:enumeration value="45:Verkaufsstätten (allgemein)" />
      <xs:enumeration value="46:Kaufhäuser" />
      <xs:enumeration value="47:Kaufhauszentren / Einkaufszentren" />
      <xs:enumeration value="48:Märkte" />
      <xs:enumeration value="49:Märkte mit sehr hohem Anteil von Kühlung für Lebensmittel" />
      <xs:enumeration value="50:Läden" />
      <xs:enumeration value="51:Läden mit sehr hohem Anteil von Kühlung für Lebensmittel" />
      <xs:enumeration value="52:Fernmeldetechnik" />
      <xs:enumeration value="53:Sonstiges">
        <xs:annotation>
          <xs:documentation> Im Fall der Nutzung von '53:Sonstiges' ist dann auch das Feld
            'Hauptnutzung-Gebaeudekategorie-Sonstiges-Beschreibung' zu befüllen. Für die Seite 3 im
            Energieverbrauchsausweis Nichtwohngebäude darf die Gebäudekategorie '53:Sonstiges' nicht
            verwendet werden. </xs:documentation>
        </xs:annotation>
      </xs:enumeration>
    </xs:restriction>
  </xs:simpleType>
  <xs:complexType name="Nichtwohngebaeude-Bedarfs-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält im Falle eines Nichtwohngebäudes die
        Bedarfswerte. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Bruttovolumen" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Konditioniertes brutto-Gebäudevolumen des
            Gebäudes/Gebäudeteils (ganze Kubikmeter) (Bruttovolumen: V_e) ggf. überschlägig
            ermittelter Wert. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Bauteil-Opak" type="n1:Bauteil-Opak-Daten" minOccurs="1" maxOccurs="10000" />
      <xs:element name="Bauteil-Transparent" type="n1:Bauteil-Transparent-Daten" minOccurs="0"
        maxOccurs="10000" />
      <xs:element name="Bauteil-Dach" type="n1:Bauteil-Dach-Daten" minOccurs="0" maxOccurs="10000" />
      <xs:element name="Waermebrueckenzuschlag" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wert des Wärmebrückenzuschlags für die Gebäudehülle
            (Wärmebrückenzuschlag: delta_U_WB). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="3" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="-0.999" />
            <xs:maxInclusive value="0.999" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="mittlere-Waermedurchgangskoeffizienten" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Sind die Anforderungen an die mittleren
            Wärmedurchgangskoeffizienten der verschiedenen Hüllflächenbauteile (Anlage 3 GEG)
            eingehalten? Relevant nur bei Neubau und umfassender Modernisierung. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:boolean" />
        </xs:simpleType>
      </xs:element>
      <xs:element name="Transmissionswaermesenken" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Transmissionswärmesenken in kWh/a
            (Transmissionswärmesenken: Q_T). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Luftdichtheit" type="n1:Luftdichtheit-18599-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Luftdichtheit der Gebäudehülle. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Zone" type="n1:Zonen-Daten" minOccurs="1" maxOccurs="200" />
      <xs:element name="Heizsystem" type="n1:Heizungsanlage-Daten" minOccurs="1" maxOccurs="200">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angaben zum / zu den Wärmeerzeuger(n); ist ein Gebäude
            ausschließlich passiv solar beheizt (Fenstereinstrahlung), ist als Wärmeerzeuger
            "Sonstiges" anzugeben. Ein Hinweis im Erläuterungsfeld auf Seite 4 des Energieausweises
            ist für diesen Fall zu empfehlen. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Pufferspeicher-Nenninhalt" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Volumen eines ggfs. vorhandenen Heizungs-Pufferspeichers
            (keiner = 0) in Liter (Pufferspeicher-Nenninhalt: V_s). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Auslegungstemperatur" type="n1:Heizkreisauslegungstemperatur-enum"
        minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Temperaturniveau der Heizungsverteilung Vorlauf/Rücklauf,
            bzw. Angabe von Luftheizsystem oder ausschliesslicher Beheizung über
            Einzelraumheizgeräte; anzugeben ist die Temperatur des höchsten Kreises, bei krummen
            Werten ist die nach Vorlauftemperatur nächst höhere Auswahl anzugeben. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Heizsystem-innerhalb-Huelle" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> TRUE, wenn alle Wärmeerzeuger innerhalb der thermischen
            Gebäudehülle stehen. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Warmwasserbereitungssystem" type="n1:Trinkwarmwasseranlage-Daten"
        minOccurs="0" maxOccurs="50">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Warmwassererzeuger, bei mehreren sinnvollerweise nach
            abnehmender energetischer Relevanz. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Trinkwarmwasserspeicher-Nenninhalt" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Volumen eines ggfs. vorhandenen Warmwasserspeichers (kein
            Speicher = 0) bzw. Summe der Volumina bei mehreren Speichern, in Liter
            (Trinkwarmwasserspeicher-Nenninhalt: V_s). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Trinkwarmwasserverteilung-Zirkulation" type="xs:boolean" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Ist zur Warmwasserverteilung eine Trinkwasser-seitige
            Zirkulation vorhanden? </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Kaelteanlage" type="n1:Kaelteanlage-Daten" minOccurs="0" maxOccurs="30">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Kälteerzeuger, bei mehreren sinnvollerweise nach
            abnehmender energetischer Relevanz. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Deckungsanteil-RLT-Kuehlung" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anteil der Kälte, die über RLT-Anlagen verteilt und
            übergeben wird, in %. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Deckungsanteil-Direkte-Raumkuehlung" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anteil der Kälte, die über Direktkühlanlagen (Kühldecken,
            Kühlsegel usw.) verteilt und übergeben wird, in %. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="RLT-System" type="n1:RLT-Anlagen-Daten" minOccurs="0" maxOccurs="100">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angaben zu vorhandenen Lüftungsanlagen, bei mehreren
            sinnvollerweise nach abnehmender Relevanz. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Automatisierungsgrad" type="n1:Automatisierungsgrad-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Überwiegende Gebäueautomationsklasse für Heizen, Kühlen
            und mechanisches Belüften (soweit vorhanden) in den Nutzungsräumen nach DIN V 18599-11. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Automatisierungsgrad-Technisches-Gebaeudemanagement"
        type="n1:Automatisierungsgrad-Technisches-Gebaeudemanagement-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Gebäudeautomationsklasse für das übergreifende
            Gebäudemanagement nach DIN V 18599-11, Tab. 3 Z. 94-96. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="angerechneter-lokaler-erneuerbarer-Strom" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Größe des Abzugs (in kWh/a m2) bei der Primärenergie bzw.
            bei der Endenergie für den gebäudenah erzeugten Strom aus erneuerbarer Energie nach der
            entsprechenden Bilanzierungsregel (vgl. GEG § 23 (2) und (3) bzw. GEG §23 (4)) </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Innovationsklausel" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe ob Innovationsklausel gemäß § 103 (1) GEG 2020
            angewendet wurde. (Alternative Anforderungen: Treibhausgasemissionen, Höchstwert der
            Endenergiebedarfs + Transmissionswärmeverlust (nur für Neubau und WG) +
            Wärmedurchgangskoeffizienten der wärmeübertragenden Umfassungsfläche (nur für Neubau und
            NWG) - Aussetzung der Hauptanforderungen (Neubau § 10, Bestand § 50)) </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Quartiersregelung" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe ob Quartiersregelung gemäß § 103 (3) -
            Gesamtbilanzierung für Wärmeversorgung zusammenhängender Gebäude - zutreffend ist. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Primaerenergiebedarf-Hoechstwert-Bestand" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergie-Anforderungswert Modernisierter Altbau in
            kWh/m²a bezogen auf die energetische Nutzfläche. Außerdem richtet sich die Länge der
            grün-rot-Skala nach diesem Wert (Skalenendewert das Dreifache des Wertes), daher ist der
            Wert immer erforderlich. (Primärenergiebedarf: Q_p,Ref) </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:choice>
        <xs:sequence>
          <xs:element name="Endenergiebedarf-Hoechstwert-Bestand" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Endenergie-Anforderungswert Modernisierter Altbau in
                kWh/m²a bezogen auf die energetische Nutzfläche, nur bei Ausstellungsanlass
                Modernisierung. Wenn entsprechend dem Ausstellungsanlass kein Wert zu übermitteln
                ist, kann eine 0 eingetragen werden, da dieser Wert dann nicht relevant ist. </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="7" />
                <xs:minInclusive value="0" />
                <xs:maxExclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
          <xs:element name="Treibhausgasemissionen-Hoechstwert-Bestand" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Anforderungswert für Treibhausgasemissionen
                Modernisierter Altbau in kg/m²a bezogen auf die energetische Nutzfläche, nur bei
                Ausstellungsanlass Modernisierung. Wenn entsprechend dem Ausstellungsanlass kein
                Wert zu übermitteln ist, kann eine 0 eingetragen werden, da dieser Wert dann nicht
                relevant ist. </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="8" />
                <xs:minInclusive value="-100000" />
                <xs:maxInclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
        </xs:sequence>
        <xs:sequence>
          <xs:element name="Primaerenergiebedarf-Hoechstwert-Neubau" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Primärenergie-Anforderungswert Neubau (Kennwert des
                Referenzgebäudes, ab 2016 mit Berücksichtigung des entspr. Faktors) in kWh/m²a
                bezogen auf die energetische Nutzfläche, bei Neubau im Energieausweis einzutragen.
                (Primärenergiebedarf: Q_p,Ref). </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="7" />
                <xs:minInclusive value="0" />
                <xs:maxExclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
          <xs:element name="Endenergiebedarf-Hoechstwert-Neubau" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Endenergiebedarf-Anforderungswert Neubau (Kennwert
                des Referenzgebäudes, ab 2016 mit Berücksichtigung des entspr. Faktors) in kWh/m²a
                bezogen auf die energetische Nutzfläche. </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="7" />
                <xs:minInclusive value="0" />
                <xs:maxExclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
          <xs:element name="Treibhausgasemissionen-Hoechstwert-Neubau" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Anforderungswert für Treibhausgasemissionen Neubau
                (Kennwert des Referenzgebäudes) in kg/m²a bezogen auf die energetische Nutzfläche. </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:decimal">
                <xs:fractionDigits value="2" />
                <xs:totalDigits value="8" />
                <xs:minInclusive value="-100000" />
                <xs:maxInclusive value="100000" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
        </xs:sequence>
      </xs:choice>
      <xs:element name="Energietraeger-Liste" type="n1:Endenergie-Energietraeger-Daten"
        minOccurs="1" maxOccurs="10" />
      <xs:element name="Endenergiebedarf-Waerme-NGF" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiekennwert Wärme in kWh/m²a bezogen auf die
            Nettogrundfläche (Endenergiebedarf-Wärme: Q_f). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Strom-NGF" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiekennwert Strom in kWh/m²a bezogen auf die
            Nettogrundfläche (Endenergiebedarf-Strom: Q_f). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Gesamt-NGF" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiekennwert Gesamt in kWh/m²a bezogen auf die
            Nettogrundfläche. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="6" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Primaerenergiebedarf-NGF" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiekennwert in kWh/m²a bezogen auf die
            Nettogrundfläche (Primärenergiebedarf: Q_p). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Ein-Zonen-Modell" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wurde das Verfahren nach Anlage 6 zu § 32 Absatz 4 GEG
            (Ein-Zonen-Modell) angewendet? </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Vereinfachte-Datenaufnahme" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wurden Regeln zur vereinfachten Datenaufnahme nach § 50
            (4) GEG bzw. Bekanntmachungen angewendet? </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Vereinfachungen-18599-1-D" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wurden Vereinfachungen nach § 21 Absatz 2 GEG und DIN V
            18599-1:2018-09 Anhang D angewendet? (auch für zu errichtende Nichtwohngebäude/ Neubau
            anwendbar) </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Art-der-Nutzung-erneuerbaren-Energie-1"
        type="n1:Art-der-Nutzung-erneuerbaren-Energie-enum" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Es wird die erste Angabe im Energieausweis zur Nutzung
            erneuerbarer Energien nach GEG Abschnit 4 § 34 erwartet, relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Deckungsanteil-1" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Deckungsanteil in % für erste Angabe zur Nutzung
            erneuerbarer Energien nach GEG Abschnitt 4 § 34 (Neubau), relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Anteil-der-Pflichterfuellung-1" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Pflichterfüllungsanteil in % für die erste Angabe zur
            Nutzung erneuerbarer Energien nach GEG. (§ 34: Die prozentualen Anteile der
            tatsächlichen Nutzung der einzelnen Maßnahmen im Verhältnis der jeweils nach den § 35
            bis § 45 vorgesehenen Nutzung müssen in der Summe mindestens 100 Prozent Erfüllungsgrad
            ergeben.) </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="999" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Art-der-Nutzung-erneuerbaren-Energie-2"
        type="n1:Art-der-Nutzung-erneuerbaren-Energie-enum" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Es wird die zweite Angabe im Energieausweis zur Nutzung
            erneuerbarer Energien nach GEG Abschnit 4 § 34 erwartet, relevant nur bei Neubau </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Deckungsanteil-2" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Deckungsanteil in % für zweite Angabe Angabe zur Nutzung
            erneuerbarer Energien nach GEG Abschnitt 4 § 34 (Neubau), relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Anteil-der-Pflichterfuellung-2" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Pflichterfüllungsanteil in % für die zweite Angabe zur
            Nutzung erneuerbarer Energien nach GEG. (§ 34: Die prozentualen Anteile der
            tatsächlichen Nutzung der einzelnen Maßnahmen im Verhältnis der jeweils nach den § 35
            bis § 45 vorgesehenen Nutzung müssen in der Summe mindestens 100 Prozent Erfüllungsgrad
            ergeben.) </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="999" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="verschaerft-nach-GEG-45-eingehalten" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Der gemäß § 45 GEG um diese Prozentzahl verschärfte
            Anforderungswert (15 % Unterschreitung Wärmedurchgangskoeffizienten) als Maßnahme zur
            Einsparung von Energie ist eingehalten. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:boolean" />
        </xs:simpleType>
      </xs:element>
      <xs:choice>
        <xs:element name="nicht-verschaerft-nach-GEG-34" type="xs:boolean" minOccurs="1"
          maxOccurs="1" fixed="true">
          <xs:annotation>
            <xs:documentation xml:lang="DE"> Keine Maßnahmen nach $ 45 in Verbindung mit $ 34. </xs:documentation>
          </xs:annotation>
        </xs:element>
        <xs:sequence>
          <xs:element name="verschaerft-nach-GEG-34" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Die in Verbindung mit § 34 GEG Maßnahmen nach § 45 in
                Kombination zur Nutzung erneuerbarer Energien zur Deckung des Wärme- und
                Kälteenergiebedarfs sind eingehalten (Anteil der Pflichterfüllung in %). </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:int">
                <xs:minInclusive value="0" />
                <xs:maxInclusive value="100" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
          <xs:element name="Anforderung-nach-GEG-19-unterschritten" minOccurs="1" maxOccurs="1">
            <xs:annotation>
              <xs:documentation xml:lang="DE"> Die Anforderung nach $ 19 GEG wurde unterschritten
                (in %). </xs:documentation>
            </xs:annotation>
            <xs:simpleType>
              <xs:restriction base="xs:int">
                <xs:minInclusive value="0" />
                <xs:maxInclusive value="100" />
              </xs:restriction>
            </xs:simpleType>
          </xs:element>
        </xs:sequence>
      </xs:choice>
      <xs:element name="Anforderung-nach-GEG-52-Renovierung-eingehalten" type="xs:boolean"
        minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Die Anforderungen des § 52 Absatz 1 GEG werden
            eingehalten. Relevant nur bei Bestandsgebäuden. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Sommerlicher-Waermeschutz" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Sind die Anforderungen an den sommerlichen Wärmeschutz
            eingehalten? Relevant nur bei Neubau. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:boolean" />
        </xs:simpleType>
      </xs:element>
      <xs:element name="Treibhausgasemissionen-Zusaetzliche-Verbrauchsdaten" minOccurs="0"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe der Treibhausgasemissionen in kg als CO2
            Äquivalent/(m²a); nur bei kombinierten Energieausweisen (Bedarf/Verbrauch), zusammen mit
            der Übermittlung der zusätzlichen Verbrauchsdaten. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="8" />
            <xs:minInclusive value="-100000" />
            <xs:maxInclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Zusaetzliche-Verbrauchsdaten" type="n1:Nichtwohngebaeude-Verbrauchs-Daten"
        minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Bei Energieausweisen auf Bedarfsbasis können neben den
            Bedarfsangaben zusätzlich Angaben zum Verbrauch im Energieausweis dargestellt werden,
            die dann hier entsprechend integriert werden. </xs:documentation>
        </xs:annotation>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="Automatisierungsgrad-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="A" />
      <xs:enumeration value="B" />
      <xs:enumeration value="C" />
      <xs:enumeration value="D" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Art-der-Nutzung-erneuerbaren-Energie-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="solarthermische Anlagen" />
      <xs:enumeration value="Strom aus erneuerbaren Energien" />
      <xs:enumeration value="Geothermie oder Umweltwärme" />
      <xs:enumeration value="feste Biomasse" />
      <xs:enumeration value="flüssige Biomasse" />
      <xs:enumeration value="gasförmige Biomasse" />
      <xs:enumeration value="Kälte aus erneuerbaren Energien" />
      <xs:enumeration value="Abwärme" />
      <xs:enumeration value="Kraft-Wärme-Kopplung hocheff. KWK-Anlage" />
      <xs:enumeration value="Kraft-Wärme-Kopplung Brennstoffzellenh." />
      <xs:enumeration value="Fernwärme oder Fernkälte" />
      <xs:enumeration value="Maßnahmen zur Einsparung von Energie" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Automatisierungsgrad-Technisches-Gebaeudemanagement-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="A" />
      <xs:enumeration value="B" />
      <xs:enumeration value="C" />
      <xs:enumeration value="D" />
    </xs:restriction>
  </xs:simpleType>
  <xs:complexType name="Bauteil-Opak-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält im Falle eines Bedarfsausweises die
        Hüllflächendaten. Die Summe aller Flächen muss der gesamten energetisch wirksamen Hüllfläche
        entsprechen. Wand- und Dachflächen gegen Aussenluft sind daher netto ohne transparente
        Bauteile usw. anzugeben. Gleichartige Flächen gleichen U-Wertes und gleicher Orientierung
        können aufsummiert angegeben werden. Türen (ausser Fenstertüren), Rollladenkästen u.ä. sind
        als Wandstücke einzugeben. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Flaechenbezeichnung" minOccurs="1" maxOccurs="1">
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="[\w].{1,499}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Flaeche" minOccurs="1" maxOccurs="1">
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="U-Wert" minOccurs="1" maxOccurs="1">
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="3" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Ausrichtung" type="n1:Ausrichtung-enum" minOccurs="1" maxOccurs="1" />
      <xs:element name="grenztAn" type="n1:Medium-enum" minOccurs="1" maxOccurs="1" />
      <xs:element name="Glasdach-Lichtband-Lichtkuppel" type="xs:boolean" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Kennzeichen, ob das Bauteil ein(e) Glasdach, Lichtband
            oder Lichtkuppel ist. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Vorhangfassade" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Kennzeichen, ob das Bauteil eine Vorhangfassade ist. </xs:documentation>
        </xs:annotation>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="Bauteil-Transparent-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält im Falle eines Bedarfsausweises die
        Hüllflächendaten. Die Summe aller Flächen muss der gesamten energetisch wirksamen Hüllfläche
        entsprechen. Wand- und Dachflächen gegen Aussenluft sind daher netto ohne transparente
        Bauteile usw. anzugeben. Gleichartige Flächen gleichen U-Wertes und gleicher Orientierung
        können aufsummiert angegeben werden. Türen (ausser Fenstertüren), Rollladenkästen u.ä. sind
        als Wandstücke einzugeben. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Flaechenbezeichnung" minOccurs="1" maxOccurs="1">
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="[\w].{1,499}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Flaeche" minOccurs="1" maxOccurs="1">
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="U-Wert" minOccurs="1" maxOccurs="1">
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="3" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="g-Wert" minOccurs="1" maxOccurs="1">
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="3" />
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="0.99" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Ausrichtung" type="n1:Ausrichtung-enum" minOccurs="1" maxOccurs="1" />
      <xs:element name="Glasdach-Lichtband-Lichtkuppel" type="xs:boolean" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Kennzeichen, ob das Bauteil ein(e) Glasdach, Lichtband
            oder Lichtkuppel ist. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Vorhangfassade" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Kennzeichen, ob das Bauteil eine Vorhangfassade ist. </xs:documentation>
        </xs:annotation>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="Bauteil-Dach-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält im Falle eines Bedarfsausweises die
        Hüllflächendaten. Die Summe aller Flächen muss der gesamten energetisch wirksamen Hüllfläche
        entsprechen. Wand- und Dachflächen gegen Aussenluft sind daher netto ohne transparente
        Bauteile usw. anzugeben. Gleichartige Flächen gleichen U-Wertes und gleicher Orientierung
        können aufsummiert angegeben werden. Türen (ausser Fenstertüren), Rollladenkästen u.ä. sind
        als Wandstücke einzugeben. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Flaechenbezeichnung" minOccurs="1" maxOccurs="1">
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="[\w].{1,499}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Flaeche" minOccurs="1" maxOccurs="1">
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="U-Wert" minOccurs="1" maxOccurs="1">
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="3" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="Ausrichtung-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="N" />
      <xs:enumeration value="S" />
      <xs:enumeration value="O" />
      <xs:enumeration value="W" />
      <xs:enumeration value="NO" />
      <xs:enumeration value="NW" />
      <xs:enumeration value="SO" />
      <xs:enumeration value="SW" />
      <xs:enumeration value="HOR" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Medium-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Aussenluft" />
      <xs:enumeration value="Raumluft" />
      <xs:enumeration value="Erdreich" />
    </xs:restriction>
  </xs:simpleType>
  <xs:complexType name="Kaelteanlage-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Angaben zur jeweiligen Kälteerzeugung und -verteilung sowie
        Rückkühlung. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Kaelteerzeuger-Bauweise" type="n1:Kaelteerzeuger-Typ-enum" minOccurs="1"
        maxOccurs="1" />
      <xs:element name="Kaelteerzeuger-Regelung" type="n1:Kaelteerzeuger-Regelung-enum"
        minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Die Zuordnung bezieht sich auf die Art der
            Teillastregelung in DIN V 18599 Tab. 25, 27 und 29, z.B. auf die Formulierung
            "…regelung" = stufenlos regelnd "mehrstufig schaltbar" = mehrstufig taktend "stetige
            Regelung" = stufenlos regelnd "Zweipunktregelung" = ein-aus-Betrieb </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Rueckkuehlung-Bauweise" type="n1:Rueckkuehlung-enum" minOccurs="1"
        maxOccurs="1" />
      <xs:element name="Kaelteverteilung-Primaerkreis-Temperatur"
        type="n1:Kaelte-Temperaturniveau-enum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Temperaturniveau der Kälteverteilung Vorlauf/Rücklauf,
            bzw. Angabe von Direktkühlung mit Kältemittelkreis. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Nennkaelteleistung" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Die Nennkälteleistung ist die Kälteleistung in kW, die
            eine Kälteanlage unter Auslegungsbedingungen abgibt (DIN V 18599-7:2011-07)
            (Nennkälteleistung für KKM: Q(Punkt)_C,outg). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="1000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Kaelteerzeuger-Baujahr" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Baujahr dieses Kälteerzeugers oder Jahr der massgeblichen
            letzten baulichen Veränderung des Kälteerzeugers. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:gYear">
            <xs:minInclusive value="1800" />
            <xs:maxInclusive value="2100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Anzahl-baugleiche" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anzahl ggfs. baugleich vorhandener Geräte (mehrfache
            zentrale Kälteerzeuger, Splittgeräte usw.), bei nur einem Gerät Angabe "1". </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Energietraeger" type="n1:Energietraeger-enum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Vom Kälteerzeuger verwendeter Energieträger. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Primaerenergiefaktor" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Verwendeter Primärenergiefaktor des Energieträgers
            (Primärenergiefaktor: f_p) </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="3" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="Kaelteerzeuger-Typ-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Kolben- und Scrollverdichteranlagen" />
      <xs:enumeration value="Schraubenverdichteranlagen" />
      <xs:enumeration value="Turboverdichteranlagen" />
      <xs:enumeration value="Sorptionskältemaschine" />
      <xs:enumeration value="Sonstiges" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Kaelteerzeuger-Regelung-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="ein-aus" />
      <xs:enumeration value="Heißgasbypass oder ähnliches" />
      <xs:enumeration value="mehrstufig schaltend" />
      <xs:enumeration value="invertergeregelt" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Rueckkuehlung-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Luftgekühlt-Kompaktbauweise" />
      <xs:enumeration value="Luftgekühlt-Splitbauweise" />
      <xs:enumeration value="Wassergekühlt-Verdunstungskühler" />
      <xs:enumeration value="Wassergekühlt-Trockenkühler" />
      <xs:enumeration value="Sonstiges" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Kaelte-Temperaturniveau-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="direktverdampfend" />
      <xs:enumeration value="6/12 oder kälter" />
      <xs:enumeration value="über 6/12" />
    </xs:restriction>
  </xs:simpleType>
  <xs:complexType name="RLT-Anlagen-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Angaben zur jeweiligen Lüftungsanlage. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Funktion-Zuluft" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anlage führt den Räumen Zuluft aus (ggfs. aufbereiteter)
            Außenluft (evtl. zusammen mit Umluft) zu. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Funktion-Abluft" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anlage führt aus den Räumen Abluft ab. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="WRG-Rueckwaermzahl" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anlagenspezifische Angabe in Prozent, die den
            Wärmerückgewinnungsgrad der Lüftungsanlage zonenunabhängig widerspiegelt. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Funktion-Heizregister" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anlage kann die Luft erwärmen. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Funktion-Kuehlregister" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anlage kann die Luft kühlen. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Funktion-Dampfbefeuchter" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anlage kann die Luft mittels Dampfbefeuchter befeuchten. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Funktion-Wasserbefeuchter" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anlage kann die Luft mittels Sprüh- oder Rieselbefeuchter
            befeuchten. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Energietraeger-Befeuchtung" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Vom Befeuchter verwendeter Energieträger (bei
            Wasserbefeuchter derjenige Energieträger, der das/die zugehörige Heizregister der
            Heizanlage versorgt), Angabe nur erforderlich, wenn Anlage befeuchten kann. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="n1:Energietraeger-enum" />
        </xs:simpleType>
      </xs:element>
      <xs:element name="Anzahl-baugleiche" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Anzahl ggfs. baugleich vorhandener Geräte, bei nur einem
            Gerät Angabe "1". </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="Zonen-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält die Angaben zu einer einzelnen
        Nutzungszone bei Nichtwohngebäuden, die Zonen sollten sinnvollerweise in absteigender
        Flächengröße angeordnet werden, es ist eine vollständige Liste aller rechnerisch verwendeten
        Zonen anzugeben. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Zonenbezeichnung" minOccurs="1" maxOccurs="1">
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="[\w].{1,39}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Nutzung" type="n1:Nutzung-enum" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Nutzungsprofil nach DIN V 18599 </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Anwenderspezifische_Nutzung_Bezeichnung" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Bezeichnung der anwenderspezifischen Nutzung der Zone. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="[\w].{1,39}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Zonenbesonderheiten" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Hinweise auf Anpassungen des Nutzungsprofils nach DIN V
            18599, wenn Standard-Vorgaben nicht verwendbar; bei völlig neu definierten Profilen
            (weil in DIN V 18599 nicht aufgelistet) bitte eine dort nicht verwendete
            Nutzungsprofilnummer (z.B. ab 90) verwenden und hier alle relevanten Daten anführen. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{0,500}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Nettogrundflaeche-Zone" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Nettogrundfläche der jeweiligen Zone in m²
            (Nettogrundfläche: A_NGF). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="10000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="mittlere-lichte-Raumhoehe" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Mittlere lichte Raumhöhe der jeweiligen Zone in m (lichte
            Raumhöhe: h_R). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="4" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Sonnenschutz-System" type="n1:Sonnenschutz-enum" minOccurs="1"
        maxOccurs="50">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Sonnenschutz der transparenten Flächen nach außen. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Beleuchtungs-System" type="n1:Lampenart-enum" minOccurs="1" maxOccurs="50">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Überwiegende Beleuchtungstechnik (Kunstlicht) der
            jeweiligen Zone. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Beleuchtungs-Verteilung" type="n1:Beleuchtungsart-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Lichtverteilung (Kunstlicht) in der jeweiligen Zone. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Praesenzkontrolle-Kunstlicht" type="xs:boolean" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wird das Kunstlicht über eine Präsenzerfassung
            automatisch geschaltet? </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Tageslichtabhaengige-Kontrollsysteme" type="xs:boolean" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Wird das Kunstlicht in Abhängigkeit vom Tageslicht
            automatisch geschaltet oder geregelt? </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Endenergiebedarf-Heizung" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Heizung (statisch und RLT) in kWh/a ohne
            Hilfsenergie (Endenergiebedarf-Heizung: Q_h,f + Q_h*,f). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Kuehlung" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Kühlung (statisch und RLT) in kWh/a ohne
            Hilfsenergie; 0 wenn nicht gekühlt (gilt sinngemäß auch für andere nicht vorhandene
            Konditionierungsarten der Zone) (Endenergiebedarf-Kuehlung: Q_c,f + Q_c*,f). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Befeuchtung" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Befeuchtung (RLT) in kWh/a ohne
            Hilfsenergie (Endenergiebedarf-Befeuchtung: Q_m*,f). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Trinkwarmwasser" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Trinkwasser-Erwärmung in kWh/a ohne
            Hilfsenergie (Endenergiebedarf-Trinkwarmwasser: Q_w,f). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Beleuchtung" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Beleuchtung in kWh/a
            (Endenergiebedarf-Beleuchtung: Q_l,f). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Lufttransport" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Luftförderung einer mechanischen
            Belüftung in kWh/a (Endenergiebedarf-Lufttransport: W_v). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Hilfsenergie" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Hilfsenergie in kWh/a ohne Luftförderung
            (Endenergiebedarf-Hilfsenergie: W_f - W_v). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100000000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="Nutzung-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="1:Einzelbüro" />
      <xs:enumeration value="2:Gruppenbüro (zwei bis sechs Arbeitsplätze)" />
      <xs:enumeration value="3:Großraumbüro (ab sieben Arbeitsplätze)" />
      <xs:enumeration value="4:Besprechung, Sitzung, Seminar" />
      <xs:enumeration value="5:Schalterhalle" />
      <xs:enumeration value="6:Einzelhandel / Kaufhaus" />
      <xs:enumeration value="7:Einzelhandel / Kaufhaus (Leb.-Abteilung mit Kühlprodukten)" />
      <xs:enumeration value="8:Klassenzimmer (Schule), Gruppenraum (Kindergarten)" />
      <xs:enumeration value="9:Hörsaal, Auditorium" />
      <xs:enumeration value="10:Bettenzimmer" />
      <xs:enumeration value="11:Hotelzimmer" />
      <xs:enumeration value="12:Kantine" />
      <xs:enumeration value="13:Restaurant" />
      <xs:enumeration value="14:Küchen in Nichtwohngebäuden" />
      <xs:enumeration value="15:Küche - Vorbereitung, Lager" />
      <xs:enumeration value="16:WC und Sanitärräume in Nichtwohngebäuden" />
      <xs:enumeration value="17:sonstige Aufenthaltsräume" />
      <xs:enumeration value="18:Nebenflächen ohne Aufenthaltsräume" />
      <xs:enumeration value="19:Verkehrsflächen" />
      <xs:enumeration value="20:Lager, Technik, Archiv" />
      <xs:enumeration value="21:Rechenzentrum" />
      <xs:enumeration value="22.1:Gewerbliche und industrielle Hallen - schwere Arbeit" />
      <xs:enumeration value="22.2:Gewerbliche und industrielle Hallen - mittelschwere Arbeit" />
      <xs:enumeration value="22.3:Gewerbliche und industrielle Hallen - leichte Arbeit" />
      <xs:enumeration value="23:Zuschauerbereich" />
      <xs:enumeration value="24:Theater - Foyer" />
      <xs:enumeration value="25:Bühne" />
      <xs:enumeration value="26:Messe / Kongress" />
      <xs:enumeration value="27:Ausstellungsräume und Museum" />
      <xs:enumeration value="28:Bibliothek - Lesesaal" />
      <xs:enumeration value="29:Bibliothek - Freihandbereich" />
      <xs:enumeration value="30:Bibliothek - Magazin und Depot" />
      <xs:enumeration value="31:Turnhalle" />
      <xs:enumeration value="32:Parkhäuser (Büro- und Privatnutzung)" />
      <xs:enumeration value="33:Parkhäuser (öffentliche Nutzung)" />
      <xs:enumeration value="34:Saunabereich" />
      <xs:enumeration value="35:Fitnessraum" />
      <xs:enumeration value="36:Labor" />
      <xs:enumeration value="37:Untersuchungs- und Behandlungsräume" />
      <xs:enumeration value="38:Spezialpflegebereiche" />
      <xs:enumeration value="39:Flure des allgemeinen Pflegebereichs" />
      <xs:enumeration value="40:Arztpraxen und Therapeutische Praxen" />
      <xs:enumeration value="41:Lagerhallen, Logistikhallen" />
      <xs:enumeration value="42:Wohnen (EFH)" />
      <xs:enumeration value="43:Wohnen (MFH)" />
      <xs:enumeration value="44:Anwenderspezifische Nutzung" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Sonnenschutz-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="keine Bewertung des Sonnenschutzes (Zone: Wohnen)">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Sonnenschutz nicht erforderlich, da keine transparenten
            Flächen nach außen in sonnenbeschienene Richtungen vorhanden. </xs:documentation>
        </xs:annotation>
      </xs:enumeration>
      <xs:enumeration value="Kein Sonnen- und/oder Blendschutz" />
      <xs:enumeration value="Nur Blendschutz" />
      <xs:enumeration value="Sonnen- und/oder Blendschutz, automatisch betrieben" />
      <xs:enumeration value="Nur Blendschutz, lichtlenkend" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Lampenart-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Glühlampen und Halogenglühlampe" />
      <xs:enumeration value="Leuchtstofflampen-stabförmig-KVG/VVG" />
      <xs:enumeration value="Leuchtstofflampen-stabförmig-EVG" />
      <xs:enumeration value="Leuchtstofflampen-T5-stabförmig-EVG-effiziente Reflektoren" />
      <xs:enumeration value="Leuchtstofflampen-kompakt-externes VG-alle VG" />
      <xs:enumeration value="Leuchtstofflampen-kompakt-integriertes EVG" />
      <xs:enumeration value="Hochdruckentladungslampen-alle VG" />
      <xs:enumeration value="LED-Leuchten" />
      <xs:enumeration value="LED-Ersatzlampen (stab- und kolbenförmig)" />
      <xs:enumeration value="keine Bewertung der Beleuchtung (Zone: Wohnen)" />
      <xs:enumeration value="Fachplanung" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Beleuchtungsart-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="direkt" />
      <xs:enumeration value="indirekt" />
      <xs:enumeration value="direkt-indirekt" />
      <xs:enumeration value="keine Bewertung der Beleuchtung vorhanden (Zone:Wohnen)" />
      <xs:enumeration value="Fachplanung" />
    </xs:restriction>
  </xs:simpleType>
  <xs:complexType name="Endenergie-Energietraeger-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält die Angaben zu den spezifischen
        Endenergiebedarfswerten nach Energieträger, bezogen auf die Nettogrundfläche bei
        Nichtwohngebäuden, bzw. auf die Nettonutzfläche bei Wohngebäude, siehe Tabelle
        Endenergiebedarf im Energieausweis-Formular. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Energietraegerbezeichnung" type="n1:Energietraeger-enum" minOccurs="1"
        maxOccurs="1" />
      <xs:element name="Primaerenergiefaktor" minOccurs="1" maxOccurs="1">
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="3" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="10" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Heizung-spezifisch" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Heizung (statisch und RLT) in kWh/m²a
            mit Hilfsenergie (Endenergiebedarf-Heizung: Q_h,f + Q_h*,f; W_h + W_h*). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Kuehlung-Befeuchtung-spezifisch" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Kühlung (statisch und RLT) und
            Befeuchtung in kWh/m²a mit Hilfsenergie; 0 wenn nicht gekühlt (gilt sinngemäß auch für
            andere nicht vorhandene Konditionierungsarten des gesamten Gebäudes)
            (Endenergiebedarf-Kuehlung-Befeuchtung: Q_c,f + Q_c*,f + Q_m*,f; W_c + W_c* + W_m*). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Trinkwarmwasser-spezifisch" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Trinkwasser-Erwärmung in kWh/m²a mit
            Hilfsenergie (Endenergiebedarf-Trinkwarmwasser: Q_w,f; W_w). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Beleuchtung-spezifisch" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Beleuchtung in kWh/m²a
            (Endenergiebedarf-Beleuchtung: Q_l,f; W_l). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Lueftung-spezifisch" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Luftförderung der mechanischen Belüftung
            in kWh/m²a (Endenergiebedarf-Lüftung: W_v). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Energietraeger-Gesamtgebaeude-spezifisch" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf dieses Energieträgers für das gesamte
            Gebäude und alle Konditionierungsarten in kWh/m²a (Energieträger: Q_f). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="NWG-Aushang-Daten">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält die Angaben zu den Nutz-, End- und
        Primärenergiebedarfswerten für das Balkendiagramm im Aushangformular bei Nichtwohngebäuden,
        GEG § 84 Absatz 8 bzw. Muster für den Aushang als Bekanntmachung im Bundesanzeiger. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Nutzenergiebedarf-Heizung-Diagramm" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Nutzenergiebedarf Heizung (statisch und RLT) in kWh/m²a
            mit Hilfsenergie (Nutzenergiebedarf-Heizung: Q_h,b + Q_h*,b). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Nutzenergiebedarf-Trinkwarmwasser-Diagramm" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Nutzenergiebedarf Trinkwasser-Erwärmung in kWh/m²a mit
            Hilfsenergie (Nutzenergiebedarf-Trinkwarmwasser: Q_w,b). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Nutzenergiebedarf-Beleuchtung-Diagramm" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Nutzenergiebedarf Beleuchtung in kWh/m²a
            (Nutzenergiebedarf-Beleuchtung: Q_l,b). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Nutzenergiebedarf-Lueftung-Diagramm" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Nutzenergiebedarf Luftförderung der mechanischen
            Belüftung in kWh/m²a. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Nutzenergiebedarf-Kuehlung-Befeuchtung-Diagramm" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Nutzenergiebedarf Kühlung (statisch und RLT) und
            Befeuchtung in kWh/m²a mit Hilfsenergie; 0 wenn nicht gekühlt (gilt sinngemäß auch für
            andere nicht vorhandene Konditionierungsarten des gesamten Gebäudes)
            (Nutzenergiebedarf-Kuehlung-Befeuchtung: Q_c,b + Q_c*,b + Q_m*,b). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Heizung-Diagramm" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Heizung (statisch und RLT) in kWh/m²a
            mit Hilfsenergie (Endenergiebedarf-Heizung: Q_h,f + Q_h*,f + W_h + W_h*). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Trinkwarmwasser-Diagramm" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Trinkwasser-Erwärmung in kWh/m²a mit
            Hilfsenergie (Endenergiebedarf-Trinkwarmwasser: Q_w,f + W_w). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Beleuchtung-Diagramm" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Beleuchtung in kWh/m²a
            (Endenergiebedarf-Beleuchtung: Q_l,f + W_l). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Lueftung-Diagramm" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Luftförderung der mechanischen Belüftung
            in kWh/m²a (Endenergiebedarf-Lueftung: W_v). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Endenergiebedarf-Kuehlung-Befeuchtung-Diagramm" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Endenergiebedarf Kühlung (statisch und RLT) und
            Befeuchtung in kWh/m²a mit Hilfsenergie; 0 wenn nicht gekühlt (gilt sinngemäß auch für
            andere nicht vorhandene Konditionierungsarten des gesamten Gebäudes)
            (Endenergiebedarf-Kuehlung-Befeuchtung: Q_c,f + Q_c*,f + Q_m*,f + W_c + W_c* + W_m*). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Primaerenergiebedarf-Heizung-Diagramm" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiebedarf Heizung (statisch und RLT) in kWh/m²a
            mit Hilfsenergie. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Primaerenergiebedarf-Trinkwarmwasser-Diagramm" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiebedarf Trinkwasser-Erwärmung in kWh/m²a mit
            Hilfsenergie. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Primaerenergiebedarf-Beleuchtung-Diagramm" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiebedarf Beleuchtung in kWh/m²a. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Primaerenergiebedarf-Lueftung-Diagramm" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiebedarf Luftförderung der mechanischen
            Belüftung in kWh/m²a. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Primaerenergiebedarf-Kuehlung-Befeuchtung-Diagramm" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Primärenergiebedarf Kühlung (statisch und RLT) und
            Befeuchtung in kWh/m²a mit Hilfsenergie; 0 wenn nicht gekühlt (gilt sinngemäß auch für
            andere nicht vorhandene Konditionierungsarten des gesamten Gebäudes). </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:decimal">
            <xs:fractionDigits value="2" />
            <xs:totalDigits value="7" />
            <xs:minInclusive value="0" />
            <xs:maxExclusive value="100000" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:complexType name="Modernisierungszeile">
    <xs:annotation>
      <xs:documentation xml:lang="DE"> Dieser Block enthält zeilenweise die Textangaben zu den
        Modernisierungsempfehlungen im Energieausweis. </xs:documentation>
    </xs:annotation>
    <xs:sequence>
      <xs:element name="Nummer" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Fortlaufende Nummer der Modernisierungsempfehlung </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:int">
            <xs:minInclusive value="0" />
            <xs:maxInclusive value="100" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Bauteil-Anlagenteil" type="n1:Modernisierung-Teil-enum" minOccurs="1"
        maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Angabe des betroffenen Bauteils oder Anlagenteils; die
            Differenzierung ist dem Energieausweis-Aussteller überlassen, er kann also z.B. unter
            Heizung eine komplette neue Beheizung samt Regelung, hydr. Abgleich usw. in einer
            Empfehlung geben, oder einzelne Maßnahmen differenzierter auflisten. </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Massnahmenbeschreibung" minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Beschreibung der Maßnahme, ggfs. auch in mehreren
            Schritten. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value="[\w].{4,225}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="Modernisierungskombination" type="n1:Modernisierung-Kombi-enum"
        minOccurs="1" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Kreuzchen, ob die Maßnahme im Zusammenhang mit größeren
            Modernisierungsmaßnahmen oder sogar als Einzelmaßnahme sinnvoll ist </xs:documentation>
        </xs:annotation>
      </xs:element>
      <xs:element name="Amortisation" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Geschätzte Amortisationszeit der Maßnahme. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{0,31}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
      <xs:element name="spezifische-Kosten" minOccurs="0" maxOccurs="1">
        <xs:annotation>
          <xs:documentation xml:lang="DE"> Geschätzte Kosten pro eingesparter Kilowattstunde
            Endenergie. </xs:documentation>
        </xs:annotation>
        <xs:simpleType>
          <xs:restriction base="xs:string">
            <xs:pattern value=".{0,71}" />
          </xs:restriction>
        </xs:simpleType>
      </xs:element>
    </xs:sequence>
  </xs:complexType>
  <xs:simpleType name="Modernisierung-Teil-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="Dach" />
      <xs:enumeration value="oberste Geschossdecke" />
      <xs:enumeration value="Abseiten" />
      <xs:enumeration value="Gauben" />
      <xs:enumeration value="Dachfenster" />
      <xs:enumeration value="Außenwand gg. Außenluft" />
      <xs:enumeration value="Fenster" />
      <xs:enumeration value="Rollläden und -kästen" />
      <xs:enumeration value="Eingangstür" />
      <xs:enumeration value="Nebentür" />
      <xs:enumeration value="Kellerdecke" />
      <xs:enumeration value="Boden gg. Außenluft" />
      <xs:enumeration value="Außenwand gg. Erdreich" />
      <xs:enumeration value="Boden gegen Erdreich" />
      <xs:enumeration value="Luftundichtigkeiten" />
      <xs:enumeration value="Heizung" />
      <xs:enumeration value="Wärmeerzeuger" />
      <xs:enumeration value="Wärmeverteilung / -abgabe" />
      <xs:enumeration value="Warmwasserbereitung" />
      <xs:enumeration value="Lüftung" />
      <xs:enumeration value="Lüftungskonzept" />
      <xs:enumeration value="Lüftungsanlage" />
      <xs:enumeration value="Luftverteilung / -abgabe" />
      <xs:enumeration value="Kühlung" />
      <xs:enumeration value="Kälteerzeugung" />
      <xs:enumeration value="Kälteverteilung / -abgabe" />
      <xs:enumeration value="Be-/Entfeuchtung" />
      <xs:enumeration value="Beleuchtung" />
      <xs:enumeration value="Anlagenregelung" />
      <xs:enumeration value="Gebäudeautomation" />
      <xs:enumeration value="Sonstiges" />
    </xs:restriction>
  </xs:simpleType>
  <xs:simpleType name="Modernisierung-Kombi-enum">
    <xs:restriction base="xs:string">
      <xs:enumeration value="in Zusammenhang mit größerer Modernisierung" />
      <xs:enumeration value="als Einzelmaßnahme" />
    </xs:restriction>
  </xs:simpleType>
</xs:schema>