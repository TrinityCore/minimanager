<?php
/*
 * Project Name: MiniManager for Project Trinity
 * License: GNU General Public License v2(GPL)
 * Language: German
 * Supported Minimanager Revision: 66
 * Translator: konqi, Shevonar, mrbungle, maddy
 */

$lang_global = [
  // ----- GENERAL ERROR CODES -----
  'err_sql_conn_db' => 'Fehler - Keiner Verbindung zur Datenbank!',
  'err_sql_open_db' => 'Fehler - Kann Datenbank nicht &ouml;ffnen!',
  'err_no_result' => 'Keine Ergebnisse.',
  'err_no_user' => 'Keine Benutzer gefunden!',
  'err_no_records_found' => 'Keine Eintr&auml;ge gefunden!',
  'err_no_search_passed' => 'Kein Suchwert &uuml;bergeben.',
  'err_invalid_input' => 'Falsche Eingabe',
  'err_no_permission' => 'Sie haben nicht die Erlaubnis, auf diese Daten zu zugreifen oder sie zu bearbeiten',
  // ------ GENERAL -----
  'empty_fields' => 'Es wurden Felder leer gelassen',
  'search' => 'Suchen',
  'limit' => 'maximal',
  'back' => 'zur&uuml;ck',
  'go_back' => 'gehe zur&uuml;ck',
  'home' => 'Home',
  'none' => 'nichts',
  'delete' => 'l&ouml;schen',
  'delete_short' => 'Del.',
  'edit' => 'Bearbeiten',
  'yes' => 'JA',
  'yes_low' => 'Ja',
  'no' => 'NEIN',
  'no_low' => 'Nein',
  'are_you_sure' => 'BIST DU SICHER?',
  'will_be_erased' => 'Wird unwiederruflich aus der Datenbank gelöscht!',
  'unlinked' => 'Unverlinked',
  'country' => 'Land',
  'language_select' => 'W&auml;hle Sprache',
  'language_0' => 'Englisch',
  'language_1' => 'Koreanisch',
  'language_2' => 'Franz&ouml;sisch',
  'language_3' => 'Deutsch',
  'language_4' => 'Chinesisch',
  'language_5' => 'Taiwanesisch',
  'language_6' => 'Spanisch',
  'language_7' => 'Mexikanisch',
  'language_8' => 'Russisch'
];


// ----- VERIFY.PHP -----
function lang_verify()
{
  $lang_verify =
      [
  'verify_failed' => 'Verifizierung fehlgeschlagen!',
  'verify_success' => 'Verifizierung erfolgreich. Du kannst dich jetzt mit deinem Account einloggen!'
      ];
  return $lang_verify;
}
// ----- LOGIN.PHP -----
function lang_login()
{
  $lang_login =
      [
  'login' => 'Einloggen',
  'username' => 'Username',
  'password' => 'Passwort',
  'not_registrated' => 'Nicht registriert?',
  'bad_pass_user' => 'Ung&uuml;ltiger Benutzername und/oder Passwort!',
  'missing_pass_user' => 'Fehlender Benutzername und/oder Passwort!',
  'banned_acc' => 'Account gebannt, Bitte Serveradministrator kontaktieren',
  'locked_acc' => 'Account gesperrt, Bitte Serveradministrator kontaktieren',
  'no_permision' => 'Du hast KEINE Berechtigung auf die angeforderten Daten zuzugreifen',
  'enter_valid_logon' => 'Bitte einen g&uuml;ltigen Benutzernamen und ein g&uuml;ltiges Passwort eingeben:',
  'select_realm' => 'W&auml;hle einen Realm',
  'remember_me' => 'Eingeloggt bleiben',
  'pass_recovery' => 'Passwort vergessen?',
  'verify_required' => 'Dein Account muss verifiziert werden. Bitte &uuml;berpr&uuml;fe deine E-Mails!',
  'after_registration' => 'Ihr Benutzername wurde erfolgreich angelegt!',
      ];
  return $lang_login;
}


// ----- GUILD.PHP -----
function lang_guild()
{
  $lang_guild =
      [
  'my_guilds' => 'Meine Gilde',
  'id' => 'ID',
  'guild_name' => 'Gildenname',
  'guild_leader' => 'Gildenanf&uuml;hrer',
  'guild_faction' => 'Fraktion',
  'tot_m_online' => 'Mitglieder online',
  'guild_motd' => 'Gilden-MoTD',
  'create_date' => 'Erstellunsdatum',
  'show_guilds' => 'Zeige alle Gilden',
  'by_name' => 'nach Name',
  'by_guild_leader' => 'nach Anf&uuml;hrer',
  'by_id' => 'nach ID',
  'browse_guilds' => 'Gilden durchsuchen',
  'tot_members' => 'Mitglieder insgesammt',
  'remove' => 'L&ouml;.',
  'tot_guilds' => 'Gilden insgesamt',
  'guild' => 'Gilde',
  'info' => 'Info',
  'motd' => 'MoTD',
  'name' => 'Name',
  'race' => 'Rasse',
  'class' => 'Klasse',
  'level' => 'Level',
  'rank' => 'Rang',
  'pnote' => 'Spielernotiz',
  'offnote' => 'Notiz',
  'online' => 'online',
  'llogin' => 'Letzter Login (Tage)',
  'del_guild' => 'L&ouml;sche Gilde',
  'guild_id' => 'Gilden-ID',
  'guild_search_result' => 'Gilden-Suchergebnis',
  'guildbank' => 'Gildenbank',
  'members' => 'Members',
      ];
  return $lang_guild;
}


// ----- GUILDBANK.PHP -----
function lang_guildbank()
{
  $lang_guildbank =
      [
  'guild' => 'Gilde',
  'guildbank' => 'Gildenbank',
  'tab' => 'Tab',
  'notfound' => 'Falsche ID, keine Gildenbank gefunden.',
      ];
  return $lang_guildbank;
}


$lang_register = [
  // ----- REGISTER.PHP -----
  'create_acc' => 'Accounterstellung',
  'username' => 'Benutzername',
  'use_eng_chars_limited_len' => 'Nur Buchstaben und Zahlen benutzen (keine Umlaute oder Sonderzeichen). Minimale L&auml;nge: 4 | Maximale L&auml;nge: 14',
  'password' => 'Passwort',
  'confirm_password' => 'Passwort best&auml;tigen',
  'min_pass_len' => 'Minimale L&auml;nge 4 | Maximale L&auml;nge 25',
  'email' => 'E-Mail',
  'use_valid_mail' => 'Bitte versichere dich, dass du eine g&uuml;ltige E-Mail-Adresse eingibst.',
  'create_acc_button' => 'Account erstellen',
  'diff_pass_entered' => 'Die Passwörter müssen übereinstimmen.', //Popup-Fenster mit "normalen" Umlauten!
  'already_exist' => 'existiert bereits',
  'acc_reg_closed' => 'Pech gehabt, die Accounterstellung ist zur Zeit deaktiviert.',
  'wrong_pass_username_size' => 'Du hast die L&auml;nge des Benutzernamens / des Passworts nicht beachtet!',
  'bad_chars_used' => 'Der Benutzername darf nur Buchstaben und Zahlen enthalten!',
  'invalid_email' => 'Bitte eine G&Uuml;LTIGE E-Mail-Adresse angeben!',
  'banned_ip' => 'Deine IP-Addresse ist gebannt.',
  'contact_serv_admin' => 'Kontaktiere den Serveradministrator.',
  'users_ip_range' => 'Benutzer im IP-Bereich',
  'cannot_create_acc' => 'k&ouml;nnen keine Accounts erstellen.',
  'fill_all_fields' => 'Bitte alle Felder ausf&uuml;llen.',
  'acc_type' => 'Spieltyp',
  'acc_type_desc' => 'Welches Addon m&ouml;chtest du aktivieren? Du kannst diese Option sp&auml;ter jederzeit &auml;ndern.',
  'classic' => 'Klassisch',
  'tbc' => 'TBC',
  'wotlk' => 'WotLK',
  'recover_acc_password' => 'Passwortwiederherstellung f&uuml;r einen Account',
  'user_pass_rec_desc' => 'Bitte gib den Benutzernamen an mit dem du dich registriert hast.',
  'mail_pass_rec_desc' => 'Versichere dich, dass du die selbe E-Mail-Adresse wie bei deiner Anmeldung verwendest.',
  'recover_pass' => 'Passwort wiederherstellen',
  'user_mail_not_found' => 'Die angegebene Kombination aus Accountname und E-Mail-Adresse wurde nicht gefunden.',
  'recovery_mail_sent' => 'E-Mail zur Wiederherstellung des Passwortes wurde versandt.',
  'read_terms' => 'Lese, verstehe und akzeptiere bitte die Nutzungsbedingungen, unter denen du deinen Account erstellst',
  'terms' => 'Nutzungsbedingungen',
  'error_terms' => 'Nutzungsbedingungen wurden nicht akzeptiert!',
  'i_agree' => 'Ich bin einverstanden',
  'i_dont_agree' => 'Ich bin nicht einverstanden',
  'pass_too_long' => 'Das angegebene Passwort &uuml;berschreitet die maximal erlaube L&auml;nge',
  'account_needs_verified' => 'That account is already registered and awaiting verification!',   // TODO
  'invited_by' => 'Geworben von',
  'invited_info' => 'Wenn dich ein anderer Spieler geworben hat, gib hier seinen Accountnamen an.',
  'email_address_used' => 'Diese Mailadresse hat schon einen Account.',
  'referrer_not_found' => 'Der Account des Spielers, der dich geworben hat, wurde nicht gefunden.<br/>Stelle bitte sicher, dass du einen g&uuml;ltigen Accountnamen eingegeben hast.',
  'server_code' => 'Server Passphrase',   // TODO
  'server_code_incorrect' => 'Incorrect Server Passphrase. Contact the Administrator',   // TODO
];


// ----- INDEX.PHP -----
function lang_index()
{
  $lang_index =
      [
  'realm' => 'Realm',
  'online' => 'online',
  'offline_or_let_high' => 'offline oder Latenz zu hoch',
  'add_motd' => 'Nachricht des Tages hinzuf&uuml;gen',
  'delete' => 'L&ouml;schen',
  'tot_users_online' => 'Spieler online',
  'name' => 'Name',
  'race' => 'Rasse',
  'class' => 'Klasse',
  'level' => 'Level',
  'map' => 'Karte',
  'zone' => 'Zone',
  'rank' => 'Rang',
  'honor_kills' => 'Kills',
  'latency' => 'Latenzzeit',
  'a_latency' => 'Durchschnittliche Latenzzeit',
  'guild' => 'Gilde',
  'trinity_rev' => 'Trinity Rev',
  'using_db' => 'DB Version:',
  'maxplayers' => 'Max Spieler w&auml;hrend dieser Laufzeit',
      ];
  return $lang_index;
}


// ----- HEADER.PHP -----
function lang_header()
{
$lang_header =
    [
  'menu' => 'Men&uuml;',

  'main' => '&Uuml;bersicht',
  'tools' => 'Werkzeuge',
  'db' => 'Datenbank',
  'system' => 'System',
  'support' => 'Support',
  'my_acc' => 'Profil',

  'auctionhouse' => 'Auktionshaus',
  'arena_teams' => 'Arena Teams',
  'guilds' => 'Gilden',
  'honor' => 'Ehre-Rangliste',
  'top100' => 'TOP 100',
  'statistics' => 'Statistik',
  'player_map' => 'Spielerkarte',

  'accounts' => 'Benutzerkonten',
  'characters' => 'Charaktere',
  'command' => 'Befehle',
  'mail' => 'Nachrichten',
  'tickets' => 'Tickets',
  'banned_list' => 'Bannliste',
  'cleanup' => 'Aufr&auml;umen',

  'events' => 'Ereignisse',
  'instances' => 'Instanz-Informationen',
  'items' => 'Gegenst&auml;nde',
  'creatures' => 'Kreaturen',
  'game_object' => 'Spielobjekte',
  'teleports' => 'Teleport-Punkte',
  'backup' => 'Backup',
  'run_patch' => 'SQL Patch einspielen',
  'repair' => 'Reparieren/Optimieren',

  'realm' => 'Realm',
  'add_motd' => 'MOTD hinzuf&uuml;gen',
  'spelld' => 'Deaktivierte(r) Zauber',
  'message' => 'Nachricht senden',
  'ssh_line' => 'SSH Zeile',

  'forums' => 'Forum',
  'irc' => 'IRC Applet',
  'bugreport' => 'Bug Report',

  // please check the length at 'My Account' Menu
  'realms' => '------Realms------',
  'my_characters' => '----Charaktere----',
  'account' => '--Benutzerkonto--',
  'edit_my_acc' => 'Meine Daten &auml;ndern',
  'logout' => 'Abmelden',
    ];
  return $lang_header;
}


  // -----FOOTER.PHP -----
function lang_footer()
{
  $lang_footer =
      [
  'bugs_to_admin' => 'Probleme und Fehlerberichte an den ',
  'site_admin' => 'Seitenadministrator',
      ];
  return $lang_footer;
}


  // ----- REPAIR.PHP -----
function lang_repair()
{
  $lang_repair =
      [
  'repair_optimize' => 'Tabellen reparieren/optimieren',
  'repair' => 'Reparieren',
  'optimize' => 'Optimieren',
  'start' => 'Start',
  'repair_finished' => 'Reparatur/Optimierung beendet',
  'no_table_selected' => 'Keine Tabelle(n) ausgew&auml;hlt',
  'table_name' => 'Tabellenname',
  'status' => 'Status',
  'num_records' => 'Anzahl der Eintr&auml;ge',
  'tables' => 'Tabellen',
  'select_tables' => 'W&auml;hlen Sie die Tabellen aus, die bearbeitet werden sollen.',
  'repair_error' => 'Fehler',
      ];
  return $lang_repair;
}


