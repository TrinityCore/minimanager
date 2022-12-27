<?php


//#############################################################################
//
// configuration note.
//
// Do not edit, move or delete this file.
//
// Option 1 (recommended)
//  Copy config.user.php as config.php.
//  Copy only the settings you want to change into config.php
//  Make changes there.
//
// Option 2
//  Copy this file as config.php,
//  Make changes there.


//#############################################################################
//---- Version Information ----

$show_version['show']        =  '2';    // 0 - Don't Show, 1 - Show Version, 2 - Show Version and SVN Revision
$show_version['version']     =  '0.2 - Development';
$show_version['version_lvl'] = '-1';    // Minimum account level to show Version to, -1 is guest account
$show_version['svnrev']      =  '0';    // SVN Revision will be read from .svn folder, values here hold no meaning or effect
$show_version['svnrev_lvl']  =  '5';    // Minimum account level to show SVN Revision to.


//#############################################################################
//---- SQL Configuration ----
//
//  SQL server type  :
//  'MySQL'   - Mysql (Do not use it. You will have SQL attacks!)
//  'PgSQL'   - PostgreSQL
//  'MySQLi'  - MySQLi
//  'SQLLite' - SQLLite

$db_type          = 'MySQLi';

// only Creature, Item and Game Object uses this setting, the rest uses $itemperpage.
// $itemperpage setting is lower down at Layout configuration.
$sql_search_limit =  100;                         // limit number of maximum search results

$mmfpm_db['addr']     = '127.0.0.1:3306';         // SQL server IP:port this DB located on, use `mysql` if local docker
$mmfpm_db['user']     = 'root';                   // SQL server login this DB located on
$mmfpm_db['pass']     = '1';                      // SQL server pass this DB located on
$mmfpm_db['name']     = 'mmfpm';                  // MiniManager DB name
$mmfpm_db['encoding'] = 'utf8';                   // SQL connection encoding

$realm_db['addr']     = '127.0.0.1:3306';         // SQL server IP:port this realmd located on
$realm_db['user']     = 'root';                   // SQL server login this realmd located on
$realm_db['pass']     = '1';                      // SQL server pass this realmd located on
$realm_db['name']     = 'auth';                   // realmd DB name
$realm_db['encoding'] = 'utf8';                   // SQL connection encoding

          // position in array must represent realmd ID
$world_db[1]['addr']          = '127.0.0.1:3306'; // SQL server IP:port this DB located on
$world_db[1]['user']          = 'root';           // SQL server login this DB located on
$world_db[1]['pass']          = '1';              // SQL server pass this DB located on
$world_db[1]['name']          = 'world';         // World Database name, by default "world"
$world_db[1]['encoding']      = 'utf8';           // SQL connection encoding

               // position in array must represent realmd ID
$characters_db[1]['addr']     = '127.0.0.1:3306'; // SQL server IP:port this DB located on
$characters_db[1]['user']     = 'root';           // SQL server login this DB located on
$characters_db[1]['pass']     = '1';              // SQL server pass this DB located on
$characters_db[1]['name']     = 'characters';     // Character Database name
$characters_db[1]['encoding'] = 'utf8';           // SQL connection encoding

/* Sample Second Realm config
          // position in array must represent realmd ID
$world_db[2]['addr']          = '127.0.0.1:3306'; // SQL server IP:port this DB located on
$world_db[2]['user']          = 'root';           // SQL server login this DB located on
$world_db[2]['pass']          = '1';              // SQL server pass this DB located on
$world_db[2]['name']          = 'world';          // World Database name, by default "world"
$world_db[2]['encoding']      = 'utf8';           // SQL connection encoding
               // position in array must represent realmd ID
$characters_db[2]['addr']     = '127.0.0.1:3306'; // SQL server IP:port this DB located on
$characters_db[2]['user']     = 'root';           // SQL server login this DB located on
$characters_db[2]['pass']     = '1';              // SQL server pass this DB located on
$characters_db[2]['name']     = 'characters';     // Character Database name
$characters_db[2]['encoding'] = 'utf8';           // SQL connection encoding
*/


