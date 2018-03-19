#!/bin/sh

#######
# As WP is and should be served through a load balancer for a HA setup, the IP address
# that comes through will be that of the LB. In order to get around this we use the
# RealIP module
######

echo "# $LB_IP_RANGE unavailable at start time. This is a placeholder to keep nginx from stopping." > /etc/nginx/real_ip.conf

# If LB_IP_RANGE env var containing the IP range for the load balancers is set, user the Real IP Nginx module

if [ ! -z "$LB_IP_RANGE" ]
then
	echo "Set trusted sources that can set the RealIP e.g loadbalancer at ${LB_IP_RANGE}"
	echo "set_real_ip_from ${LB_IP_RANGE};" > /etc/nginx/real_ip.conf
	echo "real_ip_header X-Forwarded-For;" >> /etc/nginx/real_ip.conf
	echo "real_ip_recursive on;" >> /etc/nginx/real_ip.conf
fi
