-- Insert Admission Requirements
INSERT INTO admission_requirements (grade_level, min_age_years, required_documents, fee_amount, academic_requirements) VALUES
('Grade 1', 6, '["birth_certificate", "aadhaar_card", "photographs", "transfer_certificate"]', 25000.00, 'Basic literacy and numeracy skills'),
('Grade 2-3', 7, '["birth_certificate", "aadhaar_card", "photographs", "previous_report_card", "transfer_certificate"]', 25000.00, 'Previous class completion certificate'),
('Grade 4-5', 9, '["birth_certificate", "aadhaar_card", "photographs", "previous_report_card", "transfer_certificate"]', 35000.00, 'Good academic record from previous school'),
('Grade 6-8', 11, '["birth_certificate", "aadhaar_card", "photographs", "previous_report_card", "transfer_certificate"]', 45000.00, 'Consistent academic performance');

-- Insert Admission Timeline
INSERT INTO admission_timeline (event_name, event_date, description, academic_year) VALUES
('Admissions Open', '2024-01-01', 'Online and offline applications accepted for 2024-25 session', '2024-25'),
('Early Application Deadline', '2024-03-31', 'Priority consideration for early applicants', '2024-25'),
('First Round Selections', '2024-04-15', 'Selection letters dispatched to successful candidates', '2024-25'),
('Final Application Deadline', '2024-05-31', 'Last date for submission of applications', '2024-25'),
('Fee Payment Deadline', '2024-06-15', 'Last date for fee payment to confirm admission', '2024-25');

-- Insert Fee Structure
INSERT INTO fee_structure (grade_level, tuition_fee, admission_fee, development_fee, other_charges, academic_year) VALUES
('Grade 1-3', 20000.00, 2000.00, 3000.00, 0.00, '2024-25'),
('Grade 4-6', 28000.00, 2500.00, 4500.00, 0.00, '2024-25'),
('Grade 7-8', 36000.00, 3000.00, 6000.00, 0.00, '2024-25');

-- Insert Hostel Fees
INSERT INTO hostel_fees (hostel_type, room_charges, food_charges, other_charges, academic_year) VALUES
('Standard Hostel', 40000.00, 18000.00, 2000.00, '2024-25'),
('Premium Hostel', 55000.00, 22000.00, 3000.00, '2024-25');