create database departamentos;
use departamentos;

CREATE TABLE IF NOT EXISTS `departamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(64) NOT NULL,
  `responsable` varchar(64) NOT NULL,
  `cargoResp` varchar(64) DEFAULT NULL,
  `fotoResp` varchar(64) NULL,
  `email` varchar(64) DEFAULT NULL,
  `telefono` varchar(64) NOT NULL,
  `informacion` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;