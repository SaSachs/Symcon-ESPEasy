# switch
Beschreibung des Moduls.

### Inhaltsverzeichnis

1. [Funktionsumfang](#1-funktionsumfang)
2. [Voraussetzungen](#2-voraussetzungen)
3. [Software-Installation](#3-software-installation)
4. [Einrichten der Instanzen in IP-Symcon](#4-einrichten-der-instanzen-in-ip-symcon)
5. [Statusvariablen und Profile](#5-statusvariablen-und-profile)
6. [PHP-Befehlsreferenz](#6-php-befehlsreferenz)

### 1. Funktionsumfang

Einbinden von ESPEasy in Symcon

### 2. Vorraussetzungen

- IP-Symcon ab Version 6.0
- Ein interner oder externer MQTT-Broker
- Mit ESPEasy geflashtes Gerät (z.B. Wemos D1)
- In ESPEasy muss unter Controllers ein MQTT-Broker mit Protokoll "Home Assistant (openHAB) MQTT" eingerichtet sein
- Die Devices müssen in ESPEasy eingerichtet sein, Häkchen bei 'Enabled' und 'Send to Controller' nicht vergessen

### 3. Software-Installation

Über das Module Control folgende URL hinzufügen https://github.com/SaSachs/Symcon-ESPEasy

### 4. Einrichten der Instanzen in IP-Symcon

Unter 'Instanz hinzufügen' ist das 'ESPEasy'-Modul unter dem Hersteller 'SaSachs' aufgeführt.

__Konfigurationsseite__:

Name          | Beschreibung
--------      | ------------------
Unit Name     | Name des ESPEasy-Geräts - In ESPEasy zu finden unter Config - Unit Name (zugleich MQTT Topic)
Task Name     | Name des Devices - Zu finden unter Devices - Add/Edit - Name
Value Name    | Name des Werts in ESPEasy - Zu finden unter Devices Add/Edit - Values - Name
Variable Name | Variablenname in Symcon - Variable wird automatisch erzeugt
Type          | Variablentyp in Symcon (Bool - Auswahl möglich ob Eingang oder Ausgang in ESPEasy)
Variable Profile     | Auswahl eines Variablenprofils für die zu erstellende Variable
Inverted      | Auswahl ob Ausgabe invertiert werden soll (Relevant nur bei Type Bool)
GPIO          | Angabe des GPIO in ESPEasy - Zu finden unter Devices - Add/Edit - GPIO (Benötigt für Type Bool(Output))

### 5. Statusvariablen und Profile

Die Statusvariablen/Kategorien werden automatisch angelegt. Das Löschen einzelner kann zu Fehlfunktionen führen.

### 6. PHP-Befehlsreferenze

`RequestAction($VariablenID, $Value);`
Schalten eines Ausgangs (nur Variablen Type Bool(Output))

Beispiel:
`RequestAction(12345,true);  //Einschalten`

`RequestAction(12345,false); //Ausschalten`