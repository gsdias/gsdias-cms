ALTER TABLE `options` CHANGE `name` `name` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `pages_review` ADD `parent` INT(12) DEFAULT NULL;

INSERT INTO options (`name`, `label`, `value`, `index`) VALUES
('gsd-maintenance_checkbox', '{LANG_MAINTENANCE_LABEL}', NULL, 6),
('gsd-debug_checkbox', '{LANG_DEBUG_LABEL}', NULL, 7);
