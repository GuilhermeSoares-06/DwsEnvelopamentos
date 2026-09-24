-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 24/09/2026 às 21:44
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `dws`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `caminhoes`
--

CREATE TABLE `caminhoes` (
  `camid` int(11) NOT NULL,
  `cammarca` varchar(50) DEFAULT NULL,
  `camelo` varchar(50) DEFAULT NULL,
  `camimg` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `caminhoes`
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
(10, 'Ford', 'Cargo 2429', 'cargo.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `carros`
--

CREATE TABLE `carros` (
  `carid` int(11) NOT NULL,
  `carmodelo` varchar(50) DEFAULT NULL,
  `carmarca` varchar(50) DEFAULT NULL,
  `carimg` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `carros`
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
(11, 'Brasília 1300', 'Volkswagen', NULL),
(12, 'Brasília 1600', 'Volkswagen', NULL),
(13, 'Fiat Uno Mille', 'Fiat', NULL),
(14, 'Uno Turbo', 'Fiat', NULL),
(15, 'Uno SX', 'Fiat', NULL),
(16, 'Variant I', 'Volkswagen', NULL),
(17, 'Variant II', 'Volkswagen', NULL),
(18, 'Fox 1.0', 'Volkswagen', NULL),
(19, 'Fox 1.6', 'Volkswagen', NULL),
(20, 'Fox Prime', 'Volkswagen', NULL),
(21, 'Palio Fire', 'Fiat', NULL),
(22, 'Palio ELX', 'Fiat', NULL),
(23, 'Palio Weekend', 'Fiat', NULL),
(24, 'Palio Adventure', 'Fiat', NULL),
(25, 'Celta Life', 'Chevrolet', NULL),
(26, 'Celta Spirit', 'Chevrolet', NULL),
(27, 'Celta Super', 'Chevrolet', NULL),
(28, 'Chevette SL', 'Chevrolet', NULL),
(29, 'Chevette Marajó', 'Chevrolet', NULL),
(30, 'Opala Comodoro', 'Chevrolet', NULL),
(31, 'Opala Diplomata', 'Chevrolet', NULL),
(32, 'Opala SS', 'Chevrolet', NULL),
(33, 'Monza Classic', 'Chevrolet', NULL),
(34, 'Monza SL/E', 'Chevrolet', NULL),
(35, 'Kadett GL', 'Chevrolet', NULL),
(36, 'Kadett GSi', 'Chevrolet', NULL),
(37, 'Escort XR3', 'Ford', NULL),
(38, 'Escort Hobby', 'Ford', NULL),
(39, 'Corcel II', 'Ford', NULL),
(40, 'Del Rey Ghia', 'Ford', NULL),
(41, 'Belina II', 'Ford', NULL),
(42, 'Pampa GL', 'Ford', NULL),
(43, 'Verona LX', 'Ford', NULL),
(44, 'Santana GLS', 'Volkswagen', NULL),
(45, 'Santana Quantum', 'Volkswagen', NULL),
(46, 'Gol GT', 'Volkswagen', NULL),
(47, 'Gol GTS', 'Volkswagen', NULL),
(48, 'Gol CL', 'Volkswagen', NULL),
(49, 'Saveiro CL', 'Volkswagen', NULL),
(50, 'Parati GLS', 'Volkswagen', NULL),
(51, 'Fusca 1200', 'Volkswagen', NULL),
(52, 'Fusca 1300', 'Volkswagen', NULL),
(53, 'Fusca 1300 L', 'Volkswagen', NULL),
(54, 'Fusca 1500', 'Volkswagen', NULL),
(55, 'Fusca 1600', 'Volkswagen', NULL),
(56, 'Fusca 1300 Fuscão', 'Volkswagen', NULL),
(57, 'Fusca 1300 Série Prata', 'Volkswagen', NULL),
(58, 'Fusca 1500 Série Ouro', 'Volkswagen', NULL),
(59, 'Fusca 1600 Itamar', 'Volkswagen', NULL),
(60, 'Fusca Itamar 1993', 'Volkswagen', NULL),
(61, 'Fusca Itamar 1994', 'Volkswagen', NULL),
(62, 'Fusca Itamar 1995', 'Volkswagen', NULL),
(63, 'Fusca Itamar 1996', 'Volkswagen', NULL),
(64, 'Fusca Itamar 1997', 'Volkswagen', NULL),
(65, 'Fusca Itamar 1998', 'Volkswagen', NULL),
(66, 'Fusca Itamar 1999', 'Volkswagen', NULL),
(67, 'Fusca Itamar 2000', 'Volkswagen', NULL),
(68, 'Fusca 1200 Export', 'Volkswagen', NULL),
(69, 'Fusca 1300 Export', 'Volkswagen', NULL),
(70, 'Fusca 1302', 'Volkswagen', NULL),
(71, 'Fusca 1303', 'Volkswagen', NULL),
(72, 'Fusca 1303 L', 'Volkswagen', NULL),
(73, 'Fusca 1303 S', 'Volkswagen', NULL),
(74, 'Fusca 1500 L', 'Volkswagen', NULL),
(75, 'Fusca 1500 Luxo', 'Volkswagen', NULL),
(76, 'Fusca 1600 Sedan', 'Volkswagen', NULL),
(77, 'Fusca 1600 S Sedan', 'Volkswagen', NULL),
(78, 'Fusca Série Prata', 'Volkswagen', NULL),
(79, 'Fusca Série Ouro', 'Volkswagen', NULL),
(80, 'Fusca Última Série', 'Volkswagen', NULL),
(81, 'Fusca 1300 1968', 'Volkswagen', NULL),
(82, 'Fusca 1300 1970', 'Volkswagen', NULL),
(83, 'Fusca 1300 1972', 'Volkswagen', NULL),
(84, 'Fusca 1300 1974', 'Volkswagen', NULL),
(85, 'Fusca 1300 1976', 'Volkswagen', NULL),
(86, 'Fusca 1300 1978', 'Volkswagen', NULL),
(87, 'Fusca 1300 1980', 'Volkswagen', NULL),
(88, 'Fusca 1300 1982', 'Volkswagen', NULL),
(89, 'Fusca 1500 1971', 'Volkswagen', NULL),
(90, 'Fusca 1500 1973', 'Volkswagen', NULL),
(91, 'Fusca 1500 1975', 'Volkswagen', NULL),
(92, 'Fusca 1500 1977', 'Volkswagen', NULL),
(93, 'Fusca 1500 1979', 'Volkswagen', NULL),
(94, 'Fusca 1600 1984', 'Volkswagen', NULL),
(95, 'Fusca 1600 1986', 'Volkswagen', NULL),
(96, 'Fusca 1600 1988', 'Volkswagen', NULL),
(97, 'Fusca 1600 1990', 'Volkswagen', NULL),
(98, 'Fusca 1600 1992', 'Volkswagen', NULL),
(99, 'Fusca 1600 1993', 'Volkswagen', NULL),
(100, 'Fusca 1600 1994', 'Volkswagen', NULL),
(101, 'Fusca 1600 1995', 'Volkswagen', NULL),
(102, 'Brasília 1300 1973', 'Volkswagen', NULL),
(103, 'Brasília 1300 1974', 'Volkswagen', NULL),
(104, 'Brasília 1300 1975', 'Volkswagen', NULL),
(105, 'Brasília 1300 1976', 'Volkswagen', NULL),
(106, 'Brasília 1300 1977', 'Volkswagen', NULL),
(107, 'Brasília 1300 1978', 'Volkswagen', NULL),
(108, 'Brasília 1300 1979', 'Volkswagen', NULL),
(109, 'Brasília 1300 1980', 'Volkswagen', NULL),
(110, 'Brasília 1300 1981', 'Volkswagen', NULL),
(111, 'Brasília 1300 1982', 'Volkswagen', NULL),
(112, 'Brasília 1300 L', 'Volkswagen', NULL),
(113, 'Brasília 1300 Luxo', 'Volkswagen', NULL),
(114, 'Brasília 1300 S', 'Volkswagen', NULL),
(115, 'Brasília 1300 LS', 'Volkswagen', NULL),
(116, 'Brasília 1300 Special', 'Volkswagen', NULL),
(117, 'Brasília 1600 1973', 'Volkswagen', NULL),
(118, 'Brasília 1600 1974', 'Volkswagen', NULL),
(119, 'Brasília 1600 1975', 'Volkswagen', NULL),
(120, 'Brasília 1600 1976', 'Volkswagen', NULL),
(121, 'Brasília 1600 1977', 'Volkswagen', NULL),
(122, 'Brasília 1600 1978', 'Volkswagen', NULL),
(123, 'Brasília 1600 1979', 'Volkswagen', NULL),
(124, 'Brasília 1600 1980', 'Volkswagen', NULL),
(125, 'Brasília 1600 1981', 'Volkswagen', NULL),
(126, 'Brasília 1600 1982', 'Volkswagen', NULL),
(127, 'Brasília 1600 L', 'Volkswagen', NULL),
(128, 'Brasília 1600 Luxo', 'Volkswagen', NULL),
(129, 'Brasília 1600 S', 'Volkswagen', NULL),
(130, 'Brasília 1600 LS', 'Volkswagen', NULL),
(131, 'Brasília 1600 Special', 'Volkswagen', NULL),
(132, 'Brasília 1600 4 Portas', 'Volkswagen', NULL),
(133, 'Brasília 1600 2 Portas', 'Volkswagen', NULL),
(134, 'Brasília Amarela', 'Volkswagen', NULL),
(135, 'Brasília Vermelha', 'Volkswagen', NULL),
(136, 'Brasília Azul', 'Volkswagen', NULL),
(137, 'Brasília Branca', 'Volkswagen', NULL),
(138, 'Brasília Verde', 'Volkswagen', NULL),
(139, 'Brasília Bege', 'Volkswagen', NULL),
(140, 'Brasília Marrom', 'Volkswagen', NULL),
(141, 'Brasília Laranja', 'Volkswagen', NULL),
(142, 'Brasília 1600 Série Especial', 'Volkswagen', NULL),
(143, 'Brasília 1600 Série Luxo', 'Volkswagen', NULL),
(144, 'Brasília 1600 Série Prata', 'Volkswagen', NULL),
(145, 'Brasília 1600 Série Ouro', 'Volkswagen', NULL),
(146, 'Brasília 1600 Clássica', 'Volkswagen', NULL),
(147, 'Brasília 1600 Standard', 'Volkswagen', NULL),
(148, 'Brasília 1600 Super', 'Volkswagen', NULL),
(149, 'Brasília 1600 Gran Luxo', 'Volkswagen', NULL),
(150, 'Brasília 1600 Nacional', 'Volkswagen', NULL),
(151, 'Carro Flinstons(Mônica)', 'PreHistorico', NULL),
(152, 'Escort XR3', 'Ford', 'escort_xr3.jpg'),
(153, 'Escort Hobby', 'Ford', 'escort_hobby.jpg'),
(154, 'Escort Zetec', 'Ford', 'escort_zetec.jpg'),
(155, 'Escort Cosworth', 'Ford', 'escort_cosw.jpg'),
(156, 'Escort Ghia', 'Ford', 'escort_ghia.jpg'),
(157, '206', 'Peugeot', 'peugeot_206.jpg'),
(158, '207', 'Peugeot', 'peugeot_207.jpg'),
(159, '208', 'Peugeot', 'peugeot_208.jpg'),
(160, '307', 'Peugeot', 'peugeot_307.jpg'),
(161, '308', 'Peugeot', 'peugeot_308.jpg'),
(162, '408', 'Peugeot', 'peugeot_408.jpg'),
(163, '5008', 'Peugeot', 'peugeot_5008.jpg'),
(164, '2008', 'Peugeot', 'peugeot_2008.jpg'),
(165, '3008', 'Peugeot', 'peugeot_3008.jpg'),
(166, 'RCZ', 'Peugeot', 'peugeot_rcz.jpg'),
(167, 'Gol', 'Volkswagen', 'gol.jpg'),
(168, 'Uno', 'Fiat', 'uno.jpg'),
(169, 'Palio', 'Fiat', 'palio.jpg'),
(170, 'Corsa', 'Chevrolet', 'corsa.jpg'),
(171, 'Celta', 'Chevrolet', 'celta.jpg'),
(172, 'Onix', 'Chevrolet', 'onix.jpg'),
(173, 'HB20', 'Hyundai', 'hb20.jpg'),
(174, 'Corolla', 'Toyota', 'corolla.jpg'),
(175, 'Civic', 'Honda', 'civic.jpg'),
(176, 'Sandero', 'Renault', 'sandero.jpg'),
(177, 'Logan', 'Renault', 'logan.jpg'),
(178, 'Clio', 'Renault', 'clio.jpg'),
(179, 'Fiesta', 'Ford', 'fiesta.jpg'),
(180, 'Ka', 'Ford', 'ka.jpg'),
(181, 'Focus', 'Ford', 'focus.jpg'),
(182, 'EcoSport', 'Ford', 'ecosport.jpg'),
(183, 'Fusion', 'Ford', 'fusion.jpg'),
(184, 'Ranger', 'Ford', 'ranger.jpg'),
(185, 'Mustang', 'Ford', 'mustang.jpg'),
(186, 'Fox', 'Volkswagen', 'fox.jpg'),
(187, 'Polo', 'Volkswagen', 'polo.jpg'),
(188, 'Golf', 'Volkswagen', 'golf.jpg'),
(189, 'Jetta', 'Volkswagen', 'jetta.jpg'),
(190, 'Passat', 'Volkswagen', 'passat.jpg'),
(191, 'T-Cross', 'Volkswagen', 'tcross.jpg'),
(192, 'Nivus', 'Volkswagen', 'nivus.jpg'),
(193, 'Argo', 'Fiat', 'argo.jpg'),
(194, 'Cronos', 'Fiat', 'cronos.jpg'),
(195, 'Toro', 'Fiat', 'toro.jpg'),
(196, 'Mobi', 'Fiat', 'mobi.jpg'),
(197, 'Pulse', 'Fiat', 'pulse.jpg'),
(198, 'Fastback', 'Fiat', 'fastback.jpg'),
(199, 'Siena', 'Fiat', 'siena.jpg'),
(200, 'Prisma', 'Chevrolet', 'prisma.jpg'),
(201, 'Cruze', 'Chevrolet', 'cruze.jpg'),
(202, 'Tracker', 'Chevrolet', 'tracker.jpg'),
(203, 'Spin', 'Chevrolet', 'spin.jpg'),
(204, 'S10', 'Chevrolet', 's10.jpg'),
(205, 'Camaro', 'Chevrolet', 'camaro.jpg'),
(206, 'Fit', 'Honda', 'fit.jpg'),
(207, 'City', 'Honda', 'city.jpg'),
(208, 'HR-V', 'Honda', 'hrv.jpg'),
(209, 'CR-V', 'Honda', 'crv.jpg'),
(210, 'Accord', 'Honda', 'accord.jpg'),
(211, 'Etios', 'Toyota', 'etios.jpg'),
(212, 'Yaris', 'Toyota', 'yaris.jpg'),
(213, 'Hilux', 'Toyota', 'hilux.jpg'),
(214, 'SW4', 'Toyota', 'sw4.jpg'),
(215, 'Rav4', 'Toyota', 'rav4.jpg'),
(216, 'i30', 'Hyundai', 'i30.jpg'),
(217, 'Tucson', 'Hyundai', 'tucson.jpg'),
(218, 'Creta', 'Hyundai', 'creta.jpg'),
(219, 'Santa Fe', 'Hyundai', 'santafe.jpg'),
(220, 'Elantra', 'Hyundai', 'elantra.jpg'),
(221, 'Kwid', 'Renault', 'kwid.jpg'),
(222, 'Duster', 'Renault', 'duster.jpg'),
(223, 'Captur', 'Renault', 'captur.jpg'),
(224, 'Fluence', 'Renault', 'fluence.jpg'),
(225, 'March', 'Nissan', 'march.jpg'),
(226, 'Versa', 'Nissan', 'versa.jpg'),
(227, 'Kicks', 'Nissan', 'kicks.jpg'),
(228, 'Sentra', 'Nissan', 'sentra.jpg'),
(229, 'Frontier', 'Nissan', 'frontier.jpg'),
(230, 'C3', 'Citroën', 'c3.jpg'),
(231, 'C4 Cactus', 'Citroën', 'c4cactus.jpg'),
(232, 'C5 Aircross', 'Citroën', 'c5aircross.jpg'),
(233, 'Xsara Picasso', 'Citroën', 'picasso.jpg'),
(234, 'Cerato', 'Kia', 'cerato.jpg'),
(235, 'Sportage', 'Kia', 'sportage.jpg'),
(236, 'Seltos', 'Kia', 'seltos.jpg'),
(237, 'Picanto', 'Kia', 'picanto.jpg'),
(238, 'ASX', 'Mitsubishi', 'asx.jpg'),
(239, 'Lancer', 'Mitsubishi', 'lancer.jpg'),
(240, 'L200 Triton', 'Mitsubishi', 'l200.jpg'),
(241, 'Outlander', 'Mitsubishi', 'outlander.jpg'),
(242, 'Renegade', 'Jeep', 'renegade.jpg'),
(243, 'Compass', 'Jeep', 'compass.jpg'),
(244, 'Commander', 'Jeep', 'commander.jpg'),
(245, 'Grand Cherokee', 'Jeep', 'cherokee.jpg'),
(246, 'Classe A', 'Mercedes-Benz', 'classea.jpg'),
(247, 'Classe C', 'Mercedes-Benz', 'classec.jpg'),
(248, 'Série 3', 'BMW', 'serie3.jpg'),
(249, 'X1', 'BMW', 'bmwx1.jpg'),
(250, 'A3', 'Audi', 'audia3.jpg'),
(251, 'Q3', 'Audi', 'audiq3.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `cliid` int(11) NOT NULL,
  `clinome` varchar(255) DEFAULT NULL,
  `clicpf` varchar(14) DEFAULT NULL,
  `cliendereco` varchar(100) DEFAULT NULL,
  `cliservico` int(11) DEFAULT NULL,
  `clitel` varchar(20) DEFAULT NULL,
  `cliemail` varchar(150) DEFAULT NULL,
  `clisenha` varchar(150) DEFAULT NULL,
  `tipocliente` enum('cliente','funcionario') DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `clientes`
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
-- Estrutura para tabela `configuracoes`
--

CREATE TABLE `configuracoes` (
  `id` int(11) NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text NOT NULL,
  `tipo` enum('texto','numero','imagem','json') DEFAULT 'texto',
  `descricao` varchar(255) DEFAULT NULL,
  `atualizado_em` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `configuracoes`
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
-- Estrutura para tabela `faturamento_mensal`
--

CREATE TABLE `faturamento_mensal` (
  `id` int(11) NOT NULL,
  `ano` int(11) NOT NULL,
  `mes` int(11) NOT NULL,
  `total_servicos` int(11) DEFAULT 0,
  `faturamento_total` decimal(10,2) DEFAULT 0.00,
  `faturamento_aprovado` decimal(10,2) DEFAULT 0.00,
  `fechado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `galeria`
--

CREATE TABLE `galeria` (
  `id` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descricao` text DEFAULT NULL,
  `tipo` enum('carro','moto','caminhao','aquatico','mobilia') NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `horarios_ocupados`
--

CREATE TABLE `horarios_ocupados` (
  `id` int(11) NOT NULL,
  `data` date NOT NULL,
  `horario` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `horarios_ocupados`
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
-- Estrutura para tabela `mobilia`
--

CREATE TABLE `mobilia` (
  `mobid` int(11) NOT NULL,
  `mobmedida` varchar(50) DEFAULT NULL,
  `fachada` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `mobilia`
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
(10, 'Escrivaninha 1,40m x 0,60m', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `motos`
--

CREATE TABLE `motos` (
  `motid` int(11) NOT NULL,
  `motmarca` varchar(50) DEFAULT NULL,
  `motmodelo` varchar(50) DEFAULT NULL,
  `motimg` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `motos`
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
(10, 'Suzuki', 'GSX-S750', 'gsxs750.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `nauticos`
--

CREATE TABLE `nauticos` (
  `nauid` int(11) NOT NULL,
  `naumodelo` varchar(30) DEFAULT NULL,
  `naumarca` varchar(20) DEFAULT NULL,
  `nauimg` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `nauticos`
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
(10, 'Real 29', 'Real Powerboats', 'real29.jpg');

-- --------------------------------------------------------

--
-- Estrutura para tabela `servicos`
--

CREATE TABLE `servicos` (
  `serid` int(11) NOT NULL,
  `cliid` int(11) NOT NULL,
  `tipo_servico` enum('carro','moto','caminhao','aquatico','mobilia') NOT NULL,
  `serdescricao` varchar(255) NOT NULL,
  `servalor` varchar(20) NOT NULL,
  `serstatus_pagamento` varchar(30) DEFAULT 'pendente',
  `serstatus_servico` varchar(20) DEFAULT 'pendente',
  `sermp_preference_id` varchar(64) DEFAULT NULL,
  `sermp_payment_id` varchar(64) DEFAULT NULL,
  `sermp_metodo_pagamento` varchar(50) DEFAULT NULL,
  `serdata_pagamento` datetime DEFAULT NULL,
  `serdata_servico` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `servicos`
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
-- Estrutura para tabela `vendas`
--

CREATE TABLE `vendas` (
  `venid` int(11) NOT NULL,
  `vencam` int(11) DEFAULT NULL,
  `vencar` int(11) DEFAULT NULL,
  `venmob` int(11) DEFAULT NULL,
  `venmot` int(11) DEFAULT NULL,
  `vennau` int(11) DEFAULT NULL,
  `venser` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `vendas`
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
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `caminhoes`
--
ALTER TABLE `caminhoes`
  ADD PRIMARY KEY (`camid`);

--
-- Índices de tabela `carros`
--
ALTER TABLE `carros`
  ADD PRIMARY KEY (`carid`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`cliid`),
  ADD UNIQUE KEY `cliservico` (`cliservico`);

--
-- Índices de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices de tabela `faturamento_mensal`
--
ALTER TABLE `faturamento_mensal`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_mes` (`ano`,`mes`),
  ADD KEY `idx_ano_mes` (`ano`,`mes`);

--
-- Índices de tabela `galeria`
--
ALTER TABLE `galeria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_criado` (`criado_em`);

--
-- Índices de tabela `horarios_ocupados`
--
ALTER TABLE `horarios_ocupados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_slot` (`data`,`horario`);

--
-- Índices de tabela `mobilia`
--
ALTER TABLE `mobilia`
  ADD PRIMARY KEY (`mobid`);

--
-- Índices de tabela `motos`
--
ALTER TABLE `motos`
  ADD PRIMARY KEY (`motid`);

--
-- Índices de tabela `nauticos`
--
ALTER TABLE `nauticos`
  ADD PRIMARY KEY (`nauid`);

--
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`serid`),
  ADD KEY `cliid` (`cliid`),
  ADD KEY `idx_servicos_mp_preference` (`sermp_preference_id`),
  ADD KEY `idx_servicos_mp_payment` (`sermp_payment_id`);

--
-- Índices de tabela `vendas`
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
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `caminhoes`
--
ALTER TABLE `caminhoes`
  MODIFY `camid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `carros`
--
ALTER TABLE `carros`
  MODIFY `carid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=252;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `cliid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `configuracoes`
--
ALTER TABLE `configuracoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `faturamento_mensal`
--
ALTER TABLE `faturamento_mensal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `galeria`
--
ALTER TABLE `galeria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `horarios_ocupados`
--
ALTER TABLE `horarios_ocupados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `mobilia`
--
ALTER TABLE `mobilia`
  MODIFY `mobid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `motos`
--
ALTER TABLE `motos`
  MODIFY `motid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `nauticos`
--
ALTER TABLE `nauticos`
  MODIFY `nauid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `serid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `venid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `servicos`
--
ALTER TABLE `servicos`
  ADD CONSTRAINT `servicos_ibfk_1` FOREIGN KEY (`cliid`) REFERENCES `clientes` (`cliid`);

--
-- Restrições para tabelas `vendas`
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
