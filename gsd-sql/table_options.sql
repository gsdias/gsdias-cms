--
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `name` varchar(20) NOT NULL PRIMARY KEY,
  `index` int(10) DEFAULT NULL,
  `label` varchar(30) NOT NULL,
  `value` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

--
-- Indexes for table `options`
--
INSERT INTO options (`name`, `label`, `value`, `index`) VALUES ('gsd-name', 'Nome', NULL, 1), ('gsd-email', 'Email', NULL, 2), ('gsd-ga', 'Google Analytics', NULL, 3), ('gsd-fb', 'Facebook', NULL, 4), ('gsd-locale_select', 'Lingua', 'en_GB', 5);
