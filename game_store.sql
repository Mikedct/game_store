-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 02:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `game_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminID` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `dateOfBirth` date NOT NULL,
  `phoneNumber` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminID`, `firstName`, `lastName`, `username`, `email`, `dateOfBirth`, `phoneNumber`, `password`) VALUES
(1, 'Jason', 'Valentino', 'SpaxAdmin', 'jason@admin.com', '2000-04-20', '08952142142', 'cfdb09744b075bfb140be051ccd91f0a'),
(2, 'Andi', 'Saputra', 'NoobMaster123', 'noobmaster@gmail.com', '2001-08-16', '089312412582', 'f55a2ac6a334bfba03e66f6a8a459113'),
(3, 'Nino', 'Kusuma', 'BlahajSharky', 'undersea@hotmail.com', '2003-01-16', '082142124850', '78f29cdd54526b8e04b6f2b727d7bbcc');

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `gameID` int(11) NOT NULL,
  `gameCode` varchar(50) NOT NULL,
  `title` varchar(50) NOT NULL,
  `genre` varchar(50) NOT NULL,
  `platform` varchar(50) NOT NULL,
  `price` int(11) NOT NULL,
  `releaseDate` date NOT NULL,
  `developer` varchar(50) NOT NULL,
  `publisher` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `adminID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game`
--

INSERT INTO `game` (`gameID`, `gameCode`, `title`, `genre`, `platform`, `price`, `releaseDate`, `developer`, `publisher`, `description`, `adminID`) VALUES
(1, 'AC001', 'Monster Hunter World', 'Action', 'PS4', 69, '2018-01-26', 'Capcom', 'Capcom', 'Welcome to a new world! Take on the role of a hunter and slay ferocious monsters in a living, breathing ecosystem where you can use the landscape and its diverse inhabitants to get the upper hand. Hunt alone or in co-op with up to three other players, and use materials collected from fallen foes to craft new gear and take on even bigger, badder beasts!', 1),
(2, 'SH001', 'Counter Strike 2', 'Shooter', 'PC', 0, '2012-08-22', 'Valve', 'Valve', 'For over two decades, Counter-Strike has offered an elite competitive experience, one shaped by millions of players from across the globe. And now the next chapter in the CS story is about to begin. This is Counter-Strike 2.', 2),
(3, 'HR001', 'Outlast', 'Horror', 'PC', 19, '2013-09-04', 'Red Barrels', 'Red Barrels', 'Hell is an experiment you can\'t survive in Outlast, a first-person survival horror game developed by veterans of some of the biggest game franchises in history. As investigative journalist Miles Upshur, explore Mount Massive Asylum and try to survive long enough to discover its terrible secret... if you dare.', 1),
(4, 'AD001', 'Red Dead Redemption 2', 'Adventure', 'Xbox One', 69, '2018-10-26', 'Rockstar Games', 'Rockstar Games', 'America, 1899.\r\n\r\nArthur Morgan and the Van der Linde gang are outlaws on the run. With federal agents and the best bounty hunters in the nation massing on their heels, the gang must rob, steal and fight their way across the rugged heartland of America in order to survive. As deepening internal divisions threaten to tear the gang apart, Arthur must make a choice between his own ideals and loyalty to the gang who raised him.', 3);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `orderID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `gameID` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `paymentID` int(11) NOT NULL,
  `totalPrice` int(11) NOT NULL,
  `orderDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`orderID`, `userID`, `username`, `gameID`, `title`, `paymentID`, `totalPrice`, `orderDate`) VALUES
(1, 1, 'Jason', 2, 'Counter Strike 2', 1, 0, '2025-04-22');

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `paymentID` int(11) NOT NULL,
  `paymentMethod` varchar(50) NOT NULL,
  `paymentStatus` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`paymentID`, `paymentMethod`, `paymentStatus`) VALUES
(1, 'Gopay', 'Waiting'),
(2, 'Visa', 'Waiting');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `reviewID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `gameID` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `Text` text NOT NULL,
  `Rating` int(2) NOT NULL,
  `Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review`
--

INSERT INTO `review` (`reviewID`, `userID`, `username`, `gameID`, `title`, `Text`, `Rating`, `Date`) VALUES
(1, 4, 'AxeViking', 2, 'Counter Strike 2', 'It doesn\'t matter if you have 100 hours of playtime, 1000 hours, or even 5000 hours, you will remain a noob forever. There are enough cheaters in the game to make you drop the game, but once you get caught up in this endless addiction, there is no turning back.', 7, '2025-02-11'),
(2, 1, 'SpaxterDex', 1, 'Monster Hunter World', 'Peak Game', 10, '2025-04-15'),
(3, 2, 'budiUBD', 3, 'Outlast', 'I was not scared at all (lie...)\r\nChase scenes increase my heartbeat... I don\'t want to experience something like that again but will probably continue to do so regardless.\r\n\r\n9/10 very good horror game', 9, '2024-06-19'),
(4, 3, 'DodiCool', 4, 'Red Dead Redemption 2', 'The game didn\'t really sit right with me until I realised that I didn\'t need to craft every outfit, find every dino bone and obsess over every cig card. This game made me realise that I honestly don\'t actually enjoy open-world games, and that\'s fine, because once I put aside the boring hunting and focused only on the story, I had a great adventure.\r\n', 8, '2020-07-17');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `userID` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `dateOfBirth` date NOT NULL,
  `phoneNumber` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userID`, `firstName`, `lastName`, `username`, `email`, `dateOfBirth`, `phoneNumber`, `password`) VALUES
(1, 'Jason', 'Valentino', 'SpaxterDex', 'jason@ubd.ac.id', '2000-04-20', '08952142142', 'cfdb09744b075bfb140be051ccd91f0a'),
(2, 'Budi', 'Dharma', 'budiUBD', 'budi@ubd.ac.id', '2002-12-20', '082525353623', '812e4bfbf919978d2ac7f5cff004c0b1'),
(3, 'Dodi', 'Rangga', 'DodiCool', 'dodi@ubd.ac.id', '2002-02-25', '082598260295', '17ebfeb66d2173e72ce691d018169841'),
(4, 'Nina', 'Bjorn', 'AxeViking', 'nina@ubd.ac.id', '2002-09-07', '082523450832', '3d2d7ffedc52d576f1038b7eb09c2274');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`gameID`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`orderID`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`paymentID`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`reviewID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `gameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `orderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `paymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `reviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
