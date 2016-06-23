ALTER TABLE `#__customer` ADD `temp` INT(11) NOT NULL ;
update #__customer set temp = customer_group_id;
ALTER TABLE `#__customer` CHANGE `customer_group_id` `customer_group_id` VARCHAR(250) NOT NULL;
update `#__customer` set customer_group_id = concat('[' , temp, ']');
ALTER TABLE `#__customer` ADD `profile_img` VARCHAR(255) NOT NULL ;

Delete from #__setting where `code` = '_version_';
INSERT INTO `#__setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES ( 0, '_version_', '_version_', '1.7.0', 0);

CREATE TABLE IF NOT EXISTS `#__formcreator` (
                            `form_id` int(11) NOT NULL AUTO_INCREMENT,
                            `title` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
                            `url` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
                            `email` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
                            `success_msg` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
                            `status` int(11) NOT NULL,
                            `formdata` text NOT NULL,
                            `total_value` float NOT NULL,
                            `form_ip` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
                            `date_added` int(1) NOT NULL,
                            PRIMARY KEY (`form_id`)
                            ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=46 ;

ALTER TABLE `#__modification` CHANGE COLUMN `xml` `xml` MEDIUMTEXT NOT NULL;