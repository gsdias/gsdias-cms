ALTER TABLE `users` CHANGE `notifications` `notifications` TEXT NULL DEFAULT NULL;
ALTER TABLE `pages` ADD `body` TEXT NULL AFTER `published`;
ALTER TABLE `pages_review` ADD `body` TEXT NULL AFTER `published`;
