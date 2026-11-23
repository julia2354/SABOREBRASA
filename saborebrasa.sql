-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 23/11/2026 às 16:56
-- Versão do servidor: 8.3.0
-- Versão do PHP: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `saborebrasa`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `cliente`
--

DROP TABLE IF EXISTS `cliente`;
CREATE TABLE IF NOT EXISTS `cliente` (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `telefone` varchar(15) NOT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `foto` varchar(255) NOT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `nome`, `email`, `telefone`, `senha`, `foto`) VALUES
(1, 'Shu', 'paocombata@gmail.com', '31 982557229', '0', ''),
(2, 'Shu', 'paocombata@gmail.com', '31 982557229', '0', ''),
(3, 'Shu', 'paocombata@gmail.com', '31 982557229', '0', ''),
(4, 'Shu', 'paocombata@gmail.com', '31 982557229', '0', ''),
(5, 'Shu', 'paocombata@gmail.com', '31 982557229', '0', ''),
(6, 'Ana', 'paocombata@gmail.com', '31 982557229', '0', ''),
(7, 'Shu', 'paocombata@gmail.com', '31 982557229', '0', ''),
(8, 'Shu', 'paocombata@gmail.com', '31 982557229', '0', ''),
(9, 'aaaaa', 'paocombata@gmail.com', '31 982557229', '0', ''),
(10, 'Júlia', 'paocombata@gmail.com', '31 982557229', '$2y$10$u7NJXPXlJVzjGYSm0Oyuiu8oXMZ79mmaoCZmSeKQfKtHwyPemR6n2', ''),
(11, 'Júlia', 'paocombata@gmail.com', '31 982557229', '$2y$10$wuENt/7/rC3HQ4CgrQ8zGuqWQROuCNf/J4hhIK5Rv79cXUL3yC29m', ''),
(12, 'Júlia', 'paocombata@gmail.com', '31 982557229', '$2y$10$owcLhS4zv3E4LyuXT5HLqOQhxwB7zYFPmalMvIC7hU0eUfg/vesde', ''),
(14, 'Júlia', 'paocombata@gmail.com', '31 982557229', '$2y$10$OHa.ajxhcuKHkSvDhx9Ki.5LU1y3WRIQ5iRxy.PS7iAFJYFb.amGi', ''),
(15, 'Júlia', 'paocombata@gmail.com', '31 982557229', '$2y$10$E08RwWIB.osFoYK2JqJ1OekVf18KAouESHBAFnMX8y2hGoiEU6CV6', ''),
(16, 'Júlia', 'paocombata@gmail.com', '31 982557229', '$2y$10$HLdfzShq2v4nX5n8Y1/UW.c.c3HdUsdXUw9xqJCE1MhmUs4mzP5mO', ''),
(17, 'Júlia', 'paocombata@gmail.com', '31 982557229', '$2y$10$DSsWV4hRY.awhciiQWEZYuxUtlCO8j.qG8qOs4JUC/.yAIO63VTvu', '');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
