#! /bin/sh
#

openssl pkcs12 -in $1 -out ca.pem -cacerts -nokeys
openssl pkcs12 -in $1 -out client.pem -clcerts -nokeys
openssl pkcs12 -in $1 -out key.pem -nocerts
