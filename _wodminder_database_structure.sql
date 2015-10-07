-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 07, 2015 at 11:36 AM
-- Server version: 5.5.43
-- PHP Version: 5.4.39-0+deb7u2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `wodminder`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
CREATE TABLE IF NOT EXISTS `audit_log` (
  `audit_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `log_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(50) DEFAULT NULL,
  `member_id` bigint(20) unsigned DEFAULT NULL,
  `member_name` varchar(100) DEFAULT NULL,
  `controller` varchar(100) DEFAULT NULL,
  `short_description` varchar(100) DEFAULT NULL,
  `full_info` varchar(5024) DEFAULT NULL,
  PRIMARY KEY (`audit_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10142 ;

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

DROP TABLE IF EXISTS `blog`;
CREATE TABLE IF NOT EXISTS `blog` (
  `blog_id` int(11) NOT NULL AUTO_INCREMENT,
  `headline` varchar(255) NOT NULL,
  `blog_text` text NOT NULL,
  `blog_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `publish` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`blog_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

--
-- Table structure for table `box`
--

DROP TABLE IF EXISTS `box`;
CREATE TABLE IF NOT EXISTS `box` (
  `box_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `twitter_id` varchar(100) DEFAULT NULL,
  `box_name` varchar(200) DEFAULT NULL,
  `box_abbreviation` varchar(25) DEFAULT NULL COMMENT 'abbreivation for box name',
  `location` varchar(200) DEFAULT NULL,
  `box_url` varchar(500) DEFAULT NULL,
  `facebook_page_id` varchar(30) DEFAULT NULL,
  `sm_package` tinyint(1) NOT NULL COMMENT 'Has the box subscribed to the social media package?',
  `super_order` tinyint(3) unsigned DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`box_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='information on Xfit facility.' AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Table structure for table `box_class_time`
--

DROP TABLE IF EXISTS `box_class_time`;
CREATE TABLE IF NOT EXISTS `box_class_time` (
  `bct_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `box_id` bigint(20) unsigned NOT NULL,
  `bwt_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Class tier (if applicable)',
  `class_time_description` varchar(100) NOT NULL,
  `class_time` time NOT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'duration (in minutes) of class',
  `on_monday` tinyint(1) NOT NULL,
  `on_tuesday` tinyint(1) NOT NULL,
  `on_wednesday` tinyint(1) NOT NULL,
  `on_thursday` tinyint(1) NOT NULL,
  `on_friday` tinyint(1) NOT NULL,
  `on_saturday` tinyint(1) NOT NULL,
  `on_sunday` tinyint(1) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_by` varchar(100) NOT NULL,
  PRIMARY KEY (`bct_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='collection of class times for a box.  not worried about whic' AUTO_INCREMENT=52 ;

-- --------------------------------------------------------

--
-- Table structure for table `box_staff`
--

DROP TABLE IF EXISTS `box_staff`;
CREATE TABLE IF NOT EXISTS `box_staff` (
  `box_id` bigint(20) unsigned NOT NULL,
  `member_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`box_id`,`member_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `box_staff_training_log`
--

DROP TABLE IF EXISTS `box_staff_training_log`;
CREATE TABLE IF NOT EXISTS `box_staff_training_log` (
  `bstl_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `box_id` bigint(20) unsigned NOT NULL,
  `bct_id` bigint(20) unsigned NOT NULL,
  `training_date` date NOT NULL,
  `class_size` int(15) NOT NULL,
  `note` text,
  `created_date` datetime NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_by` varchar(100) NOT NULL,
  PRIMARY KEY (`bstl_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=34 ;

-- --------------------------------------------------------

--
-- Table structure for table `box_wod`
--

DROP TABLE IF EXISTS `box_wod`;
CREATE TABLE IF NOT EXISTS `box_wod` (
  `bw_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `box_id` bigint(20) unsigned NOT NULL,
  `wod_id` bigint(20) unsigned DEFAULT NULL,
  `wod_type_id` bigint(20) unsigned DEFAULT NULL COMMENT 'type of wod (if not specified by WOD ID)',
  `scale_id` bigint(20) DEFAULT NULL COMMENT 'if null, then use rx field',
  `bwt_id` bigint(20) unsigned DEFAULT NULL COMMENT 'tier level of box wod (if applicable)',
  `daily_message` mediumtext COMMENT 'any motivational text the staff member wants to display to their users that is not related to buy in, wod or cash-out',
  `buy_in` text,
  `simple_title` varchar(200) DEFAULT NULL COMMENT 'if wod_id is null, this is the title of the wod',
  `simple_description` mediumtext COMMENT 'if wod_id is null, this is the description',
  `cash_out` text,
  `wod_date` date DEFAULT NULL COMMENT 'date of the wod',
  `score_type` varchar(2) DEFAULT NULL COMMENT 'I for integer (e.g. Round/Rep Count, T for Time and O for Other)',
  `image_name` varchar(200) NOT NULL COMMENT 'name of image generated for daily message',
  `image_link` varchar(1000) NOT NULL COMMENT 'for daily image if user decides to paste link instead of upload',
  `image_caption` varchar(500) DEFAULT NULL,
  `form_uniqid` varchar(50) DEFAULT NULL COMMENT 'Hack preventing duplicate box entries from being saved',
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`bw_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2184 ;

-- --------------------------------------------------------

--
-- Table structure for table `box_wod_tier`
--

DROP TABLE IF EXISTS `box_wod_tier`;
CREATE TABLE IF NOT EXISTS `box_wod_tier` (
  `bwt_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `box_id` bigint(20) unsigned NOT NULL,
  `tier_name` varchar(200) NOT NULL,
  `tier_order` int(11) NOT NULL,
  PRIMARY KEY (`bwt_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='box wod tier is used for facilities who do more than one typ' AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

DROP TABLE IF EXISTS `ci_sessions`;
CREATE TABLE IF NOT EXISTS `ci_sessions` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(45) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text NOT NULL,
  PRIMARY KEY (`session_id`),
  KEY `last_activity_idx` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
CREATE TABLE IF NOT EXISTS `event` (
  `event_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `hosting_box_id` bigint(20) unsigned DEFAULT NULL COMMENT 'box hosting the event',
  `host_name` varchar(500) DEFAULT NULL COMMENT 'if not hosted by a box, this is the name of the entity hosting the event (e.g. not a box "St. Clair Scramble" or maybe multiple boxes "CID / Moody CrossFit")',
  `es_id` bigint(20) unsigned NOT NULL,
  `is_team_event` tinyint(1) NOT NULL DEFAULT '0',
  `event_name` varchar(250) NOT NULL,
  `start_date` date NOT NULL,
  `duration` tinyint(3) unsigned NOT NULL COMMENT 'how many days the event will last (must be at least 1)',
  `publish` tinyint(1) DEFAULT NULL COMMENT 'boolean which states event (and all wods for this event have been saved) and are ready for viewing.',
  `result_hyperlink` varchar(500) DEFAULT NULL,
  `event_main_hyperlink` varchar(500) DEFAULT NULL COMMENT 'main link to the event',
  `facebook_page` varchar(500) DEFAULT NULL,
  `twitter_account` varchar(500) DEFAULT NULL,
  `note` text,
  `created_date` datetime NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_by` varchar(100) NOT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=25 ;

-- --------------------------------------------------------

--
-- Table structure for table `event_scale`
--

DROP TABLE IF EXISTS `event_scale`;
CREATE TABLE IF NOT EXISTS `event_scale` (
  `es_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scale_name` varchar(100) NOT NULL,
  PRIMARY KEY (`es_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

--
-- Table structure for table `event_scale_option`
--

DROP TABLE IF EXISTS `event_scale_option`;
CREATE TABLE IF NOT EXISTS `event_scale_option` (
  `eso_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `es_id` bigint(20) unsigned NOT NULL,
  `scale_option` varchar(100) NOT NULL,
  `scale_order` int(11) NOT NULL DEFAULT '1',
  `rx` tinyint(1) NOT NULL,
  PRIMARY KEY (`eso_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=36 ;

-- --------------------------------------------------------

--
-- Table structure for table `event_wod`
--

DROP TABLE IF EXISTS `event_wod`;
CREATE TABLE IF NOT EXISTS `event_wod` (
  `ew_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned DEFAULT NULL,
  `wod_id` bigint(20) unsigned DEFAULT NULL COMMENT 'if it''s a benchmark wod',
  `score_type` varchar(2) DEFAULT NULL,
  `remainder_name` varchar(200) DEFAULT NULL COMMENT 'sometimes score is broken down into two things.  if this field is named, then the remainder field in member_event_wod should be savable',
  `es_id` bigint(20) unsigned DEFAULT NULL,
  `wod_date` date DEFAULT NULL,
  `simple_title` varchar(200) DEFAULT NULL,
  `simple_description` mediumtext,
  `note` text,
  `team_wod` tinyint(1) NOT NULL DEFAULT '0',
  `result_hyperlink` varchar(500) DEFAULT NULL COMMENT 'the url link showing the results to the wod (if more applicable than the event results link)',
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ew_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=64 ;

-- --------------------------------------------------------

--
-- Table structure for table `exercise`
--

DROP TABLE IF EXISTS `exercise`;
CREATE TABLE IF NOT EXISTS `exercise` (
  `exercise_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `weight_type_id` bigint(20) unsigned DEFAULT NULL,
  `max_type` varchar(1) DEFAULT NULL COMMENT 'Indicates the way this exercise is maxed (''W'' = Weight, ''T'' = Time, ''R'' = Reps).  Null means there is not max.',
  `title` varchar(200) DEFAULT NULL,
  `demo_link` varchar(200) DEFAULT NULL COMMENT 'link to the exercise demo',
  `abbreviation` varchar(10) DEFAULT NULL,
  `min_weight` int(11) unsigned DEFAULT NULL COMMENT 'will be used when slider implemented on screens to select weight',
  `max_weight` int(11) unsigned DEFAULT NULL COMMENT 'will be used when slider implemented on screens to select weight',
  `description` varchar(500) DEFAULT NULL,
  `wod_category` varchar(100) DEFAULT NULL COMMENT 'this is the category andrew gave me in the list of exercises he had.  not sure what to do with it yet.',
  `title_andrew` varchar(200) DEFAULT NULL COMMENT 'this is an exercise name that came from Andrew',
  PRIMARY KEY (`exercise_id`),
  UNIQUE KEY `UNIQUE_EXERCISE` (`title_andrew`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='list of xfit sizes INDIVIDUALLY; grouped to be figured out' AUTO_INCREMENT=196 ;

-- --------------------------------------------------------

--
-- Table structure for table `keys`
--

DROP TABLE IF EXISTS `keys`;
CREATE TABLE IF NOT EXISTS `keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(40) NOT NULL,
  `level` int(2) NOT NULL,
  `ignore_limits` tinyint(1) NOT NULL DEFAULT '0',
  `date_created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_affiliates`
--

DROP TABLE IF EXISTS `marketing_affiliates`;
CREATE TABLE IF NOT EXISTS `marketing_affiliates` (
  `ma_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ct_id` bigint(20) unsigned NOT NULL,
  `affiliate_name` varchar(255) NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `url` varchar(1024) DEFAULT NULL,
  `url_status` varchar(255) DEFAULT NULL,
  `just_search_url` tinyint(1) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) NOT NULL,
  PRIMARY KEY (`ma_id`),
  KEY `ct_id` (`ct_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_affiliate_type`
--

DROP TABLE IF EXISTS `marketing_affiliate_type`;
CREATE TABLE IF NOT EXISTS `marketing_affiliate_type` (
  `mat_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `affiliate_type` varchar(255) NOT NULL,
  `created_date` datetime NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_by` varchar(100) NOT NULL,
  PRIMARY KEY (`mat_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `meal_type`
--

DROP TABLE IF EXISTS `meal_type`;
CREATE TABLE IF NOT EXISTS `meal_type` (
  `meal_type_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `display_order` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`meal_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
CREATE TABLE IF NOT EXISTS `member` (
  `member_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_login` varchar(200) NOT NULL COMMENT 'the user''s login to the site',
  `last_name` varchar(100) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `gender` varchar(1) NOT NULL,
  `box_id` bigint(20) unsigned DEFAULT NULL,
  `other_box` varchar(100) DEFAULT NULL COMMENT 'user''s box_id is ''other''.  Need to know which box where they are a member',
  `birth_date` date DEFAULT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(500) DEFAULT NULL,
  `site_admin` tinyint(1) unsigned DEFAULT NULL,
  `is_competitor` tinyint(1) NOT NULL COMMENT 'shows "event" button if they are',
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=391 ;

-- --------------------------------------------------------

--
-- Table structure for table `member_event_info`
--

DROP TABLE IF EXISTS `member_event_info`;
CREATE TABLE IF NOT EXISTS `member_event_info` (
  `mei_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `event_id` bigint(20) unsigned NOT NULL,
  `member_id` bigint(20) unsigned NOT NULL,
  `eso_id` bigint(20) unsigned NOT NULL,
  `rank` varchar(100) DEFAULT NULL,
  `number_of_competitors` varchar(100) DEFAULT NULL,
  `team_name` varchar(200) DEFAULT NULL,
  `teammates` varchar(500) DEFAULT NULL,
  `note` text,
  `created_date` datetime NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_by` varchar(100) NOT NULL,
  PRIMARY KEY (`mei_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='saves overall event info for a member' AUTO_INCREMENT=39 ;

-- --------------------------------------------------------

--
-- Table structure for table `member_event_wod`
--

DROP TABLE IF EXISTS `member_event_wod`;
CREATE TABLE IF NOT EXISTS `member_event_wod` (
  `mew_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ew_id` bigint(20) unsigned NOT NULL,
  `member_id` bigint(20) unsigned NOT NULL,
  `score` varchar(200) DEFAULT NULL,
  `remainder` varchar(200) DEFAULT NULL,
  `rank` varchar(100) DEFAULT NULL,
  `member_rating` tinyint(1) unsigned DEFAULT NULL,
  `note` text,
  `created_date` datetime NOT NULL,
  `created_by` varchar(100) NOT NULL,
  `modified_date` datetime NOT NULL,
  `modified_by` varchar(100) NOT NULL,
  PRIMARY KEY (`mew_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='saves event wod info for a member' AUTO_INCREMENT=83 ;

-- --------------------------------------------------------

--
-- Table structure for table `member_max`
--

DROP TABLE IF EXISTS `member_max`;
CREATE TABLE IF NOT EXISTS `member_max` (
  `mm_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `exercise_id` bigint(20) unsigned NOT NULL,
  `max_date` date NOT NULL,
  `max_rep` smallint(6) unsigned DEFAULT NULL COMMENT 'max number of reps for weighted exercises (N/A for non-weighted maxes (e.g. pull-ups))',
  `round_count` smallint(6) unsigned DEFAULT NULL COMMENT 'number of rounds to do the repitition',
  `max_value` decimal(7,1) unsigned NOT NULL COMMENT 'if exercise.max_type = ''W'', then #s; if ''T'', then seconds, if ''R'', then Reps',
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`mm_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3434 ;

-- --------------------------------------------------------

--
-- Table structure for table `member_paleo`
--

DROP TABLE IF EXISTS `member_paleo`;
CREATE TABLE IF NOT EXISTS `member_paleo` (
  `mp_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `meal_date_time` datetime NOT NULL,
  `meal_type_id` bigint(20) unsigned DEFAULT NULL,
  `protein` varchar(250) DEFAULT NULL,
  `veggie_or_fruit` varchar(250) DEFAULT NULL,
  `fat` varchar(250) DEFAULT NULL,
  `note` varchar(100) DEFAULT NULL,
  `image_name` varchar(250) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`mp_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=542 ;

-- --------------------------------------------------------

--
-- Table structure for table `member_weight_log`
--

DROP TABLE IF EXISTS `member_weight_log`;
CREATE TABLE IF NOT EXISTS `member_weight_log` (
  `mwl_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `weight` decimal(7,1) unsigned NOT NULL,
  `weight_date` date NOT NULL,
  `bmi` decimal(7,3) unsigned DEFAULT NULL,
  `body_fat_percentage` decimal(7,3) unsigned DEFAULT NULL,
  `cholesterol` int(10) unsigned NOT NULL,
  `blood_pressure` varchar(100) NOT NULL,
  `how_i_feel` tinyint(3) unsigned NOT NULL,
  `note` varchar(1000) NOT NULL,
  `image_name` varchar(250) DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`mwl_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=231 ;

-- --------------------------------------------------------

--
-- Table structure for table `member_wod`
--

DROP TABLE IF EXISTS `member_wod`;
CREATE TABLE IF NOT EXISTS `member_wod` (
  `mw_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` bigint(20) unsigned NOT NULL,
  `bw_id` bigint(20) unsigned DEFAULT NULL COMMENT 'if user does a wod at a box, this will have the value',
  `wod_id` bigint(20) unsigned DEFAULT NULL COMMENT 'if user does a benchmark wod on their own, then a value will be in here',
  `so_id` bigint(20) DEFAULT NULL COMMENT 'if null, then use rx field',
  `bct_id` bigint(20) unsigned DEFAULT NULL COMMENT 'box wod class time',
  `custom_title` varchar(100) DEFAULT NULL COMMENT 'when user does a wod on their own and not linked to a benchmark wod, they can title their own WOD',
  `wod_date` date DEFAULT NULL COMMENT 'date of the wod if not related to a box_wod',
  `score` varchar(200) DEFAULT NULL COMMENT 'wod score',
  `note` text,
  `rx` tinyint(1) DEFAULT NULL COMMENT 'did user rx?',
  `member_rating` tinyint(1) unsigned DEFAULT NULL,
  `class_attended` tinyint(2) unsigned DEFAULT NULL COMMENT 'class user attended (value in military time)',
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`mw_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11975 ;

-- --------------------------------------------------------

--
-- Table structure for table `movement`
--

DROP TABLE IF EXISTS `movement`;
CREATE TABLE IF NOT EXISTS `movement` (
  `movement_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `movement` varchar(255) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`movement_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=74 ;

-- --------------------------------------------------------

--
-- Table structure for table `scale`
--

DROP TABLE IF EXISTS `scale`;
CREATE TABLE IF NOT EXISTS `scale` (
  `scale_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `box_id` bigint(20) unsigned NOT NULL,
  `scale_name` varchar(100) NOT NULL,
  PRIMARY KEY (`scale_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `scale_option`
--

DROP TABLE IF EXISTS `scale_option`;
CREATE TABLE IF NOT EXISTS `scale_option` (
  `so_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scale_id` bigint(20) unsigned NOT NULL,
  `option` varchar(100) NOT NULL,
  `scale_order` int(11) NOT NULL DEFAULT '1',
  `rx` tinyint(1) NOT NULL,
  PRIMARY KEY (`so_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=38 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_box_wod_leaders`
--
DROP VIEW IF EXISTS `view_box_wod_leaders`;
CREATE TABLE IF NOT EXISTS `view_box_wod_leaders` (
`member_id` bigint(20) unsigned
,`wod_id` bigint(20) unsigned
,`title` varchar(200)
,`full_name` varchar(201)
,`rx` tinyint(1)
,`score` varchar(200)
,`wod_date` date
,`ScoreOrder` decimal(8,1)
,`member_box_id` bigint(20) unsigned
,`box_wod_box_id` bigint(20) unsigned
);
-- --------------------------------------------------------

--
-- Table structure for table `weight_type`
--

DROP TABLE IF EXISTS `weight_type`;
CREATE TABLE IF NOT EXISTS `weight_type` (
  `wt_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`wt_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `wod`
--

DROP TABLE IF EXISTS `wod`;
CREATE TABLE IF NOT EXISTS `wod` (
  `wod_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wod_type_id` bigint(20) unsigned DEFAULT NULL,
  `wod_category_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` varchar(2000) DEFAULT NULL,
  `note` varchar(1000) DEFAULT NULL,
  `score_type` varchar(2) DEFAULT NULL COMMENT 'I for integer (e.g. Round/Rep Count, T for Time and O for Other)',
  `image_name` varchar(255) DEFAULT NULL COMMENT 'The image for heros that can be found on Crossfit''s FAQ website',
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`wod_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=450 ;

-- --------------------------------------------------------

--
-- Table structure for table `wod_category`
--

DROP TABLE IF EXISTS `wod_category`;
CREATE TABLE IF NOT EXISTS `wod_category` (
  `wod_category_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`wod_category_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `wod_max`
--

DROP TABLE IF EXISTS `wod_max`;
CREATE TABLE IF NOT EXISTS `wod_max` (
  `wm_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `wod_id` bigint(20) unsigned DEFAULT NULL,
  `exercise_id` bigint(20) unsigned DEFAULT NULL,
  `max_rep` smallint(6) unsigned DEFAULT NULL,
  `round_count` smallint(6) unsigned DEFAULT NULL,
  `created_date` datetime DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL,
  `modified_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`wm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='used to quickly access the exercises for a wod date.' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `wod_movement`
--

DROP TABLE IF EXISTS `wod_movement`;
CREATE TABLE IF NOT EXISTS `wod_movement` (
  `wod_id` bigint(20) unsigned NOT NULL,
  `movement_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`wod_id`,`movement_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `wod_type`
--

DROP TABLE IF EXISTS `wod_type`;
CREATE TABLE IF NOT EXISTS `wod_type` (
  `wod_type_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`wod_type_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Structure for view `view_box_wod_leaders`
--
DROP TABLE IF EXISTS `view_box_wod_leaders`;

CREATE ALGORITHM=UNDEFINED DEFINER=`wodminder`@`localhost` SQL SECURITY DEFINER VIEW `view_box_wod_leaders` AS (select `m`.`member_id` AS `member_id`,`w`.`wod_id` AS `wod_id`,`w`.`title` AS `title`,concat_ws(' ',`m`.`first_name`,`m`.`last_name`) AS `full_name`,`mw`.`rx` AS `rx`,(case when (ifnull(`bw`.`score_type`,`w`.`score_type`) = 'T') then concat_ws(':',floor((`mw`.`score` / 60)),lpad(cast((`mw`.`score` - (floor((`mw`.`score` / 60)) * 60)) as char(2) charset utf8),2,'0')) else `mw`.`score` end) AS `score`,`bw`.`wod_date` AS `wod_date`,(case when (ifnull(`bw`.`score_type`,`w`.`score_type`) <> 'T') then (cast(`mw`.`score` as decimal(7,1)) * -(1)) else cast(`mw`.`score` as decimal(7,1)) end) AS `ScoreOrder`,`m`.`box_id` AS `member_box_id`,`bw`.`box_id` AS `box_wod_box_id` from (((`member` `m` join `member_wod` `mw` on((`m`.`member_id` = `mw`.`member_id`))) join `box_wod` `bw` on((`bw`.`bw_id` = `mw`.`bw_id`))) join `wod` `w` on((`bw`.`wod_id` = `w`.`wod_id`))));

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
