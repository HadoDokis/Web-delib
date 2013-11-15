#!/bin/bash
# Retrieve Platform and Platform Version
if [ -f "/etc/lsb-release" ];
then
  platform=$(grep DISTRIB_ID /etc/lsb-release | cut -d "=" -f 2 | tr '[A-Z]' '[a-z]')
  platform_version=$(grep DISTRIB_RELEASE /etc/lsb-release | cut -d "=" -f 2)
echo $platform && echo $platform_version
elif [ -f "/etc/debian_version" ];
then
  platform="debian"
  platform_version=$(echo -e `cat /etc/debian_version`)
echo $platform && echo $platform_version
elif [ -f "/etc/redhat-release" ];
then
  platform=$(sed 's/^\(.\+\) release.*/\1/' /etc/redhat-release | tr '[A-Z]' '[a-z]')
  platform_version=$(sed 's/^.\+ release \([.0-9]\+\).*/\1/' /etc/redhat-release)
  echo $platform && echo $platform_version
fi
