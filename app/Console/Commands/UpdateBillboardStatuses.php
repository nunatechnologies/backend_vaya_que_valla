<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\BillboardFace;
use App\Models\BillboardFaceStatusLog;
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
        // Solo procesa vallas con ambas fechas. Si no tiene fechas, el admin la controla manualmente.
        $faces = BillboardFace::whereNotNull('rented_from')
            ->whereNotNull('available_from')
            ->get();
        $today = now();
        $touched = 0;

        foreach ($faces as $face)
        {
            $startDate = Carbon::parse($face->rented_from);
            $endDate = Carbon::parse($face->available_from);

            $clearDates = false;

            if ($today->lt($startDate)) {
                // Antes de que arranque el alquiler — la valla aún no debería marcarse como rentada
                $status = 'VERDE';
            } elseif ($today->gt($endDate)) {
                // Contrato vencido: liberar valla y limpiar fechas para que el admin pueda manipular libre
                $status = 'VERDE';
                $clearDates = true;
            } else {
                // Durante el alquiler: ROJO normal, AMARILLO en el último mes
                $daysRemaining = $today->diffInDays($endDate, false);
                $status = $daysRemaining > 30 ? 'ROJO' : 'AMARILLO';
            }

            $needsUpdate = $face->status !== $status || $clearDates;
            if (!$needsUpdate) {
                continue;
            }

            if ($face->status !== $status) {
                BillboardFaceStatusLog::create([
                    'billboard_face_id' => $face->id,
                    'from_status' => $face->status,
                    'to_status' => $status,
                ]);
            }

            $face->status = $status;
            if ($clearDates) {
                $face->rented_from = null;
                $face->available_from = null;
            }
            $face->save();
            $touched++;
        }

        $this->info("BillboardFaces status updated. Touched: {$touched}");
    }
}
