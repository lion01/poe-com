
--
-- Table structure for table `#__poe_payment_status`
--
DROP TABLE IF EXISTS `#__poe_payment_status` ;
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
(1, 'Pending', 1),
(2, 'Complete', 2),
(3, 'Failed', 3),
(4, 'Waiting', 4),
(5, 'Account', 5);

--
-- Table structure for table `#__poe_region`
--
DROP TABLE IF EXISTS `#__poe_region`;
CREATE TABLE IF NOT EXISTS `#__poe_region` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `code2` char(5) NOT NULL,
  `country_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=477 ;

--
-- Dumping data for table `#__poe_region`
--

INSERT INTO `#__poe_region` (`id`, `name`, `code2`, `country_id`) VALUES
(1, 'Alabama', 'AL', 222),
(2, 'Alaska', 'AK', 222),
(3, 'Arizona', 'AZ', 222),
(4, 'Arkansas', 'AR', 222),
(5, 'California', 'CA', 222),
(6, 'Colorado', 'CO', 222),
(7, 'Connecticut', 'CT', 222),
(8, 'Delaware', 'DE', 222),
(9, 'District Of Columbia', 'DC', 222),
(10, 'Florida', 'FL', 222),
(11, 'Georgia', 'GA', 222),
(12, 'Hawaii', 'HI', 222),
(13, 'Idaho', 'ID', 222),
(14, 'Illinois', 'IL', 222),
(15, 'Indiana', 'IN', 222),
(16, 'Iowa', 'IA', 222),
(17, 'Kansas', 'KS', 222),
(18, 'Kentucky', 'KY', 222),
(19, 'Louisiana', 'LA', 222),
(20, 'Maine', 'ME', 222),
(21, 'Maryland', 'MD', 222),
(22, 'Massachusetts', 'MA', 222),
(23, 'Michigan', 'MI', 222),
(24, 'Minnesota', 'MN', 222),
(25, 'Mississippi', 'MS', 222),
(26, 'Missouri', 'MO', 222),
(27, 'Montana', 'MT', 222),
(28, 'Nebraska', 'NE', 222),
(29, 'Nevada', 'NV', 222),
(30, 'New Hampshire', 'NH', 222),
(31, 'New Jersey', 'NJ', 222),
(32, 'New Mexico', 'NM', 222),
(33, 'New York', 'NY', 222),
(34, 'North Carolina', 'NC', 222),
(35, 'North Dakota', 'ND', 222),
(36, 'Ohio', 'OH', 222),
(37, 'Oklahoma', 'OK', 222),
(38, 'Oregon', 'OR', 222),
(39, 'Pennsylvania', 'PA', 222),
(40, 'Rhode Island', 'RI', 222),
(41, 'South Carolina', 'SC', 222),
(42, 'South Dakota', 'SD', 222),
(43, 'Tennessee', 'TN', 222),
(44, 'Texas', 'TX', 222),
(45, 'Utah', 'UT', 222),
(46, 'Vermont', 'VT', 222),
(47, 'Virginia', 'VA', 222),
(48, 'Washington', 'WA', 222),
(49, 'West Virginia', 'WV', 222),
(50, 'Wisconsin', 'WI', 222),
(51, 'Wyoming', 'WY', 222),
(52, 'Alberta', 'AB', 38),
(53, 'British Columbia', 'BC', 38),
(54, 'Manitoba', 'MB', 38),
(55, 'New Brunswick', 'NB', 38),
(56, 'Newfoundland and Labrador', 'NL', 38),
(57, 'Northwest Territories', 'NT', 38),
(58, 'Nova Scotia', 'NS', 38),
(59, 'Nunavut', 'NU', 38),
(60, 'Ontario', 'ON', 38),
(61, 'Prince Edward Island', 'PE', 38),
(62, 'Quebec', 'QC', 38),
(63, 'Saskatchewan', 'SK', 38),
(64, 'Yukon', 'YT', 38),
(65, 'England', 'EN', 222),
(66, 'Northern Ireland', 'NI', 222),
(67, 'Scotland', 'SD', 222),
(68, 'Wales', 'WS', 222),
(69, 'Australian Capital Territory', 'AC', 13),
(70, 'New South Wales', 'NS', 13),
(71, 'Northern Territory', 'NT', 13),
(72, 'Queensland', 'QL', 13),
(73, 'South Australia', 'SA', 13),
(74, 'Tasmania', 'TS', 13),
(75, 'Victoria', 'VI', 13),
(76, 'Western Australia', 'WA', 13),
(77, 'Aguascalientes', 'AG', 138),
(78, 'Baja California Norte', 'BN', 138),
(79, 'Baja California Sur', 'BS', 138),
(80, 'Campeche', 'CA', 138),
(81, 'Chiapas', 'CS', 138),
(82, 'Chihuahua', 'CH', 138),
(83, 'Coahuila', 'CO', 138),
(84, 'Colima', 'CM', 138),
(85, 'Distrito Federal', 'DF', 138),
(86, 'Durango', 'DO', 138),
(87, 'Guanajuato', 'GO', 138),
(88, 'Guerrero', 'GU', 138),
(89, 'Hidalgo', 'HI', 138),
(90, 'Jalisco', 'JA', 138),
(91, 'M', 'EM', 138),
(92, 'Michoac', 'MI', 138),
(93, 'Morelos', 'MO', 138),
(94, 'Nayarit', 'NY', 138),
(95, 'Nuevo Le', 'NL', 138),
(96, 'Oaxaca', 'OA', 138),
(97, 'Puebla', 'PU', 138),
(98, 'Quer', 'QU', 138),
(99, 'Quintana Roo', 'QR', 138),
(100, 'San Luis Potos', 'SP', 138),
(101, 'Sinaloa', 'SI', 138),
(102, 'Sonora', 'SO', 138),
(103, 'Tabasco', 'TA', 138),
(104, 'Tamaulipas', 'TM', 138),
(105, 'Tlaxcala', 'TX', 138),
(106, 'Veracruz', 'VZ', 138),
(107, 'Yucat', 'YU', 138),
(108, 'Zacatecas', 'ZA', 138),
(109, 'Acre', 'AC', 30),
(110, 'Alagoas', 'AL', 30),
(111, 'Amap', 'AP', 30),
(112, 'Amazonas', 'AM', 30),
(113, 'Bah', 'BA', 30),
(114, 'Cear', 'CE', 30),
(115, 'Distrito Federal', 'DF', 30),
(116, 'Espirito Santo', 'ES', 30),
(117, 'Goi', 'GO', 30),
(118, 'Maranh', 'MA', 30),
(119, 'Mato Grosso', 'MT', 30),
(120, 'Mato Grosso do Sul', 'MS', 30),
(121, 'Minas Gera', 'MG', 30),
(122, 'Paran', 'PR', 30),
(123, 'Para', 'PB', 30),
(124, 'Par', 'PA', 30),
(125, 'Pernambuco', 'PE', 30),
(126, 'Piau', 'PI', 30),
(127, 'Rio Grande do Norte', 'RN', 30),
(128, 'Rio Grande do Sul', 'RS', 30),
(129, 'Rio de Janeiro', 'RJ', 30),
(130, 'Rond', 'RO', 30),
(131, 'Roraima', 'RR', 30),
(132, 'Santa Catarina', 'SC', 30),
(133, 'Sergipe', 'SE', 30),
(134, 'S', 'SP', 30),
(135, 'Tocantins', 'TO', 30),
(136, 'Anhui', '34', 44),
(137, 'Beijing', '11', 44),
(138, 'Chongqing', '50', 44),
(139, 'Fujian', '35', 44),
(140, 'Gansu', '62', 44),
(141, 'Guangdong', '44', 44),
(142, 'Guangxi Zhuang', '45', 44),
(143, 'Guizhou', '52', 44),
(144, 'Hainan', '46', 44),
(145, 'Hebei', '13', 44),
(146, 'Heilongjiang', '23', 44),
(147, 'Henan', '41', 44),
(148, 'Hubei', '42', 44),
(149, 'Hunan', '43', 44),
(150, 'Jiangsu', '32', 44),
(151, 'Jiangxi', '36', 44),
(152, 'Jilin', '22', 44),
(153, 'Liaoning', '21', 44),
(154, 'Nei Mongol', '15', 44),
(155, 'Ningxia Hui', '64', 44),
(156, 'Qinghai', '63', 44),
(157, 'Shandong', '37', 44),
(158, 'Shanghai', '31', 44),
(159, 'Shaanxi', '61', 44),
(160, 'Sichuan', '51', 44),
(161, 'Tianjin', '12', 44),
(162, 'Xinjiang Uygur', '65', 44),
(163, 'Xizang', '54', 44),
(164, 'Yunnan', '53', 44),
(165, 'Zhejiang', '33', 44),
(166, 'Israel', 'IL', 104),
(167, 'Gaza Strip', 'GZ', 104),
(168, 'West Bank', 'WB', 104),
(169, 'St. Maarten', 'SM', 151),
(170, 'Bonaire', 'BN', 151),
(171, 'Curacao', 'CR', 151),
(172, 'Alba', 'AB', 175),
(173, 'Arad', 'AR', 175),
(174, 'Arges', 'AG', 175),
(175, 'Bacau', 'BC', 175),
(176, 'Bihor', 'BH', 175),
(177, 'Bistrita-Nasaud', 'BN', 175),
(178, 'Botosani', 'BT', 175),
(179, 'Braila', 'BR', 175),
(180, 'Brasov', 'BV', 175),
(181, 'Bucuresti', 'B', 175),
(182, 'Buzau', 'BZ', 175),
(183, 'Calarasi', 'CL', 175),
(184, 'Caras Severin', 'CS', 175),
(185, 'Cluj', 'CJ', 175),
(186, 'Constanta', 'CT', 175),
(187, 'Covasna', 'CV', 175),
(188, 'Dambovita', 'DB', 175),
(189, 'Dolj', 'DJ', 175),
(190, 'Galati', 'GL', 175),
(191, 'Giurgiu', 'GR', 175),
(192, 'Gorj', 'GJ', 175),
(193, 'Hargita', 'HR', 175),
(194, 'Hunedoara', 'HD', 175),
(195, 'Ialomita', 'IL', 175),
(196, 'Iasi', 'IS', 175),
(197, 'Ilfov', 'IF', 175),
(198, 'Maramures', 'MM', 175),
(199, 'Mehedinti', 'MH', 175),
(200, 'Mures', 'MS', 175),
(201, 'Neamt', 'NT', 175),
(202, 'Olt', 'OT', 175),
(203, 'Prahova', 'PH', 175),
(204, 'Salaj', 'SJ', 175),
(205, 'Satu Mare', 'SM', 175),
(206, 'Sibiu', 'SB', 175),
(207, 'Suceava', 'SV', 175),
(208, 'Teleorman', 'TR', 175),
(209, 'Timis', 'TM', 175),
(210, 'Tulcea', 'TL', 175),
(211, 'Valcea', 'VL', 175),
(212, 'Vaslui', 'VS', 175),
(213, 'Vrancea', 'VN', 175),
(214, 'Agrigento', 'AG', 105),
(215, 'Alessandria', 'AL', 105),
(216, 'Ancona', 'AN', 105),
(217, 'Aosta', 'AO', 105),
(218, 'Arezzo', 'AR', 105),
(219, 'Ascoli Piceno', 'AP', 105),
(220, 'Asti', 'AT', 105),
(221, 'Avellino', 'AV', 105),
(222, 'Bari', 'BA', 105),
(223, 'Belluno', 'BL', 105),
(224, 'Benevento', 'BN', 105),
(225, 'Bergamo', 'BG', 105),
(226, 'Biella', 'BI', 105),
(227, 'Bologna', 'BO', 105),
(228, 'Bolzano', 'BZ', 105),
(229, 'Brescia', 'BS', 105),
(230, 'Brindisi', 'BR', 105),
(231, 'Cagliari', 'CA', 105),
(232, 'Caltanissetta', 'CL', 105),
(233, 'Campobasso', 'CB', 105),
(234, 'Carbonia-Iglesias', 'CI', 105),
(235, 'Caserta', 'CE', 105),
(236, 'Catania', 'CT', 105),
(237, 'Catanzaro', 'CZ', 105),
(238, 'Chieti', 'CH', 105),
(239, 'Como', 'CO', 105),
(240, 'Cosenza', 'CS', 105),
(241, 'Cremona', 'CR', 105),
(242, 'Crotone', 'KR', 105),
(243, 'Cuneo', 'CN', 105),
(244, 'Enna', 'EN', 105),
(245, 'Ferrara', 'FE', 105),
(246, 'Firenze', 'FI', 105),
(247, 'Foggia', 'FG', 105),
(248, 'Forli-Cesena', 'FC', 105),
(249, 'Frosinone', 'FR', 105),
(250, 'Genova', 'GE', 105),
(251, 'Gorizia', 'GO', 105),
(252, 'Grosseto', 'GR', 105),
(253, 'Imperia', 'IM', 105),
(254, 'Isernia', 'IS', 105),
(255, 'L''Aquila', 'AQ', 105),
(256, 'La Spezia', 'SP', 105),
(257, 'Latina', 'LT', 105),
(258, 'Lecce', 'LE', 105),
(259, 'Lecco', 'LC', 105),
(260, 'Livorno', 'LI', 105),
(261, 'Lodi', 'LO', 105),
(262, 'Lucca', 'LU', 105),
(263, 'Macerata', 'MC', 105),
(264, 'Mantova', 'MN', 105),
(265, 'Massa-Carrara', 'MS', 105),
(266, 'Matera', 'MT', 105),
(267, 'Medio Campidano', 'VS', 105),
(268, 'Messina', 'ME', 105),
(269, 'Milano', 'MI', 105),
(270, 'Modena', 'MO', 105),
(271, 'Napoli', 'NA', 105),
(272, 'Novara', 'NO', 105),
(273, 'Nuoro', 'NU', 105),
(274, 'Ogliastra', 'OG', 105),
(275, 'Olbia-Tempio', 'OT', 105),
(276, 'Oristano', 'OR', 105),
(277, 'Padova', 'PD', 105),
(278, 'Palermo', 'PA', 105),
(279, 'Parma', 'PR', 105),
(280, 'Pavia', 'PV', 105),
(281, 'Perugia', 'PG', 105),
(282, 'Pesaro e Urbino', 'PU', 105),
(283, 'Pescara', 'PE', 105),
(284, 'Piacenza', 'PC', 105),
(285, 'Pisa', 'PI', 105),
(286, 'Pistoia', 'PT', 105),
(287, 'Pordenone', 'PN', 105),
(288, 'Potenza', 'PZ', 105),
(289, 'Prato', 'PO', 105),
(290, 'Ragusa', 'RG', 105),
(291, 'Ravenna', 'RA', 105),
(292, 'Reggio Calabria', 'RC', 105),
(293, 'Reggio Emilia', 'RE', 105),
(294, 'Rieti', 'RI', 105),
(295, 'Rimini', 'RN', 105),
(296, 'Roma', 'RM', 105),
(297, 'Rovigo', 'RO', 105),
(298, 'Salerno', 'SA', 105),
(299, 'Sassari', 'SS', 105),
(300, 'Savona', 'SV', 105),
(301, 'Siena', 'SI', 105),
(302, 'Siracusa', 'SR', 105),
(303, 'Sondrio', 'SO', 105),
(304, 'Taranto', 'TA', 105),
(305, 'Teramo', 'TE', 105),
(306, 'Terni', 'TR', 105),
(307, 'Torino', 'TO', 105),
(308, 'Trapani', 'TP', 105),
(309, 'Trento', 'TN', 105),
(310, 'Treviso', 'TV', 105),
(311, 'Trieste', 'TS', 105),
(312, 'Udine', 'UD', 105),
(313, 'Varese', 'VA', 105),
(314, 'Venezia', 'VE', 105),
(315, 'Verbano Cusio Ossola', 'VB', 105),
(316, 'Vercelli', 'VC', 105),
(317, 'Verona', 'VR', 105),
(318, 'Vibo Valenzia', 'VV', 105),
(319, 'Vicenza', 'VI', 105),
(320, 'Viterbo', 'VT', 105),
(321, 'A Coru', '15', 195),
(322, 'Alava', '01', 195),
(323, 'Albacete', '02', 195),
(324, 'Alicante', '03', 195),
(325, 'Almeria', '04', 195),
(326, 'Asturias', '33', 195),
(327, 'Avila', '05', 195),
(328, 'Badajoz', '06', 195),
(329, 'Baleares', '07', 195),
(330, 'Barcelona', '08', 195),
(331, 'Burgos', '09', 195),
(332, 'Caceres', '10', 195),
(333, 'Cadiz', '11', 195),
(334, 'Cantabria', '39', 195),
(335, 'Castellon', '12', 195),
(336, 'Ceuta', '51', 195),
(337, 'Ciudad Real', '13', 195),
(338, 'Cordoba', '14', 195),
(339, 'Cuenca', '16', 195),
(340, 'Girona', '17', 195),
(341, 'Granada', '18', 195),
(342, 'Guadalajara', '19', 195),
(343, 'Guipuzcoa', '20', 195),
(344, 'Huelva', '21', 195),
(345, 'Huesca', '22', 195),
(346, 'Jaen', '23', 195),
(347, 'La Rioja', '26', 195),
(348, 'Las Palmas', '35', 195),
(349, 'Leon', '24', 195),
(350, 'Lleida', '25', 195),
(351, 'Lugo', '27', 195),
(352, 'Madrid', '28', 195),
(353, 'Malaga', '29', 195),
(354, 'Melilla', '52', 195),
(355, 'Murcia', '30', 195),
(356, 'Navarra', '31', 195),
(357, 'Ourense', '32', 195),
(358, 'Palencia', '34', 195),
(359, 'Pontevedra', '36', 195),
(360, 'Salamanca', '37', 195),
(361, 'Santa Cruz de Tenerife', '38', 195),
(362, 'Segovia', '40', 195),
(363, 'Sevilla', '41', 195),
(364, 'Soria', '42', 195),
(365, 'Tarragona', '43', 195),
(366, 'Teruel', '44', 195),
(367, 'Toledo', '45', 195),
(368, 'Valencia', '46', 195),
(369, 'Valladolid', '47', 195),
(370, 'Vizcaya', '48', 195),
(371, 'Zamora', '49', 195),
(372, 'Zaragoza', '50', 195),
(373, 'Buenos Aires', 'BA', 10),
(374, 'Ciudad Autonoma De Buenos Aires', 'CB', 10),
(375, 'Catamarca', 'CA', 10),
(376, 'Chaco', 'CH', 10),
(377, 'Chubut', 'CT', 10),
(378, 'Cordoba', 'CO', 10),
(379, 'Corrientes', 'CR', 10),
(380, 'Entre Rios', 'ER', 10),
(381, 'Formosa', 'FR', 10),
(382, 'Jujuy', 'JU', 10),
(383, 'La Pampa', 'LP', 10),
(384, 'La Rioja', 'LR', 10),
(385, 'Mendoza', 'ME', 10),
(386, 'Misiones', 'MI', 10),
(387, 'Neuquen', 'NQ', 10),
(388, 'Rio Negro', 'RN', 10),
(389, 'Salta', 'SA', 10),
(390, 'San Juan', 'SJ', 10),
(391, 'San Luis', 'SL', 10),
(392, 'Santa Cruz', 'SC', 10),
(393, 'Santa Fe', 'SF', 10),
(394, 'Santiago Del Estero', 'SE', 10),
(395, 'Tierra Del Fuego', 'TF', 10),
(396, 'Tucuman', 'TU', 10),
(397, 'Aragatsotn', 'AG', 11),
(398, 'Ararat', 'AR', 11),
(399, 'Armavir', 'AV', 11),
(400, 'Gegharkunik', 'GR', 11),
(401, 'Kotayk', 'KT', 11),
(402, 'Lori', 'LO', 11),
(403, 'Shirak', 'SH', 11),
(404, 'Syunik', 'SU', 11),
(405, 'Tavush', 'TV', 11),
(406, 'Vayots-Dzor', 'VD', 11),
(407, 'Yerevan', 'ER', 11),
(408, 'Andaman & Nicobar Islands', 'AI', 99),
(409, 'Andhra Pradesh', 'AN', 99),
(410, 'Arunachal Pradesh', 'AR', 99),
(411, 'Assam', 'AS', 99),
(412, 'Bihar', 'BI', 99),
(413, 'Chandigarh', 'CA', 99),
(414, 'Chhatisgarh', 'CH', 99),
(415, 'Dadra & Nagar Haveli', 'DD', 99),
(416, 'Daman & Diu', 'DA', 99),
(417, 'Delhi', 'DE', 99),
(418, 'Goa', 'GO', 99),
(419, 'Gujarat', 'GU', 99),
(420, 'Haryana', 'HA', 99),
(421, 'Himachal Pradesh', 'HI', 99),
(422, 'Jammu & Kashmir', 'JA', 99),
(423, 'Jharkhand', 'JH', 99),
(424, 'Karnataka', 'KA', 99),
(425, 'Kerala', 'KE', 99),
(426, 'Lakshadweep', 'LA', 99),
(427, 'Madhya Pradesh', 'MD', 99),
(428, 'Maharashtra', 'MH', 99),
(429, 'Manipur', 'MN', 99),
(430, 'Meghalaya', 'ME', 99),
(431, 'Mizoram', 'MI', 99),
(432, 'Nagaland', 'NA', 99),
(433, 'Orissa', 'OR', 99),
(434, 'Pondicherry', 'PO', 99),
(435, 'Punjab', 'PU', 99),
(436, 'Rajasthan', 'RA', 99),
(437, 'Sikkim', 'SI', 99),
(438, 'Tamil Nadu', 'TA', 99),
(439, 'Tripura', 'TR', 99),
(440, 'Uttaranchal', 'UA', 99),
(441, 'Uttar Pradesh', 'UT', 99),
(442, 'West Bengal', 'WE', 99),
(443, 'Ahmadi va Kohkiluyeh', 'BO', 101),
(444, 'Ardabil', 'AR', 101),
(445, 'Azarbayjan-e Gharbi', 'AG', 101),
(446, 'Azarbayjan-e Sharqi', 'AS', 101),
(447, 'Bushehr', 'BU', 101),
(448, 'Chaharmahal va Bakhtiari', 'CM', 101),
(449, 'Esfahan', 'ES', 101),
(450, 'Fars', 'FA', 101),
(451, 'Gilan', 'GI', 101),
(452, 'Gorgan', 'GO', 101),
(453, 'Hamadan', 'HA', 101),
(454, 'Hormozgan', 'HO', 101),
(455, 'Ilam', 'IL', 101),
(456, 'Kerman', 'KE', 101),
(457, 'Kermanshah', 'BA', 101),
(458, 'Khorasan-e Junoubi', 'KJ', 101),
(459, 'Khorasan-e Razavi', 'KR', 101),
(460, 'Khorasan-e Shomali', 'KS', 101),
(461, 'Khuzestan', 'KH', 101),
(462, 'Kordestan', 'KO', 101),
(463, 'Lorestan', 'LO', 101),
(464, 'Markazi', 'MR', 101),
(465, 'Mazandaran', 'MZ', 101),
(466, 'Qazvin', 'QA', 101),
(467, 'Qom', 'QO', 101),
(468, 'Semnan', 'SE', 101),
(469, 'Sistan va Baluchestan', 'SB', 101),
(470, 'Tehran', 'TE', 101),
(471, 'Yazd', 'YA', 101),
(472, 'Zanjan', 'ZA', 101),
(473, 'Qaasuitsup', 'GL-QA', 84),
(474, 'Qeqqata', 'GL-QE', 84),
(475, 'Sermersooq', 'GL-SM', 84),
(476, 'Kujalleq', 'GL-KU', 84);

-- --------------------------------------------------------

ALTER TABLE  `#__poe_product` ADD  `list_description` VARCHAR( 255 ) NOT NULL AFTER  `description`

