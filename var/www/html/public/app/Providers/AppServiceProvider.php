<?php

namespace App\Providers;
use Illuminate\Contracts\Events\Dispatcher;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use App\Models\User;
use Auth;


use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Dispatcher $events, Auth $auth)
    {
        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $c = NULL;
            $user = User::find(Auth::user()->id);

            $event->menu->add('MENU PRINCIPAL');
        

                $event->menu->add([
                    'text' => 'Cadastro de Despesas',
                    'url' => '/cupom/index',
                    'icon' => 'far fa-file-alt',
                ]);

                $event->menu->add([
                    'text' => 'Galeria de imagens',
                    'url' => '/images/index',
                    'icon' => 'far fa-images',
                ]);
            

                if(Auth::user()->nivel_acesso == 1){
                    $event->menu->add([
                        'text' => 'Tipos de Despesas',
                        'url' => '/servicos/index',
                        'icon' => 'fa fa-wrench'
                    ]);
                }
        

                if(Auth::user()->nivel_acesso == 1){
                    $event->menu->add([
                        'text' => 'Usuários',
                        'url' => '/usuarios',
                        'icon' => 'far fa-user'
                    ]);
                }

   
        });
    }
}
