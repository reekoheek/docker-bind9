FROM debian

MAINTAINER Ganesha <reekoheek@gmail.com>

ENV DEBIAN_FRONTEND noninteractive
ENV APT_PROXY http://192.168.1.10:3128

RUN \
  echo "\n\
Acquire::HTTP::Proxy \"$APT_PROXY\";\n\
Acquire::HTTPS::Proxy \"$APT_PROXY\";\n\
" > /etc/apt/apt.conf.d/01proxy && \
 echo " \n\
deb http://kambing.ui.ac.id/debian/ jessie main\n\
deb http://kambing.ui.ac.id/debian/ jessie-updates main\n\
deb http://kambing.ui.ac.id/debian-security/ jessie/updates main\n\
" > /etc/apt/sources.list && \
# apt-get -o Acquire::Check-Valid-Until=false update -y
  apt-get update -y

RUN apt-get install -y \
  bind9 \
  php5-cli \
  supervisor \
  curl && \
  mkdir -p /var/run/named && \
  chown bind:bind /var/run/named

ENV USE_API false

COPY entrypoint.sh /entrypoint.sh
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY api /api

# RUN curl -sS https://getcomposer.org/installer | php && \
#   cd /api && \
#   /composer.phar install

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/sbin/named", "-4", "-u", "bind", "-g"]