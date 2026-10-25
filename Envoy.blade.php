@servers(['prod' => 'goodcat.dev'])

@setup
    $app_dir = '/var/www/goodcat.dev';
@endsetup

@story('deploy')
    pull_repository
    run_composer
    run_node
    optimize_laravel
@endstory

@task('pull_repository')
    echo 'Pulling updates'
    cd {{ $app_dir }}
    git pull
@endtask

@task('run_composer')
    echo 'Installing dependencies'
    cd {{ $app_dir }}
    composer install --optimize-autoloader --no-dev
@endtask

@task('run_node')
    echo 'Compile assets'
    cd {{ $app_dir }}
    source "$HOME/.nvm/nvm.sh"
    nvm install 24
    npm ci
    npx vite build
@endtask

@task('migrate_database')
    echo 'Migrating database'
    cd {{ $app_dir }}
    php artisan migrate --force --isolated
@endtask

@task('optimize_laravel')
    echo 'Optimize Laravel'
    cd {{ $app_dir }}
    php artisan storage:link --force
    php artisan optimize
@endtask

