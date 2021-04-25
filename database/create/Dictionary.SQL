# Remove existing tables
DROP TABLE IF EXISTS sphp_dictionarytype;
DROP TABLE IF EXISTS sphp_dictionary;
DROP TABLE IF EXISTS sphp_dictionarydata;

# Create tables
CREATE TABLE sphp_dictionarytype (
	DictionaryTypeID INT(11) PRIMARY KEY AUTO_INCREMENT, 
	DictionaryTypeName VARCHAR(255) NOT NULL, 
	DictionaryTypeIdentifier VARCHAR(255) NOT NULL, 
	DictionaryTypeIsActive TINYINT(1) NOT NULL DEFAULT 0, 
	UserIDInserted INT(11) NULL, 
	UserIDUpdated INT(11) NULL, 
	UserIDLocked INT(11) NULL, 
	TimeInserted DATETIME NULL, 
	TimeUpdated DATETIME NULL, 
	TimeLocked DATETIME NULL, 
	UNIQUE UniqueByName(DictionaryTypeName), 
	UNIQUE UniqueByIdentifier(DictionaryTypeIdentifier), 
	INDEX (DictionaryTypeIsActive)
) ENGINE = MyISAM;

CREATE TABLE sphp_dictionary (
	DictionaryID INT(11) PRIMARY KEY AUTO_INCREMENT, 
	DictionaryTypeID INT(11) NOT NULL, 
	DictionaryName VARCHAR(255) NOT NULL, 
    DictionaryDescription VARCHAR(255) NULL, 
	DictionaryIsActive TINYINT(1) NOT NULL DEFAULT 0, 
	UserIDInserted INT(11) NULL, 
	UserIDUpdated INT(11) NULL, 
	UserIDLocked INT(11) NULL, 
	TimeInserted DATETIME NULL, 
	TimeUpdated DATETIME NULL, 
	TimeLocked DATETIME NULL, 
	UNIQUE (DictionaryTypeID, DictionaryName), 
	INDEX (DictionaryName), 
	INDEX (DictionaryIsActive)
) ENGINE = MyISAM;

CREATE TABLE sphp_dictionarydata (
	DictionaryDataID INT(11) PRIMARY KEY AUTO_INCREMENT, 
	DictionaryID INT(11) NOT NULL, 
	LanguageID INT(11) NOT NULL DEFAULT 0, 
	DictionaryDataName VARCHAR(255) NOT NULL, 
	DictionaryDataValueText TEXT NULL, 
	DictionaryDataValueNumber FLOAT(16, 5) NULL, 
	DictionaryDataIsActive TINYINT(1) NOT NULL DEFAULT 0, 
	UserIDInserted INT(11) NULL, 
	UserIDUpdated INT(11) NULL, 
	UserIDLocked INT(11) NULL, 
	TimeInserted DATETIME NULL, 
	TimeUpdated DATETIME NULL, 
	TimeLocked DATETIME NULL, 
	UNIQUE (DictionaryID, LanguageID, DictionaryDataName), 
	INDEX (DictionaryID), 
	INDEX (LanguageID), 
	INDEX (DictionaryDataName), 
	INDEX (DictionaryDataIsActive)
) ENGINE = MyISAM;

# Create sample data into tables
INSERT INTO sphp_dictionarytype (DictionaryTypeName, DictionaryTypeIdentifier, DictionaryTypeIsActive, TimeInserted) VALUES ('Word', 'WORD', 1, NOW());
INSERT INTO sphp_dictionary (DictionaryTypeID, DictionaryName, DictionaryIsActive, TimeInserted) VALUES (1, 'System', 1, NOW());

INSERT INTO sphp_dictionarydata (DictionaryID, LanguageID, DictionaryDataName, DictionaryDataValueText, DictionaryDataValueNumber, DictionaryDataIsActive, TimeInserted) VALUES 
(1, 1, 'Sample text', 'Sample text value', NULL, 1, NOW()), 
(1, 1, 'Sample number', NULL, 25.68447, 1, NOW()), 
(1, 2, 'উদাহরণ টেক্সট', 'উদাহরণ টেক্সট তথ্য', NULL, 1, NOW()), 
(1, 2, 'উদাহরণ নাম্বার', NULL, 54.25441, 1, NOW());

# Show data from tables
SELECT			DT.DictionaryTypeName, DT.DictionaryTypeIdentifier, #DT.DictionaryTypeIsActive, DT.DictionaryTypeID, 
				D.DictionaryName, D.DictionaryDescription, #D.DictionaryIsActive, D.DictionaryID, 
				DD.DictionaryDataName, DD.DictionaryDataValueText, DictionaryDataValueNumber, #DD.DictionaryDataIsActive, DD.DictionaryDataID
				NULL AS _NULL
FROM			sphp_dictionarydata AS DD
	LEFT JOIN	sphp_dictionary AS D ON D.DictionaryID = DD.DictionaryID
	LEFT JOIN	sphp_dictionarytype AS DT ON DT.DictionaryTypeID = D.DictionaryTypeID
ORDER BY		DT.DictionaryTypeName ASC, 
				D.DictionaryName ASC, 
				DD.DictionaryDataName ASC
;