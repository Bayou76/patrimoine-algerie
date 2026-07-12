<?php

namespace Database\Seeders;

use App\Models\Itinerary;
use App\Models\Site;
use Illuminate\Database\Seeder;

class ItinerariesSeeder extends Seeder
{
    public function run(): void
    {
        $itineraries = [
            [
                'slug' => 'sur-les-traces-des-romains',
                'duration' => '5 jours',
                'difficulty' => 'moyen',
                'theme' => 'romain',
                'cover_image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1d/Timgad_-_Amphith%C3%A9%C3%A2tre.jpg/1280px-Timgad_-_Amphith%C3%A9%C3%A2tre.jpg',
                'sites' => [
                    ['slug' => 'timgad', 'day_label' => 'Jour 1', 'note' => 'La « Pompéi africaine » — arc de Trajan et théâtre.'],
                    ['slug' => 'djemila', 'day_label' => 'Jour 2', 'note' => 'Cuicul, ses forums et son musée de mosaïques.'],
                    ['slug' => 'theveste-tebessa', 'day_label' => 'Jour 3', 'note' => 'L\'arc de Caracalla et le temple de Minerve.'],
                    ['slug' => 'hippone', 'day_label' => 'Jour 4', 'note' => 'La ville d\'Augustin, port et basilique.'],
                    ['slug' => 'tipaza', 'day_label' => 'Jour 5 — matin', 'note' => 'Ruines face à la mer.'],
                    ['slug' => 'cherchell', 'day_label' => 'Jour 5 — après-midi', 'note' => 'Musée archéologique de Iol Caesarea.'],
                ],
                'translations' => [
                    'fr' => [
                        'title' => 'Sur les traces des Romains',
                        'summary' => 'Cinq jours à travers les grandes cités romaines d\'Algérie, de Timgad à Tipaza.',
                        'description' => 'Un voyage à travers les provinces africaines de l\'Empire romain : forums, théâtres, arcs et mosaïques. À faire en voiture, entre l\'est algérien et la côte.',
                    ],
                    'ar' => [
                        'title' => 'على خطى الرومان',
                        'summary' => 'خمسة أيام عبر أكبر المدن الرومانية في الجزائر، من تيمقاد إلى تيبازة.',
                        'description' => 'رحلة عبر الولايات الإفريقية للإمبراطورية الرومانية: منتديات ومسارح وأقواس وفسيفساء. يُنصح بالتنقل بالسيارة بين الشرق الجزائري والساحل.',
                    ],
                    'en' => [
                        'title' => 'In the footsteps of the Romans',
                        'summary' => 'Five days across Algeria\'s great Roman cities, from Timgad to Tipaza.',
                        'description' => 'A journey through the African provinces of the Roman Empire: forums, theaters, arches, and mosaics. Best done by car, from eastern Algeria to the coast.',
                    ],
                ],
            ],
            [
                'slug' => 'grand-sud-saharien',
                'duration' => '1 semaine',
                'difficulty' => 'soutenu',
                'theme' => 'sud',
                'cover_image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e9/Tassili_n%27Ajjer.jpg/1280px-Tassili_n%27Ajjer.jpg',
                'sites' => [
                    ['slug' => 'timimoun', 'day_label' => 'Jour 1-2', 'note' => 'L\'oasis rouge et sa palmeraie.'],
                    ['slug' => 'taghit', 'day_label' => 'Jour 3', 'note' => 'La « perle de la Saoura », gravures rupestres.'],
                    ['slug' => 'el-oued', 'day_label' => 'Jour 4', 'note' => 'La ville aux mille coupoles.'],
                    ['slug' => 'vallee-du-mzab', 'day_label' => 'Jour 5', 'note' => 'Ghardaïa et son architecture ibadite (UNESCO).'],
                    ['slug' => 'hoggar-assekrem', 'day_label' => 'Jour 6-7', 'note' => 'Les paysages volcaniques du Hoggar au coucher du soleil.'],
                    ['slug' => 'tassili-najjer', 'day_label' => 'Extension', 'note' => 'Fresques préhistoriques du Sahara central.'],
                ],
                'translations' => [
                    'fr' => [
                        'title' => 'Grand Sud saharien',
                        'summary' => 'Une semaine dans le désert algérien, des oasis aux paysages volcaniques.',
                        'description' => 'Traversée du Sud algérien à la rencontre des oasis, des ksour et des massifs sahariens. Prévoyez un guide local pour le Hoggar et le Tassili.',
                    ],
                    'ar' => [
                        'title' => 'الجنوب الكبير الصحراوي',
                        'summary' => 'أسبوع في الصحراء الجزائرية، من الواحات إلى المشاهد البركانية.',
                        'description' => 'رحلة عبر الجنوب الجزائري لاكتشاف الواحات والقصور والجبال الصحراوية. يُنصح بمرافقة مرشد محلي عند زيارة الهقار والطاسيلي.',
                    ],
                    'en' => [
                        'title' => 'The great Saharan south',
                        'summary' => 'A week in the Algerian desert, from oases to volcanic landscapes.',
                        'description' => 'A journey through southern Algeria to discover oases, ksour, and Saharan massifs. A local guide is recommended for the Hoggar and Tassili.',
                    ],
                ],
            ],
            [
                'slug' => 'alger-en-un-jour',
                'duration' => '1 jour',
                'difficulty' => 'facile',
                'theme' => 'villes',
                'cover_image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/93/Kasbah_of_Algiers.jpg/1280px-Kasbah_of_Algiers.jpg',
                'sites' => [
                    ['slug' => 'casbah-alger', 'day_label' => 'Matin', 'note' => 'Balade dans les ruelles de la Casbah (UNESCO).'],
                    ['slug' => 'mosquee-ketchaoua', 'day_label' => 'Matin', 'note' => 'Mosquée emblématique restaurée.'],
                    ['slug' => 'grande-poste-alger', 'day_label' => 'Midi', 'note' => 'Chef-d\'œuvre néo-mauresque, à voir de l\'extérieur.'],
                    ['slug' => 'notre-dame-afrique', 'day_label' => 'Après-midi', 'note' => 'Vue panoramique sur la baie.'],
                    ['slug' => 'monument-des-martyrs', 'day_label' => 'Fin de journée', 'note' => 'Coucher de soleil depuis Riadh El Feth.'],
                ],
                'translations' => [
                    'fr' => [
                        'title' => 'Alger en un jour',
                        'summary' => 'Une journée intense dans la capitale, de la Casbah au Monument des Martyrs.',
                        'description' => 'Un condensé d\'Alger pour un premier séjour : quartier historique, chef-d\'œuvre colonial, panoramas et symboles contemporains.',
                    ],
                    'ar' => [
                        'title' => 'الجزائر العاصمة في يوم واحد',
                        'summary' => 'يوم مكثف في العاصمة، من القصبة إلى مقام الشهيد.',
                        'description' => 'خلاصة العاصمة الجزائرية لأول زيارة: الحي التاريخي، الروائع الاستعمارية، الإطلالات البانورامية والرموز المعاصرة.',
                    ],
                    'en' => [
                        'title' => 'Algiers in one day',
                        'summary' => 'An intense day in the capital, from the Casbah to the Martyrs Memorial.',
                        'description' => 'A condensed Algiers tour for a first visit: historic district, colonial masterpiece, panoramas, and contemporary landmarks.',
                    ],
                ],
            ],
            [
                'slug' => 'route-spirituelle-tlemcen',
                'duration' => '2 jours',
                'difficulty' => 'facile',
                'theme' => 'spirituel',
                'cover_image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/49/Mosqu%C3%A9e_de_Sidi_Boumediene.jpg/1280px-Mosqu%C3%A9e_de_Sidi_Boumediene.jpg',
                'sites' => [
                    ['slug' => 'grande-mosquee-tlemcen', 'day_label' => 'Jour 1 — matin', 'note' => 'Mosquée almoravide du XIe siècle.'],
                    ['slug' => 'sidi-boumediene', 'day_label' => 'Jour 1 — après-midi', 'note' => 'Complexe mérinide et mausolée du saint.'],
                    ['slug' => 'mansourah', 'day_label' => 'Jour 2 — matin', 'note' => 'Les ruines de la ville-camp mérinide.'],
                    ['slug' => 'djamaa-el-djazair', 'day_label' => 'Jour 2 — extension', 'note' => 'Grande Mosquée d\'Alger, si vous prolongez.'],
                ],
                'translations' => [
                    'fr' => [
                        'title' => 'Route spirituelle autour de Tlemcen',
                        'summary' => 'Deux jours au cœur du patrimoine religieux de l\'ouest algérien.',
                        'description' => 'Tlemcen fut une capitale spirituelle du Maghreb médiéval. Cet itinéraire suit les traces des dynasties almoravide et mérinide.',
                    ],
                    'ar' => [
                        'title' => 'المسار الروحي حول تلمسان',
                        'summary' => 'يومان في قلب التراث الديني للغرب الجزائري.',
                        'description' => 'كانت تلمسان عاصمة روحية للمغرب في العصور الوسطى. يتتبع هذا المسار خطى الدولتين المرابطية والمرينية.',
                    ],
                    'en' => [
                        'title' => 'Spiritual route around Tlemcen',
                        'summary' => 'Two days at the heart of western Algeria\'s religious heritage.',
                        'description' => 'Tlemcen was a spiritual capital of the medieval Maghreb. This itinerary follows the traces of the Almoravid and Marinid dynasties.',
                    ],
                ],
            ],
            [
                'slug' => 'nature-et-montagnes-du-nord',
                'duration' => '4 jours',
                'difficulty' => 'moyen',
                'theme' => 'naturel',
                'cover_image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Parc_national_du_Djurdjura.jpg/1280px-Parc_national_du_Djurdjura.jpg',
                'sites' => [
                    ['slug' => 'chrea', 'day_label' => 'Jour 1', 'note' => 'Parc national de l\'Atlas blidéen.'],
                    ['slug' => 'djurdjura', 'day_label' => 'Jour 2', 'note' => 'Randonnées en Kabylie.'],
                    ['slug' => 'gorges-du-rhumel-constantine', 'day_label' => 'Jour 3', 'note' => 'Les gorges spectaculaires de Constantine.'],
                    ['slug' => 'parc-national-el-kala', 'day_label' => 'Jour 4', 'note' => 'Lacs et forêts humides classées.'],
                ],
                'translations' => [
                    'fr' => [
                        'title' => 'Nature et montagnes du Nord',
                        'summary' => 'Quatre jours dans les parcs nationaux et paysages du nord algérien.',
                        'description' => 'De l\'Atlas au littoral est, un circuit tourné vers les parcs, les gorges et les forêts humides. Idéal au printemps ou en automne.',
                    ],
                    'ar' => [
                        'title' => 'طبيعة وجبال الشمال',
                        'summary' => 'أربعة أيام في الحدائق الوطنية ومناظر الشمال الجزائري.',
                        'description' => 'من الأطلس إلى الساحل الشرقي، جولة مخصصة للحدائق والوديان والغابات الرطبة. مثالية في الربيع أو الخريف.',
                    ],
                    'en' => [
                        'title' => 'Nature and northern mountains',
                        'summary' => 'Four days across northern Algeria\'s national parks and landscapes.',
                        'description' => 'From the Atlas to the eastern coast, a circuit focused on parks, gorges, and wetlands. Best done in spring or autumn.',
                    ],
                ],
            ],
        ];

        foreach ($itineraries as $data) {
            $itinerary = Itinerary::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'duration' => $data['duration'],
                    'difficulty' => $data['difficulty'],
                    'theme' => $data['theme'],
                    'cover_image' => $data['cover_image'],
                ]
            );

            $itinerary->translations()->delete();
            foreach ($data['translations'] as $lang => $t) {
                $itinerary->translations()->create([
                    'language_code' => $lang,
                    'title' => $t['title'],
                    'summary' => $t['summary'],
                    'description' => $t['description'],
                ]);
            }

            $itinerary->sites()->detach();
            foreach ($data['sites'] as $index => $s) {
                $site = Site::where('slug', $s['slug'])->first();
                if ($site) {
                    $itinerary->sites()->attach($site->id, [
                        'position' => $index + 1,
                        'day_label' => $s['day_label'],
                        'note' => $s['note'],
                    ]);
                }
            }
        }
    }
}
