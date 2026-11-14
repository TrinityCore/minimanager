# MiniManager

A web management tool for [TrinityCore](https://github.com/TrinityCore/TrinityCore) 3.3.5a servers.

MiniManager reads the TrinityCore `auth`, `world`, and `characters` databases and keeps its own data in a
separate `mmfpm` database. Features include:

- Account and character management (inventory, spells, talents, quests, reputation, mail, pets)
- Guilds, arena teams, honor, and top-100 rankings
- Item, creature, and game object lookups, and a quest tracker
- Tickets, bans, in-game mail, message of the day, and a small forum
- Multi-realm support, a player map, and server console access over SSH, Telnet, or SOAP

## Requirements

| Component | Version |
| --- | --- |
| PHP | 8.1 or newer, with the `gmp` and `mysqli` extensions |
| MySQL | 8.0 or newer |
| Web server | Any server that runs PHP (Apache, nginx with PHP-FPM, ...) |
| TrinityCore | 3.3.5a, with access to its `auth`, `world`, and `characters` databases |

Optional: the PHP `soap` extension, if you set `$enable_soap` in the configuration.

## Quick start with Docker

The Docker setup runs Apache, PHP 8.3, and MySQL 8.4. To use nginx or Caddy instead of Apache, see
[Using nginx or Caddy](#using-nginx-or-caddy). The MySQL container holds only the `mmfpm` database.
Your TrinityCore databases stay where they are.

1. Copy the configuration file:

   ```shell
   cp scripts/config.dist.php scripts/config.php
   ```

2. Edit `scripts/config.php`:
   - Set `$mmfpm_db['addr']` to `mysql:3306`, `user` to `root`, and `pass` to the MySQL root password you set
     in step 3 (default `rootpassword`).
   - Set `$realm_db`, `$world_db`, and `$characters_db` to your TrinityCore databases. Use an address the PHP
     container can reach. `127.0.0.1` points at the container itself, not at your host.

3. Start the stack. Set `MYSQL_ROOT_PASSWORD` to change the default root password.

   ```shell
   export MYSQL_ROOT_PASSWORD=change-me
   docker compose -f .docker/docker-compose.yml up -d
   ```

   On first start, MySQL creates the `mmfpm` database and its tables.

4. Import the DBC data:

   ```shell
   unzip -p SQL/333a_dbc.zip | docker compose -f .docker/docker-compose.yml exec -T mysql \
     mysql -uroot -p"$MYSQL_ROOT_PASSWORD" mmfpm
   ```

5. Open <http://localhost:8080> and log in with a TrinityCore account.

MySQL listens on `127.0.0.1:3306` and stores its data in `.docker/mysql/data/`.

## Manual installation

1. Copy the repository into your web server's document root.

2. Create the `mmfpm` database and import the schema and DBC data:

   ```shell
   mysql -u root -p -e "CREATE DATABASE mmfpm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
   mysql -u root -p mmfpm < SQL/mmfpm.sql
   unzip -p SQL/333a_dbc.zip | mysql -u root -p mmfpm
   ```

3. Copy the configuration file and edit it (see [Configuration](#configuration)):

   ```shell
   cp scripts/config.dist.php scripts/config.php
   ```

4. Make the `backup/` directory writable by the web server if you plan to use the backup page.

5. Block web access to `SQL/`, `backup/`, `doc/`, and `.git/`. See [Using nginx or Caddy](#using-nginx-or-caddy)
   for example configs that do this.

## Using nginx or Caddy

The Docker stack runs Apache. The repository has an equivalent config for each supported web server:

| Server | Config |
| --- | --- |
| Apache | `.docker/apache/minimanager.apache.conf` |
| nginx | `.docker/nginx/minimanager.nginx.conf` |
| Caddy | `.docker/caddy/Caddyfile` |

Each config sends `.php` requests to PHP-FPM. Each config also blocks `SQL/`, `backup/`, `doc/`, repository metadata,
and `.sql`, `.md`, and `.yml` files.

nginx and Caddy ignore `.htaccess` files. Keep the deny rules when you adapt a config. Without them, anyone can
download the files in `backup/`.

For a manual installation, change the document root (`/var/www/html`) and the PHP-FPM address (`php:9000`) to match
your server.

To use nginx in the Docker stack, create `.docker/docker-compose.override.yml`:

```yaml
services:
  apache:
    build: !reset null
    image: nginx:stable-alpine
    volumes:
      - ./nginx/minimanager.nginx.conf:/etc/nginx/conf.d/default.conf:ro
```

For Caddy, use `image: caddy:2-alpine` and mount `./caddy/Caddyfile:/etc/caddy/Caddyfile:ro` instead. Then start the
stack with both files:

```shell
docker compose -f .docker/docker-compose.yml -f .docker/docker-compose.override.yml up -d
```

## Configuration

All settings live in `scripts/config.php`. The file has comments for each setting. The main sections are:

- **SQL configuration**: connection details for `$mmfpm_db`, `$realm_db` (the TrinityCore `auth` database), and one
  `$world_db` and `$characters_db` entry per realm. The array index must match the realm ID in `auth.realmlist`.
- **Game server configuration**: one `$server` entry per realm, with game, console, and Telnet ports. SOAP settings
  follow.
- **Mail configuration**: SMTP settings for registration and password reset mail.
- **Account levels**: names for each security level in `$gm_level_arr`.
- **Module and security settings**: the minimum level each page needs for read, insert, update, and delete.

Do not remove the `HEADER_LOADED` check at the bottom of the file. It stops direct requests to `config.php`.

### Accounts and access

MiniManager logs you in with your TrinityCore account. Your access level is the `SecurityLevel` value in
`auth.account_access` for the current realm, or for `RealmID = -1` (all realms). Accounts without a row are players
(level 0).

```sql
INSERT INTO auth.account_access (AccountID, SecurityLevel, RealmID) VALUES (<account id>, <level>, -1);
```

TrinityCore uses levels 0 to 3. The default page permissions in `config.php` need level 5 (SysOp) to insert, update,
or delete. Lower the levels in the module and security settings to match the levels your staff accounts have.

## Upgrading

1. Pull the latest code.
2. Compare `scripts/config.dist.php` with your `scripts/config.php` and copy over any new settings.
3. Make sure your `config.php` ends with this block:

   ```php
   if (!defined('HEADER_LOADED')) {
     header('Location: /');
   }
   ```

4. Installations from before August 2020 must apply `SQL/update/2020_08_11_mmfpm_changes.sql`. This script drops and
   recreates the `mm_account` table, so pending (unverified) registrations are lost.

Docker users who move from the old MySQL 5.6 image to MySQL 8.4 cannot reuse `.docker/mysql/data/`. MySQL does not
upgrade 5.6 data directly to 8.4. Dump the `mmfpm` database first, remove the data directory, start the new stack,
and import the dump.

### Spell Category Filter (`char_spell.php`)

To enable the spell category filter (Class, Professions, Mounts, Pets, and Other) in `char_spell.php`, import `SkillLineAbility.dbc` into your `minimanager` database by running the following script from the MiniManager root directory (adjust `$dbc_path` to your server's DBC folder):

```bash
php << 'IMPORT'
<?php
\$dbc_path = "/path/to/SkillLineAbility.dbc";

require "scripts/config.php";
\$m = new mysqli(\(mmfpm_db["addr"], \)mmfpm_db["user"], \(mmfpm_db["pass"], \)mmfpm_db["name"]);
fh = fopen(dbc_path, "rb");
\$hdr = unpack("a4magic/Vrows/Vcols/Vrowsize/Vstrsize", fread(\(fh, 20));\)m->query("DROP TABLE IF EXISTS dbc_skilllineability");
\$m->query("CREATE TABLE dbc_skilllineability (field_0 INT UNSIGNED PRIMARY KEY, field_1 INT UNSIGNED, field_2 INT UNSIGNED, KEY idx_skill (field_1), KEY idx_spell (field_2)) ENGINE=MyISAM");
\$vals = array();
for (\$i = 0; i < hdr["rows"]; \(i++) {\)r = unpack("Va/Vb/Vc", fread(fh, hdr["rowsize"]));
  \(vals[] = "(".\)r["a"].",".\(r["b"].",".\)r["c"].")";
  if (count(\(vals) >= 1000) {\)m->query("INSERT INTO dbc_skilllineability VALUES ".implode(",", \(vals));\)vals = array();
  }
}
if (!empty(vals)) m->query("INSERT INTO dbc_skilllineability VALUES ".implode(",", \$vals));
fclose(\$fh);
echo "Imported ".\$hdr["rows"]." rows into dbc_skilllineability\n";
?>
IMPORT
```

## License

MiniManager is released under the GNU General Public License v2. See [doc/GPLv2](doc/GPLv2).

