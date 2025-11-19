<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DocumentController extends Controller
{
    /**
     * Store a newly created document in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id' => 'required|exists:equipment,id',
            'name' => 'required|string|max:255',
            'document' => 'required|file|max:10240', // Max 10MB
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            // Récupérer l'équipement
            $equipment = Equipment::findOrFail($validated['equipment_id']);
            
            // Gérer le téléchargement du fichier
            $file = $request->file('document');
            $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('documents', $fileName, 'public');

            // Créer le document
            $document = new Document([
                'name' => $validated['name'],
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
                'notes' => $validated['notes'] ?? null,
                'uploaded_by' => Auth::id(),
            ]);

            $equipment->documents()->save($document);

            return redirect()
                ->route('equipment.show', $equipment)
                ->with('success', 'Le document a été téléchargé avec succès.');

        } catch (\Exception $e) {
            \Log::error('Erreur lors du téléchargement du document: ' . $e->getMessage());
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors du téléchargement du document.');
        }
    }

    /**
     * Display the specified document.
     *
     * @param  \App\Models\Document  $document
     * @return \Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function show(Document $document)
    {
        return Storage::disk('public')->download($document->file_path, $document->file_name);
    }

    /**
     * Remove the specified document from storage.
     *
     * @param  \App\Models\Document  $document
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Document $document)
    {
        try {
            // Supprimer le fichier physique
            Storage::disk('public')->delete($document->file_path);
            
            // Supprimer l'entrée en base de données
            $document->delete();

            return back()->with('success', 'Le document a été supprimé avec succès.');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du document: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression du document.');
        }
    }
}
