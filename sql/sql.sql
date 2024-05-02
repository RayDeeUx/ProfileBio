-- Create the bios table
CREATE TABLE bios (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    accountID INT(11),
    bio TEXT
);

-- Create the auth table
CREATE TABLE auth (
    accountID INT(50) PRIMARY KEY,
    auth VARCHAR(255)
);

-- Create the bannedwords table
CREATE TABLE bannedwords (
    word TEXT,
	PRIMARY KEY (word(255))
);
