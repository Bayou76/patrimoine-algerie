<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;

class EnglishContentSeeder extends Seeder
{
    /**
     * Adds English translations to the sites already seeded by SitesSeeder.
     * Safe to re-run: uses updateOrCreate for translations and refreshes en timeline events.
     */
    public function run(): void
    {
        foreach ($this->content() as $slug => $data) {
            $site = Site::where('slug', $slug)->first();
            if (! $site) {
                continue;
            }

            $site->translations()->updateOrCreate(
                ['language_code' => 'en'],
                $data['translation']
            );

            $site->timelineEvents()->where('language_code', 'en')->delete();
            foreach ($data['timeline'] ?? [] as $event) {
                $site->timelineEvents()->create([
                    'language_code' => 'en',
                    'year' => $event['year'],
                    'period_label' => $event['period_label'],
                    'title' => $event['title'],
                    'description' => $event['description'],
                ]);
            }
        }
    }

    private function content(): array
    {
        return [
            'djemila' => [
                'translation' => [
                    'name' => 'Djemila',
                    'description' => 'Roman city of Cuicul, nestled in the mountains — one of the best-preserved sites in North Africa.',
                    'history' => "Djemila, the ancient Cuicul, was founded at the end of the 1st century AD, under the reign of Emperor Nerva, as a colony for legion veterans and to oversee the highlands of Numidia. Perched at 900 metres in a rugged setting of the Petite Kabylie mountains, chosen for its defensive position, the city adopted an urban plan more organic than the strictly geometric layout of its neighbour Timgad.\n\nDuring the 2nd and 3rd centuries, the city reached its peak thanks to trade in wheat and olive oil, precious goods exported to Rome. It was endowed with a forum, several temples — including one dedicated to the imperial Severan dynasty — a theatre seating around 3,000 spectators, and vast houses adorned with mosaics of exceptional quality, today displayed in the site museum and among the best preserved in North Africa.\n\nFrom the 4th century, Cuicul converted to Christianity and gained a substantial ecclesiastical quarter with basilicas and a baptistery. The city gradually declined, weakened by the political instability of the late Roman Empire and then by the 5th-century Vandal invasions, before being largely deserted. Passing under Muslim rule in the 7th century, it fell into oblivion for over a millennium — which paradoxically preserved its remains from any later rebuilding.\n\nRediscovered and systematically excavated from 1909 under the direction of French archaeologist Albert Ballu, Djemila is today regarded as one of the best-preserved Roman urban ensembles in the Mediterranean, earning its inscription on the UNESCO World Heritage list in 1982.",
                    'visit_info' => "Plan around 2 to 3 hours to visit the whole site. Wear closed shoes — the ground is uneven. The adjoining museum holds an exceptional collection of Roman mosaics not to be missed. Spring and autumn are the most pleasant seasons; summer can be very hot at this altitude.",
                ],
                'timeline' => [
                    ['year' => 100, 'period_label' => 'Late 1st century', 'title' => 'Foundation of Cuicul', 'description' => 'Rome founds Cuicul as a military colony tasked with overseeing the highlands of Numidia.'],
                    ['year' => 200, 'period_label' => '2nd - 3rd century', 'title' => 'Golden age', 'description' => "Thanks to the wheat and olive-oil trade, Cuicul gains a forum, temples, and lavish houses decorated with mosaics."],
                    ['year' => 216, 'period_label' => '216', 'title' => 'New forum and Arch of Caracalla', 'description' => "Under the Severan dynasty, the city expands with a second forum and a monumental arch dedicated to Emperor Caracalla."],
                    ['year' => 260, 'period_label' => '3rd century', 'title' => 'Crisis of the 3rd century', 'description' => "Like the rest of the Roman Empire, the city goes through a period of political and economic instability."],
                    ['year' => 350, 'period_label' => '4th century', 'title' => 'Christianisation', 'description' => "The city converts to Christianity; a Christian quarter and a baptistery are built."],
                    ['year' => 500, 'period_label' => '6th century', 'title' => 'Decline and abandonment', 'description' => "Weakened by Vandal invasions, the city is gradually deserted by its inhabitants."],
                    ['year' => 650, 'period_label' => '7th century', 'title' => 'Muslim conquest of the region', 'description' => "The region comes under Muslim rule; the ancient site, largely abandoned already, sinks into oblivion."],
                    ['year' => 1909, 'period_label' => '1909', 'title' => 'Systematic archaeological excavations', 'description' => "French excavations led by Albert Ballu uncover most of the remains visible today."],
                    ['year' => 1982, 'period_label' => '1982', 'title' => 'UNESCO listing', 'description' => "Djemila is inscribed on the UNESCO World Heritage list for the exceptional preservation of its remains."],
                ],
            ],

            'timgad' => [
                'translation' => [
                    'name' => 'Timgad',
                    'description' => 'Roman city founded by Trajan, famous for its perfectly preserved grid plan.',
                    'history' => "Founded in 100 AD by Emperor Trajan under the name Thamugadi, Timgad was designed ex nihilo, on virgin ground, according to a strict grid plan inherited from the tradition of Roman military camps. The city was settled with veterans of the Third Legion Augusta, rewarded with land after their years of service, and organised around two main axes — the cardo and decumanus — meeting at the forum.\n\nOver the 2nd and 3rd centuries, buoyed by the agricultural prosperity of the surrounding highlands, Timgad expanded well beyond its original plan: new districts, additional baths and monumental arches — including the famous 12-metre-high 'Arch of Trajan' — sprang up outside the walls. The city then had a public library, a theatre seating 3,500, and fourteen public bath complexes, a remarkable figure reflecting the standard of living of its inhabitants.\n\nIn the 4th century, Timgad also became a major stronghold of Donatism, a dissident Christian movement particularly strong in North Africa and at odds with the official Church of Rome. The city declined after the 5th-century Vandal invasions; a Byzantine fort was built in 539 to protect the remaining inhabitants, in vain — the city was gradually abandoned and then buried under the sand in the 7th–8th centuries, which preserved it intact for over a thousand years.\n\nRediscovered by European explorers in the 19th century and systematically excavated from 1881, Timgad is today considered one of the most complete and best-preserved examples of Roman provincial urbanism, listed on the UNESCO World Heritage list since 1982.",
                    'visit_info' => "Plan for 2 hours to walk the site, which stretches over a vast flat area with little shade — bring water and sun protection. The Arch of Trajan at the entrance is one of the most photographed monuments in Algeria.",
                ],
                'timeline' => [
                    ['year' => 100, 'period_label' => '100 AD', 'title' => 'Foundation of Thamugadi', 'description' => 'Emperor Trajan founds the city for veterans of the Roman legion, along a grid plan.'],
                    ['year' => 150, 'period_label' => 'Mid-2nd century', 'title' => 'Expansion beyond the walls', 'description' => "The city's prosperity drives expansion beyond the original plan, with new districts, extra baths and a triumphal arch."],
                    ['year' => 200, 'period_label' => '2nd - 3rd century', 'title' => 'Peak of the city', 'description' => "Timgad prospers, with a forum, a public library and fourteen public bath complexes."],
                    ['year' => 380, 'period_label' => '4th century', 'title' => 'Stronghold of Donatism', 'description' => 'The city becomes a stronghold of Donatism, a dissident Christian movement especially strong in North Africa.'],
                    ['year' => 430, 'period_label' => '5th century', 'title' => 'Vandal invasions', 'description' => 'The Vandal invasions weaken the city, which begins its decline.'],
                    ['year' => 539, 'period_label' => '539', 'title' => 'Byzantine fort', 'description' => "After the Byzantine reconquest of North Africa, a fort is built to shield the remaining inhabitants from Berber raids."],
                    ['year' => 700, 'period_label' => '7th - 8th century', 'title' => 'Abandonment and burial', 'description' => 'Gradually covered by sand, Timgad is abandoned — which will preserve its remains exceptionally well.'],
                    ['year' => 1881, 'period_label' => '1881', 'title' => 'Archaeological rediscovery', 'description' => 'French excavations reveal a remarkably well-preserved site beneath the sand.'],
                    ['year' => 1982, 'period_label' => '1982', 'title' => 'UNESCO listing', 'description' => 'Timgad is inscribed on the UNESCO World Heritage list.'],
                ],
            ],

            'hoggar-assekrem' => [
                'translation' => [
                    'name' => 'Hoggar — Assekrem',
                    'description' => 'Saharan volcanic massif peaking above 2,900 m, famous for its landscapes and sunrises.',
                    'history' => "The Hoggar, or Ahaggar, is a mountain range of volcanic origin that rises in the heart of the Algerian Sahara. Born of intense volcanic activity tens of millions of years ago, it was then shaped by millennia of erosion that carved its rocks into sharp peaks and basalt chimneys of spectacular forms — the tallest, the Tahat, reaching over 2,900 metres, the highest point in Algeria.\n\nRock engravings testify to human presence in the massif from prehistoric times, well before the arrival, around the 11th century, of the Tuareg Kel Ahaggar, who developed a nomadic pastoral way of life perfectly adapted to the desert's constraints, based on herding camels and goats and mastering the massif's rare water points.\n\nThe Assekrem plateau owes its modern fame to Charles de Foucauld, explorer turned monk hermit, who in 1911 built an isolated chapel at over 2,700 metres altitude, still standing today. Around the turn of the 20th century, several French military missions — including that of Foureau-Lamy — explored and mapped for the first time this region, until then poorly known to Europeans.\n\nIn 1987, a cultural park was created to protect both the massif's exceptional natural heritage and its Tuareg cultural heritage. The Assekrem remains today one of the most popular trekking destinations in the Algerian Sahara, renowned for its sunrises and sunsets over an ocean of volcanic rock.",
                    'visit_info' => "The visit requires a local guide and a 4x4 vehicle — the tracks are impassable in a regular car. Plan an early start to reach the Assekrem before dawn. Nights are cool even in summer: bring warm clothing.",
                ],
                'timeline' => [
                    ['year' => -20000000, 'period_label' => 'Geological formation', 'title' => 'Birth of the volcanic massif', 'description' => "Intense volcanic activity shapes the Hoggar over millions of years, giving it its current landscape of peaks and basalt chimneys."],
                    ['year' => -3000, 'period_label' => 'Prehistory', 'title' => 'Earliest signs of settlement', 'description' => "Rock engravings attest to ancient human presence in the massif, well before the arrival of the Tuareg."],
                    ['year' => 1000, 'period_label' => 'Around the 11th century', 'title' => 'Arrival of the Tuareg Kel Ahaggar', 'description' => "The Tuareg Kel Ahaggar settle in the massif, developing a pastoral way of life adapted to the desert."],
                    ['year' => 1900, 'period_label' => 'Around 1900', 'title' => 'French exploration missions', 'description' => "French military missions, such as Foureau-Lamy, explore and map the massif."],
                    ['year' => 1911, 'period_label' => '1911', 'title' => "Charles de Foucauld's hermitage", 'description' => "Charles de Foucauld builds a hermitage on the Assekrem plateau, still visible today."],
                    ['year' => 1987, 'period_label' => '1987', 'title' => 'Ahaggar Cultural Park', 'description' => 'A cultural park is created to protect the natural and cultural heritage of the massif.'],
                ],
            ],

            'mosquee-ketchaoua' => [
                'translation' => [
                    'name' => 'Ketchaoua Mosque',
                    'description' => 'Ottoman mosque of the 18th century overlooking the Casbah, with a turbulent history — mosque, cathedral, and mosque again.',
                    'history' => "The Ketchaoua Mosque occupies a strategic location at the heart of the Casbah of Algiers, on a terrace overlooking the bay. A first mosque was built there in 1612, before being largely rebuilt and enlarged in 1794 on the orders of Dey Hassan Pacha, who gave it most of its present appearance: a richly decorated hypostyle prayer hall preceded by a courtyard and flanked by two octagonal minarets.\n\nAfter the French conquest of 1830, the building was requisitioned by the new colonial authorities and then, in 1845, fully transformed into a Catholic cathedral under the name Saint-Philippe. The architects of the time kept part of the original Moorish structure and decoration while adding vaults and neo-Byzantine elements, creating a hybrid edifice quite unique in the Mediterranean religious landscape.\n\nOn Algerian independence in 1962, the building recovered its original function as a mosque. Included in 1992 within the perimeter of the Casbah of Algiers listed as UNESCO World Heritage, the Ketchaoua Mosque remains one of the most visited and most symbolically charged monuments of the capital, summing up on its own the successive layers of Algiers' history: Ottoman, colonial, and then independent.",
                    'visit_info' => "Access to the interior may be limited outside prayer times; check on site or with a guide. The mosque is best visited as part of a walk through the Casbah, of which it is one of the highlights.",
                ],
                'timeline' => [
                    ['year' => 1612, 'period_label' => '1612', 'title' => 'Initial construction', 'description' => "A first mosque is built on this spot, at the heart of the Casbah of Algiers."],
                    ['year' => 1794, 'period_label' => '1794', 'title' => 'Ottoman reconstruction', 'description' => 'Dey Hassan Pacha has the mosque rebuilt and enlarged in its current form.'],
                    ['year' => 1830, 'period_label' => '1830', 'title' => 'French conquest', 'description' => "After the capture of Algiers, the building is requisitioned by the colonial authorities."],
                    ['year' => 1845, 'period_label' => '1845', 'title' => 'Turned into Saint-Philippe Cathedral', 'description' => 'The mosque is converted into a Catholic cathedral in a hybrid style blending Moorish arches and neo-Byzantine vaults.'],
                    ['year' => 1962, 'period_label' => '1962', 'title' => 'Restored as a mosque', 'description' => "On Algerian independence, the building recovers its function as a mosque."],
                    ['year' => 1992, 'period_label' => '1992', 'title' => 'Included in the UNESCO perimeter', 'description' => "The mosque is included within the perimeter of the Casbah of Algiers, listed as UNESCO World Heritage."],
                ],
            ],

            'casbah-alger' => [
                'translation' => [
                    'name' => 'Casbah of Algiers',
                    'description' => "Historic medina of Algiers — a maze of alleys and Ottoman houses overlooking the bay, listed as World Heritage.",
                    'history' => "The Casbah of Algiers refers both to the summit citadel and to the whole historic district that extends amphitheatre-like from the port up to the heights of the city, offering a stunning view of the bay. The site has been occupied since antiquity under the name Icosium — a modest Phoenician trading post that became a Roman city — but it was under the Ottoman Regency of Algiers, from 1516, that the district truly took its current form: a tight maze of narrow alleys, staircases and houses with inner courtyards, designed as much for domestic privacy as for defence.\n\nAlgiers then became one of the main corsair powers of the Mediterranean, a status that peaked at the turn of the 17th and 18th centuries and drew several foreign invasion attempts on the city: as early as 1541, the fleet of Emperor Charles V failed to seize the city, driven off notably by a providential storm; in 1816, an Anglo-Dutch fleet bombarded the port in an attempt to end the corsair activity.\n\nThe French conquest of 1830 marked a deep break: part of the Ottoman urban fabric was destroyed to make way for a new European-style city, while the Casbah itself, preserved in its broad outlines, became the symbol of a resilient Algiers identity. This dimension of resistance culminated during the war of independence, when the district served as the setting for the Battle of Algiers in 1956-1957.\n\nListed as UNESCO World Heritage in 1992 for its exceptional architecture and urbanism, the Casbah remains today a lively, inhabited district — but part of its old buildings suffer from a lack of maintenance, prompting major preservation efforts.",
                    'visit_info' => "The Casbah is best visited on foot, ideally with a local guide to understand the history of the district and access some of the traditional houses. Wear comfortable shoes — the alleys are steep and cobbled. The district is inhabited: respect the tranquillity of residents.",
                ],
                'timeline' => [
                    ['year' => -100, 'period_label' => 'Antiquity', 'title' => 'Phoenician then Roman Icosium', 'description' => "The site has been occupied since antiquity under the name Icosium — a Phoenician trading post, then a Roman city."],
                    ['year' => 1516, 'period_label' => '1516', 'title' => 'Start of the Ottoman Regency', 'description' => 'Algiers becomes the seat of the Ottoman Regency; the Casbah develops as a citadel and heart of the fortified city.'],
                    ['year' => 1541, 'period_label' => '1541', 'title' => 'Charles V siege fails', 'description' => "The fleet of Emperor Charles V fails to seize the city, held back by a storm — reinforcing the regency's reputation."],
                    ['year' => 1660, 'period_label' => '17th - 18th century', 'title' => 'Golden age of the corsairs', 'description' => 'Algiers becomes a major corsair capital of the Mediterranean, thriving on trade and privateering.'],
                    ['year' => 1816, 'period_label' => '1816', 'title' => 'Anglo-Dutch bombardment', 'description' => 'A British and Dutch fleet bombards the city to put an end to corsair activity.'],
                    ['year' => 1830, 'period_label' => '1830', 'title' => 'French conquest', 'description' => "The capture of Algiers by France marks a turning point, with major urban changes around the Casbah."],
                    ['year' => 1957, 'period_label' => '1956-1957', 'title' => 'Battle of Algiers', 'description' => "The Casbah is the setting of the Battle of Algiers, a major episode of the war of independence."],
                    ['year' => 1992, 'period_label' => '1992', 'title' => 'UNESCO listing', 'description' => "The Casbah is inscribed on the UNESCO World Heritage list for its exceptional architecture and urbanism."],
                ],
            ],

            'tassili-najjer' => [
                'translation' => [
                    'name' => "Tassili n'Ajjer",
                    'description' => "Vast Saharan plateau listed as World Heritage, home to one of the largest concentrations of prehistoric rock art in the world.",
                    'history' => "The Tassili n'Ajjer is a vast sandstone plateau in the central Sahara, carved over millions of years by erosion into a spectacular landscape of deep canyons, natural arches and veritable 'stone forests'. But its worldwide fame owes above all to the some 15,000 rock engravings and paintings covering its walls — one of the largest concentrations of prehistoric art ever recorded, spread across nearly ten millennia.\n\nSpecialists distinguish several successive stylistic periods. The oldest, known as 'bubaline', saw the emergence, from the 10th millennium BC, of depictions of wildlife now vanished from the region — giant buffalos, elephants, rhinoceroses. From the 8th millennium came the so-called 'Round Heads' period, marked by stylised human figures with ritual or shamanic overtones. Then from the 4th to the 2nd millennium came the Bovidian period, the richest and most famous, whose scenes of herds and daily life — including the famous 'Running Horned Woman' — attest to a Sahara still green. The Equine and then Cameline periods that followed accompanied the gradual and irreversible drying of the Sahara.\n\nLong known only to Tuareg populations, the site was truly revealed to the world by French officer Charles Brenans in 1933, and above all by the famous mission of ethnologist Henri Lhote in 1956-1957, whose tracings and copies of paintings — displayed and published internationally — sparked huge interest in this 'desert Louvre'. Inscribed on the UNESCO World Heritage list in 1982 on both natural and cultural grounds, the Tassili n'Ajjer remains today a strictly regulated site, both to preserve its fragility and for visitor safety in this remote Saharan region.",
                    'visit_info' => "The visit takes place exclusively on multi-day treks led by an accredited guide, with prior authorisation. Bring full desert hiking gear (water, sun protection, warm clothes for the night). Circuits generally start from Djanet.",
                ],
                'timeline' => [
                    ['year' => -10000, 'period_label' => '10th - 8th millennium BC', 'title' => "'Bubaline' period", 'description' => "The oldest engravings depict wildlife now vanished from the region: giant buffalos, elephants, rhinoceroses."],
                    ['year' => -8000, 'period_label' => '8th - 6th millennium BC', 'title' => "'Round Heads' period", 'description' => "Stylised human figures with rounded heads appear, often read as ritual or shamanic scenes."],
                    ['year' => -4000, 'period_label' => '4th - 2nd millennium BC', 'title' => 'Bovidian period', 'description' => "Depictions of cattle herds — including the famous 'Running Horned Woman' — testify to the rise of herding while the Sahara was still green."],
                    ['year' => -1500, 'period_label' => 'From the 15th century BC', 'title' => 'Equine period', 'description' => 'Horses and chariots appear in the depictions, brought in through contact with the Mediterranean world.'],
                    ['year' => -500, 'period_label' => '1st millennium BC', 'title' => 'Cameline period and desertification', 'description' => "The arrival of the dromedary in the iconography goes hand in hand with the final transformation of the Sahara into a desert."],
                    ['year' => 1933, 'period_label' => '1933', 'title' => 'Modern discovery', 'description' => "French officer Charles Brenans first documents the scale of the plateau's engravings and paintings."],
                    ['year' => 1956, 'period_label' => '1956-1957', 'title' => 'Henri Lhote mission', 'description' => "French ethnologist Henri Lhote leads an expedition that makes the plateau's rock paintings known worldwide."],
                    ['year' => 1982, 'period_label' => '1982', 'title' => 'UNESCO listing', 'description' => "The Tassili n'Ajjer is inscribed on the UNESCO World Heritage list on both natural and cultural grounds."],
                ],
            ],

            'hippone' => [
                'translation' => [
                    'name' => 'Hippo Regius',
                    'description' => 'Ancient Berber-Punic then Roman city, linked to Saint Augustine, who was its bishop.',
                    'history' => "Hippo, the ancient Hippo Regius, owes its name to its past as a royal residence: under the Numidian kings — including the famous Massinissa — the city served as a secondary capital, earning it the Latin epithet 'Regius'. Founded several centuries earlier by Phoenician merchants who set up a trading post along this coast favourable to maritime trade, it came under Roman rule in 46 BC, after the fall of the Numidian kingdom.\n\nAs a prosperous Roman port, Hippo owes its most enduring historical fame to Saint Augustine — one of the fathers of the Church and one of the greatest thinkers of late antiquity — who was its bishop from 396 until his death in 430. It was in Hippo that Augustine wrote a large part of his work, including his famous Confessions, and led an intense intellectual and theological life that shaped nascent Western Christianity as a whole. His death came as the city was besieged by the Vandals, who took it shortly afterwards.\n\nThe ancient site then fell into long oblivion, until French archaeological excavations, begun in the late 19th century and continued into the 1930s, uncovered the forum, the baths and the city's important Christian quarter. In tribute to Saint Augustine, a modern basilica was built at the very end of the 19th century on the hill overlooking the ruins, where it still dominates the archaeological site today.",
                    'visit_info' => "The archaeological site includes a forum, baths, and a modern basilica dedicated to Saint Augustine, built in the 19th century overlooking the ruins. Allow 1.5 to 2 hours for the visit.",
                ],
                'timeline' => [
                    ['year' => -1000, 'period_label' => '1st millennium BC', 'title' => 'Phoenician foundation', 'description' => 'Phoenician merchants found a trading post on this coastal site.'],
                    ['year' => -203, 'period_label' => '3rd - 2nd century BC', 'title' => 'Residence of the Numidian kings', 'description' => "Under the Numidian kings — including Massinissa — Hippo serves as a royal residence, earning its Latin name Hippo Regius."],
                    ['year' => -46, 'period_label' => '46 BC', 'title' => 'Roman annexation', 'description' => "The city joins the Roman province of Africa after the fall of the Numidian kingdom."],
                    ['year' => 396, 'period_label' => '396', 'title' => 'Augustine becomes bishop of Hippo', 'description' => "Saint Augustine, one of the great figures of Christian thought, leads the diocese until his death in 430."],
                    ['year' => 430, 'period_label' => '430', 'title' => 'Vandal siege', 'description' => "The city is besieged by the Vandals as Augustine is dying; it falls shortly after his death."],
                    ['year' => 1900, 'period_label' => 'Late 19th century', 'title' => 'Construction of Saint Augustine Basilica', 'description' => 'A modern basilica is built overlooking the site, in tribute to Saint Augustine.'],
                    ['year' => 1930, 'period_label' => '20th century', 'title' => 'Modern archaeological excavations', 'description' => "French excavations uncover the forum, the baths and the Christian quarter of the site."],
                ],
            ],

            'kalaa-beni-hammad' => [
                'translation' => [
                    'name' => "Al Qal'a of Beni Hammad",
                    'description' => 'First capital of the Hammadids, a fortress and royal city of the 11th century nestled in the Hodna mountains.',
                    'history' => "The Al Qal'a of Beni Hammad was founded in 1007 by Hammad ibn Buluggin, a prince of the Zirid dynasty who broke with his overlords in Kairouan in 1015 to assert his independence and found his own line, the Hammadids. Built in a defensive position in the Hodna mountains, the city became the first capital of this Berber dynasty which, at its peak, ruled over much of the central Maghreb.\n\nAround the middle of the 11th century, the Qal'a reached its golden age: it acquired sumptuous palaces, gardens and above all a Great Mosque which, with its thirteen naves and a minaret over twenty metres high, is regarded as the largest mosque built in North Africa before the 20th century. The city also became a renowned intellectual and artisanal centre, attracting scholars and artists from across the Maghreb.\n\nThis prosperity was however short-lived: from 1090, the growing threat of the Banu Hilal invasions pushed the Hammadids to abandon their capital in favour of the better-protected coastal city of Béjaïa. The final blow came in 1152, when the Almohads partially destroyed what remained of the city. Abandoned ever since, the Qal'a escaped any later rebuilding — remarkably preserving its remains until their scientific rediscovery, with the first French archaeological excavations from 1908. The site was inscribed on the UNESCO World Heritage list in 1980.",
                    'visit_info' => "The site, quite remote, is visited on foot across several hectares of remains. The minaret of the Great Mosque, some twenty metres high, is the best-preserved element. Bring good walking shoes.",
                ],
                'timeline' => [
                    ['year' => 1007, 'period_label' => '1007', 'title' => "Foundation of the Qal'a", 'description' => 'Hammad ibn Buluggin founds the city as the capital of the Hammadid dynasty.'],
                    ['year' => 1015, 'period_label' => '1015', 'title' => 'Independence from the Zirids', 'description' => 'Hammad ibn Buluggin breaks with the Zirid dynasty of Kairouan and asserts his political autonomy.'],
                    ['year' => 1050, 'period_label' => 'Mid-11th century', 'title' => 'Architectural and scholarly peak', 'description' => 'The city thrives as a political and intellectual centre, with palaces and the Great Mosque.'],
                    ['year' => 1090, 'period_label' => '1090', 'title' => 'Abandonment before the Banu Hilal', 'description' => 'Threatened by the Banu Hilal invasions, the capital is abandoned in favour of Béjaïa.'],
                    ['year' => 1152, 'period_label' => '1152', 'title' => 'Almohad destruction', 'description' => 'The Almohads partially destroy the city, hastening its final decline.'],
                    ['year' => 1908, 'period_label' => '1908', 'title' => 'First archaeological excavations', 'description' => 'French excavations begin the systematic documentation of the ruins.'],
                    ['year' => 1980, 'period_label' => '1980', 'title' => 'UNESCO listing', 'description' => "The site is inscribed on the UNESCO World Heritage list."],
                ],
            ],

            'vallee-du-mzab' => [
                'translation' => [
                    'name' => "M'Zab Valley",
                    'description' => "Valley of five fortified ksour — a masterpiece of Ibadi architecture perfectly adapted to the desert.",
                    'history' => "The M'Zab valley has been settled from the 11th century by the Ibadis, a minority and rigorist branch of Islam distinct from majority Sunnism and Shi'ism, who took refuge in this arid region after being driven from other parts of the Maghreb by religious persecution. On this hostile land they gradually founded five fortified cities — El Atteuf in 1012, the oldest, then Bounoura, Ghardaïa in 1053, Melika, and finally Beni Isguen in 1347, known for its particularly strict religious conservatism.\n\nEach of these ksour is organised along a remarkable urban principle directly inherited from Ibadi religious precepts: houses are arranged in concentric circles around the central mosque on top of a hill, with the most modest dwellings on the outskirts and wealthier houses closer to the religious centre. This plan also allows an extraordinarily efficient management of water — a scarce resource — through a sophisticated system of wells and irrigation channels feeding the surrounding palm groves.\n\nThe region kept broad political and religious autonomy until French colonisation, which from 1882 imposed an administrative and military presence in the valley. To this day, Mozabite society retains its own institutions regulating the religious and social life of the cities. The M'Zab valley, inscribed on the UNESCO World Heritage list in 1982, is unanimously praised by modern architects and urbanists — including Le Corbusier — as a model of vernacular urbanism perfectly adapted to its desert environment.",
                    'visit_info' => "Visiting Beni Isguen, the most conservative city, is only allowed with a licensed guide. The evening markets and surrounding palm groves are also well worth the detour.",
                ],
                'timeline' => [
                    ['year' => 1012, 'period_label' => '1012', 'title' => 'Foundation of El Atteuf', 'description' => "El Atteuf, the oldest of the five ksour, is founded by the first Ibadi refugees."],
                    ['year' => 1053, 'period_label' => '1053', 'title' => 'Foundation of Ghardaïa', 'description' => 'Ghardaïa, the largest of the five cities, is founded.'],
                    ['year' => 1347, 'period_label' => '1347', 'title' => 'Foundation of Beni Isguen', 'description' => 'Beni Isguen, known for its religious conservatism, is the last of the five cities to be founded.'],
                    ['year' => 1882, 'period_label' => '1882', 'title' => 'French colonisation', 'description' => 'The region comes under French administration, which imposes a military presence in the valley.'],
                    ['year' => 1982, 'period_label' => '1982', 'title' => 'UNESCO listing', 'description' => "The M'Zab valley is inscribed on the UNESCO World Heritage list for its exceptional urbanism."],
                ],
            ],

            'grande-mosquee-tlemcen' => [
                'translation' => [
                    'name' => 'Great Mosque of Tlemcen',
                    'description' => "Almoravid mosque from 1082 — one of the oldest and most richly decorated in Algeria.",
                    'history' => "The Great Mosque of Tlemcen was founded in 1082 by the Almoravids, a Berber dynasty of Saharan origin who had just taken the city and sought to assert their religious legitimacy through a building worthy of their imperial ambition. The prayer hall, laid out in eleven naves perpendicular to the back wall, adopts a hypostyle plan inherited from the great Maghrebi and Andalusi mosque tradition.\n\nIn 1136, the Almohad dynasty, who had by then supplanted the Almoravids, enlarged the building and considerably enriched its decoration: it is from this period that dates the famous muqarnas dome that crowns the mihrab, a masterpiece of carved stucco regarded as one of the summits of Almohad architectural art, alongside the Koutoubia of Marrakech and the Giralda of Seville. The mosque also received, around 1145, a finely carved wooden minbar today counted among the oldest preserved in the world.\n\nUnder the Zayyanid dynasty, who made Tlemcen their capital from the 13th century, the building continued to be embellished: the present square minaret was added in 1236. The mosque then crossed the centuries without major transformation, remaining an active place of worship to this day. In recognition of its exceptional monumental heritage, Tlemcen and its historic monuments, including the Great Mosque, have been on UNESCO's tentative World Heritage list since 2002.",
                    'visit_info' => "Access to the prayer hall may be restricted for non-Muslims outside certain hours; check on site. The minaret, added in 1236, is visible from the outside.",
                ],
                'timeline' => [
                    ['year' => 1082, 'period_label' => '1082', 'title' => 'Almoravid foundation', 'description' => 'The Almoravids found the mosque shortly after taking Tlemcen.'],
                    ['year' => 1136, 'period_label' => '1136', 'title' => 'Almohad enlargement', 'description' => 'The Almohads enlarge the building and enrich its decoration.'],
                    ['year' => 1145, 'period_label' => '1145', 'title' => 'The Almoravid minbar', 'description' => "A carved wooden minbar, today one of the oldest preserved in the world, is offered to the mosque."],
                    ['year' => 1236, 'period_label' => '1236', 'title' => 'Zayyanid minaret added', 'description' => 'The Zayyanid dynasty has the present minaret built.'],
                    ['year' => 2002, 'period_label' => '2002', 'title' => "UNESCO tentative list", 'description' => "Tlemcen and its monuments, including the Great Mosque, are put forward for UNESCO World Heritage inscription."],
                ],
            ],

            'chrea' => [
                'translation' => [
                    'name' => 'Chréa',
                    'description' => "Mountain range of the Atlas near Blida — Atlas cedar forest and the ski resort closest to Algiers.",
                    'history' => "Chréa is a mountain range belonging to the Blidean Atlas, less than 50 km south of Algiers, whose highest point exceeds 1,600 metres. Its close proximity to the capital made it, from colonial times, a favourite destination — to escape the coast's summer heat and, in winter, to enjoy one of the few massifs in northern Algeria with enough snow for skiing.\n\nThe massif is above all famous for hosting one of the country's last great Atlas cedar forests — a majestic tree today threatened by climate change and dwindling rainfall. This forest wealth, combined with a remarkable fauna including the Barbary macaque — an endemic North African species now vulnerable — led to the massif being classified as a national park in 1925, one of the oldest in the country, and then extended and reorganised in 1983 to strengthen the protection of its ecosystems.\n\nA ski resort developed on the slopes of the massif over the 20th century, becoming the most accessible from the capital and a very popular family outing spot, despite increasingly irregular snowfall in recent decades. Outside the winter season, Chréa remains a popular hiking destination, offering from its ridges spectacular views over the agricultural plain of the Mitidja and, on clear days, as far as the Bay of Algiers.",
                    'visit_info' => "Chréa hosts the ski resort closest to the capital, open in winter depending on snow. Outside the ski season, the park is well suited to hiking, with fine views over the Mitidja plain.",
                ],
                'timeline' => [
                    ['year' => 1925, 'period_label' => '1925', 'title' => 'Creation of the national park', 'description' => 'Chréa is classified as a national park to protect its cedar forest and its wildlife.'],
                    ['year' => 1945, 'period_label' => 'Mid-20th century', 'title' => 'Development of the ski resort', 'description' => 'A ski resort develops on the slopes of the massif, becoming the most accessible from Algiers.'],
                    ['year' => 1983, 'period_label' => '1983', 'title' => 'Extension and reclassification', 'description' => "The park is reorganised and extended to strengthen the protection of its ecosystems."],
                ],
            ],

            'monument-des-martyrs' => [
                'translation' => [
                    'name' => "Martyrs' Memorial",
                    'description' => "Monumental memorial overlooking Algiers, dedicated to the martyrs of the war of independence — recognisable by its three concrete palm leaves and its eternal flame.",
                    'history' => "The Martyrs' Memorial, or Maqam Echahid ('sanctuary of the martyr'), was inaugurated on 5 July 1982, the twentieth anniversary of Algerian independence. Erected on the heights of El Madania, it dominates the bay of Algiers and remains visible from a large part of the city, standing out as the most recognisable architectural symbol of the capital alongside the Casbah.\n\n96 metres tall, the monument takes the form of three stylised concrete palm leaves that meet at the summit, sheltering at their centre an eternal flame in tribute to the combatants of the war of independence (1954-1962) and, more broadly, to all the struggles for the country's liberation. Its design combines the work of Algerian architect Bachir Yellès and Polish sculptor Marian Konieczny, who created the three six-metre-tall bronze statues placed at the base of each leaf.\n\nEach of these statues, guarded by a soldier, embodies a distinct stage of the national struggle: popular resistance from 1830 to 1954 against French colonisation; the National Liberation Army, spearhead of the war of independence; and the People's National Army, heir to the struggle after 1962. The site also houses the National Museum of the Moudjahid, dedicated to the history of the national movement and the war of liberation.\n\nToday, the Martyrs' Memorial remains a central place of memory for Algerians, hosting the main official ceremonies linked to the history of independence, notably every 5 July and 1 November.",
                    'visit_info' => "The outside area (esplanade, panoramic view) is freely accessible at any time. The adjoining National Museum of the Moudjahid can be visited in about an hour. The panorama over Algiers and its bay is particularly spectacular at sunset.",
                ],
                'timeline' => [
                    ['year' => 1954, 'period_label' => '1954', 'title' => 'Start of the war of independence', 'description' => "The armed struggle launched on 1 November 1954 marks the start of the war of independence, to which the monument pays tribute."],
                    ['year' => 1962, 'period_label' => '1962', 'title' => 'Algerian independence', 'description' => "Algeria gains independence on 5 July 1962, after over 130 years of French rule."],
                    ['year' => 1978, 'period_label' => '1978', 'title' => 'Start of construction', 'description' => "Work on the monument begins on the heights of El Madania."],
                    ['year' => 1982, 'period_label' => '5 July 1982', 'title' => 'Inauguration', 'description' => "The Martyrs' Memorial is inaugurated for the twentieth anniversary of independence."],
                ],
            ],

            'djamaa-el-djazair' => [
                'translation' => [
                    'name' => 'Djamaa el Djazaïr (Great Mosque of Algiers)',
                    'description' => "Africa's largest mosque and the third largest in the world by capacity, with the world's tallest minaret overlooking the bay of Algiers.",
                    'history' => "Djamaa el Djazaïr, the Great Mosque of Algiers, was designed as a state project meant to endow the Algerian capital with a religious building worthy of its history, championed by the Algerian presidency from the 2000s onwards. The international architectural competition was won by the German firm KSP Jürgen Engel Architekten, in partnership with the engineering office Krebs und Kiefer, while construction was entrusted to the Chinese company China State Construction Engineering Corporation.\n\nWork began on 16 August 2012 on a near-28-hectare site by the sea, in the district of Mohammadia. After seven years of construction, the mosque was inaugurated in April 2019. Its minaret reaches 265 metres, making it the tallest minaret in the world; a panoramic lift takes visitors to an observation platform offering a circular view of Algiers and its bay.\n\nThe prayer hall, crowned by a 50-metre-diameter dome reaching 70 metres in height, extends over 22,000 square metres and can accommodate 37,000 worshippers, while the entire complex — esplanades included — can host up to 120,000. This makes Djamaa el Djazaïr the third-largest mosque in the world by capacity, after the Great Mosque of Mecca and the Prophet's Mosque in Medina.\n\nBeyond the place of worship, the complex houses a library, a Quranic studies centre, a museum of Islamic art and history, and a school — making Djamaa el Djazaïr at once a religious monument, a national architectural symbol, and a cultural centre.",
                    'visit_info' => "Access to the prayer hall is possible outside worship hours for visitors respecting the required dress. Climbing the minaret by panoramic lift is usually by booking or separate ticket. Plan half a day to explore the whole complex (mosque, museum, library).",
                ],
                'timeline' => [
                    ['year' => 2005, 'period_label' => '2000s', 'title' => 'Project launch', 'description' => "The Algerian presidency initiates the project for a great national mosque for Algiers."],
                    ['year' => 2012, 'period_label' => '16 August 2012', 'title' => 'Start of construction', 'description' => "Construction begins in Mohammadia, entrusted to the Chinese company CSCEC."],
                    ['year' => 2019, 'period_label' => 'April 2019', 'title' => 'Inauguration', 'description' => "Djamaa el Djazaïr is inaugurated after seven years of construction."],
                    ['year' => 2020, 'period_label' => '2019-2020', 'title' => "Tallest minaret record", 'description' => "At 265 metres, the minaret of Djamaa el Djazaïr becomes the tallest in the world."],
                ],
            ],

            'djurdjura' => [
                'translation' => [
                    'name' => 'Djurdjura',
                    'description' => "Emblematic mountain range of Kabylia — snow-capped peaks in winter, forests and gorges, refuge of the Barbary macaque.",
                    'history' => "The Djurdjura is the highest range of the Tell chain, culminating at Lalla Khedidja over 2,300 metres. The Romans called it 'Mons Ferratus' (the iron mountain), referring to the mineral richness of the subsoil and to the fierce resistance the Kabyle populations put up against Roman annexation.\n\nThe very name Djurdjura is thought to come from the Kabyle 'Jjerjer', evoking great cold or altitude. The range is a natural water tower for the whole of Kabylia, cut by spectacular gorges, caves and cedar and oak forests that shelter one of the last wild populations of Barbary macaques — an endemic North African primate now threatened.\n\nThe massif was made a national park on 23 July 1983 to protect this exceptional natural heritage. The Tikjda resort on its southern slope makes it one of the few massifs in northern Algeria accessible for altitude hiking and, in winter, for skiing.",
                    'visit_info' => "The park is suited to hiking at various levels of difficulty, from the Tikjda resort to the summits of Lalla Khedidja. The surrounding gorges and waterfalls are best visited in spring, when the water is plentiful. Bring good walking shoes.",
                ],
                'timeline' => [
                    ['year' => -100, 'period_label' => 'Antiquity', 'title' => 'Resistance to Roman annexation', 'description' => "The Kabyle populations of the massif resist Roman rule, which nicknames it 'Mons Ferratus'."],
                    ['year' => 1983, 'period_label' => '23 July 1983', 'title' => 'Creation of the national park', 'description' => 'The Djurdjura is classified as a national park to protect its mountain ecosystems.'],
                    ['year' => 1990, 'period_label' => '20th century', 'title' => 'Development of Tikjda', 'description' => "The Tikjda resort develops as a gateway for hiking and skiing."],
                ],
            ],

            'sidi-boumediene' => [
                'translation' => [
                    'name' => 'Sidi Boumediene (El Eubbad)',
                    'description' => "Funerary and religious complex of the Sufi Abu Madyan — a masterpiece of Almohad and Marinid art near Tlemcen.",
                    'history' => "Abu Madyan, known as Sidi Boumediene, is one of the greatest Sufi masters of the medieval Maghreb, who died in 1198 while on his way to Marrakesh at the summons of the Almohad caliph. He was buried on the heights of El Eubbad, at the gates of Tlemcen, where a mausoleum was built shortly afterwards on the orders of the Almohad ruler Muhammad al-Nasir.\n\nOver the centuries, the site was considerably enriched: successive princes and rulers of Tlemcen added monuments and embellishments, turning the complex into an ensemble that includes a mosque, a madrasa and the mausoleum. The Marinid sultans of the 14th century funded works of great decorative richness there: carved doors, zellige mosaic tilework and muqarnas ceilings.\n\nThe tomb of Sidi Boumediene remains today a major Sufi pilgrimage site, visited by pilgrims from across the Maghreb, and one of the best-preserved medieval religious architectural ensembles in Algeria.",
                    'visit_info' => "The complex can be visited in about an hour. It sits on the heights of El Eubbad, a few kilometres from central Tlemcen, offering a fine view of the city. Respectful dress is recommended.",
                ],
                'timeline' => [
                    ['year' => 1198, 'period_label' => '1198', 'title' => 'Death of Abu Madyan', 'description' => "The Sufi master Abu Madyan dies on the road to Marrakesh; he is buried at El Eubbad."],
                    ['year' => 1200, 'period_label' => 'Around 1200', 'title' => 'Construction of the mausoleum', 'description' => "The Almohad caliph Muhammad al-Nasir has a mausoleum built over Abu Madyan's tomb."],
                    ['year' => 1339, 'period_label' => '1339', 'title' => 'Marinid enrichment', 'description' => 'The Marinid sultan Abu al-Hasan has a mosque, a madrasa and lavish decoration added.'],
                ],
            ],

            'gorges-du-rhumel-constantine' => [
                'translation' => [
                    'name' => 'Rhumel Gorges and suspension bridges',
                    'description' => "Dramatic natural canyon encircling Constantine, spanned by some of the most spectacular suspension bridges in the world.",
                    'history' => "Constantine, built on a rocky plateau, is ringed by the Rhumel gorges — a spectacular natural canyon carved to nearly 200 metres deep — which earned the city the nickname 'city of suspension bridges'. This exceptional geological setting long made Constantine a naturally fortified place.\n\nTo open up the city, several daring bridges were built at the turn of the 20th century under the administration of mayor Émile Morinaud. The most famous, the Sidi M'Cid Bridge — designed by French engineer Ferdinand Arnodin — was begun in 1909 and opened in 1912; at 175 metres above the canyon, it was then the tallest bridge in the world, a record it held until 1929.\n\nOther remarkable structures complete the ensemble, such as the Sidi Rached Bridge and the Bab El Kantara Bridge, making the crossing of the gorges by these suspended walkways one of the most vertiginous and photographed experiences in Algeria.",
                    'visit_info' => "Walking across the Sidi M'Cid Bridge — free and open at all times — offers the best views of the gorges. Several viewpoints laid out in the city also let you admire the canyon without crossing the bridges.",
                ],
                'timeline' => [
                    ['year' => 1909, 'period_label' => '1909', 'title' => "Start of construction of the Sidi M'Cid Bridge", 'description' => "Construction of the bridge, designed by engineer Ferdinand Arnodin, begins under mayor Émile Morinaud."],
                    ['year' => 1912, 'period_label' => '19 April 1912', 'title' => 'Inauguration', 'description' => "The Sidi M'Cid Bridge is inaugurated; it becomes the tallest bridge in the world."],
                    ['year' => 1929, 'period_label' => '1929', 'title' => 'Loss of the world record', 'description' => "The Royal Gorge Bridge in the United States surpasses the Sidi M'Cid Bridge in height."],
                    ['year' => 2000, 'period_label' => '2000', 'title' => 'Restoration', 'description' => "The bridge's cables are replaced during major restoration works."],
                ],
            ],

            'mansourah' => [
                'translation' => [
                    'name' => 'Mansourah',
                    'description' => "Ruins of a 14th-century Marinid royal city, dominated by an imposing 38-metre minaret, at the gates of Tlemcen.",
                    'history' => "Mansourah, 'the Victorious', was founded in 1299 by the Marinid sultan Abu Yaqub Yusuf as a fortified camp to besiege Tlemcen, then the capital of the rival Zayyanid dynasty. The siege lasted eight years, marked by famine in the besieged city, before ending abruptly with the assassination of the Marinid sultan in 1307.\n\nThe city was rebuilt and became again, in 1335, the headquarters for a second siege — this time led successfully by Sultan Abu al-Hasan, who took Tlemcen. Mansourah then acquired stone ramparts nearly 12 metres high, a palace and a great mosque whose portal was richly decorated under the patronage of the same ruler.\n\nAbandoned after the Marinid withdrawal, the city fell into ruin, but its 38-metre square minaret, inspired by the great Almohad and Andalusi towers, largely resisted time: only three of its four faces remain today, but it stands as one of the most impressive monuments of the Tlemcen region.",
                    'visit_info' => "The site, freely accessible, can be visited in an hour. The minaret, visible from afar, is the best-preserved element; the remains of the ramparts and palace can be discovered by walking through the enclosure.",
                ],
                'timeline' => [
                    ['year' => 1299, 'period_label' => '1299', 'title' => 'Foundation by the Marinids', 'description' => "Sultan Abu Yaqub Yusuf founds Mansourah as a siege camp against Tlemcen."],
                    ['year' => 1307, 'period_label' => '1307', 'title' => 'End of the first siege', 'description' => "The Marinid sultan is assassinated, ending the first siege of Tlemcen."],
                    ['year' => 1335, 'period_label' => '1335', 'title' => 'Second siege and capture of Tlemcen', 'description' => "Sultan Abu al-Hasan rebuilds Mansourah and eventually takes Tlemcen."],
                    ['year' => 1400, 'period_label' => '15th century', 'title' => 'Abandonment and ruin', 'description' => 'After the Marinid withdrawal, the city is gradually abandoned.'],
                ],
            ],

            'parc-national-el-kala' => [
                'translation' => [
                    'name' => 'El Kala National Park',
                    'description' => "Mosaic of lakes, marshes and cork oak forests in Algeria's far north-east — a world biosphere reserve.",
                    'history' => "El Kala National Park, created on 23 July 1983, protects one of the richest wetland complexes in North Africa, in Algeria's far north-east, on the Tunisian border. It contains six major bodies of water — including Lake Tonga and Lake Oubeira — and Lake Mellah, the country's only lagoon directly connected to the sea.\n\nThis diversity of habitats — lakes, peat bogs and vast cork oak forests culminating at Djebel El-Ghorra — makes it a sanctuary for exceptional wildlife: Barbary deer, wild boar, otters and more than 60 species of waterbirds. Five of the park's wetlands are listed as Ramsar sites of international importance.\n\nIn recognition of this ecological wealth, UNESCO designated the park a world biosphere reserve on 17 December 1990. El Kala remains today one of the favoured destinations for ecotourism in Algeria, between forest hikes and birdwatching.",
                    'visit_info' => "The shores of Lake Tonga, fitted with an observation trail, are ideal for birdwatching, especially at dawn. The town of El Kala and its beaches nicely complete the visit. Bring binoculars.",
                ],
                'timeline' => [
                    ['year' => 1983, 'period_label' => '23 July 1983', 'title' => 'Creation of the national park', 'description' => 'El Kala is classified as a national park to protect its exceptional wetlands.'],
                    ['year' => 1990, 'period_label' => '17 December 1990', 'title' => 'UNESCO biosphere reserve', 'description' => "UNESCO designates El Kala a world biosphere reserve."],
                    ['year' => 2004, 'period_label' => '2001-2009', 'title' => 'Ramsar listing', 'description' => "Five wetlands of the park are gradually listed as Ramsar sites of international importance."],
                ],
            ],

            'mausolee-royal-mauretanie' => [
                'translation' => [
                    'name' => 'Royal Mausoleum of Mauretania',
                    'description' => "Monumental tomb of the Berber king Juba II and Queen Cleopatra Selene, built in the 1st century BC on the heights of Tipaza.",
                    'history' => "The Royal Mausoleum of Mauretania, known locally as the 'Tomb of the Christian Woman' (Kbor er-Roumia) — a misleading name, as it has no connection with Christianity — is a monumental funerary structure built around 3 BC by King Juba II of Mauretania and his wife Cleopatra Selene II, daughter of Cleopatra VII of Egypt and Mark Antony. It is most likely the royal tomb intended for this Berber and Hellenistic couple.\n\nErected on a hill overlooking the coastal plain a few kilometres from the ancient city of Tipaza, the monument takes the form of a circular tumulus over 60 metres in diameter, set on a square base and topped by a stepped cone. Its original height was close to 40 metres — today reduced to about 30 metres after centuries of erosion and attempts at looting. Inside, a spiral vaulted gallery leads to a central funerary chamber, now empty.\n\nThe monument belongs to the tradition of Numidian royal tombs such as the Medracen further south, from which it draws architectural inspiration. It bears witness to the fusion of Berber traditions, Hellenistic influence and Roman art that characterised the kingdom of Juba II, a lettered ruler and loyal vassal of Rome. The site was inscribed on the UNESCO World Heritage list in 1982 as part of the 'Tipasa' property, but has been on the endangered heritage list since 2002.",
                    'visit_info' => "The mausoleum can be visited freely along a marked path from the car park. Allow one hour round trip, more if you want to walk around the whole monument. On clear days, the view from the heights reaches the sea.",
                ],
                'timeline' => [
                    ['year' => -25, 'period_label' => 'Around 25 BC', 'title' => 'Reign of Juba II', 'description' => 'The Berber king Juba II is installed by Rome as ruler of Mauretania, marries Cleopatra Selene and makes Iol/Caesarea (Cherchell) his capital.'],
                    ['year' => -3, 'period_label' => 'Around 3 BC', 'title' => 'Construction of the mausoleum', 'description' => 'The royal couple has the circular mausoleum built, most likely as their own burial place.'],
                    ['year' => 1866, 'period_label' => '19th century', 'title' => 'Scientific rediscovery', 'description' => "Adrien Berbrugger, curator of the Algiers museum, undertakes the first serious explorations of the monument."],
                    ['year' => 1982, 'period_label' => '1982', 'title' => 'UNESCO listing', 'description' => 'The mausoleum is inscribed as part of the Tipasa property on the World Heritage list.'],
                    ['year' => 2002, 'period_label' => '2002', 'title' => 'Endangered heritage list', 'description' => "The site is added to UNESCO's list of World Heritage in Danger due to threats to its integrity."],
                ],
            ],

            'cherchell' => [
                'translation' => [
                    'name' => 'Cherchell (Iol Caesarea)',
                    'description' => "Former capital of Mauretania under Juba II — a Mediterranean port with Roman ruins and a renowned archaeological museum.",
                    'history' => "Cherchell, the ancient Iol, was founded around the 6th century BC as a Phoenician and then Carthaginian trading post on the coast of central Mauretania. It rose to royal status under the reign of the Berber king Juba II, who chose it around 25 BC as the capital of his kingdom and renamed it Caesarea, in tribute to his protector Emperor Augustus.\n\nUnder this cultured ruler — educated in Rome and married to Cleopatra Selene, daughter of Cleopatra VII — the city became a brilliant Hellenistic cultural centre, endowed with a theatre, an amphitheatre, a hippodrome, a lighthouse inspired by the one in Alexandria and many works of art. Its population reached several tens of thousands at its peak.\n\nAnnexed by the Roman Empire on the death of Juba II's son Ptolemy in AD 40, Caesarea became the capital of a new province, Mauretania Caesariensis. The city prospered until the Vandal invasions of the 5th century, then declined. Today, Cherchell preserves important ancient remains — baths, theatre, aqueducts — and its National Public Museum holds one of the richest collections of Roman sculptures and mosaics in Algeria.",
                    'visit_info' => "The archaeological museum takes about 1.5 hours to visit; do not miss the statues and mosaics from Juba II's kingdom. The ruins of the ancient city, scattered through the modern urban fabric, can be freely explored in half a day.",
                ],
                'timeline' => [
                    ['year' => -600, 'period_label' => '6th - 5th century BC', 'title' => 'Phoenician foundation of Iol', 'description' => 'Phoenician merchants found a trading post on this strategic coastal site.'],
                    ['year' => -25, 'period_label' => 'Around 25 BC', 'title' => 'Juba II makes it his capital', 'description' => "The Berber king Juba II chooses Iol as the capital of his kingdom and renames it Caesarea."],
                    ['year' => 40, 'period_label' => 'AD 40', 'title' => 'Annexed by the Roman Empire', 'description' => "After the assassination of Juba II's son Ptolemy, Mauretania is incorporated into the Empire; Caesarea becomes the capital of Mauretania Caesariensis."],
                    ['year' => 429, 'period_label' => '5th century', 'title' => 'Vandal invasion', 'description' => "The city is taken by the Vandals, marking the start of its decline."],
                    ['year' => 1904, 'period_label' => '1904', 'title' => 'Opening of the archaeological museum', 'description' => "A museum is founded to house the rich collection of sculptures and mosaics unearthed on the site."],
                ],
            ],

            'notre-dame-afrique' => [
                'translation' => [
                    'name' => "Notre-Dame d'Afrique Basilica",
                    'description' => "Neo-Byzantine basilica perched on the Bologhine cliffs, overlooking Algiers and its bay since 1872.",
                    'history' => "Notre-Dame d'Afrique is a Catholic basilica built on the cliffs of the Bologhine district, on the western heights of Algiers, where it towers more than a hundred metres above the bay. Designed by architect Jean-Eugène Fromageau in a very free Neo-Byzantine style inspired by both Hagia Sophia in Constantinople and North African art, it was inaugurated in 1872 after fourteen years of construction.\n\nThe basilica owes its existence to the initiative of Archbishop Lavigerie of Algiers, founder of the missionary congregation of the White Fathers, who wished to give the city a sanctuary dedicated to the Virgin Mary as protector of sailors and the sick. It is famous for its inscription in the apse: 'Notre-Dame d'Afrique, pray for us and for the Muslims', reflecting the spirit of interfaith dialogue that presided over its construction — a message that remains powerful in today's Algeria.\n\nAfter Algerian independence in 1962, the basilica remained an active place of worship despite the sharp decline of the Catholic community. Restored between 2007 and 2010 through a large international project, it remains a strong architectural symbol of Algiers, visited by both faithful and tourists of all beliefs, drawn by its exceptional view and peaceful atmosphere.",
                    'visit_info' => "Access to the basilica and its esplanade is free outside services. The view over the Bay of Algiers and the Casbah is spectacular, especially at sunset. Respectful dress is recommended.",
                ],
                'timeline' => [
                    ['year' => 1858, 'period_label' => '1858', 'title' => 'Start of construction', 'description' => "Construction of the basilica begins on the heights of Bologhine under Archbishop Pavy, then Archbishop Lavigerie."],
                    ['year' => 1872, 'period_label' => '1872', 'title' => 'Inauguration', 'description' => "The basilica is inaugurated after fourteen years of work."],
                    ['year' => 1962, 'period_label' => '1962', 'title' => 'Algerian independence', 'description' => "Despite the mass departure of the Catholic community, the basilica remains open for worship."],
                    ['year' => 2010, 'period_label' => '2007-2010', 'title' => 'Major restoration', 'description' => "A large international restoration project restores the basilica to its original splendour."],
                ],
            ],

            'ghoufi' => [
                'translation' => [
                    'name' => "Balconies of Ghoufi",
                    'description' => "Spectacular Aurès canyon, with Chaoui cliff-dwelling villages clinging to walls over 200 metres high.",
                    'history' => "The Balconies of Ghoufi are a spectacular canyon carved by the Abiod River in the Aurès highlands, about 80 kilometres south of Batna. Stretching over 3 to 4 kilometres, the rocky walls rise in places to more than 200 metres, revealing a palette of reds, ochres and greys characteristic of the region.\n\nGhoufi's uniqueness lies in its cliff-hanging troglodyte villages: six hamlets — Hitesla, Idharene, Ath Mimoune, Ath Yahia, Ath Mansour and Taouriret — stack their stone houses right against the rock, on what are locally called 'balconies'. These dwellings, several centuries old, were built by the Chaouis, the Berber population of the Aurès, and continuously inhabited until the 1970s, before their inhabitants gradually moved down into the valley.\n\nThe buildings display traditional Berber architecture: walls of roughly cut stone bonded with local mortar, ceilings supported by palm trunks and wooden beams, fortress-granaries called taqliaths perched above the houses. Today partly in ruins, the site bears witness to a remarkable way of life adapted to a harsh environment, and attracts both hikers and lovers of Berber heritage.",
                    'visit_info' => "The canyon can be viewed from several developed viewpoints overlooking the gorge; the descent to the bottom is done on foot along steep paths (allow half a day). A local guide is recommended to explore the troglodyte villages.",
                ],
                'timeline' => [
                    ['year' => 1600, 'period_label' => '17th century', 'title' => 'Founding of the villages', 'description' => "The Chaouis build their first cliff-hanging hamlets in the canyon walls, some four centuries ago."],
                    ['year' => 1900, 'period_label' => 'Late 19th century', 'title' => 'Ethnographic recognition', 'description' => "French explorers and ethnographers document the Chaoui way of life in Ghoufi."],
                    ['year' => 1975, 'period_label' => '1970s', 'title' => 'Gradual abandonment', 'description' => "The inhabitants gradually move down to settle in the valley, leaving the villages in ruins."],
                    ['year' => 2000, 'period_label' => 'Since 2000', 'title' => 'Heritage tourism', 'description' => "The site attracts a growing number of visitors, combining hiking with discovery of Chaoui Berber heritage."],
                ],
            ],

            'grande-poste-alger' => [
                'translation' => [
                    'name' => "Grande Poste of Algiers",
                    'description' => "Neo-Moorish building of 1910 in the heart of the capital — a masterpiece of colonial 'Arabising' architecture and an emblem of Algiers.",
                    'history' => "The Grande Poste of Algiers was inaugurated in 1910 on the boulevard now known as Mohamed Khemisti, in the heart of the capital. Designed by architects Jules Voinot and Marius Toudoire, it is Algeria's largest postal building and one of the masterpieces of the so-called 'Neo-Moorish' or 'Arabising' style that colonial France developed at the turn of the 20th century.\n\nThis style, which sought to reinterpret Maghrebi and Andalusi architectural traditions, is expressed here through a main facade with three monumental horseshoe arches, decoration of ceramic and carved wood, and an interior domed hall entirely covered with mashrabiyas, carved plasterwork and zellige mosaic tiles — evoking the palaces of the Almoravids and Almohads.\n\nA strong architectural symbol of Algiers — on a par with the Casbah, the Martyrs' Memorial and Djamaa el Djazaïr — the Grande Poste has witnessed several major political events, from the uprising of 11 December 1960 to the Hirak protests of 2019. Since 2015, while retaining its postal function, part of the building has housed a museum dedicated to the history of post and telecommunications in Algeria, allowing visitors to freely discover its remarkable interiors.",
                    'visit_info' => "The interior can be freely visited during opening hours; the main hall and its dome are worth taking the time to look up at. The museum traces postal history and features fine stamp collections.",
                ],
                'timeline' => [
                    ['year' => 1910, 'period_label' => '1910', 'title' => 'Inauguration', 'description' => "The Grande Poste is inaugurated by the French authorities as the central headquarters of the Algerian postal service."],
                    ['year' => 1960, 'period_label' => '11 December 1960', 'title' => 'Independence protests', 'description' => "The boulevard opposite the Grande Poste becomes one of the main stages for popular protests in support of the FLN."],
                    ['year' => 2015, 'period_label' => '2015', 'title' => 'Opening of the Postal Museum', 'description' => "Part of the building is converted into a museum tracing the history of post and telecommunications in Algeria."],
                    ['year' => 2019, 'period_label' => '2019', 'title' => 'Hirak', 'description' => "The square in front of the Grande Poste becomes the epicentre of the Hirak movement's weekly protests."],
                ],
            ],
        ];
    }
}