$lang_backup = [
  // ----- BACKUP.PHP -----
  'backup_options' => 'Backupoptionen',
  'select_option' => 'W&auml;hle Backupoption',
  'save' => 'Speichern',
  'load' => 'Laden',
  'to_from' => 'auf/in/von/aus',
  'web_backup' => 'dem Webserver',
  'local_file' => 'eine(r) lokale(n) Datei',
  'acc_on_file' => 'einer Datei pro Account',
  'enter_acc_name' => 'Zu ladende ID von Accounts eingeben',
  'backup_acc' => 'Sichere Benutzerkonten',
  'go' => 'Los',
  'save_table_struc_backup' => 'Speichere Tabellenstruktur zus&auml;tzlich zur Datensicherung',
  'select_file' => 'W&auml;hle Datei',
  'max_file_size' => 'Max. Gr&ouml;&szlig;e f&uuml;r Dateiupload',
  'use_ftp_for_large_files' => 'F&uuml;r gr&auml;&szlig;ere Volumen kannst du deine Backupdateien beliebiger Gr&ouml;&szlig;e<br /> per FTP in dein Backupverzeichnis hochladen',
  'upload' => 'Hochladen',
  'upload_sql_file_only' => 'Da kannst nur .sql oder .qbquery Dateien hochladen.',
  'upload_err_write_permission' => 'Konnte Datei nicht hochladen<br />&Uuml;berpr&uuml;fe Verzeichnisberechtigung f&uuml;r',
  'file_not_found' => 'Datei nicht gefunden!',
  'file_write_err' => 'Konnte nicht in Datei schreiben!',
  'backup_finished' => 'Backup erfolgreich beendet',
  'select_backup' => 'W&auml;hle Datenbankdatei',
  'file_loaded' => 'Datei geladen und',
  'que_executed' => 'Abfrage erfolgreich ausgef&uuml;hrt',
  'tables_to_save' => 'Folgende Tabellen werden gesichert',
  'save_all_realms' => 'Speichere Daten von allen Realms',
];


  // ----- BANNED.PHP -----
function lang_banned()
{
  $lang_banned =
      [
  'add_to_banned' => 'Account/IP bannen',
  'tot_banned' => 'Gebannte Accounts/IPs insgesamt',
  'ip_acc' => 'IP / Account',
  'will_be_removed_from_banned' => 'Wird aus der Bannliste gel&ouml;scht.',
  'ban_entry' => 'IP / Account bannen',
  'err_del_entry' => 'Fehler beim L&ouml;schen des Eintrags',
  'updated' => 'Aktualisierung asugef&uuml;hrt!',
  'banned_list' => 'Bannliste',
  'bandate' => 'Banndatum',
  'unbandate' => 'Entbanndatum',
  'bannedby' => 'Gebannt von',
  'banreason' => 'Grund',
  'banned_ips' => 'Gebannte IPs',
  'banned_accounts' => 'Gebannte Accounts',
  'ban_type' => 'Banntyp',
  'account' => 'Account',
  'ip' => 'IP',
  'ban_time' => 'Gebannt f&uuml;r (Stunden)',
  'entry' => 'Eintrag (Accountname / IP)',
  'acc_not_found' => 'Account nicht gefunden',
      ];
return $lang_banned;
}


  // ----- CHAR.PHP -----
function lang_char()
{
    $lang_char =
        [
  'character' => 'Charakter',
  'online' => 'Online',
  'offline' => 'Offline',
  'username' => 'Benutzername',
  'acc_id' => 'Account ID',
  'guild_leader' => 'Gildenleiter',
  'guild' => 'Gilde',
  'rank' => 'Rang',
  'honor_points' => 'Ehre Rang',
  'honor_kills' => 'Siege',
  'exp' => 'Erfahrung',
  'melee_d' => 'Nachkampfschaden',
  'spell_d' => 'Zauberschaden',
  'ranged_d' => 'Fernkampfschaden',
  'melee_hit' => 'Melee Hit',   // TODO
  'spell_hit' => 'Spell Hit',   // TODO
  'ranged_hit' => 'Ranged Hit',   // TODO
  'melee_ap' => 'Nahkampfangriffskraft',
  'spell_heal' => 'Spell Heal',   // TODO
  'ranged_ap' => 'Distanzangriffskraft',
  'expertise' => 'Expertise',   // TODO
  'resilience' => 'Resilience',   // TODO
  'defense' => 'Verteidigung',
  'block' => 'Blocken',
  'dodge' => 'Ausweichen',
  'parry' => 'Parrieren',
  'melee_crit' => 'Kritisch',
  'spell_crit' => 'Zauber Kritisch',
  'ranged_crit' => 'Distanz Kritisch',
  'spell_haste' => 'Tempo Wertung',
  'days' => 'Tage',
  'hours' => 'Stunden',
  'min' => 'Minuten',
  'backpack' => 'Rucksack',
  'gold' => 'Gold',
  'tot_paly_time' => 'Gesamte Spielzeit',
  'chars_acc' => 'Charakter-Account',
  'send_mail' => 'InGame-Nachricht senden',
  'del_char' => 'Charakter l&ouml;schen',
  'no_char_found' => 'Kein Charakter gefunden!',
  'char_sheet' => 'Charakter&uuml;bersicht',
  'inventory' => 'Inventar',
  'reputation' => 'Ruf',
  'pets' => 'Tiere',
  'extra' => 'Extras',
  'spells' => 'Zauber',
  'bank_items' => 'Bankgegenst&auml;nde',
  'quests' => 'Quests',
  'no_act_quests' => 'Keine aktiven Quests gefunden.',
  'quest_id' => 'ID',
  'quest_level' => 'Stufe',
  'quest_title' => 'Quest Titel',
  'rewarded' => 'Rewarded',
  'classskills' => 'Klassen Fertigkeiten',
  'professions' => 'Berufe',
  'secondaryskills' => 'Sekund&auml;re Fertigkeiten',
  'weaponskills' => 'Waffen Fertigkeiten',
  'armorproficiencies' => 'Armor Proficiencies',
  'languages' => 'Sprachen',
  'skills' => 'Fertigkeiten',
  'skill_id' => 'ID',
  'skill_name' => 'Fertigkeit',
  'skill_value' => 'Wert',
  'talents' => 'Talente',
  'showhide' => 'zeigen/verstecken',
  'icon' => 'Icon',
  'quantity' => 'Quantity',
  // ----char_talents.php----
  'talent_rate' => 'Talent Rate',
  'talent_points' => 'Talent Punkte',
  'talent_points_used' => 'Talent Punkte benutzt',
  'talent_points_shown' => 'Talent Points Shown',    // <---- TODO
  'talent_points_left' => 'Talent Punkte verbleibend',
  // --- skill_rank_array ---
  'apprentice' => 'Lehrling',
  'journeyman' => 'Geselle',
  'expert' => 'Experte',
  'artisan' => 'Fachmann',
  'master' => 'Meister',
  'inherent' => 'Inherent', //TODO: Translate
  'wise' => 'Wise', //TODO: Translate
  // -----char_friends.php---
  'friends' => 'Freunde',
  'friendof' => 'Freunde von',
  'ignored' => 'ignoriert',
  'ignoredby' => 'ignoriert von',
  'name' => 'Name',
  'race' => 'Rasse',
  'class' => 'Klasse',
  'level' => 'Level',
  'map' => 'Map',
  'zone' => 'Zone',
  'online' => 'online',
  // ---- edit_char.php ----
  'update' => 'Aktualisiere Charakterdaten',
  'edit_char' => 'Editiere Charkaterdaten',
  'edit_button' => 'Bearbeite Daten',
  'edit_rules' => 'Die Werte sind Charakterbasiswerte ohne Gegenstands/Buffmodifikatoren.',
  'edit_offline_only_char' => ' - Nur Charakter die offline sind k&ouml;nnen bearbeitet werden.',
  'no_permission' => 'Du hast nicht die ben&ouml;tigte Berechtigung um diesen Charakter zu bearbeiten.',
  'err_edit_online_char' => 'Charaktere, die online sind, k&ouml;nnen nicht bearbeitet werden.',
  'updated' => 'Charakterdaten wurden erfolgreich aktualisiert',
  'update_err' => 'Fehler beim aktualisieren der Charakterdaten',
  'use_numeric' => 'Du kannst nicht nicht-nummerische Werte verwenden',
  'check_to_delete' => 'Markiere die Box neben dem Gegenstand, um ihn zu l&ouml;schen.',
  'to_char_view' => 'Gehe zur Charakteransicht',
  'inv_bank' => 'Inventar- und Bankgegenst&auml;nde',
  'location' => 'Ort',
  'move_to' => 'Teleportieren nach (.tele Ortsname)',
  'max_acc' => 'The account you are trying to move this character to has the max ammount of players in it.',   // TODO
  'no_tp_location' => 'Keinen Teleport-Ort mit diesem Namen gefunden.',
  // ---- char_achieve.php ----
  'show' => 'zeige',
  'all' => 'Alle',
  'earned' => 'Erledigt',
  'incomplete' => 'Unvollst&auml;ndige',
  'summary' => 'Zusammenfassung',
  'recent' => 'Neuste',
  'achievements' => 'Erfolge',
  'achievement_title' => 'Erfolgs Titel',
  'achievement_points' => 'Punkte',
  'achievement_date' => 'Datum',
  // ---- char_mail.php ----
  'mail' => 'Mail',
  // ---- char_rep.php ----
  'Alliance' => 'Allianz',
  'Horde' => 'Horde',
  'Alliance Forces' => 'Streitkr&auml;fte der Allianz',
  'Horde Forces' => 'Streitkr&auml;fte der Horde',
  'Steamwheedle Cartel' => 'Dampfdruckkartell',
  'The Burning Crusade' => 'The Burning Crusade',
  'Shattrath City' => 'Shattrath',
  'Alliance Vanguard' => 'Vorposten der Allianz',
  'Horde Expedition' => 'Expedition der Horde',
  'Sholazar Basin' => 'Sholazarbecken',
  'Wrath of the Lich King' => 'Wrath of the Lich King',
  'Other' => 'Andere',
  'Unknown' => 'Unbekannt',
        ];
  return $lang_char;
}


