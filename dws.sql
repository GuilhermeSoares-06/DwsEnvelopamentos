-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 25, 2026 at 02:13 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dws`
--

-- --------------------------------------------------------

--
-- Table structure for table `caminhoes`
--

CREATE TABLE `caminhoes` (
  `camid` int NOT NULL,
  `cammarca` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `camelo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `camimg` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `caminhoes`
--

INSERT INTO `caminhoes` (`camid`, `cammarca`, `camelo`, `camimg`) VALUES
(1, 'Mercedes-Benz', 'Actros 2651', 'actros.jpg'),
(2, 'Volvo', 'FH 540', 'fh540.jpg'),
(3, 'Scania', 'R 450', 'r450.jpg'),
(4, 'Volkswagen', 'Meteor 29.520', 'meteor.jpg'),
(5, 'DAF', 'XF 530', 'xf530.jpg'),
(6, 'Iveco', 'S-Way', 'sway.jpg'),
(7, 'Mercedes-Benz', 'Atego 1719', 'atego.jpg'),
(8, 'Volvo', 'VM 330', 'vm330.jpg'),
(9, 'Scania', 'P 360', 'p360.jpg'),
(10, 'Ford', 'Cargo 2429', 'cargo.jpg'),
(11, 'Mercedes-Benz', 'Actros 2546', NULL),
(12, 'Volvo', 'FH 500', NULL),
(13, 'Scania', 'R 450', NULL),
(14, 'Volkswagen', 'Constellation 24.280', NULL),
(15, 'DAF', 'XF 480', NULL),
(16, 'Iveco', 'S-Way 480', NULL),
(17, 'Ford', 'Cargo 2429', NULL),
(18, 'Mercedes-Benz', 'Atego 1719', NULL),
(19, 'Volvo', 'VM 330', NULL),
(20, 'Scania', 'P 360', NULL),
(21, 'Volkswagen', 'Meteor 29.520', NULL),
(22, 'DAF', 'CF 410', NULL),
(23, 'Iveco', 'Tector 240E28', NULL),
(24, 'Ford', 'F-4000', NULL),
(25, 'Mercedes-Benz', 'Axor 2544', NULL),
(26, 'Volvo', 'FM 420', NULL),
(27, 'Scania', 'G 450', NULL),
(28, 'Volkswagen', 'Delivery 11.180', NULL),
(29, 'DAF', 'XF 530', NULL),
(30, 'Iveco', 'Daily 35-160', NULL),
(31, 'Ford', 'Cargo 1723', NULL),
(32, 'Mercedes-Benz', 'Accelo 1016', NULL),
(33, 'Volvo', 'FH 540', NULL),
(34, 'Scania', 'R 500', NULL),
(35, 'Volkswagen', 'Constellation 25.360', NULL),
(36, 'DAF', 'XF 510', NULL),
(37, 'Iveco', 'Tector 170E22', NULL),
(38, 'Ford', 'Cargo 2842', NULL),
(39, 'Mercedes-Benz', 'Atego 2430', NULL),
(40, 'Volvo', 'VM 270', NULL),
(41, 'Scania', 'P 310', NULL),
(42, 'Volkswagen', 'Worker 17.230', NULL),
(43, 'DAF', 'CF 450', NULL),
(44, 'Iveco', 'Hi-Way 440', NULL),
(45, 'Ford', 'Cargo 1119', NULL),
(46, 'Mercedes-Benz', 'Actros 2653', NULL),
(47, 'Volvo', 'FH 460', NULL),
(48, 'Scania', 'R 540', NULL),
(49, 'Volkswagen', 'Delivery 13.180', NULL),
(50, 'DAF', 'CF 480', NULL),
(51, 'Iveco', 'Tector 17-280', NULL),
(52, 'Ford', 'Cargo 3031', NULL),
(53, 'Mercedes-Benz', 'Atego 1726', NULL),
(54, 'Volvo', 'FM 460', NULL),
(55, 'Scania', 'G 410', NULL),
(56, 'Volkswagen', 'Constellation 19.320', NULL),
(57, 'DAF', 'XF 460', NULL),
(58, 'Iveco', 'Daily 45S17', NULL),
(59, 'Ford', 'F-350', NULL),
(60, 'Mercedes-Benz', 'Axor 2644', NULL),
(61, 'Volvo', 'FH 440', NULL),
(62, 'Scania', 'R 480', NULL),
(63, 'Volkswagen', 'Constellation 31.330', NULL),
(64, 'DAF', 'CF 440', NULL),
(65, 'Iveco', 'Tector 11-190', NULL),
(66, 'Ford', 'Cargo 816', NULL),
(67, 'Mercedes-Benz', 'Accelo 815', NULL),
(68, 'Volvo', 'VM 220', NULL),
(69, 'Scania', 'P 250', NULL),
(70, 'Volkswagen', 'Worker 13.180', NULL),
(71, 'DAF', 'XF 105', NULL),
(72, 'Iveco', 'Tector 9-190', NULL),
(73, 'Ford', 'Cargo 1519', NULL),
(74, 'Mercedes-Benz', 'Atego 1419', NULL),
(75, 'Volvo', 'FM 380', NULL),
(76, 'Scania', 'G 440', NULL),
(77, 'Volkswagen', 'Constellation 24.250', NULL),
(78, 'DAF', 'CF 85', NULL),
(79, 'Iveco', 'Tector 150E21', NULL),
(80, 'Ford', 'Cargo 1933', NULL),
(81, 'Mercedes-Benz', 'Actros 2651', NULL),
(82, 'Volvo', 'FH 480', NULL),
(83, 'Scania', 'R 410', NULL),
(84, 'Volkswagen', 'Constellation 26.280', NULL),
(85, 'DAF', 'XF 105 510', NULL),
(86, 'Iveco', 'Tector 240E25', NULL),
(87, 'Ford', 'Cargo 2422', NULL),
(88, 'Mercedes-Benz', 'Axor 1933', NULL),
(89, 'Volvo', 'FM 330', NULL),
(90, 'Scania', 'G 380', NULL),
(91, 'Volkswagen', 'Delivery 9.170', NULL),
(92, 'DAF', 'CF 85 360', NULL),
(93, 'Iveco', 'Eurocargo 170E22', NULL),
(94, 'Ford', 'Cargo 1319', NULL),
(95, 'Mercedes-Benz', 'Actros 2658', NULL),
(96, 'Volvo', 'FH 400', NULL),
(97, 'Scania', 'P 340', NULL),
(98, 'Volkswagen', 'Constellation 31.390', NULL),
(99, 'DAF', 'XF 530 6x4', NULL),
(100, 'Iveco', 'S-Way 540', NULL),
(101, 'Ford', 'Cargo 2423', NULL),
(102, 'Mercedes-Benz', 'Atego 2730', NULL),
(103, 'Volvo', 'FM 370', NULL),
(104, 'Scania', 'R 440', NULL),
(105, 'Volkswagen', 'Meteor 28.460', NULL),
(106, 'DAF', 'XF 480 6x2', NULL),
(107, 'Iveco', 'Tector 240E28 6x2', NULL),
(108, 'Ford', 'Cargo 2842 6x2', NULL),
(109, 'Mercedes-Benz', 'Actros 2548', NULL),
(110, 'Volvo', 'FH 540 6x4', NULL),
(111, 'Mercedes-Benz', 'Actros 2548', NULL),
(112, 'Volvo', 'FH 460 6x2', NULL),
(113, 'Scania', 'R 450 6x2', NULL),
(114, 'Volkswagen', 'Constellation 30.330', NULL),
(115, 'DAF', 'XF 530 6x2', NULL),
(116, 'Iveco', 'S-Way 460', NULL),
(117, 'Ford', 'Cargo 1932', NULL),
(118, 'Mercedes-Benz', 'Atego 1729', NULL),
(119, 'Volvo', 'FM 410', NULL),
(120, 'Scania', 'G 370', NULL),
(121, 'Volkswagen', 'Delivery 17.230', NULL),
(122, 'DAF', 'CF 450 6x2', NULL),
(123, 'Iveco', 'Tector 17-280 6x2', NULL),
(124, 'Ford', 'Cargo 2428', NULL),
(125, 'Mercedes-Benz', 'Axor 2036', NULL),
(126, 'Volvo', 'FH 520', NULL),
(127, 'Scania', 'R 420', NULL),
(128, 'Volkswagen', 'Constellation 26.420', NULL),
(129, 'DAF', 'XF 480 6x4', NULL),
(130, 'Iveco', 'Hi-Way 480', NULL),
(131, 'Ford', 'Cargo 1729', NULL),
(132, 'Mercedes-Benz', 'Atego 2429', NULL),
(133, 'Volvo', 'VM 330 6x2', NULL),
(134, 'Scania', 'P 320', NULL),
(135, 'Volkswagen', 'Worker 24.220', NULL),
(136, 'DAF', 'CF 360', NULL),
(137, 'Iveco', 'Tector 15-210', NULL),
(138, 'Ford', 'Cargo 2629', NULL),
(139, 'Mercedes-Benz', 'Actros 2653 6x4', NULL),
(140, 'Volvo', 'FH 500 6x4', NULL),
(141, 'Scania', 'G 410 6x2', NULL),
(142, 'Volkswagen', 'Constellation 33.460', NULL),
(143, 'DAF', 'XF 530 6x4', NULL),
(144, 'Iveco', 'S-Way 540 6x4', NULL),
(145, 'Ford', 'Cargo 2842 6x4', NULL),
(146, 'Mercedes-Benz', 'Atego 3030', NULL),
(147, 'Volvo', 'FM 500', NULL),
(148, 'Scania', 'R 500 6x4', NULL),
(149, 'Volkswagen', 'Meteor 29.520 6x4', NULL),
(150, 'DAF', 'XF 480 6x4', NULL),
(151, 'Iveco', 'Tector 24-280', NULL),
(152, 'Ford', 'Cargo 3133', NULL),
(153, 'Mercedes-Benz', 'Axor 3344', NULL),
(154, 'Volvo', 'FMX 370', NULL),
(155, 'Scania', 'G 440 6x4', NULL),
(156, 'Volkswagen', 'Constellation 32.360', NULL),
(157, 'DAF', 'CF 410 6x2', NULL),
(158, 'Iveco', 'Tector 24-300', NULL),
(159, 'Ford', 'Cargo 2629 6x2', NULL),
(160, 'Mercedes-Benz', 'Actros 2651 6x4', NULL),
(161, 'Volvo', 'FH 540 6x4', NULL),
(162, 'Scania', 'R 540 6x4', NULL),
(163, 'Volkswagen', 'Meteor 28.460 6x2', NULL),
(164, 'DAF', 'XF 530 6x4', NULL),
(165, 'Iveco', 'S-Way 480 6x2', NULL),
(166, 'Ford', 'Cargo 4532', NULL),
(167, 'Mercedes-Benz', 'Atego 1725', NULL),
(168, 'Volvo', 'VM 270 6x2', NULL),
(169, 'Scania', 'P 360 6x2', NULL),
(170, 'Volkswagen', 'Constellation 24.330', NULL),
(171, 'DAF', 'CF 480 6x2', NULL),
(172, 'Iveco', 'Hi-Way 440 6x2', NULL),
(173, 'Ford', 'Cargo 2423 6x2', NULL),
(174, 'Mercedes-Benz', 'Accelo 1316', NULL),
(175, 'Volvo', 'FH 440 6x2', NULL),
(176, 'Scania', 'G 450 6x2', NULL),
(177, 'Volkswagen', 'Delivery 13.180 6x2', NULL),
(178, 'DAF', 'XF 510 6x2', NULL),
(179, 'Iveco', 'Tector 17-280 4x2', NULL),
(180, 'Ford', 'Cargo 1719', NULL),
(181, 'Mercedes-Benz', 'Axor 2540', NULL),
(182, 'Volvo', 'FM 380 6x2', NULL),
(183, 'Scania', 'R 480 6x2', NULL),
(184, 'Volkswagen', 'Constellation 25.390', NULL),
(185, 'DAF', 'CF 440 6x2', NULL),
(186, 'Iveco', 'Daily 70C17', NULL),
(187, 'Ford', 'Cargo 1317', NULL),
(188, 'Mercedes-Benz', 'Atego 1419 4x2', NULL),
(189, 'Volvo', 'VM 330 4x2', NULL),
(190, 'Scania', 'P 310 6x2', NULL),
(191, 'Volkswagen', 'Worker 15.180', NULL),
(192, 'DAF', 'CF 85 410', NULL),
(193, 'Iveco', 'Tector 17-190', NULL),
(194, 'Ford', 'Cargo 816 4x2', NULL),
(195, 'Mercedes-Benz', 'Accelo 1016 4x2', NULL),
(196, 'Volvo', 'VM 270 4x2', NULL),
(197, 'Scania', 'P 250 4x2', NULL),
(198, 'Volkswagen', 'Delivery 9.170 4x2', NULL),
(199, 'DAF', 'CF 410 4x2', NULL),
(200, 'Iveco', 'Daily 45S17 4x2', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `carros`
--

CREATE TABLE `carros` (
  `carid` int NOT NULL,
  `carmodelo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carmarca` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carimg` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carros`
--

INSERT INTO `carros` (`carid`, `carmodelo`, `carmarca`, `carimg`) VALUES
(1, 'Civic', 'Honda', 'civic.jpg'),
(2, 'Corolla', 'Toyota', 'corolla.jpg'),
(3, 'Onix', 'Chevrolet', 'onix.jpg'),
(4, 'Gol', 'Volkswagen', 'gol.jpg'),
(5, 'HB20', 'Hyundai', 'hb20.jpg'),
(6, 'Camaro', 'Chevrolet', 'camaro.jpg'),
(7, 'Mustang', 'Ford', 'mustang.jpg'),
(8, 'Jetta', 'Volkswagen', 'jetta.jpg'),
(9, 'Creta', 'Hyundai', 'creta.jpg'),
(10, 'Compass', 'Jeep', 'compass.jpg'),
(11, 'Uno', 'Fiat', NULL),
(12, 'Strada', 'Fiat', NULL),
(13, 'Siena', 'Fiat', NULL),
(14, 'Punto', 'Fiat', NULL),
(15, 'Idea', 'Fiat', NULL),
(16, 'Bravo', 'Fiat', NULL),
(17, 'Stilo', 'Fiat', NULL),
(18, 'Grand Siena', 'Fiat', NULL),
(19, 'Fiorino', 'Fiat', NULL),
(20, 'Linea', 'Fiat', NULL),
(21, 'Tipo', 'Fiat', NULL),
(22, '500', 'Fiat', NULL),
(23, '147', 'Fiat', NULL),
(24, 'Marea', 'Fiat', NULL),
(25, 'Tempra', 'Fiat', NULL),
(26, 'Corsa', 'Chevrolet', NULL),
(27, 'Astra', 'Chevrolet', NULL),
(28, 'Vectra', 'Chevrolet', NULL),
(29, 'Meriva', 'Chevrolet', NULL),
(30, 'Zafira', 'Chevrolet', NULL),
(31, 'Montana', 'Chevrolet', NULL),
(32, 'Classic', 'Chevrolet', NULL),
(33, 'Agile', 'Chevrolet', NULL),
(34, 'Spin', 'Chevrolet', NULL),
(35, 'Cruze', 'Chevrolet', NULL),
(36, 'Prisma', 'Chevrolet', NULL),
(37, 'Joy', 'Chevrolet', NULL),
(38, 'Blazer', 'Chevrolet', NULL),
(39, 'Trailblazer', 'Chevrolet', NULL),
(40, 'Kwid', 'Renault', NULL),
(41, 'Sandero', 'Renault', NULL),
(42, 'Logan', 'Renault', NULL),
(43, 'Duster', 'Renault', NULL),
(44, 'Oroch', 'Renault', NULL),
(45, 'Clio', 'Renault', NULL),
(46, 'Symbol', 'Renault', NULL),
(47, 'Fluence', 'Renault', NULL),
(48, 'Megane', 'Renault', NULL),
(49, 'Scenic', 'Renault', NULL),
(50, 'Master', 'Renault', NULL),
(51, 'Ka', 'Ford', NULL),
(52, 'Fiesta', 'Ford', NULL),
(53, 'Focus', 'Ford', NULL),
(54, 'Fusion', 'Ford', NULL),
(55, 'EcoSport', 'Ford', NULL),
(56, 'Ranger', 'Ford', NULL),
(57, 'Courier', 'Ford', NULL),
(58, 'F-250', 'Ford', NULL),
(59, 'Maverick', 'Ford', NULL),
(60, 'Bronco', 'Ford', NULL),
(61, 'Territory', 'Ford', NULL),
(62, 'Edge', 'Ford', NULL),
(63, 'Polo', 'Volkswagen', NULL),
(64, 'Voyage', 'Volkswagen', NULL),
(65, 'Virtus', 'Volkswagen', NULL),
(66, 'T-Cross', 'Volkswagen', NULL),
(67, 'Nivus', 'Volkswagen', NULL),
(68, 'Taos', 'Volkswagen', NULL),
(69, 'Tiguan', 'Volkswagen', NULL),
(70, 'Amarok', 'Volkswagen', NULL),
(71, 'Up!', 'Volkswagen', NULL),
(72, 'Fox', 'Volkswagen', NULL),
(73, 'SpaceFox', 'Volkswagen', NULL),
(74, 'Parati', 'Volkswagen', NULL),
(75, 'Saveiro', 'Volkswagen', NULL),
(76, 'Santana', 'Volkswagen', NULL),
(77, 'Golf', 'Volkswagen', NULL),
(78, 'Passat', 'Volkswagen', NULL),
(79, 'Quantum', 'Volkswagen', NULL),
(80, '208', 'Peugeot', NULL),
(81, '2008', 'Peugeot', NULL),
(82, '3008', 'Peugeot', NULL),
(83, '308', 'Peugeot', NULL),
(84, '307', 'Peugeot', NULL),
(85, '408', 'Peugeot', NULL),
(86, '206', 'Peugeot', NULL),
(87, '207', 'Peugeot', NULL),
(88, 'Partner', 'Peugeot', NULL),
(89, 'C3', 'Citroën', NULL),
(90, 'C4 Cactus', 'Citroën', NULL),
(91, 'Aircross', 'Citroën', NULL),
(92, 'C4 Lounge', 'Citroën', NULL),
(93, 'Xsara Picasso', 'Citroën', NULL),
(94, 'HB20S', 'Hyundai', NULL),
(95, 'HB20X', 'Hyundai', NULL),
(96, 'i30', 'Hyundai', NULL),
(97, 'ix35', 'Hyundai', NULL),
(98, 'Tucson', 'Hyundai', NULL),
(99, 'Elantra', 'Hyundai', NULL),
(100, 'Azera', 'Hyundai', NULL),
(101, 'Santa Fe', 'Hyundai', NULL),
(102, 'City', 'Honda', NULL),
(103, 'Fit', 'Honda', NULL),
(104, 'HR-V', 'Honda', NULL),
(105, 'WR-V', 'Honda', NULL),
(106, 'CR-V', 'Honda', NULL),
(107, 'Accord', 'Honda', NULL),
(108, 'CR-Z', 'Honda', NULL),
(109, 'Etios', 'Toyota', NULL),
(110, 'Yaris', 'Toyota', NULL),
(111, 'Corolla Cross', 'Toyota', NULL),
(112, 'Hilux', 'Toyota', NULL),
(113, 'SW4', 'Toyota', NULL),
(114, 'RAV4', 'Toyota', NULL),
(115, 'Prius', 'Toyota', NULL),
(116, 'March', 'Nissan', NULL),
(117, 'Versa', 'Nissan', NULL),
(118, 'Kicks', 'Nissan', NULL),
(119, 'Sentra', 'Nissan', NULL),
(120, 'Frontier', 'Nissan', NULL),
(121, 'Livina', 'Nissan', NULL),
(122, 'Tiida', 'Nissan', NULL),
(123, 'Mobi', 'Fiat', NULL),
(124, 'Argo', 'Fiat', NULL),
(125, 'Cronos', 'Fiat', NULL),
(126, 'Pulse', 'Fiat', NULL),
(127, 'Fastback', 'Fiat', NULL),
(128, 'Toro', 'Fiat', NULL),
(129, 'Palio', 'Fiat', NULL),
(130, 'Renegade', 'Jeep', NULL),
(131, 'Commander', 'Jeep', NULL),
(132, 'Wrangler', 'Jeep', NULL),
(133, 'Cherokee', 'Jeep', NULL),
(134, 'Onix', 'Chevrolet', NULL),
(135, 'Onix Plus', 'Chevrolet', NULL),
(136, 'Tracker', 'Chevrolet', NULL),
(137, 'Spin', 'Chevrolet', NULL),
(138, 'Montana', 'Chevrolet', NULL),
(139, 'Cruze', 'Chevrolet', NULL),
(140, 'Argo', 'Fiat', NULL),
(141, 'Cronos', 'Fiat', NULL),
(142, 'Mobi', 'Fiat', NULL),
(143, 'Pulse', 'Fiat', NULL),
(144, 'Fastback', 'Fiat', NULL),
(145, 'Strada', 'Fiat', NULL),
(146, 'Toro', 'Fiat', NULL),
(147, 'Uno', 'Fiat', NULL),
(148, 'Palio', 'Fiat', NULL),
(149, 'Siena', 'Fiat', NULL),
(150, 'Sandero', 'Renault', NULL),
(151, 'Logan', 'Renault', NULL),
(152, 'Kwid', 'Renault', NULL),
(153, 'Duster', 'Renault', NULL),
(154, 'Captur', 'Renault', NULL),
(155, 'Oroch', 'Renault', NULL),
(156, 'HB20', 'Hyundai', NULL),
(157, 'HB20S', 'Hyundai', NULL),
(158, 'Creta', 'Hyundai', NULL),
(159, 'Tucson', 'Hyundai', NULL),
(160, 'i30', 'Hyundai', NULL),
(161, 'Civic', 'Honda', NULL),
(162, 'City', 'Honda', NULL),
(163, 'Fit', 'Honda', NULL),
(164, 'HR-V', 'Honda', NULL),
(165, 'WR-V', 'Honda', NULL),
(166, 'Corolla', 'Toyota', NULL),
(167, 'Yaris', 'Toyota', NULL),
(168, 'Yaris Sedan', 'Toyota', NULL),
(169, 'Etios', 'Toyota', NULL),
(170, 'Hilux', 'Toyota', NULL),
(171, 'SW4', 'Toyota', NULL),
(172, 'Gol', 'Volkswagen', NULL),
(173, 'Polo', 'Volkswagen', NULL),
(174, 'Virtus', 'Volkswagen', NULL),
(175, 'T-Cross', 'Volkswagen', NULL),
(176, 'Nivus', 'Volkswagen', NULL),
(177, 'Taos', 'Volkswagen', NULL),
(178, 'Saveiro', 'Volkswagen', NULL),
(179, 'Voyage', 'Volkswagen', NULL),
(180, 'Fox', 'Volkswagen', NULL),
(181, 'Jetta', 'Volkswagen', NULL),
(182, 'Ka', 'Ford', NULL),
(183, 'Ka Sedan', 'Ford', NULL),
(184, 'Fiesta', 'Ford', NULL),
(185, 'EcoSport', 'Ford', NULL),
(186, 'Ranger', 'Ford', NULL),
(187, 'Focus', 'Ford', NULL),
(188, '208', 'Peugeot', NULL),
(189, '2008', 'Peugeot', NULL),
(190, '308', 'Peugeot', NULL),
(191, 'C3', 'Citroën', NULL),
(192, 'C4 Cactus', 'Citroën', NULL),
(193, 'C4 Lounge', 'Citroën', NULL),
(194, 'City', 'Honda', NULL),
(195, 'Versa', 'Nissan', NULL),
(196, 'Kicks', 'Nissan', NULL),
(197, 'March', 'Nissan', NULL),
(198, 'Sentra', 'Nissan', NULL),
(199, 'Frontier', 'Nissan', NULL),
(200, 'Virtus', 'Volkswagen', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `cliid` int NOT NULL,
  `clinome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clicpf` varchar(14) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliendereco` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliservico` int DEFAULT NULL,
  `clitel` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliemail` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clisenha` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipocliente` enum('cliente','funcionario') COLLATE utf8mb4_unicode_ci DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clientes`
--

INSERT INTO `clientes` (`cliid`, `clinome`, `clicpf`, `cliendereco`, `cliservico`, `clitel`, `cliemail`, `clisenha`, `tipocliente`) VALUES
(9, 'Dariel', NULL, NULL, NULL, NULL, NULL, '$2y$10$VlKGd6KSR6qrtrELfSK8kelHpEN7DA56BoM5I0OZEK6PbYjwflVBa', 'funcionario'),
(10, 'admin', NULL, NULL, NULL, NULL, NULL, 'admin123', 'funcionario'),
(15, 'Anna Julia', '49154297842', 'Piraju', NULL, '14998581848', NULL, '$2y$10$SOcXs1MM4W7fVD.oBv.rC.8RNsmRUgfHsbPKPBK9aETbF.5IcGEaS', 'cliente'),
(16, 'Carlos Henrique', '12345678901', 'Rua das Flores, 100 - Piraju', NULL, '14990000001', NULL, '$2y$10$exemplo', 'cliente'),
(17, 'Mariana Oliveira', '23456789012', 'Rua São Paulo, 250 - Piraju', NULL, '14990000002', NULL, '$2y$10$exemplo', 'cliente'),
(18, 'Rafael Santos', '34567890123', 'Avenida Brasil, 500 - Piraju', NULL, '14990000003', NULL, '$2y$10$exemplo', 'cliente'),
(19, 'Juliana Costa', '45678901234', 'Rua Paraná, 80 - Piraju', NULL, '14990000004', NULL, '$2y$10$exemplo', 'cliente'),
(20, 'Lucas Almeida', '56789012345', 'Rua Minas Gerais, 120 - Piraju', NULL, '14990000005', NULL, '$2y$10$exemplo', 'cliente'),
(21, 'Fernanda Martins', '67890123456', 'Rua Bahia, 350 - Piraju', NULL, '14990000006', NULL, '$2y$10$exemplo', 'cliente'),
(22, 'Bruno Ferreira', '78901234567', 'Rua Goiás, 75 - Piraju', NULL, '14990000007', NULL, '$2y$10$exemplo', 'cliente'),
(23, 'Camila Rodrigues', '89012345678', 'Rua Paraná, 410 - Piraju', NULL, '14990000008', NULL, '$2y$10$exemplo', 'cliente'),
(24, 'Diego Souza', '90123456789', 'Rua São João, 90 - Piraju', NULL, '14990000009', NULL, '$2y$10$exemplo', 'cliente'),
(25, 'Patricia Lima', '01234567890', 'Avenida Principal, 700 - Piraju', NULL, '14990000010', NULL, '$2y$10$exemplo', 'cliente'),
(27, 'Guilherme Soares', '49467262843', 'Piraju', NULL, '(14) 99617-5617', NULL, '$2y$10$XH058DYovCjU5UX35XDUQOXedKUFGN2RU5leI554VOYa7Gh.rjCny', 'cliente'),
(28, 'Teste Login', '11122233344', 'Rua Teste 123', NULL, '14999999999', 'teste@teste.com', '$2y$10$UY3fR/8jF9t7y0fH6lZJ8uYqX5z4F8kJ5nG4rR0zX5pW2vB7dQ7u', 'cliente');

-- --------------------------------------------------------

--
-- Table structure for table `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int NOT NULL,
  `chave` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `valor` text COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('texto','numero','imagem','json') COLLATE utf8mb4_general_ci DEFAULT 'texto',
  `descricao` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `atualizado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `configuracoes`
--

INSERT INTO `configuracoes` (`id`, `chave`, `valor`, `tipo`, `descricao`, `atualizado_em`) VALUES
(1, 'preco_carro', '100', 'numero', 'Preço base carro', '2026-09-24 18:12:06'),
(2, 'preco_moto', '1000', 'numero', 'Preço base moto', '2026-09-22 19:14:02'),
(3, 'preco_caminhao', '2500', 'numero', 'Preço base caminhão', '2026-09-22 18:12:00'),
(4, 'preco_aquatico', '1800', 'numero', 'Preço base aquático', '2026-09-22 18:12:00'),
(5, 'preco_mobilia', '300', 'numero', 'Preço base mobília', '2026-09-22 18:12:00'),
(6, 'acabamento_brilhante', '1.15', 'numero', 'Fator Brilhante', '2026-09-22 18:12:00'),
(7, 'acabamento_perolizado', '1.30', 'numero', 'Fator Perolizado', '2026-09-22 18:12:00'),
(8, 'acabamento_texturizado', '1.50', 'numero', 'Fator Texturizado', '2026-09-24 19:05:24');

-- --------------------------------------------------------

--
-- Table structure for table `faturamento_mensal`
--

CREATE TABLE `faturamento_mensal` (
  `id` int NOT NULL,
  `ano` int NOT NULL,
  `mes` int NOT NULL,
  `total_servicos` int DEFAULT '0',
  `faturamento_total` decimal(10,2) DEFAULT '0.00',
  `faturamento_aprovado` decimal(10,2) DEFAULT '0.00',
  `fechado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `galeria`
--

CREATE TABLE `galeria` (
  `id` int NOT NULL,
  `titulo` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `descricao` text COLLATE utf8mb4_general_ci,
  `tipo` enum('carro','moto','caminhao','aquatico','mobilia') COLLATE utf8mb4_general_ci NOT NULL,
  `imagem` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `horarios_ocupados`
--

CREATE TABLE `horarios_ocupados` (
  `id` int NOT NULL,
  `data` date NOT NULL,
  `horario` varchar(10) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `horarios_ocupados`
--

INSERT INTO `horarios_ocupados` (`id`, `data`, `horario`) VALUES
(1, '2026-09-03', '08:00'),
(2, '2026-09-03', '09:00'),
(3, '2026-09-03', '10:00'),
(4, '2026-09-03', '11:00'),
(5, '2026-09-04', '08:00'),
(6, '2026-09-04', '09:00'),
(7, '2026-09-04', '10:00'),
(8, '2026-09-04', '14:00'),
(9, '2026-09-05', '08:00'),
(10, '2026-09-05', '09:00'),
(11, '2026-09-05', '13:00'),
(12, '2026-09-05', '14:00'),
(13, '2026-09-06', '09:00'),
(14, '2026-09-06', '10:00'),
(15, '2026-09-06', '14:00'),
(16, '2026-09-07', '08:00'),
(17, '2026-09-07', '10:00'),
(18, '2026-09-07', '11:00'),
(19, '2026-09-08', '09:00'),
(20, '2026-09-08', '14:00');

-- --------------------------------------------------------

--
-- Table structure for table `mobilia`
--

CREATE TABLE `mobilia` (
  `mobid` int NOT NULL,
  `mobmedida` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fachada` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mobilia`
--

INSERT INTO `mobilia` (`mobid`, `mobmedida`, `fachada`) VALUES
(1, 'Mesa 1,80m x 0,90m', NULL),
(2, 'Mesa 2,00m x 1,00m', NULL),
(3, 'Armário 2,00m x 0,80m', NULL),
(4, 'Guarda-roupa 2,20m x 0,60m', NULL),
(5, 'Sofá 2,50m x 1,00m', NULL),
(6, 'Sofá 3,00m x 1,00m', NULL),
(7, 'Cama casal 1,88m x 1,38m', NULL),
(8, 'Cama queen 1,98m x 1,58m', NULL),
(9, 'Estante 1,80m x 0,40m', NULL),
(10, 'Escrivaninha 1,40m x 0,60m', NULL),
(11, 'Mesa de jantar - 1,60m x 0,90m', NULL),
(12, 'Mesa de jantar - 2,00m x 1,00m', NULL),
(13, 'Mesa de jantar redonda - 1,20m diâmetro', NULL),
(14, 'Mesa de jantar redonda - 1,50m diâmetro', NULL),
(15, 'Mesa de jantar oval - 1,80m x 1,00m', NULL),
(16, 'Mesa de escritório - 1,20m x 0,60m', NULL),
(17, 'Mesa de escritório - 1,60m x 0,70m', NULL),
(18, 'Mesa executiva - 1,80m x 0,80m', NULL),
(19, 'Mesa de reunião - 2,40m x 1,10m', NULL),
(20, 'Mesa de reunião - 3,00m x 1,20m', NULL),
(21, 'Escrivaninha - 1,00m x 0,50m', NULL),
(22, 'Escrivaninha com gavetas - 1,40m x 0,60m', NULL),
(23, 'Escrivaninha em L - 1,60m x 1,40m', NULL),
(24, 'Escrivaninha em L - 1,80m x 1,60m', NULL),
(25, 'Mesa gamer - 1,20m x 0,60m', NULL),
(26, 'Mesa gamer - 1,50m x 0,70m', NULL),
(27, 'Mesa gamer em L - 1,60m x 1,40m', NULL),
(28, 'Mesa infantil - 0,90m x 0,50m', NULL),
(29, 'Mesa dobrável - 1,20m x 0,60m', NULL),
(30, 'Mesa lateral - 0,60m x 0,50m', NULL),
(31, 'Mesa de centro - 1,00m x 0,60m', NULL),
(32, 'Mesa de centro - 1,20m x 0,70m', NULL),
(33, 'Mesa de centro redonda - 0,80m diâmetro', NULL),
(34, 'Mesa de canto - 0,60m x 0,60m', NULL),
(35, 'Mesa de cabeceira - 0,50m x 0,40m', NULL),
(36, 'Mesa de cabeceira suspensa - 0,50m x 0,35m', NULL),
(37, 'Aparador - 1,20m x 0,40m', NULL),
(38, 'Aparador - 1,80m x 0,45m', NULL),
(39, 'Aparador com gavetas - 2,00m x 0,50m', NULL),
(40, 'Buffet - 1,80m x 0,50m', NULL),
(41, 'Rack para TV - 1,60m x 0,40m', NULL),
(42, 'Rack para TV - 2,00m x 0,45m', NULL),
(43, 'Rack suspenso - 1,80m x 0,35m', NULL),
(44, 'Rack com painel - 2,20m x 0,45m', NULL),
(45, 'Painel para TV - 1,80m x 0,20m', NULL),
(46, 'Painel para TV - 2,40m x 0,25m', NULL),
(47, 'Painel ripado - 2,50m x 0,30m', NULL),
(48, 'Painel decorativo - 3,00m x 0,25m', NULL),
(49, 'Estante para livros - 0,80m x 1,80m', NULL),
(50, 'Estante modular - 1,20m x 2,00m', NULL),
(51, 'Estante industrial - 1,50m x 2,00m', NULL),
(52, 'Estante de madeira - 1,80m x 2,20m', NULL),
(53, 'Estante de canto - 0,80m x 1,80m', NULL),
(54, 'Estante para plantas - 0,70m x 1,60m', NULL),
(55, 'Nicho de parede - 0,60m x 0,30m', NULL),
(56, 'Nicho decorativo - 0,80m x 0,30m', NULL),
(57, 'Nicho para livros - 1,00m x 0,30m', NULL),
(58, 'Prateleira de parede - 1,20m x 0,25m', NULL),
(59, 'Prateleira de parede - 1,80m x 0,30m', NULL),
(60, 'Prateleira reforçada - 2,00m x 0,40m', NULL),
(61, 'Sofá 2 lugares - 1,60m x 0,85m', NULL),
(62, 'Sofá 3 lugares - 2,10m x 0,90m', NULL),
(63, 'Sofá 4 lugares - 2,60m x 0,95m', NULL),
(64, 'Sofá retrátil - 2,20m x 1,00m', NULL),
(65, 'Sofá retrátil - 2,80m x 1,05m', NULL),
(66, 'Sofá reclinável - 2,40m x 1,00m', NULL),
(67, 'Sofá em L - 2,50m x 1,80m', NULL),
(68, 'Sofá em L - 3,00m x 2,00m', NULL),
(69, 'Poltrona - 0,80m x 0,80m', NULL),
(70, 'Poltrona reclinável - 0,90m x 0,90m', NULL),
(71, 'Puff quadrado - 0,50m x 0,50m', NULL),
(72, 'Puff redondo - 0,60m diâmetro', NULL),
(73, 'Banqueta - 0,40m x 0,40m', NULL),
(74, 'Banqueta alta - 0,45m x 0,45m', NULL),
(75, 'Banco de madeira - 1,20m x 0,40m', NULL),
(76, 'Banco de jardim - 1,50m x 0,50m', NULL),
(77, 'Banco baú - 1,20m x 0,45m', NULL),
(78, 'Chaise longue - 1,60m x 0,75m', NULL),
(79, 'Recamier - 1,40m x 0,55m', NULL),
(80, 'Divã - 1,80m x 0,75m', NULL),
(81, 'Guarda-roupa 2 portas - 1,20m x 0,55m', NULL),
(82, 'Guarda-roupa 3 portas - 1,80m x 0,60m', NULL),
(83, 'Guarda-roupa 4 portas - 2,40m x 0,65m', NULL),
(84, 'Guarda-roupa 6 portas - 2,80m x 0,70m', NULL),
(85, 'Guarda-roupa com espelho - 2,00m x 0,60m', NULL),
(86, 'Guarda-roupa planejado - 3,00m x 0,65m', NULL),
(87, 'Closet modulado - 2,40m x 2,00m', NULL),
(88, 'Closet planejado - 3,00m x 2,50m', NULL),
(89, 'Cômoda 4 gavetas - 0,90m x 0,45m', NULL),
(90, 'Cômoda 6 gavetas - 1,40m x 0,50m', NULL),
(91, 'Criado-mudo - 0,50m x 0,40m', NULL),
(92, 'Criado-mudo com gaveta - 0,60m x 0,40m', NULL),
(93, 'Criado-mudo suspenso - 0,50m x 0,35m', NULL),
(94, 'Penteadeira - 1,00m x 0,45m', NULL),
(95, 'Penteadeira com espelho - 1,20m x 0,50m', NULL),
(96, 'Penteadeira camarim - 1,40m x 0,55m', NULL),
(97, 'Sapateira vertical - 0,70m x 1,80m', NULL),
(98, 'Sapateira horizontal - 1,20m x 0,40m', NULL),
(99, 'Baú para roupas - 1,00m x 0,50m', NULL),
(100, 'Baú decorativo - 1,20m x 0,60m', NULL),
(101, 'Beliche infantil - 1,90m x 0,90m', NULL),
(102, 'Beliche adulto - 2,00m x 1,00m', NULL),
(103, 'Cama de solteiro - 1,90m x 0,90m', NULL),
(104, 'Cama de solteiro com gavetas - 1,90m x 0,90m', NULL),
(105, 'Cama de casal - 1,90m x 1,40m', NULL),
(106, 'Cama queen - 1,98m x 1,58m', NULL),
(107, 'Cama king - 1,98m x 1,93m', NULL),
(108, 'Cama box solteiro - 1,88m x 0,88m', NULL),
(109, 'Cama box casal - 1,88m x 1,38m', NULL),
(110, 'Cama com baú - 1,98m x 1,58m', NULL),
(111, 'Armário de cozinha - 1,20m x 0,40m', NULL),
(112, 'Armário aéreo - 1,50m x 0,35m', NULL),
(113, 'Armário aéreo - 2,00m x 0,35m', NULL),
(114, 'Armário de cozinha inferior - 1,80m x 0,60m', NULL),
(115, 'Armário de cozinha planejado - 2,40m x 0,60m', NULL),
(116, 'Torre quente - 0,70m x 0,60m', NULL),
(117, 'Paneleiro - 0,60m x 0,55m', NULL),
(118, 'Cristaleira - 1,00m x 0,45m', NULL),
(119, 'Cristaleira com vidro - 1,20m x 0,50m', NULL),
(120, 'Despenseiro - 0,60m x 0,50m', NULL),
(121, 'Balcão de cozinha - 1,50m x 0,60m', NULL),
(122, 'Balcão de cozinha - 2,00m x 0,60m', NULL),
(123, 'Balcão de cozinha em L - 2,40m x 1,80m', NULL),
(124, 'Balcão americano - 1,80m x 0,45m', NULL),
(125, 'Bancada de cozinha - 2,00m x 0,65m', NULL),
(126, 'Bancada de cozinha - 2,50m x 0,65m', NULL),
(127, 'Ilha de cozinha - 1,80m x 0,90m', NULL),
(128, 'Ilha de cozinha - 2,40m x 1,00m', NULL),
(129, 'Carrinho de cozinha - 0,80m x 0,50m', NULL),
(130, 'Fruteira de cozinha - 0,50m x 0,40m', NULL),
(131, 'Armário de banheiro - 0,60m x 0,40m', NULL),
(132, 'Gabinete de banheiro - 0,80m x 0,45m', NULL),
(133, 'Gabinete de banheiro - 1,00m x 0,50m', NULL),
(134, 'Gabinete com espelho - 1,20m x 0,50m', NULL),
(135, 'Bancada de banheiro - 1,00m x 0,55m', NULL),
(136, 'Bancada de banheiro - 1,50m x 0,60m', NULL),
(137, 'Nicho para banheiro - 0,60m x 0,30m', NULL),
(138, 'Armário para lavanderia - 1,20m x 0,50m', NULL),
(139, 'Armário multiuso - 1,80m x 0,50m', NULL),
(140, 'Tábua de passar com armário - 1,20m x 0,45m', NULL),
(141, 'Mesa de atendimento - 1,20m x 0,60m', NULL),
(142, 'Balcão de atendimento - 1,80m x 0,70m', NULL),
(143, 'Balcão de recepção - 2,00m x 0,80m', NULL),
(144, 'Balcão de recepção em L - 2,40m x 1,60m', NULL),
(145, 'Mesa para consultório - 1,40m x 0,70m', NULL),
(146, 'Mesa para sala de reunião - 2,00m x 1,00m', NULL),
(147, 'Armário de escritório - 0,90m x 0,45m', NULL),
(148, 'Arquivo de escritório - 0,50m x 0,60m', NULL),
(149, 'Gaveteiro - 0,45m x 0,50m', NULL),
(150, 'Gaveteiro volante - 0,40m x 0,50m', NULL),
(151, 'Estação de trabalho - 1,20m x 0,60m', NULL),
(152, 'Estação de trabalho dupla - 2,40m x 0,60m', NULL),
(153, 'Estação de trabalho tripla - 3,60m x 0,60m', NULL),
(154, 'Mesa para computador - 1,20m x 0,60m', NULL),
(155, 'Mesa para notebook - 0,90m x 0,50m', NULL),
(156, 'Bancada de trabalho - 2,00m x 0,70m', NULL),
(157, 'Bancada industrial - 2,50m x 0,80m', NULL),
(158, 'Armário industrial - 1,80m x 0,60m', NULL),
(159, 'Estante industrial - 2,00m x 0,60m', NULL),
(160, 'Prateleira comercial - 1,50m x 0,40m', NULL),
(161, 'Vitrine de loja - 1,20m x 0,50m', NULL),
(162, 'Vitrine de loja - 1,80m x 0,50m', NULL),
(163, 'Vitrine de vidro - 2,00m x 0,60m', NULL),
(164, 'Balcão expositor - 1,50m x 0,60m', NULL),
(165, 'Balcão caixa - 1,80m x 0,70m', NULL),
(166, 'Arara de roupas - 1,20m x 0,50m', NULL),
(167, 'Arara de roupas dupla - 1,80m x 0,60m', NULL),
(168, 'Expositor de produtos - 1,00m x 0,40m', NULL),
(169, 'Gôndola comercial - 1,80m x 0,80m', NULL),
(170, 'Estante para estoque - 2,00m x 0,60m', NULL),
(171, 'Mesa para varanda - 1,20m x 0,70m', NULL),
(172, 'Mesa para jardim - 1,50m x 0,80m', NULL),
(173, 'Mesa de área externa - 1,80m x 0,90m', NULL),
(174, 'Banco para varanda - 1,20m x 0,45m', NULL),
(175, 'Banco para jardim - 1,80m x 0,50m', NULL),
(176, 'Espreguiçadeira - 2,00m x 0,70m', NULL),
(177, 'Baú externo - 1,20m x 0,60m', NULL),
(178, 'Armário externo - 1,80m x 0,70m', NULL),
(179, 'Bancada para churrasqueira - 2,00m x 0,70m', NULL),
(180, 'Armário para churrasqueira - 1,50m x 0,60m', NULL),
(181, 'Mesa de desenho - 1,20m x 0,80m', NULL),
(182, 'Mesa de artesanato - 1,50m x 0,70m', NULL),
(183, 'Bancada para ferramentas - 2,00m x 0,70m', NULL),
(184, 'Bancada de oficina - 2,50m x 0,80m', NULL),
(185, 'Armário para ferramentas - 1,80m x 0,60m', NULL),
(186, 'Painel para ferramentas - 2,00m x 0,30m', NULL),
(187, 'Estante para garagem - 1,80m x 0,50m', NULL),
(188, 'Armário para garagem - 2,00m x 0,60m', NULL),
(189, 'Sapateira para entrada - 1,20m x 0,40m', NULL),
(190, 'Banco para entrada - 1,00m x 0,40m', NULL),
(191, 'Escrivaninha infantil - 0,90m x 0,50m', NULL),
(192, 'Mesa infantil - 1,00m x 0,50m', NULL),
(193, 'Baú infantil para brinquedos - 0,90m x 0,50m', NULL),
(194, 'Estante infantil - 0,80m x 1,20m', NULL),
(195, 'Organizador de brinquedos - 1,00m x 0,40m', NULL),
(196, 'Armário infantil - 1,20m x 0,50m', NULL),
(197, 'Cômoda infantil - 0,90m x 0,45m', NULL),
(198, 'Nicho infantil - 0,60m x 0,30m', NULL),
(199, 'Mesa para estudos - 1,20m x 0,60m', NULL),
(200, 'Estante para estudos - 0,90m x 1,80m', NULL),
(201, 'Púlpito de madeira - 0,70m x 0,50m', NULL),
(202, 'Balcão para eventos - 1,80m x 0,60m', NULL),
(203, 'Mesa para buffet - 1,80m x 0,70m', NULL),
(204, 'Mesa para festas - 2,00m x 0,80m', NULL),
(205, 'Carrinho auxiliar - 0,80m x 0,50m', NULL),
(206, 'Carrinho bar - 1,00m x 0,50m', NULL),
(207, 'Armário para documentos - 1,80m x 0,50m', NULL),
(208, 'Arquivo com gavetas - 0,80m x 0,50m', NULL),
(209, 'Armário multiuso alto - 2,00m x 0,50m', NULL),
(210, 'Armário multiuso baixo - 1,20m x 0,50m', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `motos`
--

CREATE TABLE `motos` (
  `motid` int NOT NULL,
  `motmarca` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motmodelo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motimg` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `motos`
--

INSERT INTO `motos` (`motid`, `motmarca`, `motmodelo`, `motimg`) VALUES
(1, 'Honda', 'CG 160 Titan', 'cg160.jpg'),
(2, 'Honda', 'CB 500F', 'cb500f.jpg'),
(3, 'Yamaha', 'MT-03', 'mt03.jpg'),
(4, 'Yamaha', 'Fazer 250', 'fazer250.jpg'),
(5, 'Honda', 'Biz 125', 'biz125.jpg'),
(6, 'Yamaha', 'Lander 250', 'lander.jpg'),
(7, 'Kawasaki', 'Ninja 400', 'ninja400.jpg'),
(8, 'BMW', 'F 850 GS', 'f850gs.jpg'),
(9, 'Harley-Davidson', 'Iron 883', 'iron883.jpg'),
(10, 'Suzuki', 'GSX-S750', 'gsxs750.jpg'),
(11, 'Honda', 'CG 160 Fan', NULL),
(12, 'Honda', 'CG 160 Titan', NULL),
(13, 'Honda', 'CG 160 Start', NULL),
(14, 'Honda', 'CG 160 Cargo', NULL),
(15, 'Honda', 'Biz 110i', NULL),
(16, 'Honda', 'Biz 125', NULL),
(17, 'Honda', 'Pop 110i', NULL),
(18, 'Honda', 'Elite 125', NULL),
(19, 'Honda', 'PCX 160', NULL),
(20, 'Honda', 'ADV 160', NULL),
(21, 'Honda', 'NXR 160 Bros', NULL),
(22, 'Honda', 'XRE 190', NULL),
(23, 'Honda', 'XRE 300', NULL),
(24, 'Honda', 'CB 300F Twister', NULL),
(25, 'Honda', 'CB 500F', NULL),
(26, 'Honda', 'CB 500X', NULL),
(27, 'Honda', 'CB 650R', NULL),
(28, 'Honda', 'CBR 650R', NULL),
(29, 'Honda', 'CB 1000R', NULL),
(30, 'Honda', 'Africa Twin', NULL),
(31, 'Yamaha', 'Factor 125i', NULL),
(32, 'Yamaha', 'Factor 150', NULL),
(33, 'Yamaha', 'Fazer FZ15', NULL),
(34, 'Yamaha', 'Fazer FZ25', NULL),
(35, 'Yamaha', 'Fazer 250', NULL),
(36, 'Yamaha', 'Fazer 600', NULL),
(37, 'Yamaha', 'MT-03', NULL),
(38, 'Yamaha', 'MT-07', NULL),
(39, 'Yamaha', 'MT-09', NULL),
(40, 'Yamaha', 'MT-10', NULL),
(41, 'Yamaha', 'R3', NULL),
(42, 'Yamaha', 'R7', NULL),
(43, 'Yamaha', 'R1', NULL),
(44, 'Yamaha', 'Lander 250', NULL),
(45, 'Yamaha', 'Crosser 150', NULL),
(46, 'Yamaha', 'XTZ 125', NULL),
(47, 'Yamaha', 'NMAX 160', NULL),
(48, 'Yamaha', 'XMAX 250', NULL),
(49, 'Yamaha', 'Ténéré 700', NULL),
(50, 'Yamaha', 'Super Ténéré 1200', NULL),
(51, 'Suzuki', 'Yes 125', NULL),
(52, 'Suzuki', 'Intruder 125', NULL),
(53, 'Suzuki', 'Burgman 125', NULL),
(54, 'Suzuki', 'Burgman 400', NULL),
(55, 'Suzuki', 'GSX-S 750', NULL),
(56, 'Suzuki', 'GSX-S 1000', NULL),
(57, 'Suzuki', 'GSX-R 750', NULL),
(58, 'Suzuki', 'GSX-R 1000', NULL),
(59, 'Suzuki', 'V-Strom 650', NULL),
(60, 'Suzuki', 'V-Strom 1050', NULL),
(61, 'Suzuki', 'Hayabusa', NULL),
(62, 'Suzuki', 'Bandit 650', NULL),
(63, 'Suzuki', 'Bandit 1250', NULL),
(64, 'Suzuki', 'Boulevard M800', NULL),
(65, 'Suzuki', 'Boulevard M1500', NULL),
(66, 'Kawasaki', 'Ninja 300', NULL),
(67, 'Kawasaki', 'Ninja 400', NULL),
(68, 'Kawasaki', 'Ninja 650', NULL),
(69, 'Kawasaki', 'Ninja ZX-6R', NULL),
(70, 'Kawasaki', 'Ninja ZX-10R', NULL),
(71, 'Kawasaki', 'Ninja H2', NULL),
(72, 'Kawasaki', 'Z300', NULL),
(73, 'Kawasaki', 'Z400', NULL),
(74, 'Kawasaki', 'Z650', NULL),
(75, 'Kawasaki', 'Z900', NULL),
(76, 'Kawasaki', 'Z1000', NULL),
(77, 'Kawasaki', 'Versys-X 300', NULL),
(78, 'Kawasaki', 'Versys 650', NULL),
(79, 'Kawasaki', 'Versys 1000', NULL),
(80, 'Kawasaki', 'Vulcan S', NULL),
(81, 'BMW', 'G 310 R', NULL),
(82, 'BMW', 'G 310 GS', NULL),
(83, 'BMW', 'F 750 GS', NULL),
(84, 'BMW', 'F 850 GS', NULL),
(85, 'BMW', 'F 900 R', NULL),
(86, 'BMW', 'F 900 XR', NULL),
(87, 'BMW', 'R 1250 GS', NULL),
(88, 'BMW', 'R 1250 RT', NULL),
(89, 'BMW', 'S 1000 R', NULL),
(90, 'BMW', 'S 1000 RR', NULL),
(91, 'BMW', 'S 1000 XR', NULL),
(92, 'BMW', 'R 18', NULL),
(93, 'Ducati', 'Monster 937', NULL),
(94, 'Ducati', 'Monster 1200', NULL),
(95, 'Ducati', 'Panigale V2', NULL),
(96, 'Ducati', 'Panigale V4', NULL),
(97, 'Ducati', 'Streetfighter V2', NULL),
(98, 'Ducati', 'Streetfighter V4', NULL),
(99, 'Ducati', 'Multistrada V2', NULL),
(100, 'Ducati', 'Multistrada V4', NULL),
(101, 'Ducati', 'Diavel 1260', NULL),
(102, 'Ducati', 'Diavel V4', NULL),
(103, 'Triumph', 'Speed 400', NULL),
(104, 'Triumph', 'Scrambler 400 X', NULL),
(105, 'Triumph', 'Trident 660', NULL),
(106, 'Triumph', 'Street Triple 765', NULL),
(107, 'Triumph', 'Speed Triple 1200', NULL),
(108, 'Triumph', 'Tiger Sport 660', NULL),
(109, 'Triumph', 'Tiger 900', NULL),
(110, 'Triumph', 'Tiger 1200', NULL),
(111, 'Triumph', 'Bonneville T100', NULL),
(112, 'Triumph', 'Bonneville T120', NULL),
(113, 'Royal Enfield', 'Hunter 350', NULL),
(114, 'Royal Enfield', 'Meteor 350', NULL),
(115, 'Royal Enfield', 'Classic 350', NULL),
(116, 'Royal Enfield', 'Bullet 350', NULL),
(117, 'Royal Enfield', 'Himalayan 411', NULL),
(118, 'Royal Enfield', 'Himalayan 450', NULL),
(119, 'Royal Enfield', 'Interceptor 650', NULL),
(120, 'Royal Enfield', 'Continental GT 650', NULL),
(121, 'Royal Enfield', 'Super Meteor 650', NULL),
(122, 'Royal Enfield', 'Shotgun 650', NULL),
(123, 'KTM', 'Duke 200', NULL),
(124, 'KTM', 'Duke 390', NULL),
(125, 'KTM', 'Duke 790', NULL),
(126, 'KTM', 'Duke 890', NULL),
(127, 'KTM', 'Duke 1290', NULL),
(128, 'KTM', 'RC 200', NULL),
(129, 'KTM', 'RC 390', NULL),
(130, 'KTM', '390 Adventure', NULL),
(131, 'KTM', '790 Adventure', NULL),
(132, 'KTM', '1290 Super Adventure', NULL),
(133, 'Harley-Davidson', 'Iron 883', NULL),
(134, 'Harley-Davidson', 'Sportster S', NULL),
(135, 'Harley-Davidson', 'Nightster', NULL),
(136, 'Harley-Davidson', 'Street Bob', NULL),
(137, 'Harley-Davidson', 'Fat Bob', NULL),
(138, 'Harley-Davidson', 'Fat Boy', NULL),
(139, 'Harley-Davidson', 'Low Rider S', NULL),
(140, 'Harley-Davidson', 'Low Rider ST', NULL),
(141, 'Harley-Davidson', 'Road King', NULL),
(142, 'Harley-Davidson', 'Street Glide', NULL),
(143, 'Shineray', 'Worker 125', NULL),
(144, 'Shineray', 'Jet 50', NULL),
(145, 'Shineray', 'Jet 125', NULL),
(146, 'Shineray', 'Phoenix 50', NULL),
(147, 'Shineray', 'Urban 150', NULL),
(148, 'Shineray', 'SHI 175', NULL),
(149, 'Shineray', 'Free 150', NULL),
(150, 'Shineray', 'SE 175', NULL),
(151, 'Shineray', 'XY 250', NULL),
(152, 'Shineray', 'Discover 250', NULL),
(153, 'Haojue', 'DK 160', NULL),
(154, 'Haojue', 'DK 150', NULL),
(155, 'Haojue', 'DR 160', NULL),
(156, 'Haojue', 'NK 150', NULL),
(157, 'Haojue', 'Master Ride 150', NULL),
(158, 'Haojue', 'Chopper Road 150', NULL),
(159, 'Haojue', 'Lindy 125', NULL),
(160, 'Haojue', 'VR 150', NULL),
(161, 'Haojue', 'DL 160', NULL),
(162, 'Haojue', 'DR 300', NULL),
(163, 'Dafra', 'Apache RTR 150', NULL),
(164, 'Dafra', 'Apache RTR 200', NULL),
(165, 'Dafra', 'Cruisym 150', NULL),
(166, 'Dafra', 'Cruisym 300', NULL),
(167, 'Dafra', 'Citycom 300i', NULL),
(168, 'Dafra', 'NH 190', NULL),
(169, 'Dafra', 'NH 300', NULL),
(170, 'Dafra', 'Horizon 150', NULL),
(171, 'Dafra', 'Horizon 300', NULL),
(172, 'Dafra', 'Maxsym 400', NULL),
(173, 'Bajaj', 'Dominar 160', NULL),
(174, 'Bajaj', 'Dominar 200', NULL),
(175, 'Bajaj', 'Dominar 250', NULL),
(176, 'Bajaj', 'Dominar 400', NULL),
(177, 'Bajaj', 'Pulsar N160', NULL),
(178, 'Bajaj', 'Pulsar N250', NULL),
(179, 'Bajaj', 'Pulsar NS200', NULL),
(180, 'Bajaj', 'Pulsar NS400Z', NULL),
(181, 'Bajaj', 'Avenger 160', NULL),
(182, 'Bajaj', 'Avenger 220', NULL),
(183, 'MV Agusta', 'Brutale 800', NULL),
(184, 'MV Agusta', 'Dragster 800', NULL),
(185, 'MV Agusta', 'F3 800', NULL),
(186, 'MV Agusta', 'Turismo Veloce 800', NULL),
(187, 'MV Agusta', 'Rush 1000', NULL),
(188, 'Aprilia', 'RS 660', NULL),
(189, 'Aprilia', 'Tuono 660', NULL),
(190, 'Aprilia', 'RSV4', NULL),
(191, 'Aprilia', 'Tuono V4', NULL),
(192, 'Aprilia', 'Tuareg 660', NULL),
(193, 'Indian', 'Scout', NULL),
(194, 'Indian', 'Scout Bobber', NULL),
(195, 'Indian', 'Chief', NULL),
(196, 'Indian', 'Chief Bobber', NULL),
(197, 'Indian', 'Chieftain', NULL),
(198, 'Moto Guzzi', 'V7 Stone', NULL),
(199, 'Moto Guzzi', 'V85 TT', NULL),
(200, 'Moto Guzzi', 'V100 Mandello', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `nauticos`
--

CREATE TABLE `nauticos` (
  `nauid` int NOT NULL,
  `naumodelo` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `naumarca` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nauimg` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nauticos`
--

INSERT INTO `nauticos` (`nauid`, `naumodelo`, `naumarca`, `nauimg`) VALUES
(1, 'FS 230', 'Focker', 'focker230.jpg'),
(2, '250 WA', 'Schaefer', 'schaefer250.jpg'),
(3, 'Real 24', 'Real Powerboats', 'real24.jpg'),
(4, 'NX 290', 'NX Boats', 'nx290.jpg'),
(5, 'Fishing 19', 'Fishing Raptor', 'fishing19.jpg'),
(6, 'Runner 330', 'Runner', 'runner330.jpg'),
(7, 'Phantom 303', 'Schaefer', 'phantom303.jpg'),
(8, 'Focker 240', 'Focker', 'focker240.jpg'),
(9, 'NX 260', 'NX Boats', 'nx260.jpg'),
(10, 'Real 29', 'Real Powerboats', 'real29.jpg'),
(11, 'FS 160', 'Focker', NULL),
(12, 'FS 180', 'Focker', NULL),
(13, 'FS 210', 'Focker', NULL),
(14, 'FS 230', 'Focker', NULL),
(15, 'FS 240', 'Focker', NULL),
(16, 'FS 250', 'Focker', NULL),
(17, 'FS 290', 'Focker', NULL),
(18, 'FS 310', 'Focker', NULL),
(19, 'FS 330', 'Focker', NULL),
(20, 'FS 360', 'Focker', NULL),
(21, 'Focker 190', 'Fibrafort', NULL),
(22, 'Focker 240', 'Fibrafort', NULL),
(23, 'Focker 265', 'Fibrafort', NULL),
(24, 'Focker 330', 'Fibrafort', NULL),
(25, 'Focker 377', 'Fibrafort', NULL),
(26, 'Focker 420', 'Fibrafort', NULL),
(27, 'Focker 440', 'Fibrafort', NULL),
(28, 'Focker 500', 'Fibrafort', NULL),
(29, 'Focker 555', 'Fibrafort', NULL),
(30, 'Focker 600', 'Fibrafort', NULL),
(31, 'FS 160 Open', 'Fishing', NULL),
(32, 'FS 180 Open', 'Fishing', NULL),
(33, 'FS 200 Open', 'Fishing', NULL),
(34, 'FS 220 Open', 'Fishing', NULL),
(35, 'FS 250 Open', 'Fishing', NULL),
(36, 'FS 280 Open', 'Fishing', NULL),
(37, 'FS 300 Open', 'Fishing', NULL),
(38, 'FS 320 Open', 'Fishing', NULL),
(39, 'FS 350 Open', 'Fishing', NULL),
(40, 'FS 380 Open', 'Fishing', NULL),
(41, 'Phantom 235', 'Schaefer', NULL),
(42, 'Phantom 265', 'Schaefer', NULL),
(43, 'Phantom 290', 'Schaefer', NULL),
(44, 'Phantom 303', 'Schaefer', NULL),
(45, 'Phantom 345', 'Schaefer', NULL),
(46, 'Phantom 375', 'Schaefer', NULL),
(47, 'Phantom 400', 'Schaefer', NULL),
(48, 'Phantom 450', 'Schaefer', NULL),
(49, 'Phantom 500', 'Schaefer', NULL),
(50, 'Phantom 640', 'Schaefer', NULL),
(51, 'Fishing 175', 'Quest', NULL),
(52, 'Fishing 190', 'Quest', NULL),
(53, 'Fishing 210', 'Quest', NULL),
(54, 'Fishing 230', 'Quest', NULL),
(55, 'Fishing 250', 'Quest', NULL),
(56, 'Fishing 270', 'Quest', NULL),
(57, 'Fishing 290', 'Quest', NULL),
(58, 'Fishing 310', 'Quest', NULL),
(59, 'Fishing 350', 'Quest', NULL),
(60, 'Fishing 380', 'Quest', NULL),
(61, 'Cuddy 180', 'Runner', NULL),
(62, 'Cuddy 220', 'Runner', NULL),
(63, 'Cuddy 240', 'Runner', NULL),
(64, 'Cuddy 260', 'Runner', NULL),
(65, 'Cuddy 280', 'Runner', NULL),
(66, 'Cuddy 300', 'Runner', NULL),
(67, 'Cuddy 330', 'Runner', NULL),
(68, 'Cuddy 350', 'Runner', NULL),
(69, 'Cuddy 380', 'Runner', NULL),
(70, 'Cuddy 420', 'Runner', NULL),
(71, 'Open 160', 'Ventura', NULL),
(72, 'Open 180', 'Ventura', NULL),
(73, 'Open 200', 'Ventura', NULL),
(74, 'Open 220', 'Ventura', NULL),
(75, 'Open 240', 'Ventura', NULL),
(76, 'Open 260', 'Ventura', NULL),
(77, 'Open 280', 'Ventura', NULL),
(78, 'Open 300', 'Ventura', NULL),
(79, 'Open 330', 'Ventura', NULL),
(80, 'Open 360', 'Ventura', NULL),
(81, 'Fly 200', 'Real', NULL),
(82, 'Fly 230', 'Real', NULL),
(83, 'Fly 250', 'Real', NULL),
(84, 'Fly 280', 'Real', NULL),
(85, 'Fly 300', 'Real', NULL),
(86, 'Fly 330', 'Real', NULL),
(87, 'Fly 360', 'Real', NULL),
(88, 'Fly 390', 'Real', NULL),
(89, 'Fly 420', 'Real', NULL),
(90, 'Fly 450', 'Real', NULL),
(91, 'Solara 330', 'Solara', NULL),
(92, 'Solara 350', 'Solara', NULL),
(93, 'Solara 370', 'Solara', NULL),
(94, 'Solara 400', 'Solara', NULL),
(95, 'Solara 450', 'Solara', NULL),
(96, 'Solara 500', 'Solara', NULL),
(97, 'Solara 550', 'Solara', NULL),
(98, 'Solara 600', 'Solara', NULL),
(99, 'Solara 650', 'Solara', NULL),
(100, 'Solara 700', 'Solara', NULL),
(101, 'Azimut 40', 'Azimut', NULL),
(102, 'Azimut 45', 'Azimut', NULL),
(103, 'Azimut 50', 'Azimut', NULL),
(104, 'Azimut 55', 'Azimut', NULL),
(105, 'Azimut 60', 'Azimut', NULL),
(106, 'Azimut 66', 'Azimut', NULL),
(107, 'Azimut 72', 'Azimut', NULL),
(108, 'Azimut 78', 'Azimut', NULL),
(109, 'Azimut 80', 'Azimut', NULL),
(110, 'Azimut 90', 'Azimut', NULL),
(111, 'Ferretti 450', 'Ferretti', NULL),
(112, 'Ferretti 500', 'Ferretti', NULL),
(113, 'Ferretti 550', 'Ferretti', NULL),
(114, 'Ferretti 600', 'Ferretti', NULL),
(115, 'Ferretti 670', 'Ferretti', NULL),
(116, 'Ferretti 720', 'Ferretti', NULL),
(117, 'Ferretti 780', 'Ferretti', NULL),
(118, 'Ferretti 850', 'Ferretti', NULL),
(119, 'Ferretti 920', 'Ferretti', NULL),
(120, 'Ferretti 1000', 'Ferretti', NULL),
(121, 'Sea Ray 190', 'Sea Ray', NULL),
(122, 'Sea Ray 210', 'Sea Ray', NULL),
(123, 'Sea Ray 230', 'Sea Ray', NULL),
(124, 'Sea Ray 250', 'Sea Ray', NULL),
(125, 'Sea Ray 270', 'Sea Ray', NULL),
(126, 'Sea Ray 290', 'Sea Ray', NULL),
(127, 'Sea Ray 310', 'Sea Ray', NULL),
(128, 'Sea Ray 350', 'Sea Ray', NULL),
(129, 'Sea Ray 400', 'Sea Ray', NULL),
(130, 'Sea Ray 450', 'Sea Ray', NULL),
(131, 'Bayliner 160', 'Bayliner', NULL),
(132, 'Bayliner 180', 'Bayliner', NULL),
(133, 'Bayliner 200', 'Bayliner', NULL),
(134, 'Bayliner 220', 'Bayliner', NULL),
(135, 'Bayliner 240', 'Bayliner', NULL),
(136, 'Bayliner 260', 'Bayliner', NULL),
(137, 'Bayliner 280', 'Bayliner', NULL),
(138, 'Bayliner 300', 'Bayliner', NULL),
(139, 'Bayliner 340', 'Bayliner', NULL),
(140, 'Bayliner 380', 'Bayliner', NULL),
(141, 'Beneteau 30', 'Beneteau', NULL),
(142, 'Beneteau 34', 'Beneteau', NULL),
(143, 'Beneteau 38', 'Beneteau', NULL),
(144, 'Beneteau 40', 'Beneteau', NULL),
(145, 'Beneteau 46', 'Beneteau', NULL),
(146, 'Beneteau 50', 'Beneteau', NULL),
(147, 'Beneteau 55', 'Beneteau', NULL),
(148, 'Beneteau 60', 'Beneteau', NULL),
(149, 'Beneteau 62', 'Beneteau', NULL),
(150, 'Beneteau 70', 'Beneteau', NULL),
(151, 'Jeanneau 32', 'Jeanneau', NULL),
(152, 'Jeanneau 36', 'Jeanneau', NULL),
(153, 'Jeanneau 40', 'Jeanneau', NULL),
(154, 'Jeanneau 44', 'Jeanneau', NULL),
(155, 'Jeanneau 47', 'Jeanneau', NULL),
(156, 'Jeanneau 50', 'Jeanneau', NULL),
(157, 'Jeanneau 54', 'Jeanneau', NULL),
(158, 'Jeanneau 60', 'Jeanneau', NULL),
(159, 'Jeanneau 64', 'Jeanneau', NULL),
(160, 'Jeanneau 70', 'Jeanneau', NULL),
(161, 'Yacht 40', 'Intermarine', NULL),
(162, 'Yacht 44', 'Intermarine', NULL),
(163, 'Yacht 48', 'Intermarine', NULL),
(164, 'Yacht 52', 'Intermarine', NULL),
(165, 'Yacht 56', 'Intermarine', NULL),
(166, 'Yacht 60', 'Intermarine', NULL),
(167, 'Yacht 68', 'Intermarine', NULL),
(168, 'Yacht 72', 'Intermarine', NULL),
(169, 'Yacht 80', 'Intermarine', NULL),
(170, 'Yacht 90', 'Intermarine', NULL),
(171, 'FX Cruiser', 'Yamaha', NULL),
(172, 'VX Cruiser', 'Yamaha', NULL),
(173, 'VX Deluxe', 'Yamaha', NULL),
(174, 'GP 1800', 'Yamaha', NULL),
(175, 'SuperJet', 'Yamaha', NULL),
(176, 'EX Deluxe', 'Yamaha', NULL),
(177, 'EXR', 'Yamaha', NULL),
(178, 'VX Limited', 'Yamaha', NULL),
(179, 'FX SVHO', 'Yamaha', NULL),
(180, 'FX Cruiser SVHO', 'Yamaha', NULL),
(181, 'GTI 130', 'Sea-Doo', NULL),
(182, 'GTI SE 130', 'Sea-Doo', NULL),
(183, 'GTI SE 170', 'Sea-Doo', NULL),
(184, 'GTR 230', 'Sea-Doo', NULL),
(185, 'GTR-X 300', 'Sea-Doo', NULL),
(186, 'GTX 170', 'Sea-Doo', NULL),
(187, 'GTX 230', 'Sea-Doo', NULL),
(188, 'GTX Limited 300', 'Sea-Doo', NULL),
(189, 'RXP-X 300', 'Sea-Doo', NULL),
(190, 'RXT-X 300', 'Sea-Doo', NULL),
(191, 'STX 160', 'Kawasaki', NULL),
(192, 'Ultra 160', 'Kawasaki', NULL),
(193, 'Ultra 310X', 'Kawasaki', NULL),
(194, 'Ultra 310LX', 'Kawasaki', NULL),
(195, 'SX-R 160', 'Kawasaki', NULL),
(196, 'STX 15F', 'Kawasaki', NULL),
(197, 'Ultra 310R', 'Kawasaki', NULL),
(198, 'Ultra 310SE', 'Kawasaki', NULL),
(199, 'Jet Ski 900', 'Kawasaki', NULL),
(200, 'Jet Ski 1100', 'Kawasaki', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `servicos`
--

CREATE TABLE `servicos` (
  `serid` int NOT NULL,
  `cliid` int NOT NULL,
  `tipo_servico` enum('carro','moto','caminhao','aquatico','mobilia') COLLATE utf8mb4_unicode_ci NOT NULL,
  `serdescricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `servalor` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serstatus_pagamento` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `serstatus_servico` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `sermp_preference_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sermp_payment_id` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sermp_metodo_pagamento` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serdata_pagamento` datetime DEFAULT NULL,
  `serdata_servico` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `servicos`
--

INSERT INTO `servicos` (`serid`, `cliid`, `tipo_servico`, `serdescricao`, `servalor`, `serstatus_pagamento`, `serstatus_servico`, `sermp_preference_id`, `sermp_payment_id`, `sermp_metodo_pagamento`, `serdata_pagamento`, `serdata_servico`) VALUES
(10, 15, 'carro', 'Veículo: carro | pintar de rosa', '800.00', 'pendente', 'pendente', NULL, NULL, NULL, NULL, '2026-08-27 08:00:00'),
(11, 15, 'carro', 'Veículo: carro | pintar de rosa', '800.00', 'pendente', 'pendente', NULL, NULL, NULL, NULL, '2026-08-27 08:30:00'),
(12, 15, 'carro', 'Veículo: sdasda | sadsad', '800.00', 'pendente', 'pendente', NULL, NULL, NULL, NULL, '2026-08-20 16:00:00'),
(13, 16, 'carro', 'Veículo: Honda Civic | Pintura completa | Acabamento brilhante', '1200.00', 'aprovado', 'pendente', NULL, NULL, 'pix', '2026-09-01 10:00:00', '2026-09-03 08:00:00'),
(14, 17, 'carro', 'Veículo: Toyota Corolla | Polimento completo | Acabamento brilhante', '650.00', 'pendente', 'pendente', NULL, NULL, NULL, NULL, '2026-09-03 09:00:00'),
(15, 18, 'moto', 'Veículo: Honda CG 160 | Pintura completa | Acabamento brilhante', '700.00', 'aprovado', 'pendente', NULL, NULL, 'pix', '2026-09-02 09:30:00', '2026-09-04 08:00:00'),
(16, 19, 'moto', 'Veículo: Yamaha MT-03 | Pintura do tanque e carenagem', '550.00', 'pendente', 'pendente', NULL, NULL, NULL, NULL, '2026-09-04 09:00:00'),
(17, 20, 'caminhao', 'Veículo: Volvo FH 540 | Pintura completa da cabine', '3500.00', 'aprovado', 'pendente', NULL, NULL, 'cartao', '2026-09-02 11:00:00', '2026-09-05 08:00:00'),
(18, 21, 'caminhao', 'Veículo: Scania R 450 | Pintura externa completa', '4200.00', 'pendente', 'pendente', NULL, NULL, NULL, NULL, '2026-09-05 09:00:00'),
(19, 22, 'aquatico', 'Embarcação: Focker FS 230 | Pintura e revitalização', '2800.00', 'aprovado', 'pendente', NULL, NULL, 'pix', '2026-09-02 14:00:00', '2026-09-06 09:00:00'),
(20, 23, 'aquatico', 'Embarcação: NX 290 | Polimento e acabamento náutico', '1800.00', 'pendente', 'pendente', NULL, NULL, NULL, NULL, '2026-09-06 10:00:00'),
(21, 24, 'mobilia', 'Móvel: Mesa | Pintura e restauração', '450.00', 'aprovado', 'pendente', NULL, NULL, 'pix', '2026-09-02 15:00:00', '2026-09-07 08:00:00'),
(22, 25, 'mobilia', 'Móvel: Guarda-roupa | Restauração e pintura', '800.00', 'pendente', 'pendente', NULL, NULL, NULL, NULL, '2026-09-07 10:00:00'),
(23, 16, 'carro', 'Veículo: Chevrolet Camaro | Pintura preta metálica', '1800.00', 'pendente', 'pendente', NULL, NULL, NULL, NULL, '2026-09-08 09:00:00'),
(24, 17, 'moto', 'Veículo: Kawasaki Ninja 400 | Pintura personalizada', '950.00', 'aprovado', 'pendente', NULL, NULL, 'pix', '2026-09-02 16:00:00', '2026-09-08 14:00:00'),
(25, 18, 'caminhao', 'Veículo: Mercedes-Benz Actros | Pintura completa', '4800.00', 'pendente', 'pendente', NULL, NULL, NULL, NULL, '2026-09-09 08:00:00'),
(26, 19, 'aquatico', 'Embarcação: Schaefer 250 WA | Pintura externa', '3200.00', 'pendente', 'pendente', NULL, NULL, NULL, NULL, '2026-09-09 10:00:00'),
(27, 20, 'mobilia', 'Móvel: Sofá | Reforma e pintura estrutural', '600.00', 'aprovado', 'finalizado', NULL, NULL, 'cartao', '2026-09-02 17:00:00', '2026-09-10 09:00:00'),
(72, 27, 'carro', 'Veículo: Hyundai Creta | pintar de preto | Acabamento: Brilhante (+15%)', '920.00', 'pagar_no_local', 'finalizado', NULL, NULL, NULL, NULL, '2026-09-23 08:30:00'),
(73, 27, 'carro', 'Veículo: Audi Q3 | Pintar de cromado | Acabamento: Brilhante (+15%)', '920.00', 'pagar_no_local', 'finalizado', NULL, NULL, NULL, NULL, '2026-09-25 08:30:00');

-- --------------------------------------------------------

--
-- Table structure for table `vendas`
--

CREATE TABLE `vendas` (
  `venid` int NOT NULL,
  `vencam` int DEFAULT NULL,
  `vencar` int DEFAULT NULL,
  `venmob` int DEFAULT NULL,
  `venmot` int DEFAULT NULL,
  `vennau` int DEFAULT NULL,
  `venser` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vendas`
--

INSERT INTO `vendas` (`venid`, `vencam`, `vencar`, `venmob`, `venmot`, `vennau`, `venser`) VALUES
(1, NULL, 1, NULL, NULL, NULL, 13),
(2, NULL, 2, NULL, NULL, NULL, 14),
(3, NULL, NULL, NULL, 1, NULL, 15),
(4, NULL, NULL, NULL, 3, NULL, 16),
(5, 2, NULL, NULL, NULL, NULL, 17),
(6, 3, NULL, NULL, NULL, NULL, 18),
(7, NULL, NULL, NULL, NULL, 1, 19),
(8, NULL, NULL, NULL, NULL, 4, 20),
(9, NULL, NULL, 1, NULL, NULL, 21),
(10, NULL, NULL, 4, NULL, NULL, 22),
(11, NULL, 6, NULL, NULL, NULL, 23),
(12, NULL, NULL, NULL, 7, NULL, 24),
(13, 1, NULL, NULL, NULL, NULL, 25),
(14, NULL, NULL, NULL, NULL, 2, 26),
(15, NULL, NULL, 5, NULL, NULL, 27);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `caminhoes`
--
ALTER TABLE `caminhoes`
  ADD PRIMARY KEY (`camid`);

--
-- Indexes for table `carros`
--
ALTER TABLE `carros`
  ADD PRIMARY KEY (`carid`);

--
-- Indexes for table `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`cliid`),
  ADD UNIQUE KEY `cliservico` (`cliservico`);

--
-- Indexes for table `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Indexes for table `faturamento_mensal`
--
ALTER TABLE `faturamento_mensal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_mes` (`ano`,`mes`),
  ADD KEY `idx_ano_mes` (`ano`,`mes`);

--
-- Indexes for table `galeria`
--
ALTER TABLE `galeria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_criado` (`criado_em`);

--
-- Indexes for table `horarios_ocupados`
--
ALTER TABLE `horarios_ocupados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_slot` (`data`,`horario`);

--
-- Indexes for table `mobilia`
--
ALTER TABLE `mobilia`
  ADD PRIMARY KEY (`mobid`);

--
-- Indexes for table `motos`
--
ALTER TABLE `motos`
  ADD PRIMARY KEY (`motid`);

--
-- Indexes for table `nauticos`
--
ALTER TABLE `nauticos`
  ADD PRIMARY KEY (`nauid`);

--
-- Indexes for table `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`serid`),
  ADD KEY `cliid` (`cliid`),
  ADD KEY `idx_servicos_mp_preference` (`sermp_preference_id`),
  ADD KEY `idx_servicos_mp_payment` (`sermp_payment_id`);

--
-- Indexes for table `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`venid`),
  ADD KEY `vencam` (`vencam`),
  ADD KEY `vencar` (`vencar`),
  ADD KEY `venmob` (`venmob`),
  ADD KEY `venmot` (`venmot`),
  ADD KEY `vennau` (`vennau`),
  ADD KEY `venser` (`venser`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `caminhoes`
--
ALTER TABLE `caminhoes`
  MODIFY `camid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=213;

--
-- AUTO_INCREMENT for table `carros`
--
ALTER TABLE `carros`
  MODIFY `carid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=202;

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `cliid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `faturamento_mensal`
--
ALTER TABLE `faturamento_mensal`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `galeria`
--
ALTER TABLE `galeria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `horarios_ocupados`
--
ALTER TABLE `horarios_ocupados`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `mobilia`
--
ALTER TABLE `mobilia`
  MODIFY `mobid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- AUTO_INCREMENT for table `motos`
--
ALTER TABLE `motos`
  MODIFY `motid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `nauticos`
--
ALTER TABLE `nauticos`
  MODIFY `nauid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT for table `servicos`
--
ALTER TABLE `servicos`
  MODIFY `serid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `vendas`
--
ALTER TABLE `vendas`
  MODIFY `venid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `servicos`
--
ALTER TABLE `servicos`
  ADD CONSTRAINT `servicos_ibfk_1` FOREIGN KEY (`cliid`) REFERENCES `clientes` (`cliid`);

--
-- Constraints for table `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`vencam`) REFERENCES `caminhoes` (`camid`),
  ADD CONSTRAINT `vendas_ibfk_2` FOREIGN KEY (`vencar`) REFERENCES `carros` (`carid`),
  ADD CONSTRAINT `vendas_ibfk_3` FOREIGN KEY (`venmob`) REFERENCES `mobilia` (`mobid`),
  ADD CONSTRAINT `vendas_ibfk_4` FOREIGN KEY (`venmot`) REFERENCES `motos` (`motid`),
  ADD CONSTRAINT `vendas_ibfk_5` FOREIGN KEY (`vennau`) REFERENCES `nauticos` (`nauid`),
  ADD CONSTRAINT `vendas_ibfk_6` FOREIGN KEY (`venser`) REFERENCES `servicos` (`serid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
