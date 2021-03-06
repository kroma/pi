#!/bin/sh

#php-redis installer shell script for Pi

USERNAME="pi"

SERVICE="phpredis"

GITURL="https://github.com/nicolasff/phpredis.git"

HOMEDIR="/home/$USERNAME"
INSTALL="$HOMEDIR/install"
DIRECTORY="$INSTALL/$SERVICE"



if ! [ -d "$HOMEDIR/" ]; then
  # Control will enter here if $DIRECTORY exists.
  echo "pi is not installed. \n - Use \"pi install\" to download latest version from GitHub." 1>&2
  exit 1
fi

if ! [ -d "$INSTALL/" ]; then
  # Control will enter here if $DIRECTORY exists.
  sudo -u pi mkdir "$INSTALL"
fi


if [ -d "$DIRECTORY" ]; then
  # Control will enter here if $DIRECTORY exists.
  tput setaf 1
  printf "phpredis is already installed. "
  tput sgr 0
  tput setaf 6
  echo "\n    Use \"pi update phpredis\" to retrieve latest version from GitHub."
  tput sgr 0
  exit 1
fi


# echo "changing to $INSTALL"
cd "$INSTALL"

sudo -u pi git clone --single-branch --depth=1 -b develop "$GITURL" "$DIRECTORY"

cd "$DIRECTORY"

echo "running phpize ..."
sudo -u "$USERNAME" phpize > /dev/null

echo "running ./configure ..."
sudo -u "$USERNAME" ./configure --enable-redis-igbinary > /dev/null

echo "running make..."
sudo -u "$USERNAME" make > /dev/null

echo "make install..."
sudo make install > /dev/null

tput setaf 6
echo "done!"
tput sgr 0
