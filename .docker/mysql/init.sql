CREATE DATABASE IF NOT EXISTS mmfpm;
USE mmfpm;
--
-- Table structure for table `mm_forum_posts`
--

DROP TABLE IF EXISTS `mm_forum_posts`;

CREATE TABLE `mm_forum_posts`
(
    `id`         bigint(20) unsigned NOT NULL AUTO_INCREMENT,
    `authorid`   bigint(20) unsigned NOT NULL DEFAULT '0',
    `authorname` varchar(16)  NOT NULL DEFAULT '',
    `forum`      bigint(20) unsigned NOT NULL DEFAULT '0',
    `topic`      bigint(20) unsigned NOT NULL DEFAULT '0',
    `lastpost`   bigint(20) unsigned NOT NULL DEFAULT '0',
    `name`       varchar(50)  NOT NULL DEFAULT '',
    `text`       longtext,
    `time`       varchar(255) NOT NULL,
    `annouced`   tinyint(3) unsigned NOT NULL DEFAULT '0',
    `sticked`    tinyint(3) unsigned NOT NULL DEFAULT '0',
    `closed`     tinyint(3) unsigned NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `mm_forum_posts`
--

LOCK
TABLES `mm_forum_posts` WRITE;
/*!40000 ALTER TABLE `mm_forum_posts` DISABLE KEYS */;
INSERT INTO `mm_forum_posts`(`id`, `authorid`, `authorname`, `forum`, `topic`, `lastpost`, `name`, `text`, `time`,
                             `annouced`, `sticked`)
values (1, 0, 'miniManagerTeam', 1, 1, 1, 'Hello Admin!',
        '[b]Hi[/b] !!:D<br /><br />If you are reading this, that means that you have [i]correctly[/i] installed this forum, XD<br /><br /><br /><br />So what\' s next?<br /><br />Edit [color=red]forum.conf.php[/color]<br /><br /><br /><br />And enjoy!<br /><br /><br /><br />Report bugs at [url=https://github.com/TrinityCore/minimanager/issues]MiniManager tracker[/url]<br /><br /><br /><br />Bye!<br /><br />miniManagerTeam','00/00/00 00:00:00',1,0);
/*!40000 ALTER TABLE `mm_forum_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mm_motd`
--

DROP TABLE IF EXISTS `mm_motd`;

CREATE TABLE `mm_motd` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifier',
  `realmid` int(11) NOT NULL,
  `type` longtext NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='MOTD System';

--
-- Dumping data for table `mm_motd`
--

LOCK TABLES `mm_motd` WRITE;
/*!40000 ALTER TABLE `mm_motd` DISABLE KEYS */;
INSERT INTO `mm_motd` VALUES (1, 1, '02/05/10 14:29:07 Posted by: MiniManager Team', 'Hello Admin\r\n\r\nhelp supporting Minimanager\r\n\r\nhttps://github.com/TrinityCore/minimanager/issues\r\n\r\nif you found a bug or improved it,
        please contribute\r\n\r\nor it will eventually stop development from lack of interrest from community ');
/*!40000 ALTER TABLE `mm_motd` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mm_point_system_invites`
--

DROP TABLE IF EXISTS `mm_point_system_invites`;
CREATE TABLE `mm_point_system_invites` (
  `entry` int(11) NOT NULL auto_increment,
  `PlayersAccount` char(50) default NULL,
  `InvitedBy` char(50) default NULL,
  `InviterAccount` char(50) default NULL,
  `Treated` int(1) NOT NULL default '0',
  `Rewarded` int(1) NOT NULL default '0',
  UNIQUE KEY `entry` (`entry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Table structure for table `mm_account`
--

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
