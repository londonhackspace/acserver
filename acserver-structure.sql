-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 25, 2013 at 05:07 PM
-- Server version: 5.5.29-0ubuntu0.12.10.1
-- PHP Version: 5.4.6-1ubuntu1.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `acserver`
--

-- --------------------------------------------------------

--
-- Table structure for table `tools`
--

CREATE TABLE IF NOT EXISTS `tools` (
  `tool_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `status` int(1) NOT NULL,
  `status_message` varchar(120) NULL,                       -- Text description of what's wrong
  PRIMARY KEY (`tool_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `acnodes`
--
CREATE TABLE IF NOT EXISTS `acnodes` (
  `acnode_id` int(11) NOT NULL,
  `unique_identifier` varchar(30) NOT NULL,             -- Some way of uniquely identifying an Acnode
                                                        --    MAC address or similar, so they don't get
                                                        --    muddled up

  `shared_secret` varchar(36) NOT NULL,                 -- Unique per acnode - use the uuid() mysql function

  `tool_id` int(11) unique NOT NULL,
  PRIMARY KEY (`acnode_id`),
  UNIQUE KEY `acnodes` (`acnode_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `acnodes` ADD CONSTRAINT FK_acnodes_tool_id FOREIGN KEY (tool_id) REFERENCES tools(tool_id);

-- --------------------------------------------------------

--
-- Table structure for table `users`
-- Note that this table is replicated from the carddb.php on the LHS website
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `nick` varchar(50) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `cards`
-- Note that this table is replicated from the carddb.php on the LHS website
--

CREATE TABLE IF NOT EXISTS `cards` (
  `card_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,

  `card_unique_identifier` varchar(15) NOT NULL,
  
  `last_used` datetime DEFAULT NULL,

  PRIMARY KEY (`card_id`),
  UNIQUE KEY `cards` (`card_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `cards` ADD CONSTRAINT FK_cards_user_id FOREIGN KEY (user_id) REFERENCES users(user_id);

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `tool_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `last_used` datetime DEFAULT NULL,
  `added_by_user_id` int(11) NULL,
  `added_on` datetime NOT NULL,
  `permission` int(1) NOT NULL                  -- 0 => No Permissions ; 1 => User ; 2 => maintainer
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `permissions` ADD CONSTRAINT FK_permissions_tool_id FOREIGN KEY (tool_id) REFERENCES tools(tool_id);
ALTER TABLE `permissions` ADD CONSTRAINT FK_permissions_user_id FOREIGN KEY (user_id) REFERENCES users(user_id);
ALTER TABLE `permissions` ADD CONSTRAINT FK_permissions_added_by_user_id FOREIGN KEY (added_by_user_id) REFERENCES users(user_id);


-- --------------------------------------------------------

--
-- Table structure for table `toolusage`
--

CREATE TABLE IF NOT EXISTS `toolusage` (
  `tool_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
ALTER TABLE `toolusage` ADD CONSTRAINT FK_toolusage_tool_id FOREIGN KEY (tool_id) REFERENCES tools(tool_id);
ALTER TABLE `toolusage` ADD CONSTRAINT FK_toolusage_user_id FOREIGN KEY (user_id) REFERENCES users(user_id);


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
