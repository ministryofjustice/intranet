#!/usr/bin/env ash

source bin/composer-auth.sh

if [ ! -d "./vendor" ]; then
  composer install
fi

if [ ! -d "./vendor-assets" ]; then
  # Nginx file sharing regex-parts
  regex_files='\(htm\|html\|js\|css\|png\|jpg\|jpeg\|gif\|ico\|svg\|webmanifest\)'
  regex_path='\(app\/themes\/clarity\|app\/mu\-plugins\|app\/plugins\|wp\)'

  echo "Generating vendor-assets directory..."

  mkdir -p ./vendor-assets
  find public/ -name '*node_modules*' -prune -name '*uploads*' -prune -name '*clarity/src*' -prune -name '*clarity/webpack*' -prune -o -type f -regex "public\/${regex_path}.*\.${regex_files}" -exec cp --parent "{}" vendor-assets/  \;

  echo "Done."
fi
