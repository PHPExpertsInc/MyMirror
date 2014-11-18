CREATE TABLE CachedDomains (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(254) NOT NULL DEFAULT '',
  firstGrabbed datetime DEFAULT NULL,
  count int(11) DEFAULT NULL,
  isAlive tinyint(1) DEFAULT NULL,
  PRIMARY KEY (id) 
) ENGINE=InnoDB;

CREATE TABLE GrabbedURLs (
  id int(11) NOT NULL AUTO_INCREMENT,
  url varchar(1024) NOT NULL,
  last_fetched datetime NOT NULL,
  first_added datetime NOT NULL,
  domainID int(11) DEFAULT NULL,
  PRIMARY KEY (id),
  FOREIGN KEY (domainID) REFERENCES CachedDomains(id)
) ENGINE=InnoDB;

CREATE TABLE Users (
  id int(11) NOT NULL AUTO_INCREMENT,
  username varchar(50) NOT NULL,
  password binary(48) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uidx_username (username)
) ENGINE=InnoDB;

CREATE TABLE UserURLs (
  id int(11) NOT NULL AUTO_INCREMENT,
  userID int(11) NOT NULL,
  urlID int(11) NOT NULL,
  title varchar(254) DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY userID (userID,urlID),
  KEY urlID (urlID),
  FOREIGN KEY (userID) REFERENCES Users (id),
  FOREIGN KEY (urlID) REFERENCES GrabbedURLs (id)
) ENGINE=InnoDB;

CREATE VIEW vw_UserLinks AS 
    SELECT g.id, uu.userID, g.url, uu.title, g.last_fetched, d.name domain 
    FROM UserURLs uu 
        JOIN GrabbedURLs g ON g.id=uu.urlID 
        JOIN CachedDomains d ON d.id=g.domainID;

