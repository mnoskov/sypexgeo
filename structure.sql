CREATE TABLE IF NOT EXISTS `modx_locations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `city_id` int(10) UNSIGNED NOT NULL,
  `region_id` int(10) UNSIGNED NOT NULL,
  `phone` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `city_id` (`city_id`,`region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;