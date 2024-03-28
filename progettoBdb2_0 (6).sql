-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Creato il: Lug 03, 2020 alle 11:58
-- Versione del server: 10.4.11-MariaDB
-- Versione PHP: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `progettoBdb2.0`
--
CREATE DATABASE IF NOT EXISTS `progettoBdb2.0` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `progettoBdb2.0`;

-- --------------------------------------------------------

--
-- Struttura della tabella `blog`
--

CREATE TABLE `blog` (
  `idblog` smallint(6) NOT NULL,
  `titolo` varchar(40) NOT NULL,
  `autore` smallint(6) DEFAULT NULL,
  `font` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `blog`
--

INSERT INTO `blog` (`idblog`, `titolo`, `autore`, `font`) VALUES
(54, 'Serie A', 18, 1),
(59, 'Stranger Things', 18, NULL),
(60, 'Premier League', 18, NULL),
(64, 'Formula 1 2020', 18, NULL),
(65, 'NBA', 18, NULL),
(80, 'tony', 20, NULL),
(83, 'vale rossi', 20, NULL),
(89, 'freddy mercuri', 18, NULL),
(91, 'Wimbledon', 18, NULL),
(93, 'Pisa Calcio', 18, NULL),
(120, 'arte 1400', 18, NULL),
(124, 'piscine di cascina', 18, NULL),
(126, 'Roma 1960', 18, NULL),
(137, 'beatles', 18, 4),
(145, 'mondiali 2006', 190, 1),
(146, 'musica house', 197, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `coautore`
--

CREATE TABLE `coautore` (
  `idcoautore` smallint(6) NOT NULL,
  `idblog` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `coautore`
--

INSERT INTO `coautore` (`idcoautore`, `idblog`) VALUES
(18, 146),
(27, 54);

-- --------------------------------------------------------

--
-- Struttura della tabella `commenti`
--

CREATE TABLE `commenti` (
  `idcommento` smallint(6) NOT NULL,
  `idpost` smallint(6) NOT NULL,
  `idautore` smallint(6) DEFAULT NULL,
  `dataeora` varchar(40) DEFAULT NULL,
  `testo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `commenti`
--

INSERT INTO `commenti` (`idcommento`, `idpost`, `idautore`, `dataeora`, `testo`) VALUES
(34, 144, 18, NULL, 'wow'),
(54, 162, 18, '02/07/2020 11:20:08 AM', 'wow non vedo l\'ora'),
(56, 51, 197, '02/07/2020 4:36:04 PM', 'non vedo l\'ora');

-- --------------------------------------------------------

--
-- Struttura della tabella `foto`
--

CREATE TABLE `foto` (
  `idpost` smallint(6) NOT NULL,
  `idfoto` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `foto`
--

INSERT INTO `foto` (`idpost`, `idfoto`) VALUES
(141, 70),
(153, 109),
(158, 173);

-- --------------------------------------------------------

--
-- Struttura della tabella `FotoBlog`
--

CREATE TABLE `FotoBlog` (
  `idBlog` smallint(6) NOT NULL,
  `idFoto` smallint(6) NOT NULL,
  `sfondo` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `FotoBlog`
--

INSERT INTO `FotoBlog` (`idBlog`, `idFoto`, `sfondo`) VALUES
(54, 197, 0),
(54, 198, 1),
(59, 86, 0),
(64, 87, 0),
(89, 81, 0),
(91, 88, 0),
(126, 147, 0),
(137, 185, 0),
(137, 186, 1),
(145, 193, 0),
(145, 194, 1),
(146, 200, 0),
(146, 201, 1);

-- --------------------------------------------------------

--
-- Struttura della tabella `FotoProfilo`
--

CREATE TABLE `FotoProfilo` (
  `idUtente` smallint(6) NOT NULL,
  `idFoto` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `FotoProfilo`
--

INSERT INTO `FotoProfilo` (`idUtente`, `idFoto`) VALUES
(18, 202),
(108, 114),
(190, 187),
(197, 199);

-- --------------------------------------------------------

--
-- Struttura della tabella `multimedia`
--

CREATE TABLE `multimedia` (
  `idmultimedia` smallint(6) NOT NULL,
  `file` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `multimedia`
--

INSERT INTO `multimedia` (`idmultimedia`, `file`) VALUES
(113, ''),
(62, '5eecedf53911c8.65605617.jpg'),
(63, '5eecee082117e3.34259957.jpg'),
(64, '5eecee1f892055.06268203.jpg'),
(65, '5eecee270af1c6.11736630.jpg'),
(66, '5eecee354b5757.53076711.jpg'),
(67, '5eecee5b8e3953.48144883.jpg'),
(68, '5eecee6c0edbc8.49638769.jpg'),
(69, '5eeceea6a05d36.85152744.jpg'),
(70, '5eeceed846c818.41908899.jpg'),
(71, '5eecef1c41e943.19782152.jpg'),
(72, '5eedc2fa43d1b8.70151679.jpg'),
(73, '5eedc75341c5e0.48972953.jpg'),
(74, '5eedc83398e634.42303783.jpg'),
(75, '5eedc85196f4f0.46368359.jpg'),
(76, '5eedc9f37c78a2.33426013.jpg'),
(77, '5eedca1f3d22e7.21840431.jpg'),
(78, '5eedcaa5082123.66743363.jpg'),
(79, '5eedcb8762a659.38197555.jpg'),
(80, '5eedcb9f30ec91.83704152.jpg'),
(81, '5eedcbd5259f82.16388717.jpg'),
(82, '5eedcbf4d61c33.81770722.jpg'),
(83, '5eedcd734c27e6.75471836.jpg'),
(84, '5eedcdc01b6843.61427521.jpg'),
(85, '5eedcdf6b59e45.37601767.jpg'),
(86, '5eedce841d3cc8.90093305.jpg'),
(87, '5eedcfa4226d32.51569718.jpg'),
(88, '5eedcfd328a761.30776645.png'),
(89, '5eedd330493313.37547041.jpg'),
(90, '5eedd3aa382c55.20937674.jpg'),
(91, '5eedd3e2159bc2.71727691.jpg'),
(92, '5eedd4abee9d15.76991335.png'),
(93, '5eedd5b59e9d54.33405979.png'),
(94, '5eedd5bd0a2ab1.59714124.png'),
(95, '5eedd704f41057.30442894.png'),
(96, '5eedd7200d2dd9.25560664.jpg'),
(97, '5eedd73b0dabb1.38835872.jpg'),
(98, '5eedd759651368.72756462.jpg'),
(99, '5eedd7cc7befb8.44423391.jpg'),
(100, '5eedd9ef9d13a6.92143325.png'),
(101, '5eee09823c8f04.89714320.jpg'),
(102, '5eee0df91540a6.32190512.jpg'),
(103, '5eef24d247d551.84687516.jpg'),
(104, '5eef24de09dd29.30704455.jpg'),
(105, '5eef8d05e2cae2.97292021.jpg'),
(106, '5eef8d1eb581c4.88799611.jpg'),
(107, '5eef8d328c0bb4.96520849.jpg'),
(108, '5ef07640afaa93.04076594.jpg'),
(109, '5ef0c0841bd9a2.38139716.jpg'),
(110, '5ef0d010b99f96.03351408.jpg'),
(111, '5ef1dc0e7c14e6.44414704.jpg'),
(112, '5ef1dc374bf948.67387547.jpg'),
(128, '5ef329664fac34.25251375.jpg'),
(130, '5ef36e56097b81.48427535.jpg'),
(131, '5ef37105bee314.80034087.jpg'),
(132, '5ef37631dfd7b4.13842144.jpg'),
(133, '5ef376fe2834d4.69722975.jpg'),
(134, '5ef37757520a62.08451976.jpg'),
(135, '5ef37859b06700.93821692.jpg'),
(136, '5ef378c7be7020.02007932.jpg'),
(137, '5ef37941699544.77989447.jpg'),
(138, '5ef3796e0fcea0.69497908.jpg'),
(139, '5ef379d4e31b08.17098778.jpg'),
(140, '5ef37a5816f837.49261369.jpg'),
(141, '5ef37a8f0d9164.48676947.jpg'),
(142, '5ef37b1033ca10.71421846.jpg'),
(143, '5ef38742a50df6.93388076.jpg'),
(144, '5ef387bfa8c417.74334509.jpg'),
(145, '5ef388cb0dd590.66997953.jpg'),
(146, '5ef462beb2cc37.37485139.jpg'),
(147, '5ef46c58943871.78224693.jpg'),
(149, '5ef5a87b4f0928.70696702.jpg'),
(150, '5efafb78dab345.01102348.jpg'),
(151, '5efafba874e639.72857038.png'),
(152, '5efafbec94cc67.13629756.jpg'),
(153, '5efafc896c6576.43257251.png'),
(154, '5efafce527a254.24831535.jpg'),
(155, '5efaff14a4cf31.20846778.jpg'),
(156, '5efaff14abfcc1.45352709.png'),
(157, '5efaffe4721a17.35349895.jpg'),
(158, '5efaffe4784270.42856911.png'),
(159, '5efb015d5162d1.09624984.jpg'),
(160, '5efb015d5862d3.06585498.jpg'),
(161, '5efb061c6e6350.95877476.png'),
(162, '5efb061c75d5f2.94534396.png'),
(163, '5efb06391af7b0.92752120.png'),
(164, '5efb063926a9e7.40699026.jpg'),
(165, '5efb06948e9da4.22021714.png'),
(166, '5efb0694934d57.34267743.png'),
(167, '5efb073d893d66.91486077.png'),
(168, '5efb079b6aff11.78985522.jpg'),
(169, '5efb07d1131550.53838912.png'),
(170, '5efb07dd4c81a3.19084224.jpg'),
(171, '5efb082d5fe027.93780070.png'),
(172, '5efb102d3de698.95255070.jpg'),
(173, '5efb110a4053d3.20621568.jpg'),
(174, '5efc4ad4b6b1e4.48835613.jpg'),
(175, '5efc4b6834af86.65985459.jpg'),
(176, '5efc4b68375f45.65710456.jpg'),
(177, '5efc4bb6373a32.28848216.jpg'),
(178, '5efc4ca1bf1ed8.21644428.jpg'),
(179, '5efc4d9a4179b1.41252381.jpg'),
(180, '5efc4df922bc33.47706866.jpg'),
(181, '5efc4df924d627.60483529.jpg'),
(182, '5efc4f98ee52a3.43036625.jpg'),
(183, '5efc5359abf109.21354637.jpg'),
(184, '5efc5359afc093.04368572.png'),
(185, '5efc536900b468.74037132.jpg'),
(186, '5efc536903b1e9.88649033.png'),
(187, '5efc92ce5a3221.23969672.jpg'),
(188, '5efc9437521551.33826974.jpg'),
(189, '5efc94b4203211.02174030.jpg'),
(190, '5efc98e56a2ef3.51890301.jpg'),
(191, '5efc998f914b48.93804115.jpg'),
(192, '5efc998f949119.87101717.jpg'),
(193, '5efc99bdbb22f4.72095859.png'),
(194, '5efc99c4ec2c97.37251004.png'),
(195, '5efdddbca86471.62117895.jpg'),
(196, '5efde0563d6da3.69523239.png'),
(197, '5efde0cfa694c3.81613983.jpg'),
(198, '5efde0cfaaba99.87630003.jpg'),
(199, '5efdee3aa69d34.28789339.jpg'),
(200, '5efdf09513b533.44338369.jpg'),
(201, '5efdf0951659f5.34704126.jpg'),
(202, '5efdf0fa17c371.02971206.jpg'),
(203, '5eff00ebf3e377.66078636.jpg'),
(114, 'images.jpg');

-- --------------------------------------------------------

--
-- Struttura della tabella `piaciuti`
--

CREATE TABLE `piaciuti` (
  `idutente` smallint(6) NOT NULL,
  `idpost` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `piaciuti`
--

INSERT INTO `piaciuti` (`idutente`, `idpost`) VALUES
(18, 40),
(18, 47),
(18, 48),
(18, 50),
(18, 51),
(18, 56),
(18, 132),
(18, 140),
(18, 141),
(18, 144),
(18, 153),
(18, 155),
(18, 156),
(18, 158),
(20, 40),
(20, 51),
(190, 47),
(197, 51);

-- --------------------------------------------------------

--
-- Struttura della tabella `post`
--

CREATE TABLE `post` (
  `idpost` smallint(6) NOT NULL,
  `idblog` smallint(6) NOT NULL,
  `idautore` smallint(6) DEFAULT NULL,
  `titolo` varchar(40) NOT NULL,
  `dataeora` varchar(40) DEFAULT NULL,
  `testo` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `post`
--

INSERT INTO `post` (`idpost`, `idblog`, `idautore`, `titolo`, `dataeora`, `testo`) VALUES
(40, 54, 18, 'Juve-Sampdoria', NULL, 'Ronaldo a'),
(47, 54, 18, 'Coppa Italia', NULL, 'Juve Milan 0-0'),
(48, 54, 18, 'Coppa Italia', NULL, 'Inter Napoli'),
(50, 54, 18, '23 giornata', NULL, 'Atalanta Milan'),
(51, 59, 18, '4 stagione', NULL, 'In arrivo a dicembre 2020'),
(54, 64, 18, 'Carlos Sainz Ferrari', NULL, 'È l uomo giusto?'),
(56, 54, 18, 'Brescia-SPAL', NULL, 'Sfida salvezz'),
(132, 54, 18, 'ciao', NULL, 'ciao'),
(140, 54, 18, 'petagna', NULL, 'grande giocatore'),
(141, 89, 18, 'Freddy Mercury', NULL, 'È stato il cantante dei queen'),
(144, 83, 20, 'il mugello', NULL, 'gara'),
(152, 89, 18, 'aaaaaaa', NULL, 'aasssv'),
(153, 54, 18, 'partita', NULL, 'partiststssg'),
(155, 54, 18, '31 giornata', '25/06/2020 11:28.20 AM', 'partite assurde'),
(156, 60, 18, 'campionato 2019/2020', '29/06/2020 10:11:42 AM', 'liverpool campione'),
(158, 54, 18, '32 giornata', '30/06/2020 12:16:42 PM', 'wow'),
(162, 64, 18, 'inizio stagione 2020', '02/07/2020 11:19:54 AM', 'domenica 5 luglio');

-- --------------------------------------------------------

--
-- Struttura della tabella `segueblog`
--

CREATE TABLE `segueblog` (
  `idblog` smallint(6) NOT NULL,
  `idutente` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `segueblog`
--

INSERT INTO `segueblog` (`idblog`, `idutente`) VALUES
(54, 20),
(54, 190),
(59, 18),
(59, 20),
(59, 100),
(83, 20);

-- --------------------------------------------------------

--
-- Struttura della tabella `seguetema`
--

CREATE TABLE `seguetema` (
  `idutente` smallint(6) NOT NULL,
  `idtema` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `seguetema`
--

INSERT INTO `seguetema` (`idutente`, `idtema`) VALUES
(27, 14),
(27, 18);

-- --------------------------------------------------------

--
-- Struttura della tabella `segui`
--

CREATE TABLE `segui` (
  `idfollower` smallint(6) NOT NULL,
  `idseguito` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `segui`
--

INSERT INTO `segui` (`idfollower`, `idseguito`) VALUES
(18, 20),
(18, 27),
(20, 18),
(20, 29),
(100, 20),
(190, 18),
(190, 20);

-- --------------------------------------------------------

--
-- Struttura della tabella `tema`
--

CREATE TABLE `tema` (
  `idtema` smallint(6) NOT NULL,
  `nometema` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tema`
--

INSERT INTO `tema` (`idtema`, `nometema`) VALUES
(36, 'arte'),
(27, 'basket'),
(38, 'beatles'),
(33, 'biologia'),
(12, 'calcio'),
(23, 'canoa'),
(10, 'Cavalli'),
(19, 'Cucina'),
(39, 'dell\'arte'),
(9, 'Formula 1'),
(21, 'Informatica'),
(28, 'ippica'),
(20, 'Letteratura'),
(22, 'Musica'),
(18, 'Natura'),
(35, 'olimpiadi'),
(37, 'piscine'),
(11, 'Scherma'),
(17, 'Sci'),
(25, 'SerieTV'),
(30, 'sport'),
(24, 'Storia'),
(15, 'Tennis'),
(14, 'Viaggi');

-- --------------------------------------------------------

--
-- Struttura della tabella `tematica`
--

CREATE TABLE `tematica` (
  `idblog` smallint(6) NOT NULL,
  `idtema` smallint(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `tematica`
--

INSERT INTO `tematica` (`idblog`, `idtema`) VALUES
(54, 12),
(59, 25),
(60, 12),
(64, 9),
(65, 27),
(89, 22),
(91, 15),
(93, 12),
(120, 36),
(124, 37),
(126, 35),
(137, 22),
(145, 12),
(146, 22);

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `idutente` smallint(6) NOT NULL,
  `nickname` varchar(20) NOT NULL DEFAULT 'Utente non esistente',
  `documento` varchar(15) NOT NULL,
  `nome` varchar(20) NOT NULL,
  `cognome` varchar(20) NOT NULL,
  `telefono` varchar(10) NOT NULL,
  `mail` varchar(25) NOT NULL,
  `password` varchar(15) NOT NULL,
  `bio` text NOT NULL,
  `numerocarta` varchar(16) DEFAULT NULL,
  `upgrade` tinyint(1) NOT NULL,
  `moderatore` tinyint(1) DEFAULT NULL,
  `tipo` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`idutente`, `nickname`, `documento`, `nome`, `cognome`, `telefono`, `mail`, `password`, `bio`, `numerocarta`, `upgrade`, `moderatore`, `tipo`) VALUES
(18, 'gnero32222', 'ax456', 'franco', 'genrino', '324638835', 'jacopogneri9@gmail.com', 'marghe    ', 'ciaoneeee', '3527353845238453', 1, 1, 'premium'),
(20, 'sirryyy', 'ax77777', 'alessio', 'siragusa', '34556', 'ale@sir', 'messina ', 'alessandro del piero 10', NULL, 1, 1, 'premium'),
(27, 'AleMa', 'AX3457', 'Alessandro', 'Moro', '345567843', 'ale@magno', 'Grecia', 'Sono macedone', '', 0, NULL, 'standard'),
(29, 'NapoBona', 'ax3456', 'Napoleone', 'Bonaparte', '3456783', 'napo@gmail.com', 'Waterloo', 'imperatore dal 1804', '', 0, NULL, 'standard'),
(100, 'Bisione3', 'ax3435363', 'claudio', 'bisio', '3537489494', 'bisio@gmail.com', 'ciao', 'ciao', NULL, 0, NULL, 'standard'),
(108, 'castiBa22', 'ax334533', 'baldassarre', 'castiglione', '2236733838', 'balda@gmail.com', 'ciao', 'ciao', '7899945758599505', 0, NULL, 'premium'),
(190, 'franchino32', 'ax343434343434', 'franco', 'franchi', '3525382368', 'franco@gmail.com', 'ciao   ', 'ciao', '5622793273027320', 0, NULL, 'premium'),
(197, 'guetta33', 'ax342111', 'david', 'guetta', '3523528352', 'guetta@gmail.com', 'musica    ', 'sono david guetta', '5434534534535845', 0, NULL, 'premium');

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `blog`
--
ALTER TABLE `blog`
  ADD PRIMARY KEY (`idblog`),
  ADD UNIQUE KEY `titolo` (`titolo`),
  ADD KEY `blog_author` (`autore`);

--
-- Indici per le tabelle `coautore`
--
ALTER TABLE `coautore`
  ADD PRIMARY KEY (`idcoautore`,`idblog`),
  ADD KEY `coautore_idblog` (`idblog`);

--
-- Indici per le tabelle `commenti`
--
ALTER TABLE `commenti`
  ADD PRIMARY KEY (`idcommento`),
  ADD KEY `commenti_post` (`idpost`),
  ADD KEY `commento_author` (`idautore`);

--
-- Indici per le tabelle `foto`
--
ALTER TABLE `foto`
  ADD PRIMARY KEY (`idpost`,`idfoto`),
  ADD KEY `foto_multimedia` (`idfoto`);

--
-- Indici per le tabelle `FotoBlog`
--
ALTER TABLE `FotoBlog`
  ADD PRIMARY KEY (`idBlog`,`idFoto`),
  ADD KEY `multimedia_foto` (`idFoto`);

--
-- Indici per le tabelle `FotoProfilo`
--
ALTER TABLE `FotoProfilo`
  ADD PRIMARY KEY (`idUtente`,`idFoto`),
  ADD KEY `profilo_foto` (`idFoto`);

--
-- Indici per le tabelle `multimedia`
--
ALTER TABLE `multimedia`
  ADD PRIMARY KEY (`idmultimedia`),
  ADD UNIQUE KEY `file` (`file`);

--
-- Indici per le tabelle `piaciuti`
--
ALTER TABLE `piaciuti`
  ADD PRIMARY KEY (`idutente`,`idpost`),
  ADD KEY `piaciuti_post` (`idpost`);

--
-- Indici per le tabelle `post`
--
ALTER TABLE `post`
  ADD PRIMARY KEY (`idpost`),
  ADD KEY `post_blog` (`idblog`),
  ADD KEY `post_author` (`idautore`);

--
-- Indici per le tabelle `segueblog`
--
ALTER TABLE `segueblog`
  ADD PRIMARY KEY (`idblog`,`idutente`),
  ADD KEY `blog_utente` (`idutente`);

--
-- Indici per le tabelle `seguetema`
--
ALTER TABLE `seguetema`
  ADD PRIMARY KEY (`idutente`,`idtema`),
  ADD KEY `tema_temaseguito` (`idtema`);

--
-- Indici per le tabelle `segui`
--
ALTER TABLE `segui`
  ADD PRIMARY KEY (`idfollower`,`idseguito`),
  ADD KEY `segui_seguito` (`idseguito`);

--
-- Indici per le tabelle `tema`
--
ALTER TABLE `tema`
  ADD PRIMARY KEY (`idtema`),
  ADD UNIQUE KEY `nometema` (`nometema`);

--
-- Indici per le tabelle `tematica`
--
ALTER TABLE `tematica`
  ADD PRIMARY KEY (`idblog`,`idtema`),
  ADD KEY `tematica_tema` (`idtema`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`idutente`),
  ADD UNIQUE KEY `nickname` (`nickname`),
  ADD UNIQUE KEY `documento` (`documento`),
  ADD UNIQUE KEY `mail` (`mail`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `blog`
--
ALTER TABLE `blog`
  MODIFY `idblog` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT per la tabella `commenti`
--
ALTER TABLE `commenti`
  MODIFY `idcommento` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT per la tabella `multimedia`
--
ALTER TABLE `multimedia`
  MODIFY `idmultimedia` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT per la tabella `post`
--
ALTER TABLE `post`
  MODIFY `idpost` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=163;

--
-- AUTO_INCREMENT per la tabella `tema`
--
ALTER TABLE `tema`
  MODIFY `idtema` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT per la tabella `utente`
--
ALTER TABLE `utente`
  MODIFY `idutente` smallint(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `blog`
--
ALTER TABLE `blog`
  ADD CONSTRAINT `blog_author` FOREIGN KEY (`autore`) REFERENCES `utente` (`idutente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `coautore`
--
ALTER TABLE `coautore`
  ADD CONSTRAINT `coautore_idblog` FOREIGN KEY (`idblog`) REFERENCES `blog` (`idblog`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coautore_idutente` FOREIGN KEY (`idcoautore`) REFERENCES `utente` (`idutente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `commenti`
--
ALTER TABLE `commenti`
  ADD CONSTRAINT `commenti_post` FOREIGN KEY (`idpost`) REFERENCES `post` (`idpost`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `commento_author` FOREIGN KEY (`idautore`) REFERENCES `utente` (`idutente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `foto`
--
ALTER TABLE `foto`
  ADD CONSTRAINT `foto_multimedia` FOREIGN KEY (`idfoto`) REFERENCES `multimedia` (`idmultimedia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `foto_post` FOREIGN KEY (`idpost`) REFERENCES `post` (`idpost`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `FotoBlog`
--
ALTER TABLE `FotoBlog`
  ADD CONSTRAINT `blog_foto` FOREIGN KEY (`idBlog`) REFERENCES `blog` (`idblog`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `multimedia_foto` FOREIGN KEY (`idFoto`) REFERENCES `multimedia` (`idmultimedia`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `FotoProfilo`
--
ALTER TABLE `FotoProfilo`
  ADD CONSTRAINT `profilo_foto` FOREIGN KEY (`idFoto`) REFERENCES `multimedia` (`idmultimedia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `profilo_utente` FOREIGN KEY (`idUtente`) REFERENCES `utente` (`idutente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `piaciuti`
--
ALTER TABLE `piaciuti`
  ADD CONSTRAINT `piaciuti_post` FOREIGN KEY (`idpost`) REFERENCES `post` (`idpost`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `piaciuti_utente` FOREIGN KEY (`idutente`) REFERENCES `utente` (`idutente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `post`
--
ALTER TABLE `post`
  ADD CONSTRAINT `post_author` FOREIGN KEY (`idautore`) REFERENCES `utente` (`idutente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `post_blog` FOREIGN KEY (`idblog`) REFERENCES `blog` (`idblog`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `segueblog`
--
ALTER TABLE `segueblog`
  ADD CONSTRAINT `blog_seguito` FOREIGN KEY (`idblog`) REFERENCES `blog` (`idblog`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `blog_utente` FOREIGN KEY (`idutente`) REFERENCES `utente` (`idutente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `seguetema`
--
ALTER TABLE `seguetema`
  ADD CONSTRAINT `tema_temaseguito` FOREIGN KEY (`idtema`) REFERENCES `tema` (`idtema`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tema_utente` FOREIGN KEY (`idutente`) REFERENCES `utente` (`idutente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `segui`
--
ALTER TABLE `segui`
  ADD CONSTRAINT `segui_follower` FOREIGN KEY (`idfollower`) REFERENCES `utente` (`idutente`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `segui_seguito` FOREIGN KEY (`idseguito`) REFERENCES `utente` (`idutente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Limiti per la tabella `tematica`
--
ALTER TABLE `tematica`
  ADD CONSTRAINT `tematica_blog` FOREIGN KEY (`idblog`) REFERENCES `blog` (`idblog`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tematica_tema` FOREIGN KEY (`idtema`) REFERENCES `tema` (`idtema`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
