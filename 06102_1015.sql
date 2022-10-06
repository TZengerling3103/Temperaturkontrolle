-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 06. Okt 2022 um 10:15
-- Server-Version: 10.4.25-MariaDB
-- PHP-Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `tempcontroll`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `benlog`
--

CREATE TABLE `benlog` (
  `LogID` int(11) NOT NULL,
  `BenutzerID` int(11) NOT NULL,
  `SensorID` int(11) NOT NULL,
  `Datum` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `MaxTemp` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `benlog`
--

INSERT INTO `benlog` (`LogID`, `BenutzerID`, `SensorID`, `Datum`, `MaxTemp`) VALUES
(1, 4, 1, '2022-10-04 12:48:20', 30);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `benutzer`
--

CREATE TABLE `benutzer` (
  `BenutzerID` int(11) NOT NULL,
  `Name` varchar(30) NOT NULL,
  `AnmeldeName` varchar(20) NOT NULL,
  `Passwort` varchar(40) NOT NULL,
  `TeleNr` varchar(15) NOT NULL,
  `Berechtigung` int(11) NOT NULL DEFAULT 10,
  `Status` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `benutzer`
--

INSERT INTO `benutzer` (`BenutzerID`, `Name`, `AnmeldeName`, `Passwort`, `TeleNr`, `Berechtigung`, `Status`) VALUES
(1, 'Jakob', 'JLuther', 'JL123456', '0174123456789', 0, 1),
(2, 'Tom', 'TZengerling', 'TZ123456', '0174123456788', 0, 1),
(3, 'Phillip', 'PLaetsch', 'PL123456', '0174123456799', 0, 1),
(4, 'Raphael2', 'RBrezinski', 'RB123456', '0174123456779', 0, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `hersteller`
--

CREATE TABLE `hersteller` (
  `HerstellerID` int(11) NOT NULL,
  `Name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `hersteller`
--

INSERT INTO `hersteller` (`HerstellerID`, `Name`) VALUES
(1, 'Bosch ');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `sensor`
--

CREATE TABLE `sensor` (
  `SensorID` int(11) NOT NULL,
  `SchrankID` int(11) NOT NULL,
  `Adresse` varchar(20) NOT NULL,
  `MaxTemp` double NOT NULL,
  `HerstellerID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `sensor`
--

INSERT INTO `sensor` (`SensorID`, `SchrankID`, `Adresse`, `MaxTemp`, `HerstellerID`) VALUES
(1, 1, 'SENS4978', 30, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `temperaturen`
--

CREATE TABLE `temperaturen` (
  `TempID` int(11) NOT NULL,
  `temperatur` double NOT NULL,
  `Zeit` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `SensorID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Daten für Tabelle `temperaturen`
--

INSERT INTO `temperaturen` (`TempID`, `temperatur`, `Zeit`, `SensorID`) VALUES
(1, 23, '2022-10-02 22:00:00', 1),
(2, 35, '2022-10-02 22:00:00', 1),
(3, 43, '2022-10-03 22:00:00', 1);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `benlog`
--
ALTER TABLE `benlog`
  ADD PRIMARY KEY (`LogID`),
  ADD KEY `BenutzerID` (`BenutzerID`,`SensorID`),
  ADD KEY `SensorID` (`SensorID`);

--
-- Indizes für die Tabelle `benutzer`
--
ALTER TABLE `benutzer`
  ADD PRIMARY KEY (`BenutzerID`),
  ADD UNIQUE KEY `Anmeldename` (`AnmeldeName`);

--
-- Indizes für die Tabelle `hersteller`
--
ALTER TABLE `hersteller`
  ADD PRIMARY KEY (`HerstellerID`);

--
-- Indizes für die Tabelle `sensor`
--
ALTER TABLE `sensor`
  ADD PRIMARY KEY (`SensorID`),
  ADD KEY `HerstellerID` (`HerstellerID`);

--
-- Indizes für die Tabelle `temperaturen`
--
ALTER TABLE `temperaturen`
  ADD PRIMARY KEY (`TempID`),
  ADD KEY `SensorID` (`SensorID`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `benlog`
--
ALTER TABLE `benlog`
  MODIFY `LogID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `benutzer`
--
ALTER TABLE `benutzer`
  MODIFY `BenutzerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT für Tabelle `hersteller`
--
ALTER TABLE `hersteller`
  MODIFY `HerstellerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `sensor`
--
ALTER TABLE `sensor`
  MODIFY `SensorID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT für Tabelle `temperaturen`
--
ALTER TABLE `temperaturen`
  MODIFY `TempID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `benlog`
--
ALTER TABLE `benlog`
  ADD CONSTRAINT `benlog_ibfk_1` FOREIGN KEY (`SensorID`) REFERENCES `sensor` (`SensorID`),
  ADD CONSTRAINT `benlog_ibfk_2` FOREIGN KEY (`BenutzerID`) REFERENCES `benutzer` (`BenutzerID`);

--
-- Constraints der Tabelle `sensor`
--
ALTER TABLE `sensor`
  ADD CONSTRAINT `sensor_ibfk_1` FOREIGN KEY (`HerstellerID`) REFERENCES `hersteller` (`HerstellerID`);

--
-- Constraints der Tabelle `temperaturen`
--
ALTER TABLE `temperaturen`
  ADD CONSTRAINT `temperaturen_ibfk_1` FOREIGN KEY (`SensorID`) REFERENCES `sensor` (`SensorID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
