FROM httpd:2.4-alpine
COPY minimanager.apache.conf /usr/local/apache2/conf/minimanager.apache.conf
RUN echo "Include /usr/local/apache2/conf/minimanager.apache.conf" \
    >> /usr/local/apache2/conf/httpd.conf
