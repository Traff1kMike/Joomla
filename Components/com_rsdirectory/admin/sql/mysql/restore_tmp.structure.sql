CREATE TABLE IF NOT EXISTS `#__rsdirectory_restore_tmp` (
  `id_old` int(11) unsigned NOT NULL,
  `id_new` int(11) unsigned NOT NULL,
  `table` varchar(255) NOT NULL,
  KEY `id_old` (`id_old`),
  KEY `id_new` (`id_new`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;