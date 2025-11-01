<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\OperationLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $query = OperationLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filtros usando input()
        if ($request->input('operation')) {
            $query->where('operation', $request->input('operation'));
        }

        if ($request->input('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', "%" . $request->input('search') . "%")
                    ->orWhereHas('user', function ($q) use ($request) {
                        $q->where('name', 'like', "%" . $request->input('search') . "%");
                    });
            });
        }

        if ($request->input('dateFrom')) {
            $query->whereDate('created_at', '>=', $request->input('dateFrom'));
        }

        if ($request->input('dateTo')) {
            $query->whereDate('created_at', '<=', $request->input('dateTo'));
        }

        return $query->paginate(5000);
    }
}