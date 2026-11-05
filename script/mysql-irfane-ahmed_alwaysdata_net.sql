-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql-irfane-ahmed.alwaysdata.net
-- Generation Time: Nov 05, 2026 at 03:14 PM
-- Server version: 11.8.9-MariaDB
-- PHP Version: 8.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `irfane-ahmed_supercar`
--
DROP DATABASE IF EXISTS `irfane-ahmed_supercar`;
CREATE DATABASE IF NOT EXISTS `irfane-ahmed_supercar` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_ai_ci;
USE `irfane-ahmed_supercar`;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `nom` varchar(100) NOT NULL DEFAULT 'Administrateur'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `login`, `password_hash`, `nom`) VALUES
(1, 'admin', '$2y$10$WbWxtMh0HQ8J7wbjr4WvMedMA/ZQ4NBDzHsdWvNXIptbQJ6VKlD7W', 'Administrateur');

-- --------------------------------------------------------

--
-- Table structure for table `beneficier`
--

CREATE TABLE `beneficier` (
  `idService` int(11) NOT NULL,
  `idVoiture` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client`
--

CREATE TABLE `client` (
  `idClient` int(11) NOT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `inscrit` tinyint(1) DEFAULT 1,
  `dateInscription` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `client`
--

INSERT INTO `client` (`idClient`, `prenom`, `email`, `telephone`, `nom`, `inscrit`, `dateInscription`) VALUES
(5, 'Ali', 'ali@gmail.com', '59000001', 'Hassan', 0, '2026-04-01'),
(6, 'Sara', 'sara@gmail.com', '59000002', 'Mohamed', 0, '2026-04-02'),
(7, 'Jean', 'jean@gmail.com', '59000003', 'Dupont', 0, '2026-04-03'),
(11, 'Youssef', 'youssef3@gmail.com', '59000003', 'Karim', 0, NULL),
(12, 'Amina', 'amina4@gmail.com', '59000004', 'Said', 0, NULL),
(13, 'Omar', 'omar5@gmail.com', '59000005', 'Nadir', 0, NULL),
(15, 'Hassan', 'hassan7@gmail.com', '59000007', 'Abdi', 0, NULL),
(16, 'Salma', 'salma8@gmail.com', '59000008', 'Yahya', 0, NULL),
(17, 'Bilal', 'bilal9@gmail.com', '59000009', 'Khan', 0, NULL),
(18, 'Nadia', 'nadia10@gmail.com', '59000010', 'Ali', 0, NULL),
(19, 'Karim', 'karim11@gmail.com', '59000011', 'Omar', 0, NULL),
(20, 'Zara', 'zara12@gmail.com', '59000012', 'Hussein', 0, NULL),
(21, 'Imran', 'imran13@gmail.com', '59000013', 'Sheikh', 0, NULL),
(23, 'Tariq', 'tariq15@gmail.com', '59000015', 'Malik', 0, NULL),
(25, 'Rachid', 'rachid17@gmail.com', '59000017', 'Benali', 0, NULL),
(26, 'Meryem', 'meryem18@gmail.com', '59000018', 'Toumi', 0, NULL),
(27, 'Anis', 'anis19@gmail.com', '59000019', 'Brahimi', 0, NULL),
(28, 'Sofia', 'sofia20@gmail.com', '59000020', 'Mehdi', 0, NULL),
(29, 'Nabil', 'nabil21@gmail.com', '59000021', 'Farouk', 0, NULL),
(30, 'Lina', 'lina22@gmail.com', '59000022', 'Khaled', 0, NULL),
(31, 'Adel', 'adel23@gmail.com', '59000023', 'Mansour', 0, NULL),
(32, 'Yasmine', 'yasmine24@gmail.com', '59000024', 'Amar', 0, NULL),
(33, 'Walid', 'walid25@gmail.com', '59000025', 'Cherif', 0, NULL),
(34, 'Dina', 'dina26@gmail.com', '59000026', 'Saad', 0, NULL),
(35, 'Fares', 'fares27@gmail.com', '59000027', 'Zidane', 0, NULL),
(37, 'Kamel', 'kamel29@gmail.com', '59000029', 'Selim', 0, NULL),
(38, 'Rania', 'rania30@gmail.com', '59000030', 'Latif', 0, NULL),
(39, 'Sami', 'sami31@gmail.com', '59000031', 'Ghali', 0, NULL),
(40, 'Nada', 'nada32@gmail.com', '59000032', 'Haddad', 0, NULL),
(41, 'Issam', 'issam33@gmail.com', '59000033', 'Daoud', 0, NULL),
(42, 'Farida', 'farida34@gmail.com', '59000034', 'Kamel', 0, NULL),
(43, 'Mehdi', 'mehdi35@gmail.com', '59000035', 'Jaber', 0, NULL),
(44, 'Houda', 'houda36@gmail.com', '59000036', 'Salem', 0, NULL),
(45, 'Yacine', 'yacine37@gmail.com', '59000037', 'Mokhtar', 0, NULL),
(46, 'Amel', 'amel38@gmail.com', '59000038', 'Bouzid', 0, NULL),
(47, 'Reda', 'reda39@gmail.com', '59000039', 'Kassir', 0, NULL),
(48, 'Noura', 'noura40@gmail.com', '59000040', 'Fadel', 0, NULL),
(49, 'Othmane', 'othmane41@gmail.com', '59000041', 'Rizk', 0, NULL),
(50, 'Souad', 'souad42@gmail.com', '59000042', 'Aziz', 0, NULL),
(51, 'Hamza', 'hamza43@gmail.com', '59000043', 'Taleb', 0, NULL),
(52, 'Ilham', 'ilham44@gmail.com', '59000044', 'Bensalem', 0, NULL),
(53, 'Zakaria', 'zakaria45@gmail.com', '59000045', 'Moussa', 0, NULL),
(54, 'Khadija', 'khadija46@gmail.com', '59000046', 'Nouri', 0, NULL),
(55, 'Ayoub', 'ayoub47@gmail.com', '59000047', 'Hariri', 0, NULL),
(56, 'Siham', 'siham48@gmail.com', '59000048', 'Boukhaled', 0, NULL),
(57, 'Marwan', 'marwan49@gmail.com', '59000049', 'Dahmani', 0, NULL),
(58, 'Asma', 'asma50@gmail.com', '59000050', 'Zerouali', 0, NULL),
(59, 'Abdou', 'ahmedabdou2103@gmail.com', '123 321 1112', 'Ahmed', 1, '2026-04-13'),
(60, 'Abdou', 'ahmedabdou2103w@gmail.com', '123 321 1112', 'Ahmed', 1, '2026-04-14'),
(61, 'Abdou', 'ahmedabdou21033@gmail.com', '123 321 1112', 'Ahmed', 1, '2026-04-17'),
(62, 'Abdou', 'ahmedabdou210d3@gmail.com', '123 321 1112', 'Ahmed', 1, '2026-04-21'),
(63, 'Abdou', 'ahmedabd32ou2103@gmail.com', '123 321 11124', 'Ahmed', 1, '2026-04-22'),
(64, 'Abdou', 'a@gmail.com', '123 321 111233', 'Ahmed', 1, '2026-04-22'),
(65, 'Abdou', 'ahmedabd11ou2103@gmail.com', '123 321 1112', 'Ahmed', 1, '2026-04-22'),
(66, 'Irfane', 'irfaneahmed100911@gmail.com', '3211049', 'Ahmed', 1, '2026-05-09'),
(67, 'Irfane', 'irfaneahmed@gmail.com', '3211049', 'Ahmed', 1, '2026-05-10'),
(68, 'Irfane', 'irfane@gmail.com', '3211049', 'Ahmed', 0, '2026-05-11'),
(72, 'irfane', 'irfaneahmed1009@gmail.com', '3211049', 'ahmed', 1, '2026-10-07'),
(73, 'irfane', 'irfaneahmed2007@gmail.com', '3211049', 'ahmed', 1, '2026-10-13'),
(74, 'irfane', 'irfaneahmed33333@gmail.com', '3211049', 'ahmed', 1, '2026-10-14'),
(75, 'irfane', 'login@gmail.com', '3211049', 'ahmed', 1, '2026-10-16');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `idContact` int(11) NOT NULL,
  `message` varchar(255) DEFAULT NULL,
  `idClient` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`idContact`, `message`, `idClient`) VALUES
(2, 'Un petit message', 59),
(3, 'Un autre message', 59),
(4, 'Bonjour', 62),
(5, 'un test', 68),
(6, 'irfane\'\'', 68),
(7, '\'\'\'\'\'\'\'\'\'', 68),
(8, '1', 64);

-- --------------------------------------------------------

--
-- Table structure for table `demande_d_essai`
--

CREATE TABLE `demande_d_essai` (
  `idDemande` int(11) NOT NULL,
  `dateSouhaitee` date DEFAULT NULL,
  `status` varchar(50) DEFAULT 'en attente',
  `dateDemande` date DEFAULT NULL,
  `idVoiture` int(11) NOT NULL,
  `idClient` int(11) NOT NULL,
  `heureDemande` time DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

--
-- Dumping data for table `demande_d_essai`
--

INSERT INTO `demande_d_essai` (`idDemande`, `dateSouhaitee`, `status`, `dateDemande`, `idVoiture`, `idClient`, `heureDemande`) VALUES
(20, '2027-10-11', 'accepté', '2027-10-10', 67, 20, '12:45:00'),
(21, '2027-10-11', 'accepté', '2027-10-10', 67, 20, '12:45:00'),
(22, '2026-10-30', 'en attente', '2026-10-15', 74, 72, '09:00:00'),
(23, '2026-10-23', 'en attente', '2026-10-15', 65, 73, '19:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `login`
--

CREATE TABLE `login` (
  `Identifiant` int(11) NOT NULL,
  `mp` varchar(255) NOT NULL,
  `idClient` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login`
--

INSERT INTO `login` (`Identifiant`, `mp`, `idClient`) VALUES
(5, '$2y$10$W4lEk7o9eifr8s7Rl1eZaeyvzrn5WsKtFukOiabWjNi7EC18.u14K', 59),
(6, '$2y$10$xzd/Z/FQOaV.NyBl4MYPaOr1p/zja3mpNRsfKYcVy7cNRvKqYIdSW', 60),
(7, '$2y$10$YS/VpWSWtIvlqWyezjQIbOYDJsgAuA3kGJHnntqhQ3qc3XxnL5Ufa', 61),
(8, '$2y$10$qbi2KTVW9sV6P0g8cYf5AO76/MwwAzL3zVm4rMYV18WHuKcG2364K', 62),
(9, '$2y$10$yerUds61SQpVFZMNhtmnw.sKCojS7rAYeKrZPOJg7higfE2pZIkN6', 63),
(10, '$2y$10$s3bV/h9RbtBu5eLfkbDYb.tK2ND6BjPy3djS3H1WUbqlnsswHnwua', 64),
(11, '$2y$10$jAALsPuDHNaVCaICnAXTr.SYTzXF4qHtIrF6tWMmfrkSXQsobUJsq', 65),
(12, '$2y$10$6yXgIkZfWpCjrya5ZFbZiehIOZFljOHdgPa43Y7SP9sY/70C6za1W', 66),
(13, '$2y$10$ooA/biBQzOu2v0bFW1VvGOED85.0tWYNy/KiVWSBmgE8wbQJMeXtO', 67),
(14, '$2y$10$L7naVw5BbT9Yb952rV593.FqCuo/YA/V2EHwoSSi3c0KCesOyBWk6', 68),
(24, '$2y$10$73auw8FvsyjKk4S2NRril.AlnEP2DUw4fkMmmW//Ll8tkL03oUQOG', 72),
(25, '$2y$12$8PBj5e4DiYudW7kymIcbx.UQTYHHr88KD.J6vqBkVZoT58jMuegYO', 73),
(26, '$2y$10$r1uSxlqr1yCoh9rVLzEDvu7SINtmQeOaGwZRPl0I4965yDbGjD/H.', 74),
(27, '$2y$12$tYUFZ2QRtqf0sUPK42KluersqbqRlfd90OVA9joIC0bCBjagogLDS', 75);

-- --------------------------------------------------------

--
-- Table structure for table `marque`
--

CREATE TABLE `marque` (
  `id_marque` int(11) NOT NULL,
  `nom` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marque`
--

INSERT INTO `marque` (`id_marque`, `nom`) VALUES
(1, 'BMW'),
(2, 'Mercedes'),
(3, 'Audi'),
(4, 'Toyota');

-- --------------------------------------------------------

--
-- Table structure for table `service`
--

CREATE TABLE `service` (
  `idService` int(11) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `libelle` varchar(100) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service`
--

INSERT INTO `service` (`idService`, `nom`, `libelle`, `img`) VALUES
(1, 'Personnalisation', 'Personnalisation de votre voiture', 'interieur.jpg'),
(2, 'Entretien', 'Révision complète et entretien périodique', 'reparation.jpg'),
(3, 'Garantie', 'Extension de garantie constructeur', 'vindagement.jpg'),
(4, 'Assurance', 'Assurance tous risques partenaire', 'personnalisation.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `voiture`
--

CREATE TABLE `voiture` (
  `idVoiture` int(11) NOT NULL,
  `annee` varchar(10) DEFAULT NULL,
  `modele` varchar(50) DEFAULT NULL,
  `puissance` varchar(50) DEFAULT NULL,
  `prix` varchar(50) DEFAULT NULL,
  `imageVoiture` varchar(255) DEFAULT NULL,
  `typeCarburant` varchar(50) DEFAULT NULL,
  `id_marque` int(11) NOT NULL,
  `estDansCarrousel` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `voiture`
--

INSERT INTO `voiture` (`idVoiture`, `annee`, `modele`, `puissance`, `prix`, `imageVoiture`, `typeCarburant`, `id_marque`, `estDansCarrousel`) VALUES
(65, '2020', 'A4', '150 ch', '1025000', '6ac7479f9eab6.png', 'Diesel', 3, 0),
(66, '2024', 'A3', '116 ch', '1400000', '6ac7434a52b40.png', 'Essence', 3, 0),
(67, '2022', 'AMG C-63', '680 ch', '750000', '6ac74a923bbf7.jpg', 'Essence', 2, 1),
(68, '2010', 'E46', '343 ch', '250000', '6ac74c540e88d.jpg', 'Essence', 1, 0),
(69, '2021', 'Yaris Cross', '115 ch', '288000', '6ac7492383bfe.png', 'Essence', 4, 0),
(70, '2019', 'M4 Competition', '450 ch', '780000', '6ac74cd13f9d9.jpg', 'Essence', 1, 0),
(71, '2021', 'X5 M', '625 ch', '1250000', '6ac74c855bdbb.jpg', 'Essence', 1, 0),
(72, '2020', 'GLA 35 AMG', '306 ch', '620000', '6ac74b5f580e1.jpg', 'Essence', 2, 1),
(73, '2019', 'AMG GT', '476 ch', '980000', '6ac74b0ca6953.jpg', 'Essence', 2, 0),
(74, '2016', 'A5', '190ch', '1150000', '6ac73cfbaaba7.png', 'Essence', 3, 0),
(75, '2017', 'Q2', '190 ch', '580000', '6ac73ac980db5.png', 'Diesel', 3, 0),
(76, '2018', 'Hilux-24', '200 ch', '140000', '6ac74d649cb2c.jpg', 'Essence', 4, 1),
(77, '1994', '100', '90ch', '100000', '6ac73c03c10f7.png', 'Essence', 3, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `login` (`login`);

--
-- Indexes for table `beneficier`
--
ALTER TABLE `beneficier`
  ADD PRIMARY KEY (`idService`,`idVoiture`),
  ADD KEY `beneficier_ibfk_2` (`idVoiture`);

--
-- Indexes for table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`idClient`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`idContact`),
  ADD KEY `contact_ibfk_1` (`idClient`);

--
-- Indexes for table `demande_d_essai`
--
ALTER TABLE `demande_d_essai`
  ADD PRIMARY KEY (`idDemande`),
  ADD KEY `idVoiture` (`idVoiture`),
  ADD KEY `idClient` (`idClient`);

--
-- Indexes for table `login`
--
ALTER TABLE `login`
  ADD PRIMARY KEY (`Identifiant`),
  ADD UNIQUE KEY `idClient` (`idClient`);

--
-- Indexes for table `marque`
--
ALTER TABLE `marque`
  ADD PRIMARY KEY (`id_marque`);

--
-- Indexes for table `service`
--
ALTER TABLE `service`
  ADD PRIMARY KEY (`idService`);

--
-- Indexes for table `voiture`
--
ALTER TABLE `voiture`
  ADD PRIMARY KEY (`idVoiture`),
  ADD KEY `id_marque` (`id_marque`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `client`
--
ALTER TABLE `client`
  MODIFY `idClient` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `idContact` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `demande_d_essai`
--
ALTER TABLE `demande_d_essai`
  MODIFY `idDemande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `login`
--
ALTER TABLE `login`
  MODIFY `Identifiant` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `marque`
--
ALTER TABLE `marque`
  MODIFY `id_marque` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `service`
--
ALTER TABLE `service`
  MODIFY `idService` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `voiture`
--
ALTER TABLE `voiture`
  MODIFY `idVoiture` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=78;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `beneficier`
--
ALTER TABLE `beneficier`
  ADD CONSTRAINT `beneficier_ibfk_1` FOREIGN KEY (`idService`) REFERENCES `service` (`idService`),
  ADD CONSTRAINT `beneficier_ibfk_2` FOREIGN KEY (`idVoiture`) REFERENCES `voiture` (`idVoiture`);

--
-- Constraints for table `contact`
--
ALTER TABLE `contact`
  ADD CONSTRAINT `contact_ibfk_1` FOREIGN KEY (`idClient`) REFERENCES `client` (`idClient`) ON DELETE CASCADE;

--
-- Constraints for table `demande_d_essai`
--
ALTER TABLE `demande_d_essai`
  ADD CONSTRAINT `demande_ibfk_1` FOREIGN KEY (`idVoiture`) REFERENCES `voiture` (`idVoiture`),
  ADD CONSTRAINT `demande_ibfk_2` FOREIGN KEY (`idClient`) REFERENCES `client` (`idClient`) ON DELETE CASCADE;

--
-- Constraints for table `login`
--
ALTER TABLE `login`
  ADD CONSTRAINT `login_ibfk_1` FOREIGN KEY (`idClient`) REFERENCES `client` (`idClient`) ON DELETE CASCADE;

--
-- Constraints for table `voiture`
--
ALTER TABLE `voiture`
  ADD CONSTRAINT `voiture_ibfk_1` FOREIGN KEY (`id_marque`) REFERENCES `marque` (`id_marque`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
