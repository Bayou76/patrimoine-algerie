<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserTripSite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur MyTripController — « Mon voyage », l'itinéraire personnel
 * et privé d'un utilisateur connecté, construit à partir de ses favoris.
 *
 * Toutes les méthodes sont scoped à $request->user() : impossible de voir
 * ou modifier le voyage d'un autre utilisateur (pas de paramètre d'id
 * exposé, tout part de l'utilisateur authentifié).
 */
class MyTripController extends Controller
{
    /** GET /api/my-trip — les étapes du voyage, dans l'ordre, avec les infos du site. */
    public function index(Request $request)
    {
        $lang = $request->query('lang', 'fr');

        return $request->user()
            ->tripSites()
            ->with('site.translations')
            ->get()
            ->map(fn (UserTripSite $stop) => $this->formatStop($stop, $lang));
    }

    /** POST /api/my-trip — ajoute un site à la fin du voyage. */
    public function addSite(Request $request)
    {
        $validated = $request->validate([
            'site_id' => ['required', 'integer', 'exists:sites,id'],
        ]);
        $user = $request->user();

        // Déjà présent ? On ne duplique pas (contrainte unique en base de toute façon).
        if ($user->tripSites()->where('site_id', $validated['site_id'])->exists()) {
            return response()->json(['message' => 'Déjà dans le voyage.'], 422);
        }

        $nextPosition = $user->tripSites()->max('position') + 1;
        $user->tripSites()->create([
            'site_id' => $validated['site_id'],
            'position' => $nextPosition,
        ]);

        return response()->json(['message' => 'Ajouté.'], 201);
    }

    /** PUT /api/my-trip/reorder — nouvel ordre complet (liste de site_id). */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'site_ids' => ['required', 'array'],
            'site_ids.*' => ['integer'],
        ]);
        $user = $request->user();

        DB::transaction(function () use ($user, $validated) {
            foreach ($validated['site_ids'] as $index => $siteId) {
                $user->tripSites()->where('site_id', $siteId)->update(['position' => $index + 1]);
            }
        });

        return response()->json(['message' => 'Ordre mis à jour.']);
    }

    /** PUT /api/my-trip/{site} — met à jour la note personnelle sur une étape. */
    public function updateNote(Request $request, int $siteId)
    {
        $validated = $request->validate(['note' => ['nullable', 'string', 'max:500']]);

        $stop = $request->user()->tripSites()->where('site_id', $siteId)->firstOrFail();
        $stop->update(['note' => $validated['note'] ?? null]);

        return response()->json(['message' => 'Note enregistrée.']);
    }

    /** DELETE /api/my-trip/{site} — retire une étape du voyage. */
    public function removeSite(Request $request, int $siteId)
    {
        $request->user()->tripSites()->where('site_id', $siteId)->delete();

        return response()->noContent();
    }

    private function formatStop(UserTripSite $stop, string $lang): array
    {
        $site = $stop->site;
        $translation = $site->translation($lang);

        return [
            'id' => $site->id,
            'slug' => $site->slug,
            'name' => $translation?->name ?? $site->slug,
            'category' => $site->category,
            'wilaya' => $site->wilaya,
            'image_path' => $site->image_path,
            'latitude' => $site->latitude,
            'longitude' => $site->longitude,
            'position' => $stop->position,
            'note' => $stop->note,
        ];
    }
}
