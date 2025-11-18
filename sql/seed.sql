USE disciplinary;

-- Dummy seed data for testing

SET FOREIGN_KEY_CHECKS = 0;

-- Students
INSERT IGNORE INTO `Student` (StudentID, EnrollmentNo, FirstName, LastName, DOB, Gender, Email)
VALUES
 (1, 'ENR2023001', 'Alice', 'Johnson', '2006-04-12', 'F', 'alice.johnson@example.test'),
 (2, 'ENR2023002', 'Brian', 'Smith', '2005-09-30', 'M', 'brian.smith@example.test'),
 (3, 'ENR2023003', 'Carla', 'Nguyen', '2006-12-05', 'F', 'carla.nguyen@example.test'),
 (4, 'ENR2023004', 'Diego', 'Martinez', '2004-02-20', 'M', 'diego.martinez@example.test'),
 (5, 'ENR2023005', 'Eve', 'Brown', '2005-07-18', 'F', 'eve.brown@example.test'),
 (6, 'ENR2023006', 'Femi', 'Okafor', '2006-11-02', 'M', 'femi.okafor@example.test'),
 (7, 'ENR2023007', 'Grace', 'Kumar', '2005-03-11', 'F', 'grace.kumar@example.test'),
 (8, 'ENR2023008', 'Hassan', 'Ali', '2004-08-25', 'M', 'hassan.ali@example.test'),
 (9, 'ENR2023009', 'Ivy', 'Wong', '2006-01-07', 'F', 'ivy.wong@example.test'),
 (10,'ENR2023010', 'Jon', 'Doe', '2005-05-22', 'M', 'jon.doe@example.test');

-- Staff
INSERT IGNORE INTO `Staff` (StaffID, Name, Role, Email)
VALUES
 (1, 'Ms. Amanda Green', 'Discipline Officer', 'amanda.green@example.test'),
 (2, 'Mr. Paul White', 'Teacher', 'paul.white@example.test'),
 (3, 'Ms. Lydia Hart', 'Principal', 'lydia.hart@example.test'),
 (4, 'Mr. Omar Khan', 'Counselor', 'omar.khan@example.test');

-- Offense types
INSERT IGNORE INTO `OffenseType` (OffenseTypeID, Code, Description, SeverityLevel)
VALUES
 (1, 'OT-01', 'Classroom disruption', 2),
 (2, 'OT-02', 'Skipping class', 2),
 (3, 'OT-03', 'Cheating on exam', 4),
 (4, 'OT-04', 'Vandalism', 4),
 (5, 'OT-05', 'Bullying', 5),
 (6, 'OT-06', 'Late submission', 1),
 (7, 'OT-07', 'Dress code violation', 1),
 (8, 'OT-08', 'Theft', 5);

-- Incident reports (linked to students)
INSERT IGNORE INTO `IncidentReport` (IncidentID, ReportDate, Location, ReporterStaffID, StudentID, Description, Status)
VALUES
 (1, '2025-01-10 09:15:00', 'Room 101', 2, 1, 'Loud and repeated disruptions during math class.', 'Closed'),
 (2, '2025-02-05 11:30:00', 'Library', 4, 3, 'Found copying answers from another student.', 'Actioned'),
 (3, '2025-03-12 13:00:00', 'Cafeteria', 1, 5, 'Argument escalated; reported bullying behavior.', 'Under Review'),
 (4, '2025-04-01 08:45:00', 'Hallway', 2, 2, 'Skipping first period repeatedly.', 'Open'),
 (5, '2025-05-18 10:20:00', 'Art Lab', 2, 4, 'Damage to shared equipment (minor vandalism).', 'Actioned'),
 (6, '2025-06-02 14:10:00', 'Exam Hall', 3, 6, 'Suspected cheating during exam.', 'Closed'),
 (7, '2025-07-07 09:00:00', 'Room 203', 2, 7, 'Repeated late submissions and lack of participation.', 'Open'),
 (8, '2025-08-21 12:05:00', 'Playground', 1, 8, 'Physical altercation between two students.', 'Under Review'),
 (9, '2025-09-15 09:50:00', 'Room 101', 2, 1, 'Dress code violation; warned earlier this term.', 'Actioned'),
 (10,'2025-10-03 10:00:00', 'Library', 4, 9, 'Reported theft of a textbook.', 'Under Review'),
 (11,'2025-10-20 11:40:00', 'Room 105', 2, 10, 'Frequent classroom disruption.', 'Open'),
 (12,'2025-11-05 15:30:00', 'Cafeteria', 1, 5, 'Follow-up bullying incident; escalation.', 'Open');

-- ReportOffense (link incident -> offense types)
INSERT IGNORE INTO `ReportOffense` (ReportOffenseID, IncidentID, OffenseTypeID, Notes)
VALUES
 (1, 1, 1, 'Teacher logged repeated interruptions.'),
 (2, 2, 3, 'Caught with cheat sheet.'),
 (3, 3, 5, 'Verbal threats, reported by peers.'),
 (4, 4, 2, 'Absent from class without excuse.'),
 (5, 5, 4, 'Paint scratched on easel.'),
 (6, 6, 3, 'Exam caught on phone.'),
 (7, 7, 6, 'Late on homework most weeks.'),
 (8, 8, 5, 'Pushed another student in argument.'),
 (9, 9, 7, 'Wore non-compliant uniform.'),
 (10,10, 8, 'Missing expensive book from shelf.'),
 (11,11, 1, 'Distracted class repeatedly.'),
 (12,12, 5, 'Reported victim statements included.');

-- Disciplinary actions
INSERT IGNORE INTO `DisciplinaryAction` (IncidentID, ActionType, ActionDate, DurationDays, DecisionMakerID, Notes)
VALUES
 (1, 'Warning', '2025-01-15', 0, 3, 'Parent meeting; student warned.'),
 (2, 'Suspension', '2025-02-10', 3, 3, 'Suspended for 3 days due to cheating.'),
 (5, 'Repair/Replacement', '2025-05-22', 0, 3, 'Student required to repair damaged equipment.'),
 (6, 'Expulsion Recommendation', '2025-06-05', 0, 3, 'Serious cheating; escalated.'),
 (9, 'Warning', '2025-09-20', 0, 1, 'Uniform warning issued.'),
 (3, 'Counseling', '2025-03-18', 0, 4, 'Counseling sessions arranged.'),
 (12,'Suspension', '2025-11-10', 2, 3, 'Temporary suspension pending review.');

SET FOREIGN_KEY_CHECKS = 1;