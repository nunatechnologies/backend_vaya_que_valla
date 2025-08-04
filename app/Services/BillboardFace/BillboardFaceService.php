<?php

namespace App\Services\BillboardFace;

use Illuminate\Support\Facades\DB;
use App\Http\Messages\ErrorMessages;
use App\Imports\BillboardsImport;
use App\Models\BillboardFace;
use App\Models\BillboardStructure;
use App\Models\City;
use App\Models\Province;
use App\Models\User;
use App\Models\Zone;
use App\Repositories\BillboardFace\BillboardFaceRepositoryInterface;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;

class BillboardFaceService
{
    protected $billboardfaceRepository;

    public function __construct(BillboardFaceRepositoryInterface $billboardfaceRepository)
    {
        $this->billboardfaceRepository = $billboardfaceRepository;
    }

    public function getBillboardFaceById($id){
        return $this->isBillboardFaceExists($id);
    }

    private function isBillboardFaceExists($id)
    {
        return $this->billboardfaceRepository->find($id)
            ?? throw new \Exception(ErrorMessages::OBJECT_NOT_FOUND, 404);
    }

    public function createBillboardFace($data)
    {
        return DB::transaction(function () use ($data) {
            return $this->billboardfaceRepository->create($data);
        });
    }

    public function updateBillboardFace($id, $data)
    {
        return $this->billboardfaceRepository->update($id, $data);
    }

    public function billboardFaceBulkUpsert($data)
    {
        $import = new BillboardsImport();
        $sheet = Excel::toArray($import, request()->file('file'))[0];
        foreach ($sheet as $index => $row) 
        {
            [$structure, $province, $department, $city, $code, $location, $locationDetail, $reference,
            $size, $face, $pricePerMonth, $gmapUrl, $coordinates, $availability, $availableFrom, $energy, $provider, $zoneName] = $row;

            if ($index === 0 || $code == "" || $code === null) continue; // Jump headers and empty codes

            // Obtener o crear registros relacionados
            $structure = BillboardStructure::firstOrCreate(['name' => $structure]);
            $province = Province::firstOrCreate(['name' => $province]);
            $city = City::firstOrCreate(['name' => $city, 'province_id' => $province->id, 'department' => $department]);
            $zoneId = NULL;
            if ($zoneName != "" && !is_null($zoneName)) 
            {
                $zone = Zone::firstOrCreate(['name' => $zoneName]);
                $zoneId = $zone->id;
            }
            
            $availableFrom = null;
            if (!empty($availableFromRaw)) 
            {
                $parsedDate = \DateTime::createFromFormat('d/m/Y', trim($availableFromRaw));
                if ($parsedDate && $parsedDate->format('d/m/Y') === trim($availableFromRaw)) {
                    $availableFrom = $parsedDate->format('Y-m-d');
                }
            }

            // Coordinates
            [$lat, $lng] = array_map('trim', explode(',', $coordinates));

            // status
            $status = strtoupper(trim($availability)) === 'DISPONIBLE' ? 'VERDE' : 'ROJO';

            BillboardFace::updateOrCreate(
                ['code' => $code],
                [
                    'face' => $face??"",
                    'name' => $reference??"",
                    'location' => $location,
                    'location_detail' => $locationDetail??"",
                    'size' => $size,
                    'price_per_month' => floatval($pricePerMonth),
                    'traffic_data' => '',
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'entity_status' => 'active',
                    'status' => $status,
                    'available_from' => $availableFrom,
                    'billboard_structure_id' => $structure->id,
                    'zone_id' => $zoneId,
                    'city_id' => $city->id,
                    // 'advertiser_id' => null,
                ]
            );
        }
        return $sheet;
    }

    public function getAllBillboardFacePagination($datos)
    {
        $query = $this->billboardfaceRepository->allquery();

        if ($datos->filled('search')) {
            $search = $datos->query('search');
            $query->where('code', 'like', '%' . $datos->query('search') . '%')
				->orWhere('face', 'like', '%' . $datos->query('search') . '%')
				->orWhere('location_detail', 'like', '%' . $datos->query('search') . '%')
                ->orWhere('location', 'like', '%' . $datos->query('search') . '%')
                ->orWhere('name', 'like', '%' . $datos->query('search') . '%');

        }
        if ($datos->filled('city_id')) 
        {
            $cityId = $datos->query('city_id');
            $query->where('city_id', $cityId);
        }
        if ($datos->filled('face')) 
        {
            $face = $datos->query('face');
            $query->where('face', $face);
        }
        if ($datos->filled('ids')) {
            $ids = collect(explode(',', $datos->query('ids')))
                    ->filter(fn($id) => is_numeric($id))
                    ->map(fn($id) => (int) $id)
                    ->toArray();

            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }
        if ($datos->query('sortBy') && $datos->query('orderBy')) {
            $query->orderBy($datos->query('sortBy'), $datos->query('orderBy'));
        }

        return $query->paginate($datos->query('itemsPerPage') ?? 10);
    }
}
