-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 30, 2015 at 02:41 PM
-- Server version: 5.5.44-0ubuntu0.14.04.1
-- PHP Version: 5.5.9-1ubuntu4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `phalcon`
--

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `controller` varchar(20) NOT NULL,
  `action` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_permissions` (`role_id`,`controller`,`action`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=28 ;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `role_id`, `controller`, `action`) VALUES
(11, 1, 'role-api', 'assign'),
(9, 1, 'role-api', 'create'),
(10, 1, 'role-api', 'delete'),
(7, 1, 'role-api', 'getAll'),
(8, 1, 'role-api', 'getUsers'),
(6, 1, 'user-api', 'changePassword'),
(4, 1, 'user-api', 'create'),
(5, 1, 'user-api', 'delete'),
(1, 1, 'user-api', 'getAll'),
(2, 1, 'user-api', 'getById'),
(3, 1, 'user-api', 'getRoles'),
(13, 2, 'user-api', 'changePassword'),
(27, 2, 'user-api', 'getAll'),
(12, 2, 'user-api', 'getRoles');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`) VALUES
(1, 'admins'),
(3, 'newrole'),
(2, 'users');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(65) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `expire_time` datetime DEFAULT NULL,
  `access_token` varchar(250) DEFAULT NULL,
  `batch_token` varchar(250) DEFAULT NULL,
  `batch_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `last_login`, `expire_time`, `access_token`, `batch_token`, `batch_time`) VALUES
(3, 'superadmin', '$2a$08$1ETCEMrfOCxvYvzE1584r.HwSZJo1qkhvGQoVa1F58wfsVsLi9r1i', '2015-09-30 07:13:51', '2015-09-30 08:03:51', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiJiNzE5NDg3ZDc0NWE4Zjg5NmEzZDY0YzM3NmU4NDk2ZSIsImlkIjoiMyIsInVzZXJuYW1lIjoic3VwZXJhZG1pbiIsInRva2VuQ3JlYXRlQXQiOiIyMDE1LTA5LTMwIDA3OjEzOjUxIn0.smo2753lytiozv-24Ws4K62NAUhmtHCsG0pCYAQuxIU', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiJkN2U3NTVhN2VjNDJhOGFhNWZkOTdlMjJlYjBhNTllYyIsImlkIjoiMyIsInVzZXJuYW1lIjoic3VwZXJhZG1pbiIsInRva2VuQ3JlYXRlQXQiOiIyMDE1LTA5LTMwIDA2OjMzOjUzIn0.cZVhefMbzd-xA_Uor3DD-vKBbCB27Qk1yOzldO6M3tQ', '2015-09-30 07:13:51'),
(5, 'demo', '$2a$08$PDdw6HvKd3hCOpSpz4KpNO0o4780/u9BdEF0Kk5TybGYkg2JBJC5G', '2015-09-30 06:45:18', '2015-09-30 07:35:18', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiJiYWM2MGEwNWNjZDFhMWFlMmViZTMyODViNmQ0NTM3OCIsImlkIjoiNSIsInVzZXJuYW1lIjoiZGVtbyIsInRva2VuQ3JlYXRlQXQiOiIyMDE1LTA5LTMwIDA2OjQ1OjE4In0.MIbhcN6r7kQ0UWj9yFb_KGBWuRVDkro3L0Gc-njV6q4', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiJjNjNiZDkxYmNjZTJhNmFmOGFkMDQ1NDVjNzc1YTYxNSIsImlkIjoiNSIsInVzZXJuYW1lIjoiZGVtbyIsInRva2VuQ3JlYXRlQXQiOiIyMDE1LTA5LTMwIDA2OjM0OjU3In0.bEIVxG2-J55BqjXOQ5wVk7MpO4T_kwEy2ghaN0iC4ps', '2015-09-30 06:45:18'),
(6, 'euler', '$2a$08$UuKPs6KrQgF7xxdpGoT5ku1BCkm0ChPWsCYYAfTytyRHpi.JpAxey', NULL, '2015-09-30 08:29:53', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiIyY2JiZjJmZmU5OWY1ZjgyOWFmYmRhZTAxNzIzNTdlNiIsImlkIjoiNiIsInVzZXJuYW1lIjoiZXVsZXIiLCJ0b2tlbkNyZWF0ZUF0IjoiMjAxNS0wOS0zMCAwNzozOTo1MyJ9.v7pA0qbHXhz6nDHCPz4mS5VwzBEW6ZjaO6JALWTx17c', NULL, '2015-09-30 07:39:53');

-- --------------------------------------------------------

--
-- Table structure for table `users_roles`
--

CREATE TABLE IF NOT EXISTS `users_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users_roles`
--

INSERT INTO `users_roles` (`user_id`, `role_id`) VALUES
(3, 1),
(5, 2);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `permissions`
--
ALTER TABLE `permissions`
  ADD CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users_roles`
--
ALTER TABLE `users_roles`
  ADD CONSTRAINT `users_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
