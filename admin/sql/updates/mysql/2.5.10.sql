ALTER TABLE  `#__poe_order` ADD  `currency_id` INT( 11 ) NOT NULL DEFAULT  '1' AFTER  `total`;

DROP TABLE IF EXISTS `#__poe_country`;

DROP TABLE IF EXISTS `#__poe_region`;


