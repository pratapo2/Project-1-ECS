# Use a pinned version of httpd for stability
FROM httpd:2.4.57

# Install PHP 8.1 with minimal layers
RUN apt-get update && apt-get install -y php8.1 && rm -rf /var/lib/apt/lists/*

# Copy your custom index.html to the container
COPY index.html /usr/local/apache2/htdocs/index.html
