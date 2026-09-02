-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 02, 2026 at 11:22 AM
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
  `cammarca` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `camelo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `camplaca` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `camimg` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `caminhoes`
--

INSERT INTO `caminhoes` (`camid`, `cammarca`, `camelo`, `camplaca`, `camimg`) VALUES
(1, 'Mercedes-Benz', 'Actros 2651', 'CAM1A23', 'actros.jpg'),
(2, 'Volvo', 'FH 540', 'CAM2B34', 'fh540.jpg'),
(3, 'Scania', 'R 450', 'CAM3C45', 'r450.jpg'),
(4, 'Volkswagen', 'Meteor 29.520', 'CAM4D56', 'meteor.jpg'),
(5, 'DAF', 'XF 530', 'CAM5E67', 'xf530.jpg'),
(6, 'Iveco', 'S-Way', 'CAM6F78', 'sway.jpg'),
(7, 'Mercedes-Benz', 'Atego 1719', 'CAM7G89', 'atego.jpg'),
(8, 'Volvo', 'VM 330', 'CAM8H90', 'vm330.jpg'),
(9, 'Scania', 'P 360', 'CAM9I12', 'p360.jpg'),
(10, 'Ford', 'Cargo 2429', 'CAM0J34', 'cargo.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `carros`
--

CREATE TABLE `carros` (
  `carid` int NOT NULL,
  `carmodelo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carmarca` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carplaca` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carimg` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `carros`
--

INSERT INTO `carros` (`carid`, `carmodelo`, `carmarca`, `carplaca`, `carimg`) VALUES
(1, 'Civic', 'Honda', 'ABC1D23', 'civic.jpg'),
(2, 'Corolla', 'Toyota', 'DEF4G56', 'corolla.jpg'),
(3, 'Onix', 'Chevrolet', 'GHI7J89', 'onix.jpg'),
(4, 'Gol', 'Volkswagen', 'JKL1M23', 'gol.jpg'),
(5, 'HB20', 'Hyundai', 'MNO4P56', 'hb20.jpg'),
(6, 'Camaro', 'Chevrolet', 'QRS7T89', 'camaro.jpg'),
(7, 'Mustang', 'Ford', 'UVW1X23', 'mustang.jpg'),
(8, 'Jetta', 'Volkswagen', 'YZA4B56', 'jetta.jpg'),
(9, 'Creta', 'Hyundai', 'CDE7F89', 'creta.jpg'),
(10, 'Compass', 'Jeep', 'GHI1J24', 'compass.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `cliid` int NOT NULL,
  `clinome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clicpf` varchar(14) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliendereco` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cliservico` int DEFAULT NULL,
  `clitel` varchar(13) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `clisenha` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipocliente` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clientes`
--

INSERT INTO `clientes` (`cliid`, `clinome`, `clicpf`, `cliendereco`, `cliservico`, `clitel`, `clisenha`, `tipocliente`) VALUES
(9, 'Dariel', NULL, NULL, NULL, NULL, 'admin123', 'funcionario'),
(10, 'admin', NULL, NULL, NULL, NULL, 'admin123', 'funcionario'),
(14, 'Guilherme Soares', '49467262843', 'Piraju', NULL, '(14) 99617-56', '$2y$10$gKJWuhlaDItdbB2CjQXqxOYf.q/bS7ih2eahI7EgxDmq3QAHo0Z6a', 'cliente'),
(15, 'Anna Julia', '49154297842', 'Piraju', NULL, '14998581848', '$2y$10$SOcXs1MM4W7fVD.oBv.rC.8RNsmRUgfHsbPKPBK9aETbF.5IcGEaS', 'cliente'),
(16, 'Carlos Henrique', '12345678901', 'Rua das Flores, 100 - Piraju', NULL, '14990000001', '$2y$10$exemplo', 'cliente'),
(17, 'Mariana Oliveira', '23456789012', 'Rua São Paulo, 250 - Piraju', NULL, '14990000002', '$2y$10$exemplo', 'cliente'),
(18, 'Rafael Santos', '34567890123', 'Avenida Brasil, 500 - Piraju', NULL, '14990000003', '$2y$10$exemplo', 'cliente'),
(19, 'Juliana Costa', '45678901234', 'Rua Paraná, 80 - Piraju', NULL, '14990000004', '$2y$10$exemplo', 'cliente'),
(20, 'Lucas Almeida', '56789012345', 'Rua Minas Gerais, 120 - Piraju', NULL, '14990000005', '$2y$10$exemplo', 'cliente'),
(21, 'Fernanda Martins', '67890123456', 'Rua Bahia, 350 - Piraju', NULL, '14990000006', '$2y$10$exemplo', 'cliente'),
(22, 'Bruno Ferreira', '78901234567', 'Rua Goiás, 75 - Piraju', NULL, '14990000007', '$2y$10$exemplo', 'cliente'),
(23, 'Camila Rodrigues', '89012345678', 'Rua Paraná, 410 - Piraju', NULL, '14990000008', '$2y$10$exemplo', 'cliente'),
(24, 'Diego Souza', '90123456789', 'Rua São João, 90 - Piraju', NULL, '14990000009', '$2y$10$exemplo', 'cliente'),
(25, 'Patricia Lima', '01234567890', 'Avenida Principal, 700 - Piraju', NULL, '14990000010', '$2y$10$exemplo', 'cliente');

-- --------------------------------------------------------

--
-- Table structure for table `horarios_ocupados`
--

CREATE TABLE `horarios_ocupados` (
  `id` int NOT NULL,
  `data` date NOT NULL,
  `horario` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
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
  `mobmedida` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `mobilia`
--

INSERT INTO `mobilia` (`mobid`, `mobmedida`) VALUES
(1, 'Mesa 1,80m x 0,90m'),
(2, 'Mesa 2,00m x 1,00m'),
(3, 'Armário 2,00m x 0,80m'),
(4, 'Guarda-roupa 2,20m x 0,60m'),
(5, 'Sofá 2,50m x 1,00m'),
(6, 'Sofá 3,00m x 1,00m'),
(7, 'Cama casal 1,88m x 1,38m'),
(8, 'Cama queen 1,98m x 1,58m'),
(9, 'Estante 1,80m x 0,40m'),
(10, 'Escrivaninha 1,40m x 0,60m');

-- --------------------------------------------------------

--
-- Table structure for table `motos`
--

CREATE TABLE `motos` (
  `motid` int NOT NULL,
  `motmarca` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motmodelo` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motplaca` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motimg` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `motos`
