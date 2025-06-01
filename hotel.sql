-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gép: 127.0.0.1
-- Létrehozás ideje: 2025. Jún 01. 22:42
-- Kiszolgáló verziója: 10.4.32-MariaDB
-- PHP verzió: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `hotel`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `room_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` varchar(20) DEFAULT 'aktív',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `bookings`
--

INSERT INTO `bookings` (`id`, `user_email`, `room_id`, `start_date`, `end_date`, `status`, `created_at`) VALUES
(1, 'kissjanos@gmail.com', 4, '2025-05-15', '2025-05-24', 'lezárva', '2025-05-28 18:14:58'),
(2, 'kissjanos@gmail.com', 10, '2025-05-29', '2025-05-30', 'lezárva', '2025-05-28 22:33:29'),
(3, 'kissjanos@gmail.com', 8, '2025-05-23', '2025-05-28', 'lezárva', '2025-05-28 22:55:55'),
(4, 'liszkaidominik@gmail.com', 10, '2025-05-23', '2025-05-27', 'aktív', '2025-05-29 21:54:47'),
(5, 'sandorajsa@gmail.com', 10, '2025-06-19', '2025-07-19', 'lezárva', '2025-05-29 22:26:11');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `rating` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `approved` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `reviews`
--

INSERT INTO `reviews` (`id`, `name`, `content`, `rating`, `room_id`, `booking_id`, `created_at`, `approved`) VALUES
(1, 'Kiss János', 'Tök jó', 4, 4, 1, '2025-05-28 18:51:41', 1),
(2, 'Kiss János', 'Kimagaslóan magas tisztaság, valamint szeretetteljes vendégfogadás!', 5, 8, 3, '2025-05-28 22:56:39', 1),
(3, 'Liszkai Dominik', 'Szar!', 1, 10, 4, '2025-05-29 21:56:52', 0);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` int(11) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `features` varchar(255) DEFAULT NULL,
  `facilities` varchar(255) DEFAULT NULL,
  `guests` varchar(255) DEFAULT NULL,
  `rating` float DEFAULT 0,
  `stars` int(11) DEFAULT 4,
  `available_from` date DEFAULT NULL,
  `available_to` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `rooms`
--

INSERT INTO `rooms` (`id`, `name`, `description`, `price`, `image`, `created_at`, `features`, `facilities`, `guests`, `rating`, `stars`, `available_from`, `available_to`) VALUES
(5, 'Napfény Szoba', 'világos, meleg hangulatot sugall', 15000, 'uploads/68375e0a48cdf_1.jpg', '2025-05-28 21:03:38', '2 szoba,1 fürdőszoba', 'Wifi,Televízió', '5 Felnőtt, 4 Gyerek', 0, 3, '2025-05-28', '2025-09-30'),
(6, 'Holdfény Szoba', 'nyugodt, esti atmoszférát idéz', 20000, 'uploads/68375e9268ac7_2.jpg', '2025-05-28 21:05:54', '2 szoba,1 fürdőszoba,1 erkély', 'Wifi,Televízió,Légkondi', '3 Felnőtt,4 Gyerek', 0, 3, '2025-06-06', '2025-06-25'),
(7, 'Levendula Szoba', 'természetes, relaxáló hatású név', 25000, 'uploads/68375ef81e7d3_4.jpg', '2025-05-28 21:07:36', '1 szoba, 1 fürdőszoba, 2 erkély', 'Wifi,Televízió', '6 Felnőtt,3 Gyerek', 0, 4, '2025-05-16', '2025-06-16'),
(8, 'Panoráma Szoba', 'kilátásra utaló, elegáns név', 18000, 'uploads/68375f5eb1536_3.jpg', '2025-05-28 21:09:18', '1 szoba, 1 fürdőszoba, 2 erkély', 'Wifi,Televízió,Légkondi', '2 Felnőtt', 0, 4, '2025-05-07', '2025-05-31'),
(9, 'Borostyán Szoba', 'természetközeli és meghitt hangulatú', 22000, 'uploads/68375fb828c22_5.jpg', '2025-05-28 21:10:48', '3 szoba, 2 fürdőszoba, 2 erkély', 'Wifi,Televízió,Légkondi', '2 Felnőtt, 1 Gyermek', 0, 5, '2025-05-24', '2025-08-14'),
(10, 'Kék Óceán Szoba', 'friss, tengerparti érzetet kelt', 35000, 'uploads/6837604b8c5ce_6.jpg', '2025-05-28 21:13:15', '3 szoba, 2 fürdőszoba, 2 erkély', 'Wifi,Televízió,Légkondi', '6 Felnőtt,3 Gyerek', 0, 5, '2025-05-09', '2025-08-29');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `address` varchar(255) NOT NULL,
  `pin` varchar(10) NOT NULL,
  `birthdate` date NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- A tábla adatainak kiíratása `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `address`, `pin`, `birthdate`, `password`, `created_at`, `image`) VALUES
(1, 'Kiss János', 'kissjanos@gmail.com', '421902369963', '9100 Tét, Hunyadi utca 16', '9876', '2024-07-11', '$2y$10$EriPCtm2ptAax4g51G1xE.UAr8cvxgTBGDedvuUhVcIfqvCNh7H96', '2025-05-28 16:51:45', 'uploads/683723012d6ea_Képernyőfelvétel (42).png'),
(2, 'Liszkai Dominik', 'liszkaidominik@gmail.com', '36303654365', 'Ásványráró, Petőfi utca 69', '123456', '2006-09-27', '$2y$10$eAQqvmgJVSTT8w/cFdZSHeatfz66cUEcHij3Xh.kn7MAkg9QDnuFa', '2025-05-29 21:53:59', 'uploads/6838bb579f1a2_Képernyőfelvétel (4).png'),
(3, 'Sándor Ajsa', 'sandorajsa@gmail.com', '36302052547', '9100 Tét, Hunyadi utca 16', '12369', '2006-08-03', '$2y$10$bD3Awdl1YAt9WMKgHWboa.t/TvpIWXWo.Aot7lXnURdUkhfhwEVHq', '2025-05-29 22:23:47', 'uploads/6838c25315b97_banana.png');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`);

--
-- A tábla indexei `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT a táblához `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT a táblához `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT a táblához `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
