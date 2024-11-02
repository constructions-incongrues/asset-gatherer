# Use an official PHP image as a base
ARG VARIANT=7.4
FROM mcr.microsoft.com/vscode/devcontainers/php:0-${VARIANT}

# Install Composer (already included in devcontainer image)
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    rm composer-setup.php

# Set the default user to vscode
USER vscode
