# minimanager
TrinityCore Web management tool

## Installation

Ensure you have `ext-gmp` and `ext-mysqli` extensions included and enabled on your installation of PHP.

The minimum required version of PHP is currently PHP 7.4. However, as [support for 7.4 EOL'd in November 2022](https://www.php.net/supported-versions.php)
, you
should upgrade to PHP 8.0 **as soon as possible**.

A docker compose configuration has been provided for convenience. Note that you do not need to apply the `mmfpm.sql` 
when using the docker configuration - an init script does this during build.
```
docker-compose -f ./.docker/docker-compose.yml up -d
```

### Configuration

- Ensure you have an `mmfpm` MySQL database created
- Apply `mmfpm.sql` to your newly created database
  - `mysql -u trinity -p mmfpm < SQL/mmfpm.sql`
- Extract 333a_dbc.zip, and apply the SQL file to the `mmfpm` database
- Download and apply ip2nation data to your `mmfpm` database
    - http://ip2nation.com/ip2nation/Download
    - `mysql -u trinity -p mmfpm < SQL/ip2nation.sql`

- Make a copy of the configuration file, and make changes as necessary
```shell
cp scripts/config.dist.php config.php
```

- Ensure you have an account created in your TrinityCore installation
- Check to make sure you have the necessary GM level set in `auth.account_access`




