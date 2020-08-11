-- 
DROP TABLE IF EXISTS `mm_password_resets`;
CREATE TABLE `mm_password_resets` (
    `token` binary(32) not null,
    `accountId` int(11) unsigned not null,
    `oldsalt` binary(32),
    `salt` binary(32),
    `verifier` binary(32),
    `time` bigint unsigned,
    primary key (`token`)
);

DROP TABLE IF EXISTS `mm_account`;
CREATE TABLE `mm_account` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '',
  `salt` binary(32),
  `verifier` binary(32),
  `email` text,
  `joindate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_ip` varchar(30) NOT NULL DEFAULT '127.0.0.1',
  `locked` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `expansion` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `authkey` varchar(40) DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Accounts pending verification';
