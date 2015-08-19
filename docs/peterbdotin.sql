-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 16, 2015 at 05:58 AM
-- Server version: 5.5.42-cll-lve
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `peterbdotin`
--

-- --------------------------------------------------------

--
-- Table structure for table `appuser`
--

CREATE TABLE IF NOT EXISTS `appuser` (
  `id` tinyint NOT NULL AUTO_INCREMENT,
  `username` varchar(10) NOT NULL,
  `password` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `blogcategory`
--

CREATE TABLE IF NOT EXISTS `blogcategory` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(256) DEFAULT NULL,
  `color` varchar(10) DEFAULT NULL,
  `postfolder` varchar(256) DEFAULT NULL,
  `url` varchar(256) DEFAULT NULL,
  `title` varchar(256) NOT NULL,
  `backgroundtitle` varchar(256) NOT NULL,
  `backgroundtitlecolor` varchar(256) NOT NULL,
  `carouseltitle` varchar(256) NOT NULL,
  `carouselsubtitle` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `blogcategory`
--

INSERT INTO `blogcategory` (`id`, `name`, `description`, `color`, `postfolder`, `url`, `title`, `backgroundtitle`, `backgroundtitlecolor`, `carouseltitle`, `carouselsubtitle`) VALUES
(1, 'Regex', 'Regular Expressions', '#e40177', '../../blogs/regex/', 'http://barraud-w7-1/peterbarraud/blogs/regex/', 'Regular Expressions', 'Regular Expressions', '#5a5a5a', 'Regular Expressions', 'Scripting will never be done without Regular Expressions. We''ll cover examples using Perl (the top of the class), we''ll also use FrameMaker''s own Regular Expression search and replace with examples.'),
(2, 'ExtendScript', 'ExtendScript for FrameMaker', '#9f5700', '../../blogs/estk/', 'http://barraud-w7-1/peterbarraud/blogs/estk/', 'ExtendScript for FrameMaker', 'ExtendScript for FrameMaker', '#5a5a5a', 'ExtendScript for FrameMaker', 'Automate a whole lot of what you do in FrameMaker. The blog covers all sorts of examples (including the source and executables). Look and learn from the source. Or simply use the executables.'),
(3, 'Perl', 'Scripting in Perl', '#004065', '../../blogs/perl/', 'http://barraud-w7-1/peterbarraud/blogs/perl/', 'Scripting in Perl', 'Scripting in Perl', '#5a5a5a', 'Scripting in Perl', 'Though you do loads of stuff with Perl. In my blog, I''m going to talk mostly about doing stuff with text. We''ll do stuff like reading from a file, or maybe even updating the contents of a file.'),
(4, 'FrameMaker', 'Adobe FrameMaker', '#9f5700', '../../blogs/fm/', 'http://barraud-w7-1/peterbarraud/blogs/fm/', 'Authoring in FrameMaker', 'Authoring in FrameMaker', '#5a5a5a', 'Authoring in FrameMaker', 'So FrameMaker is what author in and author for at Adobe. And that''s what we''re going to do here. I''ll try to add videos to show you how to get started with some things like say Structure author or DITA');

-- --------------------------------------------------------

--
-- Table structure for table `appconfig`
--

CREATE TABLE IF NOT EXISTS `appconfig` (
  `id` tinyint NOT NULL AUTO_INCREMENT,
  `templatefolder` varchar(256) NOT NULL,
  `posttemplatename` varchar(256) NOT NULL,
  `postfolder` varchar(256) NOT NULL,
  `posturl` varchar(256) NOT NULL,
  `categorytemplatename` varchar(256) NOT NULL,
  `indextemplatename` varchar(256) NOT NULL,
  `indexfolder` varchar(256) NOT NULL,
  `indexfilename` varchar(256) NOT NULL,
  `lightcarousel` varchar(500) NOT NULL,
  `darkcarousel` varchar(500) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `appconfig`
--

INSERT INTO `appconfig` (`id`, `templatefolder`, `posttemplatename`, `postfolder`, `posturl`, `categorytemplatename`, `indextemplatename`, `indexfolder`, `indexfilename`, `lightcarousel`, `darkcarousel`) VALUES
(1, '../templates/', 'blog_post.html', '../../blogs/blogs/', 'http://barraud-w7-1/peterbarraud/blogs/blogs/', 'category_index.html', 'peterbarraud_index.html', '../../', 'index.html', 'PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI5MDAiIGhlaWdodD0iNTAwIj48cmVjdCB3aWR0aD0iOTAwIiBoZWlnaHQ9IjUwMCIgZmlsbD0iIzY2NiI+PC9yZWN0Pjx0ZXh0IHRleHQtYW5jaG9yPSJtaWRkbGUiIHg9IjQ1MCIgeT0iMjUwIiBzdHlsZT0iZmlsbDojNmE2YTZhO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjU2cHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+U2Vjb25kIHNsaWRlPC90ZXh0Pjwvc3ZnPg==', 'PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI5MDAiIGhlaWdodD0iNTAwIj48cmVjdCB3aWR0aD0iOTAwIiBoZWlnaHQ9IjUwMCIgZmlsbD0iIzU1NSI+PC9yZWN0Pjx0ZXh0IHRleHQtYW5jaG9yPSJtaWRkbGUiIHg9IjQ1MCIgeT0iMjUwIiBzdHlsZT0iZmlsbDojNWE1YTVhO2ZvbnQtd2VpZ2h0OmJvbGQ7Zm9udC1zaXplOjU2cHg7Zm9udC1mYW1pbHk6QXJpYWwsSGVsdmV0aWNhLHNhbnMtc2VyaWY7ZG9taW5hbnQtYmFzZWxpbmU6Y2VudHJhbCI+VGhpcmQgc2xpZGU8L3RleHQ+PC9zdmc+');

-- --------------------------------------------------------

--
-- Table structure for table `blog`
--

CREATE TABLE IF NOT EXISTS `blog` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(1000) NOT NULL,
  `subtitle` varchar(1000) DEFAULT NULL,
  `pagename` varchar(1024) DEFAULT NULL,
  `blog` text,
  `readyforpublish` char(0) DEFAULT NULL,
  `createdate` datetime NOT NULL,
  `modifieddate` datetime DEFAULT NULL,
  `publishdate` datetime DEFAULT NULL,
  `unpublishdate` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `blogtype`
--

CREATE TABLE IF NOT EXISTS `blogtype` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `icon` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `blogtype`
--

INSERT INTO `blogtype` (`id`, `name`, `icon`) VALUES
(1, 'Sticky', NULL),
(2, 'Video', NULL),
(3, 'Quick Start', NULL),
(4, 'Tutorial', NULL);


-- --------------------------------------------------------

--
-- Table structure for table `userresponse`
--

CREATE TABLE IF NOT EXISTS `userresponse` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `response` text NOT NULL,
  `name` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `responsedate` datetime DEFAULT NULL,
  `isok` char(0) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_map_category`
--

CREATE TABLE IF NOT EXISTS `blog_map_blogcategory` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `blogid` smallint unsigned NOT NULL,
  `categoryid` tinyint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  foreign KEY (`blogid`) references blog (`id`),
  foreign KEY (`categoryid`) references blogcategory (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `blog_map_blogtype`
--

CREATE TABLE IF NOT EXISTS `blog_map_blogtype` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `blogid` smallint unsigned NOT NULL,
  `blogtypeid` tinyint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  foreign KEY (`blogid`) references blog (`id`),
  foreign KEY (`blogtypeid`) references blogtype (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



-- --------------------------------------------------------

--
-- Table structure for table `blog_map_userresponse`
--

CREATE TABLE IF NOT EXISTS `blog_map_userresponse` (
  `id` smallint unsigned NOT NULL AUTO_INCREMENT,
  `blogid` smallint unsigned NOT NULL,
  `userresponseid` smallint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  foreign KEY (`blogid`) references blog (`id`),
  foreign KEY (`userresponseid`) references userresponse (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