//#############################################################################
//---- Game Server Configuration ----

        // position in array must represent realmd ID, same as in $world_db
$server[1]['addr']          = '127.0.0.1'; // Game Server IP, as seen by MiniManager, from your webhost
$server[1]['addr_wan']      = '127.0.0.1'; // Game Server IP, as seen by clients - Must be external address
$server[1]['game_port']     =  8085;       // Game Server port
$server[1]['term_type']     = 'SSH';       // Terminal type - ("SSH"/"Telnet")
$server[1]['term_port']     =  22;         // Terminal port
$server[1]['telnet_port']   =  3443;       // Telnet port - Telnet settins are needed for sending InGame Mail.
$server[1]['telnet_user']   = 'USER';      // Telnet username, must be all CAPS
$server[1]['telnet_pass']   = 'pass';      // Telnet password
$server[1]['both_factions'] =  true;       // Allow to see opponent faction characters. Affects only players.
$server[1]['talent_rate']   =  1;          // Talent rate set for this server, needed for talent point calculation

//---- SOAP Protocol Settings ---
$enable_soap                =  0;          // Enable if you use SOAP protocol on your realm. This will work only if you have 'php-soap' installed
$soapaddr                   = '0.0.0.0';   // Game Server IP, as seen by MiniManager, from your webhost
$soap_port                  =  7878;       // SOAP Port
$soap_user                  = 'SOAP_USER'; // SOAP Username
$soap_pass                  = 'SOAP_PASS'; // SOAP Password


/* Sample Second Realm config
        // position in array must represent realmd ID, same as in $world_db
$server[2]['addr']          = '127.0.0.1'; // Game Server IP, as seen by MiniManager, from your webhost
$server[2]['addr_wan']      = '127.0.0.1'; // Game Server IP, as seen by clients - Must be external address
$server[2]['game_port']     =  8085;       // Game Server port
$server[2]['term_type']     = 'SSH';       // Terminal type - ("SSH"/"Telnet")
$server[2]['term_port']     =  22;         // Terminal port
$server[2]['telnet_port']   =  3443;       // Telnet port - Telnet settins are needed for sending InGame Mail.
$server[2]['telnet_user']   = 'USER';      // Telnet username, must be all CAPS
$server[2]['telnet_pass']   = 'pass';      // Telnet password
$server[2]['both_factions'] =  true;       // Allow to see opponent faction characters. Affects only players.
$server[2]['talent_rate']   =  1;          // Talent rate set for this server, needed for talent point calculation
*/


//#############################################################################
//---- Mail configuration ----

$admin_mail  = 'mail@mail.com';      // mail used for bug reports and other user contact
$mailer_type = 'smtp';               // type of mailer to be used("mail", "sendmail", "smtp")
$from_mail   = 'mail@mail.com';      // all emails will be sent from this email

//smtp server config
$smtp_cfg['host'] = 'smtp.mail.com'; // smtp server
$smtp_cfg['port'] = 25;              // port
$smtp_cfg['sec']  = "";				 // SMTP Security, "tls", "ssl" or "" possible
$smtp_cfg['user'] = '';              // username - use only if auth. required
$smtp_cfg['pass'] = '';              // pass


//#############################################################################
//---- IRC Options ------

$irc_cfg['server']  = 'irc.rizon.net'; // irc server
$irc_cfg['port']    =  6667;            // port
$irc_cfg['channel'] = 'Trinityscripts';    // channel


//#############################################################################
//---- HTTP Proxy Configuration ----
// configure only if requierd

$proxy_cfg['addr'] = '';
$proxy_cfg['port'] = 80;
$proxy_cfg['user'] = '';
$proxy_cfg['pass'] = '';


//#############################################################################
//---- External Links ----

