# Loginscript 
Dies ist ein simpler Loginscript inklusive Registrierung und internem Bereich. Eine ausführliche Dokumentation und Schritt-für-Schritt-Anleitung ist auf [PHP-Einfach - Loginscript](http://www.) zu finden. 

**Download**: [.zip](https://github.com) 

## Installation

```sql
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `passwort` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `vorname` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `nachname` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
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
```


Als nächsten Schritt müsst ihr die Verbindungsdaten zu eurer Datenbank anpassen. Öffnet dazu die `inc/config.inc.php` und tragt dort den Benutzernamen, das Passwort, und den Datenbanknamen eurer MySQL-Datenbank ein.

## Screenshots
### Startseite:
![Index](/screenshots/index.png)

### Registrierung:
![Registrierung](/screenshots/registrierung.png)

### Interner Bereich (nach erfolgreichem Login):
![Interner Bereich](/screenshots/interner_bereich.png)