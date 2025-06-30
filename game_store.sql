-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 30, 2025 at 07:54 AM
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
(1, 'Jason', 'Valentino', 'JasonAdmin', 'jason@admin.com', '2000-04-20', '08952142142', 'cfdb09744b075bfb140be051ccd91f0a'),
(2, 'Andi', 'Saputra', 'AdminAndi', 'AdminAndi@gamestore.com', '2001-08-16', '89312412582', 'f55a2ac6a334bfba03e66f6a8a459113'),
(3, 'Nino', 'Kusuma', 'AdminNino', 'undersea@gamestore.com', '2004-01-16', '082142124850', '78f29cdd54526b8e04b6f2b727d7bbcc'),
(4, 'Mikha', 'Dandi', 'MikeAdmin', 'AdminMike@gamestore.com', '2001-06-03', '0898274829', 'e64b78fc3bc91bcbc7dc232ba8ec59e0'),
(7, 'Mocca', 'Saputra', 'MoccaAdmin', 'MoccaAdmin@gamestore.com', '2021-06-03', '0898274829', 'cfed01013118b69573256b6f8abdfdae');

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
  `image` varchar(50) NOT NULL,
  `adminID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `game`
--

INSERT INTO `game` (`gameID`, `gameCode`, `title`, `genre`, `platform`, `price`, `releaseDate`, `developer`, `publisher`, `description`, `image`, `adminID`) VALUES
(1, 'AC001', 'Monster Hunter World', 'Action', 'PS4', 50, '2018-02-26', 'Capcom', 'Capcom', 'Welcome to a new world! Take on the role of a hunter and slay ferocious monsters in a living, breathing ecosystem where you can use the landscape and its diverse inhabitants to get the upper hand. Hunt alone or in co-op with up to three other players, and use materials collected from fallen foes to craft new gear and take on even bigger, badder beasts!', '1750939119_mhw.png', 1),
(2, 'SH002', 'Valorant', 'Shooter', 'PC', 0, '2012-08-22', 'Valve', 'Valve', 'For over two decades, Counter-Strike has offered an elite competitive experience, one shaped by millions of players from across the globe. And now the next chapter in the CS story is about to begin. This is Counter-Strike 2.', '1750939145_cs2.png', 2),
(3, 'HR001', 'Outlast', 'Horror', 'PC', 19, '2013-09-04', 'Red Barrels', 'Red Barrels', 'Hell is an experiment you can\'t survive in Outlast, a first-person survival horror game developed by veterans of some of the biggest game franchises in history. As investigative journalist Miles Upshur, explore Mount Massive Asylum and try to survive long enough to discover its terrible secret... if you dare.', '1750939184_outlast.png', 1),
(4, 'AD020', 'Red Dead Redemption 2', 'Adventure', 'Xbox One', 69, '2018-10-26', 'Rockstar Games', 'Rockstar Games', 'America, 1899.\r\n\r\nArthur Morgan and the Van der Linde gang are outlaws on the run. With federal agents and the best bounty hunters in the nation massing on their heels, the gang must rob, steal and fight their way across the rugged heartland of America in order to survive. As deepening internal divisions threaten to tear the gang apart, Arthur must make a choice between his own ideals and loyalty to the gang who raised him.', '1750930744_rdr2.png', 3),
(9, 'RA001', 'F1 2025', 'Racing', 'PC', 69, '2025-05-30', 'Codemasters', 'Electronic Arts', 'NEXT GENERATION RACING SIMULATOR Assetto Corsa features an advanced DirectX 11 graphics engine that recreates an immersive environment, dynamic lighthing and realistic materials and surfaces. The advanced physics engine is being designed to provide a very realistic driving experience, including features and aspects of real cars, never seen on any other racing simulator such as tyre flat spots, heat cycles including graining and blistering, very advanced aerodynamic simulation with active movable aerodynamics parts controlled in real time by telemetry input channels, hybrid systems with kers and energy recovery simulation. Extremely detailed with single player and multiplayer options, exclusive licensed cars reproduced with the best accuracy possible, thanks to the official cooperation of Car Manufacturers.ASSETTO CORSA has been developed at the KUNOS Simulazioni R&D office, located just inside the international racing circuit of Vallelunga, allowing the team to develop the game with the cooperation of real world racing drivers and racing teams.', '1750939219_f1.png', 1),
(14, 'AD002', 'Clair Obscur: Expedition 33', 'Adventure', 'PC', 49, '2025-04-24', 'Sandfall Interactive', 'Kepler Interactive', 'Lead the members of Expedition 33 on their quest to destroy the Paintress so that she can never paint death again. Explore a world of wonders inspired by Belle Époque France and battle unique enemies in this turn-based RPG with real-time mechanics.', '1750930040_clair2.png', 1),
(15, 'RA002', 'Assetto Corsa Competizionze', 'Racing', 'PC', 50, '2020-12-19', 'Kunoz Simulazioni', 'Kunoz Simulazioni', 'NEXT GENERATION RACING SIMULATOR Assetto Corsa features an advanced DirectX 11 graphics engine that recreates an immersive environment, dynamic lighthing and realistic materials and surfaces. The advanced physics engine is being designed to provide a very realistic driving experience, including features and aspects of real cars, never seen on any other racing simulator such as tyre flat spots, heat cycles including graining and blistering, very advanced aerodynamic simulation with active movable aerodynamics parts controlled in real time by telemetry input channels, hybrid systems with kers and energy recovery simulation. Extremely detailed with single player and multiplayer options, exclusive licensed cars reproduced with the best accuracy possible, thanks to the official cooperation of Car Manufacturers.ASSETTO CORSA has been developed at the KUNOS Simulazioni R&D office, located just inside the international racing circuit of Vallelunga, allowing the team to develop the game with the cooperation of real world racing drivers and racing teams.', '1751247101_acc.png', 2),
(17, 'AC002', 'Yakuza: Like a Dragon', 'Action', 'Xbox Series S', 29, '2020-11-11', 'Ryu Ga Gotoku Studio', 'SEGA', 'Become Ichiban Kasuga, a low-ranking yakuza grunt left on the brink of death by the man he trusted most. Take up your legendary bat and get ready to crack some underworld skulls in dynamic RPG combat set against the backdrop of modern-day Japan.', '1751262457_yakuza2.png', 5),
(18, 'HR002', 'Silent Hill', 'Horror', 'PS5', 69, '2024-10-08', 'Bloober Team SA', 'KONAMI', 'Investigating a letter from his late wife, James returns to where they made so many memories - Silent Hill. What he finds is a ghost town, prowled by disturbing monsters and cloaked in deep fog. Confront the monsters, solve puzzles, and search for traces of your wife in this remake of SILENT HILL 2.', '1751262679_silenthill2.png', 1),
(19, 'ST001', 'Broken Arrow', 'Strategy', 'PC', 29, '2025-06-19', 'Steel Balalaika', 'Slitherine Ltd.', 'Broken Arrow is a large-scale real-time modern warfare tactics game that combines the complexity of joint-forces wargaming with action-packed real-time tactics gameplay.', '1751262856_brokenarrow.png', 4);

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
(1, 1, 'Dodi', 2, 'Counter Strike 2', 1, 0, '2025-04-22'),
(2, 2, 'Dora', 4, 'Red Dead Redemption 2', 2, 50, '2025-04-22'),
(3, 3, 'Dodi', 2, 'Counter Strike 2', 3, 0, '2025-04-23'),
(4, 2, 'Dora', 4, 'Red Dead Redemption 2', 3, 50, '2025-06-10');

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
(1, 'Gopay', 'Completed'),
(2, 'Visa', 'Completed'),
(3, 'Gopay', 'Completed');

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
(1, 1, 'DodiX', 2, 'Counter Strike 2', 'It doesn\'t matter if you have 100 hours of playtime, 1000 hours, or even 5000 hours, you will remain a noob forever. There are enough cheaters in the game to make you drop the game, but once you get caught up in this endless addiction, there is no turning back.', 8, '2025-02-11'),
(2, 1, 'DodiX', 1, 'Monster Hunter World', 'Peak Game', 10, '2025-04-15'),
(3, 2, 'LovelyDora', 3, 'Outlast', 'I was not scared at all (lie...)\r\nChase scenes increase my heartbeat... I don\'t want to experience something like that again but will probably continue to do so regardless.\r\n\r\n9/10 very good horror game', 9, '2024-06-19'),
(5, 3, 'Juventus', 2, 'Counter Strike 2', 'Kren bangetss.', 9, '2025-05-11');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
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
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `firstName`, `lastName`, `username`, `email`, `dateOfBirth`, `phoneNumber`, `password`) VALUES
(1, 'Dodi', 'Sumarji', 'DodiSu', 'Dodi@gmail.com', '2006-10-13', '08219475837', '87c6f6f11cb06306a44561f2e032125f'),
(2, 'Dora', 'Vanie', 'LovelyDora', 'Dora@gmail.com', '2004-05-21', '08219473958', '4ab52314fe615b468eaef0f57e06e6fd'),
(4, 'Juven', 'Tus', 'Juventus', 'Juventus@gmail.com', '2016-06-09', '08219475837', '38135017aa6b641ff2e7358772ea9cac'),
(5, 'Inter', 'Milan', 'IM', 'Milan@gmail.com', '2001-11-13', '08219475838', 'daae616a8c3c5e2a8f28829653efcf4b');

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `adminID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `gameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `orderID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `paymentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `reviewID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
