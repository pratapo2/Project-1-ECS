# Use a Debian-based image with httpd
FROM httpd:latest

# Switch to a Debian/Ubuntu base to support apt operations
RUN apt-get update -y && apt-get install -y php8.1

# Copy your custom index.html to the container
COPY index.html /usr/local/apache2/htdocs/index.html
