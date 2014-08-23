--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `pid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `url` varchar(60) NOT NULL,
  `lid` int DEFAULT NULL,
  `title` varchar(120) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `og_title` varchar(120) DEFAULT NULL,
  `og_description` varchar(255) DEFAULT NULL,
  `og_image` varchar(100) DEFAULT NULL,
  `show_menu` tinyint(1) DEFAULT 1,
  `require_auth` tinyint(1) DEFAULT NULL,
  `creator` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `published` tinyint(1) DEFAULT NULL,
  FOREIGN KEY (creator)
    REFERENCES users(uid)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  FOREIGN KEY (lid)
    REFERENCES layouts(lid)
    ON UPDATE CASCADE ON DELETE RESTRICT,
    UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
