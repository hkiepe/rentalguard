--
-- Tabellenstruktur
--
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `passwort` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `vorname` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' ,
  `nachname` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' ,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  `passwortcode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `passwortcode_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`), UNIQUE (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `securitytokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` int(10) NOT NULL,
  `identifier` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `securitytoken` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tabelle der Verleihvorgänge
CREATE TABLE IF NOT EXISTS `rentals` (
  `re_id` int(10) unsigned NOT NULL AUTO_INCREMENT, -- eindeutige ID des Verleihvorgangs
  `re_unique_id` int(10) unsigned NOT NULL, -- ID des Verleihvorgangs
  `re_handle` varchar(255) COLLATE utf8_unicode_ci NOT NULL, -- ID des Fahrzeuges
  `rp_id` int(10) unsigned NOT NULL, -- ID des Verleihpunktes auf dem ausgeliegen wurde
  `cl_id` int(10) unsigned NOT NULL, -- ID des Kunden, der das Fahrzeug ausgeliehen hat.
  `price` int(10) unsigned NOT NULL, -- Preis des Fahrzeuges.
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`re_id`), UNIQUE (`re_handle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tabelle der temporären Verleihvorgänge
CREATE TABLE IF NOT EXISTS `tmp_rentals` (
  `tmp_id` int(10) unsigned NOT NULL AUTO_INCREMENT, -- eindeutige ID der einträge des temporären Verleihvorgangs
  `tmp_re_handle` varchar(255) COLLATE utf8_unicode_ci NOT NULL, -- ID des Fahrzeuges
  `tmp_rp_id` int(10) unsigned NOT NULL, -- ID des Verleihpunktes auf dem ausgeliehen wurde
  `tmp_cl_id` int(10) unsigned NOT NULL, -- ID des Kunden, der das Fahrzeug ausgeliehen hat.
  `tmp_price` int(10) unsigned NOT NULL, -- Preis des Fahrzeuges.
  `tmp_created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`tmp_id`), UNIQUE (`tmp_re_handle`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Liste der Verleihpunkte
CREATE TABLE IF NOT EXISTS `rentalpoints` (
  `rp_id` int(10) unsigned NOT NULL AUTO_INCREMENT, -- eindeutige ID des Verleihpunktes
  `rp_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL, -- Name des Verleihpunktes
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`rp_id`), UNIQUE (`rp_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Liste der Fahrzeuge
CREATE TABLE IF NOT EXISTS `vehicles` (
  `vh_id` int(10) unsigned NOT NULL AUTO_INCREMENT, -- eindeutige ID des Fahrzeuges als Fremdschlüssel
  `vh_handle` varchar(255) COLLATE utf8_unicode_ci NOT NULL, -- eindeutige Kurzbezeichnung des Fahrzeuges
  `vh_rp_id` int(10) unsigned NOT NULL, -- eindeutige Zuweisung des Verleihpunktes an dem sich das Fahrzeug gerade befindet
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`vh_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Liste der Kunden
CREATE TABLE IF NOT EXISTS `clients` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `prename` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' ,
  `surenam` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' ,
  `tel` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' ,
  `pesel` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' ,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
  PRIMARY KEY (`id`), UNIQUE (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Testdaten
--
INSERT INTO rentalpoints ( rp_name ) VALUES
   ( 'Sopot' ),
   ( 'Brzezno' ),
   ( 'Jelitkowa' );

INSERT INTO `vehicles` ( vh_handle, vh_rp_id ) VALUES
  ( 'RD-1', 1 ),
  ( 'RD-2', 2 ),
  ( 'RM-3', 3 ),
  ( 'RD-4', 1 ),
  ( 'RD-5', 2 ),
  ( 'RD-6', 3 ),
  ( 'RD-7', 1 ),
  ( 'RD-8', 2 ),
  ( 'RM-1', 3 ),
  ( 'RM-2', 1 ),
  ( 'RM-3', 2 ),
  ( 'RM-4', 3 ),
  ( 'RM-5', 1 ),
  ( 'RM-6', 2 ),
  ( 'RM-7', 3 ),
  ( 'RM-8', 1 );

INSERT INTO `rentals` ( re_handle ) VALUES
  ( 'RD-1' ),
  ( 'RD-2' ),
  ( 'RD-3' ),
  ( 'RD-4' ),
  ( 'RD-5' ),
  ( 'RM-1' ),
  ( 'RM-2' ),
  ( 'RM-3' ),
  ( 'RM-4' );