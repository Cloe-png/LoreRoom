<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MakeUserChef extends Command
{
    protected $signature = 'user:make-chef {email}';
    protected $description = 'Attribue le rôle chef à un compte après confirmation';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();
        if (!$user) {
            $this->error('Aucun compte ne correspond à cette adresse.');
            return self::FAILURE;
        }
        if (!$this->confirm('Confirmer l’attribution du rôle chef ?')) {
            return self::SUCCESS;
        }

        DB::transaction(function () use ($user) {
            $user->forceFill(['role' => 'chef'])->save();
            UserLog::logAction(null, 'chef_role_assigned', null, 'user:' . $user->id);
        });
        $this->info('Rôle chef attribué.');
        return self::SUCCESS;
    }
}
