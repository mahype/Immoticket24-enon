# Ready to use - WordPress Composer Docker Environment #

you are searching for a ready to use -WordPress Docker environment- which you have only start up with one command?   
### What is in this repository? ###
* **Nginx based proxy** - so you can run multiple docker instances with different domains.
* **Simple demo WordPress install** 

### How do I get set up? ###
* Using first time the nginx-proxy [go here](#markdown-header-the-nginx-proxy) for startup instruction 
* Start the WordPress [go here](URL)

### Handle the nginx-proxy
  * First not checkout this repository
  `git clone git@bitbucket.org:webdevmedia/wordpress-docker-nginx-proxy.git`
  * cerate a new Docker network `docker network create nginx-proxy`  
  * go to nginx-proxy  `cd nginx-proxy` 
  * run `docker-compose up -d` 
  * your nginx-proxy are now running. For a proof run `docker ps` and you will see something like this


```
  | CONTAINER ID | IMAGE                      | COMMAND                  | CREATED        | STATUS        | PORTS                                    | NAMES                     |
  |--------------|----------------------------|--------------------------|----------------|---------------|------------------------------------------|---------------------------|
  | 0f8415cd052c | jwilder/nginx-proxy:alpine | "/app/docker-entrypoint" | 11 seconds ago | Up 10 seconds | 0.0.0.0:80->80/tcp, 0.0.0.0:443->443/tcp | nginx-proxy_nginx-proxy_1 |
```

  
Sometimes you will take a look into the nginx-proxy setting. With `docker ps` you will see the name of the nginx-proxy -> `nginx-proxy_nginx-proxy_1`
For that run the following command:`docker exec nginx-proxy_nginx-proxy_1 grep -vE '^\s*$' /etc/nginx/conf.d/default.conf`
  
As result you see the current proxy configuration.
  
```
    shell
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_set_header X-Forwarded-Proto $proxy_x_forwarded_proto;
    proxy_set_header X-Forwarded-Ssl $proxy_x_forwarded_ssl;
    proxy_set_header X-Forwarded-Port $proxy_x_forwarded_port;
    # Mitigate httpoxy attack (see README for details)
    proxy_set_header Proxy "";
    server {
      	server_name _; # This is just an invalid value which will never trigger on a real hostname.
      	listen 80;
      	access_log /var/log/nginx/access.log vhost;
      	return 503;
    }
```

* The nginx-proxy are now listen to all request on `127.0.0.1`
* **that's it you have never touch again the nginx-proxy**

### Start a WordPress
$ docker-compose up -d

- rename public/.htaccess into public/.htaccess-origin
- rename public/.htaccess-docker into public/.htaccess

### useful commands ###
- `docker exec DB_CONTAINER_NAME /bin/bash -c 'mysqldump -u username -ppassword wordpress > /var/lib/mysql/wordpress.sql`
- `docker exec PROXY_CONTAINER_NAME grep -vE '^\s*$' /etc/nginx/conf.d/default.conf`
- `docker exec PHP_CONTAINER_NAME /bin/bash -c 'php Search-Replace-DB/srdb.cli.php -h DB_CONTAINER_NAME -n DBNAME -u DBUSER -p DBPASS -s SEARCH_STR -r REPLACE_STR'`

## Systems

To get changes to the systems, just push them to the branches staging, sparkasse or production. Github actions will push them to the servers.

###Staging customer

https://staging.energieausweis-online-erstellen.de/

Username: staging

Password: lassMalSehen2020

###Staging sparkasse

https://sparkasse.energieausweis-online-erstellen.de/

Username: sparkasse

Password: immerR3inh1er

###Live site

https://sparkasse.energieausweis-online-erstellen.de/

## Creation of an energy certificate

Bedarfsausweis / Verbrauchsausweis process

1. Entering address.
2. Overview Page (at the bottom of the page you will find the calculation results !important).
3. Editing the data (there is the form which is created by the schema).
4. After completion of the data, the energy certificate can be bought and the calculations are working now.
5. Checking out the energy certificate
6. Buying the certificate.
- The certificate XML will be sent to DIBT to get an DIBT id.
- Emails going out to the customer and Christian Esch.
- Some 

##Important to know:
- Both, Bedarfs- an Verbrauchsausweis using different schemas (@see public/app/wpenen/enevVERSION).
- The sparkasse schema class inherits the current schema and changes it for their own needs.
- The current schema is enev2020-01 and this is valid since 2020-03-11.
- Older energy certificates using a different schema (@see public/app/plugins/enon/src/Enon/Standards_Config.php)

## Plugins

Plugins to add new code:
public/app/plugins/enon
public/app/plugins/enon-reseller

Legacy plugins
public/app/plugins/wp-energieausweis-online

## Anomalies

There is a lot of procedual code. Functions will be found in plugins and also in theme files. ;) Have a lot of fun!





