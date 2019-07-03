-- phpMyAdmin SQL Dump
-- version 4.4.11
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 28, 2016 at 11:29 AM
-- Server version: 5.6.27-2
-- PHP Version: 5.6.17-1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ems`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessment`
--

CREATE TABLE IF NOT EXISTS `assessment` (
  `stdid` int(11) NOT NULL,
  `cid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `testid` int(11) NOT NULL,
  `Marks` decimal(10,2) NOT NULL,
  `maxmarks` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `assessment`
--

INSERT INTO `assessment` (`stdid`, `cid`, `uid`, `testid`, `Marks`, `maxmarks`) VALUES
(1, 1, 3, 1, 10.00, 10.00),
(2, 1, 3, 1, 6.00, 10.00);

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE IF NOT EXISTS `course` (
  `cid` int(11) NOT NULL,
  `mcode` varchar(20) NOT NULL,
  `mname` varchar(100) NOT NULL,
  `ccode` varchar(20) NOT NULL,
  `cname` varchar(100) NOT NULL,
  `ects` int(11) NOT NULL,
  `year` varchar(5) NOT NULL,
  `sem` varchar(5) NOT NULL,
  `dept` varchar(100) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `course`
--

INSERT INTO `course` (`cid`, `mcode`, `mname`, `ccode`, `cname`, `ects`, `year`, `sem`, `dept`, `uid`) VALUES
(1, 'ITEC-M2051', 'Advanced Programming', 'ITEC2052', 'OOP', 5, 'II', 'II', 'IT', 2);

-- --------------------------------------------------------

--
-- Table structure for table `instructor`
--

CREATE TABLE IF NOT EXISTS `instructor` (
  `insid` int(11) NOT NULL,
  `idno` varchar(20) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `sex` varchar(5) NOT NULL,
  `nationality` varchar(100) NOT NULL,
  `academicrank` varchar(100) NOT NULL,
  `phoneno` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `instructor`
--

INSERT INTO `instructor` (`insid`, `idno`, `fullname`, `sex`, `nationality`, `academicrank`, `phoneno`, `email`, `uid`) VALUES
(1, '1001/05', 'Dejene Techane', 'M', 'Ethiopian', 'Assistant Lecturer', '920470386', 'dejeethiohabesha@outlook.com', 2),
(2, '1002/05', 'Girmachew Gulint', 'M', 'Ethiopian', 'Assistant Lecturer', '919809056', 'girmachew.gulint@gmail.com', 2),
(3, '1003/05', 'Haftom Berhe', 'M', 'Ethiopian', 'Assistant Lecturer', '9421584', 'haftomberhe@gmail.com', 2);

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE IF NOT EXISTS `question` (
  `testid` int(11) NOT NULL DEFAULT '0',
  `qnid` int(11) NOT NULL DEFAULT '0',
  `question` text NOT NULL,
  `optiona` text NOT NULL,
  `optionb` text NOT NULL,
  `optionc` text NOT NULL,
  `optiond` text NOT NULL,
  `correctanswer` enum('optiona','optionb','optionc','optiond') NOT NULL,
  `marks` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `question`
--

INSERT INTO `question` (`testid`, `qnid`, `question`, `optiona`, `optionb`, `optionc`, `optiond`, `correctanswer`, `marks`) VALUES
(1, 1, 'What is the default size of char data type?', '8 bit', '16 bit', '32 bit', '64 bit', 'optiona', 1.00),
(1, 2, 'What will be the output of the following program ?                \r\n public class Operators{                                                             \r\npublic static void main(String[] args){                                        \r\nint x, y, z,w;                                                                            \r\nx=12;                                                                                      \r\ny=x++ +10;                                                                             \r\nz=y-- -x;  \r\nw=++z+2; \r\nSystem.out.print(â€œY:â€+y);                          \r\nSystem.out.print(â€œZ:â€+z);   \r\nSystem.out.print(â€œW:â€+w);}}                  ', '12  14  20', '21  10  12', '21 9 12', '21 10 11', 'optionc', 3.00),
(1, 3, 'Which one of the following is not examples of e-Learning?', 'www.alison.com', 'www.vcampus.com', 'www.facebook.com', 'www.thinq.com', 'optionc', 2.00),
(1, 4, 'Which of the following is not a traditional method of collecting system requirements', 'Interview', 'Rad', 'Document Analysis', 'Fact Finding Technique', 'optionb', 2.00),
(1, 5, 'In which of the following phases of the system development life cycle, elimination of errors in the system and running the system for any variations in its working environment will be done?', 'System Maintenance', 'System Specification', 'System Design', 'System testing', 'optiona', 2.00);

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE IF NOT EXISTS `student` (
  `stdid` int(11) NOT NULL,
  `idno` varchar(100) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `sex` varchar(5) NOT NULL,
  `dept` varchar(100) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`stdid`, `idno`, `fullname`, `sex`, `dept`, `uid`) VALUES
(1, 'R/1838/02', 'Dejene Techane', 'M', 'Information Technology', 2),
(2, 'R/1852/02', 'Geteye Zigale', 'M', 'Information Technology', 2),
(3, 'R/1837/02', 'Birhane G/Medihne', 'M', 'Information Technology', 2),
(4, 'R/1834/02', 'Bereket Alayu', 'F', 'Information Technology', 2);

-- --------------------------------------------------------

--
-- Table structure for table `studentquestion`
--

CREATE TABLE IF NOT EXISTS `studentquestion` (
  `uid` int(11) NOT NULL,
  `testid` int(11) NOT NULL,
  `qnid` int(11) NOT NULL,
  `answered` enum('answered','unanswered','review','') DEFAULT NULL,
  `stdanswer` enum('optiona','optionb','optionc','optiond') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `studentquestion`
--

INSERT INTO `studentquestion` (`uid`, `testid`, `qnid`, `answered`, `stdanswer`) VALUES
(6, 1, 1, 'answered', 'optiona'),
(6, 1, 2, 'answered', 'optionc'),
(6, 1, 3, 'answered', 'optionc'),
(6, 1, 4, 'answered', 'optionb'),
(6, 1, 5, 'answered', 'optiona'),
(7, 1, 1, 'answered', 'optiona'),
(7, 1, 2, 'answered', 'optionc'),
(7, 1, 3, 'answered', 'optionb'),
(7, 1, 4, 'answered', 'optionb'),
(7, 1, 5, 'answered', 'optionc');

-- --------------------------------------------------------

--
-- Table structure for table `studenttest`
--

CREATE TABLE IF NOT EXISTS `studenttest` (
  `uid` int(11) NOT NULL,
  `testid` int(11) NOT NULL DEFAULT '0',
  `starttime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `endtime` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `correctlyanswered` int(11) NOT NULL,
  `status` enum('over','inprogress') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `studenttest`
--

INSERT INTO `studenttest` (`uid`, `testid`, `starttime`, `endtime`, `correctlyanswered`, `status`) VALUES
(6, 1, '2016-05-28 07:52:29', '2016-05-28 08:22:29', 0, 'over'),
(7, 1, '2016-05-28 07:53:18', '2016-05-28 08:23:18', 0, 'over');

-- --------------------------------------------------------

--
-- Table structure for table `test`
--

CREATE TABLE IF NOT EXISTS `test` (
  `testid` int(11) DEFAULT NULL,
  `testname` varchar(100) NOT NULL,
  `testdesc` varchar(200) NOT NULL,
  `testdate` date NOT NULL,
  `testtime` time NOT NULL,
  `cid` int(11) NOT NULL,
  `testfrom` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `testto` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `duration` int(11) NOT NULL,
  `totalquestions` int(11) NOT NULL,
  `attemptedstudents` int(11) NOT NULL,
  `testcode` varchar(100) NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `test`
--

INSERT INTO `test` (`testid`, `testname`, `testdesc`, `testdate`, `testtime`, `cid`, `testfrom`, `testto`, `duration`, `totalquestions`, `attemptedstudents`, `testcode`, `uid`) VALUES
(1, 'ooptest', 'Object Oriented Programming Test I', '2016-05-28', '08:06:47', 1, '2016-05-27 05:34:12', '2016-05-31 20:59:59', 30, 5, 0, '9,t4Æ\n{', 3);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `uid` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `username`, `password`, `role`) VALUES
(1, 'admin', '81dc9bdb52d04dc20036dbd8313ed055', 'Administrator'),
(2, 'it', '0d149b90e7394297301c90191ae775f0', 'Department'),
(3, '1001/05', '2598a8d30283cf1ca5487247b8c4fc82', 'Instructor'),
(4, '1002/05', 'e50f3ae349b438a4d5ab24f400f89235', 'Instructor'),
(5, '1003/05', 'a5b022e18962c93d2394049bfdb1ba60', 'Instructor'),
(6, 'R/1838/02', '2598a8d30283cf1ca5487247b8c4fc82', 'Student'),
(7, 'R/1852/02', '9001b4f9705436e58123fe8ee69d47d1', 'Student'),
(8, 'R/1837/02', '8841b59809b6be8a825fc2b3cb241e62', 'Student'),
(9, 'R/1834/02', '9100d314f87b64a67a439ba536f7a22a', 'Student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessment`
--
ALTER TABLE `assessment`
  ADD PRIMARY KEY (`stdid`,`cid`,`uid`,`testid`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`cid`),
  ADD UNIQUE KEY `ccode` (`ccode`);

--
-- Indexes for table `instructor`
--
ALTER TABLE `instructor`
  ADD PRIMARY KEY (`insid`),
  ADD UNIQUE KEY `idno` (`idno`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`testid`,`qnid`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`stdid`),
  ADD UNIQUE KEY `idno` (`idno`);

--
-- Indexes for table `test`
--
ALTER TABLE `test`
  ADD UNIQUE KEY `testname` (`testname`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `course`
--
ALTER TABLE `course`
  MODIFY `cid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `instructor`
--
ALTER TABLE `instructor`
  MODIFY `insid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `student`
--
ALTER TABLE `student`
  MODIFY `stdid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `uid` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
