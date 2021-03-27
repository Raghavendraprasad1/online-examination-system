-- phpMyAdmin SQL Dump
-- version 3.5.8.2
-- http://www.phpmyadmin.net
--
-- Host: sql309.byethost10.com
-- Generation Time: Dec 24, 2017 at 06:56 AM
-- Server version: 5.6.35-81.0
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `mydb_exam1`
--
CREATE DATABASE mydb_exam1;
USE mydb_exam1;
-- --------------------------------------------------------

--
-- Table structure for table `candidates_result`
--


CREATE TABLE IF NOT EXISTS `candidates_result` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Exam_ID` int(10) unsigned NOT NULL,
  `Candidate_ID` int(10) unsigned NOT NULL,
  `Max_Questions` int(11) NOT NULL,
  `Correct_Answers` int(11) NOT NULL,
  `Percentage` float unsigned NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `candidate_info`
--

CREATE TABLE IF NOT EXISTS `candidate_info` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `First_Name` varchar(15) NOT NULL,
  `Last_Name` varchar(15) NOT NULL,
  `Sex` varchar(6) NOT NULL,
  `Contact_Number` bigint(20) unsigned NOT NULL,
  `Email` varchar(40) NOT NULL,
  `Pass_Word` varchar(20) NOT NULL,
  `Current_Exam` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `current_exams`
--

CREATE TABLE IF NOT EXISTS `current_exams` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Exam_ID` int(10) unsigned NOT NULL,
  `Examiner_ID` int(11) NOT NULL,
  `Candidate_ID` int(10) unsigned NOT NULL,
  `Exam_Roll_Number` text NOT NULL,
  `Interference` int(11) NOT NULL DEFAULT '-2',
  `Suspended_Result` tinyint(1) NOT NULL DEFAULT '0',
  `Submitted` tinyint(1) NOT NULL DEFAULT '0',
  `Questions_Answered` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `examiner_info`
--

CREATE TABLE IF NOT EXISTS `examiner_info` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Institute` varchar(40) DEFAULT NULL,
  `First_Name` varchar(15) NOT NULL,
  `Last_Name` varchar(15) NOT NULL,
  `Sex` varchar(6) NOT NULL,
  `Contact_Number` bigint(20) unsigned NOT NULL,
  `Email` varchar(40) NOT NULL,
  `Country` varchar(20) NOT NULL,
  `City` varchar(20) NOT NULL,
  `Pass_Word` varchar(20) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE IF NOT EXISTS `exams` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Exam_Name` varchar(20) NOT NULL,
  `Exam_Password` varchar(20) NOT NULL,
  `Duration` int(10) unsigned NOT NULL,
  `Additional_Instructions` text,
  `Question_Bank_IDs` text NOT NULL,
  `Max_Questions` int(10) unsigned NOT NULL,
  `Start_Time` timestamp NULL DEFAULT NULL,
  `End_Time` timestamp NULL DEFAULT NULL,
  `Examiner_ID` int(11) NOT NULL,
  `Candidates_Enrolled` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE IF NOT EXISTS `questions` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Question_Bank_ID` int(10) unsigned NOT NULL,
  `Question` varchar(400) NOT NULL,
  `Option1` varchar(200) NOT NULL,
  `Option2` varchar(200) NOT NULL,
  `Option3` varchar(200) NOT NULL,
  `Option4` varchar(200) NOT NULL,
  `Correct_Option` varchar(7) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `questions_answered`
--

CREATE TABLE IF NOT EXISTS `questions_answered` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Exam_ID` int(10) unsigned NOT NULL,
  `Candidate_ID` int(10) unsigned NOT NULL,
  `Question_ID` int(10) unsigned NOT NULL,
  `Option_Choosen` varchar(7) NOT NULL,
  `Correct_Option` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `question_bank`
--

CREATE TABLE IF NOT EXISTS `question_bank` (
  `ID` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Question_Bank_Name` varchar(30) NOT NULL,
  `Subject` varchar(30) NOT NULL,
  `Description` text NOT NULL,
  `Created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Examiner_ID` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
