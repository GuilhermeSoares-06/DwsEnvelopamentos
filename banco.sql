-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 29/10/2026 às 01:31
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
-- Estrutura para tabela `cargos`
--

CREATE TABLE `cargos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `cargos`
--

INSERT INTO `cargos` (`id`, `nome`) VALUES
(1, 'Envelopador');

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `cliid` int(11) NOT NULL,
  `clinome` varchar(255) DEFAULT NULL,
  `clicpf` varchar(14) DEFAULT NULL,
  `cliendereco` varchar(255) DEFAULT NULL,
  `cliservico` int(11) DEFAULT NULL,
  `clitel` varchar(20) DEFAULT NULL,
  `cliemail` varchar(150) DEFAULT NULL,
  `clisenha` varchar(150) DEFAULT NULL,
  `tipocliente` enum('cliente','funcionario') DEFAULT 'cliente',
  `cli_ip_cadastro` varchar(45) DEFAULT NULL,
  `cli_criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`cliid`, `clinome`, `clicpf`, `cliendereco`, `cliservico`, `clitel`, `cliemail`, `clisenha`, `tipocliente`, `cli_ip_cadastro`, `cli_criado_em`) VALUES
(27, 'Guilherme Soares', '49467262843', 'Piraju', NULL, '(14) 99617-5617', NULL, '$2y$10$XH058DYovCjU5UX35XDUQOXedKUFGN2RU5leI554VOYa7Gh.rjCny', 'cliente', NULL, '2026-10-21 07:57:07'),
(29, 'Matheus Souza', '84251013018', 'Rua Chico Malta, 26, Vila Tibiriça (Tibiriçá do Paranapanema), Piraju/SP, CEP 18810410', NULL, '(14) 99617-5617', 'MatheusSouza@gmail.com', '$2y$10$EEpk2b.kzS8IwazEpS5DBOFGfdTqJp8xnGqOUI5kC0s4u.EFTL9Qm', 'cliente', '::1', '2026-10-28 18:29:56');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `cpf` varchar(14) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `salario` decimal(10,2) DEFAULT 0.00,
  `cargo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `funcionarios`
--

INSERT INTO `funcionarios` (`id`, `nome`, `usuario`, `senha`, `cpf`, `telefone`, `salario`, `cargo_id`) VALUES
(1, 'Dariel', 'Dariel', '$2y$10$VlKGd6KSR6qrtrELfSK8kelHpEN7DA56BoM5I0OZEK6PbYjwflVBa', '00000000000', '(14) 99999-9999', 5000.00, NULL),
(2, 'kevin', 'kevinvaz', '$2y$10$apsqAk5xPrexCgtLjVEBXeaEKYvBT74DrraYBif20Q6Z8P0/k0Hea', '835.740.550-99', '14996154879', 2500.00, 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `galeria`
--

INSERT INTO `galeria` (`id`, `titulo`, `descricao`, `tipo`, `imagem`, `criado_em`) VALUES
(2, 'TESTE', '', 'carro', 'img/galeria/gal_27716c1064396754.webp', '2026-10-28 17:42:02');

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
-- Estrutura para tabela `servicos`
--

CREATE TABLE `servicos` (
  `serid` int(11) NOT NULL,
  `cliid` int(11) NOT NULL,
  `tipo_servico` enum('carro','moto','caminhao','aquatico','mobilia') NOT NULL,
  `vecid` int(11) DEFAULT NULL,
  `serdescricao` varchar(1000) NOT NULL,
  `servalor` decimal(10,2) NOT NULL DEFAULT 0.00,
  `serstatus_pagamento` varchar(30) DEFAULT 'pendente',
  `serstatus_servico` varchar(20) DEFAULT 'pendente',
  `sermp_preference_id` varchar(64) DEFAULT NULL,
  `sermp_payment_id` varchar(64) DEFAULT NULL,
  `sermp_metodo_pagamento` varchar(50) DEFAULT NULL,
  `serdata_pagamento` datetime DEFAULT NULL,
  `serdata_servico` datetime DEFAULT current_timestamp(),
  `sercriado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `servicos`
--

INSERT INTO `servicos` (`serid`, `cliid`, `tipo_servico`, `vecid`, `serdescricao`, `servalor`, `serstatus_pagamento`, `serstatus_servico`, `sermp_preference_id`, `sermp_payment_id`, `sermp_metodo_pagamento`, `serdata_pagamento`, `serdata_servico`, `sercriado_em`) VALUES
(75, 27, 'carro', NULL, 'Veículo: Chevrolet Agile | Gostaria de colocar um envelopamento na textura de camuflado | Acabamento: Texturizado (+50%)', 150.00, 'pagar_no_local', 'pendente', NULL, NULL, NULL, NULL, '2026-10-29 12:00:00', '2026-10-28 15:27:17');

-- --------------------------------------------------------

--
-- Estrutura para tabela `tentativas_login`
--

