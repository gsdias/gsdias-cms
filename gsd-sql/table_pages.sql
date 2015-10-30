--
-- Table structure for table `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `pid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `url` TEXT NOT NULL,
  `beautify` TEXT DEFAULT NULL,
  `lid` int DEFAULT NULL,
  `index` int DEFAULT 0,
  `parent` int(11) DEFAULT NULL,
  `title` varchar(120) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `og_title` varchar(120) DEFAULT NULL,
  `og_description` TEXT DEFAULT NULL,
  `og_image` varchar(100) DEFAULT NULL,
  `show_menu` tinyint(1) DEFAULT 1,
  `require_auth` tinyint(1) DEFAULT NULL,
  `creator` int(11) DEFAULT 1,
  `created` timestamp NULL DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `published` tinyint(1) DEFAULT NULL,
  FOREIGN KEY (creator)
    REFERENCES users(uid)
    ON UPDATE CASCADE ON DELETE SET NULL,
  FOREIGN KEY (lid)
    REFERENCES layouts(lid)
    ON UPDATE CASCADE ON DELETE CASCADE,
    UNIQUE KEY `unique_url` (`parent`, `url`),
    UNIQUE KEY `unique_beautify` (`beautify`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
