FROM node as build
RUN mkdir -p /build/dist/js /build/dist/css
WORKDIR /build
# Install in a separate RUN command to utilize build cache.
RUN npm install --no-save webpack clean-css \
    # The following are required to make the app work with Sailfish.
    babel-loader @babel/core @babel/preset-env \
    @babel/polyfill whatwg-fetch
ADD ./build.js /build/build.js
ADD ./src/public/ /build/src
# Build JavaScript, CSS and templates cache with the build.js script.
RUN node /build/build.js

FROM composer as vendor
ADD ./src/composer.json /src/
# Install the vendor dependencies with composer.
RUN cd /src && composer install --no-dev

FROM php:7-apache
# The clients only need to have acces to the public files without providing the public path.
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
 && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf \
 # MySQL is required for the application.
 && docker-php-ext-install mysqli \
 # To set the security headers like CSP in .htaccess.
 && a2enmod headers \
 # Security settings not available in .htaccess context.
 && echo "ServerTokens Prod" >> /etc/apache2/apache2.conf
ADD ./src /var/www/html
# Create the default configuration compatible with passing options via environment.
ADD ./src/config/config.local.php.dist /var/www/html/config/config.local.php
# Copy the build result (CSS, JavaScript and an index.html containing the template cache).
# Delete the development files first.
RUN rm -rf /var/www/html/public/js /var/www/html/public/css /var/www/html/public/templates
COPY --from=build /build/dist /var/www/html/public
# Copy vendor directory from composer.
COPY --from=vendor /src/vendor /var/www/html/vendor
