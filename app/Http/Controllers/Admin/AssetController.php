<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with(['task', 'creator', 'department']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('task_id')) {
            $query->where('task_id', $request->task_id);
        }

        $assets = $query->latest()->paginate(30);

        return view('admin.assets.index', compact('assets'));
    }
}
