<?php
use Ice\Helper\Console;
?>

<?= Console::getText(' Please modify your hosts file (\'/etc/hosts\' or \'%SystemRoot%\system32\drivers\etc\host\') ', Console::C_BLACK_B, Console::BG_GREEN) ?>


127.0.0.1       <?= strtolower($moduleName) . '.global' ?> <?= strtolower($moduleName) . '.test' ?> <?= strtolower($moduleName) . '.local' ?>



<?= Console::getText(' For nginx web-server add new \'server\' section ', Console::C_BLACK_B, Console::BG_GREEN) ?>


server {
    listen       80;
    server_name  <?= strtolower($moduleName) . '.global' ?> <?= strtolower($moduleName) . '.test' ?> <?= strtolower($moduleName) . '.local' ?>;

    proxy_buffer_size   128k;
    proxy_buffers   4 256k;
    proxy_busy_buffers_size   512k;

    access_log  <?= LOG_DIR ?>access.log  combined;
    error_log  <?= LOG_DIR ?>error.log;

    root <?= ROOT_DIR ?><?= $moduleName ?>/Web;
    index index.php;

    location ^~ /resource
    {
        root <?= ROOT_DIR ?>_resource/<?= $moduleName ?>;
        index index.html;

        add_header      Cache-Control "public,must-revalidate";
        #add_header      Expires "Thu, 15 Apr 2010 20:00:00 GMT";

        gzip on;
        access_log        off;
        expires           1d;

        #open_file_cache max=1024 inactive=600s;
        #open_file_cache_valid 2000s;
        #open_file_cache_min_uses 1;
        #open_file_cache_errors on;
    }

    location / {
        try_files $uri /index.php?$is_args$args;
    }

    location ~ \.php$ {
        #fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_pass   127.0.0.1:9000;

        fastcgi_param  SCRIPT_FILENAME  $document_root$fastcgi_script_name;

        include        fastcgi_params;

        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 512k;
    }

    location ~ /\. {
        deny  all;
    }
}


<?= Console::getText(' For apache web-server add new \'vhost\' directive ', Console::C_BLACK_B, Console::BG_GREEN) ?>


<VirtualHost *:80>
    ServerName <?= strtolower($moduleName) . '.local' ?>
    ServerAlias <?= strtolower($moduleName) . '.global' ?> <?= strtolower($moduleName) . '.test' ?>

    DocumentRoot <?= ROOT_DIR ?><?= $moduleName ?>/Web
    DirectoryIndex index.php

    Alias /resource/ <?= RESOURCE_DIR ?>

    CustomLog <?= LOG_DIR ?>access.log combined
    ErrorLog <?= LOG_DIR ?>error.log

    <Directory <?= ROOT_DIR ?>>
        AllowOverride All
        Order allow,deny
        Allow from All
    </Directory>
</VirtualHost>


<?= Console::getText('ATTENTION!!! FIRST READ UPPER FOR VALID INSTALL! ', Console::C_RED_B, Console::BG_YELLOW) ?>


<?= Console::getText('Congratulations!' . "\n" .  'After web-server setting open in browser: http://' . strtolower($moduleName) . '.local', Console::C_GREEN_B) ?>


