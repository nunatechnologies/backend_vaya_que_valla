<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\info;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\note;
use function Laravel\Prompts\confirm;

class CodeGeneratorCommand extends Command
{
    protected $signature = 'codegen:generate {--table=}';
    protected $description = 'Generate files for repostory, service and resources based in the DB table';
    protected $tableName;
    protected $object;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->tableName = $this->option('table');

        if (!$this->tableName) 
        {
            $this->error('You must to specify the table name with --table=');
            return;
        }
        
        // Let's convert the table name to singular and PascalCase word
        $this->object = Str::studly(Str::singular($this->tableName));

        // Let's define the files path
        $fileDefinitions = [
            'Model' => [
                'php' => "app/Models/{$this->object}.php",
                'stub' => "stubs/model.stub",
                'method' => 'generateModelFile',
            ],
            'Request' => [
                'php' => "app/Http/Requests/{$this->object}/{$this->object}Request.php",
                'stub' => "stubs/request.stub",
                'method' => 'generateRequestFile',
            ],
            'PatchRequest' => [
                'php' => "app/Http/Requests/{$this->object}/Patch{$this->object}Request.php",
                'stub' => "stubs/patch_request.stub",
                'method' => 'generatePatchRequestFile',
            ],
            'Controller' => [
                'php' => "app/Http/Controllers/{$this->object}Controller.php",
                'stub' => "stubs/controller.stub",
                'method' => 'generateControllerFile',
            ],
            'RepositoryInterface' => [
                'php' => "app/Repositories/{$this->object}/{$this->object}RepositoryInterface.php",
                'stub' => "stubs/repository_interface.stub",
                'method' => 'generateFile'
            ],
            'Repository' => [
                'php' => "app/Repositories/{$this->object}/{$this->object}Repository.php",
                'stub' => "stubs/repository.stub",
                'method' => 'generateFile'
            ],
            'Service' => [
                'php' => "app/Services/{$this->object}/{$this->object}Service.php",
                'stub' => "stubs/service.stub",
                'method' => 'generateServiceFile'
            ],
            'Resource' => [
                'php' => "app/Http/Resources/{$this->object}/{$this->object}Resource.php",
                'stub' => "stubs/resource.stub",
                'method' => 'generateResourceFile'
            ],
        ];

        $filesToCreate = multiselect(
            label: 'Whats files do you need to create for '.$this->object.'?',
            options: array_keys($fileDefinitions),
            scroll: count($fileDefinitions)
        );

        foreach ($fileDefinitions as $key => &$config) 
        {
            if (!in_array($key, $filesToCreate)) 
            {
                unset($fileDefinitions[$key]);
            }
        }

        foreach ($fileDefinitions as $key => $fileDefinition) 
        {
            if (!file_exists(base_path($fileDefinition['stub']))) 
            {
                warning("File does not exists: {$fileDefinition['stub']}");
                return;
            }

            if (file_exists(base_path($fileDefinition['php'])))
            {
                $overwrite = confirm(
                    label: "Do you want to overwrite ".basename($fileDefinition['php'])."?",
                    default: false,
                );
                if (!$overwrite) 
                {
                    unset($fileDefinitions[$key]);
                }
            }
        }
        
        // Let's create the directories if these doesn't exits
        foreach ($fileDefinitions as $fileDefinition) 
        {
            File::ensureDirectoryExists(dirname(base_path($fileDefinition['php'])));
        }

        // Let's generate the files with its respective stubs
        foreach ($fileDefinitions as $key => $fileDefinition)
        {
            $this->{$fileDefinition['method']}($fileDefinition);
        }

        info("Files generated successfully for {$this->object} 🎉");
        $files = array_column($fileDefinitions,'php');
        note(implode("\n",$files));
        info("Copy this to AppServiceProvider, inside of register method");
        note("\t\$this->app->bind(
            {$this->object}RepositoryInterface::class,
            {$this->object}Repository::class
        );");
        info("Copy this to api.php routes file");
        note("\tRoute::get('/{$this->tableName}', [{$this->object}Controller::class, 'list_".strtolower($this->object)."_pagination']);
        Route::post('/{$this->tableName}', [{$this->object}Controller::class, 'register']);
        Route::get('/{$this->tableName}/{id}', [{$this->object}Controller::class, 'get_".strtolower($this->object)."']);
        Route::put('/{$this->tableName}/{id}', [{$this->object}Controller::class, 'update_".strtolower($this->object)."']);");

        Artisan::call('l5-swagger:generate');
    }

