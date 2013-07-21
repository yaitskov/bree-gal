
CREATE TABLE IF NOT EXISTS `tbl_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

INSERT INTO `tbl_user` (`id`, `username`, `password`) VALUES
(2, 'bree', '$2a$10$ggdbam6Q9sBd0BZ7XBWh2uVbjH3QtK1DOHfl4lt8t.VhYbjVP.TAe');


CREATE TABLE IF NOT EXISTS `tbl_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `schema_version` int(11) NOT NULL,
  `schema_consistent` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tbl_meta`
--

INSERT INTO `tbl_meta` (`id`, `schema_version`, `schema_consistent`) VALUES
(1, 1, 1);

-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `tbl_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `owner_id` int(11) NOT NULL references tbl_user(id),
  `created` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

CREATE TABLE IF NOT EXISTS `tbl_photo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  ext  varchar(5) not null,
  `gallery_id` int(11) NOT NULL references tbl_gallery(id),
  `created` int(11) DEFAULT NULL,
  `order`   int(11)  not null,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=7 ;

CREATE TABLE  `tbl_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text COLLATE utf8_unicode_ci NOT NULL,
  `created` int(11) DEFAULT NULL,
  `photo_id` int(11) NOT NULL references tbl_photo(id),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;


