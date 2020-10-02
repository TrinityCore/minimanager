Installation of Minimanager for TrinityCore

Content:
1. Configuration
2. Database Setup

Please note that there’s no guarantee that the manual works all the time.

The Minimanager for TrinityCore was released under GNU GPL v3, feel
free to change anything, but it would be nice if you leave our copyright.

Configuration

- First open config.dist.php located in /scripts
- Edit the SQL configuration so Minimanager is able to connect to your database
- There’s a new database needed: mmfpm or whatever for some features of Minimanager
- Configure the other options, there are comments if you don’t know what the option does
- When you finished configuring Minimanager, save it and close the config
- Rename the config.dist.php to config.php
- enable gmp php extension on php.ini

Database Setup

- Extract the archives 333a_dbc.zip and ip2nation.rar
- Now apply these files to your database mmfpm:
- 333a_dbc.sql
- Install_forum.sql
- Ip2nation.sql
- Mm-account.sql
- Point_system.sql