$lang_item = [
  // ----- ITEM TOOLTIP -----
  'head' => 'Kopf',
  'gloves' => 'H&auml;nde',
  'neck' => 'Hals',
  'belt' => 'Taille',
  'shoulder' => 'Schulter',
  'legs' => 'Beine',
  'back' => 'R&uuml;cken',
  'feet' => 'F&uuml;&szlig;e',
  'chest' => 'Brust',
  'finger' => 'Finger',
  'shirt' => 'Hemd',
  'tabard' => 'Wappenrock',
  'trinket' => 'Schmuck',
  'wrist' => 'Handgelenk',
  'main_hand' => 'Waffenhand',  // TODO: check
  'one_hand' => 'Einhand', // TODO: check
  'off_hand' => 'Nebenhand', // TODO: check
  'ranged' => 'Distanz', // TODO: check
  'ammo' => 'Munition',
  'bop' => 'Wird beim Aufheben gebunden',
  'boe' => 'Wird beim Anlegen gebunden',
  'bou' => 'Wird beim Benutzen gebunden',
  'quest_item' => 'Quest Gegenstand',  // TODO: check
  'axe_1h' => 'Axt (Einh&auml;ndig)',// TODO: check
  'axe_2h' => 'Axt (Zweih&auml;ndig)',// TODO: check
  'bow' => 'Bogen',
  'rifle' => 'Gewehr',
  'mace_1h' => 'Streitkolben (Einh&auml;ndig)',// TODO: check
  'mace_2h' => 'Streitkolben (Zweih&auml;ndig)',// TODO: check
  'polearm' => 'Stangenwaffe',
  'sword_1h' => 'Schwert (Einh&auml;ndig)',// TODO: check
  'sword_2h' => 'Schwert (Zweih&auml;ndig)',// TODO: check
  'staff' => 'Stab',// TODO: check
  'exotic_1h' => 'Exotisch (Einh&auml;ndig)',// TODO: check
  'exotic_2h' => 'Exotisch (Zweih&auml;ndig)',// TODO: check
  'fist_weapon' => 'Erste Waffe',// TODO: check
  'misc_weapon' => 'Sonstige Waffe',// TODO: check
  'dagger' => 'Dolch',
  'thrown' => 'Wurfwaffe',
  'spear' => 'Speer',// TODO: check
  'crossbow' => 'Armbrust',
  'wand' => 'Zauberstab',
  'fishing_pole' => 'Angel',
  'rod' => 'Enchanter\'s Rod', // <---- TODO
  'robe' => 'Robe',// TODO: check
  'tome' => 'Tome',  // <----- TODO
  'two_hand' => 'Zweih&auml;ndig',
  'off_misc' => 'Freie Hand',// TODO: check
  'thrown' => 'Wurfwaffe', // double
  'consumable' => 'Essbar',// TODO: check
  'arrows' => 'Projektil - Pfeile',// TODO: check
  'bullets' => 'Projektil - Kugeln',// TODO: check
  'projectile' => 'Projektil',// TODO: check
  'trade_goods' => 'Handelswaren',// TODO: check
  'parts' => 'Teile',// TODO: check
  'explosives' => 'Sprengstoff',// TODO: check
  'devices' => 'Ger&auml;te',// TODO: check
  'book' => 'Buch',// TODO: check
  'recipe' => 'Rezept',// TODO: check
  'LW_pattern' => 'Lederverarbeitungsmuster',// TODO: check
  'tailoring_pattern' => 'Stoffschnittmuster',// TODO: check
  'ENG_Schematic' => 'Maschinenkonstruktionsschema',// TODO: check
  'BS_plans' => 'Schmiedepl&auml;ne',// TODO: check
  'cooking_recipe' => 'Kochrezept',// TODO: check
  'alchemy_recipe' => 'Alchemie Rezept',// TODO: check
  'FA_manual' => 'Erste Hilfe Anleitung',// TODO: check
  'ench_formula' => 'Verzauberungsformel',// TODO: check
  'JC_formula' => 'Jewelcrafting Formula', // <---- TODO
  'quiver' => 'Quiver', // <---- TODO
  'ammo_pouch' => 'Ammo Pouch', // <---- TODO
  'soul_shards' => 'Soul Shards', // <---- TODO
  'herbs' => 'Herbs', // <---- TODO
  'enchanting' => 'Enchanting', // <---- TODO
  'engineering' => 'Engineering', // <---- TODO
  'gems' => 'Gems', // <---- TODO
  'keys' => 'Keys', // <---- TODO
  'mining' => 'Mining', // <---- TODO
  'key' => 'Schl&uuml;ssel',// TODO: check
  'lockpick' => 'Dietrich',// TODO: check
  'weapon' => 'Waffe',// TODO: check
  'reagent' => 'Zutat',// TODO: check
  'quest' => 'Quest',// TODO: check
  'misc_short' => 'Sonstiges',// TODO: check
  'permanent' => 'Permanent',// TODO: check
  'poor' => 'Schlecht',
  'common' => 'Verbreitet',
  'uncommon' => 'Selten',
  'rare' => 'Rar',
  'epic' => 'Episch',
  'legendary' => 'Legend&auml;r',
  'artifact' => 'Artefakt',
  'unique' => 'Einzigartig',
  'misc' => 'Sonstiges',
  'armor' => 'R&uuml;stung',
  'cloth' => 'Stoff',
  'leather' => 'Leder',
  'mail' => 'Schwere R&uuml;stung',
  'plate' => 'Platte',
  'shield' => 'Schild',
  'buckler' => 'Buckler',  // <---- TODO
  'block' => 'Block',  // <---- TODO
  'none' => 'None',  // <---- TODO
  'other' => 'Other',  // <---- TODO
  'damage' => 'Schaden',
  'speed' => 'Geschwindigkeit',
  'holy_dmg' => 'Heiligschaden',
  'fire_dmg' => 'Feuerschaden',
  'nature_dmg' => 'Naturschaden',
  'frost_dmg' => 'Frostschaden',
  'shadow_dmg' => 'Schattenschaden',
  'arcane_dmg' => 'Arkanschaden',
  'physical_dmg' => 'Physisch',
  'lvl_req' => 'Ben&ouml;tigt Stufe',
  'item_set' => 'Gegenstands-Set',
  'bag' => 'Tasche',
  'health' => 'Gesundheit',
  'mana' => 'Mana',
  'energy' => 'Energy',
  'rage' => 'Rage',
  'runic' => 'Runic Power',
  'res_arcane' => 'Arkanwiederstand',
  'res_holy' => 'Heiligwiederstand',
  'res_fire' => 'Feuerwiederstand',
  'res_nature' => 'Naturwiederstand',
  'res_frost' => 'Frostwiederstand',
  'res_shadow' => 'Schattenwiederstand',
  'strength' => 'St&auml;rke',
  'agility' => 'Beweglichkeit',
  'stamina' => 'Ausdauer',
  'intellect' => 'Intelligenz',
  'spirit' => 'Willenskraft',
  'spell_use' => 'Benutzen',
  'spell_equip' => 'Anlegen',
  'spell_coh' => 'Chance On Hit',   // <---- TODO
  'class' => 'Class',   // <---- TODO
  'slots' => 'Slots',   // <---- TODO
  'charges' => 'Charge(s)',   // <---- TODO
  'socket_bonus' => 'Socket Bonus', // <---- TODO
  'potion' => 'Potion', // <---- TODO
  'scroll' => 'Scroll', // <---- TODO
  'bandage' => 'Bandage', // <---- TODO
  'healthstone' => 'HealthStone', // <---- TODO
  'combat_effect' => 'CombatEffect', // <---- TODO
  'libram' => 'Libram', // <---- TODO
  'idol' => 'Idol', // <---- TODO
  'totem' => 'Totem', // <---- TODO
  'fishing_manual' => 'Fishing Manual', // <---- TODO
  'soul_stone' => 'Soul Stone', // <---- TODO
  'no_bind' => 'Nicht Bindend',
  'socket_meta' => 'Metasockel',
  'socket_red' => 'Roter Sockel',
  'socket_yellow' => 'Gelber Sockel',
  'socket_blue' => 'Blauer Sockel',
  'rating_by' => 'um',
  'improves' => 'Erhöht',
  'DEFENCE_RATING' => 'Defence', // <---- TODO
  'DODGE_RATING' => 'Dodge', // <---- TODO
  'PARRY_RATING' => 'Parry', // <---- TODO
  'SHIELD_BLOCK_RATING' => 'Shield Block', // <---- TODO
  'MELEE_HIT_RATING' => 'Melee Hit', // <---- TODO
  'RANGED_HIT_RATING' => 'Ranged Hit', // <---- TODO
  'SPELL_HIT_RATING' => 'Spell Hit', // <---- TODO
  'MELEE_CS_RATING' => 'Melee Crit', // <---- TODO
  'RANGED_CS_RATING' => 'Ranged Crit', // <---- TODO
  'SPELL_CS_RATING' => 'Spell Crit', // <---- TODO
  'MELEE_HA_RATING' => 'Melee Hit Avoid', // <---- TODO
  'RANGED_HA_RATING' => 'Ranged Hit Avoid', // <---- TODO
  'SPELL_HA_RATING' => 'Spell Hit Avoid', // <---- TODO
  'MELEE_CA_RATING' => 'Melee Crit Avoid', // <---- TODO
  'RANGED_CA_RATING' => 'Ranged Crit Avoid', // <---- TODO
  'SPELL_CA_RATING' => 'Spell Crit Avoid', // <---- TODO
  'MELEE_HASTE_RATING' => 'Melee Haste', // <---- TODO
  'RANGED_HASTE_RATING' => 'Ranged Haste', // <---- TODO
  'SPELL_HASTE_RATING' => 'Spell Haste', // <---- TODO
  'HIT_RATING' => 'Hit', // <---- TODO
  'CS_RATING' => 'Critical Strike', // <---- TODO
  'HA_RATING' => 'Hit Avoid', // <---- TODO
  'CA_RATING' => 'Crit Avoid', // <---- TODO
  'RESILIENCE_RATING' => 'Resistance', // <---- TODO
  'HASTE_RATING' => 'Haste' // <---- TODO
];


  // ----- CHAR_LIST.PHP -----
function lang_char_list()
{
  $lang_char_list =
      [
  'account' => 'Account',
  'back_browse_chars' => 'Zur&uuml;ck zum Charakter durchsuchen',
  'browse_chars' => 'Charakter durchsuchen',
  'by_account' => 'nach Account',
  'by_class_id' => 'Klassen ID',
  'by_guild' => 'nach Gilden',
  'by_honor_kills' => '= Rang',
  'by_id' => 'nach ID',
  'by_item' => 'Gegenstands-ID',
  'by_level' => '= Level',
  'by_map_id' => 'nach Karten ID',
  'by_name' => 'nach Name',
  'by_online' => 'Onlinestatus',
  'by_race_id' => 'Rassen ID',
  'char_ids' => 'Charakter ID(s)',
  'char_name' => 'Charakter Name',
  'characters' => 'Charaktere',
  'chars_deleted' => 'Charakter gel&ouml;scht!',
  'chars_gold' => '&lt; Gold (Kupfer)',
  'class' => 'Klasse',
  'cleanup' => 'Aufr&auml;umen',
  'del_selected_chars' => 'Markierte(n) Charakter(e) l&ouml;schen',
  'greater_honor_kills' => '&lt; Rang',
  'greater_level' => '&lt; Level',
  'guild' => 'Gilde',
  'honor_kills' => 'Kills',
  'id' => 'ID',
  'lastseen' => 'zuletzt Online',
  'level' => 'Level',
  'map' => 'Karte',
  'no_chars_del' => 'Keine Charakter gel&ouml;scht!</br>Berechtigung &uuml;berschritten?',
  'online' => 'Online',
  'race' => 'Rasse',
  'search_results' => 'Suchergebnisse',
  'tot_chars' => 'Charaktere Gesamt',
  'total' => 'Gesamt',
  'zone' => 'Zone',
      ];
  return $lang_char_list;
}

$lang_cleanup = [
  // ----- CLEANUP.PHP -----
  'cleanup_options' => 'Aufr&auml;umoptionen',
  'clean_chars' => 'Charakter aufr&auml;umen',
  'char_level' => 'Charakter Level',
  'tot_play_time' => 'Gesamte Spielzeit (sek)',
  'clean_acc' => 'Accounts aufr&auml;umen',
  'last_login_time' => 'Letzter Login (J:M:T)',
  'failed_logins' => 'Fehlgeschlagene Logins',
  'banned' => 'gebannt',
  'locked' => 'gesperrt',
  'chars_in_acc' => 'Charakter in Account',
  'clean_guilds' => 'Gilden aufr&auml;umen',
  'chars_in_guild' => 'Charakter in Gilde',
  'run_cleanup' => 'Aufr&auml;umen!',
  'chars_id' => 'Charakter ID(s)',
  'tot_of' => 'Gesamt',
  'acc_ids' => 'Account ID(s)',
  'guilds_id' => 'Gilde(n) ID(s)',
  'no_guilds_del' => 'Keine Gilde gel&ouml;scht!',
  'total' => 'Gesamt',
  'guilds_deleted' => 'Gilde(n) gel&ouml;scht!',
  'no_acc_chars_deleted' => 'Keine Accounts/Charaktere gel&ouml;scht!<br />Berechtigung &uuml;berschritten?',
  'accs_deleted' => 'Account(s) gel&ouml;scht!',
  'chars_deleted' => 'Charakter gel&ouml;scht!',
  'back_cleaning' => 'Zur&uuml;ck zum Aufr&auml;umen',
  'clean_db' => 'Datenbank aufr&auml;umen',
  'arenateams_deleted' => 'Arena Team(s) gel&ouml;scht!',
  'no_arenateams_del' => 'Arena Team(s) nicht gel&ouml;scht!'
];


  // ----- EDIT.PHP -----
function lang_edit()
{
  $lang_edit =
      [
  'edit_acc' => 'Account bearbeiten',
  'id' => 'ID',
  'username' => 'Benutzername',
  'password' => 'Passwort',
  'mail' => 'Mail',
  'invited_by' => 'Geworben von',
  'gm_level' => 'GM-Level',
  'join_date' => 'Beitrittsdatum',
  'last_ip' => 'Letzte IP',
  'client_type' => 'Spiel-Typ',
  'classic' => 'Klassisch',
  'tbc' => 'TBC',
  'wotlk' => 'WotLK',
  'tot_chars' => 'Gesamtanzahl der Charaktere',
  'characters' => 'Charaktere auf Realm',
  'update' => 'Daten sichern',
  'del_acc' => 'Account l&ouml;schen',
  'theme_options' => 'Theme Optionen',
  'select_theme' => 'Select Theme',
  'language' => 'Sprache',
  'select_layout_lang' => 'W&auml;hle deine Layout Sprache',
  'theme' => 'Theme',
  'use_valid_email' => 'Versuch es mit einer G&Uuml;LTIGEN Email-Addresse!',
  'data_updated' => 'Update erfolgreich ausgef&uuml;hrt!',
  'error_updating' => 'Fehler bei Aktualisierung! (Vielleicht wurde KEINES der Felder ver&auml;ndert?)',
  'del_error' => 'Unerwarteter Fehler beim L&ouml;schen.',
  'edit_your_acc' => 'Account bearbeiten',
  'save' => 'Speichern',
      ];
return $lang_edit;
}


  // ----- MAIL.PHP -----
function lang_mail()
{
  $lang_mail =
      [
  'mail_type' => 'Nachricht-Typ',
  'recipient' => 'Empf&auml;nger',
  'subject' => 'Betreff',
  'email' => 'Email',
  'ingame_mail' => 'WoW-Post',
  'dont_use_both_groupsend_and_to' => '* Hinweis: Wenn du die \'Massenversand\' Option verwendest, versichere dich, dass das \'Empf&auml;nger\' Feld leer ist.',
  'group_send' => 'Massenversand',
  'both' => 'Beides',
  'gm_level' => 'GM Level',
  'locked_accouns' => 'Gesperrte Accounts',
  'banned_accounts' => 'Gebannte Accounts',
  'char_level' => 'Charakter Level',
  'online' => 'Online',
  'attachments' => 'Anhang (nur InGame)',
  'money' => 'Kupfer',
  'item' => 'Gegenstand-ID',
  'stack' => 'Stapel',
  'mail_body' => 'Nachricht',
  'send' => 'senden',
  'mail_sent' => 'Mail erfolgreich gesendet.',
  'mail_err' => 'Mail Fehler',
  'no_recipient_found' => 'Kein Mailempf&auml;nger gefunden.',
  'use_name_or_email' => 'Benutze den Charakternamen f&uuml;r WoW-Post - Email-Adresse f&auml;r Email.',
  'option_unavailable' => 'Du kannst diese option mit der momentanen Konfiguration nicht verwenden.',
  'use_currect_option' => 'Manche der \'Massenversand\' Optionen k&ouml;nnen nur mit  \'WoW-Post\' oder \'Email\' verwendet werden, nicht jedoch mit beidem.',
  'send_mail' => 'Nachrichten verschicken',
  'result' => 'InGame Mail Result',   // TODO
  'mail_on' => 'Ingame Nachricht',
  'sender' => 'Sender',
  'receiver' => 'Empf&auml;nger',
  'has_items' => 'Has Items',   // TODO
  'text' => 'Text',
  'checked' => '&Uuml;berpr&uuml;ft',
  'id' => 'ID',
      ];
  return $lang_mail;
}


  // ----- MOTD.PHP -----
