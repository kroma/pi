#!/bin/bash


# pushd . > /dev/null
# cd "/home/"
# for i in $(ls -d */); do echo ${i%%/}; done
# popd > /dev/null

cat /etc/passwd | awk -F: '{print $1":"$3}'
