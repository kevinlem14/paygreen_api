FROM nginx:1.17.10

ARG FASTCGI_PASS
ENV FASTCGI_PASS=$FASTCGI_PASS

ADD docker/local/nginx/root /

RUN chmod +x /usr/local/bin/docker-entrypoint.sh
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]

CMD ["nginx", "-g", "daemon off;"]
