FROM debian

COPY root/etc/apt/sources.list /etc/apt/sources.list

RUN apt-get update -y
RUN apt-get dist-upgrade -y
RUN apt-get install bind9 -y

COPY root/ /

EXPOSE 53

VOLUME /srv/bind

CMD ["/usr/sbin/named", "-u", "bind", "-g"]
