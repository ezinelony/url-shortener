CREATE TABLE IF NOT EXISTS shortened_urls_tbl (
  id VARCHAR (250) PRIMARY KEY,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  shortened_url VARCHAR(25) UNIQUE,
  device_type_redirects_json json1
);
