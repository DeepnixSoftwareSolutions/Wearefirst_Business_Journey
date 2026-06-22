-- MySQL dump 10.13  Distrib 9.3.0, for macos15.2 (arm64)
--
-- Host: localhost    Database: wearefirst_db1
-- ------------------------------------------------------
-- Server version	9.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
INSERT INTO `failed_jobs` VALUES (1,'9b0462f5-749c-416c-a871-e6be75db2a8c','database','default','{\"uuid\":\"9b0462f5-749c-416c-a871-e6be75db2a8c\",\"displayName\":\"App\\\\Mail\\\\PaymentInvoiceMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":17:{s:8:\\\"mailable\\\";O:27:\\\"App\\\\Mail\\\\PaymentInvoiceMail\\\":4:{s:7:\\\"student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:4;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:11:\\\"isRemainder\\\";b:0;s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:28:\\\"pradeepharshana200@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:3:\\\"job\\\";N;}\",\"batchId\":null},\"createdAt\":1781179616,\"delay\":null}','InvalidArgumentException: View [emails.payment_invoice] not found. in /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/View/FileViewFinder.php:138\nStack trace:\n#0 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/View/FileViewFinder.php(78): Illuminate\\View\\FileViewFinder->findInPaths(\'emails.payment_...\', Array)\n#1 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/View/Factory.php(150): Illuminate\\View\\FileViewFinder->find(\'emails.payment_...\')\n#2 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Mail/Mailer.php(444): Illuminate\\View\\Factory->make(\'emails.payment_...\', Array)\n#3 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Mail/Mailer.php(419): Illuminate\\Mail\\Mailer->renderView(\'emails.payment_...\', Array)\n#4 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Mail/Mailer.php(312): Illuminate\\Mail\\Mailer->addContent(Object(Illuminate\\Mail\\Message), \'emails.payment_...\', NULL, NULL, Array)\n#5 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Mail/Mailable.php(207): Illuminate\\Mail\\Mailer->send(\'emails.payment_...\', Array, Object(Closure))\n#6 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Support/Traits/Localizable.php(19): Illuminate\\Mail\\Mailable->Illuminate\\Mail\\{closure}()\n#7 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Mail/Mailable.php(200): Illuminate\\Mail\\Mailable->withLocale(NULL, Object(Closure))\n#8 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Mail/SendQueuedMailable.php(82): Illuminate\\Mail\\Mailable->send(Object(Illuminate\\Mail\\MailManager))\n#9 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Mail\\SendQueuedMailable->handle(Object(Illuminate\\Mail\\MailManager))\n#10 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#11 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#12 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#13 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#14 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(129): Illuminate\\Container\\Container->call(Array)\n#15 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}(Object(Illuminate\\Mail\\SendQueuedMailable))\n#16 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Mail\\SendQueuedMailable))\n#17 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(133): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#18 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(136): Illuminate\\Bus\\Dispatcher->dispatchNow(Object(Illuminate\\Mail\\SendQueuedMailable), false)\n#19 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}(Object(Illuminate\\Mail\\SendQueuedMailable))\n#20 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Mail\\SendQueuedMailable))\n#21 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(129): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#22 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Mail\\SendQueuedMailable))\n#23 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#24 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(485): Illuminate\\Queue\\Jobs\\Job->fire()\n#25 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(435): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#26 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(201): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#27 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#28 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#29 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#30 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#31 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#32 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#33 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#34 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call(Array)\n#35 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/symfony/console/Command/Command.php(341): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#36 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#37 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/symfony/console/Application.php(1117): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#38 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/symfony/console/Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#39 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/symfony/console/Application.php(195): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#40 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#41 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#42 /Users/chinthakalakshan/Documents/wearefirst-system/artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#43 {main}','2026-06-11 06:36:57'),(2,'3cf78a71-93c1-45ec-b6f4-baa0416a6c90','database','default','{\"uuid\":\"3cf78a71-93c1-45ec-b6f4-baa0416a6c90\",\"displayName\":\"App\\\\Mail\\\\PaymentInvoiceMail\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Mail\\\\SendQueuedMailable\",\"command\":\"O:34:\\\"Illuminate\\\\Mail\\\\SendQueuedMailable\\\":17:{s:8:\\\"mailable\\\";O:27:\\\"App\\\\Mail\\\\PaymentInvoiceMail\\\":4:{s:7:\\\"student\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";i:5;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:11:\\\"isRemainder\\\";b:0;s:2:\\\"to\\\";a:1:{i:0;a:2:{s:4:\\\"name\\\";N;s:7:\\\"address\\\";s:24:\\\"diluklakshan01@gmail.com\\\";}}s:6:\\\"mailer\\\";s:4:\\\"smtp\\\";}s:5:\\\"tries\\\";N;s:7:\\\"timeout\\\";N;s:13:\\\"maxExceptions\\\";N;s:17:\\\"shouldBeEncrypted\\\";b:0;s:10:\\\"connection\\\";N;s:5:\\\"queue\\\";N;s:12:\\\"messageGroup\\\";N;s:12:\\\"deduplicator\\\";N;s:5:\\\"delay\\\";N;s:11:\\\"afterCommit\\\";N;s:10:\\\"middleware\\\";a:0:{}s:7:\\\"chained\\\";a:0:{}s:15:\\\"chainConnection\\\";N;s:10:\\\"chainQueue\\\";N;s:19:\\\"chainCatchCallbacks\\\";N;s:3:\\\"job\\\";N;}\",\"batchId\":null},\"createdAt\":1781180178,\"delay\":null}','InvalidArgumentException: View [emails.payment_invoice] not found. in /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/View/FileViewFinder.php:138\nStack trace:\n#0 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/View/FileViewFinder.php(78): Illuminate\\View\\FileViewFinder->findInPaths(\'emails.payment_...\', Array)\n#1 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/View/Factory.php(150): Illuminate\\View\\FileViewFinder->find(\'emails.payment_...\')\n#2 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Mail/Mailer.php(444): Illuminate\\View\\Factory->make(\'emails.payment_...\', Array)\n#3 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Mail/Mailer.php(419): Illuminate\\Mail\\Mailer->renderView(\'emails.payment_...\', Array)\n#4 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Mail/Mailer.php(312): Illuminate\\Mail\\Mailer->addContent(Object(Illuminate\\Mail\\Message), \'emails.payment_...\', NULL, NULL, Array)\n#5 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Mail/Mailable.php(207): Illuminate\\Mail\\Mailer->send(\'emails.payment_...\', Array, Object(Closure))\n#6 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Support/Traits/Localizable.php(19): Illuminate\\Mail\\Mailable->Illuminate\\Mail\\{closure}()\n#7 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Mail/Mailable.php(200): Illuminate\\Mail\\Mailable->withLocale(NULL, Object(Closure))\n#8 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Mail/SendQueuedMailable.php(82): Illuminate\\Mail\\Mailable->send(Object(Illuminate\\Mail\\MailManager))\n#9 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Mail\\SendQueuedMailable->handle(Object(Illuminate\\Mail\\MailManager))\n#10 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#11 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#12 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#13 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#14 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(129): Illuminate\\Container\\Container->call(Array)\n#15 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Bus\\Dispatcher->Illuminate\\Bus\\{closure}(Object(Illuminate\\Mail\\SendQueuedMailable))\n#16 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Mail\\SendQueuedMailable))\n#17 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Bus/Dispatcher.php(133): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#18 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(136): Illuminate\\Bus\\Dispatcher->dispatchNow(Object(Illuminate\\Mail\\SendQueuedMailable), false)\n#19 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(180): Illuminate\\Queue\\CallQueuedHandler->Illuminate\\Queue\\{closure}(Object(Illuminate\\Mail\\SendQueuedMailable))\n#20 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(137): Illuminate\\Pipeline\\Pipeline->Illuminate\\Pipeline\\{closure}(Object(Illuminate\\Mail\\SendQueuedMailable))\n#21 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(129): Illuminate\\Pipeline\\Pipeline->then(Object(Closure))\n#22 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(70): Illuminate\\Queue\\CallQueuedHandler->dispatchThroughMiddleware(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Mail\\SendQueuedMailable))\n#23 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Array)\n#24 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(485): Illuminate\\Queue\\Jobs\\Job->fire()\n#25 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(435): Illuminate\\Queue\\Worker->process(\'database\', Object(Illuminate\\Queue\\Jobs\\DatabaseJob), Object(Illuminate\\Queue\\WorkerOptions))\n#26 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(201): Illuminate\\Queue\\Worker->runJob(Object(Illuminate\\Queue\\Jobs\\DatabaseJob), \'database\', Object(Illuminate\\Queue\\WorkerOptions))\n#27 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon(\'database\', \'default\', Object(Illuminate\\Queue\\WorkerOptions))\n#28 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker(\'database\', \'default\')\n#29 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#30 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::Illuminate\\Container\\{closure}()\n#31 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure(Object(Closure))\n#32 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod(Object(Illuminate\\Foundation\\Application), Array, Object(Closure))\n#33 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Container/Container.php(799): Illuminate\\Container\\BoundMethod::call(Object(Illuminate\\Foundation\\Application), Array, Array, NULL)\n#34 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call(Array)\n#35 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/symfony/console/Command/Command.php(341): Illuminate\\Console\\Command->execute(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#36 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Illuminate\\Console\\OutputStyle))\n#37 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/symfony/console/Application.php(1117): Illuminate\\Console\\Command->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#38 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/symfony/console/Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand(Object(Illuminate\\Queue\\Console\\WorkCommand), Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#39 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/symfony/console/Application.php(195): Symfony\\Component\\Console\\Application->doRun(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#40 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(198): Symfony\\Component\\Console\\Application->run(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#41 /Users/chinthakalakshan/Documents/wearefirst-system/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle(Object(Symfony\\Component\\Console\\Input\\ArgvInput), Object(Symfony\\Component\\Console\\Output\\ConsoleOutput))\n#42 /Users/chinthakalakshan/Documents/wearefirst-system/artisan(16): Illuminate\\Foundation\\Application->handleCommand(Object(Symfony\\Component\\Console\\Input\\ArgvInput))\n#43 {main}','2026-06-11 06:46:18');
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_05_13_184240_create_nodes_table',1),(5,'2026_05_14_163142_create_transactions_table',1),(6,'2026_05_14_164440_add_intended_placement_to_users_table',1),(7,'2026_05_14_175515_create_salary_requests_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nodes`
--

DROP TABLE IF EXISTS `nodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nodes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `parent_node_id` bigint unsigned DEFAULT NULL,
  `position` enum('Left','Right') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `account_type` enum('Main','Sub_A','Sub_B') COLLATE utf8mb4_unicode_ci NOT NULL,
  `left_points` int NOT NULL DEFAULT '0',
  `right_points` int NOT NULL DEFAULT '0',
  `today_income_projected` decimal(15,2) NOT NULL DEFAULT '0.00',
  `node_cumulative_pending` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `nodes_user_id_foreign` (`user_id`),
  KEY `nodes_parent_node_id_foreign` (`parent_node_id`),
  CONSTRAINT `nodes_parent_node_id_foreign` FOREIGN KEY (`parent_node_id`) REFERENCES `nodes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nodes_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nodes`
