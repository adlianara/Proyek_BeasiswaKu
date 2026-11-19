<?php

namespace App\Http\Controllers;

use App\Models\Scholarship;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function home()
    {
        $scholarships = Scholarship::active()->orderBy('deadline')->take(6)->get();
        return view('home', compact('scholarships'));
    }

    public function search(Request $request)
    {
        $query = Scholarship::active()
            ->when($request->filled('q'), fn ($builder) => $builder->where(function ($q) use ($request) {
                $term = '%'.$request->string('q').'%';
                $q->where('name', 'like', $term)->orWhere('provider', 'like', $term)->orWhere('summary', 'like', $term);
            }))
            ->when($request->filled('level'), fn ($builder) => $builder->whereIn('level', (array) $request->input('level')))
            ->when($request->filled('provider'), fn ($builder) => $builder->where('provider', 'like', '%'.$request->input('provider').'%'))
            ->when($request->input('status') === 'open', fn ($builder) => $builder->whereDate('deadline', '>=', today()))
            ->when($request->input('status') === 'soon', fn ($builder) => $builder->whereBetween('deadline', [today(), today()->addDays(7)]));

        $sort = $request->input('sort', 'deadline');
        $query->orderBy($sort === 'name' ? 'name' : 'deadline', $request->input('direction') === 'desc' ? 'desc' : 'asc');
        $scholarships = $query->paginate(6)->withQueryString();
        return view('search', compact('scholarships'));
    }

    public function show(Scholarship $scholarship)
    {
        abort_unless($scholarship->is_active, 404);
        return view('detail', compact('scholarship'));
    }
}
