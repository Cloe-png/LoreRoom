<?php

namespace App\Console\Commands;

use App\Support\TrashManager;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PruneTrashCommand extends Command
{
    protected $signature = 'trash:prune';

    protected $description = 'Supprime définitivement les éléments en corbeille depuis plus de 30 jours.';

    public function handle(TrashManager $trashManager): int
    {
        $deleted = $trashManager->pruneOlderThan(Carbon::now()->subDays(30));

        $this->info(sprintf('%d élément(s) supprimé(s) définitivement.', $deleted));

        return self::SUCCESS;
    }
}
