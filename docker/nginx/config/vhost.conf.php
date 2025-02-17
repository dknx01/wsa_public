server {
    listen 80;

    server_name _;
    index index.php;
    error_log  /var/log/nginx/error.log info;
    access_log /var/log/nginx/access.log;
    root /var/www/project/public;
    sendfile off;
    client_max_body_size 32M;

    location / {
        try_files $uri /index.php?$is_args$args;
        proxy_read_timeout 150;
    }

    location ~ \.php$ {
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass php:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        fastcgi_param APPLICATION_ENV development;
        fastcgi_read_timeout 150;
    }
    location ~ /\. {
            deny all;
    }

    location ~ \.php$ {
        return 404;
    }
}


