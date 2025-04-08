-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2025 at 02:05 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rentacar`
--

-- --------------------------------------------------------

--
-- Table structure for table `karakteristike_automobila`
--

CREATE TABLE `karakteristike_automobila` (
  `Godiste` int(11) DEFAULT NULL,
  `Kilometraza` int(11) DEFAULT NULL,
  `Registracija` varchar(11) DEFAULT NULL,
  `VoziloID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karakteristike_automobila`
--

INSERT INTO `karakteristike_automobila` (`Godiste`, `Kilometraza`, `Registracija`, `VoziloID`) VALUES
(2018, 45000, 'DA123AB', 50),
(2017, 60000, 'DA456CD', 51),
(2019, 30000, 'DA789EF', 52),
(2020, 25000, 'DA234GH', 53),
(2016, 80000, 'DA567JK', 54),
(2018, 45000, 'DA890LM', 55),
(2019, 35000, 'DA345NP', 56),
(2021, 20000, 'DA678QS', 57),
(2015, 90000, 'DA901TU', 58),
(2020, 40000, 'DA112VX', 59);

-- --------------------------------------------------------

--
-- Table structure for table `korisnici`
--

CREATE TABLE `korisnici` (
  `IDKorisnici` int(11) NOT NULL,
  `ImeKorisnika` char(25) NOT NULL,
  `PrezimeKorisnika` char(25) NOT NULL,
  `KontaktKorisnika` char(40) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `korisnici`
--

INSERT INTO `korisnici` (`IDKorisnici`, `ImeKorisnika`, `PrezimeKorisnika`, `KontaktKorisnika`) VALUES
(1, 'Ivan', 'Horvat', 'ivan.horvat@gmail.com'),
(2, 'Ana', 'Kovačić', 'ana,kovacic@gmail.com'),
(3, 'Marko', 'Novak', 'marko,novak@gmail.com'),
(4, 'Lucija', 'Marić', 'lucija.maric@gmail.com'),
(5, 'Petar', 'Babić', 'petar.babic@gmail.com'),
(6, 'Ivana', 'Jurić', 'ivana.juric@gmail.com'),
(7, 'Karlo', 'Vuković', 'karlo.vukovic@gmail.com'),
(8, 'Marija', 'Božić', 'marija.bozic@gmail.com'),
(9, 'Josip', 'Pavlović', 'josip.pavlovic@gmail.com'),
(10, 'Matej', 'Knežević', 'mate.knezevicj@gmail.com'),
(33, 'Marko', 'Loncarevicx', 'marko.loncarevic@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `rezervacije`
--

CREATE TABLE `rezervacije` (
  `IDRezervacija` int(11) NOT NULL,
  `VoziloID` int(11) NOT NULL,
  `KorisnikID` int(11) NOT NULL,
  `DatumRezervacije` datetime NOT NULL DEFAULT current_timestamp(),
  `DatumPocetka` datetime NOT NULL,
  `DatumZavrsetka` datetime NOT NULL,
  `UkupnaCijena` decimal(10,2) NOT NULL,
  `StatusRezervacije` varchar(20) DEFAULT 'aktivna'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rezervacije`
--

INSERT INTO `rezervacije` (`IDRezervacija`, `VoziloID`, `KorisnikID`, `DatumRezervacije`, `DatumPocetka`, `DatumZavrsetka`, `UkupnaCijena`, `StatusRezervacije`) VALUES
(56, 50, 1, '2025-03-29 16:17:27', '2025-03-01 10:00:00', '2025-03-04 10:00:00', 150.00, 'Zavrsena'),
(57, 51, 2, '2025-03-29 16:17:27', '2025-03-05 09:00:00', '2025-03-10 17:00:00', 250.00, 'Zavrsena'),
(58, 52, 3, '2025-03-29 16:17:27', '2025-03-09 16:00:00', '2025-03-11 20:00:00', 100.00, 'Zavrsena'),
(59, 53, 4, '2025-03-29 16:17:27', '2025-03-15 08:00:00', '2025-03-22 08:00:00', 490.00, 'Zavrsena'),
(60, 54, 5, '2025-03-29 16:17:27', '2025-03-20 14:00:00', '2025-03-22 14:00:00', 100.00, 'otkazana'),
(61, 59, 1, '2025-03-29 16:20:12', '2025-03-29 16:20:00', '2025-03-30 16:20:00', 70.00, 'aktivna'),
(62, 50, 1, '2025-03-29 16:29:27', '2025-03-29 16:28:00', '2025-03-31 16:28:00', 100.00, 'aktivna'),
(65, 58, 1, '2025-03-30 13:58:24', '2025-03-30 13:58:00', '2025-03-31 13:58:00', 50.00, 'aktivna'),
(66, 59, 1, '2025-03-30 13:58:56', '2025-03-30 13:58:00', '2025-03-31 13:58:00', 70.00, 'aktivna'),
(67, 52, 5, '2025-03-30 13:59:20', '2025-03-30 13:59:00', '2025-03-31 13:59:00', 70.00, 'aktivna'),
(68, 58, 1, '2025-03-30 13:59:46', '2025-03-30 13:59:00', '2025-03-31 13:59:00', 50.00, 'aktivna'),
(69, 57, 33, '2025-03-30 14:01:06', '2025-03-29 14:00:00', '2025-03-31 14:01:00', 100.00, 'aktivna'),
(70, 51, 1, '2025-03-30 14:02:03', '2025-03-29 14:02:00', '2025-03-31 14:02:00', 120.00, 'aktivna');

