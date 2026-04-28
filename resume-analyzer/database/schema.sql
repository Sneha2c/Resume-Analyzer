CREATE DATABASE IF NOT EXISTS resume_analyzer;
USE resume_analyzer;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS resumes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  original_name VARCHAR(255) NOT NULL,
  stored_path VARCHAR(255) NOT NULL,
  mime_type VARCHAR(100) NOT NULL,
  file_size INT NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_resumes_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS analysis_results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  resume_id INT NOT NULL UNIQUE,
  score INT NOT NULL,
  skills_found JSON NULL,
  feedback TEXT NOT NULL,
  analyzed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_analysis_resume
    FOREIGN KEY (resume_id) REFERENCES resumes(id)
    ON DELETE CASCADE
);

CREATE DATABASE IF NOT EXISTS resume_analyzer;
USE resume_analyzer;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS resumes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  original_name VARCHAR(255) NOT NULL,
  stored_path VARCHAR(255) NOT NULL,
  mime_type VARCHAR(100) NOT NULL,
  file_size INT NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_resumes_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS analysis_results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  resume_id INT NOT NULL UNIQUE,
  score INT NOT NULL,
  skills_found JSON NULL,
  feedback TEXT NOT NULL,
  analyzed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_analysis_resume
    FOREIGN KEY (resume_id) REFERENCES resumes(id)
    ON DELETE CASCADE
);

