--
-- Database: `com_poecom 2.5.15`
--

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_constraint`
--

CREATE TABLE IF NOT EXISTS `#__poe_constraint` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `option_id` int(11) NOT NULL,
  `expression_id` int(4) NOT NULL,
  `limit_value` varchar(45) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_coupon`
--

CREATE TABLE IF NOT EXISTS `#__poe_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promotion_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `coupon_code` varchar(100) NOT NULL,
  `sequence_number` int(11) DEFAULT NULL,
  `rfq_id` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `status_id` int(11) NOT NULL DEFAULT '0',
  `pdf_file` varchar(255) CHARACTER SET ucs2 DEFAULT NULL,
  `printed` tinyint(1) NOT NULL DEFAULT '0',
  `emailed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_coupon_status`
--

CREATE TABLE IF NOT EXISTS `#__poe_coupon_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `#__poe_coupon_status`
--

INSERT INTO `#__poe_coupon_status` (`id`, `name`) VALUES
(1, 'Open'),
(2, 'Used'),
(3, 'Expired');

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_credit_cards`
--

CREATE TABLE IF NOT EXISTS `#__poe_credit_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `code` varchar(4) NOT NULL,
  `list_logo` varchar(50) NOT NULL,
  `logo` varchar(50) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `#__poe_credit_cards`
--

