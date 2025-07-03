-- 1. Tabel MEMBER
CREATE TABLE IF NOT EXISTS `member` (
  `member_id` int NOT NULL AUTO_INCREMENT,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `address` varchar(100) NOT NULL,
  `contact` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`member_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 2. Tabel BORROW
CREATE TABLE IF NOT EXISTS `borrow` (
  `borrow_id` int NOT NULL AUTO_INCREMENT,
  `member_id` int NOT NULL,
  `date_borrow` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `status` int DEFAULT '1',
  PRIMARY KEY (`borrow_id`),
  KEY `fk123` (`member_id`),
  CONSTRAINT `fk123` FOREIGN KEY (`member_id`) REFERENCES `member` (`member_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 3. Tabel BOOK
CREATE TABLE IF NOT EXISTS `book` (
  `book_id` int NOT NULL AUTO_INCREMENT,
  `book_title` varchar(100) NOT NULL,
  `category` varchar(100) NOT NULL,
  `author` varchar(50) NOT NULL,
  `book_copies` int NOT NULL,
  `publisher_name` varchar(100) NOT NULL,
  `isbn` varchar(50) NOT NULL,
  `copyright_year` int NOT NULL,
  `status` varchar(30) NOT NULL DEFAULT '1',
  PRIMARY KEY (`book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- 4. Tabel BORROWDETAILS
CREATE TABLE IF NOT EXISTS `borrowdetails` (
  `borrow_details_id` int NOT NULL AUTO_INCREMENT,
  `book_id` int NOT NULL,
  `borrow_id` int NOT NULL,
  `borrow_status` int DEFAULT '1',
  `date_return` date DEFAULT NULL,
  PRIMARY KEY (`borrow_details_id`),
  KEY `book_id` (`book_id`),
  KEY `borrow_id` (`borrow_id`),
  CONSTRAINT `borrowdetails_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `book` (`book_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `borrowdetails_ibfk_2` FOREIGN KEY (`borrow_id`) REFERENCES `borrow` (`borrow_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
