# NGINX

This is the Docker container that is the entrypoint for all requests to audiobooke.com. It serves as a "dispatcher" to the other microservices, which are represented by Docker containers, participating in a Docker Swarm.


For more information on which URL patterns this NGINX container handles and where it forwards them, refer to the config file [/nginx.nginx](./nginx.nginx) in the current directory.
