ALTER TABLE  `lpm_issue_counters` ADD  `imgsCount` INT( 11 ) NOT NULL DEFAULT  '0' COMMENT  'количество картинок, прикрепленных к задаче' AFTER `commentsCount`;
INSERT INTO `lpm_issue_counters` (issueId,imgsCount) SELECT `is`.`id`, (select count(*) from `lpm_images` as `i` where i.itemId = is.id) as `count` FROM `lpm_issues` as `is` WHERE `deleted` = 0 on duplicate key update `imgsCount` = VALUES(`imgsCount`);

