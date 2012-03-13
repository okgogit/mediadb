CREATE TABLE IF NOT EXISTS `<?php echo $wpdb->prefix;?>mediadb_media` (
  `media_id` varchar(255) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`media_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
