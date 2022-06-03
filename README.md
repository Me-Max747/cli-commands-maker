# CLI Commands Maker

Для теста достаточно запустить docker

    docker build -t cli-commands-maker .
    docker run --name my-cli-commands-maker -t -i cli-commands-maker

После запуска и входа в shell

    cd usr/src/phpapps/
    php app.php some_command {verbose,overwrite} [log_file=app.log] {unlimited} [methods={create,update,delete}] [paginate=50] {log}

Для успешной работы библиотеки обязательно требуется включенная директива register_argc_argv = On в php.ini
