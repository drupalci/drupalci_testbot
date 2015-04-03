FROM       drupalci/web-base
MAINTAINER drupalci

##
# PHP 5.6.0
##

RUN sudo php-build -i development --pear 5.6.7 $HOME/.phpenv/versions/5.6.7
RUN sudo chown -R root:root $HOME/.phpenv
RUN phpenv rehash
RUN phpenv global 5.6.0
RUN echo | pecl install mongo
RUN cp -f /root/.phpenv/shims/php /usr/bin/php

# Fix date.timezone warning
RUN find /root/.phpenv/ -iname php.ini -exec sh -c 'echo "date.timezone=UTC" >> {}' \;

EXPOSE 80
CMD ["/bin/bash", "/start.sh"]
