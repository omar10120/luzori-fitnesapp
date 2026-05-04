-- phpMyAdmin SQL Dump
-- version 6.0.0-dev
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 25, 2025 at 09:39 AM
-- Server version: 8.0.34-0ubuntu0.20.04.1
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fitness_mighty`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` bigint UNSIGNED NOT NULL,
  `site_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_description` longtext COLLATE utf8mb4_unicode_ci,
  `site_copyright` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `linkedin_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language_option` json DEFAULT NULL,
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `help_support_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '#ec7e4a',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `site_name`, `site_email`, `site_description`, `site_copyright`, `facebook_url`, `instagram_url`, `twitter_url`, `linkedin_url`, `language_option`, `contact_email`, `contact_number`, `help_support_url`, `color`, `created_at`, `updated_at`) VALUES
(1, 'Mighty Fitness', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '[\"en\"]', NULL, NULL, NULL, '#ec7e4a', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `assign_diets`
--

CREATE TABLE `assign_diets` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `diet_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `assign_workouts`
--

CREATE TABLE `assign_workouts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `workout_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `body_parts`
--

CREATE TABLE `body_parts` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category_diets`
--

CREATE TABLE `category_diets` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chatgpt_fit_bots`
--

CREATE TABLE `chatgpt_fit_bots` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `question` longtext COLLATE utf8mb4_unicode_ci,
  `answer` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_schedules`
--

CREATE TABLE `class_schedules` (
  `id` bigint UNSIGNED NOT NULL,
  `class_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `workout_id` bigint UNSIGNED DEFAULT NULL,
  `workout_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `workout_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `link` text COLLATE utf8mb4_unicode_ci,
  `is_paid` tinyint(1) DEFAULT '0' COMMENT '0-free, 1-paid',
  `price` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_schedule_plans`
--

CREATE TABLE `class_schedule_plans` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `class_schedule_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `default_keywords`
--