function lang_motd()
{
  $lang_motd =
      [
  'post_motd' => 'Schreibe Nachricht',
  'post_rules' => 'Hinweis: Die L&auml;nge ist auf 255 Zeichen beschr&auml;nkt.<br />HTML kann verwendet werden.',
  'err_max_len' => 'Max. L&auml;nge &uuml;berschritten',
  'add_motd' => 'F&uuml;ge MOTD hinzu',
  'edit_motd' => 'Bearbeite die MOTD',
      ];
   return $lang_motd;
}


  // -----RUN_PATCH.PHP -----
function lang_run_patch()
{
  $lang_run_patch =
      [
  'err_in_line' => 'SQL Syntaxfehler in Abfrage Nummer ',
  'run_sql_file_only' => 'Du kannst nur .sql oder .qbquery Dateien ausf&uuml;hren.',
  'file_not_found' => 'Datei nicht gefunden!',
  'select_sql_file' => 'W&auml;hle zu &ouml;ffnende SQL Datei',
  'max_filesize' => 'Max. Dateigr&ouml;&szlig;e',
  'open' => '&Ouml;ffnen',
  'run_rules' => 'Die folgenden Abfragen werden ausgef&uuml;hrt.<br />Versichere dich, dass jede Abfragezeile mit einem \'&#059\' endet.',
  'select_db' => 'W&auml;hle zu benutzende Standard-DB',
  'run_sql' => '+++ SQL ausf&uuml;hren +++',
  'query_executed' => 'SQL Abfrage erfolgreich ausgef&uuml;hrt.',
  'no_query_found' => 'Null Ergebnis zur&uuml;ckgeliefert / Keine Abfragen gefunden.',
  'run_patch' => 'SQL Patch einspielen',
      ];
  return $lang_run_patch;
}


  // ----- SSH.PHP -----
function lang_ssh()
{
  $lang_ssh =
      [
  'server_offline' => 'SSH/Telnet Server scheint offline zu sein!',
  'config_server_properly' => 'Versichere dich, dass dein SSH/Telnet Server richtig eingerichtet und online ist.'
      ];
  return $lang_ssh;
}


  // ----- REALM.PHP -----
function lang_realm()
{
  $lang_realm =
      [
  'add_realm' => 'Realm hinzuf&uuml;gen',
  'tot_realms' => 'Realms gesamt',
  'name' => 'Name',
  'address' => 'Addresse',
  'port' => 'Port',
  'icon' => 'Typ',
  'color' => 'Farbe',
  'timezone' => 'Zeitzone',
  'edit_realm' => 'Bearbeite Realmdaten',
  'id' => 'ID',
  'update' => 'Daten aktualisieren',
  'realm_id' => 'Realm ID',
  'err_deleting' => 'Fehler beim L&ouml;schen des Realms',
  'update_executed' => 'Aktualisierung erfolgreich!',
  'update_err' => 'Fehler bei Aktualisierung!<br />KEINES der beiden Felder ver&auml;ndert?',
  'realm_data' => 'Realmdaten',
  'online' => 'Status',
  'tot_char' => 'Charaktere gesamt',
  'delete' => 'Entferne Realm',
  'normal' => 'Normal',
  'pvp' => 'PVP',
  'rp' => 'RP',
  'rppvp' => 'RP-PVP',
  'ffapvp' => 'FFA-PVP',
  'development' => 'Entwicklung',
  'united_states' => 'USA',
  'oceanic' => 'Australien',
  'latin_america' => 'Latein Amerika',
  'tournament' => 'Turnier',
  'korea' => 'Korea',
  'english' => 'Englisch',
  'german' => 'Deutsch',
  'french' => 'Franz&ouml;sisch',
  'spanish' => 'Spanisch',
  'russian' => 'Russisch',
  'taiwan' => 'Taiwan',
  'china' => 'China',
  'test_server' => 'Testserver',
  'qa_server' => 'QA Server',
  'others' => 'Andere',
  'conf_from_file' => '** Dieser Realm scheint keine ordentlichen Einstellungen in der config.php zu haben.<br />&Uuml;berprÃ¼fen Sie die Einstellungen, bevor Sie CMS benutzen.',
  'offline' => 'Offline',
  'status' => 'Status',
  'notconfigured' => 'Noch nicht konfiguriert',
      ];
  return $lang_realm;
}


  // ----- TICKET.PHP -----
function lang_ticket()
{
  $lang_ticket =
      [
  'browse_tickets' => 'Tickets durchsuchen',
  'del_selected_tickets' => 'Markierte Tickets l&ouml;schen',
  'edit_reply' => 'Bearbeiten / Antworten',
  'edit_ticked' => 'Ticket bearbeiten',
  'id' => 'ID',
  'send_ingame_mail' => 'InGame-Nachricht senden',
  'sender' => 'Absender',
  'submitted_by' => '&Uuml;bermittelt von',
  'ticked_deleted' => 'Tickets erfolgreich gel&ouml;scht.',
  'ticket_id' => 'Ticket ID',
  'ticket_not_deleted' => 'Keine Tickets gel&ouml;scht!',
  'ticket_text' => 'Text',
  'ticket_update_err' => 'Fehler beim Aktualisieren des Tickets',
  'ticket_updated' => 'Ticket aktualisiert',
  'tot_tickets' => 'Tickets Gesamt',
  'update' => 'Ticket aktualisieren',
      ];
  return $lang_ticket;
}


  // ----- USER.PHP -----
function lang_user()
{
  $lang_user =
      [
  'add_acc' => 'Account erstellen',
  'cleanup' => 'Aufr&auml;men',
  'backup' => 'Backup',
  'by_name' => 'nach Name',
  'by_expansion' => 'nach TBC Acc.',
  'by_id' => 'nach ID',
  'by_gm_level' => '= GM Level',
  'greater_gm_level' => '< GM Level',
  'by_email' => 'nach Email',
  'by_join_date' => 'Beitrittsdatum',
  'by_ip' => 'nach IP',
  'by_failed_loggins' => '< Fehlg. Logins',
  'by_last_login' => 'Letztem Login',
  'by_online' => 'nach Online',
  'by_banned' => 'nach Bann',
  'by_locked' => 'nach Sperrung',
  'id' => 'ID',
  'username' => 'Benutzername',
  'gm_level' => 'GM',
  'email' => 'Mail',
  'join_date' => 'Anmeldedatum',
  'banned' => 'gebannt',
  'banned_reason' => 'Ban Grund',
  'ip' => 'IP',
  'failed_logins' => 'Fehlg.<br />Logins',
  'locked' => 'ge-<br />sperrt',
  'last_login' => 'Letzter Login',
  'online' => 'Online',
  'del_selected_users' => 'Markierte l&ouml;schen',
  'backup_selected_users' => 'Makierte(n) Benutzer sichern',
  'acc_backedup' => 'Accountsicherung erfolgreich abgeschlossen',
  'tot_acc' => 'Accounts Gesamt',
  'user_list' => 'Benutzeriste',
  'tot_found' => 'Funde Gesamt',
  'acc_ids' => 'Account ID(s)',
  'back_browsing' => 'Zur&uuml;ck',
  'no_acc_deleted' => 'Keine Accounts gel&ouml;scht!<br />Fehlende Berechtigung?',
  'total' => 'Insgesamt',
  'acc_deleted' => 'Account(s) gel&ouml;scht!',
  'char_deleted' => 'Charakter(e) gel&ouml;scht!',
  'create_new_acc' => 'Neuen Account erstellen',
  'password' => 'Passwort',
  'confirm' => 'Best&auml;tigen',
  'create_acc' => 'Account erstellen',
  'gm_level_long' => 'GM level',
  'last_ip' => 'Letzte IP',
  'ban_this_ip' => 'Diese IP bannen',
  'failed_logins_long' => 'Fehlgeschlagene Logins',
  'tot_chars' => 'Charakter Gesamt',
  'chars_on_realm' => 'Charakter auf Realm',
  'update_data' => 'Daten aktualisieren',
  'del_acc' => 'L&ouml;sche Account',
  'search_results' => 'Suchergebnisse',
  'acc_creation_failed' => 'Accounterstellung fehlgeschlagen! (leere Felder)',
  'acc_created' => 'Neuen Account erstellt',
  'nonidentical_passes' => 'Du hast zwei unterschiedliche Passw&ouml;rter eingegeben.',
  'user_already_exist' => 'Benutzername existiert bereits.',
  'username_pass_too_long' => 'Benutzername/Passwort muss zwischen 4 und 15 Zeichen lang sein!',
  'use_only_eng_charset' => 'Benutzername darf nur [A-Z][a-z][0-9] beinhalten!',
  'no_value_passed' => 'Kein Wert &uuml;bergeben',
  'edit_acc' => 'Account bearbeiten',
  'update_failed' => 'Update fehlgeschlagen - Hat sich vielleicht kein Feld ver&auml;ndert?',
  'data_updated' => 'Aktualisiert',
  'you_have_no_permission' => 'Du hast keine Berechtigung diese Daten zu &auml;ndern',
  'browse_acc' => 'Accounts durchsuchen',
  'you_have_no_permission_to_set_gmlvl' => 'Du hast keine Berechtigung um diese GM-Level zu setzen',
  'expansion_account' => 'TBC Account',
  'client_type' => 'Clienttyp',
  'classic' => 'Classic',
  'tbc' => 'TBC',
  'wotlk' => 'WotLK',
  'invited_by' => 'eingeladen von',
      ];
  return $lang_user;
}


  // ----- STAT.PHP -----
function lang_stat()
{
  $lang_stat =
      [
  'srv_statistics' => 'Server Statistik',
  'general_info' => 'Allgemeine Informationen',
  'tot_accounts' => 'Accounts insgesamt',
  'total_of' => 'Es gibt insgesamt',
  'gms_one_for' => 'GMs, einen pro',
  'players' => 'Spieler',
  'tot_chars_on_realm' => 'Charaktere auf dem Realm insgesamt',
  'average_of' => 'Durchschnittlich',
  'chars_per_acc' => 'Charaktere pro Account',
  'horde' => 'Horde',
  'alliance' => 'Allianz',
  'chars_by_race' => 'Charakteraufteilung nach Rasse',
  'chars_by_class' => 'Charakteraufteilung nach Klasse',
  'chars_by_level' => 'Charakteraufteilung nach Level',
  'reset' => 'Filter zur&uuml;cksetzen',
  'avg_uptime' => 'Durchschnittliche Serverlaufzeit',
  'max_uptime' => 'Maximale Serverlaufzeit',
  'uptime_prec' => 'Prozent Online seit dem ersten start',
  'acc_24' => 'Letzte 24 Stunden',
  'acc_48' => 'Letzte 48 Stunden',
  'acc_7' => 'Letzte 7 Tage',
  'acc_total' => 'Gesammte Spieler',
  'unique_ip' => 'Einzigartige IPs in den letzten 24 Stunden',
  'unique_ip2' => 'Einzigartige IPs in den letzten 48 Stunden',
  'unique_ipWeek' => 'Einzigartige IPs in den letzten 7 Tagen',
  'on_statistics' => 'Online Statistik',
  'max_players' => 'Maximal Spieler online seit',
  'max_ever' => 'erster Start',
  'max_restart' => 'letzter restart',
      ];
  return $lang_stat;
}


  // ----- TELE.PHP -----
function lang_tele()
{
  $lang_tele =
      [
  'add_new' => 'Hinzuf&uuml;gen',
  'add_new_tele' => 'Teleport-Punkt hinzuf&uuml;gen',
  'delete_tele' => 'L&ouml;schen',
  'edit_tele' => 'Teleport-Punkt bearbeiten',
  'error_updating' => 'Fehler beim Aktualisieren',
  'id' => 'ID',
  'loc_id' => 'Ort ID',
  'loc_name' => 'Ortsame',
  'map' => 'Karte',
  'name' => 'Name',
  'on_map' => 'Auf Karte ID',
  'orientation' => 'Ausrichtung',
  'position_x' => 'Position X',
  'position_y' => 'Position Y',
  'position_z' => 'Position Z',
  'search_results' => 'Suchergebnisse',
  'tele_locations' => 'Teleport-Punkte',
  'tele_updated' => 'Location Updated',
  'teleports' => 'Teleports',
  'tot_locations' => 'Teleport-Punkte insgesamt',
  'update_tele' => 'Teleport-Punkt aktualisieren',
  'x' => 'X',
  'y' => 'Y',
  'z' => 'Z',
      ];
  return $lang_tele;
}


  // ----- COMMAND.PHP -----
function lang_command()
{
  $lang_command =
      [
  'command_list' => 'Verf&uuml;gbare Befehle',
  'level0' => 'Player',
  'level1' => 'Moderator',
  'level2' => 'GameMaster',
  'level3' => 'Bug Tracker',
  'level4' => 'Administrator',
  'level5' => 'Sys OP',
  'level6' => 'Unbekannt',
  'command' => 'Befehl',
  'syntax' => 'Syntax',
  'description' => 'Beschreibung',
  'change_level' => '&Auml;ndere Level der markierten Befehle',
  'save' => 'Speichern',
      ];
  return $lang_command;
}