$tooltip_script             = 'https://wow.zamimg.com/widgets/power.js';
$tooltip_url                = 'https://www.wowhead.com';
$item_datasite              = $tooltip_url . '/?item=';
$quest_datasite             = $tooltip_url . '/?quest=';
$creature_datasite          = $tooltip_url . '/?npc=';
$spell_datasite             = $tooltip_url . '/?spell=';
$skill_datasite             = $tooltip_url . '/?spells=';
$go_datasite                = $tooltip_url . '/?object=';
$achievement_datasite       = $tooltip_url . '/?achievement=';
$talent_calculator_datasite = 'https://www.wowarmory.com/talent-calc.xml?cid=';

$get_icons_from_web         =  false;      // wherever to get icons from the web.
$item_icons                 = 'img/icons'; // and this is where it will save to and get from.


//#############################################################################
//---- New account creation Options ----

$disable_acc_creation  = false;     // true = Do not allow new accounts to be created
$enable_server_code    = false;     // Enables additional passphrase that must be entered during registration
$server_code           = 'trinity'; // if above is true, this is the passphrase that must be entered during registration
$expansion_select      = true;      // true = Shows option to select expansion or classic. (false = no option, WOTLK enabled by default)
$defaultoption         = 2;         // if the above is false then set what the default option will be (2 = WOTLK, 1 = TBC, 0 = Classic)
$enable_captcha        = false;     // false = no security image check (enable for protection against 'bot' registrations)
$captcha_char_limit    = 6;         // Defines the number of characters your captcha will have
$captcha_width         = 400;       // Captcha window width
$captcha_height        = 80;        //Captcha window height 
                                    // captcha needs php GD & FreeType Library support
$send_mail_on_creation = false;     // true = send mail at account creation.
$create_acc_locked     = 0;         // if set to '1' newly created accounts will be made locked to registered IP, disallowing user to login from other IPs.
$validate_mail_host    = false;     // actualy make sure the mail host provided in email is valid/accessible host.
$require_account_verify = false;    // If set to true, an email will be sent to registered email address requiring verification before account creation
$limit_acc_per_ip      = false;     // true = limit to one account per IP
$simple_register       = false;     // Sets the registration to a simple form. Name, Password, Expansion and Email.

// this option will limit account creation to users from selected net range(s).
// allow all => empty array
// e.g: "120-122.55.255-0.255-0"

$valid_ip_mask = [];
/* Sample config, you may have more then 1
$valid_ip_mask[0] = '255-0.255-0.255-0.255-0';
$valid_ip_mask[1] = '120-122.55.255-0.255-0';
$valid_ip_mask[2] = '190.50.33-16.255-0';
*/


//#############################################################################
//---- Login Options ----

$remember_me_checked  = false;      // "Remember Me" cookie check box default, false = unchecked

$allow_anony         =  true;       // allow anonymouse login, aka guest account
$anony_uname         = 'Guest';     // guest account name, this is purely cosmetic
$anony_realm_id      =  1;          // guest account default realm

// permission level for guest access is -1
// set it like how you set any page permission level in menu config below, using the value -1
// the "Guest" account exists only in MiniManager, not in your realms or server or database


//#############################################################################
//---- Layout configuration ----

$title               = 'MiniManager for TrinityCore';
$itemperpage         =  25;
$showcountryflag     =  true;

$theme               = 'Sulfur';    // file/folder name of theme to use from themes directory by default
$language            = 'english';   // default site language
$timezone            = 'UTC';       // default timezone (use your local timezone code) https://www.php.net/manual/en/timezones.php
$gm_online           = '1';         // display GM Characters in the Online Character List and Player Map (1 = enable, 0 = disable)
$gm_online_count     = '1';         // include GM Characters in the Online User Count and Player Map (1 = enable, 0 = disable)
$motd_display_poster = '1';         // display the poserter info in the MOTD (1 = enable, 0 = disable)


