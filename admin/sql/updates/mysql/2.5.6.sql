
--
-- Table structure for table `#__poe_coupon`
--

CREATE TABLE IF NOT EXISTS `#__poe_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promotion_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `coupon_code` varchar(100) CHARACTER SET utf8 NOT NULL,
  `sequence_number` int(11) DEFAULT NULL,
  `rfq_id` int(11) NOT NULL DEFAULT '0',
  `order_id` int(11) NOT NULL DEFAULT '0',
  `status_id` int(11) NOT NULL DEFAULT '0',
  `pdf_file` varchar(255) DEFAULT NULL,
  `printed` tinyint(1) NOT NULL DEFAULT '0',
  `emailed` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=ucs2 AUTO_INCREMENT=82 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_coupon_status`
--
DROP TABLE IF EXISTS `#__poe_coupon_status`;
CREATE TABLE IF NOT EXISTS `#__poe_coupon_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `#__poe_coupon_status`
--

INSERT INTO `#__poe_coupon_status` (`id`, `name`) VALUES
(1, 'Open'),
(2, 'Used'),
(3, 'Expired');



--
-- Table structure for table `#__poe_discount_amount_type`
--
DROP TABLE IF EXISTS `#__poe_discount_amount_type`;
CREATE TABLE IF NOT EXISTS `#__poe_discount_amount_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

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
DROP TABLE IF EXISTS `#__poe_discount_type`;
CREATE TABLE IF NOT EXISTS `#__poe_discount_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `#__poe_discount_type`
--

INSERT INTO `#__poe_discount_type` (`id`, `name`) VALUES
(1, 'Order'),
(2, 'Product');


--
-- Table structure for table `#__poe_promotion`
--

CREATE TABLE IF NOT EXISTS `#__poe_promotion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 NOT NULL,
  `promotion_type_id` int(11) NOT NULL,
  `start_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `end_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `discount_type_id` int(11) NOT NULL,
  `discount_amount_type_id` int(11) NOT NULL,
  `discount_amount` double(10,2) NOT NULL,
  `order_amount_min` double(10,2) NOT NULL DEFAULT '0.00',
  `product_list` text CHARACTER SET utf8,
  `product_qty_min` int(11) NOT NULL DEFAULT '0',
  `max_value` double(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__poe_promotion_type`
--
DROP TABLE IF EXISTS `#__poe_promotion_type`;
CREATE TABLE IF NOT EXISTS `#__poe_promotion_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) CHARACTER SET latin1 NOT NULL,
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

ALTER TABLE  `#__poe_order` ADD  `coupon_id` INT( 11 ) NULL AFTER  `subtotal` ,
ADD  `total_discount` DECIMAL( 10, 2 ) NOT NULL DEFAULT  '0' AFTER  `coupon_id`;

--
-- Table structure for table `#__poe_credit_cards`
--
DROP TABLE IF EXISTS `#__poe_credit_cards`;
CREATE TABLE IF NOT EXISTS `#__poe_credit_cards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(25) NOT NULL,
  `code` varchar(4) NOT NULL,
  `list_logo` varchar(50) NOT NULL,
  `logo` varchar(50) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

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
