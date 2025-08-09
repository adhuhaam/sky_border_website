-- Add About Us field to company_info table
-- Sky Border Solutions Database Update

USE skydfcaf_sky_border;

-- Add about_us column to company_info table
ALTER TABLE company_info 
ADD COLUMN about_us TEXT AFTER description;

-- Insert or update the About Us content
INSERT INTO company_info (
    company_name, 
    description, 
    about_us,
    phone, 
    email, 
    address,
    website,
    established_year,
    is_active
) VALUES (
    'Sky Border Solution Pvt Ltd',
    'Government-licensed HR consultancy and recruitment firm',
    'Sky Border Solution Pvt Ltd is a government-licensed HR consultancy and recruitment firm headquartered in the Republic of Maldives. Established in response to the rising demand for skilled foreign labor, we are strategically positioned to provide end-to-end manpower solutions. Our operations are driven by a long-term vision, well-defined mission, and a strong foundation of core values. With a seasoned leadership team that brings decades of recruitment expertise, we are adept at identifying, sourcing, and placing the most qualified talent to meet diverse organizational needs. Our consistent year-on-year growth, backed by a solid financial framework, reflects our commitment to service excellence, operational integrity, and client satisfaction. At Sky Border Solution, we are dedicated to bridging workforce gaps with professionalism, precision, and purpose.',
    '+960 330-5462',
    'info@skybordersolutions.com',
    'Male, Republic of Maldives',
    'https://skybordersolutions.com',
    2020,
    1
) ON DUPLICATE KEY UPDATE
    about_us = VALUES(about_us),
    company_name = VALUES(company_name),
    description = VALUES(description),
    phone = VALUES(phone),
    email = VALUES(email),
    address = VALUES(address),
    website = VALUES(website),
    established_year = VALUES(established_year);
