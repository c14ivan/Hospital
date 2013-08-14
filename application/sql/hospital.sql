DROP TABLE IF EXISTS `h_units`;

CREATE TABLE `h_units` (
  `unitid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`unitid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `h_rooms`;

CREATE TABLE `h_rooms` (
  `roomid` int(11) NOT NULL AUTO_INCREMENT,
  `roomnumber` varchar(45) DEFAULT NULL,
  `roomtype` varchar(20) DEFAULT NULL,
  `unitid` bigint(10) DEFAULT NULL,
  PRIMARY KEY (`roomid`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `h_patient`;

CREATE TABLE `h_patient` (
  `patientid` int(11) NOT NULL AUTO_INCREMENT,
  `patid` varchar(20) DEFAULT NULL,
  `name` varchar(45) DEFAULT NULL,
  `lastname` varchar(45) DEFAULT NULL,
  `gender` varchar(2) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `bornday` datetime DEFAULT NULL,
  `rh` varchar(2) DEFAULT NULL,
  PRIMARY KEY (`patientid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `h_diagnosis`;

CREATE TABLE `h_diagnosis` (
  `diagnosisid` int(11) NOT NULL AUTO_INCREMENT,
  `symptoms` text,
  `treatment` text,
  `doctor` int(11) DEFAULT NULL,
  PRIMARY KEY (`diagnosisid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;