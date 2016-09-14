# Secrets Server
Creates and distributes secrets based on client certificates

## Usage
    wget --ca-cert=/etc/ssl/certs/host-ca.crt --certificate=/etc/ssl/certs/host.crt --private-key=/etc/ssl/private/host.key https://secrets.example.com/<module>

## Adding modules
Modules can be added by placing an executable file in the modules folder. The executable will be called with the hostname of the client as the first argument. It should write the secret to stdout.

You need to enable the module in config.php after adding it to the modules folder.
