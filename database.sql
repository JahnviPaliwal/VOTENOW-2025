-- ============================================================
-- Online Voting System - College Election 2025 (MLC President)
-- Database: voting_system
-- ============================================================

CREATE DATABASE IF NOT EXISTS voting_system;
USE voting_system;

-- ------------------------------------------------------------
-- Table: users
-- Stores voter information collected during registration
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(15) NOT NULL UNIQUE,
    voter_id VARCHAR(50) NOT NULL UNIQUE,
    department VARCHAR(100) NOT NULL,
    has_voted TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- Table: candidates
-- Stores candidate information for the election
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    department VARCHAR(100) NOT NULL,
    achievements TEXT,
    contributions TEXT,
    past_wins TEXT,
    votes INT DEFAULT 0
);

-- ------------------------------------------------------------
-- Table: votes
-- Tracks which user voted for which candidate
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    candidate_id INT NOT NULL,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (candidate_id) REFERENCES candidates(id)
);

-- ------------------------------------------------------------
-- Sample Candidates Data
-- ------------------------------------------------------------
INSERT INTO candidates (name, department, achievements, contributions, past_wins) VALUES
(
    'Rahul Sharma',
    'Computer Science',
    'Gold Medal in Hackathon 2023, Best Student Award 2022, NSS Volunteer of the Year 2021',
    'Established the college coding club, Organized 5+ technical workshops, Led the campus Wi-Fi upgrade initiative',
    'Class Representative 2021-22, Department Secretary 2022-23'
),
(
    'Priya Verma',
    'Electronics Engineering',
    'University Rank 1 in 2022, TEDx Speaker, State-level Debate Champion',
    'Founded the Women in STEM initiative, Launched the college newsletter, Organized annual technical fest',
    'Cultural Secretary 2021-22, Student Union Treasurer 2022-23'
),
(
    'Amit Patel',
    'Mechanical Engineering',
    'Best Project Award 2023, Sports Captain, National-level Chess Player',
    'Revamped the college sports facilities, Launched mentorship program for juniors, Initiated green campus drive',
    'Sports Secretary 2021-22, Department President 2022-23'
),
(
    'Neha Singh',
    'Civil Engineering',
    'Research Paper Published in IEEE, Scholarship Recipient, Social Service Award',
    'Led infrastructure improvement proposals, Organized blood donation drives, Created student welfare committee',
    'Academic Secretary 2022-23, NSS Coordinator 2021-22'
),
(
    'Rohan Gupta',
    'Information Technology',
    'Startup Founder, Winner of National Innovation Challenge, Best Intern Award at TCS',
    'Built the college attendance app, Organized entrepreneurship bootcamp, Set up the college helpdesk',
    'Tech Club President 2022-23, Entrepreneurship Cell Head 2021-22'
);
