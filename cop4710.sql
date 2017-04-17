-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 18, 2017 at 12:10 AM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cop4710`
--

-- --------------------------------------------------------

--
-- Table structure for table `attending`
--

CREATE TABLE `attending` (
  `eventid` int(11) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `attending`
--

INSERT INTO `attending` (`eventid`, `userid`) VALUES
(1, 1),
(8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `eventid` int(11) NOT NULL,
  `name` varchar(126) NOT NULL,
  `adminid` int(11) NOT NULL,
  `email` varchar(128) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `locationid` int(11) DEFAULT NULL,
  `rsoid` int(11) DEFAULT NULL,
  `universityid` int(11) DEFAULT NULL,
  `ratingid` int(11) NOT NULL,
  `category` varchar(128) DEFAULT NULL,
  `description` varchar(1000) DEFAULT NULL,
  `startdate` date NOT NULL,
  `time` time DEFAULT NULL,
  `length` int(11) DEFAULT NULL,
  `days` set('sun','mon','tues','wed','thur','fri','sat') DEFAULT NULL,
  `enddate` date DEFAULT NULL,
  `frequency` int(11) DEFAULT NULL COMMENT '1 for every week, 2 every other, etc.',
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `privacy` set('PUBLIC','PRIVATE','RSO') NOT NULL DEFAULT 'PUBLIC'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`eventid`, `name`, `adminid`, `email`, `phone`, `locationid`, `rsoid`, `universityid`, `ratingid`, `category`, `description`, `startdate`, `time`, `length`, `days`, `enddate`, `frequency`, `approved`, `privacy`) VALUES
(1, 'Potluck', 1, 'ajluntz@knights.ucf.edu', '1234567890', NULL, NULL, 1, 1, 'Food & Drink', '<p> <img src=\"http://chilsonhills.org/wp-content/uploads/2014/01/potluck.jpg\" />  </p>', '2017-04-26', '13:00:00', NULL, '', '0000-00-00', 4, 1, 'PUBLIC'),
(8, 'We Like to Party!', 1, 'ajluntz@knights.ucf.edu', '1234567890', 1, 1, 1, 15, 'Film, Media & Entertainment', '<p><img src=\"http://weknowyourdreams.com/images/party/party-06.jpg\"/></p>', '2017-04-26', '16:00:00', -1, '', '0000-00-00', 0, 0, 'RSO'),
(9, 'Hell Week', 1, 'ajluntz@knights.ucf.edu', '4071234567', 1, -1, 1, 16, 'Auto, Boat, & Air', 'Hiya, this is hell in the form of a week.', '0000-00-00', '12:00:00', -1, '', '0000-00-00', 0, 1, 'PUBLIC');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `locationid` int(11) NOT NULL,
  `address` varchar(512) DEFAULT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`locationid`, `address`, `latitude`, `longitude`) VALUES
(1, '4000 Central Florida Blvd, Orlando, FL 32816', 28.6024321, -81.2022486),
(2, 'University of Florida Gainesville, FL 32611', 29.6436325, -82.3571189),
(3, '100 Weldon Blvd, Sanford, FL 32773', 28.744246, -81.3076821),
(4, NULL, 26.3654078, -80.1045527);

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `rsoid` int(11) NOT NULL,
  `userid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `members`
--

INSERT INTO `members` (`rsoid`, `userid`) VALUES
(9, 1),
(8, 1),
(1, 2),
(1, 6),
(1, 3),
(1, 5),
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `ratingid` int(11) NOT NULL,
  `zero` int(11) NOT NULL,
  `one` int(11) NOT NULL DEFAULT '0',
  `two` int(11) NOT NULL DEFAULT '0',
  `three` int(11) NOT NULL DEFAULT '0',
  `four` int(11) NOT NULL DEFAULT '0',
  `five` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`ratingid`, `zero`, `one`, `two`, `three`, `four`, `five`) VALUES
