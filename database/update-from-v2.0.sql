ALTER TABLE `cb_files` COMMENT = 'version:2.1';

CREATE TABLE `cb_files_labels` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_id` int(10) UNSIGNED NOT NULL,
  `label_id` int(5) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='version:2.1';
