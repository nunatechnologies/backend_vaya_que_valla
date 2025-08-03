<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateBillboardData extends Command
{
    protected $signature = 'billboards:migrate-data';
    protected $description = 'Migrate data from billboards to billboard_faces';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting data migration...');

        $billboards = DB::table('billboards')->get();

        $count = 0;

        foreach ($billboards as $billboard) 
        {
            $faces = DB::table('billboard_faces')
                ->where('billboard_id', $billboard->id)
                ->get();

            if ($faces->isEmpty()) 
                {
                $this->warn("⚠️  The billboard ID {$billboard->id} has not faces. It is omited.");
                continue;
            }

            foreach ($faces as $face) 
            {
                DB::table('billboard_faces')
                    ->where('id', $face->id)
                    ->update([
                        'name' => $billboard->name,
                        'location' => $billboard->location,
                        'zone_id' => $billboard->zone_id,
                        'advertiser_id' => $billboard->advertiser_id,
                        'city_id' => $billboard->city_id,
                        'billboard_structure_id' => $billboard->billboard_structure_id,
                        'entity_status' => $billboard->entity_status,
                        'size' => $billboard->size,
                        'price_per_month' => $billboard->price_per_month,
                        'traffic_data' => $billboard->traffic_data,
                        'longitude' => $billboard->longitude,
                        'latitude' => $billboard->latitude,
                    ]);
                $count++;
            }
        }

        $this->info("✅ Migration completed. {$count} faces updated.");
        return 0;
    }
}
