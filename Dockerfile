# Используем официальный образ PHP CLI
FROM php:7.4-cli

# Копируем скрипт в рабочую директорию контейнера
COPY analyze.php /usr/src/app/

# Устанавливаем рабочую директорию
WORKDIR /usr/src/app

# Запускаем скрипт с параметрами при запуске контейнера
CMD ["php", "analyze.php", "-u", "100", "-t", "6000"]
