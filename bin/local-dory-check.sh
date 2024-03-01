#!/bin/bash

## colours
GREEN='\033[0;32m'
YELLOW='\033[0;93m'
NC='\033[0m' # No Color

indent() {
  sed 's/^/      /'
}

# Sniff out Apple silicon and then, save the day with
# an ARM64 compatible settings file, if no file exists
copy_dory_settings() {
  if [[ -f "./.dory.yml" ]]; then
  	return 0
  fi

  if [[ $(sysctl -n machdep.cpu.brand_string) =~ "Apple" ]]; then
  	echo -e "${YELLOW}Dory proxy:${NC} installing ARM64 compatible settings..." | indent
    cp "deploy/config/local/.dory.yml" "./.dory.yml"
  	echo -e "${YELLOW}Dory proxy:${NC} Done.\n" | indent
  fi
}

# Search for the Dory Proxy container
DORY_RUNNING=$(docker ps | grep dory_dnsmasq)

# If an output is available in $DORY_RUNNING, we're good. Otherwise, try and start the proxy server
if [[ -z "$DORY_RUNNING" ]]; then
  if command -v dory &>/dev/null; then
  	copy_dory_settings
  	# Fire up Dory
    dory up
  else
    printf "\nThe Dory Proxy is used in this project. You may install it using homebrew.\n\n"
    while true; do
      read -r -p "$(echo -e "${GREEN}Would you like to install Dory using homebrew now? ${NC}" | indent)" yn
      case $yn in
      [Yy]*)
        echo -e "\nRunning ${YELLOW}brew install dory${NC}. This may take a few minutes...\n" | indent
        brew install dory | indent
  		copy_dory_settings
        echo -e "\n${YELLOW}Installation complete.${NC} Starting Dory...\n" | indent
		# Fire up Dory
		dory up | indent
        echo -e "\n\n"
        break ;;
      [Nn]*)
        echo -e "\n${YELLOW}Host configuration${NC}: Please make sure to configure SERVER_NAME in .env."
        echo -e "\n${YELLOW}Host configuration${NC}: By default, the site uses http://justice.docker/."
        break ;;
      *) echo "Please answer yes or no." | indent ;;
      esac
    done
  fi
fi
