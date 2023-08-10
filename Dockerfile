
FROM php:7.4-cli


COPY analyze.php /usr/src/app/

WORKDIR /usr/src/app

CMD ["php", "analyze.php", "-u", "100", "-t", "6000"]