//#############################################################################
//---- Player Map configuration ----

// GM online options
$map_gm_show_online_only_gmoff     = 0; // show GM point only if in '.gm off' [1/0]
$map_gm_show_online_only_gmvisible = 0; // show GM point only if in '.gm visible on' [1/0]
$map_gm_add_suffix                 = 1; // add '{GM}' to name [1/0]
$map_status_gm_include_all         = 1; // include 'all GMs in game'/'who on map' [1/0]

// status window options:
$map_show_status =  1;                  // show server status window [1/0]
$map_show_time   =  1;                  // Show autoupdate timer 1 - on, 0 - off
$map_time        = 24;                  // Map autoupdate time (seconds), 0 - not update.

// all times set in msec (do not set time < 1500 for show), 0 to disable.
$map_time_to_show_uptime    = 3000;     // time to show uptime string
$map_time_to_show_maxonline = 3000;     // time to show max online
$map_time_to_show_gmonline  = 3000;     // time to show GM online


//#############################################################################
//---- Active Translations
// 0 = English/Default; 1 = Korean; 2 = French; 4 = German; 8 = Chinese; 16 = Taiwanese; 32 = Spanish; 64 = Mexican; 128 = Russian
// Prototype for search options
// Show only on language search option active translations entries (locales_XXX)
// Example (use flag values by adding the values) : Korean (1) + German (4) + Russian (64) = 69
// NOTE : Righ now only for Creature.php

$locales_search_option =  0;         // No search option, don't use locales_XXX for search
$site_encoding         = 'utf-8';    // used charset


//#############################################################################
//---- Backup configuration ----

$backup_dir = 'backup';    // make sure webserver have the permission to write/read it!


//#############################################################################
//---- Account Levels ----

$gm_level_arr =
    [
 -1 => [-1,      'Guest',      '',''],
  0 => [0,     'Player',      '',''],
  1 => [1,  'Moderator',   'Mod',''],
  2 => [2, 'Gamemaster',    'GM',''], // change the name and alias as required
  3 => [3, 'BugTracker',    'BT',''],
  4 => [4,      'Admin', 'Admin',''],
  5 => [5,      'SysOp', 'SysOp',''],
  6 => [6,    'Unknown',   'UnK',''], // Add additional levels as required
    ];


//#############################################################################
// ---- Module and Security settings ----
// --   Meaning of the columns : TARGET, LANG_TEXT, ( READ/VIEW , INSERT , UPDATE , DELETE ) min Permission GM LEVEL
// --   Files excluded for this : Login.php, Pomm.php
// --   - Both files don't use header.php, so we can't include this method.. but its not a big deal
//
// --   - Updates will follow
// --
// --   Example: array("item.php", 'items',0,1,2,3),
// --    level 0 can only view and search,
// --    level 1 can add new items but cannot edit,
// --    level 2 can add and edit but cannot delete,
// --    level 3 has full access
//
// --   Example: array("item.php", 'items',1,0,3,2),
// --    this is tricky,
// --    level 0 would have no access
// --    level 1 can only search and browse
// --    level 2 can delete items, but cannot add or edit
// --    level 3 can add and edit, but cannot delete

