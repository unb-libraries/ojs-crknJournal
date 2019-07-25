CREATE TABLE `crkn_ips` (
  `ip` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `start` bigint(20) unsigned NOT NULL,
  `end` bigint(20) unsigned NOT NULL,
  `institution` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`institution`,`ip`),
  KEY `start_idx` (`start`),
  KEY `end_idx` (`end`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
