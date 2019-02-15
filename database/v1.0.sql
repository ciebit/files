--
-- Table `cb_files`
--
CREATE TABLE `cb_files` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(300) DEFAULT NULL,
  `url` varchar(300) NOT NULL,
  `size` int(12) UNSIGNED NOT NULL COMMENT 'Size in bytes',
  `views` int(10) UNSIGNED DEFAULT '0',
  `mimetype` char(40) NOT NULL,
  `datetime` datetime NOT NULL,
  `metadata` json DEFAULT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='version:2.0';