$menu_array =
    [
  ['index.php',        'main',
      [
      ['ahstats.php','auctionhouse', 0,5,5,5],
      ['arenateam.php', 'arena_teams', 0,5,5,5], // has own level security, but has yet to honor the new security system.
      ['guild.php',      'guilds', 0,5,5,5],
      ['honor.php',       'honor', 0,5,5,5],
      ['top100.php',      'top100', 0,5,5,5],
      ['forum.php',      'forums', 0,5,5,5], // has own level security, but has yet to honor the new security system.
      ['stat.php',  'statistics', 0,5,5,5],
      ['javascript:void(0);" onclick="window.open
              (\'./map/\', \'./map/\', \'toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=no, resizable=no, copyhistory=1, width=966, height=732\')',
                               'player_map', 0,5,5,5], // this page has yet to honor the new security system, but it is a read only page
      ],
  ],
  ['#',      'tools',
      [
      ['user.php',   'accounts', 1,5,5,5],
      ['char_list.php', 'characters', 0,5,5,5],
      ['command.php',    'command', 0,5,5,5],
      ['mail.php',       'mail', 1,5,5,5],
      ['mail_on.php',    'mail_on', 3,5,5,5],
      ['ticket.php',    'tickets', 2,5,5,5],
      ['banned.php','banned_list', 3,5,5,5],
      ['cleanup.php',    'cleanup', 5,5,5,5],
      ['irc.php',        'irc', 0,5,5,5],
      ],
  ],
  ['#',         'db',
      [
      ['events.php',     'events', 0,5,5,5],
      ['instances.php',  'instances', 0,5,5,5],
      ['item.php',      'items', 1,5,5,5],
      ['creature.php',  'creatures', 5,5,5,5], // this page has yet to honor the new security system, please use with caution.
      ['game_object.php','game_object', 5,5,5,5], // this page has yet to honor the new security system, please use with caution.
      ['tele.php',  'teleports', 1,5,5,5],
      ['backup.php',     'backup', 5,5,5,5], // this page has yet to honor the new security system, please use with caution.
      ['run_patch.php',  'run_patch', 3,5,5,5],
      ['repair.php',     'repair', 3,5,5,5],
      ],
  ],
  ['#',     'system',
      [
      ['realm.php',      'realm', 1,5,5,5],
      ['motd.php',   'add_motd', 1,5,5,5],
      ['spelld.php',     'spelld', 1,5,5,5],
      ['message.php',    'message', 1,5,5,5],
      ['ssh.php',   'ssh_line', 1,5,5,5],
      ],
  ],
  ['#',  'invisible',
      [
      ['javascript:void(0);" onclick="window.open(\'./forum.html\', \'forum\')', 'forums',0,0,0,0],
      ['char.php', 'character', 0,5,5,5],
      ['char_inv.php', 'character', 0,5,5,5],
      ['char_quest.php', 'character', 0,5,5,5],
      ['char_achieve.php', 'character', 0,5,5,5],
      ['char_skill.php', 'character', 0,5,5,5],
      ['char_talent.php', 'character', 0,5,5,5],
      ['char_rep.php', 'character', 0,5,5,5],
      ['char_pets.php', 'character', 0,5,5,5],
      ['char_friends.php', 'character', 0,5,5,5],
      ['char_extra.php', 'character', 0,5,5,5],
      ['char_spell.php', 'character', 0,5,5,5],
      ['char_mail.php', 'character', 0,5,5,5],
      ['char_edit.php', 'char_edit', 0,5,5,5],
      ['edit.php', 'myaccount', 0,5,5,5],
      ['index.php', 'startpage',-1,5,5,5],
      ['guildbank.php', 'guildbank', 0,5,5,5], // under development
      ['realm.php',     'realm', 0,5,5,5], // this last one is special, if this is not here, users are unable to switch realms
      ],                                                 // if READ is set to level 3, only level 3 and above can switch realms.
  ],                                                   // INSERT, UPDATE and DELETE should have no effect, but best to keep it at 5.
    ];


$debug = 0; // 0 - no debug, only fatal errors.
            // 1 - show total queries, mem usage, and only fatal errors.
            // 2 - show total queries, mem usage, and all errors.
            // 3 - show total queries, mem usage, all errors, and list of all global vars.
            // 4 - show total queries, mem usage, all errors, list of all global vars, and values of all global vars.


//#############################################################################
//---- Under Development ----
//
// These are either place holders for future stuff
// or stuff that are currently under development
// do not set or change any of these in here or in config.php
// unless you know what you are doing or were being told to do so
// no support are given to these 'features'

$developer_test_mode =  true;

$multi_realm_mode    =  true;