$lang_item_edit = [
  // ----- ITEM.PHP -----
  'search_item' => 'Nach Gegenst&auml;nden suchen',
  'model_id' => 'Modell ID',
  'all' => 'Alle',
  'search' => '+ Suchen +',
  'add_new_item' => 'Neuen Gegenstand hinzuf&uuml;gen',
  'tot_items_in_db' => 'Gegenst&auml;nde in der Datenbank',
  'new_search' => 'Neue Suche',
  'items_found' => 'Gegenst&auml;nde gefunden',
  'item_not_found' => 'Gegenstand nicht in der Datenbank gefunden',
  'search_results' => 'Suchergebnisse',
  'edit_item' => 'Gegenstand bearbeiten',
  'search_items' => 'Gegenstandssuche',
  'update' => 'Gegenstand in der Datenbank speichern',
  'export_sql' => 'Als SQL-Script speichern',
  'item_id' => 'Gegenstand ID',
  'err_adding_item' => 'Fehler beim hinzuf&uuml;gen des Gegenstands',
  'err_no_field_updated' => 'Keines der Felder aktualisiert.',
  'del_item' => 'Gegenstand l&ouml;schen',
  'general_tab' => 'Allgemein',
  'additional_tab' => 'Extra',
  'stats_tab' => 'Werte',
  'damage_tab' => 'Schaden',
  'spell_tab' => 'Zauber',
  'req_tab' => 'Bedingungen',
  'general' => 'Allgemein',
  'entry' => 'Eintrag',
  'entry_desc' => 'Das ist die einzigartige ID des Gegenstands.',
  'display_id' => 'Modell ID',
  'display_id_desc' => 'Das ist die Modell ID des Gegenstands',
  'req_level' => 'Ben&ouml;tigte Stufe',
  'req_level_desc' => 'Mindeststufe um daen Gegenstand zu benutzen/anzulegen',
  'item_level' => 'Gegenstandsstufe',
  'item_level_desc' => 'Stufe des Gegenstands (Itemlevel)',
  'names' => 'Namen',
  'item_name' => 'Gegenstandsname',
  'item_name_desc' => 'Name des Gegenstands',
  'description' => 'Beschreibung',
  'description_desc' => 'Kurze Beschreibung des Gegenstands, erscheint im Spiel in orange am Ende des Tooltips.',
  'script_name' => 'Script-Name',
  'script_name_desc' => 'Hier k&ouml;nnen programmierte Scripts hizugef&uuml;gt werden.',
  'class' => 'Klasse',
  'class_desc' => 'Definiert die Klasse eines Gegenstands.',
  'type' => 'Typ',
  'subclass' => 'Unterklasse',
  'subclass_desc' => 'Definiert die Unterklasse eines Gegenstands. Hinweis: Die Unterklasse muss zur Klasse des Gegenstands geh&ouml;ren. Manche Klassen haben keine Unterklassen.',
  'quality' => 'Qualit&auml;t',
  'quality_desc' => 'Die allgemeine Qualit&auml;t des Gegenstands.',
  'inv_type' => 'Platz',
  'inv_type_desc' => 'Wo dieser Gegenstand angeleget werden kann.',
  'flags' => 'Kennzeichen',
  'flags_desc' => 'TODO: Beschreibung hinzuf&uuml;gen',
  'item_set' => 'Gegenstandsset',
  'item_set_desc' => 'Die ID eines Sets, zu dem der Gegenstand geh&ouml;rt.',
  'bonding' => 'Bindung',
  'bonding_desc' => 'Art der Bindung.',
  'start_quest' => 'Startet Quest',
  'start_quest_desc' => 'ID einer Quest, die dieser Gegenstand startet.',
  'short_rules_desc' => '* Wenn du die Maus &uuml;ber einen Feldnamen bewegst, erscheint eine kurze Beschreibung.<br />* Stellen Sie sicher, dass Sie alle Felder ausgef&uuml;llt haben und nicht versuchen einen schon vorhandenen Eintrag zu erstellen.',
  'vendor' => 'H&auml;ndler',
  'buy_count' => 'Stapel',
  'buy_count_desc' => 'Gr&ouml;&szlig;e des Stapels, die der H&auml;ndler verkauft.',
  'buy_price' => 'Kaufpreis',
  'buy_price_desc' => 'Preis (in Kupfer) eines Stapels von #Stapel Gegenst&auml;nden.',
  'sell_price' => 'Verkaufspreis',
  'sell_price_desc' => 'Wieviel der H&auml;ndler dem Spieler f&uuml;r den Gegenstand zahlt. Beim freilassen wird der Gegenstand f&uuml;r nichts verkauft (kein Verkaufspreis).',
  'container' => 'Taschen',
  'bag_family' => 'Taschen-Art',
  'bag_family_desc' => 'Art der Tasche.',
  'bag_slots' => 'Taschenpl&auml;tze',
  'bag_slots_desc' => 'Anzahl der Pl&auml;tze, die diese Tasche hat.',
  'materials' => 'Materialien',
  'material' => 'Material',
  'material_desc' => 'Material, aus dem der Gegenstand besteht. Ver&auml;ndert das Ger&auml;usch, wenn der Gegenstand bewegt wird.',
  'consumables' => 'Verbrauchbar',
  'none' => 'Keins',
  'metal' => 'Metall',
  'wood' => 'Holz',
  'liquid' => 'Fl&uuml;ssig',
  'jewelry' => 'Edelstein',
  'chain' => 'Kette',
  'plate' => 'Platte',
  'cloth' => 'Stoff',
  'leather' => 'Leder',
  'page_material' => 'Seitenmaterial',
  'page_material_desc' => 'Der Hintergrund des Seitenfensters (und teilweise die Schrift).',
  'parchment' => 'Pergament',
  'stone' => 'Stein',
  'marble' => 'Marmor',
  'silver' => 'Silber',
  'bronze' => 'Bronze',
  'max_durability' => 'Max. Haltbarkeit',
  'max_durability_desc' => 'Haltbarkeit des Gegenstands.',
  'other' => 'Andere',
  'max_count' => 'Max. Anzahl',
  'max_count_desc' => 'Maximale Anzahl, die der Spieler von diesem Gegenstand haben kann. ( 0:unbegrenzt, 1:einzigartig)',
  'stackable' => 'Stapelbar',
  'stackable_desc' => 'Die Menge von diesem Gegenstand, die der Spieler in einem Taschenplatz tragen kann.',
  'page_text' => 'Seitentext',
  'page_text_desc' => 'ID eines Textes in der item_text Tabelle, der Text eines Buches oder Briefs zum Beispiel. Der Gegenstand wird im Spiel einen Lupen-Zeiger haben und wird den Seitentext nach einem Rechtsklick zeigen.',
  'RandomProperty' => 'Zuf&auml;llige Eigenschaft',
  'RandomProperty_desc' => 'Eintrag der zuf&auml;lligen Verzauberung.',
  'lang_id' => 'Sprach ID',
  'lang_id_desc' => 'Sprache, in der der Gegenstand geschrieben ist.',
  'sheath' => 'Scheide',
  'sheath_desc' => 'Wie die Waffe weggesteckt wird (zur Seite, auf den R&uuml;cken usw.).',
  'lock_id' => 'Lock Id', //TODO
  'lock_id_desc' => 'TODO: Beschreibung hinzuf&uuml;gen.',
  'disenchant_id' => 'Enzauberungs ID',
  'disenchant_id_desc' => 'Bezieht sich auf disenchant_loot_template.entry.',
  'area' => 'Gebiet',
  'area_desc' => 'Gebiet ID, in dem der Gegenstand nur benutzbar ist.',
  'map' => 'Karte',
  'map_desc' => 'Karten ID, in dem der Gegenstand nur benutzbar ist.',
  'stats' => 'Werte',
  'stat_type' => 'Wert-Typ',
  'stat_type_desc' => 'Charakter-Wert, der erh&ouml;ht wird, wenn der Gegenstand angelegt ist.',
  'stat_value' => 'Wert-Gr&ouml;&szlig;e',
  'stat_value_desc' => 'Dieser Wert wird zur Eigenschaft hinzugef&uuml;gt (oder abgezogen, wenn negativ).',
  'resis_armor' => 'Wiederstand / R&uuml;stung',
  'armor_desc' => 'R&uuml;stung dieses Gegenstands.',
  'block_desc' => 'Chance des Schildes einen Angriff zu blocken.',
  'res_holy_desc' => 'Heiligwiederstand dieses Gegenstands.',
  'res_fire_desc' => 'Feuerwiederstand dieses Gegenstands.',
  'res_nature_desc' => 'Naturwiederstand dieses Gegenstands.',
  'res_frost_desc' => 'Frostwiederstand dieses Gegenstands.',
  'res_shadow_desc' => 'Shattenwiederstand dieses Gegenstands.',
  'res_arcane_desc' => 'Arkanwiederstand dieses Gegenstands.',
  'weapon_properties' => 'Waffeneigenschaften',
  'delay' => 'Verz&ouml;gerung',
  'delay_desc' => 'Zeit in Millisekunden zwischen Angriffen.',
  'ranged_mod' => 'Ranged Mod', // TODO
  'ranged_mod_desc' => 'TODO: Beschreibung hinzuf&uuml;gen.',
  'ammo_type' => 'Munitoionsart',
  'ammo_type_desc' => 'Die Art Munition, die die Waffe ben&ouml;tigt.',
  'weapon_damage' => 'Waffenscheden',
  'damage_type' => 'Schadensart',
  'damage_type_desc' => 'Art des Schadens, den dieser Gegenstand zuf&uuml;gt.',
  'dmg_min_max' => 'Schaden : Min. - Max.',
  'dmg_min_max_desc' => 'Minimale und Maximale Schadenswerte.',
  'spell_id' => 'Zauber ID',
  'spell_id_desc' => 'Bezieht sich auf Spell.dbc.',
  'spell_trigger' => 'Zauberausl&ouml;ser',
  'spell_trigger_desc' => 'Die Aktion l&ouml;st diesen Zauber aus.',
  'spell_charges' => 'Zauberaufladungen',
  'spell_charges_desc' => 'Zahl der Aufladungen f&uuml;r diesen Zauber.(0: unbegrenzt, -X: Gegenstand verschwindet, +X: Gegenstand bleibt besthen, wenn alle aufladungen verbraucht sind).',
  'spell_cooldown' => 'Zauber-Abklingzeit',
  'spell_cooldown_desc' => 'Zauber-Abklingzeit in Millisekunden.',
  'spell_category' => 'Zauberkategorie',
  'spell_category_desc' => 'Zauberkategorie.',
  'spell_category_cooldown' => 'Zauberkatgorie-Abklingzeit',
  'spell_category_cooldown_desc' => 'Allgemeine Abklingzeit f&uuml; die gesamte Kategorie.',
  'allow_class' => 'Erlaubte Klassen',
  'allow_class_desc' => 'Charakter-Klassen, die diesen Gegenstand benutzen k&ouml;nnen.',
  'allow_race' => 'Erlaubte Rassen',
  'allow_race_desc' => 'Rassen, die diesen Gegenstand benutzen k&ouml;nnen.',
  'req_skill' => 'Ben&ouml;tigte Fertigkeit',
  'req_skill_desc' => 'Ben&ouml;tigte Fertigkeit, um diesen Gegenstand zu benutzen.',
  'req_skill_rank' => 'Ben&ouml;tigter Fertigkeitswert',
  'req_skill_rank_desc' => 'Minimaler Fertigkeitswert, um den Gegenstand zu benutzen.',
  'req_spell' => 'Ben&ouml;tigter Zauber',
  'req_spell_desc' => 'Spieler muss diesen Zauber k&ouml;nnen, um den Gegenstand zu benutzen.',
  'req_honor_rank' => 'Ben&ouml;tigter Ehrenrang',
  'req_honor_rank_desc' => 'Der ben&ouml;tigte PvP Ehrenrang, um den Gegenstand zu benutzen.',
  'req_rep_faction' => 'Ben&ouml;tigte Fraktion',
  'req_rep_faction_desc' => 'Fraktions ID (aus Faction.dbc), bei der ein Rang n&ouml;tig ist, um den Gegenstand anzlegen/zu benutzen.',
  'req_rep_rank' => 'Ben&ouml;tigter Fraktionsrang',
  'req_rep_rank_desc' => 'Minimaler Rang, der bei der in Ben&ouml;tigte Fraktion angegebenen Fraktion ben&ouml;tigt wird.',
  'req_city_rank' => 'Ben&ouml;tigter Stadtrang',
  'req_city_rank_desc' => 'Ben&ouml;tigter Stadtrang.',
  'hated' => 'Hasserf&uuml;llt',
  'hostile' => 'Feindlich',
  'unfriendly' => 'Unfreundlich',
  'neutral' => 'Neutral',
  'friendly' => 'Freundlich',
  'honored' => 'Wohlwollend',
  'reverted' => 'Respektvoll',
  'exalted' => 'Ehrf&uuml;rchtig',
  'sock_tab' => 'Sockel',
  'req_skill_disenchant' => 'Ben&ouml;tigte Entzauberungsfertigkeit',
  'req_skill_disenchant_desc' => 'Ben&ouml;tigter Verzauberungsfertigkeit, um den Gegenstand zu entzaubern.',
  'RandomSuffix' => 'Zuf&auml;llige Nachsilbe',
  'RandomSuffix_desc' => 'Eintrag der zuf&auml;lligen Verzauberung Nachsilbe.',
  'unk0' => 'unk0', // TODO
  'unk0_desc' => 'TODO: Beschreibung hinzuf&uuml;gen',
  'totem_category' => 'Totem Art',
  'totem_category_desc' => 'Art des Totems. Verkn&uuml;pft mit TotemCategory.dbc',
  'socket_color' => 'Sockelfarbe',
  'socket_color_desc' => 'Farbe dieses Sockels.',
  'socket_content' => 'Sockelinhalt',
  'socket_content_desc' => 'Edelstein, der in diesem Sockel ist.',
  'socket_bonus' => 'Sockelbonus',
  'socket_bonus_desc' => 'Bonus beim richtigen sockeln. Verkn&uuml;pft mit SpellItemEnchantment.dbc',
  'gem_properties' => 'Edelsteineigenschaft',
  'gem_properties_desc' => 'Verkn&uuml;pft mit GemProperties.dbc',
  'custom_search' => 'Eigene Filter',
  'info' => 'Info.',
  'dropped_by' => 'Gegenstand gedroppt von',
  'top_x' => '(Top 5)',
  'sold_by' => 'Gegenstand verkauft von',
  'limit_x' => '(begrenzt auf 5 Ergebnisse)',
  'mob_name' => 'Name',
  'mob_level' => 'Level',
  'mob_drop_chance' => 'Drop Chance',
  'mob_quest_drop_chance' => 'Quest Drop Chance',
  'involved_in_quests' => 'Beteiligt an Quest(s)',
  'reward_from_quest' => 'Belohnung f&uml;r Quest(s)',
  'disenchant_tab' => 'Entzauberung',
  'disenchant_templ' => 'Entzauberungsvorlage',
  'add_items_to_templ' => 'Gegenstand zur Vorlage hinzu&fuuml;gen',
  'loot_item_id' => 'Gegenstand ID',
  'loot_item_id_desc' => 'ID des Gegenstands, der hinzugef&uuml;gt werden soll.',
  'loot_drop_chance' => 'Drop Chance',
  'loot_drop_chance_desc' => 'Gegenstand Drop Chance',
  'loot_quest_drop_chance' => 'Quest Drop Chance',
  'loot_quest_drop_chance_desc' => 'Quest Drop Chance',
  'min_count' => 'Min. Anzahl',
  'min_count_desc' => 'Minimale Anzahl der Stabelgr&ouml;&szlig;e beim Droppen.',
  'max_count' => 'Max. Anzahl',
  'max_count_desc' => 'Maximale Anzahl der Stabelgr&ouml;&szlig;e beim Droppen.',
  'add_item_to_loot' => 'Gegenstand Loot Template',
  'drop_chance' => 'Drop Chance',
  'quest_drop_chance' => 'Quest Drop Chance',
  'armor_dmg_mod' => 'R&uuml;stungs-Schaden-Modifikator',
  'armor_dmg_mod_desc' => 'TODO: Beschreibung hinzuf&uuml;gen',
  'ppm_rate' => 'ppmRate',
  'ppm_rate_desc' => 'Anzahl der Procs pro Minute',
  'item_spell' => 'Gegenstandszauber',
  'freeforall' => 'F&uuml;r alle',
  'freeforall_desc' => 'F&uuml;r alle loot flag.',
  'lootcondition' => 'Loot Bedingung',
  'lootcondition_desc' => 'Loot Bedingung flag',
  'condition_value1' => 'Bedingungswert 1',
  'condition_value1_desc' => 'Bedingungswert 1 flag',
  'condition_value2' => 'Bedingungswert 2',
  'condition_value2_desc' => 'Bedingungswert 2 flag'
];

