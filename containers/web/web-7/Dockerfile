FROM       drupalci/web-base
MAINTAINER drupalci

##
# PHP master (7)
##

RUN sudo php-build -i development --pear master $HOME/.phpenv/versions/master
RUN sudo chown -R root:root $HOME/.phpenv
RUN phpenv rehash
RUN phpenv global master
# TODO: pecl mongo not working yet
# RUN echo | pecl install mongo
RUN cp -f /root/.phpenv/shims/php /usr/bin/php

# Fix date.timezone warning
RUN find /root/.phpenv/ -iname php.ini -exec sh -c 'echo "date.timezone=UTC" >> {}' \;
RUN echo "date.timezone=UTC" >> /etc/php5/cli/php.ini

EXPOSE 80
CMD ["/bin/bash", "/start.sh"]
