<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ScholarshipController extends Controller
{
    public function index(Request $request)
    {
        $scholarships = Scholarship::query()
            ->when($request->filled('q'), fn ($q) => $q->where('name', 'like', '%'.$request->input('q').'%')->orWhere('provider', 'like', '%'.$request->input('q').'%'))
            ->orderBy('deadline')->paginate(8)->withQueryString();
        $stats = ['total' => Scholarship::count(), 'active' => Scholarship::active()->count(), 'levels' => Scholarship::distinct('level')->count('level'), 'soon' => Scholarship::whereBetween('deadline', [today(), today()->addDays(7)])->count()];
        return view('admin.dashboard', compact('scholarships', 'stats'));
    }

    public function store(Request $request)
    {
        Scholarship::create($this->validated($request));
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Beasiswa berhasil ditambahkan.'], 201);
        }
        return redirect()->route('admin.dashboard')->with('success', 'Beasiswa berhasil ditambahkan.');
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        $scholarship->update($this->validated($request, $scholarship));
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Perubahan beasiswa berhasil disimpan.']);
        }
        return redirect()->route('admin.dashboard')->with('success', 'Perubahan beasiswa berhasil disimpan.');
    }

    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();
        if (request()->expectsJson()) {
            return response()->json(['message' => 'Beasiswa berhasil dihapus.']);
        }
        return redirect()->route('admin.dashboard')->with('success', 'Beasiswa berhasil dihapus.');
    }

    private function validated(Request $request, ?Scholarship $scholarship = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:180'], 'provider' => ['required', 'string', 'max:180'],
            'level' => ['required', 'in:D3,D4,S1,S2,S3'], 'deadline' => ['required', 'date'],
            'summary' => ['required', 'string', 'max:500'], 'description' => ['required', 'string'],
            'requirements_text' => ['nullable', 'string'], 'benefits_text' => ['nullable', 'string'],
            'application_url' => ['nullable', 'url', 'max:2048'], 'is_active' => ['nullable', 'boolean'],
        ]);
        $data['slug'] = $scholarship && $scholarship->name === $data['name']
            ? $scholarship->slug
            : Str::slug($data['name']).'-'.Str::lower(Str::random(5));
        $data['requirements'] = collect(preg_split('/\r\n|\r|\n/', $data['requirements_text'] ?? ''))->filter()->values()->all();
        $data['benefits'] = collect(preg_split('/\r\n|\r|\n/', $data['benefits_text'] ?? ''))->filter()->values()->all();
        unset($data['requirements_text'], $data['benefits_text']);
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
