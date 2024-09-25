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




