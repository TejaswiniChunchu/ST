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
('admin', 'admin123', 'admin', 'Admin', 'User', 'admin@example.com', '1234567890', '1 Admin Street, Admin City', NULL , NULL, 2);

CREATE TABLE Enrollments (
    EnrollmentID INT PRIMARY KEY AUTO_INCREMENT,
    userid INT NOT NULL,
    SubjectID VARCHAR(50),
    Semester VARCHAR(20) NOT NULL,
    Status VARCHAR(20) NOT NULL,
    FOREIGN KEY (userid) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (SubjectID) REFERENCES Subjects(SubjectID) ON DELETE CASCADE
);