    private function generateModelFile($fileDefinition)
    {
        $columns = DB::getSchemaBuilder()->getColumnListing($this->tableName);
        $noFillables = ['id','created_at','updated_at','deleted_at','created_by','updated_by','deleted_by'];
        foreach ($columns as $key => $column) 
        {
            if (in_array($column, $noFillables)) 
            {
                unset($columns[$key]);
            }
        }

        $fillables = "[\n";
        foreach ($columns as $key => $column) 
        {
            $fillables .= $key == 0?"[":"";
            $fillables .= "\t\t'{$column}',";
            $fillables .= ($key) == count($columns)?"\n\t]":"\n";
        }

        $stubPath = base_path($fileDefinition['stub']);
        $content = File::get($stubPath);
        $content = str_replace(['{{Object}}', '{{object}}', '{{tableName}}', '{{TableName}}', '{{fillables}}'], [$this->object, strtolower($this->object), $this->tableName, ucfirst($this->tableName), $fillables], $content);
        File::put(base_path($fileDefinition['php']), $content);
    }

    private function generateRequestFile($fileDefinition)
    {
        $columns = DB::getSchemaBuilder()->getColumns($this->tableName);
        $noFillables = ['id','created_at','updated_at','deleted_at','created_by','updated_by','deleted_by'];
        foreach ($columns as $key => $column) 
        {
            if (in_array($columns[$key]['name'], $noFillables)) 
            {
                unset($columns[$key]);
            }
        }

        $rules = "[\n";
        foreach ($columns as $key => $column) 
        {
            $name = $column['name'];
            $type = $column['type_name'];
            $maxLength = null;
            $rules .= "\t\t\t'".$name."' => ['required',";

            switch ($type) 
            {
                case 'bigint':
                case 'int':
                    $rules .= "'integer',";
                    break;
    
                case 'varchar':
                    if (preg_match('/\((\d+)\)/', $column['type'], $matches)) 
                    {
                        $maxLength = $matches[1];
                        $rules .= "'string','max:$maxLength',";
                    }
                    break;
    
                case 'decimal':
                    if (preg_match('/\((\d+),(\d+)\)/', $column['type'], $matches)) 
                    {
                        $precision = (int) $matches[1] - (int) $matches[2];
                        $rules .= "'numeric','between:0,".str_repeat('9', $precision) . "." . str_repeat('9', $matches[2])."',";
                    }
                    break;
            }
            $rules = substr($rules,0,-1);
            $rules .= "]";
            $rules .= ($key) == count($columns)?"":",\n";
        }
        $rules .= "\n\t\t]";

        $stubPath = base_path($fileDefinition['stub']);
        $content = File::get($stubPath);
        $content = str_replace(['{{Object}}', '{{object}}', '{{rules}}'], [$this->object, strtolower($this->object), $rules], $content);
        File::put(base_path($fileDefinition['php']), $content);
    }

    private function generatePatchRequestFile($fileDefinition)
    {
        $columns = DB::getSchemaBuilder()->getColumns($this->tableName);
        $noFillables = ['id','created_at','updated_at','deleted_at','created_by','updated_by','deleted_by'];
        foreach ($columns as $key => $column) 
        {
            if (in_array($columns[$key]['name'], $noFillables)) 
            {
                unset($columns[$key]);
            }
        }

        $rules = "[\n";
        foreach ($columns as $key => $column) 
        {
            $name = $column['name'];
            $type = $column['type_name'];
            $maxLength = null;
            $rules .= "\t\t\t'".$name."' => ['sometimes',";

            switch ($type) 
            {
                case 'bigint':
                case 'int':
                    $rules .= "'integer',";
                    break;
    
                case 'varchar':
                    if (preg_match('/\((\d+)\)/', $column['type'], $matches)) 
                    {
                        $maxLength = $matches[1];
                        $rules .= "'string','max:$maxLength',";
                    }
                    break;
    
                case 'decimal':
                    if (preg_match('/\((\d+),(\d+)\)/', $column['type'], $matches)) 
                    {
                        $precision = (int) $matches[1] - (int) $matches[2];
                        $rules .= "'numeric','between:0,".str_repeat('9', $precision) . "." . str_repeat('9', $matches[2])."',";
                    }
                    break;
            }
            $rules = substr($rules,0,-1);
            $rules .= "]";
            $rules .= ($key) == count($columns)?"":",\n";
        }
        $rules .= "\n\t\t]";

        $stubPath = base_path($fileDefinition['stub']);
        $content = File::get($stubPath);
        $content = str_replace(['{{Object}}', '{{object}}', '{{rules}}'], [$this->object, strtolower($this->object), $rules], $content);
        File::put(base_path($fileDefinition['php']), $content);
    }

