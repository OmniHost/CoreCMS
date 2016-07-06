CREATE TABLE IF NOT EXISTS `#__crons` (
`cron_id` int(11) NOT NULL,
`name` varchar(255) NOT NULL,
`route` varchar(255) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `time` varchar(255) NOT NULL DEFAULT '{}',
  `params` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `#__crons`  ADD PRIMARY KEY (`cron_id`);
ALTER TABLE `#__crons`
MODIFY `cron_id` int(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE IF NOT EXISTS `#__allowed_users` (
  `object_id` int(11) NOT NULL,
  `object_type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `#__denied_users` (
  `object_id` int(11) NOT NULL,
  `object_type` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `#__allowed_users`
 ADD PRIMARY KEY (`object_id`,`object_type`,`user_id`);

ALTER TABLE `#__denied_users`
 ADD PRIMARY KEY (`object_id`,`object_type`,`user_id`);


CREATE TABLE IF NOT EXISTS `#__allowed_password` (
  `object_id` int(11) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

ALTER TABLE `#__allowed_password`
 ADD PRIMARY KEY (`object_id`);


ALTER TABLE `#__menu_items` 
ADD `target` VARCHAR(50) NOT NULL AFTER `linklabel`, 
ADD `route` VARCHAR(150) NOT NULL AFTER `target`, 
ADD `params` VARCHAR(255) NOT NULL AFTER `route`,
ADD `ssl` int(1) NOT NULL DEFAULT 0 AFTER `params`;

Delete from #__setting where `code` = '_version_';
INSERT INTO `#__setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES ( 0, '_version_', '_version_', '1.8.0', 0);