
DROP TABLE IF EXISTS `queue`;
CREATE TABLE `queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `model_class` varchar(255) DEFAULT NULL,
  `model_id` varchar(255) DEFAULT NULL,
  `model_method` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `status` varchar(255) DEFAULT NULL,
  `ts` datetime DEFAULT NULL,
  `processor_id` varchar(255) DEFAULT NULL,
  `outcome` varchar(255) DEFAULT NULL,
  `error` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
