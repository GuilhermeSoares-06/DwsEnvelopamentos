-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15/07/2026 às 15:25
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
  `camplaca` varchar(10) DEFAULT NULL,
  `camimg` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `carros`
--

CREATE TABLE `carros` (
  `carid` int(11) NOT NULL,
  `carmodelo` varchar(50) DEFAULT NULL,
  `carmarca` varchar(50) DEFAULT NULL,
  `carplaca` varchar(10) DEFAULT NULL,
  `carimg` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `clitel` varchar(13) DEFAULT NULL,
  `clisenha` varchar(150) DEFAULT NULL,
  `tipocliente` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`cliid`, `clinome`, `clicpf`, `cliendereco`, `cliservico`, `clitel`, `clisenha`, `tipocliente`) VALUES
(9, 'Dariel', NULL, NULL, NULL, NULL, 'admin123', 'funcionario'),
(10, 'admin', NULL, NULL, NULL, NULL, 'admin123', 'funcionario'),
(14, 'Guilherme Soares', '49467262843', 'Piraju', NULL, '(14) 99617-56', '$2y$10$gKJWuhlaDItdbB2CjQXqxOYf.q/bS7ih2eahI7EgxDmq3QAHo0Z6a', 'cliente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `horarios_ocupados`
--

CREATE TABLE `horarios_ocupados` (
  `id` int(11) NOT NULL,
  `data` date NOT NULL,
  `horario` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mobilia`
--

CREATE TABLE `mobilia` (
  `mobid` int(11) NOT NULL,
  `mobmedida` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `motos`
--

CREATE TABLE `motos` (
  `motid` int(11) NOT NULL,
  `motmarca` varchar(50) DEFAULT NULL,
  `motmodelo` varchar(50) DEFAULT NULL,
  `motplaca` varchar(10) DEFAULT NULL,
  `motimg` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `nauticos`
--

CREATE TABLE `nauticos` (
  `nauid` int(11) NOT NULL,
  `naumodelo` varchar(30) DEFAULT NULL,
  `naumarca` varchar(20) DEFAULT NULL,
  `nauplaca` varchar(20) DEFAULT NULL,
  `nauimg` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  `serdata_servico` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `servicos`
--

INSERT INTO `servicos` (`serid`, `cliid`, `tipo_servico`, `serdescricao`, `servalor`, `serdata_servico`) VALUES
(7, 14, 'carro', 'Veículo: Camaro | pintar de preto | Acabamento: Brilhante', '920.00', '2026-07-15 10:00:00'),
(8, 14, 'carro', 'Veículo: Fusca | pintar de preto | Acabamento: Brilhante', '920.00', '2026-07-15 11:00:00'),
(9, 14, 'carro', 'Veículo: 3432 | 32ewfdsfsd | Acabamento: Brilhante', '920.00', '2026-07-27 09:00:00');

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
  ADD KEY `cliid` (`cliid`);

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
  MODIFY `camid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `carros`
--
ALTER TABLE `carros`
  MODIFY `carid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `cliid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `horarios_ocupados`
--
ALTER TABLE `horarios_ocupados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mobilia`
--
ALTER TABLE `mobilia`
  MODIFY `mobid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `motos`
--
ALTER TABLE `motos`
  MODIFY `motid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `nauticos`
--
ALTER TABLE `nauticos`
  MODIFY `nauid` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `servicos`
--
ALTER TABLE `servicos`
  MODIFY `serid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `vendas`
--
ALTER TABLE `vendas`
  MODIFY `venid` int(11) NOT NULL AUTO_INCREMENT;

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
