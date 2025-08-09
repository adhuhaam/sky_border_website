-- Industries and Positions Database Schema
-- Sky Border Solutions CMS

-- Industries table
CREATE TABLE IF NOT EXISTS industries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    industry_name VARCHAR(200) NOT NULL,
    industry_description TEXT,
    icon_class VARCHAR(100) DEFAULT 'fas fa-briefcase',
    color_theme VARCHAR(50) DEFAULT 'blue',
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Job positions table
CREATE TABLE IF NOT EXISTS job_positions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    industry_id INT NOT NULL,
    position_name VARCHAR(200) NOT NULL,
    position_description TEXT,
    is_featured BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (industry_id) REFERENCES industries(id) ON DELETE CASCADE
);

-- Insert sample industries data
INSERT INTO industries (industry_name, industry_description, icon_class, color_theme, display_order) VALUES
('Construction & Engineering', 'Major construction and infrastructure projects across the Maldives', 'fas fa-hard-hat', 'amber', 1),
('Healthcare', 'Medical professionals for hospitals, clinics, and healthcare facilities', 'fas fa-user-md', 'emerald', 2),
('Tourism & Hospitality', 'Resort staff and hospitality professionals for the tourism industry', 'fas fa-concierge-bell', 'blue', 3),
('Administration & Office', 'Administrative and office support professionals', 'fas fa-briefcase', 'violet', 4),
('Transport & Logistics', 'Transportation and logistics personnel', 'fas fa-truck', 'rose', 5),
('Education & Childcare', 'Educational professionals and childcare providers', 'fas fa-graduation-cap', 'indigo', 6);

-- Insert sample job positions
INSERT INTO job_positions (industry_id, position_name, position_description, is_featured, display_order) VALUES
-- Construction & Engineering positions
(1, 'Carpenter', 'Skilled woodworking professionals for construction projects', TRUE, 1),
(1, 'Mason', 'Stone and brick laying specialists', TRUE, 2),
(1, 'Plumber', 'Plumbing installation and maintenance experts', FALSE, 3),
(1, 'Electrician', 'Electrical installation and maintenance professionals', TRUE, 4),
(1, 'Welder', 'Metal welding and fabrication specialists', FALSE, 5),
(1, 'Painter', 'Professional painting and finishing specialists', FALSE, 6),
(1, 'Steel Fixer', 'Reinforcement steel installation experts', FALSE, 7),
(1, 'Civil Engineer', 'Construction project design and supervision', TRUE, 8),
(1, 'Quantity Surveyor', 'Cost estimation and project management', FALSE, 9),
(1, 'Excavator Operator', 'Heavy machinery operation specialists', FALSE, 10),
(1, 'Bulldozer Operator', 'Earth moving equipment operators', FALSE, 11),
(1, 'Crane Operator', 'Crane operation and lifting specialists', FALSE, 12),
(1, 'Site Supervisor', 'Construction site management and oversight', FALSE, 13),
(1, 'Project Manager', 'Overall project coordination and management', TRUE, 14),
(1, 'Mechanical Engineer', 'Mechanical systems design and maintenance', FALSE, 15),
(1, 'HVAC Technician', 'Heating, ventilation, and air conditioning specialists', FALSE, 16),
(1, 'Safety Officer', 'Construction site safety and compliance', FALSE, 17),

-- Healthcare positions
(2, 'General Practitioners', 'Primary care physicians', TRUE, 1),
(2, 'Nurses', 'Registered nurses for various medical specialties', TRUE, 2),
(2, 'Surgeons', 'Surgical specialists for various procedures', TRUE, 3),
(2, 'Paediatricians', 'Child healthcare specialists', FALSE, 4),
(2, 'Orthopaedic Surgeons', 'Bone and joint surgery specialists', FALSE, 5),
(2, 'ENT Specialists', 'Ear, nose, and throat specialists', FALSE, 6),
(2, 'Dermatologists', 'Skin condition specialists', FALSE, 7),
(2, 'Pharmacists', 'Medication management professionals', TRUE, 8),
(2, 'Laboratory Technicians', 'Medical testing and analysis specialists', FALSE, 9),
(2, 'Radiologists', 'Medical imaging specialists', TRUE, 10),
(2, 'Physiotherapists', 'Physical rehabilitation specialists', TRUE, 11),
(2, 'Speech Therapists', 'Communication disorder specialists', FALSE, 12),
(2, 'Medical Officers', 'General medical practitioners', FALSE, 13),
(2, 'Dentists', 'Oral health specialists', FALSE, 14),
(2, 'Anaesthesiologists', 'Anesthesia and pain management specialists', FALSE, 15),
(2, 'Gynaecologists', 'Women\'s health specialists', FALSE, 16),
(2, 'Psychiatrists', 'Mental health specialists', FALSE, 17),
(2, 'Occupational Therapists', 'Workplace health and rehabilitation specialists', FALSE, 18),

-- Tourism & Hospitality positions
(3, 'Head Chefs', 'Executive kitchen management professionals', TRUE, 1),
(3, 'Spa Therapists', 'Wellness and spa treatment specialists', TRUE, 2),
(3, 'Sous Chefs', 'Assistant kitchen management professionals', FALSE, 3),
(3, 'Pastry Chefs', 'Dessert and baking specialists', FALSE, 4),
(3, 'Bartenders', 'Beverage service professionals', TRUE, 5),
(3, 'Waiters/Waitresses', 'Food service professionals', FALSE, 6),
(3, 'Restaurant Supervisors', 'Food service management', FALSE, 7),
(3, 'DJs/Entertainers', 'Entertainment and music professionals', FALSE, 8),
(3, 'Butlers', 'Personal service professionals', FALSE, 9),
(3, 'Sommeliers', 'Wine service specialists', FALSE, 10),
(3, 'Bell Boys', 'Guest service and luggage assistance', FALSE, 11),
(3, 'Housekeeping Attendants', 'Room cleaning and maintenance', FALSE, 12),
(3, 'Housekeeping Supervisors', 'Housekeeping management', FALSE, 13),
(3, 'Front Office Receptionists', 'Guest check-in and customer service', FALSE, 14),
(3, 'Guest Relations Officers', 'Guest experience management', FALSE, 15),
(3, 'Concierges', 'Guest assistance and local expertise', FALSE, 16),
(3, 'Resort Managers', 'Overall resort operations management', TRUE, 17),
(3, 'Maintenance Technicians', 'Facility maintenance specialists', FALSE, 18),
(3, 'Diving Instructors', 'Scuba diving education and safety', TRUE, 19),
(3, 'Water Sports Instructors', 'Aquatic activities instruction', FALSE, 20),
(3, 'Boat Captains', 'Marine vessel operation', TRUE, 21),
(3, 'Boat Crew', 'Marine vessel support staff', FALSE, 22);
