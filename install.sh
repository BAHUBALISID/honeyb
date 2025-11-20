#!/bin/bash

echo "Installing HoneyB Security Tool Suite..."

# Create directory structure
mkdir -p honeyB
cd honeyB
mkdir -p config tools/{cateye,recon,vuln_scanner,api_security,network,reporting} lib data/{wordlists,templates} results reports exports

# Create necessary wordlists
echo "Creating default wordlists..."
cat > data/wordlists/subdomains.txt << EOF
www
api
mail
ftp
cpanel
admin
test
dev
staging
blog
shop
store
app
mobile
secure
portal
support
help
docs
webmail
server
ns1
ns2
ns3
ns4
cdn
static
assets
media
images
img
js
css
login
auth
oauth
sso
account
accounts
billing
payment
pay
invoice
invoices
EOF

echo "Installation completed!"
echo "Now you can run: php main.php"