$lang_creature = [ //TODO
  // ----- CREATURE.PHP -----
  'none' => 'None',
  'custom' => 'Custom',
  'gossip' => 'Gossip',
  'quest_giver' => 'Quest Giver',
  'vendor' => 'Vendor',
  'taxi' => 'Taxi',
  'trainer' => 'Trainer',
  'spirit_healer' => 'Spirit Healer',
  'guard' => 'Guard',
  'inn_keeper' => 'Inn Keeper',
  'banker' => 'Banker',
  'retitioner' => 'Retitioner',
  'tabard_vendor' => 'Tabard Vendor',
  'battlemaster' => 'Battlemaster',
  'auctioneer' => 'Auctioneer',
  'stable_master' => 'Stable Master',
  'armorer' => 'Armorer',
  'normal' => 'Normal',
  'elite' => 'Elite',
  'rare_elite' => 'Rare Elite',
  'world_boss' => 'World Boss',
  'rare' => 'Rare',
  'search_template' => 'Search Creature Template',
  'select' => 'Select',
  'other' => 'Other',
  'beast' => 'Beast',
  'dragonkin' => 'Dragonkin',
  'demon' => 'Demon',
  'elemental' => 'Elemental',
  'giant' => 'Giant',
  'undead' => 'Undead',
  'humanoid' => 'Humanoid',
  'critter' => 'Critter',
  'mechanical' => 'Mechanical',
  'not_specified' => 'Not Specified',
  'class' => 'Class',
  'mounts' => 'Mounts',
  'trade_skill' => 'Trade Skill',
  'pets' => 'Pets',
  'wolf' => 'Wolf',
  'cat' => 'Cat',
  'spider' => 'Spider',
  'bear' => 'Bear',
  'boar' => 'Boar',
  'crocolisk' => 'Crocolisk',
  'carrion_bird' => 'Carrion Bird',
  'crab' => 'Crab',
  'gorilla' => 'Gorilla',
  'raptor' => 'Raptor',
  'tallstrider' => 'Tallstrider',
  'felhunter' => 'Felhunter',
  'voidwalker' => 'Voidwalker',
  'succubus' => 'Succubus',
  'doomguard' => 'Doomguard',
  'scorpid' => 'Scorpid',
  'turtle' => 'Turtle',
  'scorpid' => 'Scorpid',
  'imp' => 'Imp',
  'bat' => 'Bat',
  'hyena' => 'Hyena',
  'owl' => 'Owl',
  'wind_serpent' => 'Wind Serpent',
  'search' => 'Search',
  'new_search' => 'New Search',
  'add_new' => 'Add New Creature',
  'tot_creature_templ' => 'Total Creature Templates',
  'tot_found' => 'Total Found',
  'general' => 'General',
  'stats' => 'Stats',
  'models' => 'Models',
  'additional' => 'Additional',
  'entry' => 'Entry',
  'entry_desc' => 'Creature\'s id.',
  'name' => 'Name',
  'name_desc' => 'Base name of the creature.',
  'faction' => 'Faction',
  'sub_name' => 'SubName',
  'sub_name_desc' => 'Subname of the creature.',
  'script_name' => 'Script Name',
  'script_name_desc' => 'Script\'s name creature uses.',
  'basic_status' => 'Basic Status',
  'level' => 'Level',
  'min_level' => 'Min. Level',
  'min_level_desc' => 'Creature\'s minimum level. Spawned creature have level in range from minlevel to maxlevel.',
  'max_level' => 'Max. Level',
  'max_level_desc' => 'Creature\'s maximum level. Spawned creature have level in range from minlevel to maxlevel.',
  'rank' => 'Rank',
  'rank_desc' => 'Creature\'s honor rank.',
  'health' => 'Health',
  'min_health' => 'Min Health',
  'min_health_desc' => 'Maximum creature\'s health points for creature level equal minlevel. Spawned creature have health in linear proportion to level position in range minlevel - maxlevel.',
  'max_health' => 'Max Health',
  'max_health_desc' => 'Maximum creature\'s health points for creature level equal maxlevel. Spawned creature have health in linear proportion to level position in range minlevel - maxlevel.',
  'min_mana' => 'Min Mana',
  'min_mana_desc' => 'Minimum creature\'s mana points for creature level equal minlevel. Spawned creature have mana in linear proportion to level position in range minlevel - maxlevel.',
  'max_mana' => 'Max Mana',
  'max_mana_desc' => 'Maximum creature\'s mana points for creature level equal minlevel. Spawned creature have mana in linear proportion to level position in range minlevel - maxlevel.',
  'family' => 'Family',
  'family_desc' => 'Creature\'s family type.',
  'type' => 'Type',
  'type_desc' => 'Creature type.',
  'npc_flag' => 'NPC Flag',
  'npc_flag_desc' => 'This is way to cliet know how info you see if you clic(RMB) on NPC if is vendor if is auction in fact is menu how you see Is what type of NPC it is.',
  'trainer_type' => 'Trainer Type',
  'trainer_type_desc' => 'If NPC flag is set to Trainer this flag will specify its type.',
  'loot' => 'Loot',
  'loot_id' => 'Loot Id',
  'loot_id_desc' => 'Refered to field loot_template.entry.',
  'skin_loot' => 'Skin Loot',
  'skin_loot_desc' => 'Type of loot if creature is skinned.',
  'pickpocket_loot' => 'Pickpocket Loot',
  'pickpocket_loot_desc' => 'Refered to field pickpocketing_loot_template.entry.',
  'min_gold' => 'Min Gold',
  'min_gold_desc' => 'Minimum gold drop.',
  'max_gold' => 'Max Gold',
  'max_gold_desc' => 'Maximum gold drop. 0 = creature don\'t drop any gold.',
  'basic_status' => 'Basic Status',
  'armor' => 'Armor',
  'armor_desc' => 'Creature\'s armor.',
  'speed' => 'Speed',
  'speed_desc' => 'Creature\'s speed. Use float values in 0<<3 range.',
  'size' => 'Size',
  'size_desc' => 'Creature model size 1 = 100%. Float  0<<3 range.',
  'damage' => 'Damage',
  'min_damage' => 'Min Damage',
  'min_damage_desc' => 'Creature\'s minimum melee damage.',
  'max_damage' => 'Max Damage',
  'max_damage_desc' => 'Creature\'s maximum melee damage.',
  'attack_power' => 'Attack Power',
  'attack_power_desc' => 'Creature\'s melee attack power.',
  'min_range_dmg' => 'Min Ranged Damage',
  'min_range_dmg_desc' => 'Minimum creature\'s range damage.',
  'max_range_dmg' => 'Max Ranged Damage',
  'max_range_dmg_desc' => 'Maximum creature\'s range damage.',
  'ranged_attack_power' => 'Ranged Attack Power',
  'ranged_attack_power_desc' => 'Creature\'s ranged attack power.',
  'attack_time' => 'Attack Time',
  'attack_time_desc' => 'Time between each creature\'s melee attacks (ms).',
  'range_attack_time' => 'Range Attack Time',
  'range_attack_time_desc' => 'Time between each creature\'s range attacks (ms).',
  'combat_reach' => 'Combat Reach',
  'combat_reach_desc' => 'The distance from the creature can hit you.',
  'bounding_radius' => 'Bounding Radius',
  'bounding_radius_desc' => 'Radius from what player can be attacked.',
  'spells' => 'Spells',
  'spell' => 'Spell',
  'spell_desc' => 'Creature\'s spell.',
  'resistances' => 'Resistances',
  'resis_holy' => 'Holy Resitance',
  'resis_holy_desc' => 'Holy Resitance.',
  'resis_fire' => 'Fire Resitance',
  'resis_fire_desc' => 'Fire Resitance.',
  'resis_nature' => 'Nature Resitance',
  'resis_nature_desc' => 'Nature Resitance.',
  'resis_frost' => 'Frost Resitance',
  'resis_frost_desc' => 'Frost Resitance.',
  'resis_shadow' => 'Shadow Resitance',
  'resis_shadow_desc' => 'Shadow Resitance.',
  'resis_arcane' => 'Arcane Resitance',
  'resis_arcane_desc' => 'Arcane Resitance.',
  'models' => 'Models',
  'scripts' => 'Scripts',
  'ai_name' => 'AIName',
  'ai_name_desc' => 'Name of the AI function creature uses.',
  'movement_type' => 'MovementType',
  'movement_type_desc' => 'TODO.',
  'class' => 'class',
  'class_desc' => 'Creature\'s class, like character.class field. Used for check in case npcflag include trainer flag (16) and trainer_type == TRAINER_TYPE_CLASS or TRAINER_TYPE_PETS.',
  'race' => 'Race',
  'race_desc' => 'Creature\'s race, like character.race field. Used for check in case npcflag include trainer flag (16) and trainer_type == TRAINER_TYPE_MOUNTS.',
  'trainer_spell' => 'Trainer Spell',
  'trainer_spell_desc' => 'Spell ID. Used for check in case npcflag include trainer flag (16) and trainer_type == TRAINER_TYPE_TRADESKILLS. Player must known trainer_spell to start training.',
  'inhabit_type' => 'Inhabit Type',
  'inhabit_type_desc' => 'Movment type.<br />0 - not used<br />1 - can walk (or fly above ground)<br />2 - can swim (or fly above water)<br />3 (= 1 | 2) - can walk and swim (and fly)',
  'walk' => 'Walk',
  'swim' => 'Swim',
  'both' => 'Both',
  'flags_extra' => 'Flags Extra',  //TODO need Check!
  'flags_extra_desc' => 'TODO:',  //TODO need Check!
  'flags' => 'Flags',
  'flags_desc' => 'TODO:',
  'dynamic_flags' => 'Dynamic Flags',
  'dynamic_flags_desc' => 'TODO:',
  'flag_1' => 'Flag 1',
  'flag_1_desc' => 'Mobgroup: If you attack one of this group each mob in the group will aggro you.',
  'save_to_db' => 'Save to Database',
  'save_to_script' => 'Save as SQL Script',
  'lookup_creature' => 'Lookup Creature',
  'quests' => 'Quests',
  'vendor' => 'Vendor',
  'trainer' => 'Trainer',
  'creature_swapned' => 'This Creature spawned total of',
  'times' => 'time(s)',
  'del_creature' => 'Delete Creature',
  'del_spawns' => 'Delete Spawns',
  'loot_tmpl_id' => 'Loot template ID',
  'drop_chance' => 'Drop chance',
  'quest_drop_chance' => 'Quest Drop chance',
  'start_quests' => 'Start Quests',
  'ends_quests' => 'Ends Quests',
  'sells' => 'Sells',
  'unlimited' => 'Unlimited',
  'count' => 'Count',
  'trains' => 'Trains',
  'spell_id' => 'Spell ID',
  'cost' => 'Cost',
  'req_skill' => 'Required Skill',
  'req_skill_lvl' => 'Required Skill level',
  'req_level' => 'Required Level',
  'creature_template' => 'Creature template ID',
  'all_related_data' => 'All related data will be erased as well.',
  'add_new_mob_templ' => 'Add new creature Template',
  'add_new_success' => 'Creature Successfully Added',
  'edit_mob_templ' => 'Edit Creature Template',
  'err_adding_new' => 'Error Adding New Creature',
  'err_no_fields_updated' => 'Non of the fields updated.',
  'search_creatures' => 'Search Creatures',
  'custom_search' => 'Custom Filter',
  'pickpocketloot_tmpl_id' => 'Pickpocket Loot Template ID',
  'skinning_loot_tmpl_id' => 'Skinning Loot Template ID',
  'add_items_to_templ' => 'Add Item to Template',
  'loot_item_id' => 'Item ID',
  'loot_item_id_desc' => 'ID of the item you wish to be added.',
  'loot_drop_chance' => 'Drop Chance',
  'loot_drop_chance_desc' => 'Item Drop chance',
  'loot_quest_drop_chance' => 'Quest Drop Chance',
  'loot_quest_drop_chance_desc' => 'Quest Drop chance',
  'min_count' => 'Min. Count',
  'min_count_desc' => 'Minimum number of stack size on drop.',
  'max_count' => 'Max. Count',
  'max_count_desc' => 'Maximum number of stack size on drop.',
  'add_item_to_loot' => 'Add item to Loot Template',
  'drop_chance' => 'Drop Chance',
  'add_ends_quests' => 'Add Quest ends by this NPC',
  'add_starts_quests' => 'Add Quest starts by this NPC',
  'quest_id' => 'Quest ID',
  'quest_id_desc' => 'ID of the quest.',
  'add_items_to_vendor' => 'Add item to Vendor',
  'vendor_item_id' => 'Item Id',
  'vendor_item_id_desc' => 'Id of item you wish to add.',
  'vendor_max_count' => 'Max. Count',
  'vendor_max_count_desc' => 'Maximim number of items can be soled.',
  'vendor_incrtime' => 'Increase Time',
  'vendor_incrtime_desc' => 'Time before this item can be soled again.',
  'vendor_extended_cost' => 'Extended Cost',
  'vendor_extended_cost_desc' => 'Honor point required to buy. Linked to ItemExtendedCost.dbc',
  'train_spell_id' => 'Spell Id',
  'train_spell_id_desc' => 'Id of the spell you like this trainer to train.',
  'add_spell_to_trainer' => 'Add Spell to Trainer',
  'train_cost' => 'Cost',
  'train_cost_desc' => 'Cost in cooper of this skill.',
  'req_skill' => 'Req. Skill',
  'req_skill_desc' => 'Skill id required to learn this spell.',
  'req_skill_value' => 'Req. Skill Value',
  'req_skill_value_desc' => 'Skill level required to learn this spell.',
  'req_level' => 'Req. Level',
  'req_level_desc' => 'Character level required to learn this spell.',
  'check_to_delete' => '* Check checkbox next to item to remove from template.',
  'search_results' => 'Search Results',
  'RacialLeader' => 'Racial Leader',
  'RacialLeader_desc' => 'Set to 1 if the creature is Racial Leader',
  'dmgschool' => 'Damage School',
  'dmgschool_desc' => 'The school of damage will be used by this mob',
  'freeforall' => 'Free for all',
  'freeforall_desc' => 'Free for all loot flag.',
  'lootcondition' => 'Loot condition',
  'lootcondition_desc' => 'Loot condition flag',
  'condition_value1' => 'Condition Value 1',
  'condition_value1_desc' => 'Condition Value 1 flag',
  'condition_value2' => 'Condition Value 2',
  'condition_value2_desc' => 'Condition Value 2 flag',
  'modelid_A' => 'Model ID Allied',
  'modelid_A_desc' => 'Model ID Allied',
  'modelid_A2' => 'Model ID Allied 2',
  'modelid_A2_desc' => 'Model ID Allied 2',
  'modelid_H' => 'Model ID Horde',
  'modelid_H_desc' => 'Model ID Horde',
  'modelid_H2' => 'Model ID Horde 2',
  'modelid_H2_desc' => 'Model ID Horde 2',
  'faction_A' => 'Faction (Alliance)',
  'faction_A_desc' => 'The faction if the creature is on the alliance side.',
  'faction_H' => 'Faction (Horde)',
  'faction_H_desc' => 'The faction if the creature is on the horde side.',
  'RegenHealth' => 'Regenerate Health',
  'equipment' => 'Equipment',
  'equip_slot' => 'Equip Slot',
  'equip_slot1_desc' => 'Offset to the real slot used',
  'equip_slot2_desc' => 'Offset to the real slot used',
  'equip_slot3_desc' => 'Offset to the real slot used',
  'equip_model' => 'Equip Model',
  'equip_model1_desc' => 'This is the model of the equipment used in right hand.',
  'equip_model2_desc' => 'This is the model of the equipment used in right hand.',
  'equip_model3_desc' => 'This is the model of the equipment used in distance slot.',
  'equip_info' => 'Equip Info',
  'equip_info1_desc' => 'This field controls both the animation, the way the equiped item hits and the sound it makes.',
  'equip_info2_desc' => 'This field controls both the animation, the way the equiped item hits and the sound it makes',
  'equip_info3_desc' => 'This field controls both the animation, the way the equiped item hits and the sound it makes',
  'heroic' => 'Heroische Spawn ID',
  'heroic_desc' => 'Heroische IDs werden vergeben, wenn eine Kreature fu&uml;r Instanzen im normalen und im hoerischen Modus unterschiedliche Vorlagen benutzt. Wird heroic_entry gesetzt, so wird diese Kreatur nur im normalen Modus benutzt. Ein Wert von 0 bedeutet, die Kreatur wird in beiden Fa&uml;llen benutzt, es sei denn eine andere Vorlage zeigt mit seinem heroic_entry Eintrag auf diese Kreatur.',
  'locales' => 'Lokalisierung',
  'unit_flags' => 'Unit flags',
];

