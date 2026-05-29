-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 29, 2026 at 01:57 PM
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
  `camplaca` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `camimg` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carros`
--

CREATE TABLE `carros` (
  `carid` int NOT NULL,
  `carmodelo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carmarca` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carplaca` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carimg` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `clitel` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `senha` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clientes`
--

INSERT INTO `clientes` (`cliid`, `clinome`, `clicpf`, `cliendereco`, `cliservico`, `clitel`, `senha`) VALUES
(1, 'Luis Felipe', '573.147.147/57', 'Nova América 27', NULL, '(14) 99675421', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mobilia`
--

CREATE TABLE `mobilia` (
  `mobid` int NOT NULL,
  `mobmedida` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `motos`
--

CREATE TABLE `motos` (
  `motid` int NOT NULL,
  `motmarca` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motmodelo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motplaca` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `motimg` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nauticos`
--

CREATE TABLE `nauticos` (
  `nauid` int NOT NULL,
  `naumodelo` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `naumarca` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nauplaca` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nauimg` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `serdata_servico` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `usuid` int NOT NULL,
  `usulogado` tinyint(1) DEFAULT '0',
  `usulogin` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ususenha` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idcli` int DEFAULT NULL,
  `ususalario` varchar(99) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usutipo` enum('adm') COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usuarios`
--

INSERT INTO `usuarios` (`usuid`, `usulogado`, `usulogin`, `ususenha`, `idcli`, `ususalario`, `usutipo`) VALUES
(1, 0, 'Guilherme Soares', '1234', NULL, NULL, 'adm');

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
  ADD KEY `cliid` (`cliid`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuid`),
  ADD KEY `fk_usuario_cliente` (`idcli`);

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
  MODIFY `camid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carros`
--
ALTER TABLE `carros`
  MODIFY `carid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clientes`
--
ALTER TABLE `clientes`
  MODIFY `cliid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `mobilia`
--
ALTER TABLE `mobilia`
  MODIFY `mobid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `motos`
--
ALTER TABLE `motos`
  MODIFY `motid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nauticos`
--
ALTER TABLE `nauticos`
  MODIFY `nauid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `servicos`
--
ALTER TABLE `servicos`
  MODIFY `serid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuid` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vendas`
--
ALTER TABLE `vendas`
  MODIFY `venid` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `servicos`
--
ALTER TABLE `servicos`
  ADD CONSTRAINT `servicos_ibfk_1` FOREIGN KEY (`cliid`) REFERENCES `clientes` (`cliid`);

--
-- Constraints for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_cliente` FOREIGN KEY (`idcli`) REFERENCES `clientes` (`cliid`);

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
