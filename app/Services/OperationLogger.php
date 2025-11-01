<?php

namespace App\Services;

use App\Models\OperationLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationLogger
{
    public static function log(
        string $operation,
        Model $model,
        ?string $description = null,
        ?array $oldValues = null,
        ?Request $request = null
    ): OperationLog {

        $request = $request ?? request();

        if($description && $operation == 'create' || $operation == 'update'){
            $description = '✅' . $description;
        }elseif($description && $operation == 'delete'){
            $description = '❌' . $description;
        }

        return OperationLog::create([
            'object_type' => get_class($model),
            'object_id' => $model->getKey(),
            'operation' => $operation,
            'description' => $description ?? self::generateDescription($operation, $model),
            'user_id' => Auth::id(),
            'old_values' => $oldValues,
            'new_values' => $model->getAttributes(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }

    protected static function generateDescription(string $operation, Model $model): string
    {
        $modelName = class_basename($model);
        
        return match ($operation) {
            'create' => "Novo {$modelName} criado",
            'update' => "{$modelName} atualizado",
            'delete' => "{$modelName} removido",
            default => "Operação '{$operation}' realizada em {$modelName}",
        };
    }
}