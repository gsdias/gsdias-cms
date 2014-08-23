--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `iid` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `description` varchar(100) DEFAULT NULL,
  `extension` varchar(5) NOT NULL,
  `width` int(11) NOT NULL,
  `height` int(11) NOT NULL,
  `size` varchar(10) NOT NULL,
  `creator` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (creator)
    REFERENCES users(uid)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `images` (`iid`, `name`, `description`, `extension`, `width`, `height`, `size`, `creator`, `created`) VALUES
(1, 'Logo header', '', 'png', 200, 163, '9KB', 1, '2014-08-12 13:26:42'),
(2, 'Logo footer', '', 'png', 85, 54, '3KB', 1, '2014-08-12 13:26:55'),
(3, 'GSDias logo', '', 'png', 128, 128, '8KB', 1, '2014-08-12 13:31:52'),
(4, 'Merchandising', '', 'jpg', 1483, 563, '471KB', 1, '2014-08-13 22:40:51'),
(5, 'test', '', 'jpg', 809, 544, '66KB', 1, '2014-08-20 23:10:15');
