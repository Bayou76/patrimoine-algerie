<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Concerns\SyncsItinerary;
use App\Http\Controllers\Controller;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Contrôleur AdminItineraryController — gestion complète des itinéraires
 * depuis l'espace admin (CRUD). Même principe que AdminSiteController :
 * chaque écriture passe par une transaction pour ne jamais laisser un
 * itinéraire sans traduction ni sites.
 */
class AdminItineraryController extends Controller
{
    use SyncsItinerary;

    /** GET /api/admin/itineraries — liste pour le tableau de gestion. */
    public function index(Request $request)
    {
        $this->authorizeAdmin($request);

        return Itinerary::with(['translations', 'sites', 'creator'])
            ->latest()
            ->get()
            ->map(fn (Itinerary $it) => [
                'id' => $it->id,
                'slug' => $it->slug,
                'title' => $it->translations->firstWhere('language_code', 'fr')?->title ?? $it->slug,
                'theme' => $it->theme,
                'sites_count' => $it->sites->count(),
                'is_community' => $it->created_by_user_id !== null,
                'creator_name' => $it->creator?->name,
            ]);
    }

    /** GET /api/admin/itineraries/{itinerary} — données pour le formulaire d'édition. */
    public function edit(Request $request, Itinerary $itinerary)
    {
        $this->authorizeAdmin($request);
        $itinerary->load(['translations', 'sites.translations']);

        return $this->formatForEdit($itinerary);
    }

    /** POST /api/admin/itineraries — création par un admin (itinéraire officiel). */
    public function store(Request $request)
    {
        $this->authorizeAdmin($request);
        $data = $this->validateItineraryPayload($request);

        return DB::transaction(function () use ($data) {
            $itinerary = Itinerary::create($this->itineraryAttributes($data));
            $this->syncItineraryTranslations($itinerary, $data['translations']);
            $this->syncItinerarySites($itinerary, $data['sites']);

            return response()->json(['slug' => $itinerary->slug], 201);
        });
    }

    /** PUT /api/admin/itineraries/{itinerary} — mise à jour (officiel ou communauté). */
    public function update(Request $request, Itinerary $itinerary)
    {
        $this->authorizeAdmin($request);
        $data = $this->validateItineraryPayload($request, $itinerary->id);

        return DB::transaction(function () use ($itinerary, $data) {
            $itinerary->update($this->itineraryAttributes($data));
            $this->syncItineraryTranslations($itinerary, $data['translations']);
            $this->syncItinerarySites($itinerary, $data['sites']);

            return response()->json(['slug' => $itinerary->slug]);
        });
    }

    /** DELETE /api/admin/itineraries/{itinerary} */
    public function destroy(Request $request, Itinerary $itinerary)
    {
        $this->authorizeAdmin($request);
        $itinerary->delete();

        return response()->noContent();
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user()?->is_admin, 403);
    }

    /** Structure groupée par langue, pratique pour le formulaire multi-onglets. */
    private function formatForEdit(Itinerary $itinerary): array
    {
        return [
            'id' => $itinerary->id,
            'slug' => $itinerary->slug,
            'duration' => $itinerary->duration,
            'difficulty' => $itinerary->difficulty,
            'theme' => $itinerary->theme,
            'cover_image' => $itinerary->cover_image,
            'translations' => $itinerary->translations->keyBy('language_code')->map(fn ($t) => [
                'title' => $t->title,
                'summary' => $t->summary,
                'description' => $t->description,
            ]),
            'sites' => $itinerary->sites->map(fn ($site) => [
                'site_id' => $site->id,
                'name' => $site->translation('fr')?->name ?? $site->slug,
                'day_label' => $site->pivot->day_label,
                'note' => $site->pivot->note,
            ]),
        ];
    }
}
