FROM ruby:2.5.1

ENV TARGET_URI http://intranet.docker

ADD . /

# Phantom crashes frequently without this
ENV QT_QPA_PLATFORM=offscreen

RUN apt-get update \
	&& apt-get install -y \
     apt-transport-https \
     ca-certificates \
     curl \
     gnupg2 \
     software-properties-common \
		 phantomjs \
	&& curl -fsSL https://download.docker.com/linux/$(. /etc/os-release; echo "$ID")/gpg | apt-key add - \
  && add-apt-repository \
	"deb [arch=amd64] https://download.docker.com/linux/$(. /etc/os-release; echo "$ID") $(lsb_release -cs) stable" \
	&& apt-get update \
	&& apt-get install docker-ce -y \
	&& bundle install

CMD ./wait-for-wordpress.sh ${TARGET_URI}
