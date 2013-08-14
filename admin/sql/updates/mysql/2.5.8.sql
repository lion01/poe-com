ALTER TABLE  `#__poe_option_value` ADD  `weight_modifier_uom` INT( 11 ) NOT NULL  AFTER   `modifier_value` ;
ALTER TABLE  `#__poe_option_value` ADD  `weight_modifier` CHAR( 2 ) NOT NULL  AFTER `weight_modifier_uom`  ;
ALTER TABLE  `#__poe_option_value` ADD  `weight_modifier_value` DECIMAL( 10, 2 ) NOT NULL AFTER  `weight_modifier` ;
ALTER TABLE  `#__poe_option_value` ADD  `length_modifier` CHAR( 2 ) NOT NULL AFTER  `weight_modifier_value` ;
ALTER TABLE  `#__poe_option_value` ADD  `length_modifier_value` DECIMAL( 10, 2 ) NOT NULL AFTER  `length_modifier` ;
ALTER TABLE  `#__poe_option_value` ADD  `length_modifier_uom` INT( 11 ) NOT NULL  AFTER   `length_modifier_value` ;
ALTER TABLE  `#__poe_option_value` ADD  `width_modifier` CHAR( 2 ) NOT NULL  AFTER `length_modifier_uom`  ;
ALTER TABLE  `#__poe_option_value` ADD  `width_modifier_value` DECIMAL( 10, 2 ) NOT NULL AFTER  `width_modifier` ;
ALTER TABLE  `#__poe_option_value` ADD  `width_modifier_uom` INT( 11 ) NOT NULL  AFTER  `width_modifier_value` ;
ALTER TABLE  `#__poe_option_value` ADD  `height_modifier` CHAR( 2 ) NOT NULL  AFTER   `width_modifier_uom`  ;
ALTER TABLE  `#__poe_option_value` ADD  `height_modifier_value` DECIMAL( 10, 2 ) NOT NULL AFTER  `height_modifier` ;
ALTER TABLE  `#__poe_option_value` ADD  `height_modifier_uom` INT( 11 ) NOT NULL  AFTER  `height_modifier_value`;


ALTER TABLE  `#__poe_location` ADD  `email` VARCHAR( 100 ) NOT NULL AFTER  `telephone2`


