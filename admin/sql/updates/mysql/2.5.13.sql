ALTER TABLE `#__poe_product` DROP `params`;
ALTER TABLE `#__poe_product` ADD  `related_group_id` INT( 11 ) NOT NULL AFTER  `dimuom`;
ALTER TABLE `#__poe_related_product` ADD  `sort_order` INT( 5 ) NOT NULL;
ALTER TABLE `#__poe_product` ADD  `show_related` TINYINT( 1 ) NOT NULL DEFAULT  '1' AFTER  `dimuom`;

CREATE TABLE IF NOT EXISTS `#__poe_option_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `json_optionset` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `#__poe_order` ADD  `pay_method_id` INT( 11 ) NOT NULL AFTER  `rfq_id`;
ALTER TABLE `#__poe_order` ADD  `currency_id` INT( 3 ) NOT NULL AFTER  `total`;
ALTER TABLE `#__poe_request` ADD `subtotal` decimal(10,2) NOT NULL AFTER  `juser_id`,
  ADD `total_discount` decimal(10,2) NOT NULL,
  ADD `product_tax` decimal(10,2) NOT NULL,
  ADD `shipping_cost` decimal(10,2) NOT NULL,
  ADD `shipping_tax` decimal(10,2) NOT NULL,
  ADD `selected_shipping` varchar(255) NOT NULL,
  ADD `billing_id` int(11) NOT NULL,
  ADD `shipping_id` int(11) NOT NULL,
  ADD `coupon_id` int(11) NOT NULL,
  ADD`ip_address` varchar(25) NOT NULL;

CREATE TABLE IF NOT EXISTS `#__poe_request_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `selected_options` text,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `product_tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `#__poe_tax_rate` CHANGE  `country_code`  `country_id` INT( 11 ) NOT NULL;
ALTER TABLE `#__poe_tax_rate` CHANGE  `region_code`  `region_id` INT( 11 ) NOT NULL;
ALTER TABLE `#__poe_tax_rate` ADD  `published` tinyint(1) NOT NULL DEFAULT '1';


