CREATE TABLE IF NOT EXISTS `#__currency` (
  `currency_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL,
  `code` varchar(3) NOT NULL,
  `symbol_left` varchar(12) NOT NULL,
  `symbol_right` varchar(12) NOT NULL,
  `decimal_place` char(1) NOT NULL,
  `value` float(15,8) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`currency_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `oc_currency`
--

INSERT INTO `#__currency` (`currency_id`, `title`, `code`, `symbol_left`, `symbol_right`, `decimal_place`, `value`, `status`, `date_modified`) VALUES
(1, 'New Zealand Dollar', 'NZD', '$', '', '2', 1, 1, '2015-06-01 14:40:00');

INSERT INTO `#__setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES
( 0, 'config', 'config_currency', 'NZD', 0);
