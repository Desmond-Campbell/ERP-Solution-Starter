<?php

namespace App\Modules;

/**
 * ServiceProvider
 *
 * The service provider for the modules. After being registered
 * it will make sure that each of the modules are properly loaded
 * i.e. with their routes, views etc.
 *
 * @author kundan Roy <query@programmerlab.com>
 * @package App\Modules
 */

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use App\Module;
use Config;
use File;
use Schema;

class ModulesServiceProvider extends ServiceProvider {

    /**
     * Load modules with models, controllers and views
     *
     * @return void routeModule
     */
    public function boot() {
        
        if ( Schema::hasTable('module') ) {

            $modules = Module::all();

        } else {

            $modules = [ (object) [ 'slug' => 'Core', 'name' => 'Core' ] ];

        }

        foreach ( $modules as $M ) {

            $module = $M->slug;
            $namespace = '';

            $resources = [];
            $resources['routes'] = [ 'routes', 'Routes' ];
            $resources['views'] = [ 'views', 'Views' ];
            $resources['config'] = [ 'config', 'Config', 'Menu', 'menu', 'Menus', 'menus', 'validation', 'Validation', 'validations', 'Validations', 'Forms', 'forms'  ];

            foreach ( $resources['routes'] as $route_folder ) {

                $path = __DIR__ . '/' . $module . '/' . $route_folder;

                if ( is_dir( $path ) ) {

                    $files = File::allFiles( $path );

                    foreach ( $files as $file ) { 

                        include $file->getPathname();

                    }
            
                }

            }

            foreach ( $resources['views'] as $views_folder ) {

                $path = __DIR__ . '/' . $module . '/' . $views_folder;

                if ( is_dir( $path ) ) {

                    $this->loadViewsFrom( $path, $module );
                    
                }

            }

            foreach ( $resources['config'] as $config_folder ) {

                $path = __DIR__ . '/' . $module . '/' . $config_folder;

                if ( is_dir( $path ) ) {

                    $files = $this->app['files']->files( $path );

                    foreach($files as $file)
                    {
                        $config = $this->app['files']->getRequire($file);

                        $name = $this->app['files']->name($file);

                        if($name === 'config')
                        {
                            foreach($config as $key => $value) $this->app['config']->set($namespace . $key , $value);
                        }
                        
                        if ( in_array( strtolower( $config_folder ), [ 'menu', 'menus' ] ) ) {

                            $name = 'menu';

                            $existing_config = $this->app['config']->get($namespace . $name);

                            if ( $existing_config ) {

                                foreach ( $existing_config as $key => $value ) {

                                    foreach ( $value as $position => $item ) {

                                        if ( isset( $config[$key][$position] ) ) {

                                            $adjustment = rand( 1, 100 ) / 100;
                                            
                                            if ( rand( 1, 1000 ) > 500 ) {

                                                $adjustment *= -1;

                                            }

                                            $position += $adjustment;
                                        
                                        }

                                        $position = (float) $position;

                                        $config[$key]["$position"] = $item;
                                    
                                    }

                                }

                            }

                            $this->app['config']->set($namespace . $name , $config);

                        } elseif ( in_array( strtolower( $config_folder ), [ 'validation', 'validations' ] ) ) {

                            $name = 'validations';

                            $existing_config = $this->app['config']->get($namespace . $name);

                            if ( $existing_config ) {

                                $this->app['config']->set( $namespace . $name , array_merge( $config, $existing_config ) );

                            } else {

                                $this->app['config']->set($namespace . $name , $config);

                            }

                        } elseif ( in_array( strtolower( $config_folder ), [ 'form', 'forms' ] ) ) {

                            $name = 'forms';

                            $existing_config = $this->app['config']->get($namespace . $name);

                            if ( $existing_config ) {

                                $this->app['config']->set( $namespace . $name , array_merge( $config, $existing_config ) );

                            } else {

                                $this->app['config']->set($namespace . $name , $config);

                            }

                        } else {

                            $this->app['config']->set($namespace . $name , $config);

                        }
                        
                    }

                }

            }

        }

        foreach ( Config::get('commands') as $key => $class ) {

            $this->app->singleton($key, function ($app) use ($class) {
                return $app[$class];
            });

            $this->commands($key);

        }

    }

    public function register() { 

    }

}