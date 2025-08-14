<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\BillboardFace;
use App\Models\Quote;

class UpdateBillboardStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billboards:update-statuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the status for all billboard_faces based in dates from quotes table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $faces = BillboardFace::all();
        $today = now();

        foreach ($faces as $face) 
        {
            if (!$face->rented_from || !$face->available_from) 
            {
                $face->status = 'VERDE';
                $face->save();
                continue;
            }

            $startDate = Carbon::parse($face->rented_from);
            $endDate = Carbon::parse($face->available_from)->copy();

            if ($today->lt($startDate) || $today->gt($endDate)) {
                $status = 'VERDE';
            } else {
                $daysRemaining = $today->diffInDays($endDate, false);
                $status = $daysRemaining > 30 ? 'AMARILLO' : 'ROJO';
            }

            $face->status = $status;
            $face->save();
        }

        $this->info('BillboardFaces status updated.');
    }
}