-- --------------------------------------------------------

--
-- Table structure for table `vozila`
--

CREATE TABLE `vozila` (
  `IDVozilo` int(11) NOT NULL,
  `Naziv` char(25) NOT NULL,
  `Model` char(25) DEFAULT NULL,
  `CijenaKoristenjaDnevno` float NOT NULL,
  `Raspolozivost` enum('Dostupno','Nije dostupno') NOT NULL DEFAULT 'Dostupno'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vozila`
--

INSERT INTO `vozila` (`IDVozilo`, `Naziv`, `Model`, `CijenaKoristenjaDnevno`, `Raspolozivost`) VALUES
(50, 'Fiat', 'Punto', 50, 'Dostupno'),
(51, 'Opel', 'Corsa', 60, 'Dostupno'),
(52, 'Volkswagen', 'Golf', 70, 'Dostupno'),
(53, 'Mazda', 'CX5', 50, 'Dostupno'),
(54, 'Opel', 'Astra', 50, 'Dostupno'),
(55, 'Fiat', 'Punto', 50, 'Dostupno'),
(56, 'Renault', 'Clio', 50, 'Dostupno'),
(57, 'Volkswagen', 'Polo', 50, 'Dostupno'),
(58, 'Fiat', 'Panda', 50, 'Dostupno'),
(59, 'Renault', 'Megane', 70, 'Dostupno');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `karakteristike_automobila`
--
ALTER TABLE `karakteristike_automobila`
  ADD PRIMARY KEY (`VoziloID`),
  ADD KEY `VoziloID` (`VoziloID`);

--
-- Indexes for table `korisnici`
--
ALTER TABLE `korisnici`
  ADD PRIMARY KEY (`IDKorisnici`);

--
-- Indexes for table `rezervacije`
--
ALTER TABLE `rezervacije`
  ADD PRIMARY KEY (`IDRezervacija`),
  ADD KEY `KorisnikID` (`KorisnikID`),
  ADD KEY `fk_rezervacije_vozilo` (`VoziloID`);

--
-- Indexes for table `vozila`
--
ALTER TABLE `vozila`
  ADD PRIMARY KEY (`IDVozilo`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `karakteristike_automobila`
--
ALTER TABLE `karakteristike_automobila`
  MODIFY `VoziloID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `korisnici`
--
ALTER TABLE `korisnici`
  MODIFY `IDKorisnici` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `rezervacije`
--
ALTER TABLE `rezervacije`
  MODIFY `IDRezervacija` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `vozila`
--
ALTER TABLE `vozila`
  MODIFY `IDVozilo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `karakteristike_automobila`
--
ALTER TABLE `karakteristike_automobila`
  ADD CONSTRAINT `fk_karakteristike_vozilo` FOREIGN KEY (`VoziloID`) REFERENCES `vozila` (`IDVozilo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `karakteristike_automobila_ibfk_1` FOREIGN KEY (`VoziloID`) REFERENCES `vozila` (`IDVozilo`);

--
-- Constraints for table `rezervacije`
--
ALTER TABLE `rezervacije`
  ADD CONSTRAINT `fk_rezervacije_vozilo` FOREIGN KEY (`VoziloID`) REFERENCES `vozila` (`IDVozilo`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rezervacije_ibfk_1` FOREIGN KEY (`VoziloID`) REFERENCES `vozila` (`IDVozilo`),
  ADD CONSTRAINT `rezervacije_ibfk_2` FOREIGN KEY (`KorisnikID`) REFERENCES `korisnici` (`IDKorisnici`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
