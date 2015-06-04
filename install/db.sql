SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `regrate`
--

-- --------------------------------------------------------

--
-- Table structure for table `allowed_groups`
--

CREATE TABLE IF NOT EXISTS `#__allowed_groups` (
  `object_id` int(11) NOT NULL,
  `object_type` varchar(50) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ams_meta`
--

CREATE TABLE IF NOT EXISTS `#__ams_meta` (
`ams_meta_id` int(11) NOT NULL,
  `ams_page_id` int(11) DEFAULT NULL,
  `meta_key` varchar(50) DEFAULT NULL,
  `meta_value` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ams_nodes`
--

CREATE TABLE IF NOT EXISTS `#__ams_nodes` (
`ams_node_id` int(11) NOT NULL,
  `ams_page_id` int(11) DEFAULT NULL,
  `node` varchar(50) DEFAULT NULL,
  `content` text
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ams_nodes`
--


-- --------------------------------------------------------

--
-- Table structure for table `ams_pages`
--

CREATE TABLE IF NOT EXISTS `#__ams_pages` (
`ams_page_id` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `layout_id` int(11) NOT NULL DEFAULT '0',
  `namespace` varchar(50) DEFAULT NULL,
  `name` varchar(250) DEFAULT NULL,
  `date_created` int(11) DEFAULT NULL,
  `date_modified` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0',
  `status` int(1) DEFAULT '0',
  `public` int(1) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ams_pages`
--

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE IF NOT EXISTS `#__banner` (
`banner_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `banner_image`
--

CREATE TABLE IF NOT EXISTS `#__banner_image` (
`banner_image_id` int(11) NOT NULL,
  `banner_id` int(11) NOT NULL,
  `link` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `title` varchar(64) NOT NULL,
  `description` varchar(64) NOT NULL,
  `sort_order` int(3) NOT NULL DEFAULT '0'
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;




-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE IF NOT EXISTS `#__comments` (
`comment_id` int(11) NOT NULL,
  `ams_page_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `author` varchar(64) NOT NULL,
  `text` text NOT NULL,
  `rating` int(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__country` (
`country_id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `iso_code_2` varchar(2) NOT NULL,
  `iso_code_3` varchar(3) NOT NULL,
  `address_format` text NOT NULL,
  `postcode_required` tinyint(1) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM AUTO_INCREMENT=258 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `country`
--

INSERT INTO `#__country` (`country_id`, `name`, `iso_code_2`, `iso_code_3`, `address_format`, `postcode_required`, `status`) VALUES
(1, 'Afghanistan', 'AF', 'AFG', '', 0, 1),
(2, 'Albania', 'AL', 'ALB', '', 0, 1),
(3, 'Algeria', 'DZ', 'DZA', '', 0, 1),
(4, 'American Samoa', 'AS', 'ASM', '', 0, 1),
(5, 'Andorra', 'AD', 'AND', '', 0, 1),
(6, 'Angola', 'AO', 'AGO', '', 0, 1),
(7, 'Anguilla', 'AI', 'AIA', '', 0, 1),
(8, 'Antarctica', 'AQ', 'ATA', '', 0, 1),
(9, 'Antigua and Barbuda', 'AG', 'ATG', '', 0, 1),
(10, 'Argentina', 'AR', 'ARG', '', 0, 1),
(11, 'Armenia', 'AM', 'ARM', '', 0, 1),
(12, 'Aruba', 'AW', 'ABW', '', 0, 1),
(13, 'Australia', 'AU', 'AUS', '', 0, 1),
(14, 'Austria', 'AT', 'AUT', '', 0, 1),
(15, 'Azerbaijan', 'AZ', 'AZE', '', 0, 1),
(16, 'Bahamas', 'BS', 'BHS', '', 0, 1),
(17, 'Bahrain', 'BH', 'BHR', '', 0, 1),
(18, 'Bangladesh', 'BD', 'BGD', '', 0, 1),
(19, 'Barbados', 'BB', 'BRB', '', 0, 1),
(20, 'Belarus', 'BY', 'BLR', '', 0, 1),
(21, 'Belgium', 'BE', 'BEL', '{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{postcode} {city}\r\n{country}', 0, 1),
(22, 'Belize', 'BZ', 'BLZ', '', 0, 1),
(23, 'Benin', 'BJ', 'BEN', '', 0, 1),
(24, 'Bermuda', 'BM', 'BMU', '', 0, 1),
(25, 'Bhutan', 'BT', 'BTN', '', 0, 1),
(26, 'Bolivia', 'BO', 'BOL', '', 0, 1),
(27, 'Bosnia and Herzegovina', 'BA', 'BIH', '', 0, 1),
(28, 'Botswana', 'BW', 'BWA', '', 0, 1),
(29, 'Bouvet Island', 'BV', 'BVT', '', 0, 1),
(30, 'Brazil', 'BR', 'BRA', '', 0, 1),
(31, 'British Indian Ocean Territory', 'IO', 'IOT', '', 0, 1),
(32, 'Brunei Darussalam', 'BN', 'BRN', '', 0, 1),
(33, 'Bulgaria', 'BG', 'BGR', '', 0, 1),
(34, 'Burkina Faso', 'BF', 'BFA', '', 0, 1),
(35, 'Burundi', 'BI', 'BDI', '', 0, 1),
(36, 'Cambodia', 'KH', 'KHM', '', 0, 1),
(37, 'Cameroon', 'CM', 'CMR', '', 0, 1),
(38, 'Canada', 'CA', 'CAN', '', 0, 1),
(39, 'Cape Verde', 'CV', 'CPV', '', 0, 1),
(40, 'Cayman Islands', 'KY', 'CYM', '', 0, 1),
(41, 'Central African Republic', 'CF', 'CAF', '', 0, 1),
(42, 'Chad', 'TD', 'TCD', '', 0, 1),
(43, 'Chile', 'CL', 'CHL', '', 0, 1),
(44, 'China', 'CN', 'CHN', '', 0, 1),
(45, 'Christmas Island', 'CX', 'CXR', '', 0, 1),
(46, 'Cocos (Keeling) Islands', 'CC', 'CCK', '', 0, 1),
(47, 'Colombia', 'CO', 'COL', '', 0, 1),
(48, 'Comoros', 'KM', 'COM', '', 0, 1),
(49, 'Congo', 'CG', 'COG', '', 0, 1),
(50, 'Cook Islands', 'CK', 'COK', '', 0, 1),
(51, 'Costa Rica', 'CR', 'CRI', '', 0, 1),
(52, 'Cote D''Ivoire', 'CI', 'CIV', '', 0, 1),
(53, 'Croatia', 'HR', 'HRV', '', 0, 1),
(54, 'Cuba', 'CU', 'CUB', '', 0, 1),
(55, 'Cyprus', 'CY', 'CYP', '', 0, 1),
(56, 'Czech Republic', 'CZ', 'CZE', '', 0, 1),
(57, 'Denmark', 'DK', 'DNK', '', 0, 1),
(58, 'Djibouti', 'DJ', 'DJI', '', 0, 1),
(59, 'Dominica', 'DM', 'DMA', '', 0, 1),
(60, 'Dominican Republic', 'DO', 'DOM', '', 0, 1),
(61, 'East Timor', 'TL', 'TLS', '', 0, 1),
(62, 'Ecuador', 'EC', 'ECU', '', 0, 1),
(63, 'Egypt', 'EG', 'EGY', '', 0, 1),
(64, 'El Salvador', 'SV', 'SLV', '', 0, 1),
(65, 'Equatorial Guinea', 'GQ', 'GNQ', '', 0, 1),
(66, 'Eritrea', 'ER', 'ERI', '', 0, 1),
(67, 'Estonia', 'EE', 'EST', '', 0, 1),
(68, 'Ethiopia', 'ET', 'ETH', '', 0, 1),
(69, 'Falkland Islands (Malvinas)', 'FK', 'FLK', '', 0, 1),
(70, 'Faroe Islands', 'FO', 'FRO', '', 0, 1),
(71, 'Fiji', 'FJ', 'FJI', '', 0, 1),
(72, 'Finland', 'FI', 'FIN', '', 0, 1),
(74, 'France, Metropolitan', 'FR', 'FRA', '{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{postcode} {city}\r\n{country}', 0, 1),
(75, 'French Guiana', 'GF', 'GUF', '', 0, 1),
(76, 'French Polynesia', 'PF', 'PYF', '', 0, 1),
(77, 'French Southern Territories', 'TF', 'ATF', '', 0, 1),
(78, 'Gabon', 'GA', 'GAB', '', 0, 1),
(79, 'Gambia', 'GM', 'GMB', '', 0, 1),
(80, 'Georgia', 'GE', 'GEO', '', 0, 1),
(81, 'Germany', 'DE', 'DEU', '{company}\r\n{firstname} {lastname}\r\n{address_1}\r\n{address_2}\r\n{postcode} {city}\r\n{country}', 0, 1),
(82, 'Ghana', 'GH', 'GHA', '', 0, 1),
(83, 'Gibraltar', 'GI', 'GIB', '', 0, 1),
(84, 'Greece', 'GR', 'GRC', '', 0, 1),
(85, 'Greenland', 'GL', 'GRL', '', 0, 1),
(86, 'Grenada', 'GD', 'GRD', '', 0, 1),
(87, 'Guadeloupe', 'GP', 'GLP', '', 0, 1),
(88, 'Guam', 'GU', 'GUM', '', 0, 1),
(89, 'Guatemala', 'GT', 'GTM', '', 0, 1),
(90, 'Guinea', 'GN', 'GIN', '', 0, 1),
(91, 'Guinea-Bissau', 'GW', 'GNB', '', 0, 1),
(92, 'Guyana', 'GY', 'GUY', '', 0, 1),
(93, 'Haiti', 'HT', 'HTI', '', 0, 1),
(94, 'Heard and Mc Donald Islands', 'HM', 'HMD', '', 0, 1),
(95, 'Honduras', 'HN', 'HND', '', 0, 1),
(96, 'Hong Kong', 'HK', 'HKG', '', 0, 1),
(97, 'Hungary', 'HU', 'HUN', '', 0, 1),
(98, 'Iceland', 'IS', 'ISL', '', 0, 1),
(99, 'India', 'IN', 'IND', '', 0, 1),
(100, 'Indonesia', 'ID', 'IDN', '', 0, 1),
(101, 'Iran (Islamic Republic of)', 'IR', 'IRN', '', 0, 1),
(102, 'Iraq', 'IQ', 'IRQ', '', 0, 1),
(103, 'Ireland', 'IE', 'IRL', '', 0, 1),
(104, 'Israel', 'IL', 'ISR', '', 0, 1),
(105, 'Italy', 'IT', 'ITA', '', 0, 1),
(106, 'Jamaica', 'JM', 'JAM', '', 0, 1),
(107, 'Japan', 'JP', 'JPN', '', 0, 1),
(108, 'Jordan', 'JO', 'JOR', '', 0, 1),
(109, 'Kazakhstan', 'KZ', 'KAZ', '', 0, 1),
(110, 'Kenya', 'KE', 'KEN', '', 0, 1),
(111, 'Kiribati', 'KI', 'KIR', '', 0, 1),
(112, 'North Korea', 'KP', 'PRK', '', 0, 1),
(113, 'Korea, Republic of', 'KR', 'KOR', '', 0, 1),
(114, 'Kuwait', 'KW', 'KWT', '', 0, 1),
(115, 'Kyrgyzstan', 'KG', 'KGZ', '', 0, 1),
(116, 'Lao People''s Democratic Republic', 'LA', 'LAO', '', 0, 1),
(117, 'Latvia', 'LV', 'LVA', '', 0, 1),
(118, 'Lebanon', 'LB', 'LBN', '', 0, 1),
(119, 'Lesotho', 'LS', 'LSO', '', 0, 1),
(120, 'Liberia', 'LR', 'LBR', '', 0, 1),
(121, 'Libyan Arab Jamahiriya', 'LY', 'LBY', '', 0, 1),
(122, 'Liechtenstein', 'LI', 'LIE', '', 0, 1),
(123, 'Lithuania', 'LT', 'LTU', '', 0, 1),
(124, 'Luxembourg', 'LU', 'LUX', '', 0, 1),
(125, 'Macau', 'MO', 'MAC', '', 0, 1),
(126, 'FYROM', 'MK', 'MKD', '', 0, 1),
(127, 'Madagascar', 'MG', 'MDG', '', 0, 1),
(128, 'Malawi', 'MW', 'MWI', '', 0, 1),
(129, 'Malaysia', 'MY', 'MYS', '', 0, 1),
(130, 'Maldives', 'MV', 'MDV', '', 0, 1),
(131, 'Mali', 'ML', 'MLI', '', 0, 1),
(132, 'Malta', 'MT', 'MLT', '', 0, 1),
(133, 'Marshall Islands', 'MH', 'MHL', '', 0, 1),
(134, 'Martinique', 'MQ', 'MTQ', '', 0, 1),
(135, 'Mauritania', 'MR', 'MRT', '', 0, 1),
(136, 'Mauritius', 'MU', 'MUS', '', 0, 1),
(137, 'Mayotte', 'YT', 'MYT', '', 0, 1),
(138, 'Mexico', 'MX', 'MEX', '', 0, 1),
(139, 'Micronesia, Federated States of', 'FM', 'FSM', '', 0, 1),
(140, 'Moldova, Republic of', 'MD', 'MDA', '', 0, 1),
(141, 'Monaco', 'MC', 'MCO', '', 0, 1),
(142, 'Mongolia', 'MN', 'MNG', '', 0, 1),
(143, 'Montserrat', 'MS', 'MSR', '', 0, 1),
(144, 'Morocco', 'MA', 'MAR', '', 0, 1),
(145, 'Mozambique', 'MZ', 'MOZ', '', 0, 1),
(146, 'Myanmar', 'MM', 'MMR', '', 0, 1),
(147, 'Namibia', 'NA', 'NAM', '', 0, 1),
(148, 'Nauru', 'NR', 'NRU', '', 0, 1),
(149, 'Nepal', 'NP', 'NPL', '', 0, 1),
(150, 'Netherlands', 'NL', 'NLD', '', 0, 1),
(151, 'Netherlands Antilles', 'AN', 'ANT', '', 0, 1),
(152, 'New Caledonia', 'NC', 'NCL', '', 0, 1),
(153, 'New Zealand', 'NZ', 'NZL', '', 0, 1),
(154, 'Nicaragua', 'NI', 'NIC', '', 0, 1),
(155, 'Niger', 'NE', 'NER', '', 0, 1),
(156, 'Nigeria', 'NG', 'NGA', '', 0, 1),
(157, 'Niue', 'NU', 'NIU', '', 0, 1),
(158, 'Norfolk Island', 'NF', 'NFK', '', 0, 1),
(159, 'Northern Mariana Islands', 'MP', 'MNP', '', 0, 1),
(160, 'Norway', 'NO', 'NOR', '', 0, 1),
(161, 'Oman', 'OM', 'OMN', '', 0, 1),
(162, 'Pakistan', 'PK', 'PAK', '', 0, 1),
(163, 'Palau', 'PW', 'PLW', '', 0, 1),
(164, 'Panama', 'PA', 'PAN', '', 0, 1),
(165, 'Papua New Guinea', 'PG', 'PNG', '', 0, 1),
(166, 'Paraguay', 'PY', 'PRY', '', 0, 1),
(167, 'Peru', 'PE', 'PER', '', 0, 1),
(168, 'Philippines', 'PH', 'PHL', '', 0, 1),
(169, 'Pitcairn', 'PN', 'PCN', '', 0, 1),
(170, 'Poland', 'PL', 'POL', '', 0, 1),
(171, 'Portugal', 'PT', 'PRT', '', 0, 1),
(172, 'Puerto Rico', 'PR', 'PRI', '', 0, 1),
(173, 'Qatar', 'QA', 'QAT', '', 0, 1),
(174, 'Reunion', 'RE', 'REU', '', 0, 1),
(175, 'Romania', 'RO', 'ROM', '', 0, 1),
(176, 'Russian Federation', 'RU', 'RUS', '', 0, 1),
(177, 'Rwanda', 'RW', 'RWA', '', 0, 1),
(178, 'Saint Kitts and Nevis', 'KN', 'KNA', '', 0, 1),
(179, 'Saint Lucia', 'LC', 'LCA', '', 0, 1),
(180, 'Saint Vincent and the Grenadines', 'VC', 'VCT', '', 0, 1),
(181, 'Samoa', 'WS', 'WSM', '', 0, 1),
(182, 'San Marino', 'SM', 'SMR', '', 0, 1),
(183, 'Sao Tome and Principe', 'ST', 'STP', '', 0, 1),
(184, 'Saudi Arabia', 'SA', 'SAU', '', 0, 1),
(185, 'Senegal', 'SN', 'SEN', '', 0, 1),
(186, 'Seychelles', 'SC', 'SYC', '', 0, 1),
(187, 'Sierra Leone', 'SL', 'SLE', '', 0, 1),
(188, 'Singapore', 'SG', 'SGP', '', 0, 1),
(189, 'Slovak Republic', 'SK', 'SVK', '{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city} {postcode}\r\n{zone}\r\n{country}', 0, 1),
(190, 'Slovenia', 'SI', 'SVN', '', 0, 1),
(191, 'Solomon Islands', 'SB', 'SLB', '', 0, 1),
(192, 'Somalia', 'SO', 'SOM', '', 0, 1),
(193, 'South Africa', 'ZA', 'ZAF', '', 0, 1),
(194, 'South Georgia &amp; South Sandwich Islands', 'GS', 'SGS', '', 0, 1),
(195, 'Spain', 'ES', 'ESP', '', 0, 1),
(196, 'Sri Lanka', 'LK', 'LKA', '', 0, 1),
(197, 'St. Helena', 'SH', 'SHN', '', 0, 1),
(198, 'St. Pierre and Miquelon', 'PM', 'SPM', '', 0, 1),
(199, 'Sudan', 'SD', 'SDN', '', 0, 1),
(200, 'Suriname', 'SR', 'SUR', '', 0, 1),
(201, 'Svalbard and Jan Mayen Islands', 'SJ', 'SJM', '', 0, 1),
(202, 'Swaziland', 'SZ', 'SWZ', '', 0, 1),
(203, 'Sweden', 'SE', 'SWE', '{company}\r\n{firstname} {lastname}\r\n{address_1}\r\n{address_2}\r\n{postcode} {city}\r\n{country}', 0, 1),
(204, 'Switzerland', 'CH', 'CHE', '', 0, 1),
(205, 'Syrian Arab Republic', 'SY', 'SYR', '', 0, 1),
(206, 'Taiwan', 'TW', 'TWN', '', 0, 1),
(207, 'Tajikistan', 'TJ', 'TJK', '', 0, 1),
(208, 'Tanzania, United Republic of', 'TZ', 'TZA', '', 0, 1),
(209, 'Thailand', 'TH', 'THA', '', 0, 1),
(210, 'Togo', 'TG', 'TGO', '', 0, 1),
(211, 'Tokelau', 'TK', 'TKL', '', 0, 1),
(212, 'Tonga', 'TO', 'TON', '', 0, 1),
(213, 'Trinidad and Tobago', 'TT', 'TTO', '', 0, 1),
(214, 'Tunisia', 'TN', 'TUN', '', 0, 1),
(215, 'Turkey', 'TR', 'TUR', '', 0, 1),
(216, 'Turkmenistan', 'TM', 'TKM', '', 0, 1),
(217, 'Turks and Caicos Islands', 'TC', 'TCA', '', 0, 1),
(218, 'Tuvalu', 'TV', 'TUV', '', 0, 1),
(219, 'Uganda', 'UG', 'UGA', '', 0, 1),
(220, 'Ukraine', 'UA', 'UKR', '', 0, 1),
(221, 'United Arab Emirates', 'AE', 'ARE', '', 0, 1),
(222, 'United Kingdom', 'GB', 'GBR', '', 0, 1),
(223, 'United States', 'US', 'USA', '{firstname} {lastname}\r\n{company}\r\n{address_1}\r\n{address_2}\r\n{city}, {zone} {postcode}\r\n{country}', 0, 1),
(224, 'United States Minor Outlying Islands', 'UM', 'UMI', '', 0, 1),
(225, 'Uruguay', 'UY', 'URY', '', 0, 1),
(226, 'Uzbekistan', 'UZ', 'UZB', '', 0, 1),
(227, 'Vanuatu', 'VU', 'VUT', '', 0, 1),
(228, 'Vatican City State (Holy See)', 'VA', 'VAT', '', 0, 1),
(229, 'Venezuela', 'VE', 'VEN', '', 0, 1),
(230, 'Viet Nam', 'VN', 'VNM', '', 0, 1),
(231, 'Virgin Islands (British)', 'VG', 'VGB', '', 0, 1),
(232, 'Virgin Islands (U.S.)', 'VI', 'VIR', '', 0, 1),
(233, 'Wallis and Futuna Islands', 'WF', 'WLF', '', 0, 1),
(234, 'Western Sahara', 'EH', 'ESH', '', 0, 1),
(235, 'Yemen', 'YE', 'YEM', '', 0, 1),
(237, 'Democratic Republic of Congo', 'CD', 'COD', '', 0, 1),
(238, 'Zambia', 'ZM', 'ZMB', '', 0, 1),
(239, 'Zimbabwe', 'ZW', 'ZWE', '', 0, 1),
(242, 'Montenegro', 'ME', 'MNE', '', 0, 1),
(243, 'Serbia', 'RS', 'SRB', '', 0, 1),
(244, 'Aaland Islands', 'AX', 'ALA', '', 0, 1),
(245, 'Bonaire, Sint Eustatius and Saba', 'BQ', 'BES', '', 0, 1),
(246, 'Curacao', 'CW', 'CUW', '', 0, 1),
(247, 'Palestinian Territory, Occupied', 'PS', 'PSE', '', 0, 1),
(248, 'South Sudan', 'SS', 'SSD', '', 0, 1),
(249, 'St. Barthelemy', 'BL', 'BLM', '', 0, 1),
(250, 'St. Martin (French part)', 'MF', 'MAF', '', 0, 1),
(251, 'Canary Islands', 'IC', 'ICA', '', 0, 1),
(252, 'Ascension Island (British)', 'AC', 'ASC', '', 0, 1),
(253, 'Kosovo, Republic of', 'XK', 'UNK', '', 0, 1),
(254, 'Isle of Man', 'IM', 'IMN', '', 0, 1),
(255, 'Tristan da Cunha', 'TA', 'SHN', '', 0, 1),
(256, 'Guernsey', 'GG', 'GGY', '', 0, 1),
(257, 'Jersey', 'JE', 'JEY', '', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE IF NOT EXISTS `#__customer` (
`customer_id` int(11) NOT NULL,
  `customer_group_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(96) NOT NULL,
  `telephone` varchar(32) NOT NULL,
  `password` varchar(40) NOT NULL,
  `salt` varchar(9) NOT NULL,
  `newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `address_id` int(11) NOT NULL DEFAULT '0',
  `ip` varchar(40) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `approved` tinyint(1) NOT NULL,
  `token` varchar(255) NOT NULL,
  `date_added` datetime NOT NULL,
  `country_id` int(11) NOT NULL DEFAULT '0',
  `fax` varchar(32) NOT NULL DEFAULT ''
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


--
-- Table structure for table `customer_activity`
--

CREATE TABLE IF NOT EXISTS `#__customer_activity` (
`activity_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `key` varchar(64) NOT NULL,
  `data` text NOT NULL,
  `ip` varchar(40) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `customer_ban_ip`
--

CREATE TABLE IF NOT EXISTS `#__customer_ban_ip` (
`customer_ban_ip_id` int(11) NOT NULL,
  `ip` varchar(40) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `customer_group`
--

CREATE TABLE IF NOT EXISTS `#__customer_group` (
`customer_group_id` int(11) NOT NULL,
  `approval` int(1) NOT NULL,
  `sort_order` int(3) NOT NULL,
  `name` varchar(250) NOT NULL DEFAULT '',
  `description` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `customer_group`
--

INSERT INTO `#__customer_group` (`customer_group_id`, `approval`, `sort_order`, `name`, `description`) VALUES
(-1, 0, -3, 'Everybody', 'All'),
(-2, 0, -2, 'Guest', 'Not logged visitors'),
(-3, 1, -1, 'Users', 'Logged users who have account'),
(1, 0, 1, 'Default', 'Default User Group for Registered Users');



CREATE TABLE IF NOT EXISTS `#__customer_history` (
`customer_history_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `customer_ip`
--

CREATE TABLE IF NOT EXISTS `#__customer_ip` (
`customer_ip_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `customer_login`
--

CREATE TABLE IF NOT EXISTS `#__customer_login` (
`customer_login_id` int(11) NOT NULL,
  `email` varchar(96) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `total` int(4) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--

-- --------------------------------------------------------

--
-- Table structure for table `customer_online`
--

CREATE TABLE IF NOT EXISTS `#__customer_online` (
  `ip` varchar(40) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `url` text NOT NULL,
  `referer` text NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `denied_groups`
--

CREATE TABLE IF NOT EXISTS `#__denied_groups` (
  `object_id` int(11) NOT NULL,
  `object_type` varchar(50) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `extension`
--

CREATE TABLE IF NOT EXISTS `#__extension` (
`extension_id` int(11) NOT NULL,
  `type` varchar(32) NOT NULL,
  `code` varchar(32) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `layout`
--

CREATE TABLE IF NOT EXISTS `#__layout` (
    `layout_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `layout`
--

INSERT INTO `#__layout` (`layout_id`, `name`) VALUES
(1, 'Default');

-- --------------------------------------------------------

--
-- Table structure for table `layout_module`
--

CREATE TABLE IF NOT EXISTS `#__layout_module` (
`layout_module_id` int(11) NOT NULL,
  `layout_id` int(11) NOT NULL,
  `code` varchar(64) NOT NULL,
  `position` varchar(14) NOT NULL,
  `sort_order` int(3) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table `layout_route`
--

CREATE TABLE IF NOT EXISTS `#__layout_route` (
`layout_route_id` int(11) NOT NULL,
  `layout_id` int(11) NOT NULL,
  `route` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=53 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `layout_route`
--

INSERT INTO `#__layout_route` (`layout_route_id`, `layout_id`, `route`) VALUES
(42, 1, 'common/home');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE IF NOT EXISTS `#__menus` (
`menu_id` int(11) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `header` int(1) NOT NULL DEFAULT '0',
  `footer` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `menus`
--

INSERT INTO `#__menus` (`menu_id`, `name`, `header`, `footer`) VALUES
(2, 'Main Menu', 1, 0),
(3, 'Footer Menu', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE IF NOT EXISTS `#__menu_items` (
`menu_item_id` int(11) NOT NULL,
  `menu_id` int(11) DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `title` varchar(250) DEFAULT NULL,
  `ams_page_id` int(11) DEFAULT '0',
  `link` varchar(250) DEFAULT NULL,
  `linklabel` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8;




CREATE TABLE IF NOT EXISTS `#__modification` (
`modification_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `code` varchar(64) NOT NULL,
  `author` varchar(64) NOT NULL,
  `version` varchar(32) NOT NULL,
  `link` varchar(255) NOT NULL,
  `xml` text NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `module`
--

CREATE TABLE IF NOT EXISTS `#__module` (
`module_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `code` varchar(32) NOT NULL,
  `setting` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `#__setting` (
`setting_id` int(11) NOT NULL,
  `store_id` int(11) NOT NULL DEFAULT '0',
  `code` varchar(32) NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` text NOT NULL,
  `serialized` tinyint(1) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=665 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `setting`
--

INSERT INTO `#__setting` (`setting_id`, `store_id`, `code`, `key`, `value`, `serialized`) VALUES
(656, 0, 'config', 'config_google_analytics_status', '0', 0),
(655, 0, 'config', 'config_google_analytics', '', 0),
(654, 0, 'config', 'config_error_log', '1', 0),
(652, 0, 'config', 'config_compression', '9', 0),
(653, 0, 'config', 'config_error_display', '0', 0),
(651, 0, 'config', 'config_encryption', 'sklo349gfj0', 0),
(650, 0, 'config', 'config_maintenance', '0', 0),
(649, 0, 'config', 'config_secure', '0', 0),
(648, 0, 'config', 'config_ftp_status', '0', 0),
(646, 0, 'config', 'config_ftp_password', '', 0),
(647, 0, 'config', 'config_ftp_root', '', 0),
(645, 0, 'config', 'config_ftp_username', '', 0),
(644, 0, 'config', 'config_ftp_port', '21', 0),
(642, 0, 'config', 'config_mail_alert', '', 0),
(643, 0, 'config', 'config_ftp_hostname', '', 0),
(641, 0, 'config', 'config_mail_smtp_timeout', '15', 0),
(640, 0, 'config', 'config_mail_smtp_port', '', 0),
(639, 0, 'config', 'config_mail_smtp_password', '', 0),
(638, 0, 'config', 'config_mail_smtp_username', '', 0),
(637, 0, 'config', 'config_mail_smtp_hostname', '', 0),
(636, 0, 'config', 'config_mail_parameter', '', 0),
(633, 0, 'config', 'config_account_id', '0', 0),
(634, 0, 'config', 'config_account_mail', '0', 0),
(635, 0, 'config', 'config_mail_protocol', 'mail', 0),
(632, 0, 'config', 'config_login_attempts', '5', 0),
(631, 0, 'config', 'config_customer_group_display', 'a:1:{i:0;s:1:"1";}', 1),
(630, 0, 'config', 'config_customer_group_id', '1', 0),
(629, 0, 'config', 'config_review_mail', '1', 0),
(628, 0, 'config', 'config_comment_auto_approve', '0', 0),
(627, 0, 'config', 'config_review_guest', '1', 0),
(626, 0, 'config', 'config_review_status', '1', 0),
(625, 0, 'config', 'config_image_popup_height', '600', 0),
(624, 0, 'config', 'config_image_popup_width', '800', 0),
(623, 0, 'config', 'config_image_thumb_height', '100', 0),
(622, 0, 'config', 'config_image_thumb_width', '100', 0),
(621, 0, 'config', 'config_limit_admin', '20', 0),
(620, 0, 'config', 'config_blog_limit', '5', 0),
(619, 0, 'config', 'config_product_limit', '20', 0),
(617, 0, 'config', 'config_icon', 'uploads/avatar_generic.jpg', 0),
(618, 0, 'config', 'config_country_id', '153', 0),
(616, 0, 'config', 'config_logo', 'uploads/avatar_generic.jpg', 0),
(615, 0, 'config', 'config_layout_id', '1', 0),
(614, 0, 'config', 'config_template', 'default', 0),
(613, 0, 'config', 'config_meta_keyword', 'meta keywords', 0),
(612, 0, 'config', 'config_meta_description', 'meta tags', 0),
(611, 0, 'config', 'config_meta_title', 'OmniCms', 0),
(610, 0, 'config', 'config_email', '', 0),
(609, 0, 'config', 'config_name', 'OmniCms', 0),
(657, 0, 'config', 'config_google_captcha_public', '', 0),
(658, 0, 'config', 'config_google_captcha_secret', '', 0),
(659, 0, 'config', 'config_google_captcha_status', '0', 0),
(660, 0, 'config', 'config_google_api_key', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `url_alias`
--

CREATE TABLE IF NOT EXISTS `#__url_alias` (
`url_alias_id` int(11) NOT NULL,
  `query` varchar(255) NOT NULL,
  `keyword` varchar(255) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `#__user` (
`user_id` int(11) NOT NULL,
  `user_group_id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(40) NOT NULL,
  `salt` varchar(9) NOT NULL,
  `firstname` varchar(32) NOT NULL,
  `lastname` varchar(32) NOT NULL,
  `email` varchar(96) NOT NULL,
  `profile_img` varchar(250) DEFAULT NULL,
  `dashboard` text NOT NULL,
  `code` varchar(40) NOT NULL,
  `ip` varchar(40) NOT NULL,
  `status` tinyint(1) NOT NULL,
  `date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `user_group`
--

CREATE TABLE IF NOT EXISTS `#__user_group` (
`user_group_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `permission` text NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_group`
--

INSERT INTO `#__user_group` (`user_group_id`, `name`, `permission`) VALUES
(1, 'Top Administrator', 'a:2:{s:6:"access";a:36:{i:0;s:13:"blog/category";i:1;s:9:"blog/post";i:2;s:10:"cms/banner";i:3;s:11:"cms/comment";i:4;s:8:"cms/menu";i:5;s:8:"cms/page";i:6;s:18:"common/filemanager";i:7;s:18:"dashboard/activity";i:8;s:15:"dashboard/empty";i:9;s:13:"dashboard/map";i:10;s:15:"dashboard/stats";i:11;s:13:"design/layout";i:12;s:11:"design/menu";i:13;s:19:"extension/installer";i:14;s:21:"extension/installer_1";i:15;s:22:"extension/modification";i:16;s:16:"extension/module";i:17;s:14:"module/account";i:18;s:11:"module/html";i:19;s:11:"module/menu";i:20;s:16:"module/regrating";i:21;s:24:"report/customer_activity";i:22;s:22:"report/customer_online";i:23;s:13:"sale/customer";i:24;s:20:"sale/customer_ban_ip";i:25;s:19:"sale/customer_group";i:26;s:15:"setting/setting";i:27;s:9:"user/user";i:28;s:20:"user/user_permission";i:29;s:13:"module/banner";i:30;s:15:"module/carousel";i:31;s:14:"module/gallery";i:32;s:22:"module/google_hangouts";i:33;s:16:"module/slideshow";i:34;s:22:"module/blog_categories";i:35;s:11:"module/page";}s:6:"modify";a:36:{i:0;s:13:"blog/category";i:1;s:9:"blog/post";i:2;s:10:"cms/banner";i:3;s:11:"cms/comment";i:4;s:8:"cms/menu";i:5;s:8:"cms/page";i:6;s:18:"common/filemanager";i:7;s:18:"dashboard/activity";i:8;s:15:"dashboard/empty";i:9;s:13:"dashboard/map";i:10;s:15:"dashboard/stats";i:11;s:13:"design/layout";i:12;s:11:"design/menu";i:13;s:19:"extension/installer";i:14;s:21:"extension/installer_1";i:15;s:22:"extension/modification";i:16;s:16:"extension/module";i:17;s:14:"module/account";i:18;s:11:"module/html";i:19;s:11:"module/menu";i:20;s:16:"module/regrating";i:21;s:24:"report/customer_activity";i:22;s:22:"report/customer_online";i:23;s:13:"sale/customer";i:24;s:20:"sale/customer_ban_ip";i:25;s:19:"sale/customer_group";i:26;s:15:"setting/setting";i:27;s:9:"user/user";i:28;s:20:"user/user_permission";i:29;s:13:"module/banner";i:30;s:15:"module/carousel";i:31;s:14:"module/gallery";i:32;s:22:"module/google_hangouts";i:33;s:16:"module/slideshow";i:34;s:22:"module/blog_categories";i:35;s:11:"module/page";}}'),
(10, 'Demonstration', '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `allowed_groups`
--
ALTER TABLE `#__allowed_groups`
 ADD PRIMARY KEY (`object_id`,`object_type`,`group_id`);

--
-- Indexes for table `ams_meta`
--
ALTER TABLE `#__ams_meta`
 ADD PRIMARY KEY (`ams_meta_id`);

--
-- Indexes for table `ams_nodes`
--
ALTER TABLE `#__ams_nodes`
 ADD PRIMARY KEY (`ams_node_id`);

--
-- Indexes for table `ams_pages`
--
ALTER TABLE `#__ams_pages`
 ADD PRIMARY KEY (`ams_page_id`);

--
-- Indexes for table `banner`
--
ALTER TABLE `#__banner`
 ADD PRIMARY KEY (`banner_id`);

--
-- Indexes for table `banner_image`
--
ALTER TABLE `#__banner_image`
 ADD PRIMARY KEY (`banner_image_id`);


--
-- Indexes for table `comments`
--
ALTER TABLE `#__comments`
 ADD PRIMARY KEY (`comment_id`), ADD KEY `ams_page_id` (`ams_page_id`);

--
-- Indexes for table `country`
--
ALTER TABLE `#__country`
 ADD PRIMARY KEY (`country_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `#__customer`
 ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `customer_activity`
--
ALTER TABLE `#__customer_activity`
 ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `customer_ban_ip`
--
ALTER TABLE `#__customer_ban_ip`
 ADD PRIMARY KEY (`customer_ban_ip_id`), ADD KEY `ip` (`ip`);

--
-- Indexes for table `customer_group`
--
ALTER TABLE `#__customer_group`
 ADD PRIMARY KEY (`customer_group_id`);



--
-- Indexes for table `customer_history`
--
ALTER TABLE `#__customer_history`
 ADD PRIMARY KEY (`customer_history_id`);

--
-- Indexes for table `customer_ip`
--
ALTER TABLE `#__customer_ip`
 ADD PRIMARY KEY (`customer_ip_id`), ADD KEY `ip` (`ip`);

--
-- Indexes for table `customer_login`
--
ALTER TABLE `#__customer_login`
 ADD PRIMARY KEY (`customer_login_id`), ADD KEY `email` (`email`), ADD KEY `ip` (`ip`);

--
-- Indexes for table `customer_online`
--
ALTER TABLE `#__customer_online`
 ADD PRIMARY KEY (`ip`);

--
-- Indexes for table `denied_groups`
--
ALTER TABLE `#__denied_groups`
 ADD PRIMARY KEY (`object_id`,`object_type`,`group_id`);

--
-- Indexes for table `extension`
--
ALTER TABLE `#__extension`
 ADD PRIMARY KEY (`extension_id`);

--
-- Indexes for table `layout`
--
ALTER TABLE `#__layout`
 ADD PRIMARY KEY (`layout_id`);

--
-- Indexes for table `layout_module`
--
ALTER TABLE `#__layout_module`
 ADD PRIMARY KEY (`layout_module_id`);

--
-- Indexes for table `layout_route`
--
ALTER TABLE `#__layout_route`
 ADD PRIMARY KEY (`layout_route_id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `#__menus`
 ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `#__menu_items`
 ADD PRIMARY KEY (`menu_item_id`);

--
-- Indexes for table `modification`
--
ALTER TABLE `#__modification`
 ADD PRIMARY KEY (`modification_id`);

--
-- Indexes for table `module`
--
ALTER TABLE `#__module`
 ADD PRIMARY KEY (`module_id`);



--
-- Indexes for table `setting`
--
ALTER TABLE `#__setting`
 ADD PRIMARY KEY (`setting_id`);

--
-- Indexes for table `url_alias`
--
ALTER TABLE `#__url_alias`
 ADD PRIMARY KEY (`url_alias_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `#__user`
 ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_group`
--
ALTER TABLE `#__user_group`
 ADD PRIMARY KEY (`user_group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ams_meta`
--
ALTER TABLE `#__ams_meta`
MODIFY `ams_meta_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `ams_nodes`
--
ALTER TABLE `#__ams_nodes`
MODIFY `ams_node_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=65;
--
-- AUTO_INCREMENT for table `ams_pages`
--
ALTER TABLE `#__ams_pages`
MODIFY `ams_page_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `#__banner`
MODIFY `banner_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `banner_image`
--
ALTER TABLE `#__banner_image`
MODIFY `banner_image_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;


--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `#__comments`
MODIFY `comment_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `#__country`
MODIFY `country_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=258;
--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `#__customer`
MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `customer_activity`
--
ALTER TABLE `#__customer_activity`
MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `customer_ban_ip`
--
ALTER TABLE `#__customer_ban_ip`
MODIFY `customer_ban_ip_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `customer_group`
--
ALTER TABLE `#__customer_group`
MODIFY `customer_group_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `customer_history`
--
ALTER TABLE `#__customer_history`
MODIFY `customer_history_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `customer_ip`
--
ALTER TABLE `#__customer_ip`
MODIFY `customer_ip_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `customer_login`
--
ALTER TABLE `#__customer_login`
MODIFY `customer_login_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `extension`
--
ALTER TABLE `#__extension`
MODIFY `extension_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=29;
--
-- AUTO_INCREMENT for table `layout`
--
ALTER TABLE `#__layout`
MODIFY `layout_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `layout_module`
--
ALTER TABLE `#__layout_module`
MODIFY `layout_module_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT for table `layout_route`
--
ALTER TABLE `#__layout_route`
MODIFY `layout_route_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=53;
--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `#__menus`
MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `#__menu_items`
MODIFY `menu_item_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=65;
--
-- AUTO_INCREMENT for table `modification`
--
ALTER TABLE `#__modification`
MODIFY `modification_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `module`
--
ALTER TABLE `#__module`
MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `setting`
--
ALTER TABLE `#__setting`
MODIFY `setting_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=665;
--
-- AUTO_INCREMENT for table `url_alias`
--
ALTER TABLE `#__url_alias`
MODIFY `url_alias_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=62;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `#__user`
MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=9;
--
-- AUTO_INCREMENT for table `user_group`
--
ALTER TABLE `#__user_group`
MODIFY `user_group_id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;