    private function generateControllerFile($fileDefinition)
    {
        $columns = DB::getSchemaBuilder()->getColumns($this->tableName);
        $noFillables = ['id','created_at','updated_at','deleted_at','created_by','updated_by','deleted_by'];
        foreach ($columns as $key => $column) 
        {
            if (in_array($columns[$key]['name'], $noFillables)) 
            {
                unset($columns[$key]);
            }
        }
        $typeMapping = [
            'varchar' => 'string',
            'bigint' => 'number',
            'decimal' => 'number'
        ];
        //Register doc - start
        $registerRequiredFields = [];
        $registerProperties = [];
        foreach ($columns as $column)
        {
            $registerRequiredFields[] = $column['name'];
        
            $openApiType = $typeMapping[$column['type_name']] ?? 'string';
        
            preg_match('/\((\d+),?(\d+)?\)/', $column['type'], $matches);
            $maxLength = $matches[1] ?? null;
        
            $property = " *                 @OA\Property(property=\"{$column['name']}\", type=\"{$openApiType}\"";
            
            if ($maxLength) 
            {
                $property .= ", maxLength={$maxLength}";
            }
        
            if ($column['type_name'] === 'decimal') 
            {
                $property .= ', format="float"';
            }
        
            $property .= "),";
        
            $registerProperties[] = $property;
        }
        $registerDoc = "\t *             required={" . implode(", ", array_map(fn($f) => "\"$f\"", $registerRequiredFields)) . "},\n";
        $registerDoc .= "\t".implode("\n\t", $registerProperties);
        //Register doc - end
        //Update doc - start
        $updateRequiredFields = [];
        $updateProperties = [];
        foreach ($columns as $column)
        {
            $updateRequiredFields[] = $column['name'];
        
            $openApiType = $typeMapping[$column['type_name']] ?? 'string';
        
            preg_match('/\((\d+),?(\d+)?\)/', $column['type'], $matches);
            $maxLength = $matches[1] ?? null;
        
            $property = " *                 @OA\Property(property=\"{$column['name']}\", type=\"{$openApiType}\"";
            
            if ($maxLength) 
            {
                $property .= ", maxLength={$maxLength}";
            }
        
            if ($column['type_name'] === 'decimal') 
            {
                $property .= ', format="float"';
            }
        
            $property .= "),";
        
            $updateProperties[] = $property;
        }
        $updateDoc = "\t *             required={" . implode(", ", array_map(fn($f) => "\"$f\"", $updateRequiredFields)) . "},\n";
        $updateDoc .= "\t".implode("\n\t", $updateProperties);
        //Update doc - end
        $stubPath = base_path($fileDefinition['stub']);
        $content = File::get($stubPath);
        $content = str_replace(['{{Object}}', '{{object}}', '{{tableName}}', '{{TableName}}','{{registerDoc}}','{{updateDoc}}'], [$this->object, strtolower($this->object), $this->tableName, ucfirst($this->tableName),$registerDoc,$updateDoc], $content);
        File::put(base_path($fileDefinition['php']), $content);
    }

    private function generateServiceFile($fileDefinition)
    {
        $columns = DB::getSchemaBuilder()->getColumnListing($this->tableName);
        array_push($columns,'Continue without criteria to search');
        $feedback = multiselect(
            label: 'Choose one or more options to use in the search criteria.',
            options: $columns,
            scroll: count($columns)
        );
        
        if (in_array('Continue without criteria to search',$feedback)) 
        {
            $feedback = [];
        }
        
        $query = "";
        foreach ($feedback as $key => $column) 
        {
            if(count($feedback) == 1)
            {
                $query .= "\$query->where('{$column}', 'like', '%' . \$datos->query('search') . '%');\n";
            }
            else
            {
                $closeQuery = ($key+1) == count($feedback)?";":""; 
                if ($key == 0) 
                {
                    $query .= "\$query->where('{$column}', 'like', '%' . \$datos->query('search') . '%')\n";
                }
                else
                {
                    $query .= "\t\t\t\t->orWhere('{$column}', 'like', '%' . \$datos->query('search') . '%'){$closeQuery}\n";
                }
            }
        }

        $query = $query==""?"//None attribute was selected to apply search criteria":$query;

        $content = File::get(base_path($fileDefinition['stub']));
        $content = str_replace(['{{Object}}', '{{object}}', '{{query}}'], [$this->object, strtolower($this->object), $query], $content);
        File::put(base_path($fileDefinition['php']), $content);
    }
    
    private function generateFile($fileDefinition)
    {
        $content = File::get(base_path($fileDefinition['stub']));
        $content = str_replace(['{{Object}}', '{{object}}'], [$this->object, strtolower($this->object)], $content);
        File::put(base_path($fileDefinition['php']), $content);
    }

    private function generateResourceFile($fileDefinition)
    {
        $columns = DB::getSchemaBuilder()->getColumnListing($this->tableName);

        $attributes = collect($columns)->map(function ($column) {
            return "            '{$column}' => \$this->{$column},";
        })->implode("\n");

        $content = File::get(base_path($fileDefinition['stub']));
        $content = str_replace(['{{ objectName }}', '{{ attributes }}'], [$this->object, $attributes], $content);
        File::put(base_path($fileDefinition['php']), $content);
    }
}
