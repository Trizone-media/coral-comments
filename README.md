# Coral Project setup

[Coral documentation](https://docs.coralproject.net/) offers two methods for installing Coral:
1. Docker
2. Source

We'll describe the Docker method here. 

## Server Requirements
- At Least 2GB of RAM
- At Least 1 vCPUS

## Setup server (Ubuntu server)

### Update apt and Upgrade packages
`apt update && apt upgrade -y`

Reboot after upgrade is done.

### Install fail2ban
Fail2ban scans log files and bans IPs that show the malicious signs -- too many password failures, seeking for exploits, etc.

`apt install fail2ban -y`

### Install Docker, Docker Compose, and Nginx
`apt install docker docker-compose nginx -y`

### Run Coral docker
> All the following steps should be run inside the `/root/coral` directory.

The following commands start Coral and its dependencies(Redis, Mongodb). *Redis* and *Mongo* data are stored in *data* directory. 
```
chmod +x ./run.sh
./run.sh
```
Now we have a running coral on port 5000. Note that access to this port is limited to only localhost. In the next step we setup a nginx reverse proxy that listens on *port 80* and sends the requests to *port 5000* (Docker)

### Nginx configuration
```
cp coral.conf /etc/nginx/sites-enabled/
```
Test nginx config:
`nginx -t`
You should see the following output:
```
nginx: the configuration file /etc/nginx/nginx.conf syntax is ok
nginx: configuration file /etc/nginx/nginx.conf test is successful
```
Then reload nginx:
`service nginx reload`

### Install cerbot
Update snapd:
```
sudo snap install core
sudo snap refresh core
```

Install certbot:
```
sudo snap install --classic certbot
sudo ln -s /snap/bin/certbot /usr/bin/certbot
```
Install Let's encrypt certs for Coral:
`sudo certbot --nginx`

### Setup coral
Open the URL that you have setup for Coral comments and follow the installation wizard.

We have assumed that you have created a new A-record to point to this new instance running. eg. A Record - IP address of your new instance running - comments.your.wordpresssite.com

In the last step enter your website URL in *Site permitted domains* field: https://your.wordpresssite.com.

### Setup the Wordpress plugin
Copy the updated Coral Wordpress plugin from `talk-wp` directory in this repository to your wordpress plugins directory. Then install and activate the plugin.
Then navigate to ??????[https://your.wordpress.site.com/wp-admin/options-general.php?page=talk-settings](https://your.wordpresssite.com/wp-admin/options-general.php?page=talk-settings) and set the following values:

- Server Base URL: https://your.wordpresssite.com
- Static Asset URL: https://your.wordpresssite.com
- JWT secret token: You can find this token in the authentication page(SSO) of Coral(http://CORAL_DOMAIN/admin/configure/auth).
- Version: 5+
* NB: replace your.wordpresssite.com with your own domain

## Create Database backups
The following command creates a database(mongo) backup and writes it to the *mongo-backup* directory.

```
chmod +x backup.sh
./backup.sh
```

To create automatic daily backups, run `crontab -e` and insert the following line:

```
0 0 * * * /root/coral/backup.sh
```

### Sync backups to your local machine
`rsync -avz root@SERVER_IP:coral/mongo-backup/ ~/coral-db-backup`
## Restore backups
The following command restores mongo backup from the specified date:
```
docker-compose exec mongo mongorestore /mongo-backup/{DATE}/coral
```

For example:
```
docker-compose exec mongo mongorestore /mongo-backup/2021-10-01/coral
```
