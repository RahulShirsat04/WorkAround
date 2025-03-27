-- Drop the existing table if it exists
DROP TABLE IF EXISTS job_applications;

-- Create the job_applications table with all required columns
CREATE TABLE job_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    jobseeker_id INT NOT NULL,
    job_id INT NOT NULL,
    application_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'reviewed', 'accepted', 'rejected') DEFAULT 'pending',
    cover_letter TEXT,
    FOREIGN KEY (jobseeker_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (job_id) REFERENCES jobs(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4; 