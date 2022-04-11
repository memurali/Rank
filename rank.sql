-- phpMyAdmin SQL Dump
-- version 4.4.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 15, 2020 at 09:35 AM
-- Server version: 5.6.25
-- PHP Version: 5.5.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rank`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_inter`
--

CREATE TABLE IF NOT EXISTS `tbl_inter` (
  `id` int(11) NOT NULL,
  `keyword_id` int(11) NOT NULL,
  `ReportDate` date DEFAULT NULL,
  `Keyword` varchar(255) DEFAULT NULL,
  `KeywordMarket` varchar(10) DEFAULT NULL,
  `KeywordLocation` varchar(25) DEFAULT NULL,
  `Rank` int(11) DEFAULT NULL,
  `BaseRank` int(11) DEFAULT NULL,
  `URL` varchar(255) DEFAULT NULL,
  `Advertiser` decimal(10,8) DEFAULT NULL,
  `Global` varchar(25) DEFAULT NULL,
  `Regional` varchar(25) DEFAULT NULL,
  `CPC` decimal(6,4) DEFAULT NULL,
  `Tags` varchar(255) DEFAULT NULL,
  `FileName` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_keywords`
--

CREATE TABLE IF NOT EXISTS `tbl_keywords` (
  `tag_id` int(11) NOT NULL,
  `keyword_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_results`
--

CREATE TABLE IF NOT EXISTS `tbl_results` (
  `KeywordGroups` varchar(15) NOT NULL,
  `Volume` varchar(15) NOT NULL,
  `ReportDate` date NOT NULL,
  `Rank` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tags`
--

CREATE TABLE IF NOT EXISTS `tbl_tags` (
  `tag_id` int(11) NOT NULL,
  `tag_name` varchar(25) NOT NULL,
  `tag_types` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_temporary`
--

CREATE TABLE IF NOT EXISTS `tbl_temporary` (
  `Id` int(11) NOT NULL,
  `ReportDate` date NOT NULL,
  `Keyword` varchar(35) NOT NULL,
  `KeywordMarket` varchar(25) NOT NULL,
  `KeywordLocation` varchar(25) NOT NULL,
  `KeywordDevice` varchar(25) NOT NULL,
  `KeywordTranslation` varchar(25) NOT NULL,
  `KeywordTags` varchar(45) NOT NULL,
  `AdvertiserCompetition` float(5,3) NOT NULL,
  `GlobalSearchVolume` int(11) NOT NULL,
  `RegionalSearchVolume` int(11) NOT NULL,
  `LocalSearchTrendsByMonth` int(11) NOT NULL,
  `CPC` float(3,2) NOT NULL,
  `KeywordRanking` date NOT NULL,
  `CreatedAt` date NOT NULL,
  `RequestUrl` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_whitelist`
--

CREATE TABLE IF NOT EXISTS `tbl_whitelist` (
  `Id` int(11) NOT NULL,
  `tag_name` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_inter`
--
ALTER TABLE `tbl_inter`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_tags`
--
ALTER TABLE `tbl_tags`
  ADD PRIMARY KEY (`tag_id`);

--
-- Indexes for table `tbl_whitelist`
--
ALTER TABLE `tbl_whitelist`
  ADD PRIMARY KEY (`Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_inter`
--
ALTER TABLE `tbl_inter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tbl_whitelist`
--
ALTER TABLE `tbl_whitelist`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
