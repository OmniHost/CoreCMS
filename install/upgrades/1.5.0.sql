
ALTER TABLE `#__url_alias` ADD `custom` TINYINT(1) NOT NULL DEFAULT '0' AFTER `keyword`;

INSERT INTO `#__url_alias` (`query`, `keyword`, `custom`) VALUES
('p=module/faq', 'help/faq', 1),
('p=blog/blog', 'blog', 1),
('p=common/contact', 'contact-us', 1),
('p=account/account', 'my-account', 1);



Delete from #__setting where `code` = '_version_';
INSERT INTO `#__setting` (`store_id`, `code`, `key`, `value`, `serialized`) VALUES ( 0, '_version_', '_version_', '1.6.0', 0);