CREATE TABLE `tentativas_login` (
  `id` int(11) NOT NULL,
  `chave` char(32) NOT NULL,
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `tentativas_login`
--

INSERT INTO `tentativas_login` (`id`, `chave`, `criado_em`) VALUES
(3, '9350dc73c715961ff9886f4e26f708d9', '2026-10-28 18:29:56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos`
--

CREATE TABLE `veiculos` (
  `vecid` int(11) NOT NULL,
  `vectipo` enum('carro','moto','caminhao','aquatico','mobilia') NOT NULL,
  `vecmarca` varchar(60) DEFAULT NULL,
  `vecmodelo` varchar(120) NOT NULL,
  `vecimg` varchar(100) DEFAULT NULL,
  `vecativo` tinyint(1) NOT NULL DEFAULT 1,
  `veccriado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `veiculos`
--

INSERT INTO `veiculos` (`vecid`, `vectipo`, `vecmarca`, `vecmodelo`, `vecimg`, `vecativo`, `veccriado_em`) VALUES
(1, 'carro', 'Chevrolet', 'Agile', NULL, 1, '2026-10-28 16:06:40'),
(2, 'carro', 'Chevrolet', 'Astra', NULL, 1, '2026-10-28 16:06:40'),
(3, 'carro', 'Chevrolet', 'Blazer', NULL, 1, '2026-10-28 16:06:40'),
(4, 'carro', 'Chevrolet', 'Camaro', 'camaro.jpg', 1, '2026-10-28 16:06:40'),
(5, 'carro', 'Chevrolet', 'Celta', 'celta.jpg', 1, '2026-10-28 16:06:40'),
(6, 'carro', 'Chevrolet', 'Classic', 'classic.jpg', 1, '2026-10-28 16:06:40'),
(7, 'carro', 'Chevrolet', 'Corsa', 'corsa.jpg', 1, '2026-10-28 16:06:40'),
(8, 'carro', 'Chevrolet', 'Cruze', NULL, 1, '2026-10-28 16:06:40'),
(9, 'carro', 'Chevrolet', 'Joy', NULL, 1, '2026-10-28 16:06:40'),
(10, 'carro', 'Chevrolet', 'Meriva', NULL, 1, '2026-10-28 16:06:40'),
(11, 'carro', 'Chevrolet', 'Montana', 'montana.jpg', 1, '2026-10-28 16:06:40'),
(12, 'carro', 'Chevrolet', 'Onix', 'onix.jpg', 1, '2026-10-28 16:06:40'),
(13, 'carro', 'Chevrolet', 'Onix Plus', NULL, 1, '2026-10-28 16:06:40'),
(14, 'carro', 'Chevrolet', 'Prisma', 'prisma.jpg', 1, '2026-10-28 16:06:40'),
(15, 'carro', 'Chevrolet', 'S10', 's10.jpg', 1, '2026-10-28 16:06:40'),
(16, 'carro', 'Chevrolet', 'Spin', NULL, 1, '2026-10-28 16:06:40'),
(17, 'carro', 'Chevrolet', 'Tracker', NULL, 1, '2026-10-28 16:06:40'),
(18, 'carro', 'Chevrolet', 'Trailblazer', NULL, 1, '2026-10-28 16:06:40'),
(19, 'carro', 'Chevrolet', 'Vectra', NULL, 1, '2026-10-28 16:06:40'),
(20, 'carro', 'Chevrolet', 'Zafira', NULL, 1, '2026-10-28 16:06:40'),
(21, 'carro', 'Citroën', 'Aircross', NULL, 1, '2026-10-28 16:06:40'),
(22, 'carro', 'Citroën', 'C3', NULL, 1, '2026-10-28 16:06:40'),
(23, 'carro', 'Citroën', 'C4 Cactus', NULL, 1, '2026-10-28 16:06:40'),
(24, 'carro', 'Citroën', 'C4 Lounge', NULL, 1, '2026-10-28 16:06:40'),
(25, 'carro', 'Citroën', 'Xsara Picasso', NULL, 1, '2026-10-28 16:06:40'),
(26, 'carro', 'Fiat', '147', NULL, 1, '2026-10-28 16:06:40'),
(27, 'carro', 'Fiat', '500', NULL, 1, '2026-10-28 16:06:40'),
(28, 'carro', 'Fiat', 'Argo', NULL, 1, '2026-10-28 16:06:40'),
(29, 'carro', 'Fiat', 'Bravo', NULL, 1, '2026-10-28 16:06:40'),
(30, 'carro', 'Fiat', 'Cronos', NULL, 1, '2026-10-28 16:06:40'),
(31, 'carro', 'Fiat', 'Fastback', NULL, 1, '2026-10-28 16:06:40'),
(32, 'carro', 'Fiat', 'Fiorino', NULL, 1, '2026-10-28 16:06:40'),
(33, 'carro', 'Fiat', 'Grand Siena', NULL, 1, '2026-10-28 16:06:40'),
(34, 'carro', 'Fiat', 'Idea', NULL, 1, '2026-10-28 16:06:40'),
(35, 'carro', 'Fiat', 'Linea', NULL, 1, '2026-10-28 16:06:40'),
(36, 'carro', 'Fiat', 'Marea', NULL, 1, '2026-10-28 16:06:40'),
(37, 'carro', 'Fiat', 'Mobi', NULL, 1, '2026-10-28 16:06:40'),
(38, 'carro', 'Fiat', 'Palio', 'palio.jpg', 1, '2026-10-28 16:06:40'),
(39, 'carro', 'Fiat', 'Pulse', NULL, 1, '2026-10-28 16:06:40'),
(40, 'carro', 'Fiat', 'Punto', NULL, 1, '2026-10-28 16:06:40'),
(41, 'carro', 'Fiat', 'Siena', NULL, 1, '2026-10-28 16:06:40'),
(42, 'carro', 'Fiat', 'Siena EL', 'sienael.jpg', 1, '2026-10-28 16:06:40'),
(43, 'carro', 'Fiat', 'Stilo', NULL, 1, '2026-10-28 16:06:40'),
(44, 'carro', 'Fiat', 'Strada', NULL, 1, '2026-10-28 16:06:40'),
(45, 'carro', 'Fiat', 'Strada Working', 'stradaworking.jpg', 1, '2026-10-28 16:06:40'),
(46, 'carro', 'Fiat', 'Tempra', NULL, 1, '2026-10-28 16:06:40'),
(47, 'carro', 'Fiat', 'Tipo', NULL, 1, '2026-10-28 16:06:40'),
(48, 'carro', 'Fiat', 'Toro', NULL, 1, '2026-10-28 16:06:40'),
(49, 'carro', 'Fiat', 'Uno', NULL, 1, '2026-10-28 16:06:40'),
(50, 'carro', 'Fiat', 'Uno Mille', 'unomille.jpg', 1, '2026-10-28 16:06:40'),
(51, 'carro', 'Ford', 'Bronco', NULL, 1, '2026-10-28 16:06:40'),
(52, 'carro', 'Ford', 'Courier', NULL, 1, '2026-10-28 16:06:40'),
(53, 'carro', 'Ford', 'EcoSport', 'ecosport.jpg', 1, '2026-10-28 16:06:40'),
(54, 'carro', 'Ford', 'Edge', NULL, 1, '2026-10-28 16:06:40'),
(55, 'carro', 'Ford', 'F-250', NULL, 1, '2026-10-28 16:06:40'),
(56, 'carro', 'Ford', 'Fiesta', NULL, 1, '2026-10-28 16:06:40'),
(57, 'carro', 'Ford', 'Fiesta Rocam', 'fiestarocam.jpg', 1, '2026-10-28 16:06:40'),
(58, 'carro', 'Ford', 'Focus', NULL, 1, '2026-10-28 16:06:40'),
(59, 'carro', 'Ford', 'Fusion', NULL, 1, '2026-10-28 16:06:40'),
(60, 'carro', 'Ford', 'Ka', NULL, 1, '2026-10-28 16:06:40'),
(61, 'carro', 'Ford', 'Ka Sedan', NULL, 1, '2026-10-28 16:06:40'),
(62, 'carro', 'Ford', 'Maverick', NULL, 1, '2026-10-28 16:06:40'),
(63, 'carro', 'Ford', 'Mustang', 'mustang.jpg', 1, '2026-10-28 16:06:40'),
(64, 'carro', 'Ford', 'Ranger', 'ranger.jpg', 1, '2026-10-28 16:06:40'),
(65, 'carro', 'Ford', 'Territory', NULL, 1, '2026-10-28 16:06:40'),
(66, 'carro', 'Honda', 'Accord', NULL, 1, '2026-10-28 16:06:40'),
(67, 'carro', 'Honda', 'City', NULL, 1, '2026-10-28 16:06:40'),
(68, 'carro', 'Honda', 'Civic', 'civic.jpg', 1, '2026-10-28 16:06:40'),
(69, 'carro', 'Honda', 'CR-V', NULL, 1, '2026-10-28 16:06:40'),
(70, 'carro', 'Honda', 'CR-Z', NULL, 1, '2026-10-28 16:06:40'),
(71, 'carro', 'Honda', 'Fit', NULL, 1, '2026-10-28 16:06:40'),
(72, 'carro', 'Honda', 'HR-V', NULL, 1, '2026-10-28 16:06:40'),
(73, 'carro', 'Honda', 'WR-V', NULL, 1, '2026-10-28 16:06:40'),
(74, 'carro', 'Hyundai', 'Azera', NULL, 1, '2026-10-28 16:06:40'),
(75, 'carro', 'Hyundai', 'Creta', 'creta.jpg', 1, '2026-10-28 16:06:40'),
(76, 'carro', 'Hyundai', 'Elantra', NULL, 1, '2026-10-28 16:06:40'),
(77, 'carro', 'Hyundai', 'HB20', 'hb20.jpg', 1, '2026-10-28 16:06:40'),
(78, 'carro', 'Hyundai', 'HB20S', NULL, 1, '2026-10-28 16:06:40'),
(79, 'carro', 'Hyundai', 'HB20X', NULL, 1, '2026-10-28 16:06:40'),
(80, 'carro', 'Hyundai', 'i30', NULL, 1, '2026-10-28 16:06:40'),
(81, 'carro', 'Hyundai', 'ix35', NULL, 1, '2026-10-28 16:06:40'),
(82, 'carro', 'Hyundai', 'Santa Fe', NULL, 1, '2026-10-28 16:06:40'),
(83, 'carro', 'Hyundai', 'Tucson', NULL, 1, '2026-10-28 16:06:40'),
(84, 'carro', 'Jeep', 'Cherokee', NULL, 1, '2026-10-28 16:06:40'),
(85, 'carro', 'Jeep', 'Commander', NULL, 1, '2026-10-28 16:06:40'),
(86, 'carro', 'Jeep', 'Compass', 'compass.jpg', 1, '2026-10-28 16:06:40'),
(87, 'carro', 'Jeep', 'Renegade', NULL, 1, '2026-10-28 16:06:40'),
(88, 'carro', 'Jeep', 'Wrangler', NULL, 1, '2026-10-28 16:06:40'),
(89, 'carro', 'Nissan', 'Frontier', NULL, 1, '2026-10-28 16:06:40'),
(90, 'carro', 'Nissan', 'Kicks', NULL, 1, '2026-10-28 16:06:40'),
(91, 'carro', 'Nissan', 'Livina', NULL, 1, '2026-10-28 16:06:40'),
(92, 'carro', 'Nissan', 'March', NULL, 1, '2026-10-28 16:06:40'),
(93, 'carro', 'Nissan', 'Sentra', NULL, 1, '2026-10-28 16:06:40'),
(94, 'carro', 'Nissan', 'Tiida', NULL, 1, '2026-10-28 16:06:40'),
(95, 'carro', 'Nissan', 'Versa', NULL, 1, '2026-10-28 16:06:40'),
(96, 'carro', 'Peugeot', '2008', NULL, 1, '2026-10-28 16:06:40'),
(97, 'carro', 'Peugeot', '206', NULL, 1, '2026-10-28 16:06:40'),
(98, 'carro', 'Peugeot', '207', NULL, 1, '2026-10-28 16:06:40'),
(99, 'carro', 'Peugeot', '208', NULL, 1, '2026-10-28 16:06:40'),
(100, 'carro', 'Peugeot', '3008', NULL, 1, '2026-10-28 16:06:40'),
(101, 'carro', 'Peugeot', '307', NULL, 1, '2026-10-28 16:06:40'),
(102, 'carro', 'Peugeot', '308', NULL, 1, '2026-10-28 16:06:40'),
(103, 'carro', 'Peugeot', '408', NULL, 1, '2026-10-28 16:06:40'),
(104, 'carro', 'Peugeot', 'Partner', NULL, 1, '2026-10-28 16:06:40'),
(105, 'carro', 'Renault', 'Captur', NULL, 1, '2026-10-28 16:06:40'),
(106, 'carro', 'Renault', 'Clio', NULL, 1, '2026-10-28 16:06:40'),
(107, 'carro', 'Renault', 'Duster', 'duster.jpg', 1, '2026-10-28 16:06:40'),
(108, 'carro', 'Renault', 'Fluence', NULL, 1, '2026-10-28 16:06:40'),
(109, 'carro', 'Renault', 'Kwid', NULL, 1, '2026-10-28 16:06:40'),
(110, 'carro', 'Renault', 'Logan', NULL, 1, '2026-10-28 16:06:40'),
(111, 'carro', 'Renault', 'Master', NULL, 1, '2026-10-28 16:06:40'),
(112, 'carro', 'Renault', 'Megane', NULL, 1, '2026-10-28 16:06:40'),
(113, 'carro', 'Renault', 'Oroch', NULL, 1, '2026-10-28 16:06:40'),
(114, 'carro', 'Renault', 'Sandero', NULL, 1, '2026-10-28 16:06:40'),
(115, 'carro', 'Renault', 'Scenic', NULL, 1, '2026-10-28 16:06:40'),
(116, 'carro', 'Renault', 'Symbol', NULL, 1, '2026-10-28 16:06:40'),
(117, 'carro', 'Toyota', 'Corolla', 'corolla.jpg', 1, '2026-10-28 16:06:40'),
(118, 'carro', 'Toyota', 'Corolla Cross', NULL, 1, '2026-10-28 16:06:40'),
(119, 'carro', 'Toyota', 'Etios', NULL, 1, '2026-10-28 16:06:40'),
(120, 'carro', 'Toyota', 'Hilux', 'hilux.jpg', 1, '2026-10-28 16:06:40'),
(121, 'carro', 'Toyota', 'Prius', NULL, 1, '2026-10-28 16:06:40'),
(122, 'carro', 'Toyota', 'RAV4', NULL, 1, '2026-10-28 16:06:40'),
(123, 'carro', 'Toyota', 'SW4', NULL, 1, '2026-10-28 16:06:40'),
(124, 'carro', 'Toyota', 'Yaris', NULL, 1, '2026-10-28 16:06:40'),
(125, 'carro', 'Toyota', 'Yaris Sedan', NULL, 1, '2026-10-28 16:06:40'),
(126, 'carro', 'Volkswagen', 'Amarok', NULL, 1, '2026-10-28 16:06:40'),
(127, 'carro', 'Volkswagen', 'Brasília', 'brasilia.jpg', 1, '2026-10-28 16:06:40'),
(128, 'carro', 'Volkswagen', 'Brasília 1600', 'brasilia1600.jpg', 1, '2026-10-28 16:06:40'),
(129, 'carro', 'Volkswagen', 'Brasília LS', 'brasilials.jpg', 1, '2026-10-28 16:06:40'),
(130, 'carro', 'Volkswagen', 'Fox', NULL, 1, '2026-10-28 16:06:40'),
(131, 'carro', 'Volkswagen', 'Fusca 1200', 'fusca1200.jpg', 1, '2026-10-28 16:06:40'),
(132, 'carro', 'Volkswagen', 'Fusca 1300', 'fusca1300.jpg', 1, '2026-10-28 16:06:40'),
(133, 'carro', 'Volkswagen', 'Fusca 1500', 'fusca1500.jpg', 1, '2026-10-28 16:06:40'),
(134, 'carro', 'Volkswagen', 'Fusca 1600', 'fusca1600.jpg', 1, '2026-10-28 16:06:40'),
(135, 'carro', 'Volkswagen', 'Fusca Itamar', 'fuscaitamar.jpg', 1, '2026-10-28 16:06:40'),
(136, 'carro', 'Volkswagen', 'Gol', 'gol.jpg', 1, '2026-10-28 16:06:40'),
(137, 'carro', 'Volkswagen', 'Gol G4', 'golg4.jpg', 1, '2026-10-28 16:06:40'),
(138, 'carro', 'Volkswagen', 'Gol G5', 'golg5.jpg', 1, '2026-10-28 16:06:40'),
(139, 'carro', 'Volkswagen', 'Golf', NULL, 1, '2026-10-28 16:06:40'),
(140, 'carro', 'Volkswagen', 'Jetta', 'jetta.jpg', 1, '2026-10-28 16:06:40'),
(141, 'carro', 'Volkswagen', 'Nivus', NULL, 1, '2026-10-28 16:06:40'),
(142, 'carro', 'Volkswagen', 'Parati', 'parati.jpg', 1, '2026-10-28 16:06:40'),
(143, 'carro', 'Volkswagen', 'Passat', NULL, 1, '2026-10-28 16:06:40'),
(144, 'carro', 'Volkswagen', 'Polo', NULL, 1, '2026-10-28 16:06:40'),
(145, 'carro', 'Volkswagen', 'Quantum', NULL, 1, '2026-10-28 16:06:40'),
(146, 'carro', 'Volkswagen', 'Santana', NULL, 1, '2026-10-28 16:06:40'),
(147, 'carro', 'Volkswagen', 'Saveiro', 'saveiro.jpg', 1, '2026-10-28 16:06:40'),
(148, 'carro', 'Volkswagen', 'SpaceFox', NULL, 1, '2026-10-28 16:06:40'),
(149, 'carro', 'Volkswagen', 'T-Cross', NULL, 1, '2026-10-28 16:06:40'),
(150, 'carro', 'Volkswagen', 'Taos', NULL, 1, '2026-10-28 16:06:40'),
(151, 'carro', 'Volkswagen', 'Tiguan', NULL, 1, '2026-10-28 16:06:40'),
(152, 'carro', 'Volkswagen', 'Up!', NULL, 1, '2026-10-28 16:06:40'),
(153, 'carro', 'Volkswagen', 'Virtus', NULL, 1, '2026-10-28 16:06:40'),
(154, 'carro', 'Volkswagen', 'Voyage', 'voyage.jpg', 1, '2026-10-28 16:06:40'),
(256, 'moto', 'Aprilia', 'RS 660', NULL, 1, '2026-10-28 16:06:40'),
(257, 'moto', 'Aprilia', 'RSV4', NULL, 1, '2026-10-28 16:06:40'),
(258, 'moto', 'Aprilia', 'Tuareg 660', NULL, 1, '2026-10-28 16:06:40'),
(259, 'moto', 'Aprilia', 'Tuono 660', NULL, 1, '2026-10-28 16:06:40'),
(260, 'moto', 'Aprilia', 'Tuono V4', NULL, 1, '2026-10-28 16:06:40'),
(261, 'moto', 'Bajaj', 'Avenger 160', NULL, 1, '2026-10-28 16:06:40'),
(262, 'moto', 'Bajaj', 'Avenger 220', NULL, 1, '2026-10-28 16:06:40'),
(263, 'moto', 'Bajaj', 'Dominar 160', NULL, 1, '2026-10-28 16:06:40'),
(264, 'moto', 'Bajaj', 'Dominar 200', NULL, 1, '2026-10-28 16:06:40'),
(265, 'moto', 'Bajaj', 'Dominar 250', NULL, 1, '2026-10-28 16:06:40'),
(266, 'moto', 'Bajaj', 'Dominar 400', NULL, 1, '2026-10-28 16:06:40'),
(267, 'moto', 'Bajaj', 'Pulsar N160', NULL, 1, '2026-10-28 16:06:40'),
(268, 'moto', 'Bajaj', 'Pulsar N250', NULL, 1, '2026-10-28 16:06:40'),
(269, 'moto', 'Bajaj', 'Pulsar NS200', NULL, 1, '2026-10-28 16:06:40'),
(270, 'moto', 'Bajaj', 'Pulsar NS400Z', NULL, 1, '2026-10-28 16:06:40'),
(271, 'moto', 'BMW', 'F 750 GS', NULL, 1, '2026-10-28 16:06:40'),
(272, 'moto', 'BMW', 'F 850 GS', 'f850gs.jpg', 1, '2026-10-28 16:06:40'),
(273, 'moto', 'BMW', 'F 900 R', NULL, 1, '2026-10-28 16:06:40'),
(274, 'moto', 'BMW', 'F 900 XR', NULL, 1, '2026-10-28 16:06:40'),
(275, 'moto', 'BMW', 'G 310 GS', NULL, 1, '2026-10-28 16:06:40'),
(276, 'moto', 'BMW', 'G 310 R', NULL, 1, '2026-10-28 16:06:40'),
(277, 'moto', 'BMW', 'R 1250 GS', NULL, 1, '2026-10-28 16:06:40'),
(278, 'moto', 'BMW', 'R 1250 RT', NULL, 1, '2026-10-28 16:06:40'),
(279, 'moto', 'BMW', 'R 18', NULL, 1, '2026-10-28 16:06:40'),
(280, 'moto', 'BMW', 'S 1000 R', NULL, 1, '2026-10-28 16:06:40'),
(281, 'moto', 'BMW', 'S 1000 RR', NULL, 1, '2026-10-28 16:06:40'),
(282, 'moto', 'BMW', 'S 1000 XR', NULL, 1, '2026-10-28 16:06:40'),
(283, 'moto', 'Dafra', 'Apache RTR 150', NULL, 1, '2026-10-28 16:06:40'),
(284, 'moto', 'Dafra', 'Apache RTR 200', NULL, 1, '2026-10-28 16:06:40'),
(285, 'moto', 'Dafra', 'Citycom 300i', NULL, 1, '2026-10-28 16:06:40'),
(286, 'moto', 'Dafra', 'Cruisym 150', NULL, 1, '2026-10-28 16:06:40'),
(287, 'moto', 'Dafra', 'Cruisym 300', NULL, 1, '2026-10-28 16:06:40'),
(288, 'moto', 'Dafra', 'Horizon 150', NULL, 1, '2026-10-28 16:06:40'),
(289, 'moto', 'Dafra', 'Horizon 300', NULL, 1, '2026-10-28 16:06:40'),
(290, 'moto', 'Dafra', 'Maxsym 400', NULL, 1, '2026-10-28 16:06:40'),
(291, 'moto', 'Dafra', 'NH 190', NULL, 1, '2026-10-28 16:06:40'),
(292, 'moto', 'Dafra', 'NH 300', NULL, 1, '2026-10-28 16:06:40'),
(293, 'moto', 'Ducati', 'Diavel 1260', NULL, 1, '2026-10-28 16:06:40'),
(294, 'moto', 'Ducati', 'Diavel V4', NULL, 1, '2026-10-28 16:06:40'),
(295, 'moto', 'Ducati', 'Monster 1200', NULL, 1, '2026-10-28 16:06:40'),
(296, 'moto', 'Ducati', 'Monster 937', NULL, 1, '2026-10-28 16:06:40'),
(297, 'moto', 'Ducati', 'Multistrada V2', NULL, 1, '2026-10-28 16:06:40'),
(298, 'moto', 'Ducati', 'Multistrada V4', NULL, 1, '2026-10-28 16:06:40'),
(299, 'moto', 'Ducati', 'Panigale V2', NULL, 1, '2026-10-28 16:06:40'),
(300, 'moto', 'Ducati', 'Panigale V4', NULL, 1, '2026-10-28 16:06:40'),
(301, 'moto', 'Ducati', 'Streetfighter V2', NULL, 1, '2026-10-28 16:06:40'),
(302, 'moto', 'Ducati', 'Streetfighter V4', NULL, 1, '2026-10-28 16:06:40'),
(303, 'moto', 'Haojue', 'Chopper Road 150', NULL, 1, '2026-10-28 16:06:40'),
(304, 'moto', 'Haojue', 'DK 150', NULL, 1, '2026-10-28 16:06:40'),
(305, 'moto', 'Haojue', 'DK 160', 'dk160.jpg', 1, '2026-10-28 16:06:40'),
(306, 'moto', 'Haojue', 'DL 160', NULL, 1, '2026-10-28 16:06:40'),
(307, 'moto', 'Haojue', 'DR 160', NULL, 1, '2026-10-28 16:06:40'),
(308, 'moto', 'Haojue', 'DR 300', NULL, 1, '2026-10-28 16:06:40'),
(309, 'moto', 'Haojue', 'Lindy 125', NULL, 1, '2026-10-28 16:06:40'),
(310, 'moto', 'Haojue', 'Master Ride 150', NULL, 1, '2026-10-28 16:06:40'),
(311, 'moto', 'Haojue', 'NK 150', NULL, 1, '2026-10-28 16:06:40'),
(312, 'moto', 'Haojue', 'VR 150', NULL, 1, '2026-10-28 16:06:40'),
(313, 'moto', 'Harley-Davidson', 'Fat Bob', NULL, 1, '2026-10-28 16:06:40'),
(314, 'moto', 'Harley-Davidson', 'Fat Boy', NULL, 1, '2026-10-28 16:06:40'),
(315, 'moto', 'Harley-Davidson', 'Iron 883', 'iron883.jpg', 1, '2026-10-28 16:06:40'),
(316, 'moto', 'Harley-Davidson', 'Low Rider S', NULL, 1, '2026-10-28 16:06:40'),
(317, 'moto', 'Harley-Davidson', 'Low Rider ST', NULL, 1, '2026-10-28 16:06:40'),
(318, 'moto', 'Harley-Davidson', 'Nightster', NULL, 1, '2026-10-28 16:06:40'),
(319, 'moto', 'Harley-Davidson', 'Road King', NULL, 1, '2026-10-28 16:06:40'),
(320, 'moto', 'Harley-Davidson', 'Sportster S', NULL, 1, '2026-10-28 16:06:40'),
(321, 'moto', 'Harley-Davidson', 'Street Bob', NULL, 1, '2026-10-28 16:06:40'),
(322, 'moto', 'Harley-Davidson', 'Street Glide', NULL, 1, '2026-10-28 16:06:40'),
(323, 'moto', 'Honda', 'ADV 160', NULL, 1, '2026-10-28 16:06:40'),
(324, 'moto', 'Honda', 'Africa Twin', NULL, 1, '2026-10-28 16:06:40'),
(325, 'moto', 'Honda', 'Biz 110i', NULL, 1, '2026-10-28 16:06:40'),
(326, 'moto', 'Honda', 'Biz 125', 'biz125.jpg', 1, '2026-10-28 16:06:40'),
(327, 'moto', 'Honda', 'CB 1000R', NULL, 1, '2026-10-28 16:06:40'),
(328, 'moto', 'Honda', 'CB 300F Twister', NULL, 1, '2026-10-28 16:06:40'),
(329, 'moto', 'Honda', 'CB 500F', 'cb500f.jpg', 1, '2026-10-28 16:06:40'),
(330, 'moto', 'Honda', 'CB 500X', NULL, 1, '2026-10-28 16:06:40'),
(331, 'moto', 'Honda', 'CB 650R', NULL, 1, '2026-10-28 16:06:40'),
(332, 'moto', 'Honda', 'CBR 650R', NULL, 1, '2026-10-28 16:06:40'),
(333, 'moto', 'Honda', 'CG 125 Fan', 'cg125fan.jpg', 1, '2026-10-28 16:06:40'),
(334, 'moto', 'Honda', 'CG 150 Titan', 'cg150titan.jpg', 1, '2026-10-28 16:06:40'),
(335, 'moto', 'Honda', 'CG 160 Cargo', NULL, 1, '2026-10-28 16:06:40'),
(336, 'moto', 'Honda', 'CG 160 Fan', NULL, 1, '2026-10-28 16:06:40'),
(337, 'moto', 'Honda', 'CG 160 Start', 'cg160start.jpg', 1, '2026-10-28 16:06:40'),
(338, 'moto', 'Honda', 'CG 160 Titan', 'cg160.jpg', 1, '2026-10-28 16:06:40'),
(339, 'moto', 'Honda', 'Elite 125', NULL, 1, '2026-10-28 16:06:40'),
(340, 'moto', 'Honda', 'NXR 160 Bros', 'bros160.jpg', 1, '2026-10-28 16:06:40'),
(341, 'moto', 'Honda', 'PCX 160', NULL, 1, '2026-10-28 16:06:40'),
(342, 'moto', 'Honda', 'Pop 110i', 'pop110i.jpg', 1, '2026-10-28 16:06:40'),
(343, 'moto', 'Honda', 'XRE 190', 'xre190.jpg', 1, '2026-10-28 16:06:40'),
(344, 'moto', 'Honda', 'XRE 300', 'xre300.jpg', 1, '2026-10-28 16:06:40'),
(345, 'moto', 'Indian', 'Chief', NULL, 1, '2026-10-28 16:06:40'),
(346, 'moto', 'Indian', 'Chief Bobber', NULL, 1, '2026-10-28 16:06:40'),
(347, 'moto', 'Indian', 'Chieftain', NULL, 1, '2026-10-28 16:06:40'),
(348, 'moto', 'Indian', 'Scout', NULL, 1, '2026-10-28 16:06:40'),
(349, 'moto', 'Indian', 'Scout Bobber', NULL, 1, '2026-10-28 16:06:40'),
(350, 'moto', 'Kawasaki', 'Ninja 300', NULL, 1, '2026-10-28 16:06:40'),
(351, 'moto', 'Kawasaki', 'Ninja 400', 'ninja400.jpg', 1, '2026-10-28 16:06:40'),
(352, 'moto', 'Kawasaki', 'Ninja 650', NULL, 1, '2026-10-28 16:06:40'),
(353, 'moto', 'Kawasaki', 'Ninja H2', NULL, 1, '2026-10-28 16:06:40'),
(354, 'moto', 'Kawasaki', 'Ninja ZX-10R', NULL, 1, '2026-10-28 16:06:40'),
(355, 'moto', 'Kawasaki', 'Ninja ZX-6R', NULL, 1, '2026-10-28 16:06:40'),
(356, 'moto', 'Kawasaki', 'Versys 1000', NULL, 1, '2026-10-28 16:06:40'),
(357, 'moto', 'Kawasaki', 'Versys 650', NULL, 1, '2026-10-28 16:06:40'),
(358, 'moto', 'Kawasaki', 'Versys-X 300', NULL, 1, '2026-10-28 16:06:40'),
(359, 'moto', 'Kawasaki', 'Vulcan S', NULL, 1, '2026-10-28 16:06:40'),
(360, 'moto', 'Kawasaki', 'Z1000', NULL, 1, '2026-10-28 16:06:40'),
(361, 'moto', 'Kawasaki', 'Z300', NULL, 1, '2026-10-28 16:06:40'),
(362, 'moto', 'Kawasaki', 'Z400', NULL, 1, '2026-10-28 16:06:40'),
(363, 'moto', 'Kawasaki', 'Z650', NULL, 1, '2026-10-28 16:06:40'),
(364, 'moto', 'Kawasaki', 'Z900', NULL, 1, '2026-10-28 16:06:40'),
(365, 'moto', 'KTM', '1290 Super Adventure', NULL, 1, '2026-10-28 16:06:40'),
(366, 'moto', 'KTM', '390 Adventure', NULL, 1, '2026-10-28 16:06:40'),
(367, 'moto', 'KTM', '790 Adventure', NULL, 1, '2026-10-28 16:06:40'),
(368, 'moto', 'KTM', 'Duke 1290', NULL, 1, '2026-10-28 16:06:40'),
(369, 'moto', 'KTM', 'Duke 200', NULL, 1, '2026-10-28 16:06:40'),
(370, 'moto', 'KTM', 'Duke 390', NULL, 1, '2026-10-28 16:06:40'),
(371, 'moto', 'KTM', 'Duke 790', NULL, 1, '2026-10-28 16:06:40'),
(372, 'moto', 'KTM', 'Duke 890', NULL, 1, '2026-10-28 16:06:40'),
(373, 'moto', 'KTM', 'RC 200', NULL, 1, '2026-10-28 16:06:40'),
(374, 'moto', 'KTM', 'RC 390', NULL, 1, '2026-10-28 16:06:40'),
(375, 'moto', 'Moto Guzzi', 'V100 Mandello', NULL, 1, '2026-10-28 16:06:40'),
(376, 'moto', 'Moto Guzzi', 'V7 Stone', NULL, 1, '2026-10-28 16:06:40'),
(377, 'moto', 'Moto Guzzi', 'V85 TT', NULL, 1, '2026-10-28 16:06:40'),
(378, 'moto', 'MV Agusta', 'Brutale 800', NULL, 1, '2026-10-28 16:06:40'),
(379, 'moto', 'MV Agusta', 'Dragster 800', NULL, 1, '2026-10-28 16:06:40'),
(380, 'moto', 'MV Agusta', 'F3 800', NULL, 1, '2026-10-28 16:06:40'),
(381, 'moto', 'MV Agusta', 'Rush 1000', NULL, 1, '2026-10-28 16:06:40'),
(382, 'moto', 'MV Agusta', 'Turismo Veloce 800', NULL, 1, '2026-10-28 16:06:40'),
(383, 'moto', 'Royal Enfield', 'Bullet 350', NULL, 1, '2026-10-28 16:06:40'),
(384, 'moto', 'Royal Enfield', 'Classic 350', NULL, 1, '2026-10-28 16:06:40'),
(385, 'moto', 'Royal Enfield', 'Continental GT 650', NULL, 1, '2026-10-28 16:06:40'),
(386, 'moto', 'Royal Enfield', 'Himalayan 411', NULL, 1, '2026-10-28 16:06:40'),
(387, 'moto', 'Royal Enfield', 'Himalayan 450', NULL, 1, '2026-10-28 16:06:40'),
(388, 'moto', 'Royal Enfield', 'Hunter 350', NULL, 1, '2026-10-28 16:06:40'),
(389, 'moto', 'Royal Enfield', 'Interceptor 650', NULL, 1, '2026-10-28 16:06:40'),
(390, 'moto', 'Royal Enfield', 'Meteor 350', NULL, 1, '2026-10-28 16:06:40'),
(391, 'moto', 'Royal Enfield', 'Shotgun 650', NULL, 1, '2026-10-28 16:06:40'),
(392, 'moto', 'Royal Enfield', 'Super Meteor 650', NULL, 1, '2026-10-28 16:06:40'),
(393, 'moto', 'Shineray', 'Discover 250', NULL, 1, '2026-10-28 16:06:40'),
(394, 'moto', 'Shineray', 'Free 150', NULL, 1, '2026-10-28 16:06:40'),
(395, 'moto', 'Shineray', 'Jet 125', 'jet125.jpg', 1, '2026-10-28 16:06:40'),
(396, 'moto', 'Shineray', 'Jet 50', NULL, 1, '2026-10-28 16:06:40'),
(397, 'moto', 'Shineray', 'Phoenix 50', NULL, 1, '2026-10-28 16:06:40'),
(398, 'moto', 'Shineray', 'SE 175', NULL, 1, '2026-10-28 16:06:40'),
(399, 'moto', 'Shineray', 'SHI 175', NULL, 1, '2026-10-28 16:06:40'),
(400, 'moto', 'Shineray', 'Urban 150', NULL, 1, '2026-10-28 16:06:40'),
(401, 'moto', 'Shineray', 'Worker 125', 'worker125.jpg', 1, '2026-10-28 16:06:40'),
(402, 'moto', 'Shineray', 'XY 250', NULL, 1, '2026-10-28 16:06:40'),
(403, 'moto', 'Suzuki', 'Bandit 1250', NULL, 1, '2026-10-28 16:06:40'),
(404, 'moto', 'Suzuki', 'Bandit 650', NULL, 1, '2026-10-28 16:06:40'),
(405, 'moto', 'Suzuki', 'Boulevard M1500', NULL, 1, '2026-10-28 16:06:40'),
(406, 'moto', 'Suzuki', 'Boulevard M800', NULL, 1, '2026-10-28 16:06:40'),
(407, 'moto', 'Suzuki', 'Burgman 125', NULL, 1, '2026-10-28 16:06:40'),
(408, 'moto', 'Suzuki', 'Burgman 400', NULL, 1, '2026-10-28 16:06:40'),
(409, 'moto', 'Suzuki', 'GSX-R 1000', NULL, 1, '2026-10-28 16:06:40'),
(410, 'moto', 'Suzuki', 'GSX-R 750', NULL, 1, '2026-10-28 16:06:40'),
(411, 'moto', 'Suzuki', 'GSX-S 1000', NULL, 1, '2026-10-28 16:06:40'),
(412, 'moto', 'Suzuki', 'GSX-S 750', NULL, 1, '2026-10-28 16:06:40'),
(413, 'moto', 'Suzuki', 'GSX-S750', 'gsxs750.jpg', 1, '2026-10-28 16:06:40'),
(414, 'moto', 'Suzuki', 'Hayabusa', NULL, 1, '2026-10-28 16:06:40'),
(415, 'moto', 'Suzuki', 'Intruder 125', NULL, 1, '2026-10-28 16:06:40'),
(416, 'moto', 'Suzuki', 'V-Strom 1050', NULL, 1, '2026-10-28 16:06:40'),
(417, 'moto', 'Suzuki', 'V-Strom 650', NULL, 1, '2026-10-28 16:06:40'),
(418, 'moto', 'Suzuki', 'Yes 125', NULL, 1, '2026-10-28 16:06:40'),
(419, 'moto', 'Triumph', 'Bonneville T100', NULL, 1, '2026-10-28 16:06:40'),
(420, 'moto', 'Triumph', 'Bonneville T120', NULL, 1, '2026-10-28 16:06:40'),
(421, 'moto', 'Triumph', 'Scrambler 400 X', NULL, 1, '2026-10-28 16:06:40'),
(422, 'moto', 'Triumph', 'Speed 400', NULL, 1, '2026-10-28 16:06:40'),
(423, 'moto', 'Triumph', 'Speed Triple 1200', NULL, 1, '2026-10-28 16:06:40'),
(424, 'moto', 'Triumph', 'Street Triple 765', NULL, 1, '2026-10-28 16:06:40'),
(425, 'moto', 'Triumph', 'Tiger 1200', NULL, 1, '2026-10-28 16:06:40'),
(426, 'moto', 'Triumph', 'Tiger 900', NULL, 1, '2026-10-28 16:06:40'),
(427, 'moto', 'Triumph', 'Tiger Sport 660', NULL, 1, '2026-10-28 16:06:40'),
(428, 'moto', 'Triumph', 'Trident 660', NULL, 1, '2026-10-28 16:06:40'),
(429, 'moto', 'Yamaha', 'Crosser 150', 'crosser150.jpg', 1, '2026-10-28 16:06:40'),
(430, 'moto', 'Yamaha', 'Factor 125i', NULL, 1, '2026-10-28 16:06:40'),
(431, 'moto', 'Yamaha', 'Factor 150', 'factor150.jpg', 1, '2026-10-28 16:06:40'),
(432, 'moto', 'Yamaha', 'Fazer 250', 'fazer250.jpg', 1, '2026-10-28 16:06:40'),
(433, 'moto', 'Yamaha', 'Fazer 600', NULL, 1, '2026-10-28 16:06:40'),
(434, 'moto', 'Yamaha', 'Fazer FZ15', 'fz15.jpg', 1, '2026-10-28 16:06:40'),
(435, 'moto', 'Yamaha', 'Fazer FZ25', NULL, 1, '2026-10-28 16:06:40'),
(436, 'moto', 'Yamaha', 'Lander 250', 'lander.jpg', 1, '2026-10-28 16:06:40'),
(437, 'moto', 'Yamaha', 'MT-03', 'mt03.jpg', 1, '2026-10-28 16:06:40'),
(438, 'moto', 'Yamaha', 'MT-07', NULL, 1, '2026-10-28 16:06:40'),
(439, 'moto', 'Yamaha', 'MT-09', NULL, 1, '2026-10-28 16:06:40'),
(440, 'moto', 'Yamaha', 'MT-10', NULL, 1, '2026-10-28 16:06:40'),
(441, 'moto', 'Yamaha', 'NMAX 160', NULL, 1, '2026-10-28 16:06:40'),
(442, 'moto', 'Yamaha', 'R1', NULL, 1, '2026-10-28 16:06:40'),
(443, 'moto', 'Yamaha', 'R3', NULL, 1, '2026-10-28 16:06:40'),
(444, 'moto', 'Yamaha', 'R7', NULL, 1, '2026-10-28 16:06:40'),
(445, 'moto', 'Yamaha', 'Super Ténéré 1200', NULL, 1, '2026-10-28 16:06:40'),
(446, 'moto', 'Yamaha', 'Ténéré 700', NULL, 1, '2026-10-28 16:06:40'),
(447, 'moto', 'Yamaha', 'XMAX 250', NULL, 1, '2026-10-28 16:06:40'),
(448, 'moto', 'Yamaha', 'XTZ 125', 'xtz125.jpg', 1, '2026-10-28 16:06:40'),
(449, 'moto', 'Yamaha', 'XTZ 150 Crosser', 'xtz150.jpg', 1, '2026-10-28 16:06:40'),
(511, 'caminhao', 'DAF', 'CF 360', NULL, 1, '2026-10-28 16:06:40'),
(512, 'caminhao', 'DAF', 'CF 410', NULL, 1, '2026-10-28 16:06:40'),
(513, 'caminhao', 'DAF', 'CF 410 4x2', NULL, 1, '2026-10-28 16:06:40'),
(514, 'caminhao', 'DAF', 'CF 410 6x2', NULL, 1, '2026-10-28 16:06:40'),
(515, 'caminhao', 'DAF', 'CF 440', NULL, 1, '2026-10-28 16:06:40'),
(516, 'caminhao', 'DAF', 'CF 440 6x2', NULL, 1, '2026-10-28 16:06:40'),
(517, 'caminhao', 'DAF', 'CF 450', NULL, 1, '2026-10-28 16:06:40'),
(518, 'caminhao', 'DAF', 'CF 450 6x2', NULL, 1, '2026-10-28 16:06:40'),
(519, 'caminhao', 'DAF', 'CF 480', NULL, 1, '2026-10-28 16:06:40'),
(520, 'caminhao', 'DAF', 'CF 480 6x2', NULL, 1, '2026-10-28 16:06:40'),
(521, 'caminhao', 'DAF', 'CF 85', NULL, 1, '2026-10-28 16:06:40'),
(522, 'caminhao', 'DAF', 'CF 85 360', NULL, 1, '2026-10-28 16:06:40'),
(523, 'caminhao', 'DAF', 'CF 85 410', NULL, 1, '2026-10-28 16:06:40'),
(524, 'caminhao', 'DAF', 'XF 105', NULL, 1, '2026-10-28 16:06:40'),
(525, 'caminhao', 'DAF', 'XF 105 510', NULL, 1, '2026-10-28 16:06:40'),
(526, 'caminhao', 'DAF', 'XF 460', NULL, 1, '2026-10-28 16:06:40'),
(527, 'caminhao', 'DAF', 'XF 480', NULL, 1, '2026-10-28 16:06:40'),
(528, 'caminhao', 'DAF', 'XF 480 6x2', NULL, 1, '2026-10-28 16:06:40'),
(529, 'caminhao', 'DAF', 'XF 480 6x4', NULL, 1, '2026-10-28 16:06:40'),
(530, 'caminhao', 'DAF', 'XF 510', NULL, 1, '2026-10-28 16:06:40'),
(531, 'caminhao', 'DAF', 'XF 510 6x2', NULL, 1, '2026-10-28 16:06:40'),
(532, 'caminhao', 'DAF', 'XF 530', 'xf530.jpg', 1, '2026-10-28 16:06:40'),
(533, 'caminhao', 'DAF', 'XF 530 6x2', NULL, 1, '2026-10-28 16:06:40'),
(534, 'caminhao', 'DAF', 'XF 530 6x4', NULL, 1, '2026-10-28 16:06:40'),
(535, 'caminhao', 'Ford', 'Cargo 1119', NULL, 1, '2026-10-28 16:06:40'),
(536, 'caminhao', 'Ford', 'Cargo 1317', NULL, 1, '2026-10-28 16:06:40'),
(537, 'caminhao', 'Ford', 'Cargo 1319', NULL, 1, '2026-10-28 16:06:40'),
(538, 'caminhao', 'Ford', 'Cargo 1519', NULL, 1, '2026-10-28 16:06:40'),
(539, 'caminhao', 'Ford', 'Cargo 1719', NULL, 1, '2026-10-28 16:06:40'),
(540, 'caminhao', 'Ford', 'Cargo 1723', NULL, 1, '2026-10-28 16:06:40'),
(541, 'caminhao', 'Ford', 'Cargo 1729', NULL, 1, '2026-10-28 16:06:40'),
(542, 'caminhao', 'Ford', 'Cargo 1932', NULL, 1, '2026-10-28 16:06:40'),
(543, 'caminhao', 'Ford', 'Cargo 1933', NULL, 1, '2026-10-28 16:06:40'),
(544, 'caminhao', 'Ford', 'Cargo 2422', NULL, 1, '2026-10-28 16:06:40'),
(545, 'caminhao', 'Ford', 'Cargo 2423', NULL, 1, '2026-10-28 16:06:40'),
(546, 'caminhao', 'Ford', 'Cargo 2423 6x2', NULL, 1, '2026-10-28 16:06:40'),
(547, 'caminhao', 'Ford', 'Cargo 2428', NULL, 1, '2026-10-28 16:06:40'),
(548, 'caminhao', 'Ford', 'Cargo 2429', 'cargo.jpg', 1, '2026-10-28 16:06:40'),
(549, 'caminhao', 'Ford', 'Cargo 2429 Basculante', 'cargo2429.jpg', 1, '2026-10-28 16:06:40'),
(550, 'caminhao', 'Ford', 'Cargo 2629', NULL, 1, '2026-10-28 16:06:40'),
(551, 'caminhao', 'Ford', 'Cargo 2629 6x2', NULL, 1, '2026-10-28 16:06:40'),
(552, 'caminhao', 'Ford', 'Cargo 2842', NULL, 1, '2026-10-28 16:06:40'),
(553, 'caminhao', 'Ford', 'Cargo 2842 6x2', NULL, 1, '2026-10-28 16:06:40'),
(554, 'caminhao', 'Ford', 'Cargo 2842 6x4', NULL, 1, '2026-10-28 16:06:40'),
(555, 'caminhao', 'Ford', 'Cargo 3031', NULL, 1, '2026-10-28 16:06:40'),
(556, 'caminhao', 'Ford', 'Cargo 3133', NULL, 1, '2026-10-28 16:06:40'),
(557, 'caminhao', 'Ford', 'Cargo 4532', NULL, 1, '2026-10-28 16:06:40'),
(558, 'caminhao', 'Ford', 'Cargo 816', 'cargo816.jpg', 1, '2026-10-28 16:06:40'),
(559, 'caminhao', 'Ford', 'Cargo 816 4x2', NULL, 1, '2026-10-28 16:06:40'),
(560, 'caminhao', 'Ford', 'F-350', 'f350.jpg', 1, '2026-10-28 16:06:40'),
(561, 'caminhao', 'Ford', 'F-4000', 'f4000.jpg', 1, '2026-10-28 16:06:40'),
(562, 'caminhao', 'Iveco', 'Daily 35-150', 'daily35150.jpg', 1, '2026-10-28 16:06:40'),
(563, 'caminhao', 'Iveco', 'Daily 35-160', NULL, 1, '2026-10-28 16:06:40'),
(564, 'caminhao', 'Iveco', 'Daily 45S17', NULL, 1, '2026-10-28 16:06:40'),
(565, 'caminhao', 'Iveco', 'Daily 45S17 4x2', NULL, 1, '2026-10-28 16:06:40'),
(566, 'caminhao', 'Iveco', 'Daily 70C17', 'daily70c17.jpg', 1, '2026-10-28 16:06:40'),
(567, 'caminhao', 'Iveco', 'Eurocargo 170E22', NULL, 1, '2026-10-28 16:06:40'),
(568, 'caminhao', 'Iveco', 'Hi-Way 440', NULL, 1, '2026-10-28 16:06:40'),
(569, 'caminhao', 'Iveco', 'Hi-Way 440 6x2', NULL, 1, '2026-10-28 16:06:40'),
(570, 'caminhao', 'Iveco', 'Hi-Way 480', NULL, 1, '2026-10-28 16:06:40'),
(571, 'caminhao', 'Iveco', 'S-Way', 'sway.jpg', 1, '2026-10-28 16:06:40'),
(572, 'caminhao', 'Iveco', 'S-Way 460', NULL, 1, '2026-10-28 16:06:40'),
(573, 'caminhao', 'Iveco', 'S-Way 480', NULL, 1, '2026-10-28 16:06:40'),
(574, 'caminhao', 'Iveco', 'S-Way 480 6x2', NULL, 1, '2026-10-28 16:06:40'),
(575, 'caminhao', 'Iveco', 'S-Way 540', NULL, 1, '2026-10-28 16:06:40'),
(576, 'caminhao', 'Iveco', 'S-Way 540 6x4', NULL, 1, '2026-10-28 16:06:40'),
(577, 'caminhao', 'Iveco', 'Tector 11-190', NULL, 1, '2026-10-28 16:06:40'),
(578, 'caminhao', 'Iveco', 'Tector 15-210', NULL, 1, '2026-10-28 16:06:40'),
(579, 'caminhao', 'Iveco', 'Tector 150E21', NULL, 1, '2026-10-28 16:06:40'),
(580, 'caminhao', 'Iveco', 'Tector 17-190', NULL, 1, '2026-10-28 16:06:40'),
(581, 'caminhao', 'Iveco', 'Tector 17-280', NULL, 1, '2026-10-28 16:06:40'),
(582, 'caminhao', 'Iveco', 'Tector 17-280 4x2', NULL, 1, '2026-10-28 16:06:40'),
(583, 'caminhao', 'Iveco', 'Tector 17-280 6x2', NULL, 1, '2026-10-28 16:06:40'),
(584, 'caminhao', 'Iveco', 'Tector 170E22', NULL, 1, '2026-10-28 16:06:40'),
(585, 'caminhao', 'Iveco', 'Tector 24-280', NULL, 1, '2026-10-28 16:06:40'),
(586, 'caminhao', 'Iveco', 'Tector 24-300', NULL, 1, '2026-10-28 16:06:40'),
(587, 'caminhao', 'Iveco', 'Tector 240E25', NULL, 1, '2026-10-28 16:06:40'),
(588, 'caminhao', 'Iveco', 'Tector 240E28', NULL, 1, '2026-10-28 16:06:40'),
(589, 'caminhao', 'Iveco', 'Tector 240E28 6x2', NULL, 1, '2026-10-28 16:06:40'),
(590, 'caminhao', 'Iveco', 'Tector 9-190', NULL, 1, '2026-10-28 16:06:40'),
(591, 'caminhao', 'Mercedes-Benz', '710', 'mb710.jpg', 1, '2026-10-28 16:06:40'),
(592, 'caminhao', 'Mercedes-Benz', 'Accelo 1016', NULL, 1, '2026-10-28 16:06:40'),
(593, 'caminhao', 'Mercedes-Benz', 'Accelo 1016 4x2', NULL, 1, '2026-10-28 16:06:40'),
(594, 'caminhao', 'Mercedes-Benz', 'Accelo 1316', NULL, 1, '2026-10-28 16:06:40'),
(595, 'caminhao', 'Mercedes-Benz', 'Accelo 815', 'accelo815.jpg', 1, '2026-10-28 16:06:40'),
(596, 'caminhao', 'Mercedes-Benz', 'Actros 2546', NULL, 1, '2026-10-28 16:06:40'),
(597, 'caminhao', 'Mercedes-Benz', 'Actros 2548', NULL, 1, '2026-10-28 16:06:40'),
(598, 'caminhao', 'Mercedes-Benz', 'Actros 2651', 'actros.jpg', 1, '2026-10-28 16:06:40'),
(599, 'caminhao', 'Mercedes-Benz', 'Actros 2651 6x4', NULL, 1, '2026-10-28 16:06:40'),
(600, 'caminhao', 'Mercedes-Benz', 'Actros 2653', NULL, 1, '2026-10-28 16:06:40'),
(601, 'caminhao', 'Mercedes-Benz', 'Actros 2653 6x4', NULL, 1, '2026-10-28 16:06:40'),
(602, 'caminhao', 'Mercedes-Benz', 'Actros 2658', NULL, 1, '2026-10-28 16:06:40'),
(603, 'caminhao', 'Mercedes-Benz', 'Atego 1419', NULL, 1, '2026-10-28 16:06:40'),
(604, 'caminhao', 'Mercedes-Benz', 'Atego 1419 4x2', NULL, 1, '2026-10-28 16:06:40'),
(605, 'caminhao', 'Mercedes-Benz', 'Atego 1719', 'atego.jpg', 1, '2026-10-28 16:06:40'),
(606, 'caminhao', 'Mercedes-Benz', 'Atego 1719 Baú', 'atego1719.jpg', 1, '2026-10-28 16:06:40'),
(607, 'caminhao', 'Mercedes-Benz', 'Atego 1725', NULL, 1, '2026-10-28 16:06:40'),
(608, 'caminhao', 'Mercedes-Benz', 'Atego 1726', NULL, 1, '2026-10-28 16:06:40'),
(609, 'caminhao', 'Mercedes-Benz', 'Atego 1729', NULL, 1, '2026-10-28 16:06:40'),
(610, 'caminhao', 'Mercedes-Benz', 'Atego 2426', 'atego2426.jpg', 1, '2026-10-28 16:06:40'),
(611, 'caminhao', 'Mercedes-Benz', 'Atego 2429', NULL, 1, '2026-10-28 16:06:40'),
(612, 'caminhao', 'Mercedes-Benz', 'Atego 2430', NULL, 1, '2026-10-28 16:06:40'),
(613, 'caminhao', 'Mercedes-Benz', 'Atego 2730', NULL, 1, '2026-10-28 16:06:40'),
(614, 'caminhao', 'Mercedes-Benz', 'Atego 3030', NULL, 1, '2026-10-28 16:06:40'),
(615, 'caminhao', 'Mercedes-Benz', 'Axor 1933', NULL, 1, '2026-10-28 16:06:40'),
(616, 'caminhao', 'Mercedes-Benz', 'Axor 2036', NULL, 1, '2026-10-28 16:06:40'),
(617, 'caminhao', 'Mercedes-Benz', 'Axor 2540', NULL, 1, '2026-10-28 16:06:40'),
(618, 'caminhao', 'Mercedes-Benz', 'Axor 2544', NULL, 1, '2026-10-28 16:06:40'),
(619, 'caminhao', 'Mercedes-Benz', 'Axor 2644', NULL, 1, '2026-10-28 16:06:40'),
(620, 'caminhao', 'Mercedes-Benz', 'Axor 3344', NULL, 1, '2026-10-28 16:06:40'),
(621, 'caminhao', 'Scania', 'G 370', NULL, 1, '2026-10-28 16:06:40'),
(622, 'caminhao', 'Scania', 'G 380', NULL, 1, '2026-10-28 16:06:40'),
(623, 'caminhao', 'Scania', 'G 410', NULL, 1, '2026-10-28 16:06:40'),
(624, 'caminhao', 'Scania', 'G 410 6x2', NULL, 1, '2026-10-28 16:06:40'),
(625, 'caminhao', 'Scania', 'G 440', NULL, 1, '2026-10-28 16:06:40'),
(626, 'caminhao', 'Scania', 'G 440 6x4', NULL, 1, '2026-10-28 16:06:40'),
(627, 'caminhao', 'Scania', 'G 450', NULL, 1, '2026-10-28 16:06:40'),
(628, 'caminhao', 'Scania', 'G 450 6x2', NULL, 1, '2026-10-28 16:06:40'),
(629, 'caminhao', 'Scania', 'P 250', NULL, 1, '2026-10-28 16:06:40'),
(630, 'caminhao', 'Scania', 'P 250 4x2', NULL, 1, '2026-10-28 16:06:40'),
(631, 'caminhao', 'Scania', 'P 310', NULL, 1, '2026-10-28 16:06:40'),
(632, 'caminhao', 'Scania', 'P 310 6x2', NULL, 1, '2026-10-28 16:06:40'),
(633, 'caminhao', 'Scania', 'P 320', NULL, 1, '2026-10-28 16:06:40'),
(634, 'caminhao', 'Scania', 'P 340', NULL, 1, '2026-10-28 16:06:40'),
(635, 'caminhao', 'Scania', 'P 360', 'p360.jpg', 1, '2026-10-28 16:06:40'),
(636, 'caminhao', 'Scania', 'P 360 6x2', NULL, 1, '2026-10-28 16:06:40'),
(637, 'caminhao', 'Scania', 'R 410', NULL, 1, '2026-10-28 16:06:40'),
(638, 'caminhao', 'Scania', 'R 420', NULL, 1, '2026-10-28 16:06:40'),
(639, 'caminhao', 'Scania', 'R 440', NULL, 1, '2026-10-28 16:06:40'),
(640, 'caminhao', 'Scania', 'R 450', 'r450.jpg', 1, '2026-10-28 16:06:40'),
(641, 'caminhao', 'Scania', 'R 450 6x2', NULL, 1, '2026-10-28 16:06:40'),
(642, 'caminhao', 'Scania', 'R 480', NULL, 1, '2026-10-28 16:06:40'),
(643, 'caminhao', 'Scania', 'R 480 6x2', NULL, 1, '2026-10-28 16:06:40'),
(644, 'caminhao', 'Scania', 'R 500', NULL, 1, '2026-10-28 16:06:40'),
(645, 'caminhao', 'Scania', 'R 500 6x4', NULL, 1, '2026-10-28 16:06:40'),
(646, 'caminhao', 'Scania', 'R 540', NULL, 1, '2026-10-28 16:06:40'),
(647, 'caminhao', 'Scania', 'R 540 6x4', NULL, 1, '2026-10-28 16:06:40'),
(648, 'caminhao', 'Volkswagen', 'Constellation 19.320', NULL, 1, '2026-10-28 16:06:40'),
(649, 'caminhao', 'Volkswagen', 'Constellation 24.250', NULL, 1, '2026-10-28 16:06:40'),
(650, 'caminhao', 'Volkswagen', 'Constellation 24.280', NULL, 1, '2026-10-28 16:06:40'),
(651, 'caminhao', 'Volkswagen', 'Constellation 24.280 Graneleiro', 'constelacao.jpg', 1, '2026-10-28 16:06:40'),
(652, 'caminhao', 'Volkswagen', 'Constellation 24.330', NULL, 1, '2026-10-28 16:06:40'),
(653, 'caminhao', 'Volkswagen', 'Constellation 25.360', NULL, 1, '2026-10-28 16:06:40'),
(654, 'caminhao', 'Volkswagen', 'Constellation 25.390', NULL, 1, '2026-10-28 16:06:40'),
(655, 'caminhao', 'Volkswagen', 'Constellation 26.280', NULL, 1, '2026-10-28 16:06:40'),
(656, 'caminhao', 'Volkswagen', 'Constellation 26.420', NULL, 1, '2026-10-28 16:06:40'),
(657, 'caminhao', 'Volkswagen', 'Constellation 30.330', NULL, 1, '2026-10-28 16:06:40'),
(658, 'caminhao', 'Volkswagen', 'Constellation 31.330', NULL, 1, '2026-10-28 16:06:40'),
(659, 'caminhao', 'Volkswagen', 'Constellation 31.390', NULL, 1, '2026-10-28 16:06:40'),
(660, 'caminhao', 'Volkswagen', 'Constellation 32.360', NULL, 1, '2026-10-28 16:06:40'),
(661, 'caminhao', 'Volkswagen', 'Constellation 33.460', NULL, 1, '2026-10-28 16:06:40'),
(662, 'caminhao', 'Volkswagen', 'Delivery 11.180', 'delivery11180.jpg', 1, '2026-10-28 16:06:40'),
(663, 'caminhao', 'Volkswagen', 'Delivery 13.180', NULL, 1, '2026-10-28 16:06:40'),
(664, 'caminhao', 'Volkswagen', 'Delivery 13.180 6x2', NULL, 1, '2026-10-28 16:06:40'),
(665, 'caminhao', 'Volkswagen', 'Delivery 17.230', NULL, 1, '2026-10-28 16:06:40'),
(666, 'caminhao', 'Volkswagen', 'Delivery 9.170', 'delivery9170.jpg', 1, '2026-10-28 16:06:40'),
(667, 'caminhao', 'Volkswagen', 'Delivery 9.170 4x2', NULL, 1, '2026-10-28 16:06:40'),
(668, 'caminhao', 'Volkswagen', 'Meteor 28.460', NULL, 1, '2026-10-28 16:06:40'),
(669, 'caminhao', 'Volkswagen', 'Meteor 28.460 6x2', NULL, 1, '2026-10-28 16:06:40'),
(670, 'caminhao', 'Volkswagen', 'Meteor 29.520', 'meteor.jpg', 1, '2026-10-28 16:06:40'),
(671, 'caminhao', 'Volkswagen', 'Meteor 29.520 6x4', NULL, 1, '2026-10-28 16:06:40'),
(672, 'caminhao', 'Volkswagen', 'Worker 13.180', NULL, 1, '2026-10-28 16:06:40'),
(673, 'caminhao', 'Volkswagen', 'Worker 13.180 Caçamba', 'worker13180.jpg', 1, '2026-10-28 16:06:40'),
(674, 'caminhao', 'Volkswagen', 'Worker 15.180', NULL, 1, '2026-10-28 16:06:40'),
(675, 'caminhao', 'Volkswagen', 'Worker 17.230', NULL, 1, '2026-10-28 16:06:40'),
(676, 'caminhao', 'Volkswagen', 'Worker 24.220', NULL, 1, '2026-10-28 16:06:40'),
(677, 'caminhao', 'Volvo', 'FH 400', NULL, 1, '2026-10-28 16:06:40'),
(678, 'caminhao', 'Volvo', 'FH 440', NULL, 1, '2026-10-28 16:06:40'),
(679, 'caminhao', 'Volvo', 'FH 440 6x2', NULL, 1, '2026-10-28 16:06:40'),
(680, 'caminhao', 'Volvo', 'FH 460', NULL, 1, '2026-10-28 16:06:40'),
(681, 'caminhao', 'Volvo', 'FH 460 6x2', NULL, 1, '2026-10-28 16:06:40'),
(682, 'caminhao', 'Volvo', 'FH 480', NULL, 1, '2026-10-28 16:06:40'),
(683, 'caminhao', 'Volvo', 'FH 500', NULL, 1, '2026-10-28 16:06:40'),
(684, 'caminhao', 'Volvo', 'FH 500 6x4', NULL, 1, '2026-10-28 16:06:40'),
(685, 'caminhao', 'Volvo', 'FH 520', NULL, 1, '2026-10-28 16:06:40'),
(686, 'caminhao', 'Volvo', 'FH 540', 'fh540.jpg', 1, '2026-10-28 16:06:40'),
(687, 'caminhao', 'Volvo', 'FH 540 6x4', NULL, 1, '2026-10-28 16:06:40'),
(688, 'caminhao', 'Volvo', 'FM 330', NULL, 1, '2026-10-28 16:06:40'),
(689, 'caminhao', 'Volvo', 'FM 370', NULL, 1, '2026-10-28 16:06:40'),
(690, 'caminhao', 'Volvo', 'FM 380', NULL, 1, '2026-10-28 16:06:40'),
(691, 'caminhao', 'Volvo', 'FM 380 6x2', NULL, 1, '2026-10-28 16:06:40'),
(692, 'caminhao', 'Volvo', 'FM 410', NULL, 1, '2026-10-28 16:06:40'),
(693, 'caminhao', 'Volvo', 'FM 420', NULL, 1, '2026-10-28 16:06:40'),
(694, 'caminhao', 'Volvo', 'FM 460', NULL, 1, '2026-10-28 16:06:40'),
(695, 'caminhao', 'Volvo', 'FM 500', NULL, 1, '2026-10-28 16:06:40'),
(696, 'caminhao', 'Volvo', 'FMX 370', NULL, 1, '2026-10-28 16:06:40'),
(697, 'caminhao', 'Volvo', 'VM 220', NULL, 1, '2026-10-28 16:06:40'),
(698, 'caminhao', 'Volvo', 'VM 270', NULL, 1, '2026-10-28 16:06:40'),
(699, 'caminhao', 'Volvo', 'VM 270 4x2', NULL, 1, '2026-10-28 16:06:40'),
(700, 'caminhao', 'Volvo', 'VM 270 6x2', NULL, 1, '2026-10-28 16:06:40'),
(701, 'caminhao', 'Volvo', 'VM 270 Caçamba', 'vm270.jpg', 1, '2026-10-28 16:06:40'),
(702, 'caminhao', 'Volvo', 'VM 330', 'vm330.jpg', 1, '2026-10-28 16:06:40'),
(703, 'caminhao', 'Volvo', 'VM 330 4x2', NULL, 1, '2026-10-28 16:06:40'),
(704, 'caminhao', 'Volvo', 'VM 330 6x2', NULL, 1, '2026-10-28 16:06:40'),
(766, 'aquatico', 'Artesanal', 'Barco de Fibra', NULL, 1, '2026-10-28 16:06:40'),
(767, 'aquatico', 'Artesanal', 'Canoa de Madeira', NULL, 1, '2026-10-28 16:06:40'),
(768, 'aquatico', 'Azimut', 'Azimut 40', NULL, 1, '2026-10-28 16:06:40'),
(769, 'aquatico', 'Azimut', 'Azimut 45', NULL, 1, '2026-10-28 16:06:40'),
(770, 'aquatico', 'Azimut', 'Azimut 50', NULL, 1, '2026-10-28 16:06:40'),
(771, 'aquatico', 'Azimut', 'Azimut 55', NULL, 1, '2026-10-28 16:06:40'),
(772, 'aquatico', 'Azimut', 'Azimut 60', NULL, 1, '2026-10-28 16:06:40'),
(773, 'aquatico', 'Azimut', 'Azimut 66', NULL, 1, '2026-10-28 16:06:40'),
(774, 'aquatico', 'Azimut', 'Azimut 72', NULL, 1, '2026-10-28 16:06:40'),
(775, 'aquatico', 'Azimut', 'Azimut 78', NULL, 1, '2026-10-28 16:06:40'),
(776, 'aquatico', 'Azimut', 'Azimut 80', NULL, 1, '2026-10-28 16:06:40'),
(777, 'aquatico', 'Azimut', 'Azimut 90', NULL, 1, '2026-10-28 16:06:40'),
(778, 'aquatico', 'Bayliner', 'Bayliner 160', NULL, 1, '2026-10-28 16:06:40'),
(779, 'aquatico', 'Bayliner', 'Bayliner 180', NULL, 1, '2026-10-28 16:06:40'),
(780, 'aquatico', 'Bayliner', 'Bayliner 200', NULL, 1, '2026-10-28 16:06:40'),
(781, 'aquatico', 'Bayliner', 'Bayliner 220', NULL, 1, '2026-10-28 16:06:40'),
(782, 'aquatico', 'Bayliner', 'Bayliner 240', NULL, 1, '2026-10-28 16:06:40'),
(783, 'aquatico', 'Bayliner', 'Bayliner 260', NULL, 1, '2026-10-28 16:06:40'),
(784, 'aquatico', 'Bayliner', 'Bayliner 280', NULL, 1, '2026-10-28 16:06:40'),
(785, 'aquatico', 'Bayliner', 'Bayliner 300', NULL, 1, '2026-10-28 16:06:40'),
(786, 'aquatico', 'Bayliner', 'Bayliner 340', NULL, 1, '2026-10-28 16:06:40'),
(787, 'aquatico', 'Bayliner', 'Bayliner 380', NULL, 1, '2026-10-28 16:06:40'),
(788, 'aquatico', 'Beneteau', 'Beneteau 30', NULL, 1, '2026-10-28 16:06:40'),
(789, 'aquatico', 'Beneteau', 'Beneteau 34', NULL, 1, '2026-10-28 16:06:40'),
(790, 'aquatico', 'Beneteau', 'Beneteau 38', NULL, 1, '2026-10-28 16:06:40'),
(791, 'aquatico', 'Beneteau', 'Beneteau 40', NULL, 1, '2026-10-28 16:06:40'),
(792, 'aquatico', 'Beneteau', 'Beneteau 46', NULL, 1, '2026-10-28 16:06:40'),
(793, 'aquatico', 'Beneteau', 'Beneteau 50', NULL, 1, '2026-10-28 16:06:40'),
(794, 'aquatico', 'Beneteau', 'Beneteau 55', NULL, 1, '2026-10-28 16:06:40'),
(795, 'aquatico', 'Beneteau', 'Beneteau 60', NULL, 1, '2026-10-28 16:06:40'),
(796, 'aquatico', 'Beneteau', 'Beneteau 62', NULL, 1, '2026-10-28 16:06:40'),
(797, 'aquatico', 'Beneteau', 'Beneteau 70', NULL, 1, '2026-10-28 16:06:40'),
(798, 'aquatico', 'Delta', 'Veleiro Pequeno', NULL, 1, '2026-10-28 16:06:40'),
(799, 'aquatico', 'Ferretti', 'Ferretti 1000', NULL, 1, '2026-10-28 16:06:40'),
(800, 'aquatico', 'Ferretti', 'Ferretti 450', NULL, 1, '2026-10-28 16:06:40'),
(801, 'aquatico', 'Ferretti', 'Ferretti 500', NULL, 1, '2026-10-28 16:06:40'),
(802, 'aquatico', 'Ferretti', 'Ferretti 550', NULL, 1, '2026-10-28 16:06:40'),
(803, 'aquatico', 'Ferretti', 'Ferretti 600', NULL, 1, '2026-10-28 16:06:40'),
(804, 'aquatico', 'Ferretti', 'Ferretti 670', NULL, 1, '2026-10-28 16:06:40'),
(805, 'aquatico', 'Ferretti', 'Ferretti 720', NULL, 1, '2026-10-28 16:06:40'),
(806, 'aquatico', 'Ferretti', 'Ferretti 780', NULL, 1, '2026-10-28 16:06:40'),
(807, 'aquatico', 'Ferretti', 'Ferretti 850', NULL, 1, '2026-10-28 16:06:40'),
(808, 'aquatico', 'Ferretti', 'Ferretti 920', NULL, 1, '2026-10-28 16:06:40'),
(809, 'aquatico', 'Fibrafort', 'Focker 190', NULL, 1, '2026-10-28 16:06:40'),
(810, 'aquatico', 'Fibrafort', 'Focker 240', NULL, 1, '2026-10-28 16:06:40'),
(811, 'aquatico', 'Fibrafort', 'Focker 265', NULL, 1, '2026-10-28 16:06:40'),
(812, 'aquatico', 'Fibrafort', 'Focker 330', NULL, 1, '2026-10-28 16:06:40'),
(813, 'aquatico', 'Fibrafort', 'Focker 377', NULL, 1, '2026-10-28 16:06:40'),
(814, 'aquatico', 'Fibrafort', 'Focker 420', NULL, 1, '2026-10-28 16:06:40'),
(815, 'aquatico', 'Fibrafort', 'Focker 440', NULL, 1, '2026-10-28 16:06:40'),
(816, 'aquatico', 'Fibrafort', 'Focker 500', NULL, 1, '2026-10-28 16:06:40'),
(817, 'aquatico', 'Fibrafort', 'Focker 555', NULL, 1, '2026-10-28 16:06:40'),
(818, 'aquatico', 'Fibrafort', 'Focker 600', NULL, 1, '2026-10-28 16:06:40'),
(819, 'aquatico', 'Fishing', 'FS 160 Open', NULL, 1, '2026-10-28 16:06:40'),
(820, 'aquatico', 'Fishing', 'FS 180 Open', NULL, 1, '2026-10-28 16:06:40'),
(821, 'aquatico', 'Fishing', 'FS 200 Open', NULL, 1, '2026-10-28 16:06:40'),
(822, 'aquatico', 'Fishing', 'FS 220 Open', NULL, 1, '2026-10-28 16:06:40'),
(823, 'aquatico', 'Fishing', 'FS 250 Open', NULL, 1, '2026-10-28 16:06:40'),
(824, 'aquatico', 'Fishing', 'FS 280 Open', NULL, 1, '2026-10-28 16:06:40'),
(825, 'aquatico', 'Fishing', 'FS 300 Open', NULL, 1, '2026-10-28 16:06:40'),
(826, 'aquatico', 'Fishing', 'FS 320 Open', NULL, 1, '2026-10-28 16:06:40'),
(827, 'aquatico', 'Fishing', 'FS 350 Open', NULL, 1, '2026-10-28 16:06:40'),
(828, 'aquatico', 'Fishing', 'FS 380 Open', NULL, 1, '2026-10-28 16:06:40'),
(829, 'aquatico', 'Fishing', 'Lancha de Pesca', NULL, 1, '2026-10-28 16:06:40'),
(830, 'aquatico', 'Fishing Raptor', 'Fishing 19', 'fishing19.jpg', 1, '2026-10-28 16:06:40'),
(831, 'aquatico', 'Focker', 'Focker 240', 'focker240.jpg', 1, '2026-10-28 16:06:40'),
(832, 'aquatico', 'Focker', 'FS 160', NULL, 1, '2026-10-28 16:06:40'),
(833, 'aquatico', 'Focker', 'FS 180', NULL, 1, '2026-10-28 16:06:40'),
(834, 'aquatico', 'Focker', 'FS 210', NULL, 1, '2026-10-28 16:06:40'),
(835, 'aquatico', 'Focker', 'FS 230', 'focker230.jpg', 1, '2026-10-28 16:06:40'),
(836, 'aquatico', 'Focker', 'FS 240', NULL, 1, '2026-10-28 16:06:40'),
(837, 'aquatico', 'Focker', 'FS 250', NULL, 1, '2026-10-28 16:06:40'),
(838, 'aquatico', 'Focker', 'FS 290', NULL, 1, '2026-10-28 16:06:40'),
(839, 'aquatico', 'Focker', 'FS 310', NULL, 1, '2026-10-28 16:06:40'),
(840, 'aquatico', 'Focker', 'FS 330', NULL, 1, '2026-10-28 16:06:40'),
(841, 'aquatico', 'Focker', 'FS 360', NULL, 1, '2026-10-28 16:06:40'),
(842, 'aquatico', 'Focker', 'Lancha de Passeio', NULL, 1, '2026-10-28 16:06:40'),
(843, 'aquatico', 'Intermarine', 'Yacht 40', NULL, 1, '2026-10-28 16:06:40'),
(844, 'aquatico', 'Intermarine', 'Yacht 44', NULL, 1, '2026-10-28 16:06:40'),
(845, 'aquatico', 'Intermarine', 'Yacht 48', NULL, 1, '2026-10-28 16:06:40'),
(846, 'aquatico', 'Intermarine', 'Yacht 52', NULL, 1, '2026-10-28 16:06:40'),
(847, 'aquatico', 'Intermarine', 'Yacht 56', NULL, 1, '2026-10-28 16:06:40'),
(848, 'aquatico', 'Intermarine', 'Yacht 60', NULL, 1, '2026-10-28 16:06:40'),
(849, 'aquatico', 'Intermarine', 'Yacht 68', NULL, 1, '2026-10-28 16:06:40'),
(850, 'aquatico', 'Intermarine', 'Yacht 72', NULL, 1, '2026-10-28 16:06:40'),
(851, 'aquatico', 'Intermarine', 'Yacht 80', NULL, 1, '2026-10-28 16:06:40'),
(852, 'aquatico', 'Intermarine', 'Yacht 90', NULL, 1, '2026-10-28 16:06:40'),
(853, 'aquatico', 'Jeanneau', 'Jeanneau 32', NULL, 1, '2026-10-28 16:06:40'),
(854, 'aquatico', 'Jeanneau', 'Jeanneau 36', NULL, 1, '2026-10-28 16:06:40'),
(855, 'aquatico', 'Jeanneau', 'Jeanneau 40', NULL, 1, '2026-10-28 16:06:40'),
(856, 'aquatico', 'Jeanneau', 'Jeanneau 44', NULL, 1, '2026-10-28 16:06:40'),
(857, 'aquatico', 'Jeanneau', 'Jeanneau 47', NULL, 1, '2026-10-28 16:06:40'),
(858, 'aquatico', 'Jeanneau', 'Jeanneau 50', NULL, 1, '2026-10-28 16:06:40'),
(859, 'aquatico', 'Jeanneau', 'Jeanneau 54', NULL, 1, '2026-10-28 16:06:40'),
(860, 'aquatico', 'Jeanneau', 'Jeanneau 60', NULL, 1, '2026-10-28 16:06:40'),
(861, 'aquatico', 'Jeanneau', 'Jeanneau 64', NULL, 1, '2026-10-28 16:06:40'),
(862, 'aquatico', 'Jeanneau', 'Jeanneau 70', NULL, 1, '2026-10-28 16:06:40'),
(863, 'aquatico', 'Kawasaki', 'Jet Ski 1100', NULL, 1, '2026-10-28 16:06:40'),
(864, 'aquatico', 'Kawasaki', 'Jet Ski 900', NULL, 1, '2026-10-28 16:06:40'),
(865, 'aquatico', 'Kawasaki', 'STX 15F', NULL, 1, '2026-10-28 16:06:40'),
(866, 'aquatico', 'Kawasaki', 'STX 160', NULL, 1, '2026-10-28 16:06:40'),
(867, 'aquatico', 'Kawasaki', 'SX-R 160', NULL, 1, '2026-10-28 16:06:40'),
(868, 'aquatico', 'Kawasaki', 'Ultra 160', NULL, 1, '2026-10-28 16:06:40'),
(869, 'aquatico', 'Kawasaki', 'Ultra 310LX', NULL, 1, '2026-10-28 16:06:40'),
(870, 'aquatico', 'Kawasaki', 'Ultra 310R', NULL, 1, '2026-10-28 16:06:40'),
(871, 'aquatico', 'Kawasaki', 'Ultra 310SE', NULL, 1, '2026-10-28 16:06:40'),
(872, 'aquatico', 'Kawasaki', 'Ultra 310X', NULL, 1, '2026-10-28 16:06:40'),
(873, 'aquatico', 'Levefort', 'Barco de Alumínio 6m', NULL, 1, '2026-10-28 16:06:40'),
(874, 'aquatico', 'Levefort', 'Barco de Pesca', NULL, 1, '2026-10-28 16:06:40'),
(875, 'aquatico', 'Mercury', 'Motor de Popa', NULL, 1, '2026-10-28 16:06:40'),
(876, 'aquatico', 'Metalglass', 'Barco de Alumínio', NULL, 1, '2026-10-28 16:06:40'),
(877, 'aquatico', 'NX Boats', 'NX 260', 'nx260.jpg', 1, '2026-10-28 16:06:40'),
(878, 'aquatico', 'NX Boats', 'NX 290', 'nx290.jpg', 1, '2026-10-28 16:06:40'),
(879, 'aquatico', 'Quest', 'Fishing 175', NULL, 1, '2026-10-28 16:06:40'),
(880, 'aquatico', 'Quest', 'Fishing 190', NULL, 1, '2026-10-28 16:06:40'),
(881, 'aquatico', 'Quest', 'Fishing 210', NULL, 1, '2026-10-28 16:06:40'),
(882, 'aquatico', 'Quest', 'Fishing 230', NULL, 1, '2026-10-28 16:06:40'),
(883, 'aquatico', 'Quest', 'Fishing 250', NULL, 1, '2026-10-28 16:06:40'),
(884, 'aquatico', 'Quest', 'Fishing 270', NULL, 1, '2026-10-28 16:06:40'),
(885, 'aquatico', 'Quest', 'Fishing 290', NULL, 1, '2026-10-28 16:06:40'),
(886, 'aquatico', 'Quest', 'Fishing 310', NULL, 1, '2026-10-28 16:06:40'),
(887, 'aquatico', 'Quest', 'Fishing 350', NULL, 1, '2026-10-28 16:06:40'),
(888, 'aquatico', 'Quest', 'Fishing 380', NULL, 1, '2026-10-28 16:06:40'),
(889, 'aquatico', 'Real', 'Fly 200', NULL, 1, '2026-10-28 16:06:40'),
(890, 'aquatico', 'Real', 'Fly 230', NULL, 1, '2026-10-28 16:06:40'),
(891, 'aquatico', 'Real', 'Fly 250', NULL, 1, '2026-10-28 16:06:40'),
(892, 'aquatico', 'Real', 'Fly 280', NULL, 1, '2026-10-28 16:06:40'),
(893, 'aquatico', 'Real', 'Fly 300', NULL, 1, '2026-10-28 16:06:40'),
(894, 'aquatico', 'Real', 'Fly 330', NULL, 1, '2026-10-28 16:06:40'),
(895, 'aquatico', 'Real', 'Fly 360', NULL, 1, '2026-10-28 16:06:40'),
(896, 'aquatico', 'Real', 'Fly 390', NULL, 1, '2026-10-28 16:06:40'),
(897, 'aquatico', 'Real', 'Fly 420', NULL, 1, '2026-10-28 16:06:40'),
(898, 'aquatico', 'Real', 'Fly 450', NULL, 1, '2026-10-28 16:06:40'),
(899, 'aquatico', 'Real Powerboats', 'Real 24', 'real24.jpg', 1, '2026-10-28 16:06:40'),
(900, 'aquatico', 'Real Powerboats', 'Real 29', 'real29.jpg', 1, '2026-10-28 16:06:40'),
(901, 'aquatico', 'Runner', 'Cuddy 180', NULL, 1, '2026-10-28 16:06:40'),
(902, 'aquatico', 'Runner', 'Cuddy 220', NULL, 1, '2026-10-28 16:06:40'),
(903, 'aquatico', 'Runner', 'Cuddy 240', NULL, 1, '2026-10-28 16:06:40'),
(904, 'aquatico', 'Runner', 'Cuddy 260', NULL, 1, '2026-10-28 16:06:40'),
(905, 'aquatico', 'Runner', 'Cuddy 280', NULL, 1, '2026-10-28 16:06:40'),
(906, 'aquatico', 'Runner', 'Cuddy 300', NULL, 1, '2026-10-28 16:06:40'),
(907, 'aquatico', 'Runner', 'Cuddy 330', NULL, 1, '2026-10-28 16:06:40'),
(908, 'aquatico', 'Runner', 'Cuddy 350', NULL, 1, '2026-10-28 16:06:40'),
(909, 'aquatico', 'Runner', 'Cuddy 380', NULL, 1, '2026-10-28 16:06:40'),
(910, 'aquatico', 'Runner', 'Cuddy 420', NULL, 1, '2026-10-28 16:06:40'),
(911, 'aquatico', 'Runner', 'Runner 330', 'runner330.jpg', 1, '2026-10-28 16:06:40');
INSERT INTO `veiculos` (`vecid`, `vectipo`, `vecmarca`, `vecmodelo`, `vecimg`, `vecativo`, `veccriado_em`) VALUES
(912, 'aquatico', 'Schaefer', '250 WA', 'schaefer250.jpg', 1, '2026-10-28 16:06:40'),
(913, 'aquatico', 'Schaefer', 'Phantom 235', NULL, 1, '2026-10-28 16:06:40'),
(914, 'aquatico', 'Schaefer', 'Phantom 265', NULL, 1, '2026-10-28 16:06:40'),
(915, 'aquatico', 'Schaefer', 'Phantom 290', NULL, 1, '2026-10-28 16:06:40'),
(916, 'aquatico', 'Schaefer', 'Phantom 303', 'phantom303.jpg', 1, '2026-10-28 16:06:40'),
(917, 'aquatico', 'Schaefer', 'Phantom 345', NULL, 1, '2026-10-28 16:06:40'),
(918, 'aquatico', 'Schaefer', 'Phantom 375', NULL, 1, '2026-10-28 16:06:40'),
(919, 'aquatico', 'Schaefer', 'Phantom 400', NULL, 1, '2026-10-28 16:06:40'),
(920, 'aquatico', 'Schaefer', 'Phantom 450', NULL, 1, '2026-10-28 16:06:40'),
(921, 'aquatico', 'Schaefer', 'Phantom 500', NULL, 1, '2026-10-28 16:06:40'),
(922, 'aquatico', 'Schaefer', 'Phantom 640', NULL, 1, '2026-10-28 16:06:40'),
(923, 'aquatico', 'Sea Ray', 'Sea Ray 190', NULL, 1, '2026-10-28 16:06:40'),
(924, 'aquatico', 'Sea Ray', 'Sea Ray 210', NULL, 1, '2026-10-28 16:06:40'),
(925, 'aquatico', 'Sea Ray', 'Sea Ray 230', NULL, 1, '2026-10-28 16:06:40'),
(926, 'aquatico', 'Sea Ray', 'Sea Ray 250', NULL, 1, '2026-10-28 16:06:40'),
(927, 'aquatico', 'Sea Ray', 'Sea Ray 270', NULL, 1, '2026-10-28 16:06:40'),
(928, 'aquatico', 'Sea Ray', 'Sea Ray 290', NULL, 1, '2026-10-28 16:06:40'),
(929, 'aquatico', 'Sea Ray', 'Sea Ray 310', NULL, 1, '2026-10-28 16:06:40'),
(930, 'aquatico', 'Sea Ray', 'Sea Ray 350', NULL, 1, '2026-10-28 16:06:40'),
(931, 'aquatico', 'Sea Ray', 'Sea Ray 400', NULL, 1, '2026-10-28 16:06:40'),
(932, 'aquatico', 'Sea Ray', 'Sea Ray 450', NULL, 1, '2026-10-28 16:06:40'),
(933, 'aquatico', 'Sea-Doo', 'GTI 130', NULL, 1, '2026-10-28 16:06:40'),
(934, 'aquatico', 'Sea-Doo', 'GTI SE 130', NULL, 1, '2026-10-28 16:06:40'),
(935, 'aquatico', 'Sea-Doo', 'GTI SE 170', NULL, 1, '2026-10-28 16:06:40'),
(936, 'aquatico', 'Sea-Doo', 'GTR 230', NULL, 1, '2026-10-28 16:06:40'),
(937, 'aquatico', 'Sea-Doo', 'GTR-X 300', NULL, 1, '2026-10-28 16:06:40'),
(938, 'aquatico', 'Sea-Doo', 'GTX 170', NULL, 1, '2026-10-28 16:06:40'),
(939, 'aquatico', 'Sea-Doo', 'GTX 230', NULL, 1, '2026-10-28 16:06:40'),
(940, 'aquatico', 'Sea-Doo', 'GTX Limited 300', NULL, 1, '2026-10-28 16:06:40'),
(941, 'aquatico', 'Sea-Doo', 'Jet Ski Sea-Doo', NULL, 1, '2026-10-28 16:06:40'),
(942, 'aquatico', 'Sea-Doo', 'RXP-X 300', NULL, 1, '2026-10-28 16:06:40'),
(943, 'aquatico', 'Sea-Doo', 'RXT-X 300', NULL, 1, '2026-10-28 16:06:40'),
(944, 'aquatico', 'Solara', 'Solara 330', NULL, 1, '2026-10-28 16:06:40'),
(945, 'aquatico', 'Solara', 'Solara 350', NULL, 1, '2026-10-28 16:06:40'),
(946, 'aquatico', 'Solara', 'Solara 370', NULL, 1, '2026-10-28 16:06:40'),
(947, 'aquatico', 'Solara', 'Solara 400', NULL, 1, '2026-10-28 16:06:40'),
(948, 'aquatico', 'Solara', 'Solara 450', NULL, 1, '2026-10-28 16:06:40'),
(949, 'aquatico', 'Solara', 'Solara 500', NULL, 1, '2026-10-28 16:06:40'),
(950, 'aquatico', 'Solara', 'Solara 550', NULL, 1, '2026-10-28 16:06:40'),
(951, 'aquatico', 'Solara', 'Solara 600', NULL, 1, '2026-10-28 16:06:40'),
(952, 'aquatico', 'Solara', 'Solara 650', NULL, 1, '2026-10-28 16:06:40'),
(953, 'aquatico', 'Solara', 'Solara 700', NULL, 1, '2026-10-28 16:06:40'),
(954, 'aquatico', 'Ventura', 'Open 160', NULL, 1, '2026-10-28 16:06:40'),
(955, 'aquatico', 'Ventura', 'Open 180', NULL, 1, '2026-10-28 16:06:40'),
(956, 'aquatico', 'Ventura', 'Open 200', NULL, 1, '2026-10-28 16:06:40'),
(957, 'aquatico', 'Ventura', 'Open 220', NULL, 1, '2026-10-28 16:06:40'),
(958, 'aquatico', 'Ventura', 'Open 240', NULL, 1, '2026-10-28 16:06:40'),
(959, 'aquatico', 'Ventura', 'Open 260', NULL, 1, '2026-10-28 16:06:40'),
(960, 'aquatico', 'Ventura', 'Open 280', NULL, 1, '2026-10-28 16:06:40'),
(961, 'aquatico', 'Ventura', 'Open 300', NULL, 1, '2026-10-28 16:06:40'),
(962, 'aquatico', 'Ventura', 'Open 330', NULL, 1, '2026-10-28 16:06:40'),
(963, 'aquatico', 'Ventura', 'Open 360', NULL, 1, '2026-10-28 16:06:40'),
(964, 'aquatico', 'Yamaha', 'EX Deluxe', NULL, 1, '2026-10-28 16:06:40'),
(965, 'aquatico', 'Yamaha', 'EXR', NULL, 1, '2026-10-28 16:06:40'),
(966, 'aquatico', 'Yamaha', 'FX Cruiser', NULL, 1, '2026-10-28 16:06:40'),
(967, 'aquatico', 'Yamaha', 'FX Cruiser SVHO', NULL, 1, '2026-10-28 16:06:40'),
(968, 'aquatico', 'Yamaha', 'FX SVHO', NULL, 1, '2026-10-28 16:06:40'),
(969, 'aquatico', 'Yamaha', 'GP 1800', NULL, 1, '2026-10-28 16:06:40'),
(970, 'aquatico', 'Yamaha', 'Jet Ski', NULL, 1, '2026-10-28 16:06:40'),
(971, 'aquatico', 'Yamaha', 'SuperJet', NULL, 1, '2026-10-28 16:06:40'),
(972, 'aquatico', 'Yamaha', 'VX Cruiser', NULL, 1, '2026-10-28 16:06:40'),
(973, 'aquatico', 'Yamaha', 'VX Deluxe', NULL, 1, '2026-10-28 16:06:40'),
(974, 'aquatico', 'Yamaha', 'VX Limited', NULL, 1, '2026-10-28 16:06:40'),
(975, 'aquatico', 'Zefir', 'Bote Inflável', NULL, 1, '2026-10-28 16:06:40'),
(1021, 'mobilia', NULL, 'Aparador - 1,20m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1022, 'mobilia', NULL, 'Aparador - 1,80m x 0,45m', NULL, 1, '2026-10-28 16:06:40'),
(1023, 'mobilia', NULL, 'Aparador com gavetas - 2,00m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1024, 'mobilia', NULL, 'Arara de roupas - 1,20m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1025, 'mobilia', NULL, 'Arara de roupas dupla - 1,80m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1026, 'mobilia', NULL, 'Armário 2,00m x 0,80m', NULL, 1, '2026-10-28 16:06:40'),
(1027, 'mobilia', NULL, 'Armário aéreo - 1,50m x 0,35m', NULL, 1, '2026-10-28 16:06:40'),
(1028, 'mobilia', NULL, 'Armário aéreo - 2,00m x 0,35m', NULL, 1, '2026-10-28 16:06:40'),
(1029, 'mobilia', NULL, 'Armário de banheiro - 0,60m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1030, 'mobilia', NULL, 'Armário de cozinha - 1,20m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1031, 'mobilia', NULL, 'Armário de cozinha inferior - 1,80m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1032, 'mobilia', NULL, 'Armário de cozinha planejado - 2,40m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1033, 'mobilia', NULL, 'Armário de cozinha simples - 1,80m x 0,60m', 'armario_cozinha.jpg', 1, '2026-10-28 16:06:40'),
(1034, 'mobilia', NULL, 'Armário de escritório - 0,90m x 0,45m', NULL, 1, '2026-10-28 16:06:40'),
(1035, 'mobilia', NULL, 'Armário externo - 1,80m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1036, 'mobilia', NULL, 'Armário industrial - 1,80m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1037, 'mobilia', NULL, 'Armário infantil - 1,20m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1038, 'mobilia', NULL, 'Armário multiuso - 1,80m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1039, 'mobilia', NULL, 'Armário multiuso alto - 2,00m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1040, 'mobilia', NULL, 'Armário multiuso baixo - 1,20m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1041, 'mobilia', NULL, 'Armário para churrasqueira - 1,50m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1042, 'mobilia', NULL, 'Armário para documentos - 1,80m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1043, 'mobilia', NULL, 'Armário para ferramentas - 1,80m x 0,60m', 'armario_ferramentas.jpg', 1, '2026-10-28 16:06:40'),
(1044, 'mobilia', NULL, 'Armário para garagem - 2,00m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1045, 'mobilia', NULL, 'Armário para lavanderia - 1,20m x 0,50m', 'armario_lavanderia.jpg', 1, '2026-10-28 16:06:40'),
(1046, 'mobilia', NULL, 'Arquivo com gavetas - 0,80m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1047, 'mobilia', NULL, 'Arquivo de escritório - 0,50m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1048, 'mobilia', NULL, 'Balcão americano - 1,80m x 0,45m', NULL, 1, '2026-10-28 16:06:40'),
(1049, 'mobilia', NULL, 'Balcão caixa - 1,80m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1050, 'mobilia', NULL, 'Balcão de atendimento - 1,80m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1051, 'mobilia', NULL, 'Balcão de cozinha - 1,50m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1052, 'mobilia', NULL, 'Balcão de cozinha - 2,00m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1053, 'mobilia', NULL, 'Balcão de cozinha em L - 2,40m x 1,80m', NULL, 1, '2026-10-28 16:06:40'),
(1054, 'mobilia', NULL, 'Balcão de recepção - 2,00m x 0,80m', NULL, 1, '2026-10-28 16:06:40'),
(1055, 'mobilia', NULL, 'Balcão de recepção em L - 2,40m x 1,60m', NULL, 1, '2026-10-28 16:06:40'),
(1056, 'mobilia', NULL, 'Balcão expositor - 1,50m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1057, 'mobilia', NULL, 'Balcão para eventos - 1,80m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1058, 'mobilia', NULL, 'Balcão para mercearia - 1,80m x 0,60m', 'balcao_mercearia.jpg', 1, '2026-10-28 16:06:40'),
(1059, 'mobilia', NULL, 'Bancada de banheiro - 1,00m x 0,55m', NULL, 1, '2026-10-28 16:06:40'),
(1060, 'mobilia', NULL, 'Bancada de banheiro - 1,50m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1061, 'mobilia', NULL, 'Bancada de cozinha - 2,00m x 0,65m', NULL, 1, '2026-10-28 16:06:40'),
(1062, 'mobilia', NULL, 'Bancada de cozinha - 2,50m x 0,65m', NULL, 1, '2026-10-28 16:06:40'),
(1063, 'mobilia', NULL, 'Bancada de oficina - 2,00m x 0,70m', 'bancada_oficina.jpg', 1, '2026-10-28 16:06:40'),
(1064, 'mobilia', NULL, 'Bancada de oficina - 2,50m x 0,80m', NULL, 1, '2026-10-28 16:06:40'),
(1065, 'mobilia', NULL, 'Bancada de trabalho - 2,00m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1066, 'mobilia', NULL, 'Bancada industrial - 2,50m x 0,80m', NULL, 1, '2026-10-28 16:06:40'),
(1067, 'mobilia', NULL, 'Bancada para churrasqueira - 2,00m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1068, 'mobilia', NULL, 'Bancada para ferramentas - 2,00m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1069, 'mobilia', NULL, 'Banco baú - 1,20m x 0,45m', NULL, 1, '2026-10-28 16:06:40'),
(1070, 'mobilia', NULL, 'Banco de jardim - 1,50m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1071, 'mobilia', NULL, 'Banco de madeira - 1,20m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1072, 'mobilia', NULL, 'Banco de madeira maciça - 1,50m x 0,40m', 'banco_madeira.jpg', 1, '2026-10-28 16:06:40'),
(1073, 'mobilia', NULL, 'Banco para área de churrasqueira - 1,80m x 0,40m', 'banco_churrasqueira.jpg', 1, '2026-10-28 16:06:40'),
(1074, 'mobilia', NULL, 'Banco para entrada - 1,00m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1075, 'mobilia', NULL, 'Banco para jardim - 1,80m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1076, 'mobilia', NULL, 'Banco para varanda - 1,20m x 0,45m', NULL, 1, '2026-10-28 16:06:40'),
(1077, 'mobilia', NULL, 'Banqueta - 0,40m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1078, 'mobilia', NULL, 'Banqueta alta - 0,45m x 0,45m', NULL, 1, '2026-10-28 16:06:40'),
(1079, 'mobilia', NULL, 'Baú decorativo - 1,20m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1080, 'mobilia', NULL, 'Baú externo - 1,20m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1081, 'mobilia', NULL, 'Baú infantil para brinquedos - 0,90m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1082, 'mobilia', NULL, 'Baú para roupas - 1,00m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1083, 'mobilia', NULL, 'Beliche adulto - 2,00m x 1,00m', NULL, 1, '2026-10-28 16:06:40'),
(1084, 'mobilia', NULL, 'Beliche de madeira - 1,90m x 0,90m', 'beliche.jpg', 1, '2026-10-28 16:06:40'),
(1085, 'mobilia', NULL, 'Beliche infantil - 1,90m x 0,90m', NULL, 1, '2026-10-28 16:06:40'),
(1086, 'mobilia', NULL, 'Buffet - 1,80m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1087, 'mobilia', NULL, 'Cadeira de madeira - 0,45m x 0,45m', 'cadeira_madeira.jpg', 1, '2026-10-28 16:06:40'),
(1088, 'mobilia', NULL, 'Cama box casal - 1,88m x 1,38m', NULL, 1, '2026-10-28 16:06:40'),
(1089, 'mobilia', NULL, 'Cama box solteiro - 1,88m x 0,88m', NULL, 1, '2026-10-28 16:06:40'),
(1090, 'mobilia', NULL, 'Cama casal 1,88m x 1,38m', NULL, 1, '2026-10-28 16:06:40'),
(1091, 'mobilia', NULL, 'Cama com baú - 1,98m x 1,58m', NULL, 1, '2026-10-28 16:06:40'),
(1092, 'mobilia', NULL, 'Cama de casal - 1,90m x 1,40m', NULL, 1, '2026-10-28 16:06:40'),
(1093, 'mobilia', NULL, 'Cama de casal de madeira - 1,90m x 1,40m', 'cama_casal.jpg', 1, '2026-10-28 16:06:40'),
(1094, 'mobilia', NULL, 'Cama de solteiro - 1,90m x 0,90m', NULL, 1, '2026-10-28 16:06:40'),
(1095, 'mobilia', NULL, 'Cama de solteiro com gavetas - 1,90m x 0,90m', NULL, 1, '2026-10-28 16:06:40'),
(1096, 'mobilia', NULL, 'Cama king - 1,98m x 1,93m', NULL, 1, '2026-10-28 16:06:40'),
(1097, 'mobilia', NULL, 'Cama queen - 1,98m x 1,58m', NULL, 1, '2026-10-28 16:06:40'),
(1098, 'mobilia', NULL, 'Cama queen 1,98m x 1,58m', NULL, 1, '2026-10-28 16:06:40'),
(1099, 'mobilia', NULL, 'Carrinho auxiliar - 0,80m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1100, 'mobilia', NULL, 'Carrinho bar - 1,00m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1101, 'mobilia', NULL, 'Carrinho de cozinha - 0,80m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1102, 'mobilia', NULL, 'Chaise longue - 1,60m x 0,75m', NULL, 1, '2026-10-28 16:06:40'),
(1103, 'mobilia', NULL, 'Closet modulado - 2,40m x 2,00m', NULL, 1, '2026-10-28 16:06:40'),
(1104, 'mobilia', NULL, 'Closet planejado - 3,00m x 2,50m', NULL, 1, '2026-10-28 16:06:40'),
(1105, 'mobilia', NULL, 'Cômoda 4 gavetas - 0,90m x 0,45m', NULL, 1, '2026-10-28 16:06:40'),
(1106, 'mobilia', NULL, 'Cômoda 6 gavetas - 1,40m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1107, 'mobilia', NULL, 'Cômoda infantil - 0,90m x 0,45m', NULL, 1, '2026-10-28 16:06:40'),
(1108, 'mobilia', NULL, 'Conjunto de mesa e cadeiras para varanda - 1,50m', 'conjunto_varanda.jpg', 1, '2026-10-28 16:06:40'),
(1109, 'mobilia', NULL, 'Criado-mudo - 0,50m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1110, 'mobilia', NULL, 'Criado-mudo com gaveta - 0,60m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1111, 'mobilia', NULL, 'Criado-mudo suspenso - 0,50m x 0,35m', NULL, 1, '2026-10-28 16:06:40'),
(1112, 'mobilia', NULL, 'Cristaleira - 1,00m x 0,45m', NULL, 1, '2026-10-28 16:06:40'),
(1113, 'mobilia', NULL, 'Cristaleira antiga - 1,20m x 0,50m', 'cristaleira.jpg', 1, '2026-10-28 16:06:40'),
(1114, 'mobilia', NULL, 'Cristaleira com vidro - 1,20m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1115, 'mobilia', NULL, 'Despenseiro - 0,60m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1116, 'mobilia', NULL, 'Divã - 1,80m x 0,75m', NULL, 1, '2026-10-28 16:06:40'),
(1117, 'mobilia', NULL, 'Escrivaninha - 1,00m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1118, 'mobilia', NULL, 'Escrivaninha 1,40m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1119, 'mobilia', NULL, 'Escrivaninha com gavetas - 1,40m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1120, 'mobilia', NULL, 'Escrivaninha em L - 1,60m x 1,40m', NULL, 1, '2026-10-28 16:06:40'),
(1121, 'mobilia', NULL, 'Escrivaninha em L - 1,80m x 1,60m', NULL, 1, '2026-10-28 16:06:40'),
(1122, 'mobilia', NULL, 'Escrivaninha infantil - 0,90m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1123, 'mobilia', NULL, 'Espreguiçadeira - 2,00m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1124, 'mobilia', NULL, 'Estação de trabalho - 1,20m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1125, 'mobilia', NULL, 'Estação de trabalho dupla - 2,40m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1126, 'mobilia', NULL, 'Estação de trabalho tripla - 3,60m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1127, 'mobilia', NULL, 'Estante 1,80m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1128, 'mobilia', NULL, 'Estante de canto - 0,80m x 1,80m', NULL, 1, '2026-10-28 16:06:40'),
(1129, 'mobilia', NULL, 'Estante de madeira - 1,80m x 2,20m', NULL, 1, '2026-10-28 16:06:40'),
(1130, 'mobilia', NULL, 'Estante industrial - 1,50m x 2,00m', NULL, 1, '2026-10-28 16:06:40'),
(1131, 'mobilia', NULL, 'Estante industrial - 2,00m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1132, 'mobilia', NULL, 'Estante infantil - 0,80m x 1,20m', NULL, 1, '2026-10-28 16:06:40'),
(1133, 'mobilia', NULL, 'Estante modular - 1,20m x 2,00m', NULL, 1, '2026-10-28 16:06:40'),
(1134, 'mobilia', NULL, 'Estante para estoque - 2,00m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1135, 'mobilia', NULL, 'Estante para estudos - 0,90m x 1,80m', NULL, 1, '2026-10-28 16:06:40'),
(1136, 'mobilia', NULL, 'Estante para garagem - 1,80m x 0,50m', 'estante_garagem.jpg', 1, '2026-10-28 16:06:40'),
(1137, 'mobilia', NULL, 'Estante para livros - 0,80m x 1,80m', NULL, 1, '2026-10-28 16:06:40'),
(1138, 'mobilia', NULL, 'Estante para plantas - 0,70m x 1,60m', NULL, 1, '2026-10-28 16:06:40'),
(1139, 'mobilia', NULL, 'Expositor de produtos - 1,00m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1140, 'mobilia', NULL, 'Fruteira de cozinha - 0,50m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1141, 'mobilia', NULL, 'Gabinete com espelho - 1,20m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1142, 'mobilia', NULL, 'Gabinete de banheiro - 0,80m x 0,45m', NULL, 1, '2026-10-28 16:06:40'),
(1143, 'mobilia', NULL, 'Gabinete de banheiro - 1,00m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1144, 'mobilia', NULL, 'Gaveteiro - 0,45m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1145, 'mobilia', NULL, 'Gaveteiro volante - 0,40m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1146, 'mobilia', NULL, 'Gôndola comercial - 1,80m x 0,80m', NULL, 1, '2026-10-28 16:06:40'),
(1147, 'mobilia', NULL, 'Guarda-roupa 2 portas - 1,20m x 0,55m', NULL, 1, '2026-10-28 16:06:40'),
(1148, 'mobilia', NULL, 'Guarda-roupa 2,20m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1149, 'mobilia', NULL, 'Guarda-roupa 3 portas - 1,80m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1150, 'mobilia', NULL, 'Guarda-roupa 4 portas - 2,40m x 0,65m', NULL, 1, '2026-10-28 16:06:40'),
(1151, 'mobilia', NULL, 'Guarda-roupa 6 portas - 2,80m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1152, 'mobilia', NULL, 'Guarda-roupa com espelho - 2,00m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1153, 'mobilia', NULL, 'Guarda-roupa de madeira - 2,00m x 0,60m', 'guarda_roupa.jpg', 1, '2026-10-28 16:06:40'),
(1154, 'mobilia', NULL, 'Guarda-roupa planejado - 3,00m x 0,65m', NULL, 1, '2026-10-28 16:06:40'),
(1155, 'mobilia', NULL, 'Ilha de cozinha - 1,80m x 0,90m', NULL, 1, '2026-10-28 16:06:40'),
(1156, 'mobilia', NULL, 'Ilha de cozinha - 2,40m x 1,00m', NULL, 1, '2026-10-28 16:06:40'),
(1157, 'mobilia', NULL, 'Mesa 1,80m x 0,90m', NULL, 1, '2026-10-28 16:06:40'),
(1158, 'mobilia', NULL, 'Mesa 2,00m x 1,00m', NULL, 1, '2026-10-28 16:06:40'),
(1159, 'mobilia', NULL, 'Mesa de área externa - 1,80m x 0,90m', NULL, 1, '2026-10-28 16:06:40'),
(1160, 'mobilia', NULL, 'Mesa de artesanato - 1,50m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1161, 'mobilia', NULL, 'Mesa de atendimento - 1,20m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1162, 'mobilia', NULL, 'Mesa de cabeceira - 0,50m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1163, 'mobilia', NULL, 'Mesa de cabeceira suspensa - 0,50m x 0,35m', NULL, 1, '2026-10-28 16:06:40'),
(1164, 'mobilia', NULL, 'Mesa de canto - 0,60m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1165, 'mobilia', NULL, 'Mesa de centro - 1,00m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1166, 'mobilia', NULL, 'Mesa de centro - 1,20m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1167, 'mobilia', NULL, 'Mesa de centro redonda - 0,80m diâmetro', NULL, 1, '2026-10-28 16:06:40'),
(1168, 'mobilia', NULL, 'Mesa de desenho - 1,20m x 0,80m', NULL, 1, '2026-10-28 16:06:40'),
(1169, 'mobilia', NULL, 'Mesa de escritório - 1,20m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1170, 'mobilia', NULL, 'Mesa de escritório - 1,60m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1171, 'mobilia', NULL, 'Mesa de jantar - 1,60m x 0,90m', NULL, 1, '2026-10-28 16:06:40'),
(1172, 'mobilia', NULL, 'Mesa de jantar - 2,00m x 1,00m', NULL, 1, '2026-10-28 16:06:40'),
(1173, 'mobilia', NULL, 'Mesa de jantar oval - 1,80m x 1,00m', NULL, 1, '2026-10-28 16:06:40'),
(1174, 'mobilia', NULL, 'Mesa de jantar redonda - 1,20m diâmetro', NULL, 1, '2026-10-28 16:06:40'),
(1175, 'mobilia', NULL, 'Mesa de jantar redonda - 1,50m diâmetro', NULL, 1, '2026-10-28 16:06:40'),
(1176, 'mobilia', NULL, 'Mesa de madeira para cozinha - 1,50m x 0,80m', 'mesa_madeira.jpg', 1, '2026-10-28 16:06:40'),
(1177, 'mobilia', NULL, 'Mesa de plástico para área externa - 1,20m x 0,80m', 'mesa_plastico.jpg', 1, '2026-10-28 16:06:40'),
(1178, 'mobilia', NULL, 'Mesa de reunião - 2,40m x 1,10m', NULL, 1, '2026-10-28 16:06:40'),
(1179, 'mobilia', NULL, 'Mesa de reunião - 3,00m x 1,20m', NULL, 1, '2026-10-28 16:06:40'),
(1180, 'mobilia', NULL, 'Mesa de varanda - 1,20m x 0,70m', 'mesa_varanda.jpg', 1, '2026-10-28 16:06:40'),
(1181, 'mobilia', NULL, 'Mesa dobrável - 1,20m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1182, 'mobilia', NULL, 'Mesa executiva - 1,80m x 0,80m', NULL, 1, '2026-10-28 16:06:40'),
(1183, 'mobilia', NULL, 'Mesa gamer - 1,20m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1184, 'mobilia', NULL, 'Mesa gamer - 1,50m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1185, 'mobilia', NULL, 'Mesa gamer em L - 1,60m x 1,40m', NULL, 1, '2026-10-28 16:06:40'),
(1186, 'mobilia', NULL, 'Mesa infantil - 0,90m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1187, 'mobilia', NULL, 'Mesa infantil - 1,00m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1188, 'mobilia', NULL, 'Mesa lateral - 0,60m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1189, 'mobilia', NULL, 'Mesa para buffet - 1,80m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1190, 'mobilia', NULL, 'Mesa para computador - 1,20m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1191, 'mobilia', NULL, 'Mesa para consultório - 1,40m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1192, 'mobilia', NULL, 'Mesa para estudos - 1,20m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1193, 'mobilia', NULL, 'Mesa para festas - 2,00m x 0,80m', NULL, 1, '2026-10-28 16:06:40'),
(1194, 'mobilia', NULL, 'Mesa para jardim - 1,50m x 0,80m', NULL, 1, '2026-10-28 16:06:40'),
(1195, 'mobilia', NULL, 'Mesa para notebook - 0,90m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1196, 'mobilia', NULL, 'Mesa para sala de reunião - 2,00m x 1,00m', NULL, 1, '2026-10-28 16:06:40'),
(1197, 'mobilia', NULL, 'Mesa para varanda - 1,20m x 0,70m', NULL, 1, '2026-10-28 16:06:40'),
(1198, 'mobilia', NULL, 'Mesa rústica para sítio - 2,00m x 1,00m', 'mesa_rustica.jpg', 1, '2026-10-28 16:06:40'),
(1199, 'mobilia', NULL, 'Nicho de parede - 0,60m x 0,30m', NULL, 1, '2026-10-28 16:06:40'),
(1200, 'mobilia', NULL, 'Nicho decorativo - 0,80m x 0,30m', NULL, 1, '2026-10-28 16:06:40'),
(1201, 'mobilia', NULL, 'Nicho infantil - 0,60m x 0,30m', NULL, 1, '2026-10-28 16:06:40'),
(1202, 'mobilia', NULL, 'Nicho para banheiro - 0,60m x 0,30m', NULL, 1, '2026-10-28 16:06:40'),
(1203, 'mobilia', NULL, 'Nicho para livros - 1,00m x 0,30m', NULL, 1, '2026-10-28 16:06:40'),
(1204, 'mobilia', NULL, 'Organizador de brinquedos - 1,00m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1205, 'mobilia', NULL, 'Painel decorativo - 3,00m x 0,25m', NULL, 1, '2026-10-28 16:06:40'),
(1206, 'mobilia', NULL, 'Painel para ferramentas - 2,00m x 0,30m', NULL, 1, '2026-10-28 16:06:40'),
(1207, 'mobilia', NULL, 'Painel para TV - 1,80m x 0,20m', NULL, 1, '2026-10-28 16:06:40'),
(1208, 'mobilia', NULL, 'Painel para TV - 2,40m x 0,25m', NULL, 1, '2026-10-28 16:06:40'),
(1209, 'mobilia', NULL, 'Painel ripado - 2,50m x 0,30m', NULL, 1, '2026-10-28 16:06:40'),
(1210, 'mobilia', NULL, 'Paneleiro - 0,60m x 0,55m', NULL, 1, '2026-10-28 16:06:40'),
(1211, 'mobilia', NULL, 'Penteadeira - 1,00m x 0,45m', NULL, 1, '2026-10-28 16:06:40'),
(1212, 'mobilia', NULL, 'Penteadeira camarim - 1,40m x 0,55m', NULL, 1, '2026-10-28 16:06:40'),
(1213, 'mobilia', NULL, 'Penteadeira com espelho - 1,20m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1214, 'mobilia', NULL, 'Pia com gabinete - 1,20m x 0,60m', 'pia_gabinete.jpg', 1, '2026-10-28 16:06:40'),
(1215, 'mobilia', NULL, 'Poltrona - 0,80m x 0,80m', NULL, 1, '2026-10-28 16:06:40'),
(1216, 'mobilia', NULL, 'Poltrona reclinável - 0,90m x 0,90m', NULL, 1, '2026-10-28 16:06:40'),
(1217, 'mobilia', NULL, 'Prateleira comercial - 1,50m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1218, 'mobilia', NULL, 'Prateleira de parede - 1,20m x 0,25m', NULL, 1, '2026-10-28 16:06:40'),
(1219, 'mobilia', NULL, 'Prateleira de parede - 1,80m x 0,30m', NULL, 1, '2026-10-28 16:06:40'),
(1220, 'mobilia', NULL, 'Prateleira reforçada - 2,00m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1221, 'mobilia', NULL, 'Puff quadrado - 0,50m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1222, 'mobilia', NULL, 'Puff redondo - 0,60m diâmetro', NULL, 1, '2026-10-28 16:06:40'),
(1223, 'mobilia', NULL, 'Púlpito de madeira - 0,70m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1224, 'mobilia', NULL, 'Rack com painel - 2,20m x 0,45m', NULL, 1, '2026-10-28 16:06:40'),
(1225, 'mobilia', NULL, 'Rack para TV - 1,60m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1226, 'mobilia', NULL, 'Rack para TV - 2,00m x 0,45m', NULL, 1, '2026-10-28 16:06:40'),
(1227, 'mobilia', NULL, 'Rack simples para televisão - 1,60m x 0,40m', 'rack_tv.jpg', 1, '2026-10-28 16:06:40'),
(1228, 'mobilia', NULL, 'Rack suspenso - 1,80m x 0,35m', NULL, 1, '2026-10-28 16:06:40'),
(1229, 'mobilia', NULL, 'Recamier - 1,40m x 0,55m', NULL, 1, '2026-10-28 16:06:40'),
(1230, 'mobilia', NULL, 'Sapateira horizontal - 1,20m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1231, 'mobilia', NULL, 'Sapateira para entrada - 1,20m x 0,40m', NULL, 1, '2026-10-28 16:06:40'),
(1232, 'mobilia', NULL, 'Sapateira vertical - 0,70m x 1,80m', NULL, 1, '2026-10-28 16:06:40'),
(1233, 'mobilia', NULL, 'Sofá 2 lugares - 1,60m x 0,85m', NULL, 1, '2026-10-28 16:06:40'),
(1234, 'mobilia', NULL, 'Sofá 2,50m x 1,00m', NULL, 1, '2026-10-28 16:06:40'),
(1235, 'mobilia', NULL, 'Sofá 3 lugares - 2,10m x 0,90m', NULL, 1, '2026-10-28 16:06:40'),
(1236, 'mobilia', NULL, 'Sofá 3,00m x 1,00m', NULL, 1, '2026-10-28 16:06:40'),
(1237, 'mobilia', NULL, 'Sofá 4 lugares - 2,60m x 0,95m', NULL, 1, '2026-10-28 16:06:40'),
(1238, 'mobilia', NULL, 'Sofá em L - 2,50m x 1,80m', NULL, 1, '2026-10-28 16:06:40'),
(1239, 'mobilia', NULL, 'Sofá em L - 3,00m x 2,00m', NULL, 1, '2026-10-28 16:06:40'),
(1240, 'mobilia', NULL, 'Sofá reclinável - 2,40m x 1,00m', NULL, 1, '2026-10-28 16:06:40'),
(1241, 'mobilia', NULL, 'Sofá retrátil - 2,20m x 1,00m', NULL, 1, '2026-10-28 16:06:40'),
(1242, 'mobilia', NULL, 'Sofá retrátil - 2,80m x 1,05m', NULL, 1, '2026-10-28 16:06:40'),
(1243, 'mobilia', NULL, 'Tábua de passar com armário - 1,20m x 0,45m', NULL, 1, '2026-10-28 16:06:40'),
(1244, 'mobilia', NULL, 'Torre quente - 0,70m x 0,60m', NULL, 1, '2026-10-28 16:06:40'),
(1245, 'mobilia', NULL, 'Vitrine de loja - 1,20m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1246, 'mobilia', NULL, 'Vitrine de loja - 1,80m x 0,50m', NULL, 1, '2026-10-28 16:06:40'),
(1247, 'mobilia', NULL, 'Vitrine de vidro - 2,00m x 0,60m', NULL, 1, '2026-10-28 16:06:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vendas`
--

CREATE TABLE `vendas` (
  `venid` int(11) NOT NULL,
  `venvec` int(11) DEFAULT NULL,
  `venser` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `vendas`
--

INSERT INTO `vendas` (`venid`, `venvec`, `venser`) VALUES
(1, 68, NULL),
(2, 117, NULL),
(3, 338, NULL),
(4, 437, NULL),
(5, 686, NULL),
(6, 640, NULL),
(7, 835, NULL),
(8, 878, NULL),
(9, 1157, NULL),
(10, 1148, NULL),
(11, 4, NULL),
(12, 351, NULL),
(13, 598, NULL),
(14, 912, NULL),
(15, 1234, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `cargos`
--
ALTER TABLE `cargos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`cliid`),
  ADD UNIQUE KEY `uq_clientes_cpf` (`clicpf`),
  ADD KEY `idx_ip_data` (`cli_ip_cadastro`,`cli_criado_em`),
  ADD KEY `idx_clientes_email` (`cliemail`);

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
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD KEY `cargo_id` (`cargo_id`);

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
-- Índices de tabela `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`serid`),
  ADD KEY `cliid` (`cliid`),
  ADD KEY `idx_servicos_mp_preference` (`sermp_preference_id`),
  ADD KEY `idx_servicos_mp_payment` (`sermp_payment_id`),
  ADD KEY `idx_servicos_data` (`serdata_servico`),
  ADD KEY `vecid` (`vecid`);

--
-- Índices de tabela `tentativas_login`
--
ALTER TABLE `tentativas_login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_chave_data` (`chave`,`criado_em`),
  ADD KEY `idx_criado` (`criado_em`);

--
-- Índices de tabela `veiculos`
--
ALTER TABLE `veiculos`
  ADD PRIMARY KEY (`vecid`),
  ADD UNIQUE KEY `uq_veiculo` (`vectipo`,`vecmarca`,`vecmodelo`),
  ADD KEY `idx_tipo_ativo` (`vectipo`,`vecativo`);

--
-- Índices de tabela `vendas`
--
ALTER TABLE `vendas`
  ADD PRIMARY KEY (`venid`),
  ADD KEY `venser` (`venser`),
  ADD KEY `venvec` (`venvec`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `cargos`
--
ALTER TABLE `cargos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `cliid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

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
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `galeria`
--
ALTER TABLE `galeria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `horarios_ocupados`
--
ALTER TABLE `horarios_ocupados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `serid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT de tabela `tentativas_login`
--
ALTER TABLE `tentativas_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `veiculos`
--
ALTER TABLE `veiculos`
  MODIFY `vecid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1248;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `venid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD CONSTRAINT `funcionarios_ibfk_1` FOREIGN KEY (`cargo_id`) REFERENCES `cargos` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `servicos`
--
ALTER TABLE `servicos`
  ADD CONSTRAINT `servicos_ibfk_1` FOREIGN KEY (`cliid`) REFERENCES `clientes` (`cliid`) ON DELETE CASCADE,
  ADD CONSTRAINT `servicos_ibfk_vec` FOREIGN KEY (`vecid`) REFERENCES `veiculos` (`vecid`) ON DELETE SET NULL;

--
-- Restrições para tabelas `vendas`
--
ALTER TABLE `vendas`
  ADD CONSTRAINT `vendas_ibfk_6` FOREIGN KEY (`venser`) REFERENCES `servicos` (`serid`) ON DELETE SET NULL,
  ADD CONSTRAINT `vendas_ibfk_vec` FOREIGN KEY (`venvec`) REFERENCES `veiculos` (`vecid`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
