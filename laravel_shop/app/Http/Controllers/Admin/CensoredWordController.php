<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CensoredWord;
use Illuminate\Http\Request;

class CensoredWordController extends Controller
{
    public function index()
    {
        $words = CensoredWord::orderBy('word')->paginate(20);
        return view('admin.censored-words.index', compact('words'));
    }

    public function create()
    {
        return view('admin.censored-words.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'word' => 'required|string|unique:cen.sored_words|max:50',
            'severity' => 'required|in:low,medium,high',
        ]);

        CensoredWord::create([
            'word' => strtolower($request->word),
            'severity' => $request->severity,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('admin.censored-words.index')
            ->with('success', 'Palabra añadida correctamente');
    }

    public function edit(CensoredWord $censoredWord)
    {
        return view('admin.censored-words.edit', compact('censoredWord'));
    }

    public function update(Request $request, CensoredWord $censoredWord)
    {
        $request->validate([
            'word' => 'required|string|max:50|unique:cen.sored_words,word,' . $censoredWord->id,
            'severity' => 'required|in:low,medium,high',
        ]);

        $censoredWord->update([
            'word' => strtolower($request->word),
            'severity' => $request->severity,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('admin.censored-words.index')
            ->with('success', 'Palabra actualizada correctamente');
    }

    public function destroy(CensoredWord $censoredWord)
    {
        $censoredWord->delete();
        return redirect()->route('admin.censored-words.index')
            ->with('success', 'Palabra eliminada correctamente');
    }

    public function toggleActive(CensoredWord $censoredWord)
    {
        $censoredWord->active = !$censoredWord->active;
        $censoredWord->save();
        
        return back()->with('success', 'Estado cambiado correctamente');
    }
}
