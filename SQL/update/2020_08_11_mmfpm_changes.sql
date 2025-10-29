--
-- Table structure for table `mm_account`
--

DROP TABLE IF EXISTS `mm_account`;
CREATE TABLE `mm_account` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '',
  `salt` binary(32),
  `verifier` binary(32),
  `email` text,
  `joindate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_ip` varchar(30) NOT NULL DEFAULT '127.0.0.1',
  `locked` tinyint unsigned NOT NULL DEFAULT '0',
  `expansion` tinyint unsigned NOT NULL DEFAULT '0',
  `authkey` varchar(40) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Accounts pending verification';

DROP TABLE IF EXISTS `mm_password_resets`;
CREATE TABLE `mm_password_resets` (
  `token` binary(32) NOT NULL,
  `accountId` int unsigned NOT NULL,
  `oldsalt` binary(32),
  `salt` binary(32),
  `verifier` binary(32),
  `time` bigint unsigned,
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