INSERT INTO `#__poe_credit_cards` (`id`, `name`, `code`, `list_logo`, `logo`, `enabled`) VALUES
(1, 'Visa', 'VISA', 'visa-list-logo.png', 'visa-logo.png', 1),
(2, 'MasterCard', 'MC', 'mc-list-logo.png', 'mc-logo.png', 1),
(3, 'American Express', 'AMEX', 'amex-list-logo.png', 'amex-logo.png', 1),
(4, 'Diners Club', 'DINE', 'diner-list-logo.png', 'list-logo.png', 1),
(5, 'Discover Card', 'DISC', 'discover-list-logo.png', 'discover-logo.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_currency`
--

CREATE TABLE IF NOT EXISTS `#__poe_currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `code` char(3) NOT NULL,
  `symbol` varchar(10) NOT NULL,
  `exchange_rate` float NOT NULL DEFAULT '1',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `#__poe_currency`
--

INSERT INTO `#__poe_currency` (`id`, `name`, `code`, `symbol`, `exchange_rate`, `published`) VALUES
(1, 'Canadian Dollar', 'CAD', 'C$', 1, 1),
(2, 'US Dollar', 'USD', '$', 1, 1),
(3, 'Pound Sterling', 'GBP', '&pound;', 1, 1),
(4, 'Australian Dollar ', 'AUD', 'A$', 1, 1),
(5, 'Euro', 'EUR', '&euro;', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_detail`
--

CREATE TABLE IF NOT EXISTS `#__poe_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `link_label` varchar(50) NOT NULL,
  `window_type` varchar(10) NOT NULL DEFAULT 'modal',
  `class` varchar(10) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `content_type` varchar(10) NOT NULL,
  `article_id` int(11) NOT NULL DEFAULT '0',
  `url` varchar(255) DEFAULT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_discount_amount_type`
--

CREATE TABLE IF NOT EXISTS `#__poe_discount_amount_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `#__poe_discount_amount_type`
--

INSERT INTO `#__poe_discount_amount_type` (`id`, `name`) VALUES
(1, 'Fixed'),
(2, 'Percentage');

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_discount_type`
--

CREATE TABLE IF NOT EXISTS `#__poe_discount_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `#__poe_discount_type`
--

INSERT INTO `#__poe_discount_type` (`id`, `name`) VALUES
(1, 'Order'),
(2, 'Product');

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_expression`
--

CREATE TABLE IF NOT EXISTS `#__poe_expression` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `symbol` varchar(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `#__poe_expression`
--

INSERT INTO `#__poe_expression` (`id`, `name`, `symbol`) VALUES
(1, 'Equal To', '=='),
(2, 'Not Equal', '!='),
(3, 'Minimum', '>='),
(4, 'Maximum', '<='),
(5, 'Greater Than', '>'),
(6, 'Less Than', '<'),
(7, 'Not Allowed', '<>'),
(8, 'Required', '!null');

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_flyer`
--

CREATE TABLE IF NOT EXISTS `#__poe_flyer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `header` text NOT NULL,
  `footer` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_flyer_block`
--

CREATE TABLE IF NOT EXISTS `#__poe_flyer_block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `template` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_flyer_row`
--

CREATE TABLE IF NOT EXISTS `#__poe_flyer_row` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `flyer_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `block1` int(11) NOT NULL,
  `block2` int(11) DEFAULT NULL,
  `block3` int(11) DEFAULT NULL,
  `block4` int(11) DEFAULT NULL,
  `ordering` tinyint(2) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_flyer_section`
--

CREATE TABLE IF NOT EXISTS `#__poe_flyer_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flyer_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `banner` text NOT NULL,
  `ordering` tinyint(2) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_location`
--

CREATE TABLE IF NOT EXISTS `#__poe_location` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `google_map_address` varchar(255) DEFAULT NULL,
  `latitude` varchar(50) NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `street1` varchar(100) NOT NULL,
  `street2` varchar(100) DEFAULT NULL,
  `city` varchar(50) NOT NULL,
  `region_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `telephone1` varchar(20) NOT NULL,
  `telephone2` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_option`
--

CREATE TABLE IF NOT EXISTS `#__poe_option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `dom_element` varchar(45) NOT NULL,
  `product_id` int(11) NOT NULL,
  `option_sku` varchar(50) DEFAULT NULL,
  `option_type_id` int(11) NOT NULL,
  `price_control_id` tinyint(1) NOT NULL DEFAULT '1',
  `class` varchar(50) DEFAULT NULL,
  `uom_id` int(11) DEFAULT NULL,
  `detail_id` int(11) DEFAULT NULL,
  `description` text,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_option_modifier`
--

CREATE TABLE IF NOT EXISTS `#__poe_option_modifier` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `symbol` char(2) NOT NULL,
  `text` varchar(25) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `symbol` (`symbol`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `#__poe_option_modifier`
--

INSERT INTO `#__poe_option_modifier` (`id`, `symbol`, `text`, `published`) VALUES
(1, 'N', 'No Modifier', 1),
(2, '+', 'Add', 1),
(3, '-', 'Subtract', 1),
(4, '+%', 'Add Percentage', 1),
(5, '-%', 'Subtract Percentage', 1),
(6, '=', 'Equals', 1);

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_option_set`
--

CREATE TABLE IF NOT EXISTS `#__poe_option_set` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `json_optionset` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_option_type`
--

CREATE TABLE IF NOT EXISTS `#__poe_option_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `#__poe_option_type`
--

INSERT INTO `#__poe_option_type` (`id`, `name`) VALUES
(1, 'select'),
(2, 'inputqty'),
(3, 'inputsize'),
(4, 'inputtext'),
(5, 'property');

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_option_value`
--

CREATE TABLE IF NOT EXISTS `#__poe_option_value` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `option_id` int(11) NOT NULL,
  `option_value` varchar(25) NOT NULL COMMENT 'Value used in select',
  `option_label` varchar(30) NOT NULL,
  `option_value_sku` varchar(50) DEFAULT NULL,
  `modifier` char(2) NOT NULL DEFAULT 'N',
  `modifier_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `weight_modifier_uom` int(11) DEFAULT NULL,
  `description` text,
  `weight_modifier` char(2) NOT NULL DEFAULT 'N',
  `weight_modifier_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_order`
--

CREATE TABLE IF NOT EXISTS `#__poe_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status_id` int(11) NOT NULL DEFAULT '1',
  `rfq_id` int(11) NOT NULL,
  `pay_method_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `order_date` datetime NOT NULL,
  `juser_id` int(11) NOT NULL,
  `billing_id` int(11) NOT NULL,
  `shipping_id` int(11) NOT NULL,
  `selected_shipping` varchar(255) NOT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  `coupon_id` int(11) DEFAULT NULL,
  `total_discount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `product_tax` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL,
  `shipping_tax` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `currency_id` int(3) NOT NULL,
  `ip_address` varchar(25) DEFAULT NULL,
 `email_sent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_order_line`
--

CREATE TABLE IF NOT EXISTS `#__poe_order_line` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `selected_options` text,
  `quantity` int(11) NOT NULL DEFAULT '0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `product_tax` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_order_status`
--

CREATE TABLE IF NOT EXISTS `#__poe_order_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `sort_order` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `#__poe_order_status`
--

INSERT INTO `#__poe_order_status` (`id`, `name`, `sort_order`) VALUES
(1, 'Open', 1),
(2, 'Invoiced', 2),
(3, 'Paid', 3),
(4, 'Shipped', 4),
(5, 'Canceled', 5);

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_payment_method`
--

CREATE TABLE IF NOT EXISTS `#__poe_payment_method` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL,
  `name` varchar(50) NOT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `receipt_fields` varchar(255) DEFAULT NULL,
  `plugin` varchar(100) NOT NULL,
  `pm_default` tinyint(1) NOT NULL DEFAULT '0',
  `pm_enabled` tinyint(1) NOT NULL,
  `sort_order` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_payment_status`
--

CREATE TABLE IF NOT EXISTS `#__poe_payment_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `sort_order` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `#__poe_payment_status`
--

INSERT INTO `#__poe_payment_status` (`id`, `name`, `sort_order`) VALUES
(1, 'pending', 1),
(2, 'complete', 2),
(3, 'failed', 3),
(4, 'waiting', 4),
(5, 'Account', 5);

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_payment_transaction`
--

CREATE TABLE IF NOT EXISTS `#__poe_payment_transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_number` varchar(255) NOT NULL,
  `type_id` int(11) NOT NULL,
  `amount` double(10,2) NOT NULL,
  `pay_method_id` int(11) NOT NULL,
  `rfq_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `status_id` int(11) NOT NULL DEFAULT '1',
  `transaction` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_payment_transaction_type`
--

CREATE TABLE IF NOT EXISTS `#__poe_payment_transaction_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `sort_order` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `#__poe_payment_transaction_type`
--

INSERT INTO `#__poe_payment_transaction_type` (`id`, `name`, `sort_order`) VALUES
(1, 'Payment', 1),
(2, 'Authorization', 2),
(3, 'Refund', 3);

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_product`
--

CREATE TABLE IF NOT EXISTS `#__poe_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL DEFAULT '0',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `name` varchar(45) NOT NULL,
  `sku` varchar(45) NOT NULL,
  `type` tinyint(1) NOT NULL DEFAULT '1',
  `price_config` varchar(20) NOT NULL DEFAULT '0,0,0,0,0',
  `price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `tax_exempt_ids` varchar(10) DEFAULT '["0"]',
  `order_allowed` tinyint(1) NOT NULL DEFAULT '0',
  `default_qty` int(5) NOT NULL DEFAULT '1',
  `max_qty` int(5) NOT NULL DEFAULT '0',
  `list_description` text NOT NULL,
  `tablabel` varchar(50) NOT NULL,
  `description` text,
  `main_image_width` int(5) NOT NULL,
  `show_zoom` tinyint(1) NOT NULL DEFAULT '0',
  `mainimage` varchar(255) DEFAULT NULL,
  `thumbimage` varchar(255) DEFAULT NULL,
  `weight` decimal(7,3) NOT NULL DEFAULT '0.000',
  `weightuom` int(11) NOT NULL,
  `length` decimal(7,3) NOT NULL DEFAULT '0.000',
  `width` decimal(7,3) NOT NULL DEFAULT '0.000',
  `height` decimal(7,3) NOT NULL DEFAULT '0.000',
  `dimuom` int(11) NOT NULL,
  `show_related` tinyint(1) NOT NULL DEFAULT '1',
  `related_group_id` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `page_title` varchar(255) NOT NULL,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `metadata` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_product_category_xref`
--

CREATE TABLE IF NOT EXISTS `#__poe_product_category_xref` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `ordering` int(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_product_image`
--

CREATE TABLE IF NOT EXISTS `#__poe_product_image` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `type` tinyint(1) NOT NULL,
  `related_image` int(11) NOT NULL,
  `src` varchar(255) NOT NULL,
  `alt` varchar(255) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `width` int(5) NOT NULL,
  `height` int(5) NOT NULL,
  `sort_order` tinyint(2) NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_product_image_type`
--

CREATE TABLE IF NOT EXISTS `#__poe_product_image_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `#__poe_product_image_type`
--

INSERT INTO `#__poe_product_image_type` (`id`, `name`) VALUES
(1, 'Main Image'),
(2, 'Thumb Image');

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_product_tab`
--

CREATE TABLE IF NOT EXISTS `#__poe_product_tab` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `label` varchar(50) NOT NULL,
  `content` text NOT NULL,
  `ordering` int(3) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_promotion`
--

CREATE TABLE IF NOT EXISTS `#__poe_promotion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `promotion_type_id` int(11) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `discount_type_id` int(11) NOT NULL,
  `discount_amount_type_id` int(11) NOT NULL,
  `discount_amount` double(10,2) NOT NULL,
  `order_amount_min` double(10,2) NOT NULL DEFAULT '0.00',
  `product_list` text,
  `product_qty_min` int(11) NOT NULL DEFAULT '0',
  `max_value` double(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_promotion_type`
--

CREATE TABLE IF NOT EXISTS `#__poe_promotion_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `#__poe_promotion_type`
--

INSERT INTO `#__poe_promotion_type` (`id`, `name`) VALUES
(1, 'Customer Direct'),
(2, 'General'),
(3, 'Numbered');

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_related_product`
--

CREATE TABLE IF NOT EXISTS `#__poe_related_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_related_product_group`
--

CREATE TABLE IF NOT EXISTS `#__poe_related_product_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_request`
--

CREATE TABLE IF NOT EXISTS `#__poe_request` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(100) NOT NULL,
  `status_id` int(11) NOT NULL DEFAULT '1',
  `order_id` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `currency_code` char(3) NOT NULL,
  `cart` blob NOT NULL,
  `juser_id` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `total_discount` decimal(10,2) NOT NULL,
  `product_tax` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL,
  `shipping_tax` decimal(10,2) NOT NULL,
  `selected_shipping` varchar(255) NOT NULL,
  `billing_id` int(11) NOT NULL,
  `shipping_id` int(11) NOT NULL,
  `coupon_id` int(11) NOT NULL,
  `ip_address` varchar(25) NOT NULL
  `email_sent` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `number` (`number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_request_line`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_request_status`
--

CREATE TABLE IF NOT EXISTS `#__poe_request_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `sort_order` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `#__poe_request_status`
--

INSERT INTO `#__poe_request_status` (`id`, `name`, `sort_order`) VALUES
(1, 'open', 1),
(2, 'ordered', 2),
(3, 'canceled', 3);

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_shipping_method`
--

CREATE TABLE IF NOT EXISTS `#__poe_shipping_method` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `plugin` varchar(50) NOT NULL,
  `sm_default` tinyint(1) NOT NULL DEFAULT '0',
  `sm_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` tinyint(2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plugin` (`plugin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_shipping_pkg`
--

CREATE TABLE IF NOT EXISTS `#__poe_shipping_pkg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `carrier` int(11) NOT NULL,
  `dest_country_id` int(11) NOT NULL,
  `length` decimal(5,2) NOT NULL,
  `width` decimal(5,2) NOT NULL,
  `height` decimal(5,2) NOT NULL,
  `dim_uom_id` int(11) NOT NULL,
  `box_weight` decimal(5,3) NOT NULL,
  `weight_limit` decimal(5,3) NOT NULL,
  `wgt_uom_id` int(11) NOT NULL,
  `pkg_cost` double NOT NULL,
  `handling_fee` double NOT NULL,
  `published` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_tax_rate`
--

CREATE TABLE IF NOT EXISTS `#__poe_tax_rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `rate` decimal(6,5) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_tax_type`
--

CREATE TABLE IF NOT EXISTS `#__poe_tax_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(3) NOT NULL,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `#__poe_tax_type`
--

INSERT INTO `#__poe_tax_type` (`id`, `code`, `name`) VALUES
(1, 'GST', 'Goods and Services Tax'),
(2, 'PST', 'Provincial Sales Tax'),
(3, 'TVQ', 'Taxe de vente du Qu');

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_uom`
--

CREATE TABLE IF NOT EXISTS `#__poe_uom` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `system` enum('SI','US') NOT NULL DEFAULT 'SI',
  `type` enum('length','mass','area','volume') NOT NULL,
  `symbol` varchar(5) NOT NULL,
  `plural_name` varchar(45) NOT NULL,
  `length_uom_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=15 ;

--
-- Dumping data for table `#__poe_uom`
--

INSERT INTO `#__poe_uom` (`id`, `name`, `system`, `type`, `symbol`, `plural_name`, `length_uom_id`) VALUES
(1, 'metre', 'SI', 'length', 'm', 'metres', NULL),
(2, 'millimeter', 'SI', 'length', 'mm', 'millimeters', NULL),
(3, 'centimeter', 'SI', 'length', 'cm', 'centimeters', NULL),
(4, 'kilogram', 'SI', 'mass', 'kg', 'kilograms', NULL),
(5, 'gram', 'SI', 'mass', 'g', 'grams', NULL),
(6, 'inch', 'US', 'length', 'in', 'inches', NULL),
(7, 'foot', 'US', 'length', 'ft', 'feet', NULL),
(8, 'ounce', 'US', 'mass', 'oz', 'ounces', NULL),
(9, 'pound', 'US', 'mass', 'lbs', 'pounds', NULL),
(10, 'square foot', 'US', 'area', 'sq ft', 'square feet', 7),
(11, 'square metre', 'SI', 'area', 'sq m', 'square metres', 1),
(12, 'sq inch', 'US', 'area', 'sq in', 'sq inches', 6),
(13, 'square centimeter', 'SI', 'area', 'sq cm', 'square centimeters', 3),
(14, 'square millimeter', 'SI', 'area', 'sq mm', 'square millimeters', 2);

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_uom_conversion`
--

CREATE TABLE IF NOT EXISTS `#__poe_uom_conversion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uom_1` int(11) NOT NULL,
  `uom_2` int(11) NOT NULL,
  `factor` float NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uom_1_UNIQUE` (`uom_1`,`uom_2`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `#__poe_uom_conversion`
--

INSERT INTO `#__poe_uom_conversion` (`id`, `uom_1`, `uom_2`, `factor`) VALUES
(1, 1, 2, 1000),
(2, 1, 3, 100),
(3, 1, 6, 39.97),
(4, 1, 7, 3.281),
(5, 4, 5, 1000),
(6, 4, 8, 35.274),
(7, 4, 9, 2.205),
(8, 3, 6, 0.393701);

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_users`
--

CREATE TABLE IF NOT EXISTS `#__poe_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `juser_id` int(11) NOT NULL,
  `billto_id` int(11) NOT NULL DEFAULT '0',
  `shipto_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_user_address`
--

CREATE TABLE IF NOT EXISTS `#__poe_user_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `juser_id` int(11) NOT NULL,
  `address_type` char(2) NOT NULL DEFAULT 'BT',
  `fname` varchar(30) NOT NULL,
  `lname` varchar(30) NOT NULL,
  `street1` varchar(50) NOT NULL,
  `street2` varchar(50) DEFAULT NULL,
  `city` varchar(50) NOT NULL,
  `region_id` int(11) NOT NULL,
  `country_id` int(11) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;