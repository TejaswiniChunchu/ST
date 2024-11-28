CREATE TABLE Majors (
    MajorID INT PRIMARY KEY,
    MajorName VARCHAR(255) NOT NULL,
    Department VARCHAR(255) NOT NULL
);



INSERT INTO Majors (MajorID, MajorName, Department) VALUES
(1, 'Software Engineering', 'Bachelor of Information & Communications Technology'),
(2, 'Data Management & Analytics', 'Bachelor of Information & Communications Technology'),
(3, 'Web and Mobile Development', 'Bachelor of Information & Communications Technology'),
(4, 'Business and Systems Analysis', 'Bachelor of Information & Communications Technology'),
(5, 'Systems Administration', 'Bachelor of Information & Communications Technology'),
(6, 'Network Engineering', 'Bachelor of Information & Communications Technology'),
(7, 'Security', 'Bachelor of Information & Communications Technology'),
(8, 'Project Management', 'Bachelor of Information & Communications Technology');

CREATE TABLE Subjects (
    SubjectID VARCHAR(50) PRIMARY KEY, 
    SubjectName VARCHAR(255) NOT NULL,
    Prerequisite VARCHAR(255), 
    StudyYear INT,              
    Sem VARCHAR(20),            
    CourseType VARCHAR(50),    
    EnrollmentStatus VARCHAR(20), 
    CreditHours INT NOT NULL,
    Description TEXT 
);



INSERT INTO Subjects (SubjectID, SubjectName, Prerequisite, StudyYear, Sem, CourseType, EnrollmentStatus, CreditHours, Description) VALUES
('D201', 'Advanced Programming', NULL, 2, '1', 'Major', 'Available', 105, 'Advanced concepts in programming.'),
('D202', 'Software Process', NULL, 2, '2', 'Major', 'Available', 105, 'Understanding software development processes.'),
('D211', 'Database Development', NULL, 2, '1', 'Major', 'Available', 105, 'Database design and implementation.'),
('I212', 'Enterprise Data Management', NULL, 2, '1', 'Major', 'Available', 105, 'Managing enterprise data effectively.'),
('I213', 'Dynamic Web Solutions', NULL, 2, '2', 'Major', 'Available', 105, 'Creating dynamic web applications.'),
('I203', 'Digital Multimedia', NULL, 2, '1', 'Major', 'Available', 105, 'Introduction to digital multimedia.'),
('I221', 'Analysis and Design', NULL, 2, '2', 'Major', 'Available', 105, 'Techniques for system analysis and design.'),
('T201', 'Network Services', NULL, 2, '1', 'Major', 'Available', 105, 'Introduction to network services.'),
('T211', 'Systems Security', NULL, 2, '2', 'Major', 'Available', 105, 'Principles of systems security.'),
('T206', 'Networks (Cisco RSE)', NULL, 2, '1', 'Major', 'Available', 105, 'Cisco networking concepts.'),
('T207', 'Networks (Cisco SRWE)', NULL, 2, '1', 'Major', 'Available', 105, 'Cisco networking security concepts.'),
('I263', 'Introduction to Finance', NULL, 2, '1', 'Major', 'Available', 105, 'Basics of finance in projects.'),
('I202', 'IT Project Management', NULL, 2, '2', 'Major', 'Available', 105, 'Principles of managing IT projects.');


INSERT INTO Subjects (SubjectID, SubjectName, Prerequisite, StudyYear, Sem, CourseType, EnrollmentStatus, CreditHours, Description)
VALUES 
('D301', 'Software Engineering', NULL , 3, '1', 'Major', 'Available', 105, 'Software Engineering Sem'),
('D303', 'Mobile Application Development', NULL , 3, '2', 'Major', 'Available', 105, 'Mobile App Development'),
('I302', 'Industry Project', NULL , 3, '2','Major', 'Available', 105, 'Industry Project'),
('D311', 'Advanced Database Concepts', NULL , 3, '1', 'Major', 'Available', 105, 'Advanced Database Concepts'),
('I304', 'Data Analytics and Intelligence', NULL , 3, '2', 'Major', 'Available', 105, 'Data Analytics and Intelligence'),
('I311', 'Advanced Web Solutions', NULL , 3, '1 or 2', 'Major', 'Available', 105, 'Advanced Web Solutions'),
('I303', 'Management of Information and Communication Technology', NULL , 3, '2', 'Major', 'Available', 105, 'Management of ICT'),
('I321', 'Advanced Systems Analysis', NULL , 3, '1', 'Major', 'Available', 105, 'Advanced Systems Analysis'),
('I367', 'Advanced Project Management', NULL , 3, '1 or 2', 'Major', 'Available', 105, 'Advanced Project Management'),
('T311', 'Systems Administration', NULL , 3, '2', 'Major', 'Available', 105, 'Systems Administration'),
('T301', 'Network Design', NULL , 3, '1', 'Major', 'Available', 105, 'Network Design'),
('T302', 'Cisco Scaling and Connecting Networks', NULL , 3, '2', 'Major', 'Available', 105, 'Cisco Scaling and Connecting Networks'),
('T312', 'Network Security', NULL , 3, '1', 'Major', 'Available', 105, 'Network Security');

INSERT INTO Subjects (SubjectID, SubjectName, Prerequisite, StudyYear, Sem, CourseType, EnrollmentStatus, CreditHours, Description) VALUES
('D101', 'Programme Fundamentals', NULL, 1, '2', 'Major', 'Available', 105, 'Introduction to programming concepts and methodologies.'),
('D111', 'Database Fundamentals', NULL, 1, '1', 'Major', 'Available', 105, 'Fundamentals of database design and management.'),
('I101', 'Information Systems Fundamentals', NULL, 1, '1', 'Major', 'Available', 105, 'Overview of information systems and their role in organizations.'),
('I102', 'Technical Support Fundamentals', NULL, 1, '2', 'Major', 'Available', 105, 'Principles of technical support and troubleshooting techniques.'),
('I111', 'Web Fundamentals', NULL, 1, '2', 'Major', 'Available', 105, 'Basic concepts of web development and design.'),
('I121', 'Systems Analysis Fundamentals', NULL, 1, '1', 'Major', 'Available', 105, 'Introduction to systems analysis and design methodologies.'),
('T101', 'Network Fundamentals', NULL, 1, '2', 'Major', 'Available', 105, 'Basic principles of networking and communication technologies.'),
('T111', 'Computer Hardware Fundamentals', NULL, 1, '1', 'Major', 'Available', 105, 'Overview of computer hardware components and their functions.');

UPDATE Subjects SET Prerequisite = 'I111' WHERE SubjectID = 'I213';
UPDATE Subjects SET Prerequisite = 'I121' WHERE SubjectID = 'I221';
UPDATE Subjects SET Prerequisite = 'I111&I101' WHERE SubjectID = 'I212';
UPDATE Subjects SET Prerequisite = 'I102' WHERE SubjectID = 'I202';
UPDATE Subjects SET Prerequisite = 'I101' WHERE SubjectID = 'I203';
UPDATE Subjects SET Prerequisite = '120 credits at L5' WHERE SubjectID = 'I209';
UPDATE Subjects SET Prerequisite = 'D111' WHERE SubjectID = 'D211';
UPDATE Subjects SET Prerequisite = 'D101' WHERE SubjectID = 'D202';
UPDATE Subjects SET Prerequisite = 'D101' WHERE SubjectID = 'D201';
UPDATE Subjects SET Prerequisite = 'T101' WHERE SubjectID = 'T201';
UPDATE Subjects SET Prerequisite = 'T101' WHERE SubjectID = 'T206';
UPDATE Subjects SET Prerequisite = 'T111' WHERE SubjectID = 'T211';
UPDATE Subjects SET Prerequisite = '240 credits includes all level 5 & level6 compulsory papers' WHERE SubjectID = 'I301';
UPDATE Subjects SET Prerequisite = '280 credits including I301 and all compulsory level 5, 6 papers' WHERE SubjectID = 'I302';
UPDATE Subjects SET Prerequisite = 'I221' WHERE SubjectID = 'I321';
UPDATE Subjects SET Prerequisite = 'I213' WHERE SubjectID = 'I311';
UPDATE Subjects SET Prerequisite = 'D211, I212' WHERE SubjectID = 'I304';
UPDATE Subjects SET Prerequisite = '240 Credits including all compulsory level 5 and 6 courses' WHERE SubjectID = 'I309';
UPDATE Subjects SET Prerequisite = 'D201 or D202' WHERE SubjectID = 'D301';
UPDATE Subjects SET Prerequisite = 'D101' WHERE SubjectID = 'D303';
UPDATE Subjects SET Prerequisite = 'D211' WHERE SubjectID = 'D311';
UPDATE Subjects SET Prerequisite = 'T201' WHERE SubjectID = 'T301';
UPDATE Subjects SET Prerequisite = 'T206' WHERE SubjectID = 'T302';
UPDATE Subjects SET Prerequisite = 'T211' WHERE SubjectID = 'T311';
UPDATE Subjects SET Prerequisite = 'T206 or S203 (Cybersecurity)' WHERE SubjectID = 'T312';
UPDATE Subjects SET Prerequisite = 'I202' WHERE SubjectID = 'I367';

ALTER TABLE Subjects
ADD COLUMN credits INT NOT NULL; 

UPDATE Subjects
SET credits = 15;
UPDATE Subjects SET credits = 45 WHERE SubjectID = 'I302';
INSERT INTO Subjects (SubjectID, SubjectName, Prerequisite, StudyYear, Sem, CourseType, EnrollmentStatus, CreditHours, Description, credits) VALUES ('I301', 'Professional Practice', '240 credits includes all level 5 & level6 compulsory papers', 3, '1', 'Major', 'Available', 105, 'To prepare students for an industry project/internship related to their area of study', 15);

CREATE TABLE SubjectMajors (
    SubjectID VARCHAR(50),                       -- Foreign key linking to Subjects
    MajorID INT,                                 -- Foreign key linking to Majors
    PRIMARY KEY (SubjectID, MajorID),           -- Composite primary key
    FOREIGN KEY (SubjectID) REFERENCES Subjects(SubjectID) ON DELETE CASCADE,
    FOREIGN KEY (MajorID) REFERENCES Majors(MajorID) ON DELETE CASCADE);



-- Linking Subjects to Majors
INSERT INTO SubjectMajors (SubjectID, MajorID) VALUES
('D201', 1),  
('D202', 1),  
('D211', 1),
('D211', 2),
('D201', 2),
('I212', 2),
('D211', 3),
('I213', 3),
('I203', 3),
('D211', 4),
('I221', 4),
('D202', 4),
('D211', 5),
('T201', 5),
('T211', 5),
('T201', 6),
('T206', 6),
('T211', 6),
('D202', 7),
('I212', 7),
('I263', 7),
('I202', 7);

INSERT INTO SubjectMajors (SubjectID, MajorID) VALUES ('D301', 1), ('D303', 1), ('I302', 1), ('D311', 2), ('I304', 2), ('I302', 2), ('D303', 3), ('I311', 3), ('I302', 3), ('I303', 4), ('I321', 4), ('I302', 4), ('D311', 5), ('T311', 5), ('I302', 5), ('T301', 6), ('T302', 6), ('I302', 6), ('T311', 7), ('T312', 7), ('I302', 7), ('I303', 8), ('I367', 8), ('I302', 8);

INSERT INTO SubjectMajors (SubjectID, MajorID) VALUES
('I301', 1),('I301', 2),('I301', 3),('I301', 4),('I301', 5),('I301', 6),('I301', 7),('I301', 8);

CREATE TABLE Electives (
    subjectID VARCHAR(50) PRIMARY KEY,
    Department VARCHAR(100) NOT NULL,
    StudentYear INT NOT NULL
);


INSERT INTO Electives (SubjectID, Department, StudentYear) VALUES 
('D201', 'Bachelor of Information & Communications Technology', 2),
('T206', 'Bachelor of Information & Communications Technology', 2),
('T201', 'Bachelor of Information & Communications Technology', 2),
('I263', 'Bachelor of Information & Communications Technology', 2),
('I221', 'Bachelor of Information & Communications Technology', 2),
('I213', 'Bachelor of Information & Communications Technology', 2),
('I212', 'Bachelor of Information & Communications Technology', 2),
('I203', 'Bachelor of Information & Communications Technology', 2),
('I202', 'Bachelor of Information & Communications Technology', 2),
('D211', 'Bachelor of Information & Communications Technology', 2),
('D202', 'Bachelor of Information & Communications Technology', 2),
('T211', 'Bachelor of Information & Communications Technology', 2);