$lang_game_object = [ //TODO
  // ----- GAME_OBJECT.PHP -----
  'unknown' => 'Unknown',
  'custom_search' => 'Custom Filter',
  'search' => '+ Search +',
  'add_new' => 'Add New',
  'tot_go_templ' => 'Total Game Object Templates',
  'search_template' => 'Search Game Object Template',
  'select' => 'Select',
  'new_search' => 'New Search',
  'tot_found' => 'Total Templates Found',
  'add_new_go_templ' => 'Add New Game Object Template',
  'edit_go_templ' => 'Edit Game Object Template',
  'err_adding_new' => 'Error Adding New Game Object Template',
  'err_no_fields_updated' => 'Error: No Fields Updated',
  'search_go' => 'Search Game Objects',
  'general' => 'General',
  'save_to_db' => 'Save to DB',
  'save_to_script' => 'Save to Script',
  'lookup_go' => 'Lookup GO',
  'DOOR' => 'DOOR',
  'BUTTON' => 'BUTTON',
  'QUESTGIVER' => 'QUESTGIVER',
  'CHEST' => 'CHEST',
  'BINDER' => 'BINDER',
  'GENERIC' => 'GENERIC',
  'TRAP' => 'TRAP',
  'CHAIR' => 'CHAIR',
  'SPELL_FOCUS' => 'SPELL_FOCUS',
  'TEXT' => 'TEXT',
  'GOOBER' => 'GOOBER',
  'TRANSPORT' => 'TRANSPORT',
  'AREADAMAGE' => 'AREADAMAGE',
  'CAMERA' => 'CAMERA',
  'MAP_OBJECT' => 'MAP_OBJECT',
  'MO_TRANSPORT' => 'MO_TRANSPORT',
  'DUEL_FLAG' => 'DUEL_FLAG',
  'FISHING_BOBBER' => 'FISHING_BOBBER',
  'RITUAL' => 'RITUAL',
  'MAILBOX' => 'MAILBOX',
  'AUCTIONHOUSE' => 'AUCTIONHOUSE',
  'GUARDPOST' => 'GUARDPOST',
  'SPELLCASTER' => 'SPELLCASTER',
  'MEETING_STONE' => 'MEETING_STONE',
  'BG_Flag' => 'BG_Flag',
  'FISHING_HOLE' => 'FISHING_HOLE',
  'FLAGDROP' => 'FLAGDROP',
  'CUSTOM_TELEPORTER' => 'CUSTOM_TELEPORTER',
  'LOTTERY_KIOSK' => 'LOTTERY_KIOSK',
  'CAPTURE_POINT' => 'CAPTURE_POINT',
  'AURA_GENERATOR' => 'AURA_GENERATOR',
  'DUNGEON_DIFFICULTY' => 'DUNGEON_DIFFICULTY',
  'general' => 'General',
  'name' => 'Name',
  'name_desc' => 'Object\'s name.',
  'entry' => 'Entry',
  'entry_desc' => 'Unique GO identifier value',
  'displayId' => 'Display Id',
  'displayId_desc' => 'Graphic model\'s id sent to the client.',
  'faction' => 'Faction',
  'faction_desc' => 'Object\'s faction, if any.',
  'flags' => 'Flags',
  'flags_desc' => 'TODO:',
  'type' => 'Type',
  'type_desc' => 'Game Object\'s type',
  'script_name' => 'ScriptName',
  'ScriptName_desc' => 'Script\'s name GO uses.',
  'size' => 'Size',
  'size_desc' => 'Object\'s size must be set because graphic models can be resample.',
  'tmpl_not_found' => 'Template not Found',
  'del_go' => 'Delete GO',
  'del_spawns' => 'Delete Spawns',
  'loot' => 'Loot',
  'quests' => 'Quests',
  'loot_tmpl_id' => 'Loot Template',
  'drop_chance' => 'Drop Chance',
  'quest_drop_chance' => 'Quest Drop Chance',
  'add_items_to_templ' => 'Add Items to Template',
  'loot_item_id' => 'Loot template ID',
  'loot_item_id_desc' => 'ID of the item you wish to be added.',
  'loot_drop_chance' => 'Drop Chance',
  'loot_drop_chance_desc' => 'Item Drop chance',
  'loot_quest_drop_chance' => 'Quest Drop Chance',
  'loot_quest_drop_chance_desc' => 'Quest Drop chance',
  'min_count' => 'Min. Count',
  'min_count_desc' => 'Minimum number of stack size on drop.',
  'max_count' => 'Max. Count',
  'max_count_desc' => 'Maximum number of stack size on drop.',
  'check_to_delete' => '* Check checkbox next to item to remove from template.',
  'add_starts_quests' => 'Add Quest starts by this GO',
  'quest_id' => 'Quest ID',
  'quest_id_desc' => 'ID of the quest.',
  'start_quests' => 'Start Quests',
  'ends_quests' => 'Ends Quests',
  'add_ends_quests' => 'Add Quest ends by this GO',
  'go_swapned' => 'This Game Object spawned total of',
  'times' => 'times',
  'go_template' => 'Game Object Template',
  'all_related_data' => 'Along with all related data.',
  'freeforall' => 'Free for all',
  'freeforall_desc' => 'Free for all loot flag.',
  'lootcondition' => 'Loot condition',
  'lootcondition_desc' => 'Loot condition flag',
  'condition_value1' => 'Condition Value 1',
  'condition_value1_desc' => 'Condition Value 1 flag',
  'condition_value2' => 'Condition Value 2',
  'condition_value2_desc' => 'Condition Value 2 flag',
  'datas' => 'Additional Datas',
  'data' => 'Data',
  'data_desc' => 'Data fields specific for different type field values. Each type has unique fields.<br />For more informaton visit https://trinitycore.info/index.php/Main_Page'
];


  // ----- AHSTATS.PHP -----
function lang_auctionhouse()
{
  $lang_auctionhouse =
      [
  'auctionhouse' => 'Auktionshaus',
  'seller' => 'Verk&auml;ufer',
  'buyer' => 'K&auml;ufer',
  'item' => 'Gegenstand',
  'buyoutprice' => 'Sofortkauf',
  'timeleft' => 'Restzeit',
  'lastbid' => 'Letztes Gebot',
  'firstbid' => 'Erstes Gebot',
  'dayshortcut' => 'T',
  'hourshortcut' => 'S',
  'mnshortcut' => 'M',
  'total_auctions' => 'Auktionen gesamt',
  'search_results' => 'Suchergebnisse',
  'auction_over' => 'Auktionsende',
  'all' => 'Alle',
  'item_id' => 'Gegenstand-ID',
  'item_name' => 'Gegenstand-Name',
  'seller_name' => 'Verk&auml;ufer',
  'buyer_name' => 'K&auml;ufer',
      ];
  return $lang_auctionhouse;
}


