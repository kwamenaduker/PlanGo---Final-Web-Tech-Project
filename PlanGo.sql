-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 20, 2024 at 02:49 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `PlanGo`
--

-- --------------------------------------------------------

--
-- Table structure for table `Activities`
--

CREATE TABLE `Activities` (
  `ActivityID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `Description` text NOT NULL,
  `ActivityDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Activities`
--

INSERT INTO `Activities` (`ActivityID`, `UserID`, `Description`, `ActivityDate`) VALUES
(1, 3, 'Created a new trip: Detty December.', '2024-12-16 20:19:03'),
(2, 3, 'Created a new trip: Go There.', '2024-12-16 20:36:22'),
(3, 3, 'Created a new trip: Jaiye.', '2024-12-16 23:38:52'),
(4, 3, 'Created a new trip: Baecation.', '2024-12-17 02:37:47'),
(6, 5, 'Created a new trip: Jaiye.', '2024-12-18 00:03:49'),
(7, 5, 'Created a new trip: Go There.', '2024-12-18 00:11:00'),
(8, 5, 'Created a new trip: W.', '2024-12-18 01:03:52'),
(9, 5, 'Created a new trip: We Oudy.', '2024-12-18 01:24:43'),
(10, 5, 'Updated a trip: Ready.', '2024-12-18 03:02:05'),
(12, 5, 'Updated a trip: BORGAS ALANDI.', '2024-12-18 03:05:12'),
(13, 5, 'Updated a trip: L.', '2024-12-18 03:10:38'),
(14, 5, 'Updated a trip: Many Things Dey Happen Inside.', '2024-12-18 22:31:49'),
(15, 4, 'Created a new trip: hahahaha.', '2024-12-19 23:13:01'),
(16, 4, 'Created a new trip: Active', '2024-12-20 00:48:48');

-- --------------------------------------------------------

--
-- Table structure for table `Budgets`
--

CREATE TABLE `Budgets` (
  `BudgetID` int(11) NOT NULL,
  `TripID` int(11) NOT NULL,
  `Category` varchar(255) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Budgets`
--

INSERT INTO `Budgets` (`BudgetID`, `TripID`, `Category`, `Amount`, `CreatedAt`) VALUES
(2, 7, 'Transport', 50.00, '2024-12-19 22:08:37'),
(3, 7, 'Food', 2000.00, '2024-12-19 23:05:26'),
(4, 7, 'Hotel', 4000.00, '2024-12-19 23:05:33'),
(5, 12, 'General', 3999.98, '2024-12-20 00:48:48');

-- --------------------------------------------------------

--
-- Table structure for table `Itineraries`
--

CREATE TABLE `Itineraries` (
  `ItineraryID` int(11) NOT NULL,
  `TripID` int(11) NOT NULL,
  `Activity` varchar(255) NOT NULL,
  `ActivityDate` date NOT NULL,
  `StartTime` time NOT NULL,
  `EndTime` time NOT NULL,
  `Notes` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Itineraries`
--

INSERT INTO `Itineraries` (`ItineraryID`, `TripID`, `Activity`, `ActivityDate`, `StartTime`, `EndTime`, `Notes`, `CreatedAt`) VALUES
(1, 7, 'Party', '2024-12-21', '22:30:00', '23:30:00', '', '2024-12-19 22:30:34');

-- --------------------------------------------------------

--
-- Table structure for table `SavedPlaces`
--

CREATE TABLE `SavedPlaces` (
  `PlaceID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `PlaceName` varchar(100) NOT NULL,
  `Location` varchar(100) DEFAULT NULL,
  `Notes` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `SavedPlaces`
--

INSERT INTO `SavedPlaces` (`PlaceID`, `UserID`, `PlaceName`, `Location`, `Notes`, `CreatedAt`) VALUES
(1, 3, 'Santorini', 'Greece', '', '2024-12-16 23:43:20'),
(3, 3, 'Ibiza', 'Spain', '', '2024-12-16 23:52:21'),
(4, 3, 'Tema', 'Ghana', '', '2024-12-17 00:50:02'),
(5, 5, 'Sabatani', 'Accra', '', '2024-12-18 00:04:04'),
(24, 5, 'RED', 'Activ', '', '2024-12-18 01:57:10'),
(25, 5, 'Yaya', 'Stadium', '', '2024-12-18 02:02:20'),
(26, 5, 'Get', 'IN', '', '2024-12-18 03:01:04'),
(27, 5, 'WE', 'OUDY', '', '2024-12-18 23:02:26');

-- --------------------------------------------------------

--
-- Table structure for table `Trips`
--

CREATE TABLE `Trips` (
  `TripID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `TripName` varchar(100) NOT NULL,
  `Destination` varchar(100) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `Description` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Trips`
--

INSERT INTO `Trips` (`TripID`, `UserID`, `TripName`, `Destination`, `StartDate`, `EndDate`, `Description`, `CreatedAt`) VALUES
(2, 3, 'Go There', 'Jamaica', '2024-12-16', '2024-12-22', 'Carnival', '2024-12-16 20:36:22'),
(3, 3, 'Jaiye', 'Santorini', '2024-12-17', '2024-12-30', 'just to have fun', '2024-12-16 23:38:52'),
(4, 3, 'Baecation', 'Bali', '2024-12-17', '2024-12-20', 'Marry', '2024-12-17 02:37:47'),
(7, 5, 'BORGAS ALANDI', 'Accra', '2024-12-20', '2025-01-05', '0', '2024-12-18 00:11:00'),
(8, 5, 'Many Things Dey Happen Inside', 'Lagos', '2024-12-18', '2024-12-29', '0', '2024-12-18 01:03:52'),
(9, 5, 'We Oudy', 'Bali', '2024-12-18', '2024-12-19', '', '2024-12-18 01:24:43'),
(10, 4, 'hehehehehe', 'Accra', '2024-12-20', '2024-12-21', '-', '2024-12-19 23:13:01'),
(12, 4, 'Active', 'Accra', '2024-12-20', '2024-12-22', '', '2024-12-20 00:48:48');

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `UserID` int(11) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `Email` varchar(150) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `Role` enum('Admin','User') NOT NULL DEFAULT 'User'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`UserID`, `FirstName`, `LastName`, `Email`, `Password`, `CreatedAt`, `Role`) VALUES
(3, 'Kwamena', 'Duker', 'kwaduke17@gmail.com', '$2y$10$KkDhkBUsYk.gZK0kNYqlzejWn7cpsT7s5G6T3uBsDq7H1Z/hJ0ytG', '2024-12-16 13:25:35', 'Admin'),
(4, 'Kojo', 'Duker', 'kwaduke18@gmail.com', '$2y$10$rjHoce8OBcya.K/GT/yjy.OiIKWqUC8FDhE8PePaOoHE7Bzx0wjSy', '2024-12-17 00:45:57', 'User'),
(5, 'Acquah', 'Duker', 'kwaduke19@gmail.com', '$2y$10$/1vavEiu8UHzh4D4O9Y/j.Er9RdaStOGKlm0yrJrfIEApDGMyGKD2', '2024-12-18 00:03:11', 'User'),
(6, 'Kweku', 'Duker', 'kwaduke20@gmail.com', '$2y$10$Z0LYn2ixZ71dImC/mI8SMuSFWeojKGi/j2rmPIEP9/uHxWHQAagGO', '2024-12-19 23:40:06', 'User'),
(7, 'Yaw', 'Pyne', 'kwaduke22@gmail.com', '$2y$10$5Qup4Tugy.ZITZGnMoe4Zuz9jqUM9gbYHNP36iXUd/oxx5EdUA7Q2', '2024-12-20 01:05:07', 'Admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Activities`
--
ALTER TABLE `Activities`
  ADD PRIMARY KEY (`ActivityID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `Budgets`
--
ALTER TABLE `Budgets`
  ADD PRIMARY KEY (`BudgetID`),
  ADD KEY `TripID` (`TripID`);

--
-- Indexes for table `Itineraries`
--
ALTER TABLE `Itineraries`
  ADD PRIMARY KEY (`ItineraryID`),
  ADD KEY `TripID` (`TripID`);

--
-- Indexes for table `SavedPlaces`
--
ALTER TABLE `SavedPlaces`
  ADD PRIMARY KEY (`PlaceID`),
  ADD UNIQUE KEY `UserID` (`UserID`,`PlaceName`);

--
-- Indexes for table `Trips`
--
ALTER TABLE `Trips`
  ADD PRIMARY KEY (`TripID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Activities`
--
ALTER TABLE `Activities`
  MODIFY `ActivityID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `Budgets`
--
ALTER TABLE `Budgets`
  MODIFY `BudgetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Itineraries`
--
ALTER TABLE `Itineraries`
  MODIFY `ItineraryID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `SavedPlaces`
--
ALTER TABLE `SavedPlaces`
  MODIFY `PlaceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `Trips`
--
ALTER TABLE `Trips`
  MODIFY `TripID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Activities`
--
ALTER TABLE `Activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `Budgets`
--
ALTER TABLE `Budgets`
  ADD CONSTRAINT `budgets_ibfk_1` FOREIGN KEY (`TripID`) REFERENCES `Trips` (`TripID`) ON DELETE CASCADE;

--
-- Constraints for table `Itineraries`
--
ALTER TABLE `Itineraries`
  ADD CONSTRAINT `itineraries_ibfk_1` FOREIGN KEY (`TripID`) REFERENCES `Trips` (`TripID`) ON DELETE CASCADE;

--
-- Constraints for table `SavedPlaces`
--
ALTER TABLE `SavedPlaces`
  ADD CONSTRAINT `savedplaces_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE CASCADE;

--
-- Constraints for table `Trips`
--
ALTER TABLE `Trips`
  ADD CONSTRAINT `trips_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
