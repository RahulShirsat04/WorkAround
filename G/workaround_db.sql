-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 27, 2025 at 05:36 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `workaround_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `employer_profiles`
--

CREATE TABLE `employer_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `company_name` varchar(100) NOT NULL,
  `contact_person` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `company_description` text DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `industry` varchar(100) DEFAULT NULL,
  `logo_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employer_profiles`
--

INSERT INTO `employer_profiles` (`id`, `user_id`, `company_name`, `contact_person`, `phone`, `address`, `company_description`, `website`, `industry`, `logo_path`) VALUES
(1, 1, 'Ayurveda Medical Store', 'Pradeep Joshi', '8054548725', 'kankavli', '\"Your Health, Our Priority.\"', '', 'Medical', 'uploads/logos/logo_67cfc79f575c8.png'),
(2, 4, 'The Coaching Academy', 'Shreya Bhattacharya', '9674854152', 'kankavli', 'We believe in unlocking the full potential of every student. Our expert instructors, personalized approach, and comprehensive study materials ensure that students receive the best preparation possible for their academic challenges.', 'https://thecoacingclass.com', 'coaching or tutoring', 'uploads/logos/logo_67cfc987b13ac.png'),
(3, 7, 'Skyline Apartment', 'Neel Thorat', '7452361496', 'kankavli', '\"Welcome to Skyline Apartment, where comfort meets modern living.\"', 'https://skyline.com', 'Construction', 'uploads/logos/logo_67cfcba2cd009.png'),
(4, 8, 'Kanekar Kirana Store', 'Sachin Kanekar', '7485356925', 'sdcv', 'Welcome to Kanekar Kirana Store, your one-stop shop for all your daily needs. Conveniently located in the heart of Kankvli, we offer a wide range of essential products, from groceries and household items to snacks, beverages, and personal care essentials.', 'https://example.com', 'Sore', 'uploads/logos/logo_67cfcc2033d7e.jpg'),
(5, 10, 'Sanskriti Stays', 'sanskriti sawant', '7577859565', 'A/p: janavli tal.Kankavli dist:Sindhudurg', '\"Atithi Devo Bhava\"', 'https://sanskritistays.com', 'Hotel', 'uploads/logos/logo_67cfe722cfa0d.jpg'),
(6, 12, 'DTDC', 'Suraj Bansal', '8010657545', NULL, 'DTDC is India’s leading integrated express logistics provider, operating the largest network of customer access points in the country. Our technology-driven logistics solutions cater to a wide range of customers across diverse industry verticals, making us a trusted partner in delivering excellence.', NULL, NULL, NULL),
(7, 15, 'Flavorscape', 'Shyam Singh', '7585954565', 'oros', '\"Where Every Bite is a New Discovery.\"', 'https://www.FlavorscapeDining.com', 'Food Service and Hospitality', 'uploads/logos/logo_67de4287a569d.jpg'),
(8, 17, 'MedKart', 'Mithun', '7485757687', NULL, '\"Trusted Medicine, Trusted Care.\"', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `employer_id` int(11) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `requirements` text DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `salary_range` varchar(50) DEFAULT NULL,
  `job_type` enum('part-time','temporary','contract') NOT NULL,
  `status` enum('open','closed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `employer_id`, `title`, `description`, `requirements`, `location`, `salary_range`, `job_type`, `status`, `created_at`) VALUES
(1, 1, 'Medical Assistant', '1) Attend customers.\r\n2) Give proper medicines as per doctors prescription.\r\n3) dont give medicines without doctors prescription.\r\n4) Work of 5/hr per day\r\n5 Time : 4 to 9 PM', 'H.S.C passed', 'kankavli bazarpeth', '5000-6000/- per Month', 'part-time', 'closed', '2025-03-08 05:36:59'),
(2, 4, 'Math Teacher', 'We are seeking a passionate and dedicated Math Teacher to join our team for a part-time position. The ideal candidate will be responsible for teaching 8th and 9th-grade students, helping them develop a strong foundation in mathematics, and guiding them towards academic success. This position requires 2-3 hours of teaching each day, Monday to Saturday.', 'Bachelor\'s degree in Mathematics or related field (preferred).\r\nPrevious teaching experience, especially with middle and high school students (preferred).\r\nStrong communication skills and a passion for teaching.\r\nAbility to explain complex mathematical concepts in an easy-to-understand manner.\r\nPatience, dedication, and a student-focused approach.', 'kudal', '5000-7000/- month', 'part-time', 'open', '2025-03-10 03:55:28'),
(3, 4, 'Science Teacher', 'We are seeking a dedicated and enthusiastic Science Teacher to join our team on a temporary basis for one week. The ideal candidate will be responsible for delivering engaging and comprehensive science lessons to students in 5th-8th standard, fostering a love for science and encouraging scientific thinking.', 'Bachelor\'s degree in Science or a related field.\r\nRelevant teaching certification or license.\r\nExperience teaching science to middle school students.\r\nStrong communication, interpersonal, and problem-solving skills.', 'kudal', '2000 Rs.', 'temporary', 'open', '2025-03-10 04:27:48'),
(4, 7, 'Security Guard', 'We are looking for a dedicated and reliable Night Security Guard to ensure the safety and security of Skyline Apartment during overnight hours. As a Night Security Guard, you will be responsible for monitoring the property, preventing unauthorized access, and responding to security concerns to ensure a peaceful and secure environment for our residents.', 'Previous security or law enforcement experience is a plus, especially in residential or property management settings.\r\nAbility to work overnight shifts, including weekends and holidays.\r\nStrong communication and interpersonal skills for interacting with residents and visitors.\r\nAbility to stay calm in emergency situations and make effective decisions.\r\nBasic knowledge of security systems (CCTV, alarm systems) and procedures.', 'Sawantwadi', '8000-10000/- per month', 'part-time', 'open', '2025-03-11 04:05:12'),
(5, 8, 'Worker', 'We are hiring a Part-Time Worker for our Kirana store. This is a great opportunity for individuals looking to work in a fast-paced environment and become a valuable part of a local community business. The ideal candidate will assist customers with their daily shopping needs and help ensure the smooth operation of the store.\r\n1)Working Hours: 3:00 PM to 8:00 PM, Monday to Saturday', '1)16-35 years (preferred, but all applicants are welcome).\r\n2)Minimum 10th\r\n3)Basic mathematical skills for handling cash and card transactions.Friendly, polite, and approachable demeanor to interact with customers.\r\n4)Ability to stand for long periods and carry light stock', 'A/P : Kankavli, Bazarpeth', '2500-3500/- per month', 'part-time', 'open', '2025-03-11 04:19:58'),
(6, 10, 'Waiter', 'We are seeking a friendly and professional Waiter to join our team on a contract basis. The ideal candidate should have a passion for hospitality, excellent customer service skills, and the ability to work efficiently in a fast-paced environment.\r\n1)Contract Duration: 1 year', 'Experience: Minimum 1 year of experience as a waiter in a restaurant/hotel. (Freshers with a great attitude are welcome!)\r\nEducation: High school diploma or equivalent preferred.\r\nSkills:\r\nStrong communication and interpersonal skills.\r\nBasic knowledge of food and beverages.\r\nGood customer service skills and a positive attitude.\r\nAvailability: Must be available to work on weekends and holidays as per hotel requirements.', 'A/p: Janvli tal:Kankavli dist : Sindhudurg', '10000/- month', 'contract', 'open', '2025-03-11 07:17:44'),
(7, 10, 'Housekeeping Staff', 'We are looking for part-time Housekeeping Staff to join our team and help maintain high cleanliness standards in our hotel. The ideal candidate should be detail-oriented, hardworking, and committed to providing a comfortable and hygienic environment for our guests.\r\nWork Hours: 4-6 hours per day', 'Experience: Previous housekeeping or cleaning experience preferred, but not required. Training will be provided.\r\n🔹 Education: No formal education required. Basic reading and understanding of instructions are helpful.\r\n🔹 Skills & Abilities:\r\nAttention to detail and cleanliness.\r\nAbility to work independently and in a team.\r\nGood time management to complete tasks efficiently.\r\n🔹 Availability: Must be available for flexible shifts, including weekends and holidays.', 'A/p: Janvli tal:Kankavli dist : Sindhudurg', '3000-4000/-month', 'part-time', 'open', '2025-03-11 07:21:38'),
(8, 10, 'Temporary Receptionist', 'We are looking for a Temporary Receptionist to manage front desk operations and provide excellent customer service to our guests. This role is ideal for someone with good communication skills, a welcoming personality, and the ability to handle administrative tasks efficiently.\r\nWork Hours: 6-8 hours per day', '🔹 Experience: Previous experience in front desk, reception, or hospitality is preferred but not mandatory.\r\n🔹 Education: High school diploma or equivalent; a hospitality management degree is a plus.\r\n🔹 Skills & Abilities:\r\nStrong verbal and written communication skills.\r\nCustomer service-oriented with a friendly attitude.\r\nBasic computer skills (MS Office, hotel booking software knowledge is a plus).', 'A/p: Janvli tal:Kankavli dist : Sindhudurg', '8000₹', 'temporary', 'open', '2025-03-11 07:25:31'),
(9, 10, 'Bartender', 'We are looking for a Part-Time Bartender to join our team and provide guests with a great bar experience. The ideal candidate should have good knowledge of cocktails, excellent customer service skills, and the ability to work in a fast-paced environment.\r\nWork Hours: Evening Shift, 4-6 hours per day', '🔹 Experience: Previous experience as a bartender preferred, but not mandatory. Training can be provided.\r\n🔹 Education: High school diploma or equivalent; bartending certification is a plus.\r\n🔹 Skills & Abilities:\r\nKnowledge of cocktails, wines, and spirits.\r\nExcellent communication and customer service skills.\r\nAbility to multitask and work under pressure.\r\nBasic knowledge of POS systems for billing.\r\n🔹 Availability: Must be available for evening shifts, weekends, and holidays.', 'A/p: Janvli tal:Kankavli dist : Sindhudurg', '5000-7000/- month', 'part-time', 'open', '2025-03-11 07:28:26'),
(10, 12, 'Courier Boy', 'We are looking for a reliable and energetic Part-Time Courier Boy to join our team. The ideal candidate will be responsible for picking up and delivering packages, documents, or goods in a timely and safe manner. This role requires punctuality, good navigation skills, and a customer-friendly attitude.', 'Minimum age: 18 years old (varies by location).\r\nA valid driver\'s license and a clean driving record (for bike/scooter/motorcycle/car delivery).\r\nAbility to ride a bicycle, scooter, or motorbike (depending on delivery mode).\r\nGood physical stamina to handle multiple deliveries.\r\nKnowledge of local routes and navigation apps (Google Maps, Waze, etc.).\r\nBasic communication skills in the local language.\r\nResponsible, punctual, and customer-friendly attitude.\r\n(Local city boys is plus)', 'A/p:Kankavli,Bazarpeth', '6000/-', 'part-time', 'open', '2025-03-12 04:36:54'),
(11, 15, 'Waiter', 'Join our dynamic team at Flavorscape, where every meal is a journey of flavors! As a part-time waiter, you’ll be an integral part of creating a memorable dining experience for our guests. You’ll be responsible for delivering excellent customer service, taking orders, serving food and beverages, and ensuring our guests leave with a smile.', 'Previous experience in food service or hospitality is preferred, but not required.\r\nExcellent communication and interpersonal skills.\r\nAbility to work in a fast-paced environment and handle multiple tasks.\r\nA positive, friendly, and customer-focused attitude.\r\nStrong attention to detail and reliability.', 'A/p: Oros, near post office', '5000-7000/month', 'part-time', 'open', '2025-03-18 03:53:52'),
(13, 15, 'Event Coordinator', 'As a Temporary Event Coordinator, you will be responsible for organizing and managing special events, private parties, and gatherings at Flavorscape. This role involves overseeing logistics, coordinating with vendors, and ensuring everything runs smoothly from start to finish.\r\nContract Duration: Temporary (could range from a few days to a few months, depending on the events schedule).', 'Strong organizational skills and attention to detail.\r\nExperience in event planning or hospitality preferred.\r\nExcellent communication and problem-solving abilities.\r\nAbility to work under pressure and manage multiple tasks.\r\nAvailable for flexible hours during special events.', 'oros', '5000/Event', 'contract', 'open', '2025-03-18 04:03:15'),
(14, 15, 'Social Media Manager', 'As a Temporary Social Media Manager, you will be responsible for managing Flavorscape’s social media presence. You will create and schedule engaging content, interact with customers online, and monitor the performance of our social media campaigns.\r\nContract Duration: Temporary (3-6 months depending on marketing campaigns).', 'Experience managing social media accounts for brands or businesses.\r\nStrong writing, graphic design, and content creation skills.\r\nFamiliarity with social media analytics and tools.\r\nCreativity and a passion for food and dining.\r\nAbility to work independently and meet deadlines.', 'oros', '5000/month', 'temporary', 'open', '2025-03-18 04:18:10'),
(15, 15, 'Contract-Based Food Photographer/Content Creator', 'As a Contract-Based Food Photographer, you will create high-quality images and videos of Flavorscape’s dishes, drinks, and events for use in marketing materials, on social media, and the website. Your work will help showcase the beauty and creativity of the restaurant’s menu.\r\nContract Duration: Contract-based (project length can vary, typically a few weeks to a couple of months).', 'Previous experience in food photography or content creation.\r\nA strong portfolio showcasing your food photography skills.\r\nProficiency in photography equipment and editing software.\r\nCreative eye and attention to detail.\r\nAvailability for project-based work.', 'oros', '5000-6000', 'part-time', 'open', '2025-03-18 04:20:34'),
(16, 17, 'Store Cleaner', 'We are seeking a diligent and reliable Store Cleaner to maintain the cleanliness and hygiene of our medical store. The successful candidate will be responsible for ensuring a safe, clean, and organized environment for both customers and staff. While the position is open to all, we encourage female candidates to apply as we value diversity in our workforce.', 'Dependable and punctual.\r\nAbility to work independently with minimal supervision.\r\nA strong sense of responsibility and respect for store property.\r\nPositive attitude and a good work ethic.', 'kankavli , vidyanagar', '2500/month', 'part-time', 'open', '2025-03-25 04:52:41'),
(17, 17, 'Pharmacy Assistant', 'We are looking for a Pharmacy Assistant to join our medical store team. This part-time role is perfect for someone interested in healthcare and pharmacy operations. The Pharmacy Assistant will work closely with our licensed pharmacists to provide excellent customer service, assist with the dispensing of medications, manage stock, and help with other pharmacy-related tasks. Female candidates are encouraged to apply, but all applications are welcome.', 'Education: A high school diploma or equivalent. Additional courses or certifications in pharmacy assistance are a plus.\r\n\r\nExperience: Previous experience in a pharmacy or healthcare setting is preferred but not required.\r\n\r\nKnowledge of Medications: Basic understanding of common medications and their uses is preferred.\r\n\r\nCommunication Skills: Strong customer service skills with the ability to explain medication usage and provide helpful information to customers.', 'kankavli , vidyanagar', '5000/month', 'part-time', 'open', '2025-03-25 04:54:04'),
(18, 17, 'Delivery Person', 'We are looking for a reliable and punctual Delivery Person to join our medical store team. The Delivery Person will be responsible for delivering medications and health-related products to customers within a designated area. This role is essential in ensuring our customers receive their products on time, especially for those who are elderly or unable to visit the store. Female candidates are encouraged to apply, but all are welcome.', 'Education: High school diploma or equivalent.\r\nExperience: Previous delivery experience is preferred, especially in a retail or medical setting, but not essential.\r\nDriver\'s License: A valid driver\'s license is required (for two-wheeler or four-wheeler, depending on delivery vehicle).\r\nGood Knowledge of Local Area: Familiarity with the delivery area is a plus.\r\nCustomer Service Skills: Ability to interact politely and professionally with customers.', 'kankavli , vidyanagar', '4000-5000/month', 'part-time', 'open', '2025-03-25 04:58:36');

-- --------------------------------------------------------

--
-- Table structure for table `jobseeker_profiles`
--

CREATE TABLE `jobseeker_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `education` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobseeker_profiles`
--

INSERT INTO `jobseeker_profiles` (`id`, `user_id`, `first_name`, `last_name`, `phone`, `address`, `skills`, `education`, `experience`, `resume_path`, `profile_picture`) VALUES
(2, 3, 'Sarvesh', 'Gosavi', '8245759565', 'kankavli', 'software development', '12th pass', '3 years', 'uploads/resumes/67cbe3fc0e903.pdf', 'uploads/profile_pictures/67cbe3fc0e6f4.jpeg'),
(3, 5, 'Aarav', 'Sharma', '7755968425', 'kudal', '', '', '', 'uploads/resumes/67ce6bbec0542.pdf', ''),
(4, 6, 'Aditya', 'Patel', '7745859635', '', '', '', '', 'uploads/resumes/67ce6d5a564e7.docx', ''),
(5, 9, 'Omkar', 'More', '8574362596', 'kankavli,parabwadi', '', '', '', '', 'uploads/profile_pictures/67e23c908094e.jpg'),
(6, 11, 'Tara', 'Sharma', '1234657896', '', '', '', '', 'uploads/resumes/67cfea25d15f9.pdf', 'uploads/profile_pictures/67de32fa1f2d4.jpg'),
(8, 14, 'Jay', 'Chindarkar', '8574968585', '', '', '', '', 'uploads/resumes/67d254c6885f9.docx', ''),
(9, 16, 'Ram', 'Sawant', '9028547684', 'A/p :Oros, gavkarwadi', '', '', '', 'uploads/resumes/67e0e0a3f3c6d.docx', 'uploads/profile_pictures/67e0e0a3f399c.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `id` int(11) NOT NULL,
  `job_id` int(11) DEFAULT NULL,
  `jobseeker_id` int(11) DEFAULT NULL,
  `status` enum('pending','reviewed','accepted','rejected') DEFAULT 'pending',
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `cover_letter` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_applications`
--

INSERT INTO `job_applications` (`id`, `job_id`, `jobseeker_id`, `status`, `application_date`, `cover_letter`, `updated_at`) VALUES
(1, 1, 3, 'accepted', '2025-03-08 06:30:33', NULL, '2025-03-10 05:59:52'),
(2, 2, 5, 'pending', '2025-03-10 04:34:15', NULL, '2025-03-10 05:59:30'),
(3, 1, 6, 'accepted', '2025-03-10 04:41:06', NULL, '2025-03-10 05:59:35'),
(4, 9, 5, 'rejected', '2025-03-11 07:34:20', 'Hii', '2025-03-11 07:35:55'),
(5, 9, 3, 'accepted', '2025-03-11 07:38:36', '', '2025-03-11 07:39:12'),
(6, 6, 5, 'accepted', '2025-03-11 07:43:14', '', '2025-03-11 07:43:49'),
(7, 8, 11, 'rejected', '2025-03-11 07:46:22', '', '2025-03-11 07:46:51'),
(9, 9, 14, 'pending', '2025-03-13 03:45:34', '', '2025-03-13 03:45:34'),
(12, 11, 16, 'pending', '2025-03-24 04:33:57', '', '2025-03-24 04:33:57');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `sent_at`, `is_read`) VALUES
(1, 3, 1, 'Hello, I have applied for the position of Medical Assistant. \r\n                   I am interested in this opportunity and would love to discuss it further.', '2025-03-08 06:30:33', 1),
(2, 3, 1, 'hiii', '2025-03-08 06:32:26', 1),
(3, 5, 4, 'Hello, I have applied for the position of Math Teacher. \r\n                   I am interested in this opportunity and would love to discuss it further.', '2025-03-10 04:34:15', 0),
(4, 6, 1, 'Hello, I have applied for the position of Medical Assistant. \r\n                   I am interested in this opportunity and would love to discuss it further.', '2025-03-10 04:41:06', 1),
(5, 6, 1, 'Thank you so much!!!! for giving opportunity', '2025-03-10 06:41:33', 1),
(6, 5, 10, 'Hello, I have applied for the position of Bartender. \r\n                   I am interested in this opportunity and would love to discuss it further.', '2025-03-11 07:34:20', 1),
(7, 10, 5, 'sorry you are not eligiblefor this job', '2025-03-11 07:36:43', 1),
(8, 3, 10, 'Hello, I have applied for the position of Bartender. \r\n                   I am interested in this opportunity and would love to discuss it further.', '2025-03-11 07:38:36', 1),
(9, 10, 3, 'You are selected for the Job', '2025-03-11 07:42:27', 1),
(10, 5, 10, 'Hello, I have applied for the position of Waiter. \r\n                   I am interested in this opportunity and would love to discuss it further.', '2025-03-11 07:43:14', 1),
(11, 11, 10, 'Hello, I have applied for the position of Temporary Receptionist. \r\n                   I am interested in this opportunity and would love to discuss it further.', '2025-03-11 07:46:22', 1),
(12, 10, 11, 'sorry', '2025-03-11 07:47:10', 1),
(15, 14, 10, 'Hello, I have applied for the position of Bartender. \r\n                   I am interested in this opportunity and would love to discuss it further.', '2025-03-13 03:45:34', 1),
(18, 16, 15, 'Hello, I have applied for the position of Waiter. \r\n                   I am interested in this opportunity and would love to discuss it further.', '2025-03-24 04:33:57', 1),
(19, 15, 16, 'hiii', '2025-03-26 05:30:08', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `related_id` int(11) DEFAULT NULL,
  `related_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `type`, `is_read`, `created_at`, `related_id`, `related_type`) VALUES
(1, 5, 'Your application for Waiter has been accepted', 'application_status', 0, '2025-03-11 07:43:49', 6, NULL),
(2, 11, 'Your application for Temporary Receptionist has been rejected', 'application_status', 0, '2025-03-11 07:46:51', 7, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('jobseeker','employer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `user_type`, `created_at`) VALUES
(1, 'ayurveda@gmail.com', '$2y$10$LN.NTWifMBdTZOSR7WvPkuJwD8DXUp6wFkXhgqDis3.yxKrLxMKS6', 'employer', '2025-03-08 05:26:01'),
(3, 'sarvesh@gmail.com', '$2y$10$zYm9nCku66f47BMjWXALlOlVPudiqPnmnJEhMt87/q9DqqUliXCnK', 'jobseeker', '2025-03-08 06:11:16'),
(4, 'thecoachingacademy@gmail.com', '$2y$10$gB7tYYenkuFKC.P8GlHg1.oavU/K5LsDY0NZgVh2lwbTad2KfjA/2', 'employer', '2025-03-10 03:47:45'),
(5, 'aarav@gmail.com', '$2y$10$G8tbTL3mCVSVWJDOffTEMexfBT9B1bV1VOvkuHPO08rhguzgDiEHe', 'jobseeker', '2025-03-10 04:30:14'),
(6, 'aditya@gmail.com', '$2y$10$1mJ4J0kBDIR4hBRJoANqGummRPtiM4pfaG6CtnIW75UcNH/ON/5aK', 'jobseeker', '2025-03-10 04:39:24'),
(7, 'skyline@gmail.com', '$2y$10$EfpgpH2wJFyQ8gg1iiT3f.W95Mckqf4yBx2DHUe0iesxy8O0WV92u', 'employer', '2025-03-11 04:01:34'),
(8, 'kanekar@gmail.com', '$2y$10$rgX/jmBAfgB1sEh0DAPwL.TmPMB/CeaEuTk4g0F5yIzlc.6L1ZHB2', 'employer', '2025-03-11 04:11:09'),
(9, 'omkar@gmail.com', '$2y$10$f/K7UCExeo5rOKWL0hqATO8VHlTMpVAjIHqKgu4L2plcNnuKOr17a', 'jobseeker', '2025-03-11 04:27:37'),
(10, 'sanskriti@gmail.com', '$2y$10$zCMFO5E6G8nNhZfXq.mcduyCyNeTE1WZS6DM7YDs5o7N8iP2PxQA.', 'employer', '2025-03-11 07:11:45'),
(11, 'tara@gmail.com', '$2y$10$9E88fkUft6WxDK8hNqrFouafPkdIXjuLr4QGPh.2VRjbA6G/Kq/hy', 'jobseeker', '2025-03-11 07:45:04'),
(12, 'customersupport@dtdc.com', '$2y$10$KQcVtKvdazLz8Ra2Q/pimOt4iqMe7E4Dc2S9ErelXJ6etYTXOS42W', 'employer', '2025-03-12 04:31:26'),
(14, 'jay@gmail.com', '$2y$10$0N1Zq1eNndafr3r03MxuI.cysywBE998eLTFt/r5SJdR2mHDVgErW', 'jobseeker', '2025-03-13 03:44:15'),
(15, 'flavorscape@yahoo.com', '$2y$10$Fmf.Zxjq7qS8REcLEEpoPeJWafCot1D/CMpXsk8pswrZHbqiSmYMi', 'employer', '2025-03-18 03:49:24'),
(16, 'ram@gmail.com', '$2y$10$wwJB5yl6VtZt8hQOrKsG/eAiQ7tK/NDAMBtAFajJcmtNcgbozxQNu', 'jobseeker', '2025-03-24 04:30:08'),
(17, 'medkart@gmail.com', '$2y$10$uCWjBwmj3HufvoccPybPDeZEcmgfReJ6jSn1j2vDAYNcEEPjjmXBW', 'employer', '2025-03-25 04:47:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `employer_profiles`
--
ALTER TABLE `employer_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employer_id` (`employer_id`);

--
-- Indexes for table `jobseeker_profiles`
--
ALTER TABLE `jobseeker_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `jobseeker_id` (`jobseeker_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_messages_users` (`sender_id`,`receiver_id`),
  ADD KEY `idx_messages_read` (`receiver_id`,`is_read`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `employer_profiles`
--
ALTER TABLE `employer_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `jobseeker_profiles`
--
ALTER TABLE `jobseeker_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employer_profiles`
--
ALTER TABLE `employer_profiles`
  ADD CONSTRAINT `employer_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`employer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jobseeker_profiles`
--
ALTER TABLE `jobseeker_profiles`
  ADD CONSTRAINT `jobseeker_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_applications_ibfk_2` FOREIGN KEY (`jobseeker_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