INSERT INTO Electives (SubjectID, Department, StudentYear) VALUES 
('D301', 'Bachelor of Information & Communications Technology', 3),
('D303', 'Bachelor of Information & Communications Technology', 3),
('D311', 'Bachelor of Information & Communications Technology', 3),
('I303', 'Bachelor of Information & Communications Technology', 3),
('I304', 'Bachelor of Information & Communications Technology', 3),
('I311', 'Bachelor of Information & Communications Technology', 3),
('I321', 'Bachelor of Information & Communications Technology', 3),
('I367', 'Bachelor of Information & Communications Technology', 3),
('T301', 'Bachelor of Information & Communications Technology', 3),
('T302', 'Bachelor of Information & Communications Technology', 3),
('T311', 'Bachelor of Information & Communications Technology', 3),
('T312', 'Bachelor of Information & Communications Technology', 3);


CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50),
    firstname VARCHAR(255),
    lastname VARCHAR(255),
    email VARCHAR(255),
    contactnumber VARCHAR(20),
    address TEXT,
    MajorName1 VARCHAR(255),
    MajorName2 VARCHAR(255),
    StudentYear INT
);



INSERT INTO users (username, password, role, firstname, lastname, email, contactnumber, address, MajorName1, MajorName2, StudentYear) VALUES 
('teja', 'teja123', 'user', 'Teja', 'Chunchu', 'teja.chunchu577@gmail.com', '225066554', '3 Papawai Place, Milson, Palmerston North', NULL, NULL, 2), 
('admin', 'admin', 'admin', 'Admin', 'User', 'admin@example.com', '1234567890', '1 Admin Street, Admin City', NULL , NULL, 2);

INSERT INTO Users (username, password, role, firstname, lastname, email, contactnumber, address, MajorName1, MajorName2, StudentYear) VALUES ('john', 'password1', 'user', 'John', 'Doe', 'john.doe@example.com', '9876543210', '22 Baker Street, London', NULL, NULL, 2), ('jane', 'password2', 'user', 'Jane', 'Doe', 'jane.doe@example.com', '1122334455', '10 Maple Avenue, Springfield', NULL, NULL, 2), ('mike', 'password3', 'user', 'Mike', 'Smith', 'mike.smith@example.com', '5544332211', '15 Pine Road, New York', NULL, NULL, 2), ('linda', 'password4', 'user', 'Linda', 'Brown', 'linda.brown@example.com', '9988776655', '7 Oak Street, Chicago', NULL, NULL, 3);

CREATE TABLE Enrollments (
    EnrollmentID INT PRIMARY KEY AUTO_INCREMENT,
    userid INT NOT NULL,
    SubjectID VARCHAR(50),
    Semester VARCHAR(20) NOT NULL,
    Status VARCHAR(20) NOT NULL,
    results VARCHAR(255),
    FOREIGN KEY (userid) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (SubjectID) REFERENCES Subjects(SubjectID) ON DELETE CASCADE
);

 -- Create the admins table
CREATE TABLE admins (
  id VARCHAR(255) PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL
);

-- Insert data into the admins table
INSERT INTO admins (id, name, email) VALUES
('a1', 'Navdeep Singh', 'nsingh@gmail.com');

-- Create the students table
CREATE TABLE students (
    id INT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    major VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert data into the students table
INSERT INTO students (id, name, email, major) VALUES
(1, 'Anderson', 'janderson@gmail.com', 'Data Analytics and Intelligence'),
(2, 'Michael Brown', 'mbrown@gmail.com', 'Data Analytics and Intelligence');

-- Create the profiles table
CREATE TABLE profiles (
    id CHAR(2) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    contactnumber VARCHAR(10) NOT NULL,
    address VARCHAR(255) NOT NULL
);

-- Insert data into the profiles table
INSERT INTO profiles (id, name, email, contactnumber, address) VALUES
('a0', 'Admin User', 'admin@example.com', '1234567890', 'Admin Street, Admin City'),
('1', 'John Anderson', 'janderson@gmail.com', '1234567890', 'Student Street, Student City'),
('2', 'Michael Brown', 'mbrown@gmail.com', '345678912', 'Student Street, Student City'),
('a1', 'Navdeep Singh', 'nsingh@gmail.com', '1234567890', 'Admin Street, Admin City');

-- Insert enrollments for all users into all year 1 subjects
INSERT INTO enrollments (UserID, SubjectID)
SELECT u.id, s.SubjectID
FROM Users u
JOIN Subjects s ON s.StudyYear = 1;


