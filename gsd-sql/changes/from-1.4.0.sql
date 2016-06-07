ALTER TABLE `pages` CHANGE `url` `url` VARCHAR(251) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;
ALTER TABLE `pages` CHANGE `beautify` `beautify` VARCHAR(251) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;

INSERT INTO options (`name`, `type`, `label`, `value`, `index`) VALUES
('gtm', 'input', 'Google Tag Manager', NULL, 4);
