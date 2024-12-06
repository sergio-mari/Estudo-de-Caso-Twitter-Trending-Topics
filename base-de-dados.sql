CREATE TABLE `trending_collects_brazil` (
  `id_trend` int NOT NULL AUTO_INCREMENT,
  `capture_date` timestamp NULL DEFAULT NULL,
  `term` varchar(45) DEFAULT NULL,
  `position` int DEFAULT NULL,
  `volume` int DEFAULT NULL,
  PRIMARY KEY (`id_trend`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `trending_collects_world` (
  `id_trend` int NOT NULL AUTO_INCREMENT,
  `capture_date` timestamp NULL DEFAULT NULL,
  `term` varchar(45) DEFAULT NULL,
  `position` int DEFAULT NULL,
  `volume` int DEFAULT NULL,
  PRIMARY KEY (`id_trend`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `trending_terms` (
  `term` varchar(45) NOT NULL,
  `url` varchar(200) DEFAULT NULL,
  `type` varchar(45) DEFAULT NULL,
  `context` varchar(200) DEFAULT NULL,
  `words` int DEFAULT NULL,
  `letters` int DEFAULT NULL,
  `date_inc` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`term`),
  UNIQUE KEY `term_UNIQUE` (`term`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
