# Secrets Server
Creates and distributes secrets based on client certificates

## Usage
    wget --ca-certificate=/etc/ssl/certs/host-ca.crt --certificate=/etc/ssl/certs/host.crt --private-key=/etc/ssl/private/host.key https://secrets.example.com/<module>

## Adding modules
Modules can be added by placing an executable file in the modules folder. The executable will be called with the hostname of the client as the first argument. It should write the secret to stdout.

You need to enable the module in config.php after adding it to the modules folder.

## Installation

### Puppet
Can be automatically installed using puppet: [puppet-secret_server](https://github.com/thexa4/puppet-secret_server)

### Manually
1. apt-get install apache2 git php5
2. a2enmod rewrite ssl
3. mkdir -p /opt/max/
4. cd /opt/max && git clone https://github.com/thexa4/secrets-server secrets
5. cd /opt/max/secrets
6. chmod -R 750 data
7. cp config.php.sample config.php
8. configure a virtual host that optionally checks for client certificates and uses /opt/max/secrets/public as documentroot.
