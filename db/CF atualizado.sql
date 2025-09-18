-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: cf
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'Eletrônicos','2025-09-16 10:55:13'),(2,'Bebidas','2025-09-16 10:55:24'),(3,'Brinquedos','2025-09-16 10:55:39'),(4,'Games','2025-09-16 10:55:41'),(5,'Limpeza','2025-09-16 10:55:51'),(6,'Decoração','2025-09-16 10:56:05'),(7,'Esporte','2025-09-16 10:56:22'),(8,'Móveis','2025-09-16 10:57:48'),(9,'Eletrodomésticos','2025-09-16 10:58:25'),(10,'Livros','2025-09-16 10:59:35'),(12,'Roupas','2025-09-16 11:11:18');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes`
--

DROP TABLE IF EXISTS `clientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `cpf` char(11) DEFAULT NULL,
  `cnpj` char(14) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`),
  UNIQUE KEY `cnpj` (`cnpj`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes`
--

LOCK TABLES `clientes` WRITE;
/*!40000 ALTER TABLE `clientes` DISABLE KEYS */;
INSERT INTO `clientes` VALUES (1,'João de Souza','92999999999','88888888888',NULL,'2025-09-16 14:04:08'),(2,'Leonardo','92939488822','12121212121',NULL,'2025-09-16 14:06:58'),(3,'julio','99999999999','11111111111',NULL,'2025-09-16 14:17:37'),(4,'Ricardo','9224432540','35618791013',NULL,'2025-09-16 15:05:32'),(5,'Estevão','9228964786','65153505030',NULL,'2025-09-16 15:06:43'),(6,'Gerson','9221712495','11466822040',NULL,'2025-09-16 15:08:09'),(7,'Lucas','9224735835','17052264005',NULL,'2025-09-16 15:09:30'),(8,'Gabriel','9236212353','84070473092',NULL,'2025-09-16 15:10:01'),(9,'Ramon','9235462394','72452566080',NULL,'2025-09-16 15:10:41'),(10,'Carlos','9220101947','18794951047',NULL,'2025-09-16 15:11:18');
/*!40000 ALTER TABLE `clientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `enderecos`
--

DROP TABLE IF EXISTS `enderecos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `enderecos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `tipo_logradouro` varchar(50) DEFAULT NULL,
  `logradouro` varchar(100) DEFAULT NULL,
  `numero` varchar(10) DEFAULT NULL,
  `complemento` varchar(50) DEFAULT NULL,
  `bairro` varchar(50) DEFAULT NULL,
  `cep` char(8) NOT NULL,
  `cidade` varchar(50) DEFAULT NULL,
  `estado` char(2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `enderecos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `enderecos`
--

LOCK TABLES `enderecos` WRITE;
/*!40000 ALTER TABLE `enderecos` DISABLE KEYS */;
INSERT INTO `enderecos` VALUES (1,1,NULL,'Rua Comendador J. G. Araújo','10','','Santo Antônio','69029130','Manaus','AM','2025-09-16 14:04:08'),(2,2,NULL,'Rua Rodrigo Otavio','234','','Santa Cruz','12134444','Manaus','AM','2025-09-16 14:06:58'),(3,3,NULL,'rua 3','12','','col terra nova','69123445','Manaus','Am','2025-09-16 14:17:37'),(4,4,NULL,'Avenida Djalma Batista','37','de 1/2 ao fim','Chapada','69050010','Manaus','AM','2025-09-16 15:05:32'),(5,5,NULL,'Avenida Djalma Batista','234','até 1596 - lado par','Nossa Senhora das Graças','69053000','Manaus','AM','2025-09-16 15:06:43'),(6,6,NULL,'Rua Rio Paru','125','(Res V Melhor)','Lago Azul','69018550','Manaus','AM','2025-09-16 15:08:09'),(7,7,NULL,'Rua Campos Bravos','578','','Redenção','69047000','Manaus','AM','2025-09-16 15:09:30'),(8,8,NULL,'Rua Coronel Alexandre Montoril','428','','Petrópolis','69063640','Manaus','AM','2025-09-16 15:10:01'),(9,9,NULL,'Rua José Florêncio Batista','122','','Petrópolis','69063395','Manaus','AM','2025-09-16 15:10:41'),(10,10,NULL,'Rua Monte Gilboa','321','','Colônia Terra Nova','69015343','Manaus','AM','2025-09-16 15:11:18');
/*!40000 ALTER TABLE `enderecos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fornecedor`
--

DROP TABLE IF EXISTS `fornecedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fornecedor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `cnpj` varchar(20) DEFAULT NULL,
  `cpf` varchar(20) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cpf` (`cpf`),
  UNIQUE KEY `cnpj` (`cnpj`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fornecedor`
--

LOCK TABLES `fornecedor` WRITE;
/*!40000 ALTER TABLE `fornecedor` DISABLE KEYS */;
INSERT INTO `fornecedor` VALUES (1,'MaxiSupply Brasil',NULL,'12345678909','11912345678','2025-09-16 10:52:27'),(2,'Nova Linha Distribuidora','23344256766342',NULL,'21201737584','2025-09-16 10:53:15'),(3,'Trevo Market Source',NULL,'34221677533','71993211122','2025-09-16 10:53:51'),(4,'TecnoStore Distribuição',NULL,'15975348600','85994445566','2025-09-16 10:54:23'),(5,'Conecte+ Comercial','36914725899123',NULL,'61981233344','2025-09-16 10:55:04'),(6,'Digital Pack Suprimentos',NULL,'96325814777','19997778888','2025-09-16 10:56:06'),(7,'ViaPac Suprimentos',NULL,'98765432100','21998765432','2025-09-16 10:57:18');
/*!40000 ALTER TABLE `fornecedor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `itens_venda`
--

DROP TABLE IF EXISTS `itens_venda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `itens_venda` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `venda_id` int(11) DEFAULT NULL,
  `produto_id` int(11) DEFAULT NULL,
  `quantidade` int(11) DEFAULT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `venda_id` (`venda_id`),
  KEY `produto_id` (`produto_id`),
  CONSTRAINT `itens_venda_ibfk_1` FOREIGN KEY (`venda_id`) REFERENCES `vendas` (`id`),
  CONSTRAINT `itens_venda_ibfk_2` FOREIGN KEY (`produto_id`) REFERENCES `produtos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `itens_venda`
--

LOCK TABLES `itens_venda` WRITE;
/*!40000 ALTER TABLE `itens_venda` DISABLE KEYS */;
INSERT INTO `itens_venda` VALUES (1,1,4,2,0.00),(2,2,2,3,0.00),(3,3,9,2,125.00),(4,3,15,1,89.90),(5,3,4,4,29.99),(6,4,12,5,79.90),(7,5,3,2,49.00),(8,5,15,1,89.90),(9,5,4,1,29.99),(10,5,6,1,14.90),(11,5,13,1,89.90);
/*!40000 ALTER TABLE `itens_venda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `produtos`
--

DROP TABLE IF EXISTS `produtos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `produtos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `preco` decimal(10,2) DEFAULT NULL,
  `quantidade_estoque` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `fornecedor_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria_id` (`categoria_id`),
  KEY `fornecedor_id` (`fornecedor_id`),
  CONSTRAINT `fk_produtos_fornecedor` FOREIGN KEY (`fornecedor_id`) REFERENCES `fornecedor` (`id`),
  CONSTRAINT `produtos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `produtos`
--

LOCK TABLES `produtos` WRITE;
/*!40000 ALTER TABLE `produtos` DISABLE KEYS */;
INSERT INTO `produtos` VALUES (1,'Mouse Sem Fio',NULL,1,89.90,110,'2025-09-16 11:00:04',4),(2,'Livro: \"Gestão Moderna\"',NULL,10,64.00,37,'2025-09-16 11:14:26',2),(3,'Camiseta Urban Style',NULL,12,49.00,118,'2025-09-16 11:15:09',1),(4,'Creme Facial HidraPlus',NULL,5,29.99,193,'2025-09-16 11:15:49',3),(5,'Teclado Gamer RedStrike',NULL,1,229.00,60,'2025-09-16 11:17:27',6),(6,'Sabonete Líquido Herbal',NULL,5,14.90,149,'2025-09-16 11:18:12',3),(9,'Calça Jeans Slim',NULL,12,125.00,118,'2025-09-17 10:17:51',1),(10,'Geladeira Frost Free',NULL,9,2499.00,15,'2025-09-17 10:19:25',2),(11,'Micro-ondas 20L',NULL,9,399.00,30,'2025-09-17 10:19:54',3),(12,'Bola de Futebol',NULL,7,79.90,75,'2025-09-17 10:20:46',4),(13,'Vaso de Cerâmica',NULL,6,89.90,49,'2025-09-17 10:21:19',1),(14,'Xbox Series S',NULL,4,2299.00,10,'2025-09-17 10:21:48',5),(15,'Carrinho Controle Remoto',NULL,3,89.90,43,'2025-09-17 10:22:22',2);
/*!40000 ALTER TABLE `produtos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `CPF` varchar(14) NOT NULL,
  `psecreta` varchar(255) NOT NULL,
  `cargo` enum('vendedor','gerente','dono') NOT NULL DEFAULT 'vendedor',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `CPF` (`CPF`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Kelrison Coelho','kelrison.leite@gmail.com','$2y$10$JvOjJHnQqbhG9qUBTy.kyOlL0/YW6ps4QZns1f3iQjKUeVtQXvNG2','88184242204','Preto','dono','2025-09-16 10:00:20'),(2,'Vini','vini@gmail.com','$2y$10$9VS9Wyi3N.alYGbRhgBKmudPlU42LmkROU49mKosuwhDhL9ZKvraS','44444444444','Roxo','dono','2025-09-16 10:00:50'),(3,'kaka','kaka@gmail.com','$2y$10$UnrpQj5pqi2kHPSzZyR.fuaNHxl1DN4tcp6g97WoVjvfq.F56R1jq','70591534037','verde','vendedor','2025-09-17 10:00:42'),(4,'Walter Meireles','walter@gmail.com','$2y$10$pqBfIjcsc61Z5I0BmfA5V.AMOca0B93fUH83ebGoFboKoLJCxNF.u','11122233344','amarelo','gerente','2025-09-17 10:13:55');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `vendas`
--

DROP TABLE IF EXISTS `vendas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `vendas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `data_venda` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  CONSTRAINT `vendas_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `vendas`
--

LOCK TABLES `vendas` WRITE;
/*!40000 ALTER TABLE `vendas` DISABLE KEYS */;
INSERT INTO `vendas` VALUES (1,5,0.00,'2025-09-16 11:33:46','2025-09-16 11:33:46'),(2,6,0.00,'2025-09-16 11:45:28','2025-09-16 11:45:28'),(3,1,459.86,'2025-09-18 10:27:35','2025-09-18 10:27:35'),(4,5,399.50,'2025-09-18 10:28:14','2025-09-18 10:28:14'),(5,8,322.69,'2025-09-18 10:29:00','2025-09-18 10:29:00');
/*!40000 ALTER TABLE `vendas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-18 11:12:37