--

INSERT INTO `motos` (`motid`, `motmarca`, `motmodelo`, `motplaca`, `motimg`) VALUES
(1, 'Honda', 'CG 160 Titan', 'MOT1A23', 'cg160.jpg'),
(2, 'Honda', 'CB 500F', 'MOT2B34', 'cb500f.jpg'),
(3, 'Yamaha', 'MT-03', 'MOT3C45', 'mt03.jpg'),
(4, 'Yamaha', 'Fazer 250', 'MOT4D56', 'fazer250.jpg'),
(5, 'Honda', 'Biz 125', 'MOT5E67', 'biz125.jpg'),
(6, 'Yamaha', 'Lander 250', 'MOT6F78', 'lander.jpg'),
(7, 'Kawasaki', 'Ninja 400', 'MOT7G89', 'ninja400.jpg'),
(8, 'BMW', 'F 850 GS', 'MOT8H90', 'f850gs.jpg'),
(9, 'Harley-Davidson', 'Iron 883', 'MOT9I12', 'iron883.jpg'),
(10, 'Suzuki', 'GSX-S750', 'MOT0J34', 'gsxs750.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `nauticos`
--

CREATE TABLE `nauticos` (
  `nauid` int NOT NULL,
  `naumodelo` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `naumarca` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nauplaca` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nauimg` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `nauticos`
--

INSERT INTO `nauticos` (`nauid`, `naumodelo`, `naumarca`, `nauplaca`, `nauimg`) VALUES
(1, 'FS 230', 'Focker', 'NAU001', 'focker230.jpg'),
(2, '250 WA', 'Schaefer', 'NAU002', 'schaefer250.jpg'),
(3, 'Real 24', 'Real Powerboats', 'NAU003', 'real24.jpg'),
(4, 'NX 290', 'NX Boats', 'NAU004', 'nx290.jpg'),
(5, 'Fishing 19', 'Fishing Raptor', 'NAU005', 'fishing19.jpg'),
(6, 'Runner 330', 'Runner', 'NAU006', 'runner330.jpg'),
(7, 'Phantom 303', 'Schaefer', 'NAU007', 'phantom303.jpg'),
(8, 'Focker 240', 'Focker', 'NAU008', 'focker240.jpg'),
(9, 'NX 260', 'NX Boats', 'NAU009', 'nx260.jpg'),
(10, 'Real 29', 'Real Powerboats', 'NAU010', 'real29.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `servicos`
--

CREATE TABLE `servicos` (
  `serid` int NOT NULL,
  `cliid` int NOT NULL,
  `tipo_servico` enum('carro','moto','caminhao','aquatico','mobilia') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `serdescricao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `servalor` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `serstatus_pagamento` enum('pendente','aprovado','rejeitado','cancelado','estornado') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente',
  `sermp_preference_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sermp_payment_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sermp_metodo_pagamento` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serdata_pagamento` datetime DEFAULT NULL,
  `serdata_servico` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `servicos`
--

INSERT INTO `servicos` (`serid`, `cliid`, `tipo_servico`, `serdescricao`, `servalor`, `serstatus_pagamento`, `sermp_preference_id`, `sermp_payment_id`, `sermp_metodo_pagamento`, `serdata_pagamento`, `serdata_servico`) VALUES
(7, 14, 'carro', 'Veículo: Camaro | pintar de preto | Acabamento: Brilhante', '920.00', 'pendente', NULL, NULL, NULL, NULL, '2026-07-15 10:00:00'),
(8, 14, 'carro', 'Veículo: Fusca | pintar de preto | Acabamento: Brilhante', '920.00', 'pendente', NULL, NULL, NULL, NULL, '2026-07-15 11:00:00'),
(9, 14, 'carro', 'Veículo: 3432 | 32ewfdsfsd | Acabamento: Brilhante', '920.00', 'pendente', NULL, NULL, NULL, NULL, '2026-07-27 09:00:00'),
(10, 15, 'carro', 'Veículo: carro | pintar de rosa', '800.00', 'pendente', NULL, NULL, NULL, NULL, '2026-08-27 08:00:00'),
(11, 15, 'carro', 'Veículo: carro | pintar de rosa', '800.00', 'pendente', NULL, NULL, NULL, NULL, '2026-08-27 08:30:00'),
(12, 15, 'carro', 'Veículo: sdasda | sadsad', '800.00', 'pendente', NULL, NULL, NULL, NULL, '2026-08-20 16:00:00'),
(13, 16, 'carro', 'Veículo: Honda Civic | Pintura completa | Acabamento brilhante', '1200.00', 'aprovado', NULL, NULL, 'pix', '2026-09-01 10:00:00', '2026-09-03 08:00:00'),
(14, 17, 'carro', 'Veículo: Toyota Corolla | Polimento completo | Acabamento brilhante', '650.00', 'pendente', NULL, NULL, NULL, NULL, '2026-09-03 09:00:00'),
(15, 18, 'moto', 'Veículo: Honda CG 160 | Pintura completa | Acabamento brilhante', '700.00', 'aprovado', NULL, NULL, 'pix', '2026-09-02 09:30:00', '2026-09-04 08:00:00'),
(16, 19, 'moto', 'Veículo: Yamaha MT-03 | Pintura do tanque e carenagem', '550.00', 'pendente', NULL, NULL, NULL, NULL, '2026-09-04 09:00:00'),
(17, 20, 'caminhao', 'Veículo: Volvo FH 540 | Pintura completa da cabine', '3500.00', 'aprovado', NULL, NULL, 'cartao', '2026-09-02 11:00:00', '2026-09-05 08:00:00'),
(18, 21, 'caminhao', 'Veículo: Scania R 450 | Pintura externa completa', '4200.00', 'pendente', NULL, NULL, NULL, NULL, '2026-09-05 09:00:00'),
(19, 22, 'aquatico', 'Embarcação: Focker FS 230 | Pintura e revitalização', '2800.00', 'aprovado', NULL, NULL, 'pix', '2026-09-02 14:00:00', '2026-09-06 09:00:00'),
(20, 23, 'aquatico', 'Embarcação: NX 290 | Polimento e acabamento náutico', '1800.00', 'pendente', NULL, NULL, NULL, NULL, '2026-09-06 10:00:00'),
(21, 24, 'mobilia', 'Móvel: Mesa | Pintura e restauração', '450.00', 'aprovado', NULL, NULL, 'pix', '2026-09-02 15:00:00', '2026-09-07 08:00:00'),
(22, 25, 'mobilia', 'Móvel: Guarda-roupa | Restauração e pintura', '800.00', 'pendente', NULL, NULL, NULL, NULL, '2026-09-07 10:00:00'),
(23, 16, 'carro', 'Veículo: Chevrolet Camaro | Pintura preta metálica', '1800.00', 'pendente', NULL, NULL, NULL, NULL, '2026-09-08 09:00:00'),
(24, 17, 'moto', 'Veículo: Kawasaki Ninja 400 | Pintura personalizada', '950.00', 'aprovado', NULL, NULL, 'pix', '2026-09-02 16:00:00', '2026-09-08 14:00:00'),
(25, 18, 'caminhao', 'Veículo: Mercedes-Benz Actros | Pintura completa', '4800.00', 'pendente', NULL, NULL, NULL, NULL, '2026-09-09 08:00:00'),
(26, 19, 'aquatico', 'Embarcação: Schaefer 250 WA | Pintura externa', '3200.00', 'pendente', NULL, NULL, NULL, NULL, '2026-09-09 10:00:00'),
(27, 20, 'mobilia', 'Móvel: Sofá | Reforma e pintura estrutural', '600.00', 'aprovado', NULL, NULL, 'cartao', '2026-09-02 17:00:00', '2026-09-10 09:00:00');

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
  MODIFY `camid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `carros`
--
ALTER TABLE `carros`
  MODIFY `carid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `cliid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `horarios_ocupados`
--
ALTER TABLE `horarios_ocupados`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `mobilia`
--
ALTER TABLE `mobilia`
  MODIFY `mobid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `motos`
--
ALTER TABLE `motos`
  MODIFY `motid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `nauticos`
--
ALTER TABLE `nauticos`
  MODIFY `nauid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `servicos`
--
ALTER TABLE `servicos`
  MODIFY `serid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

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