$lang_id_tab = [
  // ----- ID_TAB.PHP -----
  //---RACE---
  'human' => 'Mensch',
  'orc' => 'Orc',
  'dwarf' => 'Zwerg',
  'nightelf' => 'Nachtelf',
  'undead' => 'Untoter',
  'tauren' => 'Taure',
  'gnome' => 'Gnom',
  'troll' => 'Troll',
  'bloodelf' => 'Blutelf',
  'draenei' => 'Draenei',
  //---Class---
  'warrior' => 'Krieger',
  'paladin' => 'Paladin',
  'hunter' => 'J&auml;ger',
  'rogue' => 'Schurke',
  'priest' => 'Priester',
  'death_knight' => 'Todesritter',
  'shaman' => 'Schamane',
  'mage' => 'Magier',
  'warlock' => 'Hexenmeister',
  'druid' => 'Druide',
  //------user levels------
  'Player' => 'Player',
  'Moderator' => 'Moderator',
  'Game_Master' => 'Game Master',
  'BugTracker' => 'Bug Tracker',
  'Administrator' => 'Administrator',
  'SysOP' => 'Sys OP',
  //------factions------
  'Alliance' => 'Allianz',
  'Horde' => 'Horde',
  //------char rankings------
  'None' => 'Kein Rang',
  'Private' => 'Gefreiter',
  'Corporal' => 'Fußknecht',
  'Sergeant' => 'Landsknecht',
  'Master_Sergeant' => 'Feldwebel',
  'Sergeant_Major' => 'F&auml;hnrich',
  'Knight' => 'Leutnant',
  'Knight-Lieutenant' => 'Hauptmann',
  'Knight-Captain' => 'K&uuml;rassier',
  'Knight-Champion' => 'Ritter der Allianz',
  'Lieutenant_Commander' => 'Feldkommandant',
  'Commander' => 'Rittmeister',
  'Marshal' => 'Marschall',
  'Field_Marshal' => 'Feldmarschall',
  'Grand_Marshal' => 'Großmarschall',
  'Scout' => 'Sp&auml;her',
  'Grunt' => 'Grunzer',
  'Sergeant_H' => 'Waffentr&auml;ger',
  'Senior_Sergeant' => 'Schlachtrufer',
  'First_Sergeant' => 'Rottenmeister',
  'Stone_Guard' => 'Steingardist',
  'Blood_Guard' => 'Blutgardist',
  'Legionnare' => 'Zornbringer',
  'Centurion' => 'Klinge der Horde',
  'Champion' => 'Feldherr',
  'Lieutenant_General' => 'Sturmreiter',
  'General' => 'Kriegsherr',
  'Warlord' => 'Kriegsf&uuml;rst',
  'High_Warlord' => 'Oberster Kriegsf&uuml;rst'
];

$lang_arenateam = [
  // ----- ARENATEAM.PHP -----
  'by_name' => 'nach Name',
  'by_team_leader' => 'nach Arena Team Kapit&auml;n',
  'by_id' => 'nach Arena Team ID',
  'id' => 'ID',
  'arenateam_id' => 'Arena Team ID',
  'arenateam_name' => 'Arena Team Name',
  'captain' => 'Kapit&auml;n',
  'type' => 'Typ',
  'arenateam_online' => 'Mitglieder online',
  'create_date' => '...',  // TODO
  '2' => '2 VS 2',
  '3' => '3 VS 3',
  '5' => '5 VS 5',
  'err_no_members_found' => 'Keine Mitglieder gefunden!',
  'err_no_team_found' => 'Keine Arena Teams gefunden!',
  'del_team' => 'Arena Team l&ouml;schen',
  'team_search_result' => 'Arena Teams Suchergebnisse',
  'browse_teams' => 'Arena Teams durchsuchen',
  'tot_teams' => 'Arena Teams insgesamt',
  'members' => 'Mitglieder',
  'tot_members' => 'Mitglieder insgesamt',
  'games' => 'Gespielt',
  'rating' => 'Wertung',
  'wins' => 'Siege',
  'remove' => 'Entfernen',
  'name' => 'Name',
  'level' => 'Level',
  'played_week' => 'Diese Woche gespielt',
  'wons_week' => 'Diese Woche gewonnen',
  'played_season' => 'Diese Saison gespielt',
  'wons_season' => 'Diese Saison gewonnen',
  'arenateams' => 'Arena Teams',
  'del_team' => 'Arena Team l&ouml;schen', // double
  'games_played' => 'Gespielt',
  'games_won' => 'Sieg',
  'games_lost' => 'Verlust',
  'ratio' => 'Gewinnquote',
  'this_week' => 'Diese Woche',
  'this_season' => 'Diese Saison',
  'standings' => 'Stand :',
  'tot_found' => 'Insgesamt gefunden',
  'arenateam' => 'Arena Team'
];

$lang_honor = [
  // ----- HONOR.PHP -----
  'allied' => 'Allianz',
  'horde' => 'Horde',
  'browse_honor' => '&Uuml;bersicht',
  'guid' => 'Charakter',
  'race' => 'Rasse',
  'class' => 'Klasse',
  'level' => 'Level',
  'honor points' => 'Ehre',
  'honor' => 'Rang',
  'guild' => 'Gilde'
];


  // ----- EVENTS.PHP -----
function lang_events()
{
  $lang_events =
      [
  'total' => 'Insgesamt',
  'descr' => 'Ereignis',
  'start' => 'Erstmaliges Erscheinen',
  'occur' => 'Zeitabstand<br/><small>Tage/Stunden</small>',
  'length' => 'Dauer<br/><small>Tage/Stunden</small>',
  'events' => 'Ereignisse'
      ];
  return $lang_events;
}


  // ----- INSTANCES.PHP -----
function lang_instances()
{
  $lang_instances =
      [
  'instances' => 'Instanzen',
  'total' => 'Total',
  'map' => 'Karte',
  'level_min' => 'Mindeststufe',
  'level_max' => 'H&ouml;chststufe',
      ];
  return $lang_instances;
}


  // ----- FORM.PHP -----
function lang_captcha()
{
  $lang_captcha =
      [
  'security_code' => 'Sicherheitscode',
  'invalid_code' => 'Der Code ist ung&uuml;ltig!'
      ];
  return $lang_captcha;
}


  // ----- TOP100.PHP -----   // TODO
function lang_top()
{
  $lang_top =
      [
  'top100' => 'Top 100',
  'general' => 'Allgemein',
  'stats' => 'Stats',
  'defense' => 'Verteidigung',
  'melee' => 'Nahkampf',
  'ranged' => 'Fernkampf',
  'resist' => 'Widerstand',
  'pvp' => 'PvP',
  'health' => 'Gesundheit',
  'mana' => 'Mana',
  'str' => 'Kraft',
  'agi' => 'Beweglichkeit',
  'sta' => 'Ausdauer',
  'intel' => 'Intelligenz',
  'spi' => 'Willenskraft',
  'armor' => 'R&uuml;stung',
  'block' => 'Blocken',
  'dodge' => 'Ausweichen',
  'parry' => 'Parrieren',
  'resilience' => 'Abh&auml;rtung',
  'ap' => 'Angriffskraft',
  'att_time' => 'Att Time',
  'min_dmg' => 'Min Dmg',
  'max_dmg' => 'Max Dmg',
  'expertise' => 'Main Exp',
  'off_expertise' => 'Off Exp',
  'crit' => 'Crit',
  'hit' => 'Hit',
  'holy' => 'Heilig',
  'fire' => 'Fauer',
  'nature' => 'Natur',
  'frost' => 'Frost',
  'shadow' => 'Schatten',
  'arcane' => 'Arkan',
  'name' => 'Name',
  'race' => 'Rasse',
  'class' => 'Klasse',
  'level' => 'Level',
  'guild' => 'Gilde',
  'money' => 'Gold',
  'rank' => 'Rang',
  'honor_points' => 'Ehre',
  'kills' => 'Kills',
  'arena_points' => 'Arena',
  'time_played' => 'Spielzeit',
  'online' => 'Online'
      ];
  return $lang_top;
}


// ----- SPELLD.PHP -----
function lang_spelld()
{
  $lang_spelld =
      [
  'add_spell' => 'Zauber hinzuf&uuml;gen',
  'spell_list' => 'Zauber Liste',
  'by_id' => 'nach Zauber ID',
  'by_disable' => 'nach Maske',
  'by_comment' => 'nach Zaubernamen',
  'entry' => 'Zauber ID',
  'disable_mask' => 'Deaktivierungs Maske',
  'comment' => 'Zaubername',
  'del_selected_spells' => 'Ausgew&auml;hlte(n) Zauber l&ouml;schen',
  'tot_spell' => 'Gesamte Zauber',
  'add_new_spell' => 'Neuen Zauber hinzuf&uuml;gen',
  'entry2' => 'Zauber ID (nur Zahlen)',
  'disable_mask2' => 'Zauber deaktivierungs Maske (Tabelle &uuml;berpr&uuml;fen)',
  'comment2' => 'Zaubername (max 64 Zeichen)',
  'dm_exp' => 'Zauber deaktivierungs Maske - Legt fest, f&uuml;r wen der Zauber deaktiviert ist.',
  'value' => ' Wert ',
  'type' => ' Typ ',
  'enabled' => 'Zauber aktiviert',
  'disabled_p' => ' Zauber f&uuml;r Spieler deaktiviert',
  'disabled_crea_npc' => ' Zauber f&uuml;r Kreaturen/NPC deaktiviert ',
  'disabled_pets' => ' Zauber f&uuml;r Pets deaktiviert ',
  'combinations_hint' => 'Die Werte k&ouml;nnen addiert werden um Kombinationen zu deaktivieren.', //TODO: bessere Übersetzung finden
  'disabled_p_crea_npc' => 'Zauber f&uuml;r Spieler und Kreaturen/NPC deaktiviert',
  'disabled_p_pets' => 'Zauber f&uuml;r Spieler und Pets deaktiviert',
  'disabled_crea_npc_pets' => 'Zauber f&uuml;r Kreaturen/NPC und Pets deaktiviert',
  'disabled_p_crea_npc_pets' => 'Zauber f&uuml;r alle deaktiviert',
  'wrong_fields' => 'Einige Felder sind falsch',
  'err_add_entry' => 'Neuen Zauber hinzuf&uuml;gen fehlgeschlagen',
  'spell_added' => 'Neuer Zauber hinzugef&uuml;gt',
  'spells' => 'Deaktivierte(r) Zauber',
  'search_results' => 'Suchergebnis',
  'spell_deleted' => 'Zauber gel&ouml;scht!',
  'spell_not_deleted' => 'Kein Zauber gel&ouml;scht!',
      ];
  return $lang_spelld;
}


// ----- GLOBAL TELNET -----   // TODO
function lang_telnet()
{
  $lang_telnet =
      [
  'unable' => 'Connect failed: Unable to open network connection, please check your config',
  'unknown_host' => 'Connect failed: Unknown host, please check your config',
  'login_failed' => 'Connect failed: Login failed, please check your config',
  'not_supported' => 'Connect failed: Your PHP version does not support PHP Telnet, please check your config',
      ];
  return $lang_telnet;
}


// ---- message.php ----   // TODO
function lang_message()
{
  $lang_message  =
      [
  'main' => 'Send System Message/Global Notification',
  'announcement' => 'Announcement',
  'notification' => 'Notification',
  'both' => 'Both',
  'send' => 'Send',
  'empty_fields' => 'Empty Fields',
  'message_too_long' => 'Message too long',
  'system_message' => 'System Message',
  'global_notify' => 'Global Notify',
  'sent' => 'sent',
  'message_result' => 'Message Result',
      ];
  return $lang_message;
}


function lang_forum()
{
  global $minfloodtime;

  $lang_forum =
      [
  // BBCode you can replace this by images off course
  "image" => "Bild",
  "url" => "Link",
  "url2" => "Link mit Name",
  "bold" => "Fett",
  "italic" => "Kursiv",
  "underline" => "Unterstrichen",
  "code" => "Code",
  "quote" => "Zitat",
  "quote2" => "Zitat mit Author",
  "wrote" => "schrieb",
  "color" => "Farbe",
  "media" => "Media",
  "YouTube" => "YouTube",

  // forum_index
  "forums" => "Forum",
  "forum_index" => "Foren&uuml;bersicht",
  "you_are_here" => "Du bist hier",
  "last_post_by" => "Letzter Beitrag von",
  "in" => "in", // no use
  "no_topics" => "Keine Threads...",
  "topics" => "Threads",

  // view_forum
  "no_such_forum" => "Fehler: Kein solches Forum",
  "no_access" => "Fehler: Du hast keinen Zugang zu diesem Inhalt",
  "new_topic" => "Neues Thema",
  "annoucement" => "Ank&uuml;ndigung",
  "sticky" => "Wichtig",
  "pages" => "Seiten",
  "title" => "Threadtitel",
  "author" => "Author",
  "pages" => "Seiten", // double
  "replies" => "Antworten",
  "last_post" => "Letzter Beitrag",
  "closed" => "Geschlossen",

  // view_topic
  "no_such_topic" => "Fehler: Kein solcher Thread",
  "info" => "Info",
  "text" => "Text",
  "yes" => "Ja",
  "no" => "Nein",
  "at" => "um",
  "move" => "Verschieben",
  "edit" => "Bearbeiten",
  "delete" => "L&ouml;schen",
  "post" => "Posten",
  "open" => "&Ouml;ffnen",
  "close" => "Schlie&szlig;en",
  "quick_reply" => "Schnelle Antwort:",
  "down" => "Nach unten",
  "up" => "Nach oben",
  "normal" => "Normal",

  // delete_post
  "delete_topic" => "M&ouml;chtest du wirklich diesen Thread und alle Beitr&auml;ge darin l&ouml;schen?",
  "delete_post" => "Willst du diesen Beitrag wirklich l&ouml;schen?",
  "back" => "Zur&uuml;ck",
  "confirm" => "Best&auml;tigen",

  // add_topic
  "topic_name" => "Threadtitel",
  "please_wait" => "Bitte warte $minfloodtime Sekunden, bevor du wieder postst.",

  // do_add_topic
  "name_too_long" => "Der Threadtitel ist zu lang!",
  "name_too_short" => "Der Threadtitel ist zu kurz!",
  "msg_too_short" => "Zu kurze Nachricht!",

  // edit_post
  "no_such_post" => "Fehler: Kein solcher Beitrag",

  // move_topic
  "where" => "Wohin willst du diesen Thread verschieben?",
      ];
  return $lang_forum;
}


?>
