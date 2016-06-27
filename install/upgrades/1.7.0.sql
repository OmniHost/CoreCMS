ALTER TABLE `#__layout_module` CHANGE `position` `position` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

Delete from #__setting where `code` = '_version_';
INSERT INTO `#__setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES ( 0, '_version_', '_version_', '1.7.1', 0);