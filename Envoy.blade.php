@servers(['prod' => 'goodcat.dev'])

@setup
    $app_dir = '/var/www/goodcat.dev';
@endsetup

@story('deploy')
    pull_repository
    run_composer
    run_node
    migrate_database
    optimize_laravel
    purge_cdn
    search_index
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

@task('purge_cdn')
    echo 'Purge CDN cache'
    cd {{ $app_dir }}
    php artisan cdn:purge
@endtask

@task('search_index')
    echo 'Update search index'
    cd {{ $app_dir }}
    php artisan search:index
@endtask
