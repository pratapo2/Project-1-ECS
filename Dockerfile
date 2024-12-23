# Use a Debian-based image with httpd-2.4.62
FROM project-1-ecs:latest

# Switch to a Debian/Ubuntu base to support apt operations
RUN apt-get update -y
#RUN apt install curl -y

# Copy your custom index.html to the container
COPY index.html /usr/local/apache2/htdocs/index.html