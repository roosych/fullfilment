<?php

namespace App\Console\Commands;

use App\Enums\UserRoleEnum;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CreateRolesFromEnum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create roles from UserRoleEnum';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        foreach (UserRoleEnum::cases() as $roleEnum) {
            // Создаём роль, если её ещё нет
            $role = Role::firstOrCreate(['name' => $roleEnum->value]);
            $this->info("Роль '{$roleEnum->value}' создана или уже существует.");
        }

        $this->info('Все роли из enum созданы!');
        return 0;
    }
}