--

LOCK TABLES `nodes` WRITE;
/*!40000 ALTER TABLE `nodes` DISABLE KEYS */;
INSERT INTO `nodes` VALUES (1,1,NULL,NULL,'Main',0,1,0.00,2500.00,'2026-06-11 06:30:25','2026-06-11 07:18:49'),(2,4,1,'Left','Main',0,0,0.00,0.00,'2026-06-11 06:36:56','2026-06-11 06:36:56'),(3,4,2,'Left','Sub_A',0,0,0.00,0.00,'2026-06-11 06:36:56','2026-06-11 06:36:56'),(4,4,2,'Right','Sub_B',0,0,0.00,0.00,'2026-06-11 06:36:56','2026-06-11 06:36:56'),(5,5,1,'Right','Main',1,0,0.00,0.00,'2026-06-11 06:46:18','2026-06-11 07:18:49'),(6,5,5,'Left','Sub_A',1,0,0.00,0.00,'2026-06-11 06:46:18','2026-06-11 07:18:49'),(7,5,5,'Right','Sub_B',0,0,0.00,0.00,'2026-06-11 06:46:18','2026-06-11 06:46:18'),(8,6,6,'Left','Main',0,0,0.00,0.00,'2026-06-11 07:18:49','2026-06-11 07:18:49'),(9,6,8,'Left','Sub_A',0,0,0.00,0.00,'2026-06-11 07:18:49','2026-06-11 07:18:49'),(10,6,8,'Right','Sub_B',0,0,0.00,0.00,'2026-06-11 07:18:49','2026-06-11 07:18:49');
/*!40000 ALTER TABLE `nodes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `salary_requests`
--

DROP TABLE IF EXISTS `salary_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `salary_requests` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending Admin Approval',
  `requested_by` bigint unsigned NOT NULL,
  `approved_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `salary_requests_user_id_foreign` (`user_id`),
  KEY `salary_requests_requested_by_foreign` (`requested_by`),
  KEY `salary_requests_approved_by_foreign` (`approved_by`),
  CONSTRAINT `salary_requests_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `salary_requests_requested_by_foreign` FOREIGN KEY (`requested_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `salary_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `salary_requests`
--

LOCK TABLES `salary_requests` WRITE;
/*!40000 ALTER TABLE `salary_requests` DISABLE KEYS */;
INSERT INTO `salary_requests` VALUES (1,5,2000.00,'Approved by Admin',3,1,'2026-06-11 07:28:16','2026-06-11 07:30:48');
/*!40000 ALTER TABLE `salary_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('hZDVMu6mv5TmRt7bA4vxVD4sscKxSFEi0LS9u1Vf',5,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.4 Safari/605.1.15','YTozOntzOjY6Il90b2tlbiI7czo0MDoiVXZ3NW5UdWRLdkdmWGJUV3dYbTd5VEoybmZobWo3RFNPQkowbE5kZCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NTt9',1781183680),('YvtfHNIqefBrc45dHycxRmr64o3RakWHMvOZnKDJ',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.123.0 Chrome/148.0.7778.97 Electron/42.2.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoidGdPTlZrUTFsWWJKdWxGUTlQUVVwQTRTYnl5UllubHVRellUM01hOSI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1781185684),('zl3hOtZYSpzz9oRxSsbPNRJTwu5sFWeFajBaL2fC',1,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/148.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTlJOZmNDMnhvc29pb09iS2hPRkxKWHBOYVFUMkp4MG14UXc3M2ZHUCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czozMToiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2Rhc2hib2FyZCI7czo1OiJyb3V0ZSI7czo5OiJkYXNoYm9hcmQiO319',1781184529);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `node_id` bigint unsigned DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `transactions_user_id_foreign` (`user_id`),
  KEY `transactions_node_id_foreign` (`node_id`),
  CONSTRAINT `transactions_node_id_foreign` FOREIGN KEY (`node_id`) REFERENCES `nodes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transactions`
--

LOCK TABLES `transactions` WRITE;
/*!40000 ALTER TABLE `transactions` DISABLE KEYS */;
INSERT INTO `transactions` VALUES (1,1,NULL,'Direct Referral',2000.00,'Direct Referral Commission for registering Student: pradeep harshana','2026-06-11 06:36:56','2026-06-11 06:36:56'),(2,1,NULL,'Direct Referral',2000.00,'Direct Referral Commission for registering Student: Diluk Lakshan','2026-06-11 06:46:18','2026-06-11 06:46:18'),(3,1,1,'Pair Bonus',2500.00,'Daily Pair Bonus (1 payable pairs)','2026-06-11 07:16:07','2026-06-11 07:16:07'),(4,5,NULL,'Direct Referral',2000.00,'Direct Referral Commission for registering Student: Diluk Lakshan','2026-06-11 07:18:49','2026-06-11 07:18:49');
/*!40000 ALTER TABLE `transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nic` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `district` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `profile_image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('Admin','Manager','Accountant','Agent','Student') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Student',
  `middle_legs_unlocked` tinyint(1) NOT NULL DEFAULT '0',
  `badge` enum('None','Ruby','Diamond','Crown Diamond','2 Crown Diamond') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'None',
  `is_held` tinyint(1) NOT NULL DEFAULT '0',
  `special_offers` json DEFAULT NULL,
  `beneficiary_details` json DEFAULT NULL,
  `pending_payout_balance` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total_historical_earned` decimal(15,2) NOT NULL DEFAULT '0.00',
  `admission_fee_paid` decimal(15,2) NOT NULL DEFAULT '0.00',
  `admission_status` enum('Pending Approval','Active','Overdue') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending Approval',
  `payment_transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `registered_by_agent_id` bigint unsigned DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `intended_parent_node_id` bigint unsigned DEFAULT NULL,
  `intended_position` enum('Left','Right') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_nic_unique` (`nic`),
  KEY `users_registered_by_agent_id_foreign` (`registered_by_agent_id`),
  KEY `users_intended_parent_node_id_foreign` (`intended_parent_node_id`),
  CONSTRAINT `users_intended_parent_node_id_foreign` FOREIGN KEY (`intended_parent_node_id`) REFERENCES `nodes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `users_registered_by_agent_id_foreign` FOREIGN KEY (`registered_by_agent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Company Admin','admin@wearefirst.lk','ADMIN0000001','0700000000',NULL,NULL,NULL,NULL,'Admin',1,'None',0,NULL,NULL,6500.00,6500.00,0.00,'Active',NULL,NULL,NULL,'$2y$12$gEjj.62/G9D/yEtKiCvKkOQAoCAg7emWZpATf4hvZKJdfiBUw0YX2',NULL,'2026-06-11 06:30:25','2026-06-11 07:16:07',NULL,NULL),(2,'System Manager','manager@wearefirst.lk','STAFF0000001','0700000001',NULL,NULL,NULL,NULL,'Manager',0,'None',0,NULL,NULL,0.00,0.00,0.00,'Active',NULL,NULL,NULL,'$2y$12$QXuKrJSFhjpIRXXj.HE0AeO5ZQxdoiPsXpqE7esoV33683ulRk3k2',NULL,'2026-06-11 06:30:26','2026-06-11 06:30:26',NULL,NULL),(3,'Head Accountant','finance@wearefirst.lk','STAFF0000002','0700000002',NULL,NULL,NULL,NULL,'Accountant',0,'None',0,NULL,NULL,0.00,0.00,0.00,'Active',NULL,NULL,NULL,'$2y$12$rJnbOBVBTxTZqJcY/InkseYOViWaeJMXXmw4GieHiivERxaou8xCi',NULL,'2026-06-11 06:30:26','2026-06-11 06:30:26',NULL,NULL),(4,'pradeep harshana','pradeepharshana200@gmail.com','200222700112','0775726823','Ampara','Dehiattakandiya','no 57 weumadagama nawamedagama',NULL,'Agent',0,'None',0,'\"Dubai trip 2026\"',NULL,0.00,0.00,15000.00,'Active','1234567',1,NULL,'$2y$12$d4geM4Kkj/WVBjFee1lFIudNNkE28dbEaGCNe3tO43jJJjezGwakS',NULL,'2026-06-11 06:35:18','2026-06-11 07:42:07',1,'Left'),(5,'Diluk Lakshan','diluklakshan01@gmail.com','200201101548','0764226093','Puttalam','Nattandiya','Nattandiya',NULL,'Agent',0,'None',0,'\"Dubai trip\"',NULL,0.00,2000.00,15000.00,'Active','rdjk',1,NULL,'$2y$12$wqK4egR/28B7AKO9NWVibeShBYDoQocnkrartLoLVZbUAXToASN7e',NULL,'2026-06-11 06:44:52','2026-06-11 07:43:46',1,'Right'),(6,'Diluk Lakshan','diluklakshan02@gmail.com','200297977665','0764226093','Batticaloa','Eravur','Nattandiya',NULL,'Student',0,'None',0,NULL,NULL,0.00,0.00,15000.00,'Active','tswgdf',5,NULL,'$2y$12$TxfiiZJt7.qgHNQkgi7Ws.pO/K5NqTT9Hu.XrwNK0aZZhFhOZbGW.',NULL,'2026-06-11 07:18:10','2026-06-11 07:18:49',6,'Left');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-22 15:29:18
