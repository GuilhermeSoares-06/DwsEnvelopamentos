-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 17, 2026 at 02:45 PM
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
-- Database: `dws_banco`
--

-- --------------------------------------------------------

--
-- Table structure for table `caminhoes`
--

CREATE TABLE `caminhoes` (
  `camid` int NOT NULL,
  `cammarca` varchar(50) DEFAULT NULL,
  `camelo` varchar(50) DEFAULT NULL,
  `camplaca` varchar(10) DEFAULT NULL,
  `camimg` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carros`
--

CREATE TABLE `carros` (
  `carid` int NOT NULL,
  `carmodelo` varchar(50) DEFAULT NULL,
  `carmarca` varchar(50) DEFAULT NULL,
  `carplaca` varchar(10) DEFAULT NULL,
  `carimg` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clientes`
--

CREATE TABLE `clientes` (
  `cliid` int NOT NULL,
  `clinome` varchar(255) DEFAULT NULL,
  `clicpf` varchar(14) DEFAULT NULL,
  `cliendereco` varchar(100) DEFAULT NULL,
  `cliservico` int DEFAULT NULL,
  `clitel` varchar(13) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `mobilia`
--

CREATE TABLE `mobilia` (
  `mobid` int NOT NULL,
  `mobmedida` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `motos`
--

CREATE TABLE `motos` (
  `motid` int NOT NULL,
  `motmarca` varchar(50) DEFAULT NULL,
  `motmodelo` varchar(50) DEFAULT NULL,
  `motplaca` varchar(10) DEFAULT NULL,
  `motimg` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nauticos`
--

CREATE TABLE `nauticos` (
  `barid` int NOT NULL,
  `barmarca` varchar(50) DEFAULT NULL,
  `barmodelo` varchar(50) DEFAULT NULL,
  `barplaca` varchar(10) DEFAULT NULL,
  `barimg` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `servicos`
--

CREATE TABLE `servicos` (
  `serid` int NOT NULL,
  `cliid` int NOT NULL,
  `carid` int DEFAULT NULL,
  `motid` int DEFAULT NULL,
  `camid` int DEFAULT NULL,
  `barid` int DEFAULT NULL,
  `mobid` int DEFAULT NULL,
  `tipo_servico` enum('carro','moto','caminhao','aquatico','mobilia') NOT NULL,
  `serdescricao` varchar(255) NOT NULL,
  `servalor` varchar(20) NOT NULL,
  `serdata_servico` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usuarios`
--

CREATE TABLE `usuarios` (
  `usuid` int NOT NULL,
  `usu_tipo` enum('adm','cliente') NOT NULL DEFAULT 'cliente',
  `usulogado` tinyint(1) DEFAULT '0',
  `usulogin` varchar(150) DEFAULT NULL,
  `ususenha` varchar(150) DEFAULT NULL,
  `idcli` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
  ADD PRIMARY KEY (`barid`);

--
-- Indexes for table `servicos`
--
ALTER TABLE `servicos`
  ADD PRIMARY KEY (`serid`),
  ADD KEY `cliid` (`cliid`),
  ADD KEY `carid` (`carid`),
  ADD KEY `motid` (`motid`),
  ADD KEY `camid` (`camid`),
  ADD KEY `barid` (`barid`),
  ADD KEY `mobid` (`mobid`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuid`),
  ADD KEY `fk_usuario_cliente` (`idcli`);

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
  MODIFY `cliid` int NOT NULL AUTO_INCREMENT;

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
  MODIFY `barid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `servicos`
--
ALTER TABLE `servicos`
  MODIFY `serid` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuid` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `servicos`
--
ALTER TABLE `servicos`
  ADD CONSTRAINT `servicos_ibfk_1` FOREIGN KEY (`cliid`) REFERENCES `clientes` (`cliid`),
  ADD CONSTRAINT `servicos_ibfk_2` FOREIGN KEY (`carid`) REFERENCES `carros` (`carid`),
  ADD CONSTRAINT `servicos_ibfk_3` FOREIGN KEY (`motid`) REFERENCES `motos` (`motid`),
  ADD CONSTRAINT `servicos_ibfk_4` FOREIGN KEY (`camid`) REFERENCES `caminhoes` (`camid`),
  ADD CONSTRAINT `servicos_ibfk_5` FOREIGN KEY (`barid`) REFERENCES `nauticos` (`barid`),
  ADD CONSTRAINT `servicos_ibfk_6` FOREIGN KEY (`mobid`) REFERENCES `mobilia` (`mobid`);

--
-- Constraints for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuario_cliente` FOREIGN KEY (`idcli`) REFERENCES `clientes` (`cliid`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