(1, 0, 0, 1, 0, 1, 1),
(15, 0, 0, 0, 0, 0, 0),
(16, 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `rsos`
--

CREATE TABLE `rsos` (
  `rsoid` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `adminid` int(11) NOT NULL,
  `universityid` int(11) DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `description` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rsos`
--

INSERT INTO `rsos` (`rsoid`, `name`, `adminid`, `universityid`, `approved`, `description`) VALUES
(1, 'Club Awesome', 1, 1, 1, '<img src=\"//www.clipartkid.com/images/363/11-reasons-why-vapers-are-awesome-vape-about-it-IM94Ds-clipart.jpg\" alt=\"ClubAwesome\" />'),
(6, 'Potatoes', 1, 1, 0, '<p> <img src=\"https://upload.wikimedia.org/wikipedia/commons/4/4c/Potato_heart_mutation.jpg\" /> </p>'),
(7, 'Underwater Basket Weaving', 2, 1, 0, '<p> <img src=\"http://3cfmhg21atqf2isl5j2ps82c.wpengine.netdna-cdn.com/wp-content/uploads/2014/03/UWBW_Still-1024x576.jpg\" /> <b>We are awesome!</b>Â </p>'),
(8, 'Pokemon Fan Club', 2, 1, 0, '<p> <img src=\"https://vignette3.wikia.nocookie.net/youtubepoop/images/4/4c/Pokeball.png/revision/latest?cb=20150418234807\" />Â </p>'),
(9, 'The Fire Nation', 1, 1, 0, '<p>Â <img src=\"https://ih0.redbubble.net/image.88099330.9518/flat,800x800,075,t.u1.jpg\" /> </p>');

-- --------------------------------------------------------

--
-- Table structure for table `universities`
--

CREATE TABLE `universities` (
  `universityid` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `website` varchar(512) NOT NULL,
  `email` varchar(512) DEFAULT NULL,
  `locationid` int(11) NOT NULL,
  `super` int(11) NOT NULL,
  `description` varchar(1000) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `universities`
--

INSERT INTO `universities` (`universityid`, `name`, `website`, `email`, `locationid`, `super`, `description`) VALUES
(1, 'University of Central Florida', 'https://www.ucf.edu', NULL, 1, 1, '<img src=\"//www.ucf.edu/wp-content/uploads/2017/02/knightro_two_hands_point.png\" alt=\"Knightro\" /><p><span style=\"text-transform: uppercase;\"><b>You dream big. So do we.</b></span> UCF is an emerging pre-eminent research university in Florida and one of the largest universities in the U.S. But we\'re not just bigger - we\'re better. We\'re one of the best colleges when it comes to quality, access, impact and value. In fact, <em>Forbes</em> has named UCF one of the nation\'s most affordable colleges, while both The Princeton Review and <em>Kiplinger\'s</em> rank us a best-value university.</p>'),
(2, 'University of Florida', 'https://www.ufl.edu', NULL, 2, 1, NULL),
(3, 'Seminole State College', 'https://www.seminolestate.edu', NULL, 3, 1, NULL),
(4, 'Florida Atlantic University', 'https://www.fau.edu', NULL, 4, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` int(11) NOT NULL,
  `password` char(32) NOT NULL,
  `usertype` enum('USER','SUPER') NOT NULL,
  `universityid` int(11) DEFAULT NULL,
  `rsoid` int(11) DEFAULT NULL,
  `first` varchar(128) NOT NULL,
  `last` varchar(128) NOT NULL,
  `email` varchar(512) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `password`, `usertype`, `universityid`, `rsoid`, `first`, `last`, `email`) VALUES
(1, '5f4dcc3b5aa765d61d8327deb882cf99', 'SUPER', 1, NULL, 'Andrew', 'Luntz', 'ajluntz@knights.ucf.edu'),
(2, '5f4dcc3b5aa765d61d8327deb882cf99', 'SUPER', 1, NULL, 'Jason', 'Surh', 'jdsurh@knights.ucf.edu'),
(3, '5f4dcc3b5aa765d61d8327deb882cf99', 'USER', 1, NULL, 'John', 'Snow', 'johnsnow@knights.ucf.edu'),
(4, '5f4dcc3b5aa765d61d8327deb882cf99', 'USER', 3, NULL, 'Jeremy', 'Lopez', 'jeremylpz@live.seminolestate.edu'),
(5, '5f4dcc3b5aa765d61d8327deb882cf99', 'USER', 1, NULL, 'Eddard', 'Stark', 'eddardstark@gmail.com'),
(6, '5f4dcc3b5aa765d61d8327deb882cf99', 'USER', 1, NULL, 'Coltan', 'Carr', 'coltancarr@knights.ucf.edu'),
(7, '5f4dcc3b5aa765d61d8327deb882cf99', 'USER', 4, NULL, 'mike', 'hawk', 'mikehawk@fau.edu'),
(8, '5f4dcc3b5aa765d61d8327deb882cf99', 'USER', NULL, NULL, 'Michael', 'Luntz', 'mike.d.luntz@lmco.com'),
(9, '5f4dcc3b5aa765d61d8327deb882cf99', 'USER', NULL, NULL, 'Jody', 'Luntz', 'mjluntz@bellsouth.net');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`eventid`),
  ADD UNIQUE KEY `ratingid` (`ratingid`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`locationid`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`ratingid`);

--
-- Indexes for table `rsos`
--
ALTER TABLE `rsos`
  ADD PRIMARY KEY (`rsoid`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `universities`
--
ALTER TABLE `universities`
  ADD PRIMARY KEY (`universityid`),
  ADD UNIQUE KEY `website` (`website`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `locationid` (`locationid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `eventid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `locationid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `ratingid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT for table `rsos`
--
ALTER TABLE `rsos`
  MODIFY `rsoid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `universities`
--
ALTER TABLE `universities`
  MODIFY `universityid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`ratingid`) REFERENCES `ratings` (`ratingid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `universities`
--
ALTER TABLE `universities`
  ADD CONSTRAINT `universities_ibfk_1` FOREIGN KEY (`locationid`) REFERENCES `locations` (`locationid`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
