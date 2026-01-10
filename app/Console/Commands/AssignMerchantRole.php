<?php

namespace App\Console\Commands;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Console\Command;

class AssignMerchantRole extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:assign-merchant-role {user_id : ID пользователя}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Назначить роль merchant пользователю через Spatie Permission';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $userId = $this->argument('user_id');
        
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("Пользователь с ID {$userId} не найден.");
            return 1;
        }

        // Назначаем роль merchant
        $user->assignRole(UserRoleEnum::MERCHANT->value);
        
        // Очищаем кэш разрешений
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        $this->info("Роль 'merchant' успешно назначена пользователю {$user->name} (ID: {$user->id})");
        
        return 0;
    }
}

