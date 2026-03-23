<?php

namespace App\Services\BillboardFace;

use App\Imports\BillboardsImport;
use App\Models\BillboardFace;
use App\Models\BillboardStructure;
use App\Models\City;
use App\Models\Province;
use App\Models\Zone;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class BillboardFaceImportService
{
    /**
     * Preview the uploaded Excel file: extract headers, first rows, and save temp file.
     */
    public function preview($file)
    {
        $sheet = Excel::toArray(new BillboardsImport(), $file)[0];

        $headers = $sheet[0] ?? [];
        $dataRows = array_filter(array_slice($sheet, 1), fn($row) => !empty(array_filter($row, fn($v) => $v !== null && $v !== '')));
        $previewRows = array_slice($dataRows, 0, 5);
        $totalRows = count($dataRows);

        $fileId = (string) Str::uuid();
        $tempPath = "temp/imports/{$fileId}.xlsx";

        // Asegurar que el directorio temporal existe
        $tempDir = storage_path('app/private/temp/imports');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        Storage::disk('local')->put($tempPath, file_get_contents($file->getRealPath()));

        return [
            'headers' => $headers,
            'preview_rows' => $previewRows,
            'total_rows' => $totalRows,
            'file_id' => $fileId,
        ];
    }

    /**
     * Validate all rows from a previously uploaded file using the provided column mapping.
     */
    public function validate($fileId, $mapping)
    {
        $filePath = storage_path("app/private/temp/imports/{$fileId}.xlsx");

        if (!file_exists($filePath)) {
            throw new \Exception('Archivo no encontrado. Suba el archivo nuevamente.', 404);
        }

        $sheet = Excel::toArray(new BillboardsImport(), $filePath)[0];
        $rows = array_slice($sheet, 1); // skip header

        $errors = [];
        $newCount = 0;
        $updateCount = 0;

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // 1-based, +1 for header

            // Ignorar filas completamente vacías
            $rowValues = array_filter($row, fn($v) => $v !== null && $v !== '');
            if (empty($rowValues)) continue;

            // Extract code using mapping
            $code = isset($mapping['code']) ? trim($row[$mapping['code']] ?? '') : '';

            if (empty($code)) {
                $errors[] = ['row' => $rowNumber, 'field' => 'code', 'message' => 'El código es obligatorio'];
                continue;
            }

            // Validate coordinates if mapping exists
            if (isset($mapping['coordinates'])) {
                $coordinates = trim($row[$mapping['coordinates']] ?? '');
                if (!empty($coordinates) && strpos($coordinates, ',') === false) {
                    $errors[] = ['row' => $rowNumber, 'field' => 'coordinates', 'message' => 'Las coordenadas deben contener una coma separando latitud y longitud'];
                }
            }

            // Validate price if mapping exists
            if (isset($mapping['price_per_month'])) {
                $price = $row[$mapping['price_per_month']] ?? '';
                if (!empty($price) && !is_numeric($price)) {
                    $errors[] = ['row' => $rowNumber, 'field' => 'price_per_month', 'message' => 'El precio debe ser numérico'];
                }
            }

            // Check if code exists in DB
            $exists = BillboardFace::where('code', $code)->exists();
            if ($exists) {
                $updateCount++;
            } else {
                $newCount++;
            }
        }

        return [
            'total' => count($rows),
            'new_count' => $newCount,
            'update_count' => $updateCount,
            'errors_count' => count($errors),
            'error_details' => $errors,
        ];
    }

    /**
     * Execute the import for a batch of rows (offset to offset+limit).
     */
    public function execute($fileId, $mapping, $offset, $limit)
    {
        $filePath = storage_path("app/private/temp/imports/{$fileId}.xlsx");

        if (!file_exists($filePath)) {
            throw new \Exception('Archivo no encontrado. Suba el archivo nuevamente.', 404);
        }

        $sheet = Excel::toArray(new BillboardsImport(), $filePath)[0];
        $allRows = array_slice($sheet, 1); // skip header
        $totalRows = count($allRows);
        $rows = array_slice($allRows, $offset, $limit);

        $processed = 0;
        $created = 0;
        $updated = 0;
        $errorCount = 0;
        $errorDetails = [];

        foreach ($rows as $index => $row) {
            $rowNumber = $offset + $index + 2; // 1-based, +1 for header

            try {
                // Ignorar filas completamente vacías
                $rowValues = array_filter($row, fn($v) => $v !== null && $v !== '');
                if (empty($rowValues)) continue;

                $code = isset($mapping['code']) ? trim($row[$mapping['code']] ?? '') : '';

                if (empty($code)) {
                    $errorCount++;
                    $errorDetails[] = ['row' => $rowNumber, 'field' => 'code', 'message' => 'El código es obligatorio'];
                    continue;
                }

                // Structure
                $structureName = isset($mapping['structure']) ? trim($row[$mapping['structure']] ?? '') : '';
                $structure = !empty($structureName)
                    ? BillboardStructure::firstOrCreate(['name' => $structureName])
                    : null;

                // Province
                $provinceName = isset($mapping['province']) ? trim($row[$mapping['province']] ?? '') : '';
                $province = !empty($provinceName)
                    ? Province::firstOrCreate(['name' => $provinceName])
                    : null;

                // Department
                $department = isset($mapping['department']) ? trim($row[$mapping['department']] ?? '') : '';

                // City — busca existente case-insensitive + alias comunes antes de crear
                $cityName = isset($mapping['city']) ? trim($row[$mapping['city']] ?? '') : '';
                $city = null;
                if (!empty($cityName)) {
                    $cityAliases = [
                        'santa cruz' => 'santa cruz de la sierra',
                    ];
                    $lookup = mb_strtolower($cityName);
                    $lookup = $cityAliases[$lookup] ?? $lookup;
                    $city = City::whereRaw('LOWER(TRIM(name)) = ?', [$lookup])->first();
                    if (!$city) {
                        $city = City::create([
                            'name' => $cityName,
                            'province_id' => $province?->id,
                            'department' => $department ?: null,
                        ]);
                    }
                }

                // Zone
                $zoneName = isset($mapping['zone']) ? trim($row[$mapping['zone']] ?? '') : '';
                $zone = null;
                if (!empty($zoneName)) {
                    $zone = Zone::firstOrCreate(['name' => $zoneName]);
                }

                // Coordinates
                $lat = null;
                $lng = null;
                if (isset($mapping['coordinates'])) {
                    $coordinates = trim($row[$mapping['coordinates']] ?? '');
                    if (!empty($coordinates) && strpos($coordinates, ',') !== false) {
                        [$lat, $lng] = array_map('trim', explode(',', $coordinates));
                    }
                }

                // Availability / Status
                $availability = isset($mapping['availability']) ? trim($row[$mapping['availability']] ?? '') : '';
                $status = strtoupper($availability) === 'DISPONIBLE' ? 'VERDE' : 'ROJO';

                // Available from date
                $availableFrom = null;
                if (isset($mapping['available_from'])) {
                    $availableFromRaw = trim($row[$mapping['available_from']] ?? '');
                    if (!empty($availableFromRaw)) {
                        $parsedDate = \DateTime::createFromFormat('d/m/Y', $availableFromRaw);
                        if ($parsedDate && $parsedDate->format('d/m/Y') === $availableFromRaw) {
                            $availableFrom = $parsedDate->format('Y-m-d');
                        }
                    }
                }

                // Other mapped fields
                $location = isset($mapping['location']) ? trim($row[$mapping['location']] ?? '') : '';
                $locationDetail = isset($mapping['location_detail']) ? trim($row[$mapping['location_detail']] ?? '') : '';
                $reference = isset($mapping['name']) ? trim($row[$mapping['name']] ?? '') : '';
                $size = isset($mapping['size']) ? trim($row[$mapping['size']] ?? '') : '';
                $face = isset($mapping['face']) ? trim($row[$mapping['face']] ?? '') : '';
                $pricePerMonth = isset($mapping['price_per_month']) ? floatval($row[$mapping['price_per_month']] ?? 0) : 0;

                // Check if updating or creating
                $existing = BillboardFace::where('code', $code)->exists();

                BillboardFace::updateOrCreate(
                    ['code' => $code],
                    [
                        'face' => $face,
                        'name' => $reference,
                        'location' => $location,
                        'location_detail' => $locationDetail,
                        'size' => $size,
                        'price_per_month' => $pricePerMonth,
                        'traffic_data' => '',
                        'latitude' => $lat,
                        'longitude' => $lng,
                        'entity_status' => 'active',
                        'status' => $status,
                        'available_from' => $availableFrom,
                        'billboard_structure_id' => $structure?->id,
                        'zone_id' => $zone?->id,
                        'city_id' => $city?->id,
                    ]
                );

                $processed++;
                if ($existing) {
                    $updated++;
                } else {
                    $created++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                $errorDetails[] = ['row' => $rowNumber, 'field' => 'general', 'message' => $e->getMessage()];
            }
        }

        $nextOffset = $offset + $limit;
        $hasMore = $nextOffset < $totalRows;

        return [
            'processed' => $processed,
            'created' => $created,
            'updated' => $updated,
            'errors' => $errorCount,
            'error_details' => $errorDetails,
            'has_more' => $hasMore,
            'next_offset' => $hasMore ? $nextOffset : null,
        ];
    }

    /**
     * Import images and assign them to billboard faces by filename matching.
     */
    public function importImages($files)
    {
        $processed = 0;
        $assigned = 0;
        $notFound = [];
        $replaced = 0;

        foreach ($files as $file) {
            $processed++;
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $faces = collect();

            // If filename contains "-" followed by a letter (e.g., "1-A"), match exact code
            if (preg_match('/^(.+)-([a-zA-Z])$/', $originalName)) {
                $face = BillboardFace::where('code', $originalName)->first();
                if ($face) {
                    $faces->push($face);
                }
            }
            // If filename is just a number (e.g., "1"), match all faces with code LIKE "1-%"
            elseif (is_numeric($originalName)) {
                $faces = BillboardFace::where('code', 'LIKE', "{$originalName}-%")->get();
            }
            // Otherwise try exact match
            else {
                $face = BillboardFace::where('code', $originalName)->first();
                if ($face) {
                    $faces->push($face);
                }
            }

            if ($faces->isEmpty()) {
                $notFound[] = $file->getClientOriginalName();
                continue;
            }

            foreach ($faces as $face) {
                try {
                    // Check if face already has media (will be replaced)
                    $hadMedia = $face->getFirstMedia() !== null;

                    // Process image with GD
                    $tempPath = $this->processImage($file->getRealPath());

                    $face->addMedia($tempPath)->toMediaCollection();

                    $assigned++;
                    if ($hadMedia) {
                        $replaced++;
                    }
                } catch (\Exception $e) {
                    // Skip individual image errors silently
                    continue;
                }
            }
        }

        return [
            'processed' => $processed,
            'assigned' => $assigned,
            'not_found' => $notFound,
            'replaced' => $replaced,
        ];
    }

    /**
     * Process an image: resize to max 1500px width and convert to WebP.
     */
    private function processImage(string $sourcePath): string
    {
        $imageInfo = getimagesize($sourcePath);
        $mime = $imageInfo['mime'] ?? '';

        $source = match ($mime) {
            'image/jpeg' => imagecreatefromjpeg($sourcePath),
            'image/png' => imagecreatefrompng($sourcePath),
            'image/webp' => imagecreatefromwebp($sourcePath),
            default => imagecreatefromjpeg($sourcePath),
        };

        $origWidth = imagesx($source);
        $origHeight = imagesy($source);

        // Resize to max 1500px width, maintain aspect ratio, don't upscale
        $maxWidth = 1500;
        if ($origWidth > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = (int) round($origHeight * ($maxWidth / $origWidth));

            $resized = imagecreatetruecolor($newWidth, $newHeight);
            // Preserve transparency for PNG
            imagealphablending($resized, false);
            imagesavealpha($resized, true);

            imagecopyresampled($resized, $source, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);
            imagedestroy($source);
            $source = $resized;
        }

        // Save as WebP to temp file
        $tempPath = tempnam(sys_get_temp_dir(), 'billboard_') . '.webp';
        imagewebp($source, $tempPath, 85);
        imagedestroy($source);

        return $tempPath;
    }
}
