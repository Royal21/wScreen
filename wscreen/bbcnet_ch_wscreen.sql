-- phpMyAdmin SQL Dump
-- version 3.3.7deb5build0.10.10.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 13. Mai 2011 um 11:34
-- Server Version: 5.1.49
-- PHP-Version: 5.3.3-1ubuntu9.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `bbcnet_ch_wscreen`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `admin`
--

CREATE TABLE IF NOT EXISTS `admin` (
  `intAdmin_ID` int(11) NOT NULL AUTO_INCREMENT,
  `strAccountName` varchar(50) NOT NULL,
  `strVorname` varchar(50) NOT NULL,
  `strNachname` varchar(50) NOT NULL,
  `intIsActive` int(11) NOT NULL,
  PRIMARY KEY (`intAdmin_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Daten für Tabelle `admin`
--

INSERT INTO `admin` (`intAdmin_ID`, `strAccountName`, `strVorname`, `strNachname`, `intIsActive`) VALUES
(1, 'vonguntenm', 'Marco', 'von Gunten', 1),
(24, 'nsched', 'Doris', 'Schenk', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `folie`
--

CREATE TABLE IF NOT EXISTS `folie` (
  `intFolie_ID` int(11) NOT NULL AUTO_INCREMENT,
  `dateTimeVon` datetime NOT NULL,
  `dateTimeBis` datetime NOT NULL,
  `strText1` varchar(50) DEFAULT NULL,
  `strText2` varchar(300) DEFAULT NULL,
  `strEmpfang` varchar(40) DEFAULT NULL,
  `strPath` varchar(200) DEFAULT NULL,
  `intAdmin_ID` int(11) NOT NULL,
  `intTemplate_ID` int(11) NOT NULL,
  PRIMARY KEY (`intFolie_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=91 ;

--
-- Daten für Tabelle `folie`
--

INSERT INTO `folie` (`intFolie_ID`, `dateTimeVon`, `dateTimeBis`, `strText1`, `strText2`, `strEmpfang`, `strPath`, `intAdmin_ID`, `intTemplate_ID`) VALUES
(86, '2011-05-18 13:15:00', '2011-05-18 13:49:00', 'Herzlich Willkommen', 'Frau Hagi und Frau Schmutz von<br />\r\nSchneider Electric', 'Bitte w&auml;hlen Sie 3333', NULL, 24, 1),
(83, '2011-05-12 10:30:00', '2011-05-12 13:00:00', 'Herzlich Willkommen', 'zum JumpIn 2011 Meeting<br />\r\nim Messlabor Elektronik im UG', 'Bitte w&auml;hlen Sie 3333', NULL, 24, 1),
(84, '2011-05-18 08:45:00', '2011-05-18 10:00:00', 'Herzlich Willkommen', 'Sekundarschule Neuenegg', 'Bitte w&auml;hlen Sie 3333', NULL, 24, 1),
(85, '2011-05-18 13:50:00', '2011-05-18 14:30:00', 'Herzlich Willkommen', 'Sekundarschule Neuenegg', 'Bitte w&auml;hlen Sie 3333', NULL, 24, 1),
(81, '2011-05-10 08:30:00', '2011-05-10 13:00:00', 'Herzlich Willkommen', 'Fit for Life 2011', 'Bitte w&auml;hlen Sie 3333', NULL, 1, 1),
(82, '2011-05-11 09:00:00', '2011-05-11 14:00:00', 'Herzlich Willkommen', 'Tervetuloa<br />\r\nder finnischen Delegation und <br />\r\nHerr Rudolf Strahm', 'Bitte w&auml;hlen Sie 3333', NULL, 24, 1),
(76, '2011-04-24 00:00:00', '2011-04-25 00:00:00', 'Herzlich Willkommen', 'klj', 'Bitte w&auml;hlen Sie 3333', NULL, 1, 1),
(79, '2011-04-21 00:00:00', '2011-04-21 18:00:00', NULL, NULL, NULL, 'upload/galerie/Hoststar', 1, 2),
(80, '2011-05-11 07:30:00', '2011-05-11 08:45:00', 'Herzlich Willkommen', 'zum &uuml;K 108 f&uuml;r Informatik-Lernende<br />\r\nBitte warten sie hier. Sie werden um 8.15 Uhr abgeholt.', 'Bitte w&auml;hlen Sie 3333', NULL, 24, 1),
(87, '2011-05-24 07:45:00', '2011-05-24 09:00:00', 'Herzlich Willkommen', 'zum Informatik-Schnuppertag.<br />\r\nBitte warten sie hier. Sie werden<br />\r\nabgeholt.', 'Bitte w&auml;hlen Sie 3333', NULL, 24, 1),
(88, '2011-05-16 07:30:00', '2011-05-16 14:00:00', 'Herzlich Willkommen', 'zur Zwischenpr&uuml;fung vor der Teilpr&uuml;fung.<br />\r\nBitte warten sie hier. Sie werden<br />\r\nabgeholt.', 'Bitte w&auml;hlen Sie 3333', NULL, 24, 1),
(89, '2011-05-17 07:30:00', '2011-05-17 14:00:00', 'Herzlich Willkommen', 'zur Zwischenpr&uuml;fung vor der Teilpr&uuml;fung.<br />\r\nBitte warten sie hier. Sie werden<br />\r\nabgeholt.', 'Bitte w&auml;hlen Sie 3333', NULL, 24, 1),
(90, '2011-05-20 07:30:00', '2011-05-20 09:00:00', 'Herzlich Willkommen', 'zur Zwischenpr&uuml;fung vor der Teilpr&uuml;fung.<br />\r\nBitte warten sie hier. Sie werden<br />\r\nabgeholt.', 'Bitte w&auml;hlen Sie 3333', NULL, 24, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `template`
--

CREATE TABLE IF NOT EXISTS `template` (
  `intTemplate_ID` int(11) NOT NULL AUTO_INCREMENT,
  `strPfad` varchar(200) NOT NULL,
  `strName` varchar(50) NOT NULL,
  PRIMARY KEY (`intTemplate_ID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Daten für Tabelle `template`
--

INSERT INTO `template` (`intTemplate_ID`, `strPfad`, `strName`) VALUES
(1, 'css/images/templates/template1.png', 'Template 1'),
(2, 'upload/galerie/', 'Galerie');
