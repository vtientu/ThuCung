#!/bin/sh
# Codex Debian environment only. Uses the existing isolated checkout; no worktrees.
set -eu
TASK_TOOLS=/workspace/tools
mkdir -p "$TASK_TOOLS/sessions" "$TASK_TOOLS/apt/lists/partial" "$TASK_TOOLS/apt/cache/archives/partial"
if [ ! -x "$TASK_TOOLS/php/usr/bin/php8.4" ] || [ ! -x "$TASK_TOOLS/mariadb/usr/sbin/mariadbd" ]; then
    printf 'deb https://deb.debian.org/debian trixie main\n' > "$TASK_TOOLS/apt/sources.list"
    apt-get -o Dir::Etc::sourcelist="$TASK_TOOLS/apt/sources.list" -o Dir::Etc::sourceparts=- -o Dir::State::lists="$TASK_TOOLS/apt/lists" -o Dir::Cache="$TASK_TOOLS/apt/cache" -o APT::Sandbox::User=agent update
fi
if [ ! -x "$TASK_TOOLS/php/usr/bin/php8.4" ]; then
    mkdir -p "$TASK_TOOLS/php-packages" "$TASK_TOOLS/php"
    cd "$TASK_TOOLS/php-packages"
    apt-get -o Dir::Etc::sourcelist="$TASK_TOOLS/apt/sources.list" -o Dir::Etc::sourceparts=- -o Dir::State::lists="$TASK_TOOLS/apt/lists" download php8.4-cli php8.4-common php8.4-mysql php8.4-opcache php8.4-readline libpcre2-8-0 libsodium23 libedit2 libargon2-1
    for package in ./*.deb; do dpkg-deb -x "$package" "$TASK_TOOLS/php"; done
fi
if [ ! -x "$TASK_TOOLS/mariadb/usr/sbin/mariadbd" ]; then
    mkdir -p "$TASK_TOOLS/mariadb-packages" "$TASK_TOOLS/mariadb"
    cd "$TASK_TOOLS/mariadb-packages"
    apt-get -o Dir::Etc::sourcelist="$TASK_TOOLS/apt/sources.list" -o Dir::Etc::sourceparts=- -o Dir::State::lists="$TASK_TOOLS/apt/lists" download mariadb-server mariadb-server-core mariadb-client-core mariadb-client liburing2 libaio1t64 libnuma1 libmariadb3
    for package in ./*.deb; do dpkg-deb -x "$package" "$TASK_TOOLS/mariadb"; done
fi
cat > "$TASK_TOOLS/php-cli" <<'WRAPPER'
#!/bin/sh
export LD_LIBRARY_PATH="/workspace/tools/php/usr/lib/x86_64-linux-gnu${LD_LIBRARY_PATH:+:$LD_LIBRARY_PATH}"
exec /workspace/tools/php/usr/bin/php8.4 -d extension_dir=/workspace/tools/php/usr/lib/php/20240924 -d extension=pdo.so -d extension=mysqlnd.so -d extension=pdo_mysql.so -d session.save_path=/workspace/tools/sessions "$@"
WRAPPER
chmod +x "$TASK_TOOLS/php-cli"
"$TASK_TOOLS/php-cli" -r 'if (!in_array("mysql", PDO::getAvailableDrivers(), true)) { exit(1); } echo "PHP PDO MySQL ready\n";'
if [ ! -d "$TASK_TOOLS/mysql-data/mysql" ]; then
    LD_LIBRARY_PATH="$TASK_TOOLS/mariadb/usr/lib/x86_64-linux-gnu" "$TASK_TOOLS/mariadb/usr/bin/mariadb-install-db" --no-defaults --basedir="$TASK_TOOLS/mariadb/usr" --datadir="$TASK_TOOLS/mysql-data" --auth-root-authentication-method=normal --skip-test-db
fi