CREATE TABLE `default_keywords` (
  `id` bigint UNSIGNED NOT NULL,
  `screen_id` bigint UNSIGNED DEFAULT NULL COMMENT 'screens screenID',
  `keyword_id` bigint UNSIGNED DEFAULT NULL COMMENT 'app keyword',
  `keyword_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keyword_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `default_keywords`
--

INSERT INTO `default_keywords` (`id`, `screen_id`, `keyword_id`, `keyword_name`, `keyword_value`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'lblWalkTitle1', 'Find The Right Workout \nfor What You Need', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(2, 1, 2, 'lblWalkTitle2', 'Choose Proper Workout \n& Diet Plan to Stay Fit.', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(3, 1, 3, 'lblWalkTitle3', 'Easily Track Your Daily Activity', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(4, 1, 4, 'lblGetStarted', 'Get Started', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(5, 1, 5, 'lblNext', 'Next', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(6, 1, 6, 'lblSkip', 'Skip', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(7, 1, 231, 'lblEmailIsInvalid', 'Email is invalid', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(8, 2, 7, 'lblLogin', 'Login', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(9, 2, 8, 'lblWelcomeBack', 'Welcome Back,', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(10, 2, 9, 'lblWelcomeBackDesc', 'Hello there, sign in to continue!', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(11, 2, 10, 'lblEmail', 'Email', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(12, 2, 11, 'lblEnterEmail', 'Enter Email', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(13, 2, 12, 'lblPassword', 'Password', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(14, 2, 13, 'lblEnterPassword', 'Enter Password', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(15, 2, 14, 'lblRememberMe', 'Remember Me', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(16, 2, 15, 'lblForgotPassword', 'Forgot Password?', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(17, 2, 16, 'lblOr', 'Or', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(18, 2, 17, 'lblNewUser', 'New User?', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(19, 2, 18, 'lblRegisterNow', 'Register Now', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(20, 2, 19, 'lblContactAdmin', 'Contact to administrator', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(21, 3, 20, 'lblTellUsAboutYourself', 'Tell us about yourself!', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(22, 3, 21, 'lblFirstName', 'First Name', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(23, 3, 22, 'lblEnterFirstName', 'Enter First Name', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(24, 3, 23, 'lblLastName', 'Last Name', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(25, 3, 24, 'lblEnterLastName', 'Enter Last Name', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(26, 3, 25, 'lblPhoneNumber', 'Phone Number', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(27, 3, 26, 'lblEnterPhoneNumber', 'Enter Phone Number', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(28, 3, 27, 'lblConfirmPassword', 'Confirm Password', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(29, 3, 28, 'lblEnterConfirmPwd', 'Enter Confirm Password', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(30, 3, 29, 'errorPwdLength', 'Password length should be more than 6', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(31, 3, 30, 'errorPwdMatch', 'Both password should be matched', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(32, 3, 31, 'lblAlreadyAccount', 'Already have an account?', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(33, 4, 32, 'lblWhtGender', 'What\\\'s Your Gender?', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(34, 4, 33, 'lblMale', 'Male', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(35, 4, 34, 'lblFemale', 'Female', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(36, 5, 35, 'lblHowOld', 'How old are you?', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(37, 6, 36, 'lblLetUsKnowBetter', 'Let us know you better', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(38, 6, 37, 'lblWeight', 'Weight', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(39, 6, 38, 'lblEnterWeight', 'Enter Weight', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(40, 6, 39, 'lblHeight', 'Height', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(41, 6, 40, 'lblEnterHeight', 'Enter Height', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(42, 6, 41, 'lblDone', 'Done', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(43, 6, 42, 'lblLbs', 'LBS', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(44, 6, 43, 'lblKg', 'KG', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(45, 6, 44, 'lblCm', 'CM', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(46, 6, 45, 'lblFeet', 'FEET', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(47, 7, 46, 'lblHome', 'Home', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(48, 7, 47, 'lblDiet', 'Diet', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(49, 7, 48, 'lblShop', 'Shop', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(50, 7, 232, 'lblReport', 'Metrics', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(51, 7, 49, 'lblProfile', 'Profile', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(52, 7, 50, 'lblTapBackAgainToLeave', 'Tap back again to leave', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(53, 8, 51, 'lblHey', 'Hey,', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(54, 8, 52, 'lblHomeWelMsg', 'Stay Healthy Always', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(55, 8, 53, 'lblSearch', 'Search', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(56, 8, 54, 'lblBodyPartExercise', 'Body Parts Exercises', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(57, 8, 55, 'lblEquipmentsExercise', 'Equipment-Based Exercises', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(58, 8, 56, 'lblWorkouts', 'Workouts', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(59, 8, 57, 'lblLevels', 'Workout Levels', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(60, 8, 202, 'lblTips', 'Tips', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(61, 8, 238, 'lblLevel', 'Level', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(62, 8, 241, 'lblDay', 'Day', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(63, 9, 58, 'lblEditProfile', 'Edit Profile', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(64, 9, 59, 'lblAge', 'Age', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(65, 9, 60, 'lblEnterAge', 'Enter Age', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(66, 9, 61, 'lblGender', 'Gender', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(67, 9, 62, 'lblSave', 'Save', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(68, 9, 215, 'lblTipsInst', 'Tips & Instructions', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(69, 10, 63, 'lblNotifications', 'Notifications', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(70, 10, 64, 'lblNotificationEmpty', 'No New Notifications', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(71, 10, 210, 'lblRepeat', 'Repeat', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(72, 11, 65, 'lblSearchExercise', 'Search Exercise', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(73, 11, 66, 'lblExerciseNoFound', 'Sorry, No Exercise Found', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(74, 11, 67, 'lblAll', 'All', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(75, 11, 68, 'lblNoFoundData', 'No Data Found', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(76, 11, 69, 'lblDuration', 'Duration', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(77, 12, 70, 'lblPackageTitle', 'Be Premium Get Unlimited Access', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(78, 12, 71, 'lblPackageTitle1', 'Enjoy Workout Access Without Ads And Restrictions', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(79, 12, 72, 'lblMonth', 'Month', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(80, 12, 73, 'lblYear', 'year', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(81, 12, 74, 'lblSubscribe', 'Subscribe', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(82, 12, 75, 'lblSelectPlanToContinue', 'Select a Plan to Continue', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(83, 12, 76, 'lblSubscriptionPlans', 'Subscription Plans', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(84, 12, 77, 'lblActive', 'Active', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(85, 12, 78, 'lblHistory', 'History', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(86, 12, 79, 'lblSubscriptionMsg', 'You Haven\\\'t Subscribed Yet', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(87, 12, 80, 'lblViewPlans', 'View Plans', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(88, 12, 81, 'lblYourPlanValid', 'your plan valid', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(89, 12, 82, 'lblCancelSubscription', 'Cancel Subscription', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(90, 12, 83, 'lblTo', 'Top Fitness Reads', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(91, 12, 201, 'lblBuyNow', 'Buy Now', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(92, 12, 203, 'lblBmi', 'BMI', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(93, 13, 84, 'lblNoSetsMsg', 'Can not go forward due to blank sets', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(94, 13, 85, 'lblStartExercise', 'Start Exercise', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(95, 13, 86, 'lblBodyParts', 'Body Parts', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(96, 13, 87, 'lblEquipments', 'Equipment\\\'s', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(97, 13, 218, 'lblSets', 'Sets', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(98, 13, 219, 'lblReps', 'Reps', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(99, 13, 220, 'lblSecond', 'Second', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(100, 13, 222, 'lblTenSecondRemaining', 'ten second remaining', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(101, 13, 223, 'lblThree', 'Three', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(102, 13, 224, 'lblTwo', 'Two', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(103, 13, 225, 'lblOne', 'One', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(104, 13, 226, 'lblEnterReminderName', 'Enter Reminder Name', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(105, 13, 227, 'lblEnterDescription', 'Enter Description', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(106, 13, 240, 'lblExerciseDone', 'Exercise Done', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(107, 14, 88, 'lblSuccess', 'Success', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(108, 14, 89, 'lblSuccessMsg', 'Successfully Done=>)', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(109, 14, 90, 'lblPaymentFailed', 'Payment Failed', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(110, 14, 91, 'lblPayments', 'Payments', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(111, 14, 92, 'lblPay', 'Pay', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(112, 15, 93, 'lblWorkoutLevel', 'Workout Level', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(113, 15, 204, 'lblFullBodyWorkout', 'Full Body Workout', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(114, 15, 205, 'lblTypes', 'Type\\\'s', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(115, 15, 206, 'lblClearAll', 'Cancel All', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(116, 15, 207, 'lblSelectAll', 'Select All', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(117, 15, 208, 'lblShowResult', 'Show Result', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(118, 15, 209, 'lblSelectLevels', 'Select Levels', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(119, 15, 221, 'lblWorkoutNoFound', 'Sorry, No Workout Found', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(120, 16, 94, 'lblDietCategories', 'Diet Categories', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(121, 16, 95, 'lblBestDietDiscoveries', 'Best Diet Discoveries', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(122, 16, 96, 'lblDietaryOptions', 'Dietary Options', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(123, 16, 97, 'lblResultNoFound', 'Sorry, No Results Found', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(124, 17, 98, 'lblProductCategory', 'Fitness Accessory Category', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(125, 17, 99, 'lblProductList', 'Fitness Accessorie', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(126, 17, 100, 'lblBreak', 'Blissful Break 😊', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(127, 18, 101, 'lblHeartRate', 'Heart Rate', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(128, 18, 102, 'lblPushUp', 'Push ups in 1 minute', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(129, 18, 103, 'lblUpdate', 'Update', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(130, 18, 104, 'lblHint', '50', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(131, 18, 105, 'lblDate', 'Date', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(132, 18, 106, 'lblDelete', 'Delete', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(133, 18, 107, 'lblDeleteAccountMSg', 'Are you sure you want to delete the', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(134, 18, 108, 'lblDeleteMsg', 'Are you certain you wish to permanently remove your account?', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(135, 18, 109, 'lblDeleteAccount', 'Delete Account', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(136, 18, 216, 'lblAdd', 'Add', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(137, 18, 239, 'lblSteps', 'Steps', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(138, 19, 110, 'lblBlog', 'Blogs', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(139, 19, 111, 'lblFavoriteWorkoutAndNutristions', 'Favorites Workout & Nutrition', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(140, 19, 112, 'lblDailyReminders', 'Daily Reminders', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(141, 19, 113, 'lblPlan', 'Assigned Workout & Diet', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(142, 19, 114, 'lblSettings', 'Settings', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(143, 19, 115, 'lblAboutApp', 'About App', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(144, 19, 116, 'lblLogout', 'Logout', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(145, 19, 117, 'lblLogoutMsg', 'Are you certain you wish to log out now?', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(146, 19, 118, 'lblPrivacyPolicy', 'Privacy Policy', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(147, 19, 119, 'lblTermsOfServices', 'Terms of Service', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(148, 19, 120, 'lblAboutUs', 'About Us', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(149, 19, 121, 'lblTopFitnessReads', 'Top Fitness Reads', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(150, 19, 122, 'lblTrendingBlogs', 'Trending Blogs', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(151, 19, 123, 'lblBlogNoFound', 'Sorry, No Blog Found', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(152, 19, 124, 'lblFollowUs', 'Follow Us', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(153, 19, 211, 'lblEveryday', 'Everyday', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(154, 19, 212, 'lblReminderName', 'Reminder name', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(155, 19, 213, 'lblDescription', 'Description', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(156, 20, 125, 'lblMetricsSettings', 'Metrics Settings', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(157, 20, 126, 'lblSelectLanguage', 'Choose Language', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(158, 20, 127, 'lblAppThemes', 'App Themes', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(159, 20, 128, 'lblChangePassword', 'AChange Password', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(160, 20, 129, 'lblLight', 'Light', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(161, 20, 130, 'lblDark', 'Dark', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(162, 20, 131, 'lblSystemDefault', 'System Default', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(163, 20, 132, 'lblSelectTheme', 'Theme', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(164, 20, 133, 'lblCurrentPassword', 'Current Password', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(165, 20, 134, 'lblEnterCurrentPwd', 'Enter Current Password', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(166, 20, 135, 'lblNewPassword', 'New Password', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(167, 20, 136, 'lblPasswordMsg', 'Your new password must be different from old password', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(168, 20, 137, 'lblEnterNewPwd', 'Enter New Password', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(169, 20, 139, 'lblSubmit', 'Submit', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(170, 20, 140, 'lblIdealWeight', 'Ideal weight', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(171, 20, 141, 'lblBmr', 'BMR', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(172, 20, 142, 'lblTotalSteps', 'Total steps', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(173, 20, 200, 'lblForgotPwdMsg', 'Please enter your email address to request a password reset', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(174, 20, 236, 'lblCancel', 'Cancel', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(175, 21, 143, 'lblChatConfirmMsg', 'Do you want to clear the conversations ?', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(176, 21, 144, 'lblYes', 'Yes', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(177, 21, 145, 'lblNo', 'No', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(178, 21, 146, 'lblFitBot', 'FitBot', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(179, 21, 147, 'lblClearConversion', 'Clear Conversion', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(180, 21, 148, 'lblChatHintText', 'How can i help you...', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(181, 21, 149, 'lblCopiedToClipboard', 'Copied to Clipboard', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(182, 21, 150, 'lblQue1', 'How can I start a fitness routine as a beginner?', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(183, 21, 151, 'lblQue2', 'What are some healthy nutrition tips for fitness and weight management?', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(184, 21, 152, 'lblQue3', 'What\\\'s the best way to lose weight through exercise and diet?', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(185, 21, 153, 'lblG', 'g', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(186, 21, 154, 'lblMainGoal', 'What is your main goal?', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(187, 21, 155, 'lblHowExperienced', 'How experienced are you with fitness?', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(188, 21, 157, 'lblHoweEquipment', 'What equipments do you have?', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(189, 21, 158, 'lblHoweOftenWorkout', 'How often do you want to workout?', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(190, 21, 245, 'lblFinish', 'Finish', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(191, 21, 160, 'lblProgression', 'Progression', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(192, 21, 161, 'lblEasyHabit', 'Easy to form habit', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(193, 21, 162, 'lblRecommend', 'We recommend', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(194, 21, 163, 'lblTimesWeek', 'times/week want to workout', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(195, 21, 164, 'lblOnlyTimesWeek', 'times/week', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(196, 21, 165, 'lblUsernameShouldNotContainSpace', 'Username should not contain space', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(197, 21, 166, 'lblMinimumPasswordLengthShouldBe', 'Minimum password length should be', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(198, 21, 167, 'lblInternetIsConnected', 'Internet is connected.', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(199, 21, 168, 'lblNoDurationMsg', 'Can not go forward due to blank duration', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(200, 21, 170, 'lblErrorNotAllow', 'Sorry, You are not allowed', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(201, 21, 171, 'lblPleaseTryAgain', 'Please try again', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(202, 21, 172, 'lblInvalidUrl', 'Invalid URL', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(203, 21, 173, 'lblCalories', 'Calories', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(204, 21, 174, 'lblCarbs', 'Carbs', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(205, 21, 175, 'lblFat', 'Fat', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(206, 21, 176, 'lblProtein', 'Protein', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(207, 21, 178, 'lblKcal', 'Kcal', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(208, 21, 179, 'lblIngredients', 'Ingredients', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(209, 21, 180, 'lblInstruction', 'Instruction', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(210, 21, 181, 'lblNoInternet', 'No Internet', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(211, 21, 234, 'lblFavourite', 'Favourite', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(212, 21, 235, 'lblStore', 'Store', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(213, 21, 237, 'lblPro', 'Pro', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(214, 22, 190, 'lblContinueWithPhone', 'Continue with Phone', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(215, 22, 191, 'lblRcvCode', 'You\\\'ll receive 6 digit code to verify next.', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(216, 22, 192, 'lblVerifyOTP', 'OTP Verification', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(217, 22, 193, 'lblVerifyProceed', 'Verify & Proceed', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(218, 22, 194, 'lblCode', 'We have sent the code verification to', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(219, 22, 195, 'lblMonthly', 'Monthly', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(220, 22, 214, 'lblFav', 'Preferred Workouts & Nutrition', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(221, 22, 217, 'lblEnterText', 'Please enter some text', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(222, 22, 228, 'lblErrorThisFiledIsRequired', 'This field is required', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(223, 22, 229, 'lblSomethingWentWrong', 'Something Went Wrong', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(224, 22, 230, 'lblErrorInternetNotAvailable', 'Your internet is not working', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(225, 22, 233, 'lblContinue', 'Continue', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(226, 23, 242, 'lblSchedule', 'Schedule', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(227, 23, 243, 'lblChangeView', 'Change calendar view', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(228, 23, 244, 'lblJoin', 'Join', '2025-06-25 04:02:31', '2025-06-25 04:02:31');

-- --------------------------------------------------------

--
-- Table structure for table `diets`
--

CREATE TABLE `diets` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categorydiet_id` bigint UNSIGNED DEFAULT NULL,
  `calories` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `carbs` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `protein` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fat` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `servings` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_time` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_featured` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `ingredients` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_premium` tinyint(1) DEFAULT '0' COMMENT '0-free, 1-premium',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exercises`
--

CREATE TABLE `exercises` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instruction` text COLLATE utf8mb4_unicode_ci,
  `tips` text COLLATE utf8mb4_unicode_ci,
  `video_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `video_url` text COLLATE utf8mb4_unicode_ci,
  `bodypart_ids` text COLLATE utf8mb4_unicode_ci,
  `duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `based` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'reps, time',
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'sets, duration',
  `equipment_id` bigint UNSIGNED DEFAULT NULL,
  `level_id` bigint UNSIGNED DEFAULT NULL,
  `sets` json DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `is_premium` tinyint(1) DEFAULT '0' COMMENT '0-free, 1-premium',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_score_data`
--

CREATE TABLE `game_score_data` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `score` int DEFAULT NULL,
  `country_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `language_default_lists`
--

CREATE TABLE `language_default_lists` (
  `id` bigint UNSIGNED NOT NULL,
  `default_language_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_language_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_language_country_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `language_default_lists`
--

INSERT INTO `language_default_lists` (`id`, `default_language_name`, `default_language_code`, `default_language_country_code`, `created_at`, `updated_at`) VALUES
(1, 'Afrikaans (South Africa)', 'af', 'af-ZA', NULL, NULL),
(2, 'Albanian (Albania)', 'sq', 'sq-AL', NULL, NULL),
(3, 'Alsatian (France)', 'gsw', 'gsw-FR', NULL, NULL),
(4, 'Amharic (Ethiopia)', 'am', 'am-ET', NULL, NULL),
(5, 'Arabic (Algeria)', 'ar', 'ar-DZ', NULL, NULL),
(6, 'Arabic (Bahrain)', 'ar', 'ar-BH', NULL, NULL),
(7, 'Arabic (Egypt)', 'ar', 'ar-EG', NULL, NULL),
(8, 'Arabic (Iraq)', 'ar', 'ar-IQ', NULL, NULL),
(9, 'Arabic (Jordan)', 'ar', 'ar-JO', NULL, NULL),
(10, 'Arabic (Kuwait)', 'ar', 'ar-KW', NULL, NULL),
(11, 'Arabic (Lebanon)', 'ar', 'ar-LB', NULL, NULL),
(12, 'Arabic (Libya)', 'ar', 'ar-LY', NULL, NULL),
(13, 'Arabic (Morocco)', 'ar', 'ar-MA', NULL, NULL),
(14, 'Arabic (Oman)', 'ar', 'ar-OM', NULL, NULL),
(15, 'Arabic (Qatar)', 'ar', 'ar-QA', NULL, NULL),
(16, 'Arabic (Saudi Arabia)', 'ar', 'ar-SA', NULL, NULL),
(17, 'Arabic (Syria)', 'ar', 'ar-SY', NULL, NULL),
(18, 'Arabic (Tunisia)', 'ar', 'ar-TN', NULL, NULL),
(19, 'Arabic (U.A.E.)', 'ar', 'ar-AE', NULL, NULL),
(20, 'Arabic (Yemen)', 'ar', 'ar-YE', NULL, NULL),
(21, 'Armenian (Armenia)', 'hy', 'hy-AM', NULL, NULL),
(22, 'Assamese (India)', 'as', 'as-IN', NULL, NULL),
(23, 'Azerbaijani (Cyrillic)', 'az', 'az-Cyrl', NULL, NULL),
(24, 'Azerbaijani (Cyrillic, Azerbaijan)', 'az', 'az-Cyrl-AZ', NULL, NULL),
(25, 'Azerbaijani (Latin)', 'az', 'az-Latn', NULL, NULL),
(26, 'Azerbaijani (Latin, Azerbaijan)', 'az', 'az-Latn-AZ', NULL, NULL),
(27, 'Bangla (Bangladesh)', 'bn', 'bn-BD', NULL, NULL),
(28, 'Bangla (India)', 'bn', 'bn-IN', NULL, NULL),
(29, 'Bashkir (Russia)', 'ba', 'ba-RU', NULL, NULL),
(30, 'Basque (Spain)', 'eu', 'eu-ES', NULL, NULL),
(31, 'Belarusian (Belarus)', 'be', 'be-BY', NULL, NULL),
(32, 'Bosnian (Cyrillic)', 'bs', 'bs-Cyrl', NULL, NULL),
(33, 'Bosnian (Cyrillic, Bosnia and Herzegovina)', 'bs', 'bs-Cyrl-BA', NULL, NULL),
(34, 'Bosnian (Latin)', 'bs', 'bs-Latn', NULL, NULL),
(35, 'Bosnian (Latin, Bosnia and Herzegovina)', 'bs', 'bs-Latn-BA', NULL, NULL),
(36, 'Breton (France)', 'br', 'br-FR', NULL, NULL),
(37, 'Bulgarian (Bulgaria)', 'bg', 'bg-BG', NULL, NULL),
(38, 'Burmese (Myanmar)', 'my', 'my-MM', NULL, NULL),
(39, 'Catalan (Spain)', 'ca', 'ca-ES', NULL, NULL),
(40, 'Central Atlas Tamazight (Arabic, Morocco)', 'tzm', 'tzm-Arab-MA', NULL, NULL),
(41, 'Central Kurdish', 'ku', 'ku-Arab', NULL, NULL),
(42, 'Central Kurdish (Iraq)', 'ku', 'ku-Arab-IQ', NULL, NULL),
(43, 'Cherokee', 'chr', 'chr-Cher', NULL, NULL),
(44, 'Cherokee (United States)', 'chr', 'chr-Cher-US', NULL, NULL),
(45, 'Chinese (Simplified) (zh-Hans)', 'zh', 'zh-Hans', NULL, NULL),
(46, 'Chinese (Simplified, People\'s Republic of China)', 'zh', 'zh-CN', NULL, NULL),
(47, 'Chinese (Simplified, Singapore)', 'zh', 'zh-SG', NULL, NULL),
(48, 'Chinese (Traditional) (zh-Hant)', 'zh', 'zh-Hant', NULL, NULL),
(49, 'Chinese (Traditional, Hong Kong S.A.R.)', 'zh', 'zh-HK', NULL, NULL),
(50, 'Chinese (Traditional, Macao S.A.R.)', 'zh', 'zh-MO', NULL, NULL),
(51, 'Chinese (Traditional, Taiwan)', 'zh', 'zh-TW', NULL, NULL),
(52, 'Corsican (France)', 'co', 'co-FR', NULL, NULL),
(53, 'Croatian (Croatia)', 'hr', 'hr-HR', NULL, NULL),
(54, 'Croatian (Latin, Bosnia and Herzegovina)', 'hr', 'hr-BA', NULL, NULL),
(55, 'Czech (Czech Republic)', 'cs', 'cs-CZ', NULL, NULL),
(56, 'Danish (Denmark)', 'da', 'da-DK', NULL, NULL),
(57, 'Dari (Afghanistan)', 'prs', 'prs-AF', NULL, NULL),
(58, 'Divehi (Maldives)', 'dv', 'dv-MV', NULL, NULL),
(59, 'Dutch (Belgium)', 'nl', 'nl-BE', NULL, NULL),
(60, 'Dutch (Netherlands)', 'nl', 'nl-NL', NULL, NULL),
(61, 'Dzongkha (Bhutan)', 'dz', 'dz-BT', NULL, NULL),
(62, 'English (Australia)', 'en', 'en-AU', NULL, NULL),
(63, 'English (Belize)', 'en', 'en-BZ', NULL, NULL),
(64, 'English (Canada)', 'en', 'en-CA', NULL, NULL),
(65, 'English (Caribbean)', 'en', 'en-029', NULL, NULL),
(66, 'English (Hong Kong)', 'en', 'en-HK', NULL, NULL),
(67, 'English (India)', 'en', 'en-IN', NULL, NULL),
(68, 'English (Ireland)', 'en', 'en-IE', NULL, NULL),
(69, 'English (Jamaica)', 'en', 'en-JM', NULL, NULL),
(70, 'English (Malaysia)', 'en', 'en-MY', NULL, NULL),
(71, 'English (New Zealand)', 'en', 'en-NZ', NULL, NULL),
(72, 'English (Republic of the Philippines)', 'en', 'en-PH', NULL, NULL),
(73, 'English (Singapore)', 'en', 'en-SG', NULL, NULL),
(74, 'English (South Africa)', 'en', 'en-ZA', NULL, NULL),
(75, 'English (Trinidad and Tobago)', 'en', 'en-TT', NULL, NULL),
(76, 'English (United Arab Emirates)', 'en', 'en-AE', NULL, NULL),
(77, 'English (United Kingdom)', 'en', 'en-GB', NULL, NULL),
(78, 'English (United States)', 'en', 'en-US', NULL, NULL),
(79, 'English (Zimbabwe)', 'en', 'en-ZW', NULL, NULL),
(80, 'Estonian (Estonia)', 'et', 'et-EE', NULL, NULL),
(81, 'Faroese (Faroe Islands)', 'fo', 'fo-FO', NULL, NULL),
(82, 'Filipino (Philippines)', 'fi', 'fil-PH', NULL, NULL),
(83, 'Finnish (Finland)', 'fi', 'fi-FI', NULL, NULL),
(84, 'French (Belgium)', 'fr', 'fr-BE', NULL, NULL),
(85, 'French (Côte d’Ivoire)', 'fr', 'fr-CI', NULL, NULL),
(86, 'French (Cameroon)', 'fr', 'fr-CM', NULL, NULL),
(87, 'French (Canada)', 'fr', 'fr-CA', NULL, NULL),
(88, 'French (Caribbean)', 'fr', 'fr-029', NULL, NULL),
(89, 'French (Congo, DRC)', 'fr', 'fr-CD', NULL, NULL),
(90, 'French (France)', 'fr', 'fr-FR', NULL, NULL),
(91, 'French (Haiti)', 'fr', 'fr-HT', NULL, NULL),
(92, 'French (Luxembourg)', 'fr', 'fr-LU', NULL, NULL),
(93, 'French (Mali)', 'fr', 'fr-ML', NULL, NULL),
(94, 'French (Morocco)', 'fr', 'fr-MA', NULL, NULL),
(95, 'French (Principality of Monaco)', 'fr', 'fr-MC', NULL, NULL),
(96, 'French (Réunion)', 'fr', 'fr-RE', NULL, NULL),
(97, 'French (Senegal)', 'fr', 'fr-SN', NULL, NULL),
(98, 'French (Switzerland)', 'fr', 'fr-CH', NULL, NULL),
(99, 'Frisian (Netherlands)', 'fy', 'fy-NL', NULL, NULL),
(100, 'Fulah (Latin)', 'ff', 'ff-Latn', NULL, NULL),
(101, 'Fulah (Latin, Nigeria)', 'ff', 'ff-Latn-NG', NULL, NULL),
(102, 'Fulah (Latin, Senegal)', 'ff', 'ff-Latn-SN', NULL, NULL),
(103, 'Galician (Spain)', 'gl', 'gl-ES', NULL, NULL),
(104, 'Georgian (Georgia)', 'ka', 'ka-GE', NULL, NULL),
(105, 'German (Austria)', 'de', 'de-AT', NULL, NULL),
(106, 'German (Germany)', 'de', 'de-DE', NULL, NULL),
(107, 'German (Liechtenstein)', 'de', 'de-LI', NULL, NULL),
(108, 'German (Luxembourg)', 'de', 'de-LU', NULL, NULL),
(109, 'German (Switzerland)', 'de', 'de-CH', NULL, NULL),
(110, 'Greek', 'el', 'el-GR', NULL, NULL),
(111, 'Greenlandic (Greenland)', 'kl', 'kl-GL', NULL, NULL),
(112, 'Guarani (Paraguay)', 'gn', 'gn-PY', NULL, NULL),
(113, 'Gujarati', 'gu', 'gu-IN', NULL, NULL),
(114, 'Hausa (Latin)', 'ha', 'ha-Latn', NULL, NULL),
(115, 'Hausa (Latin, Nigeria)', 'ha', 'ha-Latn-NG', NULL, NULL),
(116, 'Hawaiian (United States)', 'haw', 'haw-US', NULL, NULL),
(117, 'Hebrew (Israel)', 'he', 'he-IL', NULL, NULL),
(118, 'Hindi', 'hi', 'hi-IN', NULL, NULL),
(119, 'Hungarian (Hungary)', 'hu', 'hu-HU', NULL, NULL),
(120, 'Icelandic (Iceland)', 'is', 'is-IS', NULL, NULL),
(121, 'Igbo (Nigeria)', 'ig', 'ig-NG', NULL, NULL),
(122, 'Indonesian (Indonesia)', 'id', 'id-ID', NULL, NULL),
(123, 'Inuktitut (Latin)', 'iu', 'iu-Latn', NULL, NULL),
(124, 'Inuktitut (Latin, Canada)', 'iu', 'iu-Latn-CA', NULL, NULL),
(125, 'Inuktitut (Syllabics)', 'iu', 'iu-Cans', NULL, NULL),
(126, 'Inuktitut (Syllabics, Canada)', 'iu', 'iu-Cans-CA', NULL, NULL),
(127, 'Irish (Ireland)', 'ga', 'ga-IE', NULL, NULL),
(128, 'Italian (Italy)', 'it', 'it-IT', NULL, NULL),
(129, 'Italian (Switzerland)', 'it', 'it-CH', NULL, NULL),
(130, 'Japanese', 'ja', 'ja-JP', NULL, NULL),
(131, 'Kannada (India)', 'kn', 'kn-IN', NULL, NULL),
(132, 'Kanuri (Latin, Nigeria)', 'kr', 'kr-Latn-NG', NULL, NULL),
(133, 'Kashmiri (Devanagari, India)', 'ks', 'ks-Deva-IN', NULL, NULL),
(134, 'Kashmiri (Perso-Arabic)', 'ks', 'ks-Arab', NULL, NULL),
(135, 'Kazakh (Kazakhstan)', 'kk', 'kk-KZ', NULL, NULL),
(136, 'Khmer (Cambodia)', 'km', 'km-KH', NULL, NULL),
(137, 'K\'iche (Guatemala)', 'qut', 'qut-GT', NULL, NULL),
(138, 'K\'iche (Latin, Guatemala)', 'quc', 'quc-Latn-GT', NULL, NULL),
(139, 'Kinyarwanda (Rwanda)', 'rw', 'rw-RW', NULL, NULL),
(140, 'Kiswahili (Kenya)', 'sw', 'sw-KE', NULL, NULL),
(141, 'Konkani (India)', 'kok', 'kok-IN', NULL, NULL),
(142, 'Korean (Korea)', 'ko', 'ko-KR', NULL, NULL),
(143, 'Kyrgyz (Kyrgyzstan)', 'ky', 'ky-KG', NULL, NULL),
(144, 'Lao (Lao P.D.R.)', 'lo', 'lo-LA', NULL, NULL),
(145, 'Latin (Vatican City)', 'la', 'la-VA', NULL, NULL),
(146, 'Latvian (Latvia)', 'lv', 'lv-LV', NULL, NULL),
(147, 'Lithuanian (Lithuania)', 'lt', 'lt-LT', NULL, NULL),
(148, 'Lower Sorbian (Germany)', 'dsb', 'dsb-DE', NULL, NULL),
(149, 'Luxembourgish (Luxembourg)', 'lb', 'lb-LU', NULL, NULL),
(150, 'Macedonian (North Macedonia)', 'mk', 'mk-MK', NULL, NULL),
(151, 'Malay (Brunei Darussalam)', 'ms', 'ms-BN', NULL, NULL),
(152, 'Malay (Malaysia)', 'ms', 'ms-MY', NULL, NULL),
(153, 'Malayalam (India)', 'ml', 'ml-IN', NULL, NULL),
(154, 'Maltese (Malta)', 'mt', 'mt-MT', NULL, NULL),
(155, 'Maori (New Zealand)', 'mi', 'mi-NZ', NULL, NULL),
(156, 'Mapudungun (Chile)', 'arn', 'arn-CL', NULL, NULL),
(157, 'Marathi (India)', 'mr', 'mr-IN', NULL, NULL),
(158, 'Mohawk (Canada)', 'moh', 'moh-CA', NULL, NULL),
(159, 'Mongolian (Cyrillic)', 'mn', 'mn-Cyrl', NULL, NULL),
(160, 'Mongolian (Cyrillic, Mongolia)', 'mn', 'mn-MN', NULL, NULL),
(161, 'Mongolian (Traditional Mongolian)', 'mn', 'mn-Mong', NULL, NULL),
(162, 'Mongolian (Traditional Mongolian, Mongolia)', 'mn', 'mn-Mong-MN', NULL, NULL),
(163, 'Mongolian (Traditional Mongolian, People\'s Republic of China)', 'mn', 'mn-Mong-CN', NULL, NULL),
(164, 'Nepali (India)', 'ne', 'ne-IN', NULL, NULL),
(165, 'Nepali (Nepal)', 'ne', 'ne-NP', NULL, NULL),
(166, 'Norwegian, Bokmål (Norway)', 'nb', 'nb-NO', NULL, NULL),
(167, 'Norwegian, Nynorsk (Norway)', 'nn', 'nn-NO', NULL, NULL),
(168, 'Occitan (France)', 'oc', 'oc-FR', NULL, NULL),
(169, 'Odia (India)', 'or', 'or-IN', NULL, NULL),
(170, 'Oromo (Ethiopia)', 'om', 'om-ET', NULL, NULL),
(171, 'Pashto (Afghanistan)', 'ps', 'ps-AF', NULL, NULL),
(172, 'Persian (Iran)', 'fa', 'fa-IR', NULL, NULL),
(173, 'Polish (Poland)', 'pl', 'pl-PL', NULL, NULL),
(174, 'Portuguese (Brazil)', 'pt', 'pt-BR', NULL, NULL),
(175, 'Portuguese (Portugal)', 'pt', 'pt-PT', NULL, NULL),
(176, 'Punjabi - Arab', 'pa', 'pa-Arab', NULL, NULL),
(177, 'Punjabi (India)', 'pa', 'pa-IN', NULL, NULL),
(178, 'Punjabi (Islamic Republic of Pakistan)', 'pa', 'pa-Arab-PK', NULL, NULL),
(179, 'Quechua (Bolivia)', 'quz', 'quz-BO', NULL, NULL),
(180, 'Quechua (Ecuador)', 'quz', 'quz-EC', NULL, NULL),
(181, 'Quechua (Peru)', 'quz', 'quz-PE', NULL, NULL),
(182, 'Romanian (Moldova)', 'ro', 'ro-MD', NULL, NULL),
(183, 'Romanian (Romania)', 'ro', 'ro-RO', NULL, NULL),
(184, 'Romansh (Switzerland)', 'rm', 'rm-CH', NULL, NULL),
(185, 'Russian (Moldova)', 'ru', 'ru-MD', NULL, NULL),
(186, 'Russian (Russia)', 'ru', 'ru-RU', NULL, NULL),
(187, 'Sakha (Russia)', 'sah', 'sah-RU', NULL, NULL),
(188, 'Sami, Inari (Finland)', 'smn', 'smn-FI', NULL, NULL),
(189, 'Sami, Lule (Norway)', 'smj', 'smj-NO', NULL, NULL),
(190, 'Sami, Lule (Sweden)', 'smj', 'smj-SE', NULL, NULL),
(191, 'Sami, Northern (Finland)', 'se', 'se-FI', NULL, NULL),
(192, 'Sami, Northern (Norway)', 'se', 'se-NO', NULL, NULL),
(193, 'Sami, Northern (Sweden)', 'se', 'se-SE', NULL, NULL),
(194, 'Sami, Skolt (Finland)', 'sm', 'sms-FI', NULL, NULL),
(195, 'Sami, Southern (Norway)', 'sm', 'sma-NO', NULL, NULL),
(196, 'Sami, Southern (Sweden)', 'sm', 'sma-SE', NULL, NULL),
(197, 'Sanskrit (India)', 'sa', 'sa-IN', NULL, NULL),
(198, 'Scottish Gaelic (United Kingdom)', 'gd', 'gd-GB', NULL, NULL),
(199, 'Serbian (Cyrillic)', 'sr', 'sr-Cyrl', NULL, NULL),
(200, 'Serbian (Cyrillic, Bosnia and Herzegovina)', 'sr', 'sr-Cyrl-BA', NULL, NULL),
(201, 'Serbian (Cyrillic, Montenegro)', 'sr', 'sr-Cyrl-ME', NULL, NULL),
(202, 'Serbian (Cyrillic, Serbia and Montenegro (Former))', 'sr', 'sr-Cyrl-CS', NULL, NULL),
(203, 'Serbian (Cyrillic, Serbia)', 'sr', 'sr-Cyrl-RS', NULL, NULL),
(204, 'Serbian (Latin)', 'sr', 'sr-Latn', NULL, NULL),
(205, 'Serbian (Latin, Bosnia and Herzegovina)', 'sr', 'sr-Latn-BA', NULL, NULL),
(206, 'Serbian (Latin, Montenegro)', 'sr', 'sr-Latn-ME', NULL, NULL),
(207, 'Serbian (Latin, Serbia and Montenegro (Former))', 'sr', 'sr-Latn-CS', NULL, NULL),
(208, 'Serbian (Latin, Serbia)', 'sr', 'sr-Latn-RS', NULL, NULL),
(209, 'Sesotho sa Leboa (South Africa)', 'nso', 'nso-ZA', NULL, NULL),
(210, 'Setswana (Botswana)', 'tn', 'tn-BW', NULL, NULL),
(211, 'Setswana (South Africa)', 'tn', 'tn-ZA', NULL, NULL),
(212, 'Sindhi', 'sd', 'sd-Arab', NULL, NULL),
(213, 'Sindhi (Islamic Republic of Pakistan)', 'sd', 'sd-Arab-PK', NULL, NULL),
(214, 'Sinhala (Sri Lanka)', 'si', 'si-LK', NULL, NULL),
(215, 'Slovak (Slovakia)', 'sk', 'sk-SK', NULL, NULL),
(216, 'Slovenian (Slovenia)', 'sl', 'sl-SI', NULL, NULL),
(217, 'Somali (Somalia)', 'so', 'so-SO', NULL, NULL),
(218, 'Sotho (South Africa)', 'st', 'st-ZA', NULL, NULL),
(219, 'Spanish (Argentina)', 'es', 'es-AR', NULL, NULL),
(220, 'Spanish (Bolivarian Republic of Venezuela)', 'es', 'es-VE', NULL, NULL),
(221, 'Spanish (Bolivia)', 'es', 'es-BO', NULL, NULL),
(222, 'Spanish (Chile)', 'es', 'es-CL', NULL, NULL),
(223, 'Spanish (Colombia)', 'es', 'es-CO', NULL, NULL),
(224, 'Spanish (Costa Rica)', 'es', 'es-CR', NULL, NULL),
(225, 'Spanish (Cuba)', 'es', 'es-CU', NULL, NULL),
(226, 'Spanish (Dominican Republic)', 'es', 'es-DO', NULL, NULL),
(227, 'Spanish (Ecuador)', 'es', 'es-EC', NULL, NULL),
(228, 'Spanish (El Salvador)', 'es', 'es-SV', NULL, NULL),
(229, 'Spanish (Guatemala)', 'es', 'es-GT', NULL, NULL),
(230, 'Spanish (Honduras)', 'es', 'es-HN', NULL, NULL),
(231, 'Spanish (Latin America)', 'es', 'es-419', NULL, NULL),
(232, 'Spanish (Mexico)', 'es', 'es-MX', NULL, NULL),
(233, 'Spanish (Nicaragua)', 'es', 'es-NI', NULL, NULL),
(234, 'Spanish (Panama)', 'es', 'es-PA', NULL, NULL),
(235, 'Spanish (Paraguay)', 'es', 'es-PY', NULL, NULL),
(236, 'Spanish (Peru)', 'es', 'es-PE', NULL, NULL),
(237, 'Spanish (Puerto Rico)', 'es', 'es-PR', NULL, NULL),
(238, 'Spanish (Spain, International Sort)', 'es', 'es-ES', NULL, NULL),
(239, 'Spanish (Traditional Sort)', 'es', 'es-ES', NULL, NULL),
(240, 'Spanish (United States)', 'es', 'es-US', NULL, NULL),
(241, 'Spanish (Uruguay)', 'es', 'es-UY', NULL, NULL),
(242, 'Swedish (Finland)', 'sv', 'sv-FI', NULL, NULL),
(243, 'Swedish (Sweden)', 'sv', 'sv-SE', NULL, NULL),
(244, 'Syriac (Syria)', 'syr', 'syr-SY', NULL, NULL),
(245, 'Tajik (Cyrillic)', 'tg', 'tg-Cyrl', NULL, NULL),
(246, 'Tajik (Cyrillic, Tajikistan)', 'tg', 'tg-Cyrl-TJ', NULL, NULL),
(247, 'Tamazight (Latin)', 'tzm', 'tzm-Latn', NULL, NULL),
(248, 'Tamazight (Latin, Algeria)', 'tzm', 'tzm-Latn-DZ', NULL, NULL),
(249, 'Tamil (India)', 'ta', 'ta-IN', NULL, NULL),
(250, 'Tamil (Sri Lanka)', 'ta', 'ta-LK', NULL, NULL),
(251, 'Tatar (Russia)', 'tt', 'tt-RU', NULL, NULL),
(252, 'Telugu (India)', 'te', 'te-IN', NULL, NULL),
(253, 'Thai (Thailand)', 'th', 'th-TH', NULL, NULL),
(254, 'Tibetan (People\'s Republic of China)', 'bo', 'bo-CN', NULL, NULL),
(255, 'Tigrinya (Eritrea)', 'ti', 'ti-ER', NULL, NULL),
(256, 'Tigrinya (Ethiopia)', 'ti', 'ti-ET', NULL, NULL),
(257, 'Tsonga (South Africa)', 'ts', 'ts-ZA', NULL, NULL),
(258, 'Turkish (Turkey)', 'tr', 'tr-TR', NULL, NULL),
(259, 'Turkmen (Turkmenistan)', 'tk', 'tk-TM', NULL, NULL),
(260, 'Ukrainian (Ukraine)', 'uk', 'uk-UA', NULL, NULL),
(261, 'Upper Sorbian (Germany)', 'hsb', 'hsb-DE', NULL, NULL),
(262, 'Urdu (India)', 'ur', 'ur-IN', NULL, NULL),
(263, 'Urdu (Islamic Republic of Pakistan)', 'ur', 'ur-PK', NULL, NULL),
(264, 'Uyghur (People\'s Republic of China)', 'ug', 'ug-CN', NULL, NULL),
(265, 'Uzbek (Cyrillic)', 'uz', 'uz-Cyrl', NULL, NULL),
(266, 'Uzbek (Cyrillic, Uzbekistan)', 'uz', 'uz-Cyrl-UZ', NULL, NULL),
(267, 'Uzbek (Latin)', 'uz', 'uz-Latn', NULL, NULL),
(268, 'Uzbek (Latin, Uzbekistan)', 'uz', 'uz-Latn-UZ', NULL, NULL),
(269, 'Valencian (Spain)', 'ca', 'ca-ES-valencia', NULL, NULL),
(270, 'Venda (South Africa)', 've', 've-ZA', NULL, NULL),
(271, 'Vietnamese (Vietnam)', 'vi', 'vi-VN', NULL, NULL),
(272, 'Welsh (United Kingdom)', 'cy', 'cy-GB', NULL, NULL),
(273, 'Wolof (Senegal)', 'wo', 'wo-SN', NULL, NULL),
(274, 'Xhosa (South Africa)', 'xh', 'xh-ZA', NULL, NULL),
(275, 'Yi (People\'s Republic of China)', 'ii', 'ii-CN', NULL, NULL),
(276, 'Yiddish (World)', 'yi', 'yi-001', NULL, NULL),
(277, 'Yoruba (Nigeria)', 'yo', 'yo-NG', NULL, NULL),
(278, 'Zulu (South Africa)', 'zu', 'zu-ZA', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `language_lists`
--

CREATE TABLE `language_lists` (
  `id` bigint UNSIGNED NOT NULL,
  `language_id` bigint UNSIGNED DEFAULT NULL,
  `language_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language_flag` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_rtl` tinyint DEFAULT '0',
  `is_default` tinyint DEFAULT '0' COMMENT '0-no, 1-yes',
  `status` tinyint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `language_version_details`
--

CREATE TABLE `language_version_details` (
  `id` bigint UNSIGNED NOT NULL,
  `default_language_id` bigint UNSIGNED DEFAULT NULL,
  `version_no` bigint UNSIGNED DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `language_version_details`
--

INSERT INTO `language_version_details` (`id`, `default_language_id`, `version_no`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, '2025-06-25 04:02:32', '2025-06-25 04:02:32');

-- --------------------------------------------------------

--
-- Table structure for table `language_with_keywords`
--

CREATE TABLE `language_with_keywords` (
  `id` bigint UNSIGNED NOT NULL,
  `language_id` bigint UNSIGNED DEFAULT NULL,
  `keyword_id` bigint UNSIGNED DEFAULT NULL,
  `screen_id` bigint UNSIGNED DEFAULT NULL,
  `keyword_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `levels`
--

CREATE TABLE `levels` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rate` bigint DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `collection_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conversions_disk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint UNSIGNED NOT NULL,
  `manipulations` json NOT NULL,
  `custom_properties` json NOT NULL,
  `generated_conversions` json NOT NULL,
  `responsive_images` json NOT NULL,
  `order_column` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2021_11_09_064224_create_user_profiles_table', 1),
(6, '2021_11_11_110731_create_permission_tables', 1),
(7, '2021_11_16_114009_create_media_table', 1),
(8, '2023_04_04_122206_create_equipment_table', 1),
(9, '2023_04_07_094031_create_workout_types_table', 1),
(10, '2023_04_07_114407_create_category_diets_table', 1),
(11, '2023_04_08_065211_create_diets_table', 1),
(12, '2023_04_12_051628_create_categories_table', 1),
(13, '2023_04_12_104633_create_levels_table', 1),
(14, '2023_04_13_092859_create_tags_table', 1),
(15, '2023_04_13_101848_create_app_settings_table', 1),
(16, '2023_04_13_124827_create_settings_table', 1),
(17, '2023_04_17_104726_create_body_parts_table', 1),
(18, '2023_04_17_111217_create_exercises_table', 1),
(19, '2023_04_17_115034_create_workouts_table', 1),
(20, '2023_04_21_052358_create_workout_days_table', 1),
(21, '2023_04_21_071141_create_workout_day_exercises_table', 1),
(22, '2023_04_22_042750_create_posts_table', 1),
(23, '2023_04_22_082012_create_user_favourite_diets_table', 1),
(24, '2023_05_01_105045_create_user_favourite_workouts_table', 1),
(25, '2023_05_05_062357_create_product_categories_table', 1),
(26, '2023_05_05_062432_create_products_table', 1),
(27, '2023_05_09_042923_create_assign_diets_table', 1),
(28, '2023_05_12_065812_create_assign_workouts_table', 1),
(29, '2023_07_08_063653_create_user_graphs_table', 1),
(30, '2023_08_18_101137_create_payment_gateways_table', 1),
(31, '2023_08_19_090449_create_notifications_table', 1),
(32, '2023_08_19_090739_create_push_notifications_table', 1),
(33, '2023_08_23_110759_create_packages_table', 1),
(34, '2023_08_26_043829_create_subscriptions_table', 1),
(35, '2023_10_14_065617_create_quotes_table', 1),
(36, '2024_06_06_101914_create_language_default_lists_table', 1),
(37, '2024_06_06_101955_create_language_lists_table', 1),
(38, '2024_06_06_102016_create_screens_table', 1),
(39, '2024_06_06_102106_create_default_keywords_table', 1),
(40, '2024_06_06_102158_create_language_with_keywords_table', 1),
(41, '2024_06_06_102229_create_language_version_details_table', 1),
(42, '2024_10_04_060234_create_chatgpt_fit_bots_table', 1),
(43, '2024_10_10_050832_create_class_schedules_table', 1),
(44, '2024_11_04_064110_create_class_schedule_plans_table', 1),
(45, '2024_12_26_054738_create_user_exercises_table', 1),
(46, '2024_12_28_092226_add_expires_at_to_personal_access_tokens_table', 1),
(47, '2025_03_12_064026_add_timezone_to_users_table', 1),
(48, '2025_03_12_092606_create_jobs_table', 1),
(49, '2025_03_12_103934_add_color_to_app_settings_table', 1),
(50, '2025_03_17_065611_add_slug_in_tables', 1),
(51, '2025_04_18_100800_create_game_score_data_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `duration` bigint UNSIGNED DEFAULT NULL,
  `price` double DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_gateways`
--

CREATE TABLE `payment_gateways` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1' COMMENT '0- InActive, 1- Active',
  `is_test` tinyint DEFAULT '1' COMMENT '0-  No, 1- Yes',
  `test_value` json DEFAULT NULL,
  `live_value` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `title`, `guard_name`, `parent_id`, `created_at`, `updated_at`) VALUES
(1, 'role', 'Role', 'web', NULL, '2023-10-11 01:42:50', '2023-10-11 01:42:50'),
(2, 'role-add', 'Role Add', 'web', 1, '2023-10-11 01:42:50', '2023-10-11 01:42:50'),
(3, 'role-list', 'Role List', 'web', 1, '2023-10-11 01:42:50', '2023-10-11 01:42:50'),
(4, 'permission', 'Permission', 'web', NULL, '2023-10-11 01:42:50', '2023-10-11 01:42:50'),
(5, 'permission-add', 'Permission Add', 'web', 4, '2023-10-11 01:42:50', '2023-10-11 01:42:50'),
(6, 'permission-list', 'Permission List', 'web', 4, '2023-10-11 01:42:50', '2023-10-11 01:42:50'),
(7, 'user', 'User', 'web', NULL, '2023-10-11 01:42:50', '2023-10-11 01:42:50'),
(8, 'user-list', 'User List', 'web', 7, '2023-10-11 01:42:50', '2023-10-11 01:42:50'),
(9, 'user-add', 'User Add', 'web', 7, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(10, 'user-edit', 'User Edit', 'web', 7, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(11, 'user-delete', 'User Delete', 'web', 7, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(12, 'user-show', 'User Show', 'web', 7, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(13, 'equipment', 'Equipment', 'web', NULL, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(14, 'equipment-list', 'Equipment List', 'web', 13, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(15, 'equipment-add', 'Equipment Add', 'web', 13, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(16, 'equipment-edit', 'Equipment Edit', 'web', 13, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(17, 'equipment-delete', 'Equipment Delete', 'web', 13, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(18, 'categorydiet', 'Categorydiet', 'web', NULL, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(19, 'categorydiet-list', 'Categorydiet List', 'web', 18, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(20, 'categorydiet-add', 'Categorydiet Add', 'web', 18, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(21, 'categorydiet-edit', 'Categorydiet Edit', 'web', 18, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(22, 'categorydiet-delete', 'Categorydiet Delete', 'web', 18, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(23, 'workouttype', 'Workouttype', 'web', NULL, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(24, 'workouttype-list', 'Workouttype List', 'web', 23, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(25, 'workouttype-add', 'Workouttype Add', 'web', 23, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(26, 'workouttype-edit', 'Workouttype Edit', 'web', 23, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(27, 'workouttype-delete', 'Workouttype Delete', 'web', 23, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(28, 'diet', 'Diet', 'web', NULL, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(29, 'diet-list', 'Diet List', 'web', 28, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(30, 'diet-add', 'Diet Add', 'web', 28, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(31, 'diet-edit', 'Diet Edit', 'web', 28, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(32, 'diet-delete', 'Diet Delete', 'web', 28, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(33, 'level', 'Level', 'web', NULL, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(34, 'level-list', 'Level List', 'web', 33, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(35, 'level-add', 'Level Add', 'web', 33, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(36, 'level-edit', 'Level Edit', 'web', 33, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(37, 'level-delete', 'Level Delete', 'web', 33, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(38, 'bodyparts', 'Bodyparts', 'web', NULL, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(39, 'bodyparts-list', 'Bodyparts List', 'web', 38, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(40, 'bodyparts-add', 'Bodyparts Add', 'web', 38, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(41, 'bodyparts-edit', 'Bodyparts Edit', 'web', 38, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(42, 'bodyparts-delete', 'Bodyparts Delete', 'web', 38, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(43, 'category', 'Category', 'web', NULL, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(44, 'category-list', 'Category List', 'web', 43, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(45, 'category-add', 'Category Add', 'web', 43, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(46, 'category-edit', 'Category Edit', 'web', 43, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(47, 'category-delete', 'Category Delete', 'web', 43, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(48, 'tags', 'Tags', 'web', NULL, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(49, 'tags-list', 'Tags List', 'web', 48, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(50, 'tags-add', 'Tags Add', 'web', 48, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(51, 'tags-edit', 'Tags Edit', 'web', 48, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(52, 'tags-delete', 'Tags Delete', 'web', 48, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(53, 'exercise', 'Exercise', 'web', NULL, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(54, 'exercise-list', 'Exercise List', 'web', 53, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(55, 'exercise-add', 'Exercise Add', 'web', 53, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(56, 'exercise-edit', 'Exercise Edit', 'web', 53, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(57, 'exercise-delete', 'Exercise Delete', 'web', 53, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(58, 'workout', 'Workout', 'web', NULL, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(59, 'workout-list', 'Workout List', 'web', 58, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(60, 'workout-add', 'Workout Add', 'web', 58, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(61, 'workout-edit', 'Workout Edit', 'web', 58, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(62, 'workout-delete', 'Workout Delete', 'web', 58, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(63, 'pages', 'Pages', 'web', NULL, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(64, 'terms-condition', 'Terms Condition', 'web', 63, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(65, 'privacy-policy', 'Privacy Policy', 'web', 63, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(66, 'post', 'Post', 'web', NULL, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(67, 'post-list', 'Post List', 'web', 66, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(68, 'post-add', 'Post Add', 'web', 66, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(69, 'post-edit', 'Post Edit', 'web', 66, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(70, 'post-delete', 'Post Delete', 'web', 66, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(71, 'productcategory', 'Productcategory', 'web', NULL, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(72, 'productcategory-list', 'Productcategory List', 'web', 71, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(73, 'productcategory-add', 'Productcategory Add', 'web', 71, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(74, 'productcategory-edit', 'Productcategory Edit', 'web', 71, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(75, 'productcategory-delete', 'Productcategory Delete', 'web', 71, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(76, 'product', 'Product', 'web', NULL, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(77, 'product-list', 'Product List', 'web', 76, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(78, 'product-add', 'Product Add', 'web', 76, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(79, 'product-edit', 'Product Edit', 'web', 76, '2023-10-11 01:42:51', '2023-10-11 01:42:51'),
(80, 'product-delete', 'Product Delete', 'web', 76, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(81, 'package', 'Package', 'web', NULL, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(82, 'package-list', 'Package List', 'web', 81, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(83, 'package-add', 'Package Add', 'web', 81, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(84, 'package-edit', 'Package Edit', 'web', 81, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(85, 'package-delete', 'Package Delete', 'web', 81, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(86, 'pushnotification', 'Pushnotification', 'web', NULL, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(87, 'pushnotification-list', 'Pushnotification List', 'web', 86, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(88, 'pushnotification-add', 'Pushnotification Add', 'web', 86, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(89, 'pushnotification-delete', 'Pushnotification Delete', 'web', 86, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(90, 'subscription', 'Subscription', 'web', NULL, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(91, 'subscription-list', 'Subscription List', 'web', 90, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(92, 'quotes', 'Quotes', 'web', NULL, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(93, 'quotes-list', 'Quotes List', 'web', 92, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(94, 'quotes-add', 'Quotes Add', 'web', 92, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(95, 'quotes-edit', 'Quotes Edit', 'web', 92, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(96, 'quotes-delete', 'Quotes Delete', 'web', 92, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(97, 'subscription-add', 'Subscription Add', 'web', 90, '2024-02-26 01:42:52', '2024-02-26 01:42:52'),
(98, 'screen', 'screen', 'web', NULL, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(99, 'screen-list', 'Screen List', 'web', 98, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(100, 'defaultkeyword', 'Defaultkeyword', 'web', NULL, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(101, 'defaultkeyword-list', 'Defaultkeyword List', 'web', 100, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(102, 'defaultkeyword-add', 'Defaultkeyword Add', 'web', 100, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(103, 'defaultkeyword-edit', 'Defaultkeyword Edit', 'web', 100, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(104, 'languagelist', 'Languagelist', 'web', NULL, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(105, 'languagelist-list', 'Languagelist List', 'web', 104, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(106, 'languagelist-add', 'Languagelist Add', 'web', 104, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(107, 'languagelist-edit', 'Languagelist Edit', 'web', 104, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(108, 'languagelist-delete', 'Languagelist Delete', 'web', 104, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(109, 'languagewithkeyword', 'Language With Keyword', 'web', NULL, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(110, 'languagewithkeyword-list', 'Language With Keyword List', 'web', 109, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(111, 'languagewithkeyword-edit', 'Language With Keyword Edit', 'web', 109, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(112, 'bulkimport', 'Bulkimport', 'web', NULL, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(113, 'bulkimport-add', 'Bulkimport Add', 'web', 112, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(114, 'classschedule', 'Class Schedule', 'web', NULL, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(115, 'classschedule-list', 'Class Schedule List', 'web', 114, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(116, 'classschedule-add', 'Class Schedule Add', 'web', 114, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(117, 'classschedule-edit', 'Class Schedule Edit', 'web', 114, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(118, 'classschedule-delete', 'Class Schedule Delete', 'web', 114, '2024-12-26 02:56:07', '2024-12-26 02:56:07'),
(119, 'subadmin', 'Sub Admin', 'web', NULL, '2024-12-26 02:56:08', '2024-12-26 02:56:08'),
(120, 'subadmin-list', 'Sub Admin List', 'web', 119, '2024-12-26 02:56:08', '2024-12-26 02:56:08'),
(121, 'subadmin-add', 'Sub Admin Add', 'web', 119, '2024-12-26 02:56:08', '2024-12-26 02:56:08'),
(122, 'subadmin-edit', 'Sub Admin Edit', 'web', 119, '2024-12-26 02:56:08', '2024-12-26 02:56:08'),
(123, 'subadmin-delete', 'Sub Admin Delete', 'web', 119, '2024-12-26 02:56:08', '2024-12-26 02:56:08'),
(124, 'page-list', 'Page List', 'web', '63', '2025-03-25 12:47:18', '2025-03-25 12:47:18'),
(125, 'page-add', 'Page Add', 'web', '63', '2025-03-25 12:47:18', '2025-03-25 12:47:18'),
(126, 'page-edit', 'Page Edit', 'web', '63', '2025-03-25 12:47:18', '2025-03-25 12:47:18'),
(127, 'page-delete', 'Page Delete', 'web', '63', '2025-03-25 12:47:18', '2025-03-25 12:47:18'),
(128, 'websitesection', 'Website Section', 'web', NULL, '2025-03-25 12:47:18', '2025-03-25 12:47:18'),
(129, 'website-section-list', 'Website Section List', 'web', '128', '2025-03-25 12:47:18', '2025-03-25 12:47:18');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tags_id` text COLLATE utf8mb4_unicode_ci,
  `category_ids` text COLLATE utf8mb4_unicode_ci,
  `datetime` datetime DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `is_featured` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `affiliate_link` text COLLATE utf8mb4_unicode_ci,
  `price` double DEFAULT NULL,
  `productcategory_id` bigint UNSIGNED DEFAULT NULL,
  `featured` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `push_notifications`
--

CREATE TABLE `push_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quotes`
--

CREATE TABLE `quotes` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` longtext COLLATE utf8mb4_unicode_ci,
  `date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `title`, `guard_name`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'Admin', 'web', 1, '2023-10-11 01:42:52', '2023-10-11 01:42:52'),
(2, 'user', 'User', 'web', 1, '2023-10-11 01:42:53', '2023-10-11 01:42:53');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1),
(81, 1),
(82, 1),
(83, 1),
(84, 1),
(85, 1),
(86, 1),
(87, 1),
(88, 1),
(89, 1),
(90, 1),
(91, 1),
(92, 1),
(93, 1),
(94, 1),
(95, 1),
(96, 1),
(97, 1),
(98, 1),
(99, 1),
(100, 1),
(101, 1),
(102, 1),
(103, 1),
(104, 1),
(105, 1),
(106, 1),
(107, 1),
(108, 1),
(109, 1),
(110, 1),
(111, 1),
(112, 1),
(113, 1),
(114, 1),
(115, 1),
(116, 1),
(117, 1),
(118, 1),
(119, 1),
(120, 1),
(121, 1),
(122, 1),
(123, 1),
(124, 1),
(125, 1),
(126, 1),
(127, 1),
(129, 1);

-- --------------------------------------------------------

--
-- Table structure for table `screens`
--

CREATE TABLE `screens` (
  `id` bigint UNSIGNED NOT NULL,
  `screenId` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `screenName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `screens`
--

INSERT INTO `screens` (`id`, `screenId`, `screenName`, `created_at`, `updated_at`) VALUES
(1, '1', 'WalkThroughScreen', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(2, '2', 'SignInScreen', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(3, '3', 'SignUpStep1Component', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(4, '4', 'SignUpStep2Component', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(5, '5', 'SignUpStep3Component', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(6, '6', 'SignUpStep4Component', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(7, '7', 'DashboardScreen', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(8, '8', 'HomeScreen', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(9, '9', 'EditProfileScreen', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(10, '10', 'NotificationScreen', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(11, '11', 'SearchScreen', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(12, '12', 'SubscribeScreen', '2025-06-25 04:02:30', '2025-06-25 04:02:30'),
(13, '13', 'ExerciseDetailScreen', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(14, '14', 'PaymentScreen', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(15, '15', 'FilterWorkoutScreen', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(16, '16', 'DietScreen', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(17, '17', 'ProductScreen', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(18, '18', 'ProgressScreen', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(19, '19', 'ProfileScreen', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(20, '20', 'SettingScreen', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(21, '21', 'ChattingImageScreen', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(22, '22', 'OTPScreen', '2025-06-25 04:02:31', '2025-06-25 04:02:31'),
(23, '23', 'ScheduleScreen', '2025-06-25 04:02:31', '2025-06-25 04:02:31');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint UNSIGNED NOT NULL,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8mb4_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `package_id` bigint UNSIGNED DEFAULT NULL,
  `total_amount` double DEFAULT '0',
  `payment_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'cash',
  `txn_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transaction_detail` json DEFAULT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'pending' COMMENT 'pending, paid, failed',
  `subscription_start_date` datetime DEFAULT NULL,
  `subscription_end_date` datetime DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'active, inactive',
  `package_data` json DEFAULT NULL,
  `callback` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `user_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `login_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `player_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_subscribe` tinyint DEFAULT '0',
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'UTC',
  `last_notification_seen` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `first_name`, `last_name`, `email`, `phone_number`, `email_verified_at`, `user_type`, `password`, `status`, `login_type`, `gender`, `display_name`, `player_id`, `is_subscribe`, `timezone`, `last_notification_seen`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'systemadmin', 'System', 'Admin', 'admin@admin.com', NULL, '2023-10-11 01:42:53', 'admin', '$2y$10$cCKqmGW.uFrliw8aYY0DpOhGYe4y1VSz8hRQ7c9R5t2sgVtCH.o9.', 'active', NULL, NULL, 'System Admin', NULL, 0, 'UTC', NULL, NULL, '2023-10-11 01:42:53', '2023-10-11 01:42:53');

-- --------------------------------------------------------

--
-- Table structure for table `user_exercises`
--

CREATE TABLE `user_exercises` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `exercise_id` bigint UNSIGNED DEFAULT NULL,
  `workout_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_favourite_diets`
--

CREATE TABLE `user_favourite_diets` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `diet_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_favourite_workouts`
--

CREATE TABLE `user_favourite_workouts` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `workout_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_graphs`
--

CREATE TABLE `user_graphs` (
  `id` bigint UNSIGNED NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date` date DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `age` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weight_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workouts`
--

CREATE TABLE `workouts` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `level_id` bigint UNSIGNED DEFAULT NULL,
  `workout_type_id` bigint UNSIGNED DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `is_premium` tinyint(1) DEFAULT '0' COMMENT '0-free, 1-premium',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workout_days`
--

CREATE TABLE `workout_days` (
  `id` bigint UNSIGNED NOT NULL,
  `workout_id` bigint UNSIGNED DEFAULT NULL,
  `sequence` bigint DEFAULT NULL,
  `is_rest` tinyint(1) DEFAULT '0' COMMENT '0-no,1-yes',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workout_day_exercises`
--

CREATE TABLE `workout_day_exercises` (
  `id` bigint UNSIGNED NOT NULL,
  `workout_id` bigint UNSIGNED DEFAULT NULL,
  `workout_day_id` bigint UNSIGNED DEFAULT NULL,
  `exercise_id` bigint UNSIGNED DEFAULT NULL,
  `sets` json DEFAULT NULL,
  `sequence` bigint UNSIGNED DEFAULT NULL,
  `duration` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workout_types`
--

CREATE TABLE `workout_types` (
  `id` bigint UNSIGNED NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_settings`
--
ALTER TABLE `app_settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assign_diets`
--
ALTER TABLE `assign_diets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `assign_workouts`
--
ALTER TABLE `assign_workouts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `body_parts`
--
ALTER TABLE `body_parts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_diets`
--
ALTER TABLE `category_diets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chatgpt_fit_bots`
--
ALTER TABLE `chatgpt_fit_bots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chatgpt_fit_bots_user_id_foreign` (`user_id`);

--
-- Indexes for table `class_schedules`
--
ALTER TABLE `class_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_schedule_plans`
--
ALTER TABLE `class_schedule_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_schedule_plans_user_id_foreign` (`user_id`);

--
-- Indexes for table `default_keywords`
--
ALTER TABLE `default_keywords`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `diets`
--
ALTER TABLE `diets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `diets_categorydiet_id_foreign` (`categorydiet_id`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exercises`
--
ALTER TABLE `exercises`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `game_score_data`
--
ALTER TABLE `game_score_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_score_data_user_id_foreign` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `language_default_lists`
--
ALTER TABLE `language_default_lists`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `language_lists`
--
ALTER TABLE `language_lists`
  ADD PRIMARY KEY (`id`),
  ADD KEY `language_lists_language_id_foreign` (`language_id`);

--
-- Indexes for table `language_version_details`
--
ALTER TABLE `language_version_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `language_with_keywords`
--
ALTER TABLE `language_with_keywords`
  ADD PRIMARY KEY (`id`),
  ADD KEY `language_with_keywords_language_id_foreign` (`language_id`);

--
-- Indexes for table `levels`
--
ALTER TABLE `levels`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_uuid_unique` (`uuid`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_productcategory_id_foreign` (`productcategory_id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `push_notifications`
--
ALTER TABLE `push_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quotes`
--
ALTER TABLE `quotes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `screens`
--
ALTER TABLE `screens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subscriptions_user_id_foreign` (`user_id`),
  ADD KEY `subscriptions_package_id_foreign` (`package_id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_exercises`
--
ALTER TABLE `user_exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_exercises_user_id_foreign` (`user_id`),
  ADD KEY `user_exercises_exercise_id_foreign` (`exercise_id`),
  ADD KEY `user_exercises_workout_id_foreign` (`workout_id`);

--
-- Indexes for table `user_favourite_diets`
--
ALTER TABLE `user_favourite_diets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_favourite_diets_user_id_foreign` (`user_id`),
  ADD KEY `user_favourite_diets_diet_id_foreign` (`diet_id`);

--
-- Indexes for table `user_favourite_workouts`
--
ALTER TABLE `user_favourite_workouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_favourite_workouts_user_id_foreign` (`user_id`),
  ADD KEY `user_favourite_workouts_workout_id_foreign` (`workout_id`);

--
-- Indexes for table `user_graphs`
--
ALTER TABLE `user_graphs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `workouts`
--
ALTER TABLE `workouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workouts_level_id_foreign` (`level_id`),
  ADD KEY `workouts_workout_type_id_foreign` (`workout_type_id`);

--
-- Indexes for table `workout_days`
--
ALTER TABLE `workout_days`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workout_days_workout_id_foreign` (`workout_id`);

--
-- Indexes for table `workout_day_exercises`
--
ALTER TABLE `workout_day_exercises`
  ADD PRIMARY KEY (`id`),
  ADD KEY `workout_day_exercises_workout_id_foreign` (`workout_id`),
  ADD KEY `workout_day_exercises_workout_day_id_foreign` (`workout_day_id`);

--
-- Indexes for table `workout_types`
--
ALTER TABLE `workout_types`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `app_settings`
--
ALTER TABLE `app_settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `assign_diets`
--
ALTER TABLE `assign_diets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `assign_workouts`
--
ALTER TABLE `assign_workouts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `body_parts`
--
ALTER TABLE `body_parts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category_diets`
--
ALTER TABLE `category_diets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chatgpt_fit_bots`
--
ALTER TABLE `chatgpt_fit_bots`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_schedules`
--
ALTER TABLE `class_schedules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_schedule_plans`
--
ALTER TABLE `class_schedule_plans`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `default_keywords`
--
ALTER TABLE `default_keywords`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=229;

--
-- AUTO_INCREMENT for table `diets`
--
ALTER TABLE `diets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exercises`
--
ALTER TABLE `exercises`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `game_score_data`
--
ALTER TABLE `game_score_data`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `language_default_lists`
--
ALTER TABLE `language_default_lists`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=279;

--
-- AUTO_INCREMENT for table `language_lists`
--
ALTER TABLE `language_lists`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `language_version_details`
--
ALTER TABLE `language_version_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `language_with_keywords`
--
ALTER TABLE `language_with_keywords`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `levels`
--
ALTER TABLE `levels`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_gateways`
--
ALTER TABLE `payment_gateways`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `push_notifications`
--
ALTER TABLE `push_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quotes`
--
ALTER TABLE `quotes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `screens`
--
ALTER TABLE `screens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_exercises`
--
ALTER TABLE `user_exercises`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_favourite_diets`
--
ALTER TABLE `user_favourite_diets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_favourite_workouts`
--
ALTER TABLE `user_favourite_workouts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_graphs`
--
ALTER TABLE `user_graphs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workouts`
--
ALTER TABLE `workouts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workout_days`
--
ALTER TABLE `workout_days`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workout_day_exercises`
--
ALTER TABLE `workout_day_exercises`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workout_types`
--
ALTER TABLE `workout_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `chatgpt_fit_bots`
--
ALTER TABLE `chatgpt_fit_bots`
  ADD CONSTRAINT `chatgpt_fit_bots_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_schedule_plans`
--
ALTER TABLE `class_schedule_plans`
  ADD CONSTRAINT `class_schedule_plans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `diets`
--
ALTER TABLE `diets`
  ADD CONSTRAINT `diets_categorydiet_id_foreign` FOREIGN KEY (`categorydiet_id`) REFERENCES `category_diets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_score_data`
--
ALTER TABLE `game_score_data`
  ADD CONSTRAINT `game_score_data_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `language_lists`
--
ALTER TABLE `language_lists`
  ADD CONSTRAINT `language_lists_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `language_default_lists` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `language_with_keywords`
--
ALTER TABLE `language_with_keywords`
  ADD CONSTRAINT `language_with_keywords_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `language_lists` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_productcategory_id_foreign` FOREIGN KEY (`productcategory_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `subscriptions_package_id_foreign` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subscriptions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_exercises`
--
ALTER TABLE `user_exercises`
  ADD CONSTRAINT `user_exercises_exercise_id_foreign` FOREIGN KEY (`exercise_id`) REFERENCES `exercises` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_exercises_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_exercises_workout_id_foreign` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_favourite_diets`
--
ALTER TABLE `user_favourite_diets`
  ADD CONSTRAINT `user_favourite_diets_diet_id_foreign` FOREIGN KEY (`diet_id`) REFERENCES `diets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_favourite_diets_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_favourite_workouts`
--
ALTER TABLE `user_favourite_workouts`
  ADD CONSTRAINT `user_favourite_workouts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_favourite_workouts_workout_id_foreign` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workouts`
--
ALTER TABLE `workouts`
  ADD CONSTRAINT `workouts_level_id_foreign` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workouts_workout_type_id_foreign` FOREIGN KEY (`workout_type_id`) REFERENCES `workout_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_days`
--
ALTER TABLE `workout_days`
  ADD CONSTRAINT `workout_days_workout_id_foreign` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `workout_day_exercises`
--
ALTER TABLE `workout_day_exercises`
  ADD CONSTRAINT `workout_day_exercises_workout_day_id_foreign` FOREIGN KEY (`workout_day_id`) REFERENCES `workout_days` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `workout_day_exercises_workout_id_foreign` FOREIGN KEY (`workout_id`) REFERENCES `workouts` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
