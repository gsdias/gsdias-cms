--
-- Table structure for table `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `name` varchar(50) NOT NULL PRIMARY KEY,
  `index` int(10) DEFAULT NULL,
  `label` varchar(30) NOT NULL,
  `value` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ;

--
-- Indexes for table `options`
--
INSERT INTO options (`name`, `label`, `value`, `index`) VALUES ('gsd-name', '{LANG_NAME}', NULL, 1), ('gsd-email', '{LANG_EMAIL}', NULL, 2), ('gsd-ga', 'Google Analytics', NULL, 3), ('gsd-fb', 'Facebook', NULL, 4), ('gsd-locale_select', '{LANG_LANGUAGE}', 'pt_PT', 5), ('gsd-maintenance_checkbox', '{LANG_LANGUAGE}', 'pt_PT', 6);
