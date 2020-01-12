FROM nginx:alpine

COPY ./default.conf /etc/nginx/conf.d/default.conf
COPY ./nginx.conf /etc/nginx/nginx.conf
COPY ./default.template.conf /etc/nginx/conf.d/default.template
COPY ./nginx.htpasswd /etc/nginx/.htpasswd
