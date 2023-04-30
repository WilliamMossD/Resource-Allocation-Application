# Resource Allocation Appliction (TALL)

A PHP web application for my dissertation project at the University of Sussex.
## Introduction

This project was developed for the Engineering & Informatics department at the University of Sussex providing an online web application to simplify and automate the process of allocating teaching assistants to sessions.

This is accomplished through the included admin interface allowing the manipulation of resources stored on the database such as timesheets, modules, sessions and session allocation. Additionally, TALL provides a user interface allowing users, non-admins, to view the sessions they have been assigned to and submit timesheets for work they have completed. 

## Dependencies

This project uses composer as a dependency manager for PHP and uses the following:
- microsoft/microsoft-graph **v1.93+**
- vlucas/phpdotenv **v5.5+**
- thenetworg/oauth2-azure **v2.1+**

## Requirements

- PHP **8.1+**
- MariaDB **v10.5.19+**

Note: This application uses Azure AD for the Single Sign-On functionality. Therefore you will need to register an application in the Azure portal and create a secret key.

## Installation

#### Web Server Files
These instructions will guide you on how to clone the repository into the correct place and create the .env file.
1. Clone the GitHub repository onto the web server.
2.  Move the contents of the \public folder into the servers \public_html folder. Leave the rest of the contents outside of the public_html folder.

3. Create a new directory named `config` outside of the public_html folder.
	
	 Your file directory should look like:
	 
		├── config
		├── test
		│   ├── assets
		│   │   ├── css
		│   │   ├── favicon
		│   │   ├── images
		│   │   └── js
		├── src
		└── vendor
4.  Now create a file named `.env` within the `\config` directory.

5.  Add the following code to the `.env` file and fill in the fields.

		# Database Details
		HOST = '{DATABASE_HOST}'
		USER = '{DATABASE_USERNAME}'
		PASSWORD = '{DATABASE_PASSWORD}'
		DATABASE = '{DATABASE_NAME}'
		
		# Azure AD Enviroment Variables
		CLIENT_ID = '{CLIENT_ID}'
		CLIENT_SECRET = '{CLIENT_SECRET}'
		REDIRECT_URI = '{REDIRECT_URI}'
		TENANT_ID = '{TENANT_ID}'
		LOGOUT_URL = '{LOGOUT_URL}'
		
		# Server Domain Name
		DOMAIN_NAME = '{DOMAIN_NAME}'


#### MySQL Database
These instructions will guide you in setting up the MySQL database.

