CREATE DATABASE hotcompostthesiswebsite;

CREATE TABLE hotcompost(
	id int AUTO_INCREMENT NOT NULL,
	status varchar (30) NOT NULL,
	createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	lastMixed TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	CONSTRAINT PK_hotcompost
	PRIMARY KEY (id)
);

CREATE TABLE layer(
    id int AUTO_INCREMENT NOT NULL,
	hotcompost_id INT NOT NULL,
	material varchar (50) NOT NULL,
    weight double NOT NULL,
	part varchar (50) NOT NULL,
    FOREIGN KEY (hotcompost_id) REFERENCES hotcompost(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    CONSTRAINT PK_layer
    PRIMARY KEY (id)
);

CREATE TABLE sensor(
    id int AUTO_INCREMENT NOT NULL,
	hotcompost_id INT NOT NULL,
    moisturePercent double NOT NULL,
	temperatureCelsius double NOT NULL,
	time TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    FOREIGN KEY (hotcompost_id) REFERENCES hotcompost(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    CONSTRAINT PK_sensor
    PRIMARY KEY (id)
);

CREATE TABLE connection(
	id int AUTO_INCREMENT NOT NULL,
	weight double NOT NULL,
	nitrogen double NOT NULL,
	phosphorus double NOT NULL,
	potassium double NOT NULL,
	request varchar (50) NOT NULL,
	CONSTRAINT PK_connection
	PRIMARY KEY (id)
);








CREATE TABLE users(
	id int AUTO_INCREMENT NOT NULL,
	email varchar (50) UNIQUE NOT NULL,
	password varchar (255) NOT NULL,
	authtype varchar (30) NOT NULL,
	firstname varchar (30) NOT NULL,
	lastname varchar (30) NOT NULL,
	CONSTRAINT PK_users
	PRIMARY KEY (id)
);

CREATE TABLE food_categories(
	id int AUTO_INCREMENT NOT NULL,
	name varchar (50) NOT NULL,
	image varchar (50) NOT NULL,
	CONSTRAINT PK_food_categories
	PRIMARY KEY (id)
);

CREATE TABLE foods (
    id int AUTO_INCREMENT NOT NULL,
    name varchar (50) NOT NULL,
    description varchar (255) NOT NULL,
	discount int NOT NULL,
    price double NOT NULL,
	image varchar (50) NOT NULL,
    food_categories_id INT (20) NOT NULL,
    FOREIGN KEY (food_categories_id) REFERENCES food_categories(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    CONSTRAINT PK_foods
    PRIMARY KEY (id)
);

CREATE TABLE drinks (
    id int AUTO_INCREMENT NOT NULL,
    name varchar (50) NOT NULL,
	price double NOT NULL,
    CONSTRAINT PK_drinks
    PRIMARY KEY (id)
);

CREATE TABLE user_carts (
    id int AUTO_INCREMENT NOT NULL,
	users_id int NOT NULL,
	foods_id int NOT NULL,
	quantity int NOT NULL,
	drinks_id int NOT NULL,
	FOREIGN KEY (users_id) REFERENCES users(id),
	FOREIGN KEY (foods_id) REFERENCES foods(id),
	FOREIGN KEY (drinks_id) REFERENCES drinks(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    CONSTRAINT PK_user_carts
    PRIMARY KEY (id)
);

CREATE TABLE receipts (
    id int AUTO_INCREMENT NOT NULL,
    users_id int NOT NULL,
	totalPrice double NOT NULL,
	orderDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	FOREIGN KEY (users_id) REFERENCES users(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    CONSTRAINT PK_receipts
    PRIMARY KEY (id)
);

CREATE TABLE food_orders (
    id int AUTO_INCREMENT NOT NULL,
	receipts_id int NOT NULL,
	foods_id int NOT NULL,
	quantity int NOT NULL,
	discount int NOT NULL,
	price double NOT NULL,
	drinks_id int NOT NULL,
	status varchar (30) NOT NULL,
	FOREIGN KEY (receipts_id) REFERENCES receipts(id),
	FOREIGN KEY (foods_id) REFERENCES foods(id),
	FOREIGN KEY (drinks_id) REFERENCES drinks(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    CONSTRAINT PK_food_orders
    PRIMARY KEY (id)
);

/* Create Table for Locations */
CREATE TABLE [ONLINE SOS SYSTEM].[dbo].[Locations](
	[loc_id] [numeric] (5) IDENTITY(1, 1) NOT NULL,
	[loc_bldg] [varchar] (30) NOT NULL,
	[loc_room] [varchar] (30) NOT NULL,
	[loc_area] [varchar] (30) NOT NULL,
	[loc_floor] [varchar] (30) NOT NULL,
	CONSTRAINT [PK_Locations]
	PRIMARY KEY ([loc_id])
);

/* Create Table for Reports */
CREATE TABLE [ONLINE SOS SYSTEM].[dbo].[Reports](
	[rep_id] [numeric] (20) IDENTITY(1, 1) NOT NULL,
	[user_id] [numeric] (20) NOT NULL FOREIGN KEY REFERENCES [Users] ([user_id]),
	[loc_id] [numeric] (5) NOT NULL FOREIGN KEY REFERENCES [Locations] ([loc_id]),
	[rep_type] [varchar] (30) NOT NULL,
	[rep_status] [varchar] (30) NOT NULL,
	[rep_date] [date] NOT NULL,
	[rep_time] [time] NOT NULL,
	CONSTRAINT [PK_Reports]
	PRIMARY KEY ([rep_id])
);

/* Create Table for EMS */
CREATE TABLE [ONLINE SOS SYSTEM].[dbo].[EMS](
	[ems_id] [numeric] (20) IDENTITY(1, 1) NOT NULL,
	[rep_id] [numeric] (20) NOT NULL FOREIGN KEY REFERENCES [Reports] ([rep_id]),
	[ems_patcon] [varchar] (30) NOT NULL,
	[ems_conc] [varchar] (30) NOT NULL,
	[ems_otherinfo] [varchar] (255),
	CONSTRAINT [PK_EMS]
	PRIMARY KEY ([ems_id])
);

/* Create Table for Security */
CREATE TABLE [ONLINE SOS SYSTEM].[dbo].[Security](
	[sec_id] [numeric] (20) IDENTITY(1, 1) NOT NULL,
	[rep_id] [numeric] (20) NOT NULL FOREIGN KEY REFERENCES [Reports] ([rep_id]),
	[sec_conc] [varchar] (30) NOT NULL,
	[sec_otherinfo] [varchar] (255),
	CONSTRAINT [PK_Security]
	PRIMARY KEY ([sec_id])
);

/* Create Table for Fires */
CREATE TABLE [ONLINE SOS SYSTEM].[dbo].[Fires](
	[fire_id] [numeric] (20) IDENTITY(1, 1) NOT NULL,
	[rep_id] [numeric] (20) NOT NULL FOREIGN KEY REFERENCES [Reports] ([rep_id]),
	[fire_otherinfo] [varchar] (255),
	CONSTRAINT [PK_Fires]
	PRIMARY KEY ([fire_id])
);

/* Insert Values to Users */
INSERT INTO Users
	(user_id, user_email, user_password, user_authtype, user_FName, user_LName, user_level, user_grade, user_phonenum, user_emerName, user_emerNum, user_houseNum, user_brgy, user_city, user_prov) VALUES
	(2022873219, 'maria_cruz@dlsl.edu.ph', 'Maria123', 'admin', 'Maria', 'Cruz', 'Assistant IT', 'IT', '9171234567', 'Maria Santos', '9172345678', 'Blk 19 Lot 23', 'Buendia', 'Batangas City', 'Batangas'),
	(2021125063, 'juan_delacruz@dlsl.edu.ph', 'Juan456', 'admin', 'Juan', 'Delacruz', 'Watchman', 'Security', '9283210987', 'Rodrigo Delacruz', '9284321098', 'Blk 7 Lot 45', 'Dalahican', 'Lucena City', 'Quezon'),
	(2023941708, 'ana_ reyes@dlsl.edu.ph', 'Ana789', 'admin', 'Ana', 'Reyes', 'Head', 'ISSESO', '9367895432', 'Elena Reyes', '9368906543', 'Blk 16 Lot 67', 'Inosluban', 'Lipa City', 'Batangas'),
	(2022406821, 'pedro_bautista@dlsl.edu.ph', 'Pedro012', 'student', 'Pedro', 'Bautista', '2nd Year', 'College', '9456127890', 'Andrea Bautista', '9456238901', 'Blk 10 Lot 89', 'Latag', 'Lipa City', 'Laguna'),
	(2021639475, 'isabela_gonzalez@dlsl.edu.ph', 'Isabela345', 'student', 'Isabela', 'Gonzalez', '1st Year', 'College', '9576432098', 'Mateo Gonzalez', '9576543109', 'Blk 10 Lot 12', 'Balintawak', 'Lipa City', 'Laguna'),
	(2023017532, 'daniel_lopez@dlsl.edu.ph', 'Daniel678', 'student', 'Daniel', 'Lopez', 'Grade 12', 'Integrated', '9654321678', 'Camila Lopez', '9655432789', 'Blk 5 Lot 34', 'Labas', 'Santa Rosa City', 'Batangas'),
	(2022284190, 'patricia_mendiola@dlsl.edu.ph', 'Patricia901', 'student', 'Patricia', 'Mendiola', '3rd Year', 'College', '9789012345', 'Gabriel Mendiola', '9789123456', 'Blk 2 Lot 56', 'San Antonio', 'San Pedro City', 'Quezon'),
	(2021762354, 'david_acosta@dlsl.edu.ph', 'David234', 'student', 'David', 'Acosta', 'Grade 9', 'Integrated', '9876543210', 'Mariana Acosta', '9877654321', 'Blk 18 Lot 78', 'Santor', 'Tanauan City', 'Batangas'),
	(2023590817, 'cristina_castro@dlsl.edu.ph', 'Cristina567', 'student', 'Cristina', 'Castro', 'Grade 8', 'Integrated', '9987654321', 'Santiago Castro', '9988765432', 'Blk 18 Lot 90', 'Alitao', 'Tayabas City', 'Laguna'),
	(2022318642, 'miguel_fernandez@dlsl.edu.ph', 'Miguel890', 'student', 'Miguel', 'Fernandez', 'Grade 9', 'Integrated', '9123456789', 'Lucia Fernandez', '9134567890', 'Blk 7 Lot 12', 'Bagong Sikat', 'Balayan', 'Batangas'),
	(2021946126, 'alyssa_delacruz@dlsl.edu.ph', 'Alyssa123', 'student', 'Alyssa', 'Delacruz', '1st Year', 'College', '9234567890', 'Diego Delacruz', '9245678901', 'Blk 6 Lot 34', 'Marinig', 'Cabuyao City', 'Laguna'),
	(2023173489, 'kevin_ reyes@dlsl.edu.ph', 'Kevin456', 'student', 'Kevin', 'Reyes', 'Grade 12', 'Integrated', '9345678901', 'Natalia Reyes', '9356789012', 'Blk 6 Lot 56', 'Lumbangan', 'Nasugbu', 'Batangas'),
	(2022601273, 'bea_bautista@dlsl.edu.ph', 'Bea789', 'student', 'Bea', 'Bautista', 'Grade 9', 'Integrated', '9456789012', 'Alejandro Bautista', '9467890123', 'Blk 4 Lot 78', 'San Francisco', 'Bi�an City', 'Laguna'),
	(2021437907, 'mark_gonzalez@dlsl.edu.ph', 'Mark012', 'student', 'Mark', 'Gonzalez', '3rd Year', 'College', '9567890123', 'Valeria Gonzalez', '9578901234', 'Blk 8 Lot 90', 'Ligtong', 'Rosario', 'Batangas'),
	(2023205760, 'shara_lopez@dlsl.edu.ph', 'Shara345', 'student', 'Shara', 'Lopez', 'Grade 7', 'Integrated', '9678901234', 'Federico Lopez', '9689012345', 'Blk 8 Lot 12', 'Uno', 'Calamba City', 'Batangas'),
	(2022079531, 'carlo_mendiola@dlsl.edu.ph', 'Carlo678', 'student', 'Carlo', 'Mendiola', 'Grade 6', 'Integrated', '9789012345', 'Sofia Mendiola', '9790123456', 'Blk 9 Lot 34', 'Santa Clara', 'Santo Tomas', 'Quezon'),
	(2021813295, 'lea_acosta@dlsl.edu.ph', 'Lea901', 'student', 'Lea', 'Acosta', '1st Year', 'College', '9890123456', 'Ignacio Acosta', '9801234567', 'Blk 14 Lot 56', 'San Antonio', 'San Pascual', 'Laguna'),
	(2023641058, 'ethan_castro@dlsl.edu.ph', 'Ethan234', 'student', 'Ethan', 'Castro', 'Grade 11', 'Integrated', '9901234567', 'Maria Castro', '9112345678', 'Blk 20 Lot 78', 'Sampaloc', 'Sariaya', 'Batangas'),
	(2022478820, 'sofia_fernandez@dlsl.edu.ph', 'Sofia567', 'student', 'Sofia', 'Fernandez', 'Grade 6', 'Integrated', '9123456789', 'Javier Fernandez', '9223456789', 'Blk 3 Lot 90', 'San Gregorio', 'San Pablo City', 'Laguna'),
	(2021210684, 'nathan_delacruz@dlsl.edu.ph', 'Nathan890', 'student', 'Nathan', 'Delacruz', '3rd Year', 'College', '9234567890', 'Gabriela Delacruz', '9334567890', 'Blk 3 Lot 12', 'Niogan', 'Taal', 'Batangas');

/* Insert Values to Locations */
Insert INTO Locations
	(loc_bldg, loc_room, loc_area, loc_floor) VALUES
	('CB', '100', 'Hallway', '1st Floor'),
	('CB', '100', 'Outside', '1st Floor'),
	('CB', '101', 'Inside', '1st Floor'),
	('CB', '102', 'Inside', '1st Floor'),
	('CB', '103', 'Inside', '1st Floor'),
	('CB', '104', 'Inside', '1st Floor'),
	('CB', '105', 'Inside', '1st Floor'),
	('CB', '106', 'Inside', '1st Floor'),
	('CB', '107', 'Inside', '1st Floor'),
	('CB', '108', 'Inside', '1st Floor'),
	('CB', '109', 'Inside', '1st Floor'),
	('CB', '200', 'Hallway', '2nd Floor'),
	('CB', '200', 'Inside', '2nd Floor'),
	('CB', '201', 'Inside', '2nd Floor'),
	('CB', '202', 'Inside', '2nd Floor'),
	('CB', '203', 'Inside', '2nd Floor'),
	('CB', '204', 'Inside', '2nd Floor'),
	('CB', '205', 'Inside', '2nd Floor'),
	('CB', '206', 'Inside', '2nd Floor'),
	('CB', '207', 'Inside', '2nd Floor'),
	('CB', '208', 'Inside', '2nd Floor'),
	('CB', '209', 'Inside', '2nd Floor'),
	('CB', '300', 'Hallway', '3rd Floor'),
	('CB', '300', 'Inside', '3rd Floor'),
	('CB', '301', 'Inside', '3rd Floor'),
	('CB', '302', 'Inside', '3rd Floor'),
	('CB', '303', 'Inside', '3rd Floor'),
	('CB', '304', 'Inside', '3rd Floor'),
	('CB', '305', 'Inside', '3rd Floor'),
	('CB', '306', 'Inside', '3rd Floor'),
	('CB', '307', 'Inside', '3rd Floor'),
	('CB', '308', 'Inside', '3rd Floor'),
	('CB', '309', 'Inside', '3rd Floor'),
	('GZ', '100', 'Hallway', '1st Floor'),
	('GZ', '100', 'Outside', '1st Floor'),
	('GZ', '101', 'Inside', '1st Floor'),
	('GZ', '102', 'Inside', '1st Floor'),
	('GZ', '201', 'Inside', '2nd Floor'),
	('GZ', '202', 'Inside', '2nd Floor'),
	('GZ', '200', 'Hallway', '2nd Floor'),
	('MB', '100', 'Hallway', '1st Floor'),
	('MB', '100', 'Outside', '1st Floor'),
	('MB', '100', 'Inside', '1st Floor'),
	('MB', '101', 'Inside', '1st Floor'),
	('MB', '102', 'Inside', '1st Floor'),
	('MB', '103', 'Inside', '1st Floor'),
	('MB', '104', 'Inside', '1st Floor'),
	('MB', '105', 'Inside', '1st Floor'),
	('MB', '106', 'Inside', '1st Floor'),
	('MB', '107', 'Inside', '1st Floor'),
	('MB', '108', 'Inside', '1st Floor'),
	('MB', '200', 'Inside', '2nd Floor'),
	('MB', '201', 'Inside', '2nd Floor'),
	('MB', '202', 'Inside', '2nd Floor'),
	('MB', '203', 'Inside', '2nd Floor'),
	('MB', '204', 'Inside', '2nd Floor'),
	('MB', '205', 'Inside', '2nd Floor'),
	('MB', '206', 'Inside', '2nd Floor'),
	('MB', '207', 'Inside', '2nd Floor'),
	('MB', '208', 'Inside', '2nd Floor');
	

/* Insert Values to Reports */
INSERT INTO Reports
	(user_id, loc_id, rep_type, rep_status, rep_date, rep_time) VALUES
	(2023017532, 32, 'EMS', 'Completed', '2024-05-06', '16:41:00'),
	(2022318642, 60, 'Security', 'Completed', '2024-05-07', '15:00:00'),
	(2022478820, 12, 'Fire', 'Completed', '2024-05-07', '11:39:40'),
	(2022601273, 1, 'EMS', 'Completed', '2024-05-07', '15:00:00'),
	(2022478820, 21, 'Security', 'Completed', '2024-05-08', '16:00:00'),
	(2022079531, 11, 'EMS', 'Completed', '2024-05-15', '12:21:00'),
	(2022318642, 35, 'Security', 'Completed', '2024-05-18', '9:00:00'),
	(2021210684, 20, 'Fire', 'Completed', '2024-05-19', '15:39:40'),
	(2023017532, 13, 'Security', 'Completed', '2024-05-21', '13:15:00'),
	(2023941708, 14, 'Security', 'Completed', '2024-05-21', '14:15:00'),
	(2023205760, 14, 'Security', 'Completed', '2024-05-21', '14:18:00'),
	(2021946126, 18, 'EMS', 'Completed', '2024-05-23', '7:21:00'),
	(2023173489, 25, 'Fire', 'Completed', '2024-05-28', '12:39:00'),
	(2022079531, 56, 'EMS', 'Completed', '2024-06-01', '13:41:00'),
	(2022079531, 56, 'EMS', 'Completed', '2024-06-01', '13:41:30'),
	(2022079531, 25, 'Fire', 'Completed', '2024-06-03', '12:00:00'),
	(2021946126, 23, 'EMS', 'Completed', '2024-06-05', '9:21:33'),
	(2023590817, 14, 'Security', 'Completed', '2024-06-06', '14:23:00'),
	(2022079531, 31, 'Security', 'Completed', '2024-06-08', '15:21:00'),
	(2022601273, 25, 'Fire', 'Completed', '2024-06-14', '16:32:00');
	
/* Insert Values to EMS */
INSERT INTO EMS
	(rep_id, ems_patcon, ems_conc, ems_otherinfo) VALUES
	(1, 'Unconscious', 'Trauma', 'Spider Trauma'),
	(4, 'Conscious', 'Medical', 'Hika'),
	(6, 'Unconscious', 'Medical', 'Heart Attack'),
	(12, 'Conscious', 'Medical', 'Anemic'),
	(14, 'Unconscious', 'Medical', NULL),
	(15, 'Unconscious', 'Medical', NULL),
	(17, 'Unconscious', 'Medical', 'Exhaustion');

/* Insert Values to Security */
INSERT INTO Security
	(rep_id, sec_conc, sec_otherinfo) VALUES
	(2, 'Missing Item', 'Laptop'),
	(5, 'Commotion', NULL),
	(7, 'Commotion', 'Bullying'),
	(9, 'Missing Item', 'Bag'),
	(10, 'Missing Item', 'Cellphone'),
	(11, 'Missing Item', 'Cellphone'),
	(18, 'Commotion', 'Fight'),
	(19, 'Missing Person', NULL);

INSERT INTO Fires
	(rep_id, fire_otherinfo) VALUES
	(3, 'Burning TV'),
	(8, NULL),
	(13, 'Bag'),
	(16, NULL),
	(20, NULL);

/* Stored Procedures */
/* Create */
/* Create Procedure for New Users */
CREATE PROCEDURE create_user(
		@user_id [numeric] (20),
		@user_email [varchar] (50),
		@user_password [varchar] (30),
		@user_FName [varchar] (30),
		@user_LName [varchar] (30),
		@user_level [varchar] (30),
		@user_grade [varchar] (30),
		@user_phonenum [varchar] (30),
		@user_emerName [varchar] (30),
		@user_emerNum [varchar] (30),
		@user_houseNum [varchar] (30),
		@user_brgy [varchar] (30),
		@user_city [varchar] (30),
		@user_prov [varchar] (30)
	) AS
		INSERT INTO USERS
			(user_id, user_email, user_password, user_authtype, user_FName, user_LName, user_level, user_grade, user_phonenum, user_emerName, user_emerNum, user_houseNum, user_brgy, user_city, user_prov) VALUES
			(@user_id, @user_email, @user_password, 'student', @user_FName, @user_LName, @user_level, @user_grade, @user_phonenum, @user_emerName, @user_emerNum, @user_houseNum, @user_brgy, @user_city, @user_prov);

/* Create a User */
EXEC create_user
	@user_id = 2021292832,
	@user_email = 'reinard_christian_ruiz@dlsl.edu.ph',
	@user_password = 'reinera321',
	@user_FName = 'Reinard',
	@user_LName = 'Ruiz',
	@user_level = 'College',
	@user_grade = '3rd Year',
	@user_phonenum = '0912 231 8485',
	@user_emerName = 'Emergency Ruiz',
	@user_emerNum = '0934 485 2315',
	@user_houseNum = '423',
	@user_brgy = 'Upa',
	@user_city = 'Mataasnakahoy',
	@user_prov = 'Batangas';
	
/* Create Procedure for New Locations */
CREATE PROCEDURE new_location(
		@loc_bldg [varchar] (30),
		@loc_room [varchar] (30),
		@loc_area [varchar] (30),
		@loc_floor [varchar] (30)
	) AS
		INSERT INTO Locations
			(loc_bldg, loc_room, loc_area, loc_floor) VALUES
			(@loc_bldg, @loc_room, @loc_area, @loc_floor);

/* Create a Location */
EXEC new_location
	@loc_bldg = 'MB',
	@loc_room = '300',
	@loc_area = 'Hallway',
	@loc_floor = '3rd Floor';

/* Create Procedure for EMS Reports */
CREATE PROCEDURE ems_report (
		@user_id [numeric] (20),
		@ems_patcon [varchar] (30),
		@ems_conc [varchar] (30),
		@ems_otherinfo [varchar] (255),
		@loc_id [numeric] (5)
	) AS
		BEGIN
			/* Create Report */
			INSERT INTO Reports
				(user_id, loc_id, rep_type, rep_status, rep_date, rep_time) VALUES
				(@user_id, @loc_id, 'EMS', 'Ongoing', GETDATE(), GETDATE());
			
			DECLARE @rep_id [numeric] (5);	/* Create new variable */
			SET @rep_id = SCOPE_IDENTITY();	/* Get the previous identity or auto increment */

			/* Create EMS */
			INSERT INTO EMS
				(rep_id, ems_patcon, ems_conc, ems_otherinfo) VALUES
				(@rep_id, @ems_patcon, @ems_conc, @ems_otherinfo)
		END;

/* Create an EMS Report */
EXEC ems_report
	@user_id = 2022079531,
	@ems_patcon = 'Conscious',
	@ems_conc = 'Medical',
	@ems_otherinfo = NULL,
	@loc_id = 34;

/* Create Procedure for Security Reports */
CREATE PROCEDURE security_report(
		@user_id [numeric] (20),
		@sec_conc [varchar] (30),
		@sec_otherinfo [varchar] (255),
		@loc_id [numeric] (5)
	) AS
		BEGIN
			/* Create Report */
			INSERT INTO Reports
				(user_id, loc_id, rep_type, rep_status, rep_date, rep_time) VALUES
				(@user_id, @loc_id, 'Security', 'Ongoing', GETDATE(), GETDATE());
			
			DECLARE @rep_id [numeric] (5);	/* Create new variable */
			SET @rep_id = SCOPE_IDENTITY();	/* Get the previous identity or auto increment */

			/* Create Security */
			INSERT INTO Security
				(rep_id, sec_conc, sec_otherinfo) VALUES
				(@rep_id, @sec_conc, @sec_otherinfo)
		END;

/* Create a Security Report */
EXEC security_report
	@user_id = 2021762354,
	@sec_conc = 'Commotion',
	@sec_otherinfo = 'College vs Senior High',
	@loc_id = 3;

/* Create Procedure for Fire Reports */
CREATE PROCEDURE fire_report(
		@user_id [numeric] (20),
		@fire_otherinfo [varchar] (255),
		@loc_id [numeric] (5)
	) AS
		BEGIN
			/* Create Report */
			INSERT INTO Reports
				(user_id, loc_id, rep_type, rep_status, rep_date, rep_time) VALUES
				(@user_id, @loc_id, 'Fire', 'Ongoing', GETDATE(), GETDATE());
			
			DECLARE @rep_id [numeric] (5);	/* Create new variable */
			SET @rep_id = SCOPE_IDENTITY();	/* Get the previous identity or auto increment */

			/* Create Fire */
			INSERT INTO Fires
				(rep_id, fire_otherinfo) VALUES
				(@rep_id, @fire_otherinfo)
		END;

/* Create a Fire Report */
EXEC fire_report
	@user_id = 2022873219,
	@fire_otherinfo = NULL,
	@loc_id = 25;

/* DELETE */
/* Create Procedure to Delete Users */
CREATE PROCEDURE delete_user(
		@user_id [numeric] (20)
	) AS
		BEGIN
			/* Delete Records of User in EMS */
			DELETE FROM EMS
				WHERE ems_id IN (
					SELECT EMS.ems_id
						FROM Reports, EMS
							WHERE Reports.rep_id = EMS.rep_id AND Reports.user_id = @user_id
				);

			/* Delete Records of User in Security */
			DELETE FROM Security
				WHERE sec_id IN (
					SELECT Security.sec_id
						FROM Reports, Security
							WHERE Reports.rep_id = Security.rep_id AND Reports.user_id = @user_id
				);

			/* Delete Records of User in Fires */
			DELETE FROM Fires
				WHERE fire_id IN (
					SELECT Fires.fire_id
						FROM Reports, Fires
							WHERE Reports.rep_id = Fires.rep_id AND Reports.user_id = @user_id
				);

			/* Delete Foreign Key in Reports */
			DELETE FROM Reports
				WHERE user_id = @user_id;

			/* Delete the User */
			DELETE FROM Users
				WHERE user_id = @user_id;
		END;

/* Delete a User */
EXEC delete_user
	@user_id = 2023590817;

/* Create Procedure to Delete Reports */
CREATE PROCEDURE delete_report(
		@rep_id [numeric] (20)
	) AS
		BEGIN
			/* Delete a Record of Report in EMS */
			DELETE FROM EMS
				WHERE rep_id = @rep_id;

			/* Delete a Record of Report in Security */
			DELETE FROM Security
				WHERE rep_id = @rep_id;

			/* Delete a Record of Report in Fires */
			DELETE FROM Fires
				WHERE rep_id = @rep_id;

			/* Delete Report */
			DELETE FROM Reports
				WHERE rep_id = @rep_id;
		END;

/* Delete Report */
EXEC delete_report
	@rep_id = 3;

/* Create Procedure to Delete Locations */
CREATE PROCEDURE delete_location(
		@loc_id [numeric] (5)
	) AS
		BEGIN
			/* Delete Records of Location in EMS */
			DELETE FROM EMS
				WHERE ems_id IN (
					SELECT EMS.ems_id
						FROM Reports, EMS
							WHERE Reports.rep_id = EMS.rep_id AND Reports.loc_id = @loc_id
				);

			/* Delete Records of Location in Security */
			DELETE FROM Security
				WHERE sec_id IN (
					SELECT Security.sec_id
						FROM Reports, Security
							WHERE Reports.rep_id = Security.rep_id AND Reports.loc_id = @loc_id
				);

			/* Delete Records of Location in Fires */
			DELETE FROM Fires
				WHERE fire_id IN (
					SELECT Fires.fire_id
						FROM Reports, Fires
							WHERE Reports.rep_id = Fires.rep_id AND Reports.loc_id = @loc_id
				);

			/* Delete Foreign Key in Reports */
			DELETE FROM Reports
				WHERE loc_id = @loc_id;
					
			/* Delete the Location */
			DELETE FROM Locations
				WHERE loc_id = @loc_id;
		END;

/* Delete a Location */
EXEC delete_location
	@loc_id = 3;

/* Create Procedure to Delete Buildings */
CREATE PROCEDURE delete_building(
	@loc_bldg [varchar] (30)
	) AS
		BEGIN
			/* Delete Records of Building in EMS */
			DELETE FROM EMS
				WHERE ems_id IN (
					SELECT EMS.ems_id
						FROM Locations, Reports, EMS
							WHERE Locations.loc_bldg = @loc_bldg AND Locations.loc_id = Reports.loc_id AND Reports.rep_id = EMS.rep_id
				);

			/* Delete Records of Building in Security */
			DELETE FROM Security
				WHERE sec_id IN (
					SELECT Security.sec_id
						FROM Locations, Reports, Security
							WHERE Locations.loc_bldg = @loc_bldg AND Locations.loc_id = Reports.loc_id AND Reports.rep_id = Security.rep_id
				);

			/* Delete Records of Building in Fires */
			DELETE FROM Fires
				WHERE fire_id IN (
					SELECT Fires.fire_id
						FROM Locations, Reports, Fires
							WHERE Locations.loc_bldg = @loc_bldg AND Locations.loc_id = Reports.loc_id AND Reports.rep_id = Fires.rep_id
				);

			/* Delete Foreign Key in Reports */
			DELETE FROM Reports
				WHERE rep_id IN (
					SELECT Reports.rep_id
						FROM Locations, Reports
							WHERE Locations.loc_bldg = @loc_bldg AND Locations.loc_id = Reports.loc_id
				);	
			/* Delete the Building */
			DELETE FROM Locations
				WHERE loc_bldg = @loc_bldg;
		END;

/* Delete a Building */
EXEC delete_building
	@loc_bldg = 'CB';

/* Update */
/* Create Procedure to Update Users */
CREATE PROCEDURE update_user (
		@user_id [numeric] (20),
		@user_email [varchar] (50),
		@user_password [varchar] (30),
		@user_authtype [varchar] (30),
		@user_FName [varchar] (30),
		@user_LName [varchar] (30),
		@user_level [varchar] (30),
		@user_grade [varchar] (30),
		@user_phonenum [varchar] (30),
		@user_emerName [varchar] (30),
		@user_emerNum [varchar] (30),
		@user_houseNum [varchar] (30),
		@user_brgy [varchar] (30),
		@user_city [varchar] (30),
		@user_prov [varchar] (30)
	) AS
		UPDATE Users
			SET 
				user_email = @user_email,
				user_password = @user_password,
				user_authtype = @user_authtype,
				user_FName = @user_FName,
				user_LName = @user_LName,
				user_level = @user_level,
				user_grade = @user_grade,
				user_phonenum = @user_phonenum,
				user_emerName = @user_emerName,
				user_emerNum = @user_emerNum,
				user_houseNum = @user_houseNum,
				user_brgy = @user_brgy,
				user_city = @user_city,
				user_prov = @user_prov
			WHERE user_id = @user_id;
			
/* Update User */
EXEC update_user
	@user_id = 2021292832,
	@user_email = 'reinard_christian_ruiz@dlsl.edu.ph',
	@user_password = 'reinera32121',
	@user_authtype = 'admin',
	@user_FName = 'Reinard',
	@user_LName = 'Ruiz',
	@user_level = 'College',
	@user_grade = '3rd Year',
	@user_phonenum = '0912 231 8485',
	@user_emerName = 'Emergency Ruiz',
	@user_emerNum = '0934 485 2315',
	@user_houseNum = '423',
	@user_brgy = 'Upa',
	@user_city = 'Mataasnakahoy',
	@user_prov = 'Batangas';

/* Create Procedure to Update Report Status */
CREATE PROCEDURE update_report_status (
		@rep_id [numeric] (20),
		@rep_status [varchar] (30)
	) AS
		UPDATE Reports
			SET rep_status = @rep_status
			WHERE rep_id = @rep_id;

/* Update Report Status */
EXEC update_report_status
	@rep_id = 21,
	@rep_status = 'Completed';

/* Create Procedure to Update Locations */
CREATE PROCEDURE update_location (
		@loc_id [numeric] (5),
		@loc_bldg [varchar] (30),
		@loc_room [varchar] (30),
		@loc_area [varchar] (30),
		@loc_floor [varchar] (30)
	) AS
		UPDATE Locations
			SET
				loc_bldg = @loc_bldg,
				loc_room = @loc_room,
				loc_area = @loc_area,
				loc_floor = @loc_floor
			WHERE loc_id = @loc_id;

/* Update Location */
EXEC update_location
	@loc_id = 34,
	@loc_bldg = 'GZ',
	@loc_room = '100',
	@loc_area = 'Restroom',
	@loc_floor = '1st Floor'

/* Views ??????? basta eto idk kung alin dito*/
/* Create View to Check Every Report with user first name and last name */
CREATE VIEW user_reports AS
	SELECT Users.user_id, Users.user_FName, Users.user_LName, Reports.rep_type, Reports.rep_status, Reports.rep_date, Reports.rep_time
		FROM Users, Reports
			WHERE Users.user_id = Reports.user_id;

/* Check reports with their user reporters */
SELECT * FROM user_reports;

/* Create View to Check Count of Every Reports */
CREATE VIEW report_type_count AS
	SELECT DISTINCT(rep_type), COUNT(rep_type) AS no_rep
		FROM Reports
			GROUP BY rep_type;
			
/* Check report type counts */
SELECT * FROM report_type_count;

/* Create View to Check Monthly Report with user first name and last name */
CREATE VIEW monthly_report AS
	SELECT Users.user_id, Users.user_FName, Users.user_LName, Reports.rep_type, Reports.rep_status, Reports.rep_date, Reports.rep_time
		FROM Users, Reports
			WHERE Users.user_id = Reports.user_id AND (Reports.rep_date BETWEEN DATEFROMPARTS(YEAR(GETDATE()), MONTH(GETDATE()), 1) AND GETDATE());

/* Check reports with their user reporters */
SELECT * FROM monthly_report;

/* Create Procedure to Check History with User first name, last name and Date Range */
CREATE PROCEDURE history_range (
		@start_date [date],
		@end_date [date]
	) AS
		SELECT Users.user_id, Users.user_FName, Users.user_LName, Reports.rep_type, Reports.rep_status, Reports.rep_date, Reports.rep_time
			FROM Users, Reports
				WHERE Users.user_id = Reports.user_id AND (rep_date BETWEEN @start_date AND @end_date)
					ORDER BY rep_date DESC;

/* Check History with Date Range */
EXEC history_range
	@start_date = '2024-05-14',
	@end_date = '2024-06-23';

/* Create Procedure to Check All User's Report */
CREATE PROCEDURE personal_report (
		@user_id [numeric] (20)
	) AS
		BEGIN
			/* Check Reports in EMS */
			SELECT Users.user_FName, Users.user_LName, Users.user_level, Users.user_grade, Reports.rep_id, Reports.rep_type, Reports.rep_status, Reports.rep_date, Reports.rep_time, EMS.ems_patcon, EMS.ems_conc, EMS.ems_otherinfo
				FROM Users, Reports, EMS
					WHERE Users.user_id = @user_id AND Users.user_id = Reports.user_id AND Reports.rep_id = EMS.rep_id;
			
			/* Check Reports in Security */
			SELECT Users.user_FName, Users.user_LName, Users.user_level, Users.user_grade, Reports.rep_id, Reports.rep_type, Reports.rep_status, Reports.rep_date, Reports.rep_time, Security.sec_conc, Security.sec_otherinfo
				FROM Users, Reports, Security
					WHERE Users.user_id = @user_id AND Users.user_id = Reports.user_id AND Reports.rep_id = Security.rep_id;

			/* Check Reports in Fires */
			SELECT Users.user_FName, Users.user_LName, Users.user_level, Users.user_grade, Reports.rep_id, Reports.rep_type, Reports.rep_status, Reports.rep_date, Reports.rep_time, Fires.fire_otherinfo
				FROM Users, Reports, Fires
					WHERE Users.user_id = @user_id AND Users.user_id = Reports.user_id AND Reports.rep_id = Fires.rep_id;
		END;
		
/* Check All User's Report */
EXEC personal_report
	@user_id = 2022079531;