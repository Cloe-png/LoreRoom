<?php

namespace App\Console\Commands;

use App\Models\UserLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PruneUserLogsCommand extends Command
{
    protected $signature = 'logs:prune';

    protected $description = 'Supprime les logs utilisateurs de plus de 90 jours.';

    public function handle(): int
    {
        $deleted = UserLog::query()
            ->where('action_at', '<=', Carbon::now()->subDays(90))
            ->delete();

        $this->info(sprintf('%d log(s) supprimé(s).', $deleted));

        return self::SUCCESS;
    }
}
