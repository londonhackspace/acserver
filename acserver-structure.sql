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
-- Table structure for table `cards`
--

CREATE TABLE IF NOT EXISTS `cards` (
  `user_id` int(11) NOT NULL,
  `card` varchar(15) NOT NULL,
  `added_by_card` varchar(14) DEFAULT NULL,
  `added_on` datetime NOT NULL,
  `last_used` datetime DEFAULT NULL,
  PRIMARY KEY (`card`),
  UNIQUE KEY `card` (`card`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `tool_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tools`
--

CREATE TABLE IF NOT EXISTS `tools` (
  `tool_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `status` int(11) NOT NULL,
  `node` int(11) NOT NULL,
  PRIMARY KEY (`tool_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `toolusage`
--

CREATE TABLE IF NOT EXISTS `toolusage` (
  `tool_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `nick` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
