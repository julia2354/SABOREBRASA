-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 24/11/2026 às 19:40
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
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(17, 'Júlia', 'paocombata@gmail.com', '31 982557229', '$2y$10$DSsWV4hRY.awhciiQWEZYuxUtlCO8j.qG8qOs4JUC/.yAIO63VTvu', ''),
(18, 'aaaaa', 'paocombata@gmail.com', '31 982557229', '$2y$10$fVrwwiNTydh5qYhcjBT1r.Dgmsjglh2Zi7JTadnmNZgqy0gpppESq', ''),
(19, 'aaaaa', 'paocombata@gmail.com', '31 982557229', '$2y$10$PJOGSo5fwBbZ9RnYsI9zmeNy6hKX0EE2q.5zAwMnwrKwgC4aqyPzG', ''),
(20, 'aaaaa', 'paocombata@gmail.com', '31 982557229', '$2y$10$o0CBlmd8XJG9FejpKjbrB.kVqvyb94kzbgmJnGCd5JZ9UL0sXZlcq', ''),
(21, 'Ana', 'paocombata@gmail.com', '31 982557229', '$2y$10$6miq9GzYJBumdHBoqjPf7.bDX1LYsqxzWvcG5QXvE6AhbN7vjgici', ''),
(22, 'ju', 'Ana@gmail.com', '31 982557229', '$2y$10$eooz85wnvWOEnWs5GfLNoe4I1H4ggYpyNG67LmKb9eAEYcM83DKh2', '');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

DROP TABLE IF EXISTS `produtos`;
CREATE TABLE IF NOT EXISTS `produtos` (
  `id_produto` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `preco` decimal(6,2) NOT NULL,
  `imagem` varchar(255) NOT NULL,
  PRIMARY KEY (`id_produto`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id_produto`, `nome`, `preco`, `imagem`) VALUES
(1, 'Big Burguer', 25.90, 'img/aa.png'),
(2, 'Fit Burguer', 28.00, 'img/carlos.png'),
(3, 'Burgão do Mar', 20.00, 'img/i.png'),
(4, 'Combo Solo', 28.99, 'img/e.png'),
(5, 'Triple Burguer', 26.50, 'img/f.png'),
(6, 'Combo Amizade', 36.50, 'img/dd.png'),
(7, 'Burguer Vegetariano', 20.50, 'img/g.png'),
(8, 'Burgão X-Tudo', 25.30, 'img/h.png'),
(9, 'Mush Burguer', 30.00, 'img/j.png'),
(10, 'Bacon Domination', 26.80, 'img/k.png'),
(11, 'Burgão de costela', 33.70, 'img/l.png'),
(12, 'Big Pig', 26.99, 'img/q.png'),
(13, 'Burganha Prime', 49.90, 'img/m.png'),
(14, 'Chicken Burguer', 24.50, 'img/n.png'),
(15, 'Super-Spicy Buguer', 37.80, 'img/o.png'),
(16, 'Mash Burguer', 39.99, 'img/p.png');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
