ALTER TABLE  `#__poe_option` CHANGE  `sort_order`  `ordering` INT( 11 ) NOT NULL DEFAULT  '0';

ALTER TABLE  `#__poe_option_value` CHANGE  `sort_order`  `ordering` INT( 11 ) NOT NULL DEFAULT  '0';

CREATE TABLE IF NOT EXISTS `#__poe_flyer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `header` text NOT NULL,
  `footer` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `#__poe_flyer_block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `template` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

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

CREATE TABLE IF NOT EXISTS `#__poe_flyer_section` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flyer_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `banner` text NOT NULL,
  `ordering` tinyint(2) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;