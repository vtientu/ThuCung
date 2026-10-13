#!/bin/sh
# Codex only. Starts retained services without dropping any existing database.
set -eu
cd /workspace/ThuCung
sh tools/cloud-setup.sh
TASK_TOOLS=/workspace/tools
if ! LD_LIBRARY_PATH="$TASK_TOOLS/mariadb/usr/lib/x86_64-linux-gnu" "$TASK_TOOLS/mariadb/usr/bin/mariadb" --no-defaults --socket="$TASK_TOOLS/mysql.sock" -uroot -e 'SELECT 1' >/dev/null 2>&1; then
    nohup env LD_LIBRARY_PATH="$TASK_TOOLS/mariadb/usr/lib/x86_64-linux-gnu" "$TASK_TOOLS/mariadb/usr/sbin/mariadbd" --no-defaults --basedir="$TASK_TOOLS/mariadb/usr" --datadir="$TASK_TOOLS/mysql-data" --socket="$TASK_TOOLS/mysql.sock" --pid-file="$TASK_TOOLS/mysql.pid" --skip-networking --log-error="$TASK_TOOLS/mysql.log" > "$TASK_TOOLS/mysql-start.log" 2>&1 &
    attempt=0
    until LD_LIBRARY_PATH="$TASK_TOOLS/mariadb/usr/lib/x86_64-linux-gnu" "$TASK_TOOLS/mariadb/usr/bin/mariadb" --no-defaults --socket="$TASK_TOOLS/mysql.sock" -uroot -e 'SELECT 1' >/dev/null 2>&1; do
        attempt=$((attempt + 1))
        if [ "$attempt" -ge 20 ]; then echo 'MariaDB startup failed; inspect /workspace/tools/mysql.log' >&2; exit 1; fi
        sleep 1
    done
fi
exists=$(LD_LIBRARY_PATH="$TASK_TOOLS/mariadb/usr/lib/x86_64-linux-gnu" "$TASK_TOOLS/mariadb/usr/bin/mariadb" --no-defaults --socket="$TASK_TOOLS/mysql.sock" -uroot -N -e "SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME='mochi_pet'")
if [ "$exists" = 0 ]; then
    LD_LIBRARY_PATH="$TASK_TOOLS/mariadb/usr/lib/x86_64-linux-gnu" "$TASK_TOOLS/mariadb/usr/bin/mariadb" --no-defaults --socket="$TASK_TOOLS/mysql.sock" -uroot -e 'CREATE DATABASE mochi_pet CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci'
fi
if [ ! -f config/database.php ]; then
    "$TASK_TOOLS/php-cli" -r 'file_put_contents("config/database.php", "<?php\nreturn " . var_export(["host"=>"127.0.0.1","port"=>3306,"name"=>"mochi_pet","user"=>"root","password"=>"","socket"=>"/workspace/tools/mysql.sock","setup_token"=>bin2hex(random_bytes(24))],true) . ";\n");'
    chmod 600 config/database.php
fi
if ! curl --fail --silent --show-error http://127.0.0.1:8080/ -o "$TASK_TOOLS/store-ready.html" 2>/dev/null || ! rg -q 'Mochi Pet' "$TASK_TOOLS/store-ready.html"; then
    nohup "$TASK_TOOLS/php-cli" -S 127.0.0.1:8080 -t /workspace/ThuCung > "$TASK_TOOLS/php-server.log" 2>&1 &
    attempt=0
    until curl --fail --silent http://127.0.0.1:8080/ -o "$TASK_TOOLS/store-ready.html" && rg -q 'Mochi Pet' "$TASK_TOOLS/store-ready.html"; do
        attempt=$((attempt + 1))
        if [ "$attempt" -ge 10 ]; then echo 'PHP readiness failed; inspect /workspace/tools/php-server.log' >&2; exit 1; fi
        sleep 1
    done
fi
curl --fail --silent --show-error 'http://127.0.0.1:8080/index.php?page=shop' -o "$TASK_TOOLS/shop-ready.html"
if ! rg -q 'product-card' "$TASK_TOOLS/shop-ready.html"; then echo 'Store has no visible products; inspect MySQL catalog.' >&2; exit 1; fi
if rg -q 'Warning:|Fatal error:|Parse error:' "$TASK_TOOLS/store-ready.html" "$TASK_TOOLS/shop-ready.html"; then echo 'PHP errors detected.' >&2; exit 1; fi
echo 'Mochi Pet MySQL and storefront ready (local verification only).'