1.  Run the following script to generate the table objects.

		SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
		START TRANSACTION;
		SET time_zone = "+00:00";

		/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
		/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
		/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
		/*!40101 SET NAMES utf8mb4 */;

		--
		-- Database: `{DATABASE_NAME}`

		-- --------------------------------------------------------
		
		--
		-- Table structure for table `assigned_to`
		--

		CREATE TABLE `assigned_to` (
		`ta_num` int(11) NOT NULL,
		`module_session_num` int(11) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

		-- --------------------------------------------------------
		--
		-- Table structure for table `modules`
		--
		CREATE TABLE `modules` (
		`module_num` int(11) NOT NULL,
		`module_name` varchar(50) NOT NULL,
		`module_convenor` varchar(50) NOT NULL,
		`module_description` varchar(100) DEFAULT NULL,
		`link` varchar(2048) DEFAULT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
		
		-- --------------------------------------------------------
		
		--
		-- Table structure for table `module_sessions`
		--
		CREATE TABLE `module_sessions` (
		`module_session_num` int(11) NOT NULL,
		`module_num` int(11) NOT NULL,
		`num_of_ta` int(11) NOT NULL,
		`session_day` varchar(9) NOT NULL,
		`session_start` time(5) NOT NULL,
		`session_end` time(5) NOT NULL,
		`session_type` varchar(8) NOT NULL,
		`session_location` varchar(50) NOT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

		-- --------------------------------------------------------

		--
		-- Table structure for table `teaching_assistants`
		--

		CREATE TABLE `teaching_assistants` (
		`ta_num` int(11) NOT NULL,
		`fname` varchar(50) NOT NULL,
		`lname` varchar(50) NOT NULL,
		`email` varchar(320) NOT NULL,
		`password` varchar(255) DEFAULT NULL,
		`admin` tinyint(1) NOT NULL DEFAULT 0
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

		-- --------------------------------------------------------

		--
		-- Table structure for table `timesheets`
		--

		CREATE TABLE `timesheets` (
		`timesheet_num` int(11) NOT NULL,
		`ta_num` int(11) NOT NULL,
		`week_num` int(11) NOT NULL,
		`year` year(4) NOT NULL,
		`monStart` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
		`monEnd` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
		`tueStart` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
		`tueEnd` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
		`wedStart` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
		`wedEnd` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
		`thuStart` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
		`thuEnd` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
		`friStart` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
		`friEnd` char(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
		`total` varchar(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
		`comments` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
		`status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
		`submit_datetime` timestamp NOT NULL DEFAULT current_timestamp(),
		`update_datetime` timestamp NULL DEFAULT NULL
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

		--
		-- Indexes for table `assigned_to`
		--
		ALTER TABLE `assigned_to`
		ADD PRIMARY KEY (`ta_num`,`module_session_num`),
		ADD KEY `module_session_num` (`module_session_num`);

		--
		-- Indexes for table `modules`
		--
		ALTER TABLE `modules`
		ADD PRIMARY KEY (`module_num`);

		--
		-- Indexes for table `module_sessions`
		--
		ALTER TABLE `module_sessions`
		ADD PRIMARY KEY (`module_session_num`),
		ADD KEY `module_num` (`module_num`);

		--
		-- Indexes for table `teaching_assistants`
		--
		ALTER TABLE `teaching_assistants`
		ADD PRIMARY KEY (`ta_num`);

		--
		-- Indexes for table `timesheets`
		--
		ALTER TABLE `timesheets`
		ADD PRIMARY KEY (`timesheet_num`),
		ADD KEY `ta_num` (`ta_num`);

		--
		-- AUTO_INCREMENT for table `modules`
		--
		ALTER TABLE `modules`
		MODIFY `module_num` int(11) NOT NULL AUTO_INCREMENT;

		--
		-- AUTO_INCREMENT for table `module_sessions`
		--
		ALTER TABLE `module_sessions`
		MODIFY `module_session_num` int(11) NOT NULL AUTO_INCREMENT;

		--
		-- AUTO_INCREMENT for table `teaching_assistants`
		--
		ALTER TABLE `teaching_assistants`
		MODIFY `ta_num` int(11) NOT NULL AUTO_INCREMENT;

		--
		-- AUTO_INCREMENT for table `timesheets`
		--
		ALTER TABLE `timesheets`
		MODIFY `timesheet_num` int(11) NOT NULL AUTO_INCREMENT;

		--
		-- Constraints for table `assigned_to`
		--
		ALTER TABLE `assigned_to`
		ADD CONSTRAINT `assigned_to_ibfk_1` FOREIGN KEY (`module_session_num`) REFERENCES `module_sessions` (`module_session_num`) ON DELETE CASCADE ON UPDATE CASCADE,
		ADD CONSTRAINT `assigned_to_ibfk_2` FOREIGN KEY (`ta_num`) REFERENCES `teaching_assistants` (`ta_num`) ON DELETE CASCADE ON UPDATE CASCADE;

		--
		-- Constraints for table `module_sessions`
		--
		ALTER TABLE `module_sessions`
		ADD CONSTRAINT `module_sessions_ibfk_1` FOREIGN KEY (`module_num`) REFERENCES `modules` (`module_num`) ON DELETE CASCADE ON UPDATE CASCADE;

		--
		-- Constraints for table `timesheets`
		--
		ALTER TABLE `timesheets`
		ADD CONSTRAINT `timesheets_ibfk_1` FOREIGN KEY (`ta_num`) REFERENCES `teaching_assistants` (`ta_num`);
		COMMIT;

		/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
		/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
		/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

2.  Fill in the fields and run this script to create an admin account. The email address for the admin must be in the azure tenant and authorized to access the application.

		INSERT INTO teaching_assistants (fname, lname, email, admin) VALUES ('{First Name}', '{Last Name}', '{Email Address}', '1')
		

