A little Doctrine DBAL driver for PostgreSQL connections.  Basically, we've taken to using certain options in the libpq
library that Doctrine's built in DSN constructor is opinionated about. Notably, *sslcert* or *service*, whereby we can
use a different SSL certificate for *cert* authentication to Postgres.

If you've never used ['service', it's a lovely way to go](https://www.postgresql.org/docs/9.6/static/libpq-pgservice.html).
You can also do your command line connection with `$ psql service=mydb` if you set that up.

I'm listening in the driverOptions array for bare Doctrine DBAL because I didn't want to add more options to the 
Symfony Doctrine bundle's top-level configuration schema.


## Example (Symfony - config.yml)
```
doctrine:
    dbal:
      driver_class: Shorock\Doctrine\FlexPDOPgSql\Driver
      user: shorock
      options:
        service: libdb
	# or
	# dsn: "pgsql:dbname=libdb.you.edu sslcert=/etc/pki/alternate.crt sslkey=/etc/pki/alternate.key"
```
