--
-- Table structure for table `pages`
--

ALTER TABLE `pages` CHANGE `url` `url` VARCHAR(251) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `pages` CHANGE `beautify` `beautify` VARCHAR(251) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

INSERT INTO options (`name`, `label`, `value`, `index`) VALUES
('gsd-gtm', 'Google Tag Manager', NULL, 4);