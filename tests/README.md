## Run Lizmap stack with docker compose

Steps:

* Launch Lizmap with docker compose

```bash
# Clean previous versions (optional)
make clean

# Pull docker images
make pull

# Run the different services
make run
```

* Open your browser at `http://localhost:9085`


For more information, refer to the [docker compose documentation](https://docs.docker.com/compose/)


## Add the test data

You can add some data in your docker test PostgreSQL database by running the SQL `tests/sql/test_data.sql`.


```bash
make import-test-data
```


## Install the module

* Install the module with:

```bash
make install-module
```

* Add the needed Lizmap rights:


```bash
make import-lizmap-acl
```

Then you can try the [Lizmap test map](http://localhost:9095/index.php/view/map/?repository=pgmetadata&project=pgmetadata).

## Access to the dockerized PostgreSQL instance

You can access the docker PostgreSQL test database `lizmap` from your host by configuring a
[service file](https://docs.qgis.org/latest/en/docs/user_manual/managing_data_source/opening_data.html#postgresql-service-connection-file).
The service file can be stored in your user home `~/.pg_service.conf` and should contain this section

```ini
[lizmap-pgmetadata]
dbname=lizmap
host=localhost
port=9097
user=lizmap
password=lizmap1234!
```

Then you can use any PostgreSQL client (psql, QGIS, PgAdmin, DBeaver) and use the `service`
instead of the other credentials (host, port, database name, user and password).

```bash
psql service=lizmap-pgmetadata
```

## Access to the lizmap container

If you want to enter into the lizmap container to execute some commands, 
execute `make shell`.
