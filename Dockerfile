FROM debian

MAINTAINER Ganesha <reekoheek@gmail.com>

ENV DEBIAN_FRONTEND noninteractive

RUN \
  echo "\n\
Acquire::HTTP::Proxy \"http://192.168.99.101:3128\";\n\
Acquire::HTTPS::Proxy \"http://192.168.99.101:3128\";\n\
" > /etc/apt/apt.conf.d/01proxy && \
  echo " \n\
deb http://kambing.ui.ac.id/debian/ jessie main\n\
deb http://kambing.ui.ac.id/debian/ jessie-updates main\n\
deb http://kambing.ui.ac.id/debian-security/ jessie/updates main\n\
" > /etc/apt/sources.list && \
  # apt-get -o Acquire::Check-Valid-Until=false update -y
  apt-get update -y

RUN \
  apt-get install -y \
    bind9 \
    php5-cli \
    supervisor \
    curl

RUN \
  mkdir -p /var/run/named && \
  chown bind:bind /var/run/named

RUN \
  apt-get install -y php5-xdebug

COPY entrypoint.sh /entrypoint.sh
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY api /api

ENV USE_API false

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/sbin/named", "-4", "-u", "bind", "-g"]