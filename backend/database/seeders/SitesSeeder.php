<?php

namespace Database\Seeders;

use App\Models\Site;
use Illuminate\Database\Seeder;

class SitesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->sites() as $data) {
            $site = Site::updateOrCreate(
                ['slug' => $data['slug']],
                [
                    'category' => $data['category'],
                    'wilaya' => $data['wilaya'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'opening_hours' => $data['opening_hours'],
                    'entry_fee' => $data['entry_fee'],
                    'image_path' => $data['image'],
                ]
            );

            foreach ($data['translations'] as $languageCode => $translation) {
                $site->translations()->updateOrCreate(
                    ['language_code' => $languageCode],
                    $translation
                );
            }

            $site->images()->delete();
            foreach ($data['gallery'] as $index => $image) {
                $site->images()->create([
                    'path' => $image['path'],
                    'caption' => $image['caption'],
                    'position' => $index + 1,
                ]);
            }

            $site->timelineEvents()->delete();
            foreach ($data['timeline'] as $event) {
                foreach (['fr', 'ar'] as $languageCode) {
                    $site->timelineEvents()->create([
                        'language_code' => $languageCode,
                        'year' => $event['year'],
                        'period_label' => $event[$languageCode]['period_label'],
                        'title' => $event[$languageCode]['title'],
                        'description' => $event[$languageCode]['description'],
                    ]);
                }
            }
        }
    }

    private function sites(): array
    {
        return [
            [
                'slug' => 'djemila',
                'category' => 'romain',
                'wilaya' => 'Setif',
                'latitude' => 36.3167,
                'longitude' => 5.7333,
                'opening_hours' => '08h00 - 17h00, fermé le mardi',
                'entry_fee' => '200 DA (résidents) / 300 DA (étrangers) — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/34/Roman_Ruins_of_Djemila_in_S%C3%A9tif%2C_Algeria.jpg/500px-Roman_Ruins_of_Djemila_in_S%C3%A9tif%2C_Algeria.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/57/Ancient_Roman_theater_%28Djemila%29_01.jpg/500px-Ancient_Roman_theater_%28Djemila%29_01.jpg', 'caption' => 'Le théâtre antique'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d9/Arch_of_Caracalla_%28Djemila%29_01.jpg/500px-Arch_of_Caracalla_%28Djemila%29_01.jpg', 'caption' => 'Arc de Caracalla'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f3/Mus%C3%A9e_de_Djemila_02.jpg/500px-Mus%C3%A9e_de_Djemila_02.jpg', 'caption' => 'Musée archéologique'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Djemila',
                        'description' => "Cité romaine de Cuicul, nichée dans les montagnes, l'une des mieux préservées d'Afrique du Nord.",
                        'history' => "Djemila, l'antique Cuicul, est fondée à la fin du Ier siècle après J.-C., sous le règne de l'empereur Nerva, comme colonie destinée à accueillir des vétérans de légion et à surveiller les hautes plaines de Numidie. Nichée à 900 mètres d'altitude dans un site accidenté des montagnes de la Petite Kabylie, choisi pour sa position défensive, la ville adopte un urbanisme plus organique que celui, parfaitement géométrique, de sa voisine Timgad.\n\nDurant les IIe et IIIe siècles, la cité connaît son apogée grâce au commerce du blé et de l'huile d'olive, denrées précieuses exportées vers Rome. Elle se dote d'un forum, de plusieurs temples dont celui dédié à la famille impériale des Sévères, d'un théâtre pouvant accueillir environ 3000 spectateurs, et de vastes demeures ornées de mosaïques d'une richesse exceptionnelle, aujourd'hui exposées au musée du site et comptant parmi les mieux conservées d'Afrique du Nord.\n\nÀ partir du IVe siècle, Cuicul se convertit au christianisme et se dote d'un important quartier ecclésiastique, avec basiliques et baptistère. La ville décline ensuite progressivement, fragilisée par l'instabilité politique de la fin de l'Empire romain puis par les invasions vandales du Ve siècle, avant d'être largement désertée. Passée sous domination musulmane au VIIe siècle, elle tombe dans l'oubli pendant plus d'un millénaire, ce qui préserve paradoxalement ses vestiges de toute reconstruction ultérieure.\n\nRedécouverte et fouillée méthodiquement à partir de 1909 sous la direction de l'archéologue français Albert Ballu, Djemila est aujourd'hui considérée comme l'un des ensembles urbains romains les mieux préservés du bassin méditerranéen, ce qui lui vaut son inscription au patrimoine mondial de l'UNESCO en 1982.",
                        'visit_info' => "Comptez environ 2 à 3 heures pour visiter l'ensemble du site. Portez des chaussures fermées, le sol est irrégulier. Le musée attenant abrite une collection exceptionnelle de mosaïques romaines à ne pas manquer. Le printemps et l'automne sont les saisons les plus agréables, l'été pouvant être très chaud en altitude.",
                    ],
                    'ar' => [
                        'name' => 'جميلة',
                        'description' => 'مدينة قويكول الرومانية، تقع بين الجبال، وتُعد من أفضل المواقع المحفوظة في شمال إفريقيا.',
                        'history' => "تأسست جميلة، المعروفة قديما باسم قويكول، في أواخر القرن الأول الميلادي في عهد الإمبراطور نيرفا، كمستعمرة رومانية خُصصت لاستقبال قدامى المحاربين من الفيلق ولمراقبة الهضاب العليا لنوميديا. اختير موقعها على ارتفاع 900 متر في تضاريس وعرة بجبال صغرى القبائل لاعتبارات دفاعية، مما جعل مخططها العمراني أكثر عضوية وتكيفا مع الأرض، على عكس جارتها تيمقاد ذات المخطط الشطرنجي الصارم.\n\nخلال القرنين الثاني والثالث، بلغت المدينة أوجها بفضل تجارة القمح وزيت الزيتون، وهما سلعتان ثمينتان كانتا تُصدَّران نحو روما. فازدانت بمنتدى وعدة معابد، من بينها معبد مخصص لأسرة السيفيروس الإمبراطورية، ومسرح يتسع لنحو 3000 متفرج، ومنازل فسيحة زُينت بفسيفساء استثنائية الجودة، معروضة اليوم في متحف الموقع ومن بين الأفضل حفظا في شمال إفريقيا.\n\nابتداء من القرن الرابع، اعتنقت قويكول المسيحية وشُيد فيها حي كنسي مهم بمعموديته وكنائسه. ثم تراجعت المدينة تدريجيا، أضعفتها الاضطرابات السياسية في أواخر عهد الإمبراطورية الرومانية ثم غزوات الوندال في القرن الخامس، قبل أن تُهجر إلى حد كبير. ومع خضوع المنطقة للحكم الإسلامي في القرن السابع، طواها النسيان لأكثر من ألف عام، وهو ما حفظ آثارها بشكل استثنائي من أي إعادة بناء لاحقة.\n\nأعيد اكتشاف الموقع وخضع لحفريات منهجية ابتداء من سنة 1909 بقيادة عالم الآثار الفرنسي ألبير بالو، وتُعد جميلة اليوم من أفضل المجمعات الحضرية الرومانية المحفوظة في حوض البحر الأبيض المتوسط، وهو ما أهّلها لتُدرج في قائمة التراث العالمي لليونسكو سنة 1982.",
                        'visit_info' => 'خصص ما بين ساعتين وثلاث ساعات لزيارة الموقع بأكمله. ارتد حذاء مغلقا فالأرضية غير مستوية. لا تفوت المتحف المجاور الذي يضم مجموعة استثنائية من الفسيفساء الرومانية. أفضل فترات الزيارة هي الربيع والخريف، فالصيف قد يكون شديد الحرارة في هذا الارتفاع.',
                    ],
                ],
                'timeline' => [
                    ['year' => 100, 'fr' => ['period_label' => 'Fin du Ier siècle', 'title' => 'Fondation de Cuicul', 'description' => 'Rome fonde Cuicul comme colonie militaire chargée de surveiller les hautes plaines de Numidie.'], 'ar' => ['period_label' => 'أواخر القرن الأول', 'title' => 'تأسيس قويكول', 'description' => 'أسس الرومان قويكول كمستعمرة عسكرية لمراقبة الهضاب العليا في نوميديا.']],
                    ['year' => 200, 'fr' => ['period_label' => 'IIe - IIIe siècle', 'title' => "Âge d'or de la cité", 'description' => "Grâce au commerce du blé et de l'huile d'olive, Cuicul se dote d'un forum, de temples et de somptueuses demeures ornées de mosaïques."], 'ar' => ['period_label' => 'القرنان الثاني والثالث', 'title' => 'العصر الذهبي للمدينة', 'description' => 'بفضل تجارة القمح وزيت الزيتون، تزينت قويكول بمنتدى ومعابد ومنازل فخمة مزينة بالفسيفساء.']],
                    ['year' => 216, 'fr' => ['period_label' => '216', 'title' => 'Nouveau forum et arc de Caracalla', 'description' => "Sous la dynastie des Sévères, la ville s'agrandit avec un second forum et un arc monumental dédié à l'empereur Caracalla."], 'ar' => ['period_label' => '216', 'title' => 'المنتدى الجديد وقوس كاراكلا', 'description' => 'في عهد أسرة السيفيروس، توسعت المدينة بمنتدى ثان وقوس ضخم أُهدي للإمبراطور كاراكلا.']],
                    ['year' => 260, 'fr' => ['period_label' => 'IIIe siècle', 'title' => 'Crise du IIIe siècle', 'description' => "Comme le reste de l'Empire romain, la cité traverse une période d'instabilité politique et économique."], 'ar' => ['period_label' => 'القرن الثالث', 'title' => 'أزمة القرن الثالث', 'description' => 'شهدت المدينة، كباقي أنحاء الإمبراطورية الرومانية، فترة من عدم الاستقرار السياسي والاقتصادي.']],
                    ['year' => 350, 'fr' => ['period_label' => 'IVe siècle', 'title' => 'Christianisation', 'description' => 'La ville se convertit au christianisme ; un quartier chrétien et un baptistère y sont construits.'], 'ar' => ['period_label' => 'القرن الرابع', 'title' => 'التنصير', 'description' => 'اعتنقت المدينة المسيحية، وبُني فيها حي مسيحي ومعمودية.']],
                    ['year' => 500, 'fr' => ['period_label' => 'VIe siècle', 'title' => 'Déclin et abandon', 'description' => 'Fragilisée par les invasions vandales, la cité est progressivement désertée par ses habitants.'], 'ar' => ['period_label' => 'القرن السادس', 'title' => 'التراجع والهجر', 'description' => 'أضعفتها غزوات الوندال، فهجرها سكانها تدريجيا.']],
                    ['year' => 650, 'fr' => ['period_label' => 'VIIe siècle', 'title' => 'Conquête musulmane de la région', 'description' => "La région passe sous domination musulmane ; le site antique, déjà largement abandonné, tombe dans l'oubli."], 'ar' => ['period_label' => 'القرن السابع', 'title' => 'الفتح الإسلامي للمنطقة', 'description' => 'خضعت المنطقة للحكم الإسلامي، وسقط الموقع القديم، المهجور أصلا إلى حد كبير، في النسيان.']],
                    ['year' => 1909, 'fr' => ['period_label' => '1909', 'title' => 'Fouilles archéologiques systématiques', 'description' => "Des fouilles françaises menées par Albert Ballu mettent au jour l'essentiel des vestiges visibles aujourd'hui."], 'ar' => ['period_label' => '1909', 'title' => 'حفريات أثرية منهجية', 'description' => 'كشفت حفريات فرنسية بقيادة ألبير بالو عن معظم الآثار المرئية اليوم.']],
                    ['year' => 1982, 'fr' => ['period_label' => '1982', 'title' => "Inscription à l'UNESCO", 'description' => "Djemila est inscrite au patrimoine mondial de l'UNESCO pour l'exceptionnel état de conservation de ses vestiges."], 'ar' => ['period_label' => '1982', 'title' => 'التسجيل في اليونسكو', 'description' => 'أُدرجت جميلة في قائمة التراث العالمي لليونسكو لحالة الحفظ الاستثنائية لآثارها.']],
                ],
            ],
            [
                'slug' => 'timgad',
                'category' => 'romain',
                'wilaya' => 'Batna',
                'latitude' => 35.4839,
                'longitude' => 6.4680,
                'opening_hours' => '08h00 - 17h00, tous les jours — à titre indicatif',
                'entry_fee' => '200 DA (résidents) / 300 DA (étrangers) — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ab/Timgad_Ruins_Panorama.jpg/500px-Timgad_Ruins_Panorama.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a8/Roman_Arch_of_Trajan_at_Thamugadi_%28Timgad%29%2C_Algeria_04966r.jpg/500px-Roman_Arch_of_Trajan_at_Thamugadi_%28Timgad%29%2C_Algeria_04966r.jpg', 'caption' => "L'arc de Trajan (photo d'archives)"],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Timgad%2C_Algeria_-_panoramio_%2830%29.jpg/500px-Timgad%2C_Algeria_-_panoramio_%2830%29.jpg', 'caption' => 'Vue générale des ruines'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ce/Timgad_%2815685889550%29.jpg/500px-Timgad_%2815685889550%29.jpg', 'caption' => 'Les colonnades du decumanus'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Timgad',
                        'description' => 'Cité romaine fondée par Trajan, célèbre pour son plan en damier parfaitement conservé.',
                        'history' => "Fondée en 100 après J.-C. par l'empereur Trajan sous le nom de Thamugadi, Timgad est conçue ex nihilo, en terrain vierge, selon un plan en damier d'une rigueur géométrique exceptionnelle, hérité de la tradition des camps militaires romains. La ville est peuplée de vétérans de la IIIe légion Auguste, récompensés de terres après leurs années de service, et organisée autour de deux axes principaux, le cardo et le decumanus, qui se croisent au forum.\n\nAu fil des IIe et IIIe siècles, portée par la prospérité agricole des hauts plateaux environnants, Timgad déborde largement de son plan initial : de nouveaux quartiers, thermes et arcs monumentaux — dont le célèbre arc dit « de Trajan », haut de 12 mètres — s'ajoutent hors les murs. La cité compte alors une bibliothèque publique, un théâtre de 3500 places et quatorze établissements de bains, un chiffre remarquable témoignant du niveau de vie de ses habitants.\n\nTimgad devient également, au IVe siècle, un foyer important du donatisme, mouvement chrétien dissident très implanté en Afrique du Nord et en délicatesse avec l'Église officielle de Rome. La ville périclite après les invasions vandales du Ve siècle ; un fort byzantin y est édifié en 539 pour protéger les derniers habitants, en vain : la cité est progressivement abandonnée puis ensevelie sous le sable au VIIe-VIIIe siècle, ce qui la préserve intacte pendant plus d'un millénaire.\n\nRedécouverte par des explorateurs européens au XIXe siècle puis fouillée systématiquement à partir de 1881, Timgad est aujourd'hui considérée comme l'un des exemples les plus complets et les mieux conservés d'urbanisme romain provincial, inscrite à ce titre au patrimoine mondial de l'UNESCO depuis 1982.",
                        'visit_info' => "Prévoyez 2 heures pour parcourir le site, qui s'étend sur une vaste surface plane peu ombragée : pensez à l'eau et à une protection solaire. L'arc de Trajan, à l'entrée, est l'un des monuments les plus photographiés d'Algérie.",
                    ],
                    'ar' => [
                        'name' => 'تيمقاد',
                        'description' => 'مدينة رومانية أسسها الإمبراطور تراجان، تشتهر بمخططها الشطرنجي المحفوظ بشكل استثنائي.',
                        'history' => "أسس الإمبراطور تراجان مدينة تيمقاد، المعروفة باسم ثاموغادي، سنة 100م على أرض عذراء، وفق مخطط شطرنجي بالغ الدقة الهندسية، موروث عن تقليد المعسكرات الرومانية العسكرية. استقر فيها قدامى المحاربين من الفيلق الروماني الثالث أوغسطة، الذين كوفئوا بأراض بعد سنوات خدمتهم، وتنظمت المدينة حول محورين رئيسيين يتقاطعان عند المنتدى.\n\nخلال القرنين الثاني والثالث، ومع ازدهار الزراعة في الهضاب المحيطة، توسعت تيمقاد كثيرا خارج مخططها الأصلي، فأضيفت أحياء جديدة وحمامات وأقواس ضخمة، من بينها قوس تراجان الشهير الذي يبلغ ارتفاعه 12 مترا. وضمت المدينة آنذاك مكتبة عمومية ومسرحا يتسع لـ 3500 متفرج وأربعة عشر حماما، رقم لافت يعكس مستوى معيشة سكانها المرتفع.\n\nأصبحت تيمقاد أيضا، في القرن الرابع، معقلا للدوناتية، وهي حركة مسيحية منشقة لاقت انتشارا واسعا في شمال إفريقيا وكانت في خلاف مع كنيسة روما الرسمية. وتراجعت المدينة بعد غزوات الوندال في القرن الخامس، وشُيد فيها حصن بيزنطي سنة 539 لحماية آخر سكانها، دون جدوى: هُجرت المدينة تدريجيا ثم غطتها الرمال في القرنين السابع والثامن، ما حفظها سليمة لأكثر من ألف عام.\n\nأعاد مستكشفون أوروبيون اكتشاف الموقع في القرن التاسع عشر، وخضع لحفريات منهجية ابتداء من 1881. وتُعد تيمقاد اليوم من أكمل الأمثلة وأفضلها حفظا للتخطيط العمراني الروماني في الأقاليم، وهو ما أهّلها للتسجيل في قائمة التراث العالمي لليونسكو سنة 1982.",
                        'visit_info' => 'خصص ساعتين لزيارة الموقع الذي يمتد على مساحة واسعة قليلة الظل، فكر في الماء والحماية من الشمس. يُعد قوس تراجان عند المدخل من أكثر المعالم التي يتم تصويرها في الجزائر.',
                    ],
                ],
                'timeline' => [
                    ['year' => 100, 'fr' => ['period_label' => '100 apr. J.-C.', 'title' => 'Fondation de Thamugadi', 'description' => "L'empereur Trajan fonde la cité pour les vétérans de la légion romaine, selon un plan en damier."], 'ar' => ['period_label' => '100م', 'title' => 'تأسيس ثاموغادي', 'description' => 'أسس الإمبراطور تراجان المدينة لقدامى المحاربين من الفيلق الروماني، وفق مخطط شطرنجي.']],
                    ['year' => 150, 'fr' => ['period_label' => 'Milieu du IIe siècle', 'title' => 'Extension hors les murs', 'description' => "La prospérité de la cité pousse son extension au-delà du plan initial, avec de nouveaux quartiers, des thermes supplémentaires et un arc de triomphe."], 'ar' => ['period_label' => 'منتصف القرن الثاني', 'title' => 'التوسع خارج الأسوار', 'description' => 'دفع ازدهار المدينة إلى توسعها خارج المخطط الأصلي، بأحياء جديدة وحمامات إضافية وقوس نصر.']],
                    ['year' => 200, 'fr' => ['period_label' => 'IIe - IIIe siècle', 'title' => 'Apogée de la cité', 'description' => "Timgad prospère, dotée d'un forum, d'une bibliothèque publique et de quatorze bains publics."], 'ar' => ['period_label' => 'القرنان الثاني والثالث', 'title' => 'أوج ازدهار المدينة', 'description' => 'ازدهرت تيمقاد بمنتدى ومكتبة عمومية وأربعة عشر حماما.']],
                    ['year' => 380, 'fr' => ['period_label' => 'IVe siècle', 'title' => 'Bastion du donatisme', 'description' => 'La ville devient un foyer du donatisme, un mouvement chrétien dissident particulièrement influent en Afrique du Nord.'], 'ar' => ['period_label' => 'القرن الرابع', 'title' => 'معقل الدوناتية', 'description' => 'أصبحت المدينة معقلا للدوناتية، وهي حركة مسيحية منشقة ذات تأثير كبير في شمال إفريقيا.']],
                    ['year' => 430, 'fr' => ['period_label' => 'Ve siècle', 'title' => 'Invasions vandales', 'description' => 'Les invasions vandales fragilisent la cité, qui amorce son déclin.'], 'ar' => ['period_label' => 'القرن الخامس', 'title' => 'غزوات الوندال', 'description' => 'أضعفت غزوات الوندال المدينة التي بدأت تتراجع.']],
                    ['year' => 539, 'fr' => ['period_label' => '539', 'title' => 'Fort byzantin', 'description' => "Après la reconquête byzantine de l'Afrique du Nord, un fort est édifié pour protéger les derniers habitants des raids berbères."], 'ar' => ['period_label' => '539', 'title' => 'الحصن البيزنطي', 'description' => 'بعد استعادة البيزنطيين لشمال إفريقيا، شُيد حصن لحماية السكان الباقين من غارات البربر.']],
                    ['year' => 700, 'fr' => ['period_label' => 'VIIe - VIIIe siècle', 'title' => 'Abandon et ensevelissement', 'description' => 'Progressivement recouverte par le sable, Timgad est abandonnée — ce qui préservera exceptionnellement ses vestiges.'], 'ar' => ['period_label' => 'القرنان السابع والثامن', 'title' => 'الهجر والدفن تحت الرمال', 'description' => 'غطتها الرمال تدريجيا وهُجرت المدينة، ما حفظ آثارها بشكل استثنائي.']],
                    ['year' => 1881, 'fr' => ['period_label' => '1881', 'title' => 'Redécouverte archéologique', 'description' => 'Les fouilles françaises mettent au jour un site remarquablement conservé sous le sable.'], 'ar' => ['period_label' => '1881', 'title' => 'إعادة الاكتشاف الأثري', 'description' => 'كشفت الحفريات الفرنسية عن موقع محفوظ بشكل استثنائي تحت الرمال.']],
                    ['year' => 1982, 'fr' => ['period_label' => '1982', 'title' => "Inscription à l'UNESCO", 'description' => 'Timgad est inscrite au patrimoine mondial de l\'UNESCO.'], 'ar' => ['period_label' => '1982', 'title' => 'التسجيل في اليونسكو', 'description' => 'أُدرجت تيمقاد في قائمة التراث العالمي لليونسكو.']],
                ],
            ],
            [
                'slug' => 'hoggar-assekrem',
                'category' => 'naturel',
                'wilaya' => 'Tamanrasset',
                'latitude' => 23.2667,
                'longitude' => 5.6333,
                'opening_hours' => 'Accès libre, guide et 4x4 recommandés — à titre indicatif',
                'entry_fee' => 'Gratuit (guide/transport à prévoir) — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/Panoramique_view_from_the_Assekrem.jpg/500px-Panoramique_view_from_the_Assekrem.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/Asskrem_Hoggar_2.jpg/500px-Asskrem_Hoggar_2.jpg', 'caption' => 'Pic Iharen (1732 m)'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/17/Hoggar8.jpg/500px-Hoggar8.jpg', 'caption' => 'Oasis du Hoggar'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/Hoggar_Mountains.jpg/500px-Hoggar_Mountains.jpg', 'caption' => 'Paysage volcanique du massif'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Hoggar — Assekrem',
                        'description' => 'Massif volcanique saharien culminant à plus de 2900 m, célèbre pour ses paysages et ses levers de soleil.',
                        'history' => "Le Hoggar, ou Ahaggar, est un massif montagneux d'origine volcanique qui s'élève au cœur du Sahara algérien. Né d'intenses activités volcaniques il y a plusieurs dizaines de millions d'années, il a ensuite été façonné par une érosion millénaire qui a sculpté ses roches en pitons acérés et en cheminées de basalte aux formes spectaculaires, dont le plus haut, le Tahat, culmine à plus de 2900 mètres — le point culminant de l'Algérie.\n\nDes gravures rupestres attestent d'une présence humaine dans le massif dès la préhistoire, bien avant l'arrivée, vers le XIe siècle, des Touaregs Kel Ahaggar, qui y développent un mode de vie pastoral et nomade parfaitement adapté aux contraintes du désert, fondé sur l'élevage de dromadaires et de chèvres et sur la maîtrise des rares points d'eau du massif.\n\nLe plateau de l'Assekrem doit sa renommée moderne à Charles de Foucauld, explorateur puis moine devenu ermite, qui y construit en 1911 une chapelle isolée à plus de 2700 mètres d'altitude, encore visible aujourd'hui. Au tournant du XXe siècle, plusieurs missions militaires françaises, dont celle de Foureau-Lamy, explorent et cartographient pour la première fois cette région jusqu'alors mal connue des Européens.\n\nEn 1987, un parc culturel est créé pour protéger à la fois le patrimoine naturel exceptionnel du massif et son patrimoine culturel touareg. L'Assekrem reste aujourd'hui l'une des destinations de trek les plus prisées du Sahara algérien, réputée pour ses levers et couchers de soleil sur un océan de roches volcaniques.",
                        'visit_info' => "La visite nécessite un guide local et un véhicule tout-terrain, les pistes n'étant pas praticables en voiture classique. Prévoyez un lever tôt pour rejoindre l'Assekrem avant l'aube. Les nuits sont fraîches, même en été : prévoyez des vêtements chauds.",
                    ],
                    'ar' => [
                        'name' => 'الهقار — آسكرام',
                        'description' => 'سلسلة جبلية بركانية صحراوية يتجاوز ارتفاعها 2900 متر، تشتهر بمناظرها الطبيعية وشروق الشمس.',
                        'history' => "الهقار، أو أهاغار، سلسلة جبلية ذات أصل بركاني ترتفع في قلب الصحراء الجزائرية. نشأت من أنشطة بركانية مكثفة قبل عشرات الملايين من السنين، ثم شكلتها تعرية استمرت آلاف السنين فنحتت صخورها إلى قمم حادة ومداخن بازلتية ذات أشكال مذهلة، أعلاها جبل تاهات الذي يتجاوز ارتفاعه 2900 متر، أعلى نقطة في الجزائر.\n\nتشهد نقوش صخرية على وجود بشري في السلسلة الجبلية منذ عصور ما قبل التاريخ، قبل وصول الطوارق من قبيلة كل أهاغار في نحو القرن الحادي عشر بزمن طويل، الذين طوروا نمط عيش رعويا وترحاليا يتلاءم تماما مع قيود الصحراء، قائما على تربية الإبل والماعز وإتقان إدارة نقاط المياه النادرة في السلسلة الجبلية.\n\nتدين هضبة آسكرام بشهرتها الحديثة لشارل دو فوكو، المستكشف الذي أصبح راهبا ناسكا، والذي بنى فيها سنة 1911 صومعة معزولة على ارتفاع يفوق 2700 متر، لا تزال قائمة إلى اليوم. ومع مطلع القرن العشرين، استكشفت عدة بعثات عسكرية فرنسية، من بينها بعثة فورو-لامي، هذه المنطقة التي كانت حتى ذلك الحين مجهولة إلى حد كبير لدى الأوروبيين، ورسمت خرائطها لأول مرة.\n\nسنة 1987، أُنشئ متنزه ثقافي لحماية التراث الطبيعي الاستثنائي للسلسلة الجبلية إلى جانب تراثها الثقافي الطارقي. وتبقى آسكرام اليوم من أكثر وجهات التنزه المرغوبة في الصحراء الجزائرية، تشتهر بشروق وغروب الشمس فوق محيط من الصخور البركانية.",
                        'visit_info' => 'تتطلب الزيارة مرشدا محليا ومركبة رباعية الدفع، فالمسالك غير صالحة للسيارات العادية. خطط للاستيقاظ باكرا للوصول إلى آسكرام قبل الفجر. الليالي باردة حتى في الصيف، لذا احضر ملابس دافئة.',
                    ],
                ],
                'timeline' => [
                    ['year' => -20000000, 'fr' => ['period_label' => 'Formation géologique', 'title' => 'Naissance du massif volcanique', 'description' => "D'intenses activités volcaniques façonnent le Hoggar sur des millions d'années, lui donnant son relief actuel de pitons et de cheminées basaltiques."], 'ar' => ['period_label' => 'التكوين الجيولوجي', 'title' => 'نشأة السلسلة البركانية', 'description' => 'شكلت أنشطة بركانية مكثفة الهقار عبر ملايين السنين، فمنحته تضاريسه الحالية من القمم الصخرية والمداخن البازلتية.']],
                    ['year' => -3000, 'fr' => ['period_label' => 'Préhistoire', 'title' => "Premières traces d'occupation", 'description' => "Des gravures rupestres attestent une présence humaine ancienne dans le massif, bien avant l'arrivée des Touaregs."], 'ar' => ['period_label' => 'عصور ما قبل التاريخ', 'title' => 'أقدم آثار الاستيطان', 'description' => 'تشهد نقوش صخرية على وجود بشري قديم في السلسلة الجبلية، قبل وصول الطوارق بزمن طويل.']],
                    ['year' => 1000, 'fr' => ['period_label' => 'Vers le XIe siècle', 'title' => 'Installation des Touaregs Kel Ahaggar', 'description' => 'Les Touaregs Kel Ahaggar s\'installent dans le massif, y développant un mode de vie pastoral adapté au désert.'], 'ar' => ['period_label' => 'نحو القرن الحادي عشر', 'title' => 'استقرار الطوارق كل أهاغار', 'description' => 'استقر الطوارق كل أهاغار في السلسلة الجبلية، وطوروا نمط عيش رعوي يتلاءم مع الصحراء.']],
                    ['year' => 1900, 'fr' => ['period_label' => 'Vers 1900', 'title' => 'Explorations françaises', 'description' => "Des missions militaires françaises, comme celle de Foureau-Lamy, explorent et cartographient le massif."], 'ar' => ['period_label' => 'نحو 1900', 'title' => 'الاستكشافات الفرنسية', 'description' => 'استكشفت بعثات عسكرية فرنسية، مثل بعثة فورو-لامي، السلسلة الجبلية ورسمت خرائطها.']],
                    ['year' => 1911, 'fr' => ['period_label' => '1911', 'title' => 'L\'ermitage de Charles de Foucauld', 'description' => "Charles de Foucauld construit un ermitage sur le plateau de l'Assekrem, encore visible aujourd'hui."], 'ar' => ['period_label' => '1911', 'title' => 'صومعة شارل دو فوكو', 'description' => 'بنى شارل دو فوكو صومعة على هضبة آسكرام، لا تزال قائمة إلى اليوم.']],
                    ['year' => 1987, 'fr' => ['period_label' => '1987', 'title' => "Création du Parc culturel de l'Ahaggar", 'description' => 'Un parc culturel est créé pour protéger le patrimoine naturel et culturel du massif.'], 'ar' => ['period_label' => '1987', 'title' => 'إنشاء المتنزه الثقافي للهقار', 'description' => 'أُنشئ متنزه ثقافي لحماية التراث الطبيعي والثقافي للسلسلة الجبلية.']],
                ],
            ],
            [
                'slug' => 'mosquee-ketchaoua',
                'category' => 'religieux',
                'wilaya' => 'Alger',
                'latitude' => 36.7838,
                'longitude' => 3.0610,
                'opening_hours' => 'Horaires de prière ; visite hors offices sur demande — à titre indicatif',
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/Ketchaoua_Mosque_-_Architectural_and_Cultural_Heritage_of_the_Casbah_of_Algiers_1.jpg/500px-Ketchaoua_Mosque_-_Architectural_and_Cultural_Heritage_of_the_Casbah_of_Algiers_1.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5f/Djama3_ketchaoua.jpg/500px-Djama3_ketchaoua.jpg', 'caption' => "L'un des minarets octogonaux"],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Algiers_cathedral_1899.jpg/500px-Algiers_cathedral_1899.jpg', 'caption' => 'La mosquée convertie en cathédrale (1899)'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8d/Mosqu%C3%A9e_Ketchaoua.jpg/500px-Mosqu%C3%A9e_Ketchaoua.jpg', 'caption' => 'Façade principale'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Mosquée Ketchaoua',
                        'description' => "Mosquée ottomane du XVIIIe siècle dominant la Casbah, à l'histoire mouvementée entre mosquée, cathédrale et à nouveau mosquée.",
                        'history' => "La mosquée Ketchaoua occupe un emplacement stratégique au cœur de la Casbah d'Alger, sur une terrasse dominant la baie. Une première mosquée y est édifiée en 1612, avant d'être largement reconstruite et agrandie en 1794 sur ordre du dey Hassan Pacha, qui lui donne l'essentiel de sa physionomie actuelle : une salle de prière hypostyle richement décorée, précédée d'une cour et flanquée de deux minarets octogonaux.\n\nAprès la conquête française de 1830, l'édifice est réquisitionné par les nouvelles autorités coloniales puis, en 1845, entièrement transformé en cathédrale catholique sous le nom de Saint-Philippe. Les architectes de l'époque conservent une partie de la structure et du décor mauresque d'origine tout en y ajoutant des voûtes et des éléments néo-byzantins, créant un édifice hybride assez unique dans le paysage religieux méditerranéen.\n\nÀ l'indépendance de l'Algérie en 1962, l'édifice retrouve sa fonction originelle de mosquée. Intégrée en 1992 au périmètre de la Casbah d'Alger inscrit au patrimoine mondial de l'UNESCO, la mosquée Ketchaoua demeure aujourd'hui l'un des monuments les plus visités et les plus chargés symboliquement de la capitale, résumant à elle seule les strates successives de l'histoire algéroise : ottomane, coloniale, puis indépendante.",
                        'visit_info' => "L'accès à l'intérieur peut être limité en dehors des heures de prière ; renseignez-vous sur place ou auprès d'un guide. La mosquée se visite idéalement dans le cadre d'une promenade dans la Casbah, dont elle constitue l'un des points hauts.",
                    ],
                    'ar' => [
                        'name' => 'جامع كتشاوة',
                        'description' => 'جامع عثماني من القرن الثامن عشر يطل على القصبة، بتاريخ حافل تنقل بين مسجد وكاتدرائية ثم مسجد من جديد.',
                        'history' => "يحتل جامع كتشاوة موقعا استراتيجيا في قلب قصبة الجزائر، على مصطبة تطل على الخليج. شُيد مسجد أول في هذا الموقع سنة 1612، قبل أن يُعاد بناؤه وتوسيعه بشكل كبير سنة 1794 بأمر من الداي حسن باشا، الذي أعطاه ملامحه الحالية إلى حد كبير: قاعة صلاة ذات أعمدة غنية الزخرفة، تسبقها ساحة ويحيط بها مئذنتان مثمنتا الشكل.\n\nبعد الاحتلال الفرنسي سنة 1830، صادرت السلطات الاستعمارية الجديدة المبنى، ثم حولته بالكامل سنة 1845 إلى كاتدرائية كاثوليكية باسم القديس فيليب. حافظ معماريو تلك الحقبة على جزء من البنية والزخرفة المغاربية الأصلية مع إضافة أقواس وعناصر بيزنطية حديثة، ما أنتج مبنى هجينا فريدا نوعا ما في المشهد الديني المتوسطي.\n\nمع استقلال الجزائر سنة 1962، استعاد المبنى وظيفته الأصلية كمسجد. وأُدرج سنة 1992 ضمن محيط قصبة الجزائر المسجل في قائمة التراث العالمي لليونسكو، ليبقى جامع كتشاوة اليوم من أكثر معالم العاصمة زيارة وأكثرها حمولة رمزية، إذ يختصر وحده الطبقات المتعاقبة لتاريخ مدينة الجزائر: العثمانية ثم الاستعمارية ثم عهد الاستقلال.",
                        'visit_info' => 'قد يكون الدخول إلى الداخل محدودا خارج أوقات الصلاة، يُنصح بالاستعلام في عين المكان أو عبر مرشد. يُفضل زيارة الجامع ضمن جولة في القصبة التي يشكل أحد أبرز معالمها.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1612, 'fr' => ['period_label' => '1612', 'title' => 'Construction initiale', 'description' => "Une première mosquée est édifiée à cet emplacement, au cœur de la Casbah d'Alger."], 'ar' => ['period_label' => '1612', 'title' => 'البناء الأول', 'description' => 'شُيد مسجد أول في هذا الموقع، في قلب قصبة الجزائر.']],
                    ['year' => 1794, 'fr' => ['period_label' => '1794', 'title' => 'Reconstruction ottomane', 'description' => 'Le dey Hassan Pacha fait reconstruire et agrandir la mosquée dans son état actuel.'], 'ar' => ['period_label' => '1794', 'title' => 'إعادة البناء العثمانية', 'description' => 'أعاد الداي حسن باشا بناء المسجد وتوسيعه ليأخذ شكله الحالي.']],
                    ['year' => 1830, 'fr' => ['period_label' => '1830', 'title' => 'Conquête française', 'description' => "Après la prise d'Alger, l'édifice est réquisitionné par les autorités coloniales."], 'ar' => ['period_label' => '1830', 'title' => 'الاحتلال الفرنسي', 'description' => 'بعد سقوط الجزائر العاصمة، صادرت السلطات الاستعمارية المبنى.']],
                    ['year' => 1845, 'fr' => ['period_label' => '1845', 'title' => 'Transformation en cathédrale Saint-Philippe', 'description' => 'La mosquée est convertie en cathédrale catholique, dans un style mêlant arcs mauresques et voûtes néo-byzantines.'], 'ar' => ['period_label' => '1845', 'title' => 'التحول إلى كاتدرائية القديس فيليب', 'description' => 'حُول المسجد إلى كاتدرائية كاثوليكية بأسلوب يمزج الأقواس المغاربية بالقباب البيزنطية الحديثة.']],
                    ['year' => 1962, 'fr' => ['period_label' => '1962', 'title' => 'Reconversion en mosquée', 'description' => "À l'indépendance de l'Algérie, l'édifice retrouve sa fonction de mosquée."], 'ar' => ['period_label' => '1962', 'title' => 'العودة إلى مسجد', 'description' => 'مع استقلال الجزائر، استعاد المبنى وظيفته كمسجد.']],
                    ['year' => 1992, 'fr' => ['period_label' => '1992', 'title' => 'Intégration au périmètre UNESCO', 'description' => "La mosquée est incluse dans le périmètre de la Casbah d'Alger, inscrite au patrimoine mondial de l'UNESCO."], 'ar' => ['period_label' => '1992', 'title' => 'الإدراج ضمن محيط اليونسكو', 'description' => 'أُدرج الجامع ضمن محيط قصبة الجزائر المسجلة في قائمة التراث العالمي لليونسكو.']],
                ],
            ],
            [
                'slug' => 'casbah-alger',
                'category' => 'casbah',
                'wilaya' => 'Alger',
                'latitude' => 36.7853,
                'longitude' => 3.0603,
                'opening_hours' => 'Accès libre aux ruelles, quartier habité — à titre indicatif',
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/16/Ruelle_%C3%A9troite_de_la_Casbah_d%27Alger%2C_April_2007.jpg/500px-Ruelle_%C3%A9troite_de_la_Casbah_d%27Alger%2C_April_2007.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/AlgerCasbah.jpg/500px-AlgerCasbah.jpg', 'caption' => 'Ruelle typique de la Casbah'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Algerfront.jpg/500px-Algerfront.jpg', 'caption' => 'Vue sur le massif du Bouzaréah'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3d/Grande_mosqu%C3%A9e_Alger.jpg/500px-Grande_mosqu%C3%A9e_Alger.jpg', 'caption' => 'La Grande Mosquée (Djamaâ El Kebir), XIe siècle'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => "Casbah d'Alger",
                        'description' => "Médina historique d'Alger, dédale de ruelles et de maisons ottomanes surplombant la baie, classée au patrimoine mondial.",
                        'history' => "La Casbah d'Alger désigne à la fois la citadelle sommitale et l'ensemble du quartier historique qui s'étend en amphithéâtre depuis le port jusqu'aux hauteurs de la ville, offrant un point de vue imprenable sur la baie. Le site est occupé depuis l'Antiquité sous le nom d'Icosium, modeste comptoir phénicien devenu cité romaine, mais c'est sous la Régence ottomane d'Alger, à partir de 1516, que le quartier prend véritablement sa forme actuelle : un dédale resserré de ruelles étroites, d'escaliers et de maisons aux patios intérieurs, conçu autant pour l'intimité domestique que pour la défense.\n\nAlger devient alors l'une des principales puissances corsaires de Méditerranée, un statut qui culmine au tournant des XVIIe et XVIIIe siècles et qui vaut à la ville plusieurs tentatives d'invasion étrangères : en 1541 déjà, la flotte de l'empereur Charles Quint échoue à s'emparer de la cité, repoussée notamment par une tempête providentielle ; en 1816, une flotte anglo-néerlandaise bombarde le port pour tenter de mettre fin aux activités de course.\n\nLa conquête française de 1830 marque une rupture profonde : une partie du tissu urbain ottoman est détruite pour faire place à une ville nouvelle à l'européenne, tandis que la Casbah elle-même, préservée dans ses grandes lignes, devient le symbole d'une identité algéroise résistante. Cette dimension de résistance culmine durant la guerre d'indépendance, lorsque le quartier sert de théâtre à la bataille d'Alger de 1956-1957.\n\nInscrite au patrimoine mondial de l'UNESCO en 1992 pour son architecture et son urbanisme exceptionnels, la Casbah reste aujourd'hui un quartier vivant et habité, dont une partie du bâti ancien souffre cependant d'un manque d'entretien et suscite d'importants efforts de sauvegarde.",
                        'visit_info' => "La visite se fait à pied, idéalement accompagnée d'un guide local pour comprendre l'histoire du quartier et accéder à certaines maisons traditionnelles. Portez des chaussures confortables, les ruelles sont pentues et pavées. Le quartier est habité : respectez la tranquillité des riverains.",
                    ],
                    'ar' => [
                        'name' => 'قصبة الجزائر',
                        'description' => 'المدينة العتيقة للجزائر العاصمة، متاهة من الأزقة والمنازل العثمانية المطلة على الخليج، مصنفة ضمن التراث العالمي.',
                        'history' => "تشير قصبة الجزائر إلى القلعة العليا وإلى كامل الحي التاريخي الممتد على شكل مدرج من الميناء إلى مرتفعات المدينة، بإطلالة رائعة على الخليج. شهد الموقع استيطانا منذ العصور القديمة تحت اسم إيكوزيوم، وهو مركز تجاري فينيقي متواضع تحول إلى مدينة رومانية، لكن الحي اتخذ شكله الحالي فعليا في عهد الإيالة العثمانية للجزائر، ابتداء من 1516: متاهة ضيقة من الأزقة والدرج والمنازل ذات الأفنية الداخلية، صُممت بقدر ما لأجل الخصوصية المنزلية بقدر ما لأجل الدفاع.\n\nأصبحت الجزائر آنذاك إحدى أبرز قوى القرصنة في البحر الأبيض المتوسط، وهي مكانة بلغت أوجها عند مطلع القرنين السابع عشر والثامن عشر، وجلبت للمدينة عدة محاولات غزو أجنبية: ففي 1541 فشل أسطول الإمبراطور شارل الخامس في الاستيلاء على المدينة، بعد أن أعاقته عاصفة كانت في صالحها؛ وفي 1816، قصف أسطول أنجلو-هولندي الميناء في محاولة لوضع حد لأنشطة القرصنة.\n\nشكل الاحتلال الفرنسي سنة 1830 قطيعة عميقة: دُمر جزء من النسيج العمراني العثماني لإفساح المجال لمدينة جديدة على الطراز الأوروبي، بينما أصبحت القصبة نفسها، التي حافظت على ملامحها العامة، رمزا لهوية جزائرية مقاومة. وبلغ هذا البعد المقاوم ذروته خلال حرب التحرير، حين كان الحي مسرحا لمعركة الجزائر 1956-1957.\n\nأُدرجت القصبة في قائمة التراث العالمي لليونسكو سنة 1992 لعمارتها وتخطيطها العمراني الاستثنائيين، وتبقى اليوم حيا حيا ومأهولا بالسكان، وإن كان جزء من مبانيه القديمة يعاني من نقص الصيانة، ما يستدعي جهودا كبيرة للحفاظ عليه.",
                        'visit_info' => 'تتم الزيارة سيرا على الأقدام، ويُفضل برفقة مرشد محلي لفهم تاريخ الحي والدخول إلى بعض المنازل التقليدية. ارتد حذاء مريحا فالأزقة منحدرة ومرصوفة بالحجارة. الحي مأهول بالسكان، فاحترم هدوء الجيران.',
                    ],
                ],
                'timeline' => [
                    ['year' => -100, 'fr' => ['period_label' => 'Antiquité', 'title' => 'Icosium phénicienne puis romaine', 'description' => 'Le site est occupé depuis l\'Antiquité sous le nom d\'Icosium, comptoir phénicien puis cité romaine.'], 'ar' => ['period_label' => 'العصور القديمة', 'title' => 'إيكوزيوم الفينيقية ثم الرومانية', 'description' => 'شهد الموقع استيطانا منذ العصور القديمة تحت اسم إيكوزيوم، مركزا تجاريا فينيقيا ثم مدينة رومانية.']],
                    ['year' => 1516, 'fr' => ['period_label' => '1516', 'title' => 'Début de la régence ottomane', 'description' => 'Alger devient le siège de la Régence ottomane ; la Casbah se développe comme citadelle et cœur de la ville fortifiée.'], 'ar' => ['period_label' => '1516', 'title' => 'بداية الإيالة العثمانية', 'description' => 'أصبحت الجزائر مقر الإيالة العثمانية، وتطورت القصبة كقلعة وقلب المدينة المحصنة.']],
                    ['year' => 1541, 'fr' => ['period_label' => '1541', 'title' => 'Échec du siège de Charles Quint', 'description' => "La flotte de l'empereur Charles Quint échoue à s'emparer d'Alger, freinée par une tempête, consolidant la réputation de la régence."], 'ar' => ['period_label' => '1541', 'title' => 'فشل حصار شارل الخامس', 'description' => 'فشل أسطول الإمبراطور شارل الخامس في الاستيلاء على الجزائر بعد أن أعاقته عاصفة، ما عزز سمعة الإيالة.']],
                    ['year' => 1660, 'fr' => ['period_label' => 'XVIIe - XVIIIe siècle', 'title' => 'Âge d\'or de la course', 'description' => 'Alger devient une capitale majeure de la course en Méditerranée, prospérant du commerce et des activités corsaires.'], 'ar' => ['period_label' => 'القرنان السابع عشر والثامن عشر', 'title' => 'العصر الذهبي لنشاط القرصنة', 'description' => 'أصبحت الجزائر عاصمة رئيسية للقرصنة في البحر الأبيض المتوسط، مزدهرة بفضل التجارة وأنشطة القرصنة.']],
                    ['year' => 1816, 'fr' => ['period_label' => '1816', 'title' => 'Bombardement anglo-néerlandais', 'description' => 'Une flotte britannique et néerlandaise bombarde la ville pour mettre fin aux activités corsaires.'], 'ar' => ['period_label' => '1816', 'title' => 'القصف الأنجلو-هولندي', 'description' => 'قصف أسطول بريطاني هولندي المدينة لوضع حد لأنشطة القرصنة.']],
                    ['year' => 1830, 'fr' => ['period_label' => '1830', 'title' => 'Conquête française', 'description' => "La prise d'Alger par la France marque un tournant, avec de profondes transformations urbaines autour de la Casbah."], 'ar' => ['period_label' => '1830', 'title' => 'الاحتلال الفرنسي', 'description' => 'شكل احتلال فرنسا للجزائر نقطة تحول، مع تحولات عمرانية عميقة حول القصبة.']],
                    ['year' => 1957, 'fr' => ['period_label' => '1956-1957', 'title' => "Bataille d'Alger", 'description' => "La Casbah est le théâtre de la bataille d'Alger, épisode majeur de la guerre d'indépendance."], 'ar' => ['period_label' => '1956-1957', 'title' => 'معركة الجزائر', 'description' => 'كانت القصبة مسرحا لمعركة الجزائر، وهي حلقة رئيسية من حرب التحرير.']],
                    ['year' => 1992, 'fr' => ['period_label' => '1992', 'title' => "Inscription à l'UNESCO", 'description' => 'La Casbah est inscrite au patrimoine mondial de l\'UNESCO pour son architecture et son urbanisme exceptionnels.'], 'ar' => ['period_label' => '1992', 'title' => 'التسجيل في اليونسكو', 'description' => 'أُدرجت القصبة في قائمة التراث العالمي لليونسكو لعمارتها وتخطيطها العمراني الاستثنائيين.']],
                ],
            ],
            [
                'slug' => 'tassili-najjer',
                'category' => 'prehistorique',
                'wilaya' => 'Illizi',
                'latitude' => 25.5000,
                'longitude' => 8.5000,
                'opening_hours' => 'Accès réglementé, guide et autorisation obligatoires — à titre indicatif',
                'entry_fee' => 'Sur devis (circuit organisé) — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Dunes_at_Tassili_n%27Ajjer.jpg/500px-Dunes_at_Tassili_n%27Ajjer.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Running_Horned_Woman%2C_6%2C000%E2%80%934%2C000_B.C.E.%2C_pigment_on_rock%2C_Tassili_n%E2%80%99Ajjer%2C_Algeria.jpg/500px-Running_Horned_Woman%2C_6%2C000%E2%80%934%2C000_B.C.E.%2C_pigment_on_rock%2C_Tassili_n%E2%80%99Ajjer%2C_Algeria.jpg', 'caption' => '« La Femme cornue qui court » (6000-4000 av. J.-C.)'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/49/Cave_painting_from_the_Tassili_n%27Ajjer_mountains.jpg/500px-Cave_painting_from_the_Tassili_n%27Ajjer_mountains.jpg', 'caption' => 'Peinture rupestre du plateau'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a3/Crying_cow_of_Taghrirt.jpg/500px-Crying_cow_of_Taghrirt.jpg', 'caption' => '« La vache pleureuse de Taghrirt »'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => "Tassili n'Ajjer",
                        'description' => "Immense plateau saharien classé au patrimoine mondial, abritant l'une des plus grandes concentrations d'art rupestre préhistorique au monde.",
                        'history' => "Le Tassili n'Ajjer est un immense plateau gréseux du Sahara central, sculpté sur des millions d'années par l'érosion en un paysage spectaculaire de canyons profonds, d'arches naturelles et de véritables « forêts de pierre ». Mais sa renommée mondiale tient avant tout aux quelque 15 000 gravures et peintures rupestres qui couvrent ses parois, l'une des plus importantes concentrations d'art préhistorique jamais recensées, échelonnées sur près de dix millénaires.\n\nLes spécialistes distinguent plusieurs grandes périodes stylistiques successives. La plus ancienne, dite « bubaline », voit apparaître dès le Xe millénaire avant notre ère des représentations d'une faune sauvage aujourd'hui disparue de la région — buffles géants, éléphants, rhinocéros. Lui succède, à partir du VIIIe millénaire, la période dite des « têtes rondes », marquée par des figures humaines stylisées aux allures rituelles ou chamaniques. Puis vient, du IVe au IIe millénaire, la période bovidienne, la plus riche et la plus célèbre, où des scènes de troupeaux et de vie quotidienne — dont la fameuse « Femme cornue qui court » — témoignent d'un Sahara encore verdoyant. Les périodes chevaline puis cameline qui suivent accompagnent la progressive et irréversible aridification du Sahara.\n\nLongtemps connu des seules populations touarègues, le site est véritablement révélé au monde par l'officier français Charles Brenans en 1933, puis surtout par la célèbre mission de l'ethnologue Henri Lhote en 1956-1957, dont les relevés et les copies de peintures, exposés et publiés internationalement, suscitent un engouement considérable pour ce « Louvre du désert ». Inscrit au patrimoine mondial de l'UNESCO en 1982 à double titre, naturel et culturel, le Tassili n'Ajjer demeure aujourd'hui un site à l'accès strictement réglementé, tant pour préserver sa fragilité que pour la sécurité des visiteurs dans cette région reculée du Sahara.",
                        'visit_info' => "La visite se fait exclusivement en trek encadré par un guide agréé, sur plusieurs jours, avec autorisation préalable. Prévoyez un équipement de randonnée désertique complet (eau, protection solaire, vêtements chauds la nuit). Les circuits partent généralement de Djanet.",
                    ],
                    'ar' => [
                        'name' => 'طاسيلي ناجر',
                        'description' => 'هضبة صحراوية شاسعة مصنفة ضمن التراث العالمي، تضم واحدة من أكبر تجمعات الفن الصخري ما قبل التاريخ في العالم.',
                        'history' => "طاسيلي ناجر هضبة رملية شاسعة في قلب الصحراء الوسطى، نحتتها التعرية على مدى ملايين السنين لتشكل مناظر طبيعية مذهلة من الأخاديد العميقة والأقواس الطبيعية وما يشبه «غابات حجرية» حقيقية. لكن شهرته العالمية تعود بالدرجة الأولى إلى نحو 15000 نقش ورسم صخري تغطي جدرانه، وهي واحدة من أكبر تجمعات الفن ما قبل التاريخي المسجلة في العالم، تمتد عبر ما يقارب عشرة آلاف سنة.\n\nيميز المختصون عدة فترات أسلوبية متعاقبة. أقدمها، المعروفة بـ«البوبالوس»، شهدت منذ الألفية العاشرة قبل الميلاد ظهور رسوم لحيوانات برية اختفت اليوم من المنطقة، كالجاموس العملاق والفيلة ووحيد القرن. تلتها، ابتداء من الألفية الثامنة، فترة «الرؤوس المستديرة»، المتميزة بشخصيات بشرية مصممة ذات طابع طقوسي أو شاماني. ثم جاءت، من الألفية الرابعة إلى الثانية، الفترة البقرية، الأغنى والأشهر، حيث تشهد مشاهد القطعان والحياة اليومية - ومنها لوحة «المرأة القرناء الراكضة» الشهيرة - على صحراء كانت لا تزال خضراء. أما الفترتان الخيلية ثم الجملية اللتان تليانها، فترافقان التصحر التدريجي الذي لا رجعة فيه للصحراء.\n\nظل الموقع معروفا لدى سكان الطوارق فقط لزمن طويل، قبل أن يكشفه للعالم الضابط الفرنسي شارل برينان سنة 1933، ثم بشكل خاص بعثة عالم الإثنولوجيا هنري لوط الشهيرة سنة 1956-1957، التي أثارت رسوماته ونسخه المعروضة والمنشورة عالميا إعجابا كبيرا بهذا «متحف اللوفر الصحراوي». أُدرج طاسيلي ناجر في قائمة التراث العالمي لليونسكو سنة 1982 بصفة مزدوجة، طبيعية وثقافية، ويبقى اليوم موقعا ذا دخول مقنن بصرامة، سواء للحفاظ على هشاشته أو لسلامة الزوار في هذه المنطقة النائية من الصحراء.",
                        'visit_info' => 'تتم الزيارة حصريا عبر رحلة مشي برفقة مرشد معتمد، على مدى عدة أيام، وبترخيص مسبق. جهز عتاد تنزه صحراوي كاملا (ماء، حماية من الشمس، ملابس دافئة لليل). تنطلق الرحلات عادة من مدينة جانت.',
                    ],
                ],
                'timeline' => [
                    ['year' => -10000, 'fr' => ['period_label' => 'Xe - VIIIe millénaire av. J.-C.', 'title' => 'Période dite « bubaline »', 'description' => "Les gravures les plus anciennes représentent une faune sauvage aujourd'hui disparue de la région : buffles géants, éléphants, rhinocéros."], 'ar' => ['period_label' => 'الألفية العاشرة إلى الثامنة ق.م', 'title' => 'فترة «البوبالوس»', 'description' => 'صورت أقدم النقوش حيوانات برية اختفت اليوم من المنطقة، كالجاموس العملاق والفيلة ووحيد القرن.']],
                    ['year' => -8000, 'fr' => ['period_label' => 'VIIIe - VIe millénaire av. J.-C.', 'title' => 'Période des « têtes rondes »', 'description' => 'Apparaissent des figures humaines stylisées à tête arrondie, souvent interprétées comme des scènes rituelles ou chamaniques.'], 'ar' => ['period_label' => 'الألفية الثامنة إلى السادسة ق.م', 'title' => 'فترة «الرؤوس المستديرة»', 'description' => 'ظهرت شخصيات بشرية مصممة برؤوس مستديرة، تُفسَّر غالبا كمشاهد طقوسية أو شامانية.']],
                    ['year' => -4000, 'fr' => ['period_label' => 'IVe - IIe millénaire av. J.-C.', 'title' => 'Période bovidienne', 'description' => "Les représentations de troupeaux de bovins, comme la célèbre « Femme cornue qui court », témoignent du développement de l'élevage, alors que le Sahara est encore une savane verdoyante."], 'ar' => ['period_label' => 'الألفية الرابعة إلى الثانية ق.م', 'title' => 'الفترة البقرية', 'description' => 'تشهد رسوم قطعان الأبقار، مثل لوحة «المرأة القرناء الراكضة» الشهيرة، على تطور تربية الماشية بينما كانت الصحراء لا تزال سافانا خضراء.']],
                    ['year' => -1500, 'fr' => ['period_label' => 'À partir du XVe siècle av. J.-C.', 'title' => 'Période chevaline', 'description' => 'Le cheval et le char apparaissent dans les représentations, introduits par des contacts avec le monde méditerranéen.'], 'ar' => ['period_label' => 'ابتداء من القرن الخامس عشر ق.م', 'title' => 'الفترة الخيلية', 'description' => 'ظهر الحصان والعربة في الرسوم، بفضل الاتصال بعالم البحر الأبيض المتوسط.']],
                    ['year' => -500, 'fr' => ['period_label' => 'Ier millénaire av. J.-C.', 'title' => 'Période cameline et aridification', 'description' => "L'arrivée du dromadaire dans l'iconographie accompagne la transformation définitive du Sahara en désert."], 'ar' => ['period_label' => 'الألفية الأولى ق.م', 'title' => 'الفترة الجملية والتصحر', 'description' => 'رافق ظهور الجمل في الرسوم التحول النهائي للصحراء إلى أرض قاحلة.']],
                    ['year' => 1933, 'fr' => ['period_label' => '1933', 'title' => 'Découverte moderne', 'description' => "L'officier français Charles Brenans documente pour la première fois l'ampleur des gravures et peintures du plateau."], 'ar' => ['period_label' => '1933', 'title' => 'الاكتشاف الحديث', 'description' => 'وثق الضابط الفرنسي شارل برينان لأول مرة حجم النقوش والرسوم الموجودة في الهضبة.']],
                    ['year' => 1956, 'fr' => ['period_label' => '1956-1957', 'title' => 'Mission Henri Lhote', 'description' => "L'ethnologue français Henri Lhote mène une expédition qui fait connaître internationalement les peintures rupestres du plateau."], 'ar' => ['period_label' => '1956-1957', 'title' => 'بعثة هنري لوط', 'description' => 'قاد عالم الإثنولوجيا الفرنسي هنري لوط بعثة جعلت الرسوم الصخرية للهضبة معروفة عالميا.']],
                    ['year' => 1982, 'fr' => ['period_label' => '1982', 'title' => "Inscription à l'UNESCO", 'description' => "Le Tassili n'Ajjer est inscrit au patrimoine mondial de l'UNESCO, au titre à la fois naturel et culturel."], 'ar' => ['period_label' => '1982', 'title' => 'التسجيل في اليونسكو', 'description' => 'أُدرج طاسيلي ناجر في قائمة التراث العالمي لليونسكو بصفته موقعا طبيعيا وثقافيا معا.']],
                ],
            ],
            [
                'slug' => 'hippone',
                'category' => 'romain',
                'wilaya' => 'Annaba',
                'latitude' => 36.9000,
                'longitude' => 7.7667,
                'opening_hours' => '08h00 - 17h00, tous les jours — à titre indicatif',
                'entry_fee' => '200 DA — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Hippo_Regius%2C_Africa_Proconsularis%2C_Algeria_-_52575435338.jpg/500px-Hippo_Regius%2C_Africa_Proconsularis%2C_Algeria_-_52575435338.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/61/Hippo_Regius_-_Annaba_02.jpg/1280px-Hippo_Regius_-_Annaba_02.jpg', 'caption' => 'Vestiges romains du site d\'Hippone'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d6/Hippo_Regius_Algeria.jpg/1280px-Hippo_Regius_Algeria.jpg', 'caption' => 'Ruines antiques d\'Hippo Regius'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/ae/Hippo_Regius_Algeria_5.jpg/500px-Hippo_Regius_Algeria_5.jpg', 'caption' => 'Ruines romaines'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Hippone',
                        'description' => 'Cité antique berbéro-punique puis romaine, associée à saint Augustin, qui y fut évêque.',
                        'history' => "Hippone, l'antique Hippo Regius, doit son nom à son passé de résidence royale : sous les rois numides, dont le célèbre Massinissa, la ville sert de capitale secondaire, ce qui lui vaut l'épithète latine de « Regius ». Fondée plusieurs siècles auparavant par des marchands phéniciens venus établir un comptoir sur cette côte propice au commerce maritime, elle passe sous domination romaine en 46 avant notre ère, après la chute du royaume numide.\n\nDevenue port romain prospère, Hippone doit sa célébrité historique la plus durable à saint Augustin, l'un des pères de l'Église et l'un des plus grands penseurs de l'Antiquité tardive, qui en est l'évêque de 396 jusqu'à sa mort en 430. C'est à Hippone qu'Augustin rédige une grande partie de son œuvre, dont ses célèbres Confessions, et qu'il anime une intense vie intellectuelle et théologique qui rayonne sur tout le christianisme occidental naissant. Sa mort survient alors que la ville est assiégée par les Vandales, qui s'en emparent peu après.\n\nLe site antique tombe ensuite dans un long oubli, jusqu'à ce que des fouilles archéologiques françaises, engagées dès la fin du XIXe siècle et poursuivies dans les années 1930, mettent au jour le forum, les thermes et l'important quartier chrétien de la ville. En hommage à saint Augustin, une basilique moderne est édifiée à la toute fin du XIXe siècle sur la colline surplombant les ruines, où elle domine aujourd'hui encore le site archéologique.",
                        'visit_info' => "Le site archéologique comprend un forum, des thermes et une basilique moderne dédiée à saint Augustin, construite au XIXe siècle en surplomb des ruines. Comptez 1h30 à 2h de visite.",
                    ],
                    'ar' => [
                        'name' => 'هيبون',
                        'description' => 'مدينة قديمة أمازيغية بونيقية ثم رومانية، ارتبط اسمها بالقديس أوغسطين الذي كان أسقفا فيها.',
                        'history' => "تدين هيبون، المعروفة باسم هيبو ريجيوس، باسمها لماضيها كمقر إقامة ملكية: في عهد ملوك نوميديا، ومنهم ماسينيسا الشهير، كانت المدينة عاصمة ثانوية، وهو ما أكسبها اللقب اللاتيني «ريجيوس». أسسها قبل ذلك بقرون تجار فينيقيون أقاموا مركزا تجاريا على هذا الساحل، ثم خضعت للحكم الروماني سنة 46 قبل الميلاد، بعد سقوط المملكة النوميدية.\n\nبعد أن أصبحت ميناء رومانيا مزدهرا، تدين هيبون بشهرتها التاريخية الأبقى للقديس أوغسطين، أحد آباء الكنيسة وأحد أعظم مفكري أواخر العصور القديمة، الذي كان أسقفا فيها من سنة 396 حتى وفاته سنة 430. وفي هيبون كتب أوغسطين جزءا كبيرا من أعماله، ومنها كتابه الشهير «الاعترافات»، وأدار فيها حياة فكرية ولاهوتية مكثفة أثرت على المسيحية الغربية الناشئة بأكملها. وقد وافته المنية بينما كانت المدينة تحت حصار الوندال، الذين استولوا عليها بعد ذلك بقليل.\n\nطوى النسيان الموقع القديم بعدها لزمن طويل، إلى أن كشفت حفريات أثرية فرنسية، بدأت في أواخر القرن التاسع عشر واستمرت في ثلاثينيات القرن العشرين، عن المنتدى والحمامات والحي المسيحي المهم للمدينة. وتكريما للقديس أوغسطين، شُيدت كاتدرائية حديثة في أواخر القرن التاسع عشر على التلة المطلة على الأطلال، لا تزال تهيمن اليوم على الموقع الأثري.",
                        'visit_info' => 'يضم الموقع الأثري منتدى وحمامات وكاتدرائية حديثة مخصصة للقديس أوغسطين، بُنيت في القرن التاسع عشر تطل على الآثار. خصص ساعة ونصف إلى ساعتين للزيارة.',
                    ],
                ],
                'timeline' => [
                    ['year' => -1000, 'fr' => ['period_label' => 'Ier millénaire av. J.-C.', 'title' => 'Fondation phénicienne', 'description' => 'Des marchands phéniciens fondent un comptoir commercial sur ce site côtier.'], 'ar' => ['period_label' => 'الألفية الأولى ق.م', 'title' => 'التأسيس الفينيقي', 'description' => 'أسس تجار فينيقيون مركزا تجاريا في هذا الموقع الساحلي.']],
                    ['year' => -203, 'fr' => ['period_label' => 'IIIe - IIe siècle av. J.-C.', 'title' => 'Résidence des rois numides', 'description' => "Sous les rois numides, dont Massinissa, Hippone sert de résidence royale, ce qui lui vaut son nom latin d'Hippo Regius."], 'ar' => ['period_label' => 'القرنان الثالث والثاني ق.م', 'title' => 'إقامة ملوك نوميديا', 'description' => 'في عهد ملوك نوميديا، ومنهم ماسينيسا، كانت هيبون مقر إقامة ملكية، ما أكسبها اسمها اللاتيني هيبو ريجيوس.']],
                    ['year' => -46, 'fr' => ['period_label' => '46 av. J.-C.', 'title' => 'Annexion romaine', 'description' => "La cité rejoint la province romaine d'Afrique après la chute du royaume de Numidie."], 'ar' => ['period_label' => '46 ق.م', 'title' => 'الضم إلى روما', 'description' => 'انضمت المدينة إلى الولاية الرومانية الإفريقية بعد سقوط مملكة نوميديا.']],
                    ['year' => 396, 'fr' => ['period_label' => '396', 'title' => "Augustin devient évêque d'Hippone", 'description' => "Saint Augustin, l'une des grandes figures de la pensée chrétienne, dirige le diocèse jusqu'à sa mort en 430."], 'ar' => ['period_label' => '396', 'title' => 'أوغسطين أسقفا على هيبون', 'description' => 'تولى القديس أوغسطين، أحد كبار مفكري المسيحية، إدارة الأسقفية حتى وفاته سنة 430.']],
                    ['year' => 430, 'fr' => ['period_label' => '430', 'title' => 'Siège vandale', 'description' => "La ville est assiégée par les Vandales alors qu'Augustin agonise ; elle tombe peu après sa mort."], 'ar' => ['period_label' => '430', 'title' => 'حصار الوندال', 'description' => 'حوصرت المدينة من قبل الوندال وأوغسطين يحتضر، وسقطت بعد وفاته بقليل.']],
                    ['year' => 1900, 'fr' => ['period_label' => 'Fin du XIXe siècle', 'title' => 'Construction de la basilique Saint-Augustin', 'description' => 'Une basilique moderne est édifiée en surplomb du site, en hommage à saint Augustin.'], 'ar' => ['period_label' => 'أواخر القرن التاسع عشر', 'title' => 'بناء كاتدرائية القديس أوغسطين', 'description' => 'شُيدت كاتدرائية حديثة تطل على الموقع تكريما للقديس أوغسطين.']],
                    ['year' => 1930, 'fr' => ['period_label' => 'XXe siècle', 'title' => 'Fouilles archéologiques modernes', 'description' => 'Des fouilles françaises mettent au jour le forum, les thermes et le quartier chrétien du site.'], 'ar' => ['period_label' => 'القرن العشرون', 'title' => 'حفريات أثرية حديثة', 'description' => 'كشفت حفريات فرنسية عن المنتدى والحمامات والحي المسيحي في الموقع.']],
                ],
            ],
            [
                'slug' => 'kalaa-beni-hammad',
                'category' => 'islamique',
                'wilaya' => "M'Sila",
                'latitude' => 35.8333,
                'longitude' => 4.7833,
                'opening_hours' => '08h00 - 17h00 — à titre indicatif',
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/00/28-2_Kal%C3%A2a_de_Beni_Hammad_%283%29.jpg/500px-28-2_Kal%C3%A2a_de_Beni_Hammad_%283%29.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/00/28-2_Kal%C3%A2a_de_Beni_Hammad_%282%29.jpg/1280px-28-2_Kal%C3%A2a_de_Beni_Hammad_%282%29.jpg', 'caption' => 'Ruines de la forteresse hammadide'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/28-2_Kal%C3%A2a_de_Beni_Hammad_%284%29.jpg/500px-28-2_Kal%C3%A2a_de_Beni_Hammad_%284%29.jpg', 'caption' => 'Vestiges du minaret de la grande mosquée'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8f/Maquette_de_Kalaa_de_Beni_Hammad.jpg/500px-Maquette_de_Kalaa_de_Beni_Hammad.jpg', 'caption' => 'Maquette de la cité reconstituée'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Kalâa des Béni Hammad',
                        'description' => 'Première capitale des Hammadides, forteresse et cité royale du XIe siècle nichée dans les monts du Hodna.',
                        'history' => "La Kalâa des Béni Hammad est fondée en 1007 par Hammad ibn Buluggin, un prince de la dynastie ziride qui rompt avec ses suzerains de Kairouan en 1015 pour affirmer son indépendance et fonder sa propre lignée, les Hammadides. Bâtie en position défensive dans les monts du Hodna, la cité devient la première capitale de cette dynastie berbère qui règne, à son apogée, sur une grande partie du Maghreb central.\n\nVers le milieu du XIe siècle, la Kalâa connaît son âge d'or : elle se dote de somptueux palais, de jardins et surtout d'une grande mosquée qui, avec ses treize nefs et son minaret de plus de vingt mètres, est considérée comme la plus vaste construite en Afrique du Nord avant le XXe siècle. La ville devient également un centre intellectuel et artisanal renommé, attirant savants et artistes de tout le Maghreb.\n\nCette prospérité est cependant de courte durée : dès 1090, la menace grandissante des invasions des tribus arabes Banu Hilal pousse les Hammadides à abandonner leur capitale au profit de la ville côtière, mieux protégée, de Béjaïa. Le coup de grâce est porté en 1152, lorsque les Almohades détruisent partiellement ce qui reste de la cité. Abandonnée depuis lors, la Kalâa échappe à toute reconstruction ultérieure, ce qui préserve remarquablement ses vestiges jusqu'à leur redécouverte scientifique, avec les premières fouilles archéologiques françaises à partir de 1908. Le site est inscrit au patrimoine mondial de l'UNESCO en 1980.",
                        'visit_info' => "Le site, assez isolé, se visite à pied sur plusieurs hectares de vestiges. Le minaret de la grande mosquée, haut d'une vingtaine de mètres, est l'élément le mieux conservé. Prévoyez de bonnes chaussures de marche.",
                    ],
                    'ar' => [
                        'name' => 'قلعة بني حماد',
                        'description' => 'أول عاصمة للحماديين، قلعة ومدينة ملكية من القرن الحادي عشر تقع في جبال الحضنة.',
                        'history' => "أسس حماد بن بلكين، أمير من الأسرة الزيرية، قلعة بني حماد سنة 1007، قبل أن يقطع علاقته بأسياده في القيروان سنة 1015 ليؤكد استقلاله ويؤسس سلالته الخاصة، الحماديين. بُنيت المدينة في موقع دفاعي بجبال الحضنة، فأصبحت أول عاصمة لهذه الدولة الأمازيغية التي حكمت في أوج قوتها جزءا كبيرا من المغرب الأوسط.\n\nنحو منتصف القرن الحادي عشر، عاشت القلعة عصرها الذهبي: تزينت بقصور فخمة وحدائق، وقبل كل شيء بجامع كبير يُعد، بأروقته الثلاثة عشر ومئذنته التي يتجاوز ارتفاعها عشرين مترا، أكبر مسجد بُني في شمال إفريقيا قبل القرن العشرين. وأصبحت المدينة أيضا مركزا فكريا وحرفيا مرموقا، استقطب العلماء والفنانين من كامل المغرب.\n\nلكن هذا الازدهار لم يدم طويلا: فمنذ 1090، دفع التهديد المتنامي لغزوات قبائل بني هلال العربية الحماديين إلى هجر عاصمتهم لصالح مدينة بجاية الساحلية الأكثر حماية. وجاءت الضربة القاضية سنة 1152، حين دمر الموحدون ما تبقى من المدينة جزئيا. ومنذ هجرها، لم تشهد القلعة أي إعادة بناء لاحقة، ما حفظ آثارها بشكل استثنائي إلى حين إعادة اكتشافها علميا، مع أولى الحفريات الأثرية الفرنسية ابتداء من 1908. وأُدرج الموقع في قائمة التراث العالمي لليونسكو سنة 1980.",
                        'visit_info' => 'الموقع معزول نسبيا، وتتم زيارته سيرا على الأقدام عبر هكتارات من الآثار. تُعد مئذنة الجامع الكبير، التي يبلغ ارتفاعها نحو عشرين مترا، العنصر الأفضل حفظا. احرص على ارتداء حذاء مشي مناسب.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1007, 'fr' => ['period_label' => '1007', 'title' => 'Fondation de la Kalâa', 'description' => 'Hammad ibn Buluggin fonde la cité comme capitale de la dynastie hammadide.'], 'ar' => ['period_label' => '1007', 'title' => 'تأسيس القلعة', 'description' => 'أسس حماد بن بلكين المدينة عاصمة للدولة الحمادية.']],
                    ['year' => 1015, 'fr' => ['period_label' => '1015', 'title' => 'Indépendance vis-à-vis des Zirides', 'description' => 'Hammad ibn Buluggin rompt avec la dynastie ziride de Kairouan et affirme son autonomie politique.'], 'ar' => ['period_label' => '1015', 'title' => 'الاستقلال عن الزيريين', 'description' => 'قطع حماد بن بلكين علاقته بالدولة الزيرية في القيروان وأكد استقلاله السياسي.']],
                    ['year' => 1050, 'fr' => ['period_label' => 'Milieu du XIe siècle', 'title' => 'Apogée architecturale et savante', 'description' => 'La ville prospère comme centre politique et intellectuel, dotée de palais et de la grande mosquée.'], 'ar' => ['period_label' => 'منتصف القرن الحادي عشر', 'title' => 'أوج الازدهار المعماري والعلمي', 'description' => 'ازدهرت المدينة كمركز سياسي وفكري، وضمت قصورا والجامع الكبير.']],
                    ['year' => 1090, 'fr' => ['period_label' => '1090', 'title' => 'Abandon face aux Banu Hilal', 'description' => 'Menacée par les invasions des tribus Banu Hilal, la capitale est abandonnée au profit de Béjaïa.'], 'ar' => ['period_label' => '1090', 'title' => 'الهجر أمام بني هلال', 'description' => 'هُجرت العاصمة تحت تهديد غزوات قبائل بني هلال لصالح بجاية.']],
                    ['year' => 1152, 'fr' => ['period_label' => '1152', 'title' => 'Destruction almohade', 'description' => 'Les Almohades détruisent partiellement la cité, accélérant son déclin définitif.'], 'ar' => ['period_label' => '1152', 'title' => 'التدمير الموحدي', 'description' => 'دمر الموحدون المدينة جزئيا، ما عجل بانهيارها النهائي.']],
                    ['year' => 1908, 'fr' => ['period_label' => '1908', 'title' => 'Premières fouilles archéologiques', 'description' => 'Des fouilles françaises commencent à documenter systématiquement les vestiges de la cité.'], 'ar' => ['period_label' => '1908', 'title' => 'أولى الحفريات الأثرية', 'description' => 'بدأت حفريات فرنسية في توثيق آثار المدينة بشكل منهجي.']],
                    ['year' => 1980, 'fr' => ['period_label' => '1980', 'title' => "Inscription à l'UNESCO", 'description' => 'Le site est inscrit au patrimoine mondial de l\'UNESCO.'], 'ar' => ['period_label' => '1980', 'title' => 'التسجيل في اليونسكو', 'description' => 'أُدرج الموقع في قائمة التراث العالمي لليونسكو.']],
                ],
            ],
            [
                'slug' => 'vallee-du-mzab',
                'category' => 'casbah',
                'wilaya' => 'Ghardaïa',
                'latitude' => 32.4833,
                'longitude' => 3.6667,
                'opening_hours' => 'Accès libre aux villes ; Beni Isguen avec guide obligatoire — à titre indicatif',
                'entry_fee' => 'Gratuit (guide payant pour Beni Isguen) — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/Panoramic_view_of_Ksar_Gharda%C3%AFa%2C_Algeria_2006.jpg/500px-Panoramic_view_of_Ksar_Gharda%C3%AFa%2C_Algeria_2006.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1e/Ksar_Beni_Isguen_1.jpg/1280px-Ksar_Beni_Isguen_1.jpg', 'caption' => 'Vue du ksar de Beni Isguen'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1e/Ksar_Beni_Isguen_1.jpg/500px-Ksar_Beni_Isguen_1.jpg', 'caption' => 'Le ksar de Beni Isguen'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Mzab_Gharda%C3%AFa.jpg/500px-Mzab_Gharda%C3%AFa.jpg', 'caption' => 'Le ksar de Bounoura'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => "Vallée du M'Zab",
                        'description' => "Vallée aux cinq ksour fortifiés, chef-d'œuvre d'architecture ibadite parfaitement adapté au désert.",
                        'history' => "La vallée du M'Zab est peuplée à partir du XIe siècle par les Ibadites, une branche minoritaire et rigoriste de l'islam, distincte du sunnisme et du chiisme majoritaires, qui trouvent refuge dans cette région désertique après avoir été chassés d'autres régions du Maghreb à la suite de persécutions religieuses. Sur ce territoire aride, ils fondent progressivement cinq cités fortifiées — El Atteuf en 1012, la plus ancienne, puis Bounoura, Ghardaïa en 1053, Melika, et enfin Beni Isguen en 1347, réputée pour son conservatisme religieux particulièrement strict.\n\nChacun de ces ksour est organisé selon un principe urbain remarquable, hérité directement des préceptes religieux ibadites : les maisons s'étagent en cercles concentriques autour de la mosquée centrale, au sommet d'une colline, l'habitat le plus modeste occupant la périphérie et les demeures plus cossues se rapprochant du centre religieux. Ce plan permet également une gestion remarquablement efficace de l'eau, ressource rare, via un système sophistiqué de puits et de canaux d'irrigation qui alimente les palmeraies environnantes.\n\nLa région conserve une large autonomie politique et religieuse jusqu'à la colonisation française, qui impose à partir de 1882 une présence administrative et militaire dans la vallée. Aujourd'hui encore, la société mozabite conserve des institutions propres qui régulent la vie religieuse et sociale des cités. La vallée du M'Zab, inscrite au patrimoine mondial de l'UNESCO en 1982, est unanimement saluée par les architectes et urbanistes modernes, dont Le Corbusier, comme un modèle d'urbanisme vernaculaire parfaitement adapté à son environnement désertique.",
                        'visit_info' => "La visite de Beni Isguen, la ville la plus conservatrice, se fait uniquement accompagnée d'un guide agréé. Les marchés du soir et les palmeraies environnantes valent également le détour.",
                    ],
                    'ar' => [
                        'name' => 'وادي ميزاب',
                        'description' => 'واد يضم خمس قصور محصنة، تحفة معمارية إباضية متكيفة تماما مع الصحراء.',
                        'history' => "استوطن وادي ميزاب ابتداء من القرن الحادي عشر الإباضيون، وهو فرع أقلية متشدد من الإسلام، متمايز عن السنة والشيعة الأغلبية، لجأوا إلى هذه المنطقة الصحراوية بعد أن طُردوا من مناطق أخرى بالمغرب إثر اضطهاد ديني. وفي هذا الإقليم القاحل، أسسوا تدريجيا خمس مدن محصنة: العطف سنة 1012، الأقدم بينها، ثم بونورة وغرداية سنة 1053 ومليكة، وأخيرا بني يزقن سنة 1347، المعروفة بمحافظتها الدينية الصارمة بشكل خاص.\n\nنُظم كل قصر من هذه القصور وفق مبدأ عمراني لافت، موروث مباشرة عن التعاليم الدينية الإباضية: تتدرج المنازل في دوائر متحدة المركز حول المسجد المركزي، فوق قمة تلة، بحيث يشغل السكن الأكثر تواضعا الأطراف وتقترب المنازل الأكثر بحبوحة من المركز الديني. ويسمح هذا المخطط أيضا بإدارة بالغة الفعالية للمياه، وهي مورد نادر، عبر نظام محكم من الآبار وقنوات الري التي تغذي بساتين النخيل المحيطة.\n\nحافظت المنطقة على استقلالية سياسية ودينية واسعة حتى الاستعمار الفرنسي، الذي فرض ابتداء من 1882 حضورا إداريا وعسكريا في الوادي. وحتى اليوم، تحافظ المجتمعات الميزابية على مؤسساتها الخاصة التي تنظم الحياة الدينية والاجتماعية للمدن. ووادي ميزاب، المسجل في قائمة التراث العالمي لليونسكو سنة 1982، يحظى بإجماع المعماريين والمخططين العمرانيين المعاصرين، ومنهم لوكوربوزييه، باعتباره نموذجا للعمران المحلي المتكيف تماما مع بيئته الصحراوية.",
                        'visit_info' => 'لا تتم زيارة بني يزقن، المدينة الأكثر محافظة، إلا برفقة مرشد معتمد. تستحق الأسواق المسائية وبساتين النخيل المحيطة الزيارة أيضا.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1012, 'fr' => ['period_label' => '1012', 'title' => "Fondation d'El Atteuf", 'description' => "El Atteuf, le plus ancien des cinq ksour, est fondé par les premiers réfugiés ibadites."], 'ar' => ['period_label' => '1012', 'title' => 'تأسيس العطف', 'description' => 'أسس اللاجئون الإباضيون الأوائل قصر العطف، أقدم القصور الخمسة.']],
                    ['year' => 1053, 'fr' => ['period_label' => '1053', 'title' => 'Fondation de Ghardaïa', 'description' => 'Ghardaïa, la plus grande des cinq cités, est fondée.'], 'ar' => ['period_label' => '1053', 'title' => 'تأسيس غرداية', 'description' => 'تأسست غرداية، أكبر المدن الخمس.']],
                    ['year' => 1347, 'fr' => ['period_label' => '1347', 'title' => 'Fondation de Beni Isguen', 'description' => 'Beni Isguen, réputée pour son conservatisme religieux, est fondée en dernier parmi les cinq cités.'], 'ar' => ['period_label' => '1347', 'title' => 'تأسيس بني يزقن', 'description' => 'تأسست بني يزقن، المعروفة بمحافظتها الدينية، آخر المدن الخمس.']],
                    ['year' => 1882, 'fr' => ['period_label' => '1882', 'title' => 'Colonisation française', 'description' => 'La région passe sous administration française, qui impose une présence militaire dans la vallée.'], 'ar' => ['period_label' => '1882', 'title' => 'الاستعمار الفرنسي', 'description' => 'خضعت المنطقة للإدارة الفرنسية التي فرضت وجودا عسكريا في الوادي.']],
                    ['year' => 1982, 'fr' => ['period_label' => '1982', 'title' => "Inscription à l'UNESCO", 'description' => 'La vallée du M\'Zab est inscrite au patrimoine mondial pour son urbanisme exceptionnel.'], 'ar' => ['period_label' => '1982', 'title' => 'التسجيل في اليونسكو', 'description' => 'أُدرج وادي ميزاب في قائمة التراث العالمي لتخطيطه العمراني الاستثنائي.']],
                ],
            ],
            [
                'slug' => 'grande-mosquee-tlemcen',
                'category' => 'religieux',
                'wilaya' => 'Tlemcen',
                'latitude' => 34.8828,
                'longitude' => -1.3167,
                'opening_hours' => 'Horaires de prière ; visite hors offices sur demande — à titre indicatif',
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/94/Grande_mosqu%C3%A9e_et_d%C3%A9pendance_Minaret_de_la_Mosqu%C3%A9e_003.jpg/500px-Grande_mosqu%C3%A9e_et_d%C3%A9pendance_Minaret_de_la_Mosqu%C3%A9e_003.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1d/Grande_mosquee_Tlemcen_%28angle%29.jpg/1280px-Grande_mosquee_Tlemcen_%28angle%29.jpg', 'caption' => 'Vue d\'angle de la Grande Mosquée'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/Grande_mosqu%C3%A9e_et_d%C3%A9pendance_Minaret_de_la_Mosqu%C3%A9e_014.jpg/500px-Grande_mosqu%C3%A9e_et_d%C3%A9pendance_Minaret_de_la_Mosqu%C3%A9e_014.jpg', 'caption' => 'Cour et minaret (1236)'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/L%27espace_royal_devant_le_mihrab_de_la_Grande_Mosqu%C3%A9e_de_Tlemcen.jpg/500px-L%27espace_royal_devant_le_mihrab_de_la_Grande_Mosqu%C3%A9e_de_Tlemcen.jpg', 'caption' => 'Le mihrab richement décoré'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Grande Mosquée de Tlemcen',
                        'description' => "Mosquée almoravide de 1082, l'une des plus anciennes et des plus richement décorées d'Algérie.",
                        'history' => "La Grande Mosquée de Tlemcen est fondée en 1082 par les Almoravides, dynastie berbère originaire du Sahara qui vient alors de s'emparer de la ville et cherche à y affirmer sa légitimité religieuse par un édifice à la hauteur de son ambition impériale. La salle de prière, organisée en onze nefs perpendiculaires au mur du fond, adopte un plan hypostyle hérité de la grande tradition des mosquées maghrébines et andalouses.\n\nEn 1136, la dynastie almohade, qui a entre-temps supplanté les Almoravides, fait agrandir l'édifice et enrichir considérablement son décor : c'est de cette période que date la célèbre coupole à muqarnas qui surplombe le mihrab, un chef-d'œuvre de stuc sculpté considéré comme l'un des sommets de l'art architectural almohade, aux côtés de la Koutoubia de Marrakech ou de la Giralda de Séville. La mosquée s'enrichit également, autour de 1145, d'un minbar en bois finement sculpté, aujourd'hui compté parmi les plus anciens conservés au monde.\n\nSous la dynastie zianide, qui fait de Tlemcen sa capitale à partir du XIIIe siècle, l'édifice continue d'être embelli : le minaret carré actuel est ajouté en 1236. La mosquée traverse ensuite les siècles sans transformation majeure, restant un lieu de culte actif jusqu'à aujourd'hui. En reconnaissance de la valeur exceptionnelle de son patrimoine monumental, l'ensemble formé par Tlemcen et ses monuments historiques, dont la Grande Mosquée, figure depuis 2002 sur la liste indicative du patrimoine mondial de l'UNESCO.",
                        'visit_info' => "L'accès à la salle de prière peut être restreint aux non-musulmans en dehors de certains horaires ; se renseigner sur place. Le minaret, ajouté en 1236, est visible depuis l'extérieur.",
                    ],
                    'ar' => [
                        'name' => 'الجامع الكبير لتلمسان',
                        'description' => 'مسجد مرابطي بُني سنة 1082، من أقدم المساجد وأغناها زخرفة في الجزائر.',
                        'history' => "أسس المرابطون، وهم دولة أمازيغية من أصل صحراوي كانت قد استولت للتو على المدينة، الجامع الكبير لتلمسان سنة 1082، سعيا لتأكيد شرعيتهم الدينية بمبنى يليق بطموحهم الإمبراطوري. تعتمد قاعة الصلاة، المنظمة في إحدى عشرة رواقا عمودية على الجدار الخلفي، مخططا ذا أعمدة موروثا عن التقليد العريق لمساجد المغرب والأندلس.\n\nسنة 1136، عمل الموحدون، الذين حلوا محل المرابطين، على توسيع المبنى وإثراء زخرفته بشكل كبير: من تلك الحقبة تعود القبة الشهيرة ذات المقرنصات التي تعلو المحراب، وهي تحفة من الجص المنحوت تُعد من أبرز قمم الفن المعماري الموحدي، إلى جانب كتبية مراكش وخيرالدا إشبيلية. كما أُهدي للجامع، نحو 1145، منبر خشبي منحوت بدقة متناهية، يُعد اليوم من بين أقدم المنابر المحفوظة في العالم.\n\nفي عهد الدولة الزيانية، التي اتخذت من تلمسان عاصمة لها ابتداء من القرن الثالث عشر، استمر تجميل المبنى: أُضيفت المئذنة المربعة الحالية سنة 1236. عبر الجامع بعدها القرون دون تحول كبير، وبقي مكان عبادة نشطا حتى اليوم. وتقديرا للقيمة الاستثنائية لتراثه المعماري، أُدرجت مدينة تلمسان ومعالمها التاريخية، ومنها الجامع الكبير، منذ 2002 في القائمة الإرشادية للتراث العالمي لليونسكو.",
                        'visit_info' => 'قد يكون الدخول إلى قاعة الصلاة مقيدا لغير المسلمين خارج أوقات معينة، يُنصح بالاستعلام في عين المكان. تُرى المئذنة، التي أُضيفت سنة 1236، من الخارج.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1082, 'fr' => ['period_label' => '1082', 'title' => 'Fondation almoravide', 'description' => 'Les Almoravides fondent la mosquée peu après leur prise de Tlemcen.'], 'ar' => ['period_label' => '1082', 'title' => 'التأسيس المرابطي', 'description' => 'أسس المرابطون المسجد بُعيد سيطرتهم على تلمسان.']],
                    ['year' => 1136, 'fr' => ['period_label' => '1136', 'title' => 'Agrandissement almohade', 'description' => 'Les Almohades agrandissent l\'édifice et enrichissent son décor.'], 'ar' => ['period_label' => '1136', 'title' => 'التوسيع الموحدي', 'description' => 'وسع الموحدون المبنى وأثروا زخرفته.']],
                    ['year' => 1145, 'fr' => ['period_label' => '1145', 'title' => 'Le minbar almoravide', 'description' => "Un minbar en bois sculpté, aujourd'hui considéré comme l'un des plus anciens conservés au monde, est offert à la mosquée."], 'ar' => ['period_label' => '1145', 'title' => 'المنبر المرابطي', 'description' => 'أُهدي للمسجد منبر خشبي منحوت، يُعد اليوم من أقدم المنابر المحفوظة في العالم.']],
                    ['year' => 1236, 'fr' => ['period_label' => '1236', 'title' => 'Ajout du minaret zianide', 'description' => 'La dynastie zianide fait construire le minaret actuel.'], 'ar' => ['period_label' => '1236', 'title' => 'إضافة المئذنة الزيانية', 'description' => 'شيدت الدولة الزيانية المئذنة الحالية.']],
                    ['year' => 2002, 'fr' => ['period_label' => '2002', 'title' => 'Liste indicative de l\'UNESCO', 'description' => 'Tlemcen et ses monuments, dont la Grande Mosquée, sont proposés au patrimoine mondial de l\'UNESCO.'], 'ar' => ['period_label' => '2002', 'title' => 'الإدراج في القائمة الإرشادية لليونسكو', 'description' => 'اقتُرحت تلمسان ومعالمها، ومنها الجامع الكبير، للتسجيل في قائمة التراث العالمي لليونسكو.']],
                ],
            ],
            [
                'slug' => 'chrea',
                'category' => 'naturel',
                'wilaya' => 'Blida',
                'latitude' => 36.4333,
                'longitude' => 2.8667,
                'opening_hours' => "Ouvert toute l'année, station de ski selon enneigement — à titre indicatif",
                'entry_fee' => 'Gratuit (accès parc) — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a0/Montagne_de_Chr%C3%A9a%2C.jpg/500px-Montagne_de_Chr%C3%A9a%2C.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/da/Chrea_National_Park.jpg/1280px-Chrea_National_Park.jpg', 'caption' => 'Paysage du parc national de Chréa'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/Waterfall_Chrea.jpg/1280px-Waterfall_Chrea.jpg', 'caption' => 'Cascade dans le massif de Chréa'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/Chrea_blida_algerie.jpg/500px-Chrea_blida_algerie.jpg', 'caption' => 'Le massif de Chréa'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Chréa',
                        'description' => 'Massif montagneux du Blidéen, forêt de cèdres de l\'Atlas et station de ski la plus proche d\'Alger.',
                        'history' => "Chréa est un massif montagneux appartenant à l'Atlas blidéen, à moins de 50 kilomètres au sud d'Alger, dont le point culminant dépasse 1600 mètres d'altitude. Sa proximité immédiate avec la capitale en a fait, dès l'époque coloniale, une destination prisée pour échapper à la chaleur estivale du littoral et profiter, l'hiver, de l'un des rares massifs d'Algérie du Nord suffisamment enneigés pour la pratique du ski.\n\nLe massif est surtout connu pour abriter l'une des dernières grandes forêts de cèdres de l'Atlas du pays, un arbre majestueux aujourd'hui menacé par le réchauffement climatique et la raréfaction des précipitations. Cette richesse forestière, associée à une faune remarquable comprenant notamment le singe magot, une espèce endémique d'Afrique du Nord aujourd'hui vulnérable, justifie le classement du massif en parc national dès 1925 — l'un des plus anciens du pays — puis son extension et sa réorganisation en 1983 afin de renforcer la protection de ses écosystèmes.\n\nUne station de ski se développe sur les pentes du massif au cours du XXe siècle, devenant la plus accessible depuis la capitale et un lieu de sortie familiale très populaire, malgré un enneigement de plus en plus irrégulier ces dernières décennies. En dehors de la saison hivernale, Chréa demeure un site de randonnée apprécié, offrant depuis ses crêtes des vues spectaculaires sur la plaine agricole de la Mitidja et, par temps clair, jusqu'à la baie d'Alger.",
                        'visit_info' => "Chréa abrite la station de ski la plus proche de la capitale, ouverte l'hiver selon l'enneigement. En dehors de la saison de ski, le parc se prête à la randonnée, avec de belles vues sur la plaine de la Mitidja.",
                    ],
                    'ar' => [
                        'name' => 'شريعة',
                        'description' => 'سلسلة جبلية في الأطلس البليدي، غابة أرز الأطلس، وأقرب محطة تزلج إلى الجزائر العاصمة.',
                        'history' => "شريعة سلسلة جبلية تابعة للأطلس البليدي، على بعد أقل من 50 كيلومترا جنوب الجزائر العاصمة، تتجاوز أعلى قممها 1600 متر. جعل قربها المباشر من العاصمة منها، منذ العهد الاستعماري، وجهة مرغوبة للهروب من حرارة الساحل صيفا والاستمتاع شتاء بواحدة من السلاسل الجبلية النادرة في شمال الجزائر التي تتساقط عليها ثلوج كافية لممارسة التزلج.\n\nتشتهر السلسلة الجبلية بشكل خاص باحتضانها إحدى آخر الغابات الكبرى لأرز الأطلس في البلاد، وهي شجرة مهيبة باتت اليوم مهددة بالاحتباس الحراري وندرة الأمطار. وهذه الثروة الغابية، المقترنة بحيوانات لافتة من بينها قرد المكاك البربري، وهو نوع مستوطن في شمال إفريقيا بات اليوم مهددا، بررت تصنيف السلسلة الجبلية متنزها وطنيا منذ 1925 - من بين أقدم المتنزهات في البلاد - ثم توسيعه وإعادة تنظيمه سنة 1983 لتعزيز حماية أنظمته البيئية.\n\nتطورت محطة للتزلج على منحدرات السلسلة الجبلية خلال القرن العشرين، لتصبح الأقرب انطلاقا من العاصمة ووجهة خروج عائلية شائعة جدا، رغم تساقط ثلوج بات غير منتظم بشكل متزايد في العقود الأخيرة. وخارج الموسم الشتوي، تبقى شريعة موقع تنزه مفضلا، تقدم من قممها إطلالات خلابة على سهل متيجة الفلاحي، وحتى على خليج الجزائر في الأيام الصافية.",
                        'visit_info' => 'تضم شريعة أقرب محطة تزلج إلى العاصمة، تفتح في الشتاء حسب تساقط الثلوج. خارج موسم التزلج، يصلح المتنزه لرحلات المشي، مع إطلالات جميلة على سهل متيجة.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1925, 'fr' => ['period_label' => '1925', 'title' => 'Création du parc national', 'description' => 'Chréa est classé parc national pour protéger sa forêt de cèdres et sa faune.'], 'ar' => ['period_label' => '1925', 'title' => 'إنشاء المتنزه الوطني', 'description' => 'صُنفت شريعة متنزها وطنيا لحماية غابة الأرز والحيوانات فيها.']],
                    ['year' => 1945, 'fr' => ['period_label' => 'Milieu du XXe siècle', 'title' => 'Développement de la station de ski', 'description' => 'Une station de ski se développe sur les pentes du massif, devenant la plus accessible depuis Alger.'], 'ar' => ['period_label' => 'منتصف القرن العشرين', 'title' => 'تطور محطة التزلج', 'description' => 'تطورت محطة للتزلج على منحدرات السلسلة الجبلية، لتصبح الأقرب انطلاقا من الجزائر العاصمة.']],
                    ['year' => 1983, 'fr' => ['period_label' => '1983', 'title' => 'Extension et reclassement', 'description' => 'Le parc est réorganisé et étendu pour renforcer la protection de ses écosystèmes.'], 'ar' => ['period_label' => '1983', 'title' => 'التوسيع وإعادة التصنيف', 'description' => 'أعيد تنظيم المتنزه وتوسيعه لتعزيز حماية أنظمته البيئية.']],
                ],
            ],
            [
                'slug' => 'tipaza',
                'category' => 'romain',
                'wilaya' => 'Tipaza',
                'latitude' => 36.5936,
                'longitude' => 2.4481,
                'opening_hours' => '08h00 - 17h30, tous les jours — à titre indicatif',
                'entry_fee' => '200 DA — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b6/View_of_Tipasa_01.jpg/500px-View_of_Tipasa_01.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/18/Roman_Ruins_of_Tipaza.jpg/1280px-Roman_Ruins_of_Tipaza.jpg', 'caption' => 'Ruines romaines de Tipaza en bord de mer'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e6/Large_Christian_Basilica_%28Tipasa%29_02.jpg/500px-Large_Christian_Basilica_%28Tipasa%29_02.jpg', 'caption' => 'La grande basilique chrétienne'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4e/Amphitheatre_%28Tipasa%29_01.jpg/500px-Amphitheatre_%28Tipasa%29_01.jpg', 'caption' => "L'amphithéâtre romain"],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Tipaza',
                        'description' => 'Cité punique puis romaine au bord de la Méditerranée, célèbre pour ses ruines antiques disséminées entre mer et collines.',
                        'history' => "Tipasa (Tipaza) est fondée par les Phéniciens comme comptoir commercial avant d'être annexée par Rome au Ier siècle, qui en fait une base stratégique pour la conquête de la Maurétanie. La ville devient une cité prospère, dotée d'un forum, de thermes, d'un amphithéâtre et de plusieurs basiliques paléochrétiennes, dont la grande basilique Sainte-Salsa, témoignant de la ferveur chrétienne précoce de la région.\n\nÀ proximité se dresse le Tombeau de la Chrétienne, mausolée royal circulaire monumental construit au Ier siècle avant notre ère pour la dynastie maurétanienne de Juba II et Cléopâtre Séléné, fille de Cléopâtre VII d'Égypte — un rare témoignage architectural de ce royaume client de Rome.\n\nTipasa décline avec les invasions vandales puis byzantines, avant d'être largement abandonnée. Ses ruines, disséminées dans un cadre naturel exceptionnel entre mer et collines, ont inspiré l'écrivain Albert Camus, qui y situe plusieurs de ses essais. Le site est inscrit au patrimoine mondial de l'UNESCO en 1982.",
                        'visit_info' => "Comptez 2 heures pour parcourir le site archéologique en bord de mer. Le Tombeau de la Chrétienne, à quelques kilomètres, mérite une visite séparée. Le cadre est particulièrement agréable au coucher du soleil.",
                    ],
                    'ar' => [
                        'name' => 'تيبازة',
                        'description' => 'مدينة بونيقية ثم رومانية على ضفاف البحر الأبيض المتوسط، تشتهر بآثارها القديمة المنتشرة بين البحر والتلال.',
                        'history' => "تأسست تيبازة على يد الفينيقيين كمركز تجاري قبل أن يضمها الرومان في القرن الأول الميلادي، فجعلوا منها قاعدة استراتيجية لغزو موريتانيا. أصبحت المدينة مزدهرة، بمنتدى وحمامات ومدرج وعدة كنائس مسيحية مبكرة، من بينها كنيسة القديسة سالسا الكبرى، شاهدة على الحماس المسيحي المبكر في المنطقة.\n\nيقع بالقرب منها ضريح المرأة المسيحية، وهو مقبرة ملكية دائرية ضخمة بُنيت في القرن الأول قبل الميلاد لأسرة يوبا الثاني وكليوباترا سيليني، ابنة كليوباترا السابعة المصرية - شاهد معماري نادر على هذه المملكة التابعة لروما.\n\nتراجعت تيبازة مع غزوات الوندال ثم البيزنطيين، قبل أن تُهجر إلى حد كبير. ألهمت آثارها، المنتشرة في إطار طبيعي استثنائي بين البحر والتلال، الكاتب ألبير كامو الذي خصص لها العديد من مقالاته. أُدرج الموقع في قائمة التراث العالمي لليونسكو سنة 1982.",
                        'visit_info' => 'خصص ساعتين لزيارة الموقع الأثري على شاطئ البحر. يستحق ضريح المرأة المسيحية، على بعد بضعة كيلومترات، زيارة منفصلة. الإطار جميل بشكل خاص عند غروب الشمس.',
                    ],
                ],
                'timeline' => [
                    ['year' => -400, 'fr' => ['period_label' => 'Ve siècle av. J.-C.', 'title' => 'Comptoir phénicien', 'description' => 'Des marchands phéniciens fondent un comptoir sur cette baie propice au mouillage.'], 'ar' => ['period_label' => 'القرن الخامس ق.م', 'title' => 'مركز تجاري فينيقي', 'description' => 'أسس تجار فينيقيون مركزا تجاريا في هذا الخليج الملائم للرسو.']],
                    ['year' => 40, 'fr' => ['period_label' => '40 apr. J.-C.', 'title' => 'Annexion romaine', 'description' => 'Rome annexe la Maurétanie et fait de Tipasa une place forte stratégique sur la côte.'], 'ar' => ['period_label' => '40م', 'title' => 'الضم الروماني', 'description' => 'ضمت روما موريتانيا وجعلت من تيبازة موقعا استراتيجيا حصينا على الساحل.']],
                    ['year' => 200, 'fr' => ['period_label' => 'IIe - IIIe siècle', 'title' => 'Apogée urbain', 'description' => 'La ville se dote de monuments publics et devient un port actif du commerce méditerranéen.'], 'ar' => ['period_label' => 'القرنان الثاني والثالث', 'title' => 'أوج الازدهار الحضري', 'description' => 'تزينت المدينة بمبان عمومية وأصبحت ميناء نشطا للتجارة المتوسطية.']],
                    ['year' => 429, 'fr' => ['period_label' => '429', 'title' => 'Invasions vandales', 'description' => 'Les Vandales envahissent l\'Afrique du Nord, précipitant le déclin de la cité.'], 'ar' => ['period_label' => '429', 'title' => 'غزوات الوندال', 'description' => 'غزا الوندال شمال إفريقيا، ما عجل بتراجع المدينة.']],
                    ['year' => 1982, 'fr' => ['period_label' => '1982', 'title' => "Inscription à l'UNESCO", 'description' => "Tipasa est inscrite au patrimoine mondial de l'UNESCO."], 'ar' => ['period_label' => '1982', 'title' => 'التسجيل في اليونسكو', 'description' => 'أُدرجت تيبازة في قائمة التراث العالمي لليونسكو.']],
                ],
            ],
            [
                'slug' => 'thagaste-souk-ahras',
                'category' => 'romain',
                'wilaya' => 'Souk Ahras',
                'latitude' => 36.2861,
                'longitude' => 7.9511,
                'opening_hours' => '08h00 - 16h00 — à titre indicatif',
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/7f/Roman_ruins_of_Saint_Augustin_%2C_Souk_Ahras_%28Algeria%29_%D8%A7%D9%84%D8%A2%D8%AB%D8%A7%D8%B1_%D8%A7%D9%84%D8%B1%D9%88%D9%85%D8%A7%D9%86%D9%8A%D8%A9_%D9%84%D9%84%D9%82%D8%AF%D9%8A%D8%B3_%D8%A3%D9%88%D8%BA%D8%B3%D8%B7%D9%8A%D9%86_%D8%A8%D9%88%D9%84%D8%A7%D9%8A%D8%A9_%D8%B3%D9%88%D9%82_%D8%A3%D9%87%D8%B1%D8%A7%D8%B3_%D8%8C_%D8%A7%D9%84%D8%AC%D8%B2%D8%A7%D8%A6%D8%B1.jpg/500px-Roman_ruins_of_Saint_Augustin_%2C_Souk_Ahras_%28Algeria%29_%D8%A7%D9%84%D8%A2%D8%AB%D8%A7%D8%B1_%D8%A7%D9%84%D8%B1%D9%88%D9%85%D8%A7%D9%86%D9%8A%D8%A9_%D9%84%D9%84%D9%82%D8%AF%D9%8A%D8%B3_%D8%A3%D9%88%D8%BA%D8%B3%D8%B7%D9%8A%D9%86_%D8%A8%D9%88%D9%84%D8%A7%D9%8A%D8%A9_%D8%B3%D9%88%D9%82_%D8%A3%D9%87%D8%B1%D8%A7%D8%B3_%D8%8C_%D8%A7%D9%84%D8%AC%D8%B2%D8%A7%D8%A6%D8%B1.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/b/b3/Roman_ruins_of_Saint_Augustin_from_Souk_Ahras_Algeria._%D8%A7%D9%84%D8%A2%D8%AB%D8%A7%D8%B1_%D8%A7%D9%84%D8%B1%D9%88%D9%85%D8%A7%D9%86%D9%8A%D8%A9_%D9%84%D9%84%D9%82%D8%AF%D9%8A%D8%B3_%D8%A3%D9%88%D8%BA%D8%B3%D8%B7%D9%8A%D9%86_%D8%A8%D9%88%D9%84%D8%A7%D9%8A%D8%A9_%D8%B3%D9%88%D9%82_%D8%A3%D9%87%D8%B1%D8%A7%D8%B3_%D8%8C_%D8%A7%D9%84%D8%AC%D8%B2%D8%A7%D8%A6%D8%B1.jpg', 'caption' => 'Ruines romaines liées à saint Augustin'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3a/Souk-Ahras.%C3%89glise_Saint-Augustin.jpg/1280px-Souk-Ahras.%C3%89glise_Saint-Augustin.jpg', 'caption' => 'Église Saint-Augustin de Souk Ahras'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/Roman_ruins_of_Saint_Augustin_Souk_Ahras_%28Algeria%29_%D8%A7%D9%84%D8%A2%D8%AB%D8%A7%D8%B1_%D8%A7%D9%84%D8%B1%D9%88%D9%85%D8%A7%D9%86%D9%8A%D8%A9_%D9%84%D9%84%D9%82%D8%AF%D9%8A%D8%B3_%D8%A3%D9%88%D8%BA%D8%B3%D8%B7%D9%8A%D9%86_%D8%A8%D9%88%D9%84%D8%A7%D9%8A%D8%A9_%D8%B3%D9%88%D9%82_%D8%A3%D9%87%D8%B1%D8%A7%D8%B3_%D8%8C_%D8%A7%D9%84%D8%AC%D8%B2%D8%A7%D8%A6%D8%B1.jpg/500px-Roman_ruins_of_Saint_Augustin_Souk_Ahras_%28Algeria%29_%D8%A7%D9%84%D8%A2%D8%AB%D8%A7%D8%B1_%D8%A7%D9%84%D8%B1%D9%88%D9%85%D8%A7%D9%86%D9%8A%D8%A9_%D9%84%D9%84%D9%82%D8%AF%D9%8A%D8%B3_%D8%A3%D9%88%D8%BA%D8%B3%D8%B7%D9%8A%D9%86_%D8%A8%D9%88%D9%84%D8%A7%D9%8A%D8%A9_%D8%B3%D9%88%D9%82_%D8%A3%D9%87%D8%B1%D8%A7%D8%B3_%D8%8C_%D8%A7%D9%84%D8%AC%D8%B2%D8%A7%D8%A6%D8%B1.jpg', 'caption' => 'Vestiges romains de Thagaste'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => "Thagaste (Souk Ahras)",
                        'description' => "Ville natale de saint Augustin, connue dans l'Antiquité sous le nom de Thagaste, avec d'importants vestiges romains.",
                        'history' => "Thagaste, aujourd'hui Souk Ahras, est un modeste bourg numide devenu municipe romain, mais sa renommée mondiale tient tout entière à un homme : saint Augustin, l'un des plus grands penseurs chrétiens, qui y naît en 354 dans une famille berbère romanisée. Il y reçoit sa première éducation avant de partir étudier à Madaure puis Carthage.\n\nLa ville conserve les vestiges d'une basilique paléochrétienne et de thermes romains, ainsi que des mosaïques et statues mises au jour par les fouilles, témoins d'une cité provinciale active entre les IIe et IVe siècles, insérée dans les grands réseaux commerciaux de l'Afrique romaine.\n\nClassé patrimoine national algérien depuis 1967, le site attire aujourd'hui pèlerins et chercheurs intéressés par les racines nord-africaines du christianisme occidental, dans une ville qui revendique fièrement son lien avec l'un des philosophes les plus influents de l'histoire.",
                        'visit_info' => "Le site archéologique se visite en une heure environ. Un musée présente les objets mis au jour lors des fouilles. La ville organise régulièrement des événements autour de la figure de saint Augustin.",
                    ],
                    'ar' => [
                        'name' => 'سوق أهراس (تاغست)',
                        'description' => 'مسقط رأس القديس أوغسطين، المعروفة في العصور القديمة باسم تاغست، وتضم آثارا رومانية مهمة.',
                        'history' => "تاغست، سوق أهراس اليوم، كانت بلدة نوميدية متواضعة تحولت إلى بلدية رومانية، لكن شهرتها العالمية تعود بالكامل لرجل واحد: القديس أوغسطين، أحد أعظم مفكري المسيحية، الذي وُلد فيها سنة 354 لعائلة أمازيغية متأثرة بالثقافة الرومانية. تلقى فيها تعليمه الأول قبل أن يرحل للدراسة في مادور ثم قرطاج.\n\nتحتفظ المدينة بآثار كنيسة مسيحية مبكرة وحمامات رومانية، إضافة إلى فسيفساء وتماثيل كشفتها الحفريات، شاهدة على مدينة إقليمية نشطة بين القرنين الثاني والرابع، منخرطة في الشبكات التجارية الكبرى لإفريقيا الرومانية.\n\nصُنف الموقع تراثا وطنيا جزائريا منذ 1967، ويستقطب اليوم الحجاج والباحثين المهتمين بالجذور الإفريقية الشمالية للمسيحية الغربية، في مدينة تفتخر بارتباطها بأحد أكثر الفلاسفة تأثيرا في التاريخ.",
                        'visit_info' => 'تتم زيارة الموقع الأثري في نحو ساعة. يعرض متحف القطع المكتشفة أثناء الحفريات. تنظم المدينة بانتظام فعاليات حول شخصية القديس أوغسطين.',
                    ],
                ],
                'timeline' => [
                    ['year' => 200, 'fr' => ['period_label' => 'IIe - IVe siècle', 'title' => 'Municipe romain actif', 'description' => "Thagaste est un municipe romain prospère, inséré dans les réseaux commerciaux de l'Afrique du Nord."], 'ar' => ['period_label' => 'القرنان الثاني والرابع', 'title' => 'بلدية رومانية نشطة', 'description' => 'كانت تاغست بلدية رومانية مزدهرة، منخرطة في الشبكات التجارية لشمال إفريقيا.']],
                    ['year' => 354, 'fr' => ['period_label' => '354', 'title' => 'Naissance de saint Augustin', 'description' => 'Augustin naît à Thagaste dans une famille berbère romanisée de rang modeste.'], 'ar' => ['period_label' => '354', 'title' => 'ولادة القديس أوغسطين', 'description' => 'وُلد أوغسطين في تاغست لعائلة أمازيغية متأثرة بالرومان من أصل متواضع.']],
                    ['year' => 370, 'fr' => ['period_label' => '370', 'title' => 'Départ pour les études', 'description' => 'Le jeune Augustin quitte Thagaste pour étudier à Madaure puis Carthage.'], 'ar' => ['period_label' => '370', 'title' => 'الرحيل للدراسة', 'description' => 'غادر أوغسطين الشاب تاغست للدراسة في مادور ثم قرطاج.']],
                    ['year' => 1967, 'fr' => ['period_label' => '1967', 'title' => 'Classement au patrimoine national', 'description' => 'Les vestiges de Thagaste sont classés patrimoine national algérien.'], 'ar' => ['period_label' => '1967', 'title' => 'التصنيف كتراث وطني', 'description' => 'صُنفت آثار تاغست تراثا وطنيا جزائريا.']],
                ],
            ],
            [
                'slug' => 'calama-guelma',
                'category' => 'romain',
                'wilaya' => 'Guelma',
                'latitude' => 36.4611,
                'longitude' => 7.4258,
                'opening_hours' => '08h00 - 17h00 — à titre indicatif',
                'entry_fee' => 'Gratuit (hors spectacles) — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/27/GM_Guelma_Theatre_romain01.jpg/500px-GM_Guelma_Theatre_romain01.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/Th%C3%A9%C3%A2tre_romain_de_Guelma%2C_Alg%C3%A9rie.jpg/1280px-Th%C3%A9%C3%A2tre_romain_de_Guelma%2C_Alg%C3%A9rie.jpg', 'caption' => 'Théâtre romain de Guelma'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/84/The_Roman_theatre_of_Guelma_01.jpg/1280px-The_Roman_theatre_of_Guelma_01.jpg', 'caption' => 'Gradins du théâtre antique de Guelma'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ed/The_Roman_theatre_of_Guelma_02.jpg/1280px-The_Roman_theatre_of_Guelma_02.jpg', 'caption' => 'Vue générale du théâtre romain'],
],
                'translations' => [
                    'fr' => [
                        'name' => 'Calama (Guelma)',
                        'description' => 'Cité romaine antique de Calama, célèbre pour son théâtre romain remarquablement conservé, toujours utilisé pour des spectacles.',
                        'history' => "Calama, aujourd'hui Guelma, est une cité numide puis romaine qui doit sa renommée à son théâtre, l'un des mieux conservés d'Afrique du Nord, construit au IIe siècle et capable d'accueillir plusieurs milliers de spectateurs. La ville est aussi le siège d'un évêché important à l'époque paléochrétienne, dont le titulaire, Possidius, fut un proche ami et biographe de saint Augustin.\n\nAprès des siècles d'abandon, le théâtre est restauré au XXe siècle et retrouve sa fonction d'origine : il accueille aujourd'hui régulièrement des concerts et festivals, offrant la particularité rare d'un monument antique toujours utilisé pour sa fonction première, près de deux mille ans après sa construction.\n\nLa ville moderne de Guelma s'est développée directement sur et autour du site antique, ce qui rend la cohabitation entre patrimoine archéologique et vie urbaine contemporaine particulièrement visible.",
                        'visit_info' => "Le théâtre se visite librement en journée ; les soirs de spectacle, il est conseillé de réserver à l'avance. Comptez une heure pour la visite du site.",
                    ],
                    'ar' => [
                        'name' => 'قالمة (كالاما)',
                        'description' => 'مدينة كالاما الرومانية القديمة، تشتهر بمسرحها الروماني المحفوظ بشكل استثنائي، لا يزال يُستخدم للعروض.',
                        'history' => "كالاما، قالمة اليوم، مدينة نوميدية ثم رومانية تدين بشهرتها لمسرحها، أحد أفضل المسارح المحفوظة في شمال إفريقيا، بُني في القرن الثاني الميلادي ويتسع لآلاف المتفرجين. كانت المدينة أيضا مقر أسقفية مهمة في العصر المسيحي المبكر، وكان أسقفها بوسيديوس صديقا مقربا للقديس أوغسطين وكاتب سيرته.\n\nبعد قرون من الإهمال، رُمم المسرح في القرن العشرين واستعاد وظيفته الأصلية: يستضيف اليوم بانتظام حفلات ومهرجانات، في خصوصية نادرة لمعلم أثري لا يزال يُستخدم لغرضه الأول، بعد نحو ألفي سنة من بنائه.\n\nتطورت مدينة قالمة الحديثة مباشرة فوق الموقع القديم وحوله، ما يجعل التعايش بين التراث الأثري والحياة الحضرية المعاصرة واضحا بشكل خاص.",
                        'visit_info' => 'يمكن زيارة المسرح بحرية خلال النهار؛ في أمسيات العروض، يُنصح بالحجز مسبقا. خصص ساعة لزيارة الموقع.',
                    ],
                ],
                'timeline' => [
                    ['year' => 100, 'fr' => ['period_label' => 'IIe siècle', 'title' => 'Construction du théâtre', 'description' => 'Le théâtre romain de Calama est construit, destiné à accueillir plusieurs milliers de spectateurs.'], 'ar' => ['period_label' => 'القرن الثاني', 'title' => 'بناء المسرح', 'description' => 'شُيد مسرح كالاما الروماني، المخصص لاستيعاب آلاف المتفرجين.']],
                    ['year' => 400, 'fr' => ['period_label' => 'Ve siècle', 'title' => 'Siège épiscopal', 'description' => 'Calama est le siège d\'un évêché dont le titulaire Possidius est un proche de saint Augustin.'], 'ar' => ['period_label' => 'القرن الخامس', 'title' => 'مقر أسقفي', 'description' => 'كانت كالاما مقر أسقفية كان صاحبها بوسيديوس مقربا من القديس أوغسطين.']],
                    ['year' => 1950, 'fr' => ['period_label' => 'XXe siècle', 'title' => 'Restauration du théâtre', 'description' => 'Le théâtre romain est restauré et retrouve sa fonction de lieu de spectacle.'], 'ar' => ['period_label' => 'القرن العشرون', 'title' => 'ترميم المسرح', 'description' => 'رُمم المسرح الروماني واستعاد وظيفته كمكان للعروض.']],
                ],
            ],
            [
                'slug' => 'theveste-tebessa',
                'category' => 'romain',
                'wilaya' => 'Tébessa',
                'latitude' => 35.4042,
                'longitude' => 8.1211,
                'opening_hours' => 'Accès libre, en centre-ville — à titre indicatif',
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1c/Caracalla_tebessa_3.jpg/500px-Caracalla_tebessa_3.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/3/3c/Arch_of_Caracalla%2C_Tebessa%2C_North_Africa..jpg', 'caption' => 'Arc de Caracalla à Tébessa'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/Porte_Caracalla_-_T%C3%A9bessa_%D8%A8%D8%A7%D8%A8_%D9%83%D8%B1%D9%83%D9%84%D8%A7_-_%D8%AA%D8%A8%D8%B3%D8%A9.jpg/1280px-Porte_Caracalla_-_T%C3%A9bessa_%D8%A8%D8%A7%D8%A8_%D9%83%D8%B1%D9%83%D9%84%D8%A7_-_%D8%AA%D8%A8%D8%B3%D8%A9.jpg', 'caption' => 'Porte romaine de Caracalla, Tébessa'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Porte_Caracalla_et_enceinte_Byzantine_2%2C_Tebessa.jpg/500px-Porte_Caracalla_et_enceinte_Byzantine_2%2C_Tebessa.jpg', 'caption' => 'Le rempart byzantin de Salomon'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Theveste (Tébessa)',
                        'description' => "Cité romaine de Theveste, célèbre pour son arc de triomphe dédié à Caracalla et ses remparts byzantins.",
                        'history' => "Theveste, aujourd'hui Tébessa, est une importante cité romaine fondée au Ier siècle comme camp de la IIIe légion Auguste, avant de devenir une colonie prospère grâce à sa position sur les routes commerciales reliant la côte aux hauts plateaux. Son monument le plus emblématique, l'arc de triomphe dédié à l'empereur Caracalla, est édifié en 214 et compte parmi les mieux conservés d'Afrique du Nord.\n\nAu VIe siècle, sous l'empereur byzantin Justinien, la ville est entourée d'une puissante enceinte fortifiée, dite « rempart de Salomon », construite pour protéger la cité des incursions berbères. Un temple dédié à Minerve, ainsi qu'une basilique paléochrétienne, complètent cet ensemble monumental exceptionnellement dense pour une ville de cette taille.\n\nTébessa demeure aujourd'hui l'une des villes d'Algérie où la continuité entre urbanisme antique et vie contemporaine est la plus frappante, les remparts byzantins encerclant encore une partie du centre-ville actuel.",
                        'visit_info' => "L'arc de Caracalla et les remparts byzantins se visitent librement, en plein centre-ville. Comptez 1h30 pour l'ensemble du parcours archéologique.",
                    ],
                    'ar' => [
                        'name' => 'تبسة (تيفست)',
                        'description' => 'مدينة تيفست الرومانية، تشتهر بقوس النصر المهدى لكاراكلا وأسوارها البيزنطية.',
                        'history' => "تيفست، تبسة اليوم، مدينة رومانية مهمة تأسست في القرن الأول كمعسكر للفيلق الروماني الثالث أوغسطة، قبل أن تصبح مستعمرة مزدهرة بفضل موقعها على طرق التجارة الرابطة بين الساحل والهضاب العليا. أبرز معالمها، قوس النصر المهدى للإمبراطور كاراكلا، شُيد سنة 214 ويُعد من بين الأفضل حفظا في شمال إفريقيا.\n\nفي القرن السادس، في عهد الإمبراطور البيزنطي جستنيان، أحيط بالمدينة سور حصين قوي، يُعرف بـ«سور سليمان»، بُني لحمايتها من غارات البربر. ويكمل هذا المجمع الأثري الكثيف بشكل استثنائي لمدينة بهذا الحجم معبد مخصص لمينرفا وكنيسة مسيحية مبكرة.\n\nتبقى تبسة اليوم من أكثر مدن الجزائر التي يتجلى فيها بوضوح الاستمرار بين العمران القديم والحياة المعاصرة، إذ لا تزال الأسوار البيزنطية تحيط بجزء من وسط المدينة الحالي.",
                        'visit_info' => 'يمكن زيارة قوس كاراكلا والأسوار البيزنطية بحرية، في وسط المدينة. خصص ساعة ونصف لكامل المسار الأثري.',
                    ],
                ],
                'timeline' => [
                    ['year' => 100, 'fr' => ['period_label' => 'Ier siècle', 'title' => 'Fondation comme camp légionnaire', 'description' => 'Theveste est fondée comme camp de la IIIe légion Auguste.'], 'ar' => ['period_label' => 'القرن الأول', 'title' => 'التأسيس كمعسكر للفيلق', 'description' => 'تأسست تيفست كمعسكر للفيلق الروماني الثالث أوغسطة.']],
                    ['year' => 214, 'fr' => ['period_label' => '214', 'title' => 'Arc de Caracalla', 'description' => "L'arc de triomphe dédié à l'empereur Caracalla est édifié."], 'ar' => ['period_label' => '214', 'title' => 'قوس كاراكلا', 'description' => 'شُيد قوس النصر المهدى للإمبراطور كاراكلا.']],
                    ['year' => 539, 'fr' => ['period_label' => 'VIe siècle', 'title' => 'Rempart byzantin de Salomon', 'description' => 'Sous Justinien, une puissante enceinte fortifiée est construite pour protéger la ville.'], 'ar' => ['period_label' => 'القرن السادس', 'title' => 'سور سليمان البيزنطي', 'description' => 'في عهد جستنيان، بُني سور حصين قوي لحماية المدينة.']],
                    ['year' => 700, 'fr' => ['period_label' => 'VIIe siècle', 'title' => 'Conquête musulmane', 'description' => 'La région passe sous domination musulmane.'], 'ar' => ['period_label' => 'القرن السابع', 'title' => 'الفتح الإسلامي', 'description' => 'خضعت المنطقة للحكم الإسلامي.']],
                ],
            ],
            [
                'slug' => 'palais-ahmed-bey',
                'category' => 'islamique',
                'wilaya' => 'Constantine',
                'latitude' => 36.3650,
                'longitude' => 6.6147,
                'opening_hours' => '08h00 - 17h00, fermé le vendredi — à titre indicatif',
                'entry_fee' => '100 DA — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/03/Ahmed_Bey_Palace_10.jpg/500px-Ahmed_Bey_Palace_10.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e8/Ahmed_Bey_Palace_01.jpg/1280px-Ahmed_Bey_Palace_01.jpg', 'caption' => 'Cour intérieure du palais Ahmed Bey'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/bf/Ahmed_Bey_Palace_09.jpg/500px-Ahmed_Bey_Palace_09.jpg', 'caption' => 'Galerie intérieure'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/69/Ahmed_Bey_Palace_05.jpg/500px-Ahmed_Bey_Palace_05.jpg', 'caption' => 'Cour intérieure'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Palais Ahmed Bey',
                        'description' => 'Palais ottoman du XIXe siècle à Constantine, chef-d\'œuvre d\'architecture andalouse-ottomane aux cours et fontaines somptueuses.',
                        'history' => "Le Palais Ahmed Bey est construit entre 1826 et 1835 par Ahmed Bey, dernier bey ottoman de Constantine, qui résiste plusieurs années à la conquête française avant d'être finalement vaincu. Le palais, conçu comme une résidence à la fois privée et officielle, mobilise des artisans venus de Tunis et d'ailleurs en Méditerranée pour réaliser un décor d'une richesse exceptionnelle.\n\nL'édifice s'organise autour de plusieurs cours intérieures ornées de marbre, de fontaines et de jardins, ainsi que de galeries aux peintures murales représentant des scènes orientalistes, un ensemble décoratif rare pour l'Afrique du Nord ottomane. Après la chute de Constantine en 1837, les autorités françaises réquisitionnent le palais pour en faire le siège du commandement militaire.\n\nRestauré et transformé en musée, le Palais Ahmed Bey est aujourd'hui considéré comme l'un des exemples les plus achevés de l'architecture civile ottomane tardive en Algérie, offrant un témoignage précieux du raffinement de la cour beylicale de Constantine à la veille de la colonisation.",
                        'visit_info' => "La visite guidée du palais dure environ 1 heure et permet d'admirer les cours, fontaines et peintures murales. Il se situe en plein centre historique de Constantine.",
                    ],
                    'ar' => [
                        'name' => 'قصر أحمد باي',
                        'description' => 'قصر عثماني من القرن التاسع عشر بقسنطينة، تحفة معمارية أندلسية عثمانية بأفنيتها ونوافيرها الفخمة.',
                        'history' => "بُني قصر أحمد باي بين 1826 و1835 على يد أحمد باي، آخر باي عثماني لقسنطينة، الذي قاوم الاحتلال الفرنسي عدة سنوات قبل أن يُهزم في النهاية. صُمم القصر كمقر إقامة خاص ورسمي في آن، واستقدم لتزيينه حرفيون من تونس وأماكن أخرى بالبحر الأبيض المتوسط لتحقيق زخرفة بالغة الثراء.\n\nينظم المبنى حول عدة أفنية داخلية مزينة بالرخام والنوافير والحدائق، إضافة إلى أروقة بلوحات جدارية تصور مشاهد استشراقية، وهي مجموعة زخرفية نادرة في شمال إفريقيا العثماني. بعد سقوط قسنطينة سنة 1837، صادرت السلطات الفرنسية القصر لتجعل منه مقرا للقيادة العسكرية.\n\nبعد ترميمه وتحويله إلى متحف، يُعد قصر أحمد باي اليوم من أكمل الأمثلة على العمارة المدنية العثمانية المتأخرة في الجزائر، وشاهدا ثمينا على رقي بلاط قسنطينة عشية الاستعمار.",
                        'visit_info' => 'تستغرق الزيارة المرفقة للقصر نحو ساعة، وتتيح مشاهدة الأفنية والنوافير واللوحات الجدارية. يقع في قلب المركز التاريخي لقسنطينة.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1826, 'fr' => ['period_label' => '1826', 'title' => 'Début de la construction', 'description' => 'Ahmed Bey entame la construction de son palais, mobilisant des artisans de toute la Méditerranée.'], 'ar' => ['period_label' => '1826', 'title' => 'بداية البناء', 'description' => 'شرع أحمد باي في بناء قصره، مستقدما حرفيين من كامل حوض المتوسط.']],
                    ['year' => 1835, 'fr' => ['period_label' => '1835', 'title' => 'Achèvement du palais', 'description' => 'Le palais est achevé, orné de cours, fontaines et peintures murales.'], 'ar' => ['period_label' => '1835', 'title' => 'اكتمال القصر', 'description' => 'اكتمل بناء القصر، المزين بالأفنية والنوافير واللوحات الجدارية.']],
                    ['year' => 1837, 'fr' => ['period_label' => '1837', 'title' => 'Chute de Constantine', 'description' => 'Après la prise de la ville par la France, le palais est réquisitionné pour le commandement militaire.'], 'ar' => ['period_label' => '1837', 'title' => 'سقوط قسنطينة', 'description' => 'بعد استيلاء فرنسا على المدينة، صودر القصر ليصبح مقرا للقيادة العسكرية.']],
                    ['year' => 1980, 'fr' => ['period_label' => 'XXe siècle', 'title' => 'Restauration et musée', 'description' => 'Le palais est restauré et transformé en musée ouvert au public.'], 'ar' => ['period_label' => 'القرن العشرون', 'title' => 'الترميم والمتحف', 'description' => 'رُمم القصر وحُول إلى متحف مفتوح للجمهور.']],
                ],
            ],
            [
                'slug' => 'fort-santa-cruz-oran',
                'category' => 'colonial',
                'wilaya' => 'Oran',
                'latitude' => 35.7106,
                'longitude' => -0.6411,
                'opening_hours' => '09h00 - 17h00 — à titre indicatif',
                'entry_fee' => '200 DA — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/71/Fort_Santa_Cruz_Oran1.jpg/500px-Fort_Santa_Cruz_Oran1.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5f/Fort_Santa_Cruz%2C_Oran_2013.jpg/1280px-Fort_Santa_Cruz%2C_Oran_2013.jpg', 'caption' => 'Vue du fort de Santa Cruz'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Fort_Santa_Cruz%2C_Oran_2013-2.jpg/1280px-Fort_Santa_Cruz%2C_Oran_2013-2.jpg', 'caption' => 'Fortification surplombant Oran'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/59/ChapelleSantaCruz2.jpeg/500px-ChapelleSantaCruz2.jpeg', 'caption' => 'La chapelle Notre-Dame de Santa Cruz'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Fort de Santa Cruz',
                        'description' => "Forteresse espagnole du XVIe siècle dominant Oran, reliée à une chapelle de pèlerinage par un réseau de galeries souterraines.",
                        'history' => "Le fort de Santa Cruz est construit entre 1577 et 1604 par les Espagnols, qui occupent Oran depuis 1509, sur le pic d'Aïdour dominant la ville et la baie à plus de 400 mètres d'altitude. Il remplace une première fortification ottomane et devient la pièce maîtresse d'un système défensif relié par un réseau de galeries souterraines, avec une impressionnante citerne pouvant stocker 300 000 litres d'eau.\n\nL'Espagne conserve Oran, à quelques interruptions près, jusqu'en 1792, date à laquelle elle cède la ville aux Ottomans. Le fort passe ensuite sous contrôle français en 1831. En 1849, à la suite d'une épidémie de choléra, l'évêque d'Oran fait édifier au flanc de la colline la chapelle Notre-Dame de Santa Cruz, devenue un important lieu de pèlerinage catholique toujours actif aujourd'hui.\n\nLe fort et sa chapelle offrent aujourd'hui l'un des panoramas les plus spectaculaires sur la baie d'Oran et demeurent parmi les monuments historiques les plus visités de la ville, résumant à eux seuls les strates espagnole, ottomane, française et religieuse de l'histoire oranaise.",
                        'visit_info' => "L'accès au fort se fait par téléphérique ou par une route sinueuse ; comptez une demi-journée avec la visite de la chapelle. Le point de vue sur la baie d'Oran est spectaculaire.",
                    ],
                    'ar' => [
                        'name' => 'قلعة سانتا كروز',
                        'description' => 'حصن إسباني من القرن السادس عشر يطل على وهران، مرتبط بكنيسة حج عبر شبكة من الأنفاق تحت الأرض.',
                        'history' => "بُني حصن سانتا كروز بين 1577 و1604 على يد الإسبان، الذين احتلوا وهران منذ 1509، فوق قمة أيدور المطلة على المدينة والخليج على ارتفاع يفوق 400 متر. حل محل تحصين عثماني أول، وأصبح حجر الزاوية في نظام دفاعي يربط عدة حصون عبر شبكة من الأنفاق تحت الأرض، مع خزان مياه ضخم بسعة 300 ألف لتر.\n\nحافظت إسبانيا على وهران، مع بعض الانقطاعات، حتى 1792، تاريخ تسليمها للعثمانيين. مر الحصن بعدها تحت السيطرة الفرنسية سنة 1831. وفي 1849، إثر وباء الكوليرا، شيد أسقف وهران على سفح التلة كنيسة سيدة سانتا كروز، التي أصبحت مزارا كاثوليكيا مهما لا يزال نشطا إلى اليوم.\n\nيقدم الحصن وكنيسته اليوم واحدة من أروع الإطلالات على خليج وهران، ويبقيان من أكثر المعالم التاريخية زيارة في المدينة، يختصران وحدهما الطبقات الإسبانية والعثمانية والفرنسية والدينية لتاريخ وهران.",
                        'visit_info' => 'يتم الوصول إلى الحصن عبر التلفريك أو طريق متعرج؛ خصص نصف يوم مع زيارة الكنيسة. الإطلالة على خليج وهران مذهلة.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1509, 'fr' => ['period_label' => '1509', 'title' => "Occupation espagnole d'Oran", 'description' => "L'Espagne s'empare d'Oran et y installe une garnison durable."], 'ar' => ['period_label' => '1509', 'title' => 'الاحتلال الإسباني لوهران', 'description' => 'استولت إسبانيا على وهران وأقامت فيها حامية دائمة.']],
                    ['year' => 1577, 'fr' => ['period_label' => '1577-1604', 'title' => 'Construction du fort', 'description' => "Les Espagnols édifient le fort de Santa Cruz sur le pic d'Aïdour."], 'ar' => ['period_label' => '1577-1604', 'title' => 'بناء الحصن', 'description' => 'شيد الإسبان حصن سانتا كروز فوق قمة أيدور.']],
                    ['year' => 1792, 'fr' => ['period_label' => '1792', 'title' => 'Cession aux Ottomans', 'description' => "L'Espagne cède Oran et son fort aux Ottomans."], 'ar' => ['period_label' => '1792', 'title' => 'التنازل للعثمانيين', 'description' => 'تنازلت إسبانيا عن وهران وحصنها للعثمانيين.']],
                    ['year' => 1831, 'fr' => ['period_label' => '1831', 'title' => 'Contrôle français', 'description' => 'Le fort passe sous contrôle des autorités coloniales françaises.'], 'ar' => ['period_label' => '1831', 'title' => 'السيطرة الفرنسية', 'description' => 'أصبح الحصن تحت سيطرة السلطات الاستعمارية الفرنسية.']],
                    ['year' => 1849, 'fr' => ['period_label' => '1849', 'title' => 'Construction de la chapelle', 'description' => "La chapelle Notre-Dame de Santa Cruz est édifiée à la suite d'une épidémie de choléra."], 'ar' => ['period_label' => '1849', 'title' => 'بناء الكنيسة', 'description' => 'شُيدت كنيسة سيدة سانتا كروز إثر وباء الكوليرا.']],
                ],
            ],
            [
                'slug' => 'taghit',
                'category' => 'naturel',
                'wilaya' => 'Béchar',
                'latitude' => 30.9167,
                'longitude' => -2.0000,
                'opening_hours' => 'Accès libre — à titre indicatif',
                'entry_fee' => 'Gratuit (excursion 4x4 en supplément) — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/AG_006_large.jpg/500px-AG_006_large.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Ksar_Taghit.jpg/1280px-Ksar_Taghit.jpg', 'caption' => 'Ksar de Taghit et palmeraie'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/92/Ksar_Taghit2.jpg/1280px-Ksar_Taghit2.jpg', 'caption' => 'Architecture traditionnelle du ksar de Taghit'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d7/Marabutto-Taghirt.JPG/500px-Marabutto-Taghirt.JPG', 'caption' => "L'oasis de Taghit"],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Taghit',
                        'description' => "Oasis saharienne au pied d'un impressionnant cordon de dunes, avec un ksar traditionnel et des gravures rupestres à proximité.",
                        'history' => "Taghit est une oasis du Sahara occidental algérien, nichée entre une palmeraie dense et l'un des plus spectaculaires cordons de dunes du Grand Erg Occidental, qui peut atteindre plusieurs centaines de mètres de hauteur. Le site est habité depuis des siècles par des populations qui ont développé une agriculture oasienne fondée sur le palmier dattier, irriguée grâce à des techniques traditionnelles adaptées à l'aridité extrême de la région.\n\nUn ksar fortifié, aux maisons de terre crue superposées à flanc de colline, domine la palmeraie et témoigne de l'architecture saharienne traditionnelle. Les environs de Taghit recèlent également plusieurs sites de gravures rupestres préhistoriques, moins connus que ceux du Tassili mais tout aussi riches en représentations de faune et de scènes de vie ancienne.\n\nAujourd'hui, Taghit est devenue une destination prisée du tourisme saharien, réputée pour ses couchers de soleil sur les dunes et son ambiance oasienne préservée, loin de l'agitation des grandes villes du nord du pays.",
                        'visit_info' => "L'excursion sur les dunes se fait idéalement au lever ou au coucher du soleil, avec un guide local et un véhicule 4x4. Le ksar et la palmeraie se visitent à pied.",
                    ],
                    'ar' => [
                        'name' => 'تاغيت',
                        'description' => 'واحة صحراوية عند سفح كثبان رملية مذهلة، بقصر تقليدي ونقوش صخرية قريبة.',
                        'history' => "تاغيت واحة في الصحراء الغربية الجزائرية، تقع بين بستان نخيل كثيف وأحد أروع سلاسل الكثبان الرملية للعرق الغربي الكبير، التي قد يبلغ ارتفاعها عدة مئات من الأمتار. يسكن الموقع منذ قرون سكان طوروا زراعة واحية قائمة على نخيل التمر، تُروى بتقنيات تقليدية متكيفة مع القحولة الشديدة للمنطقة.\n\nيهيمن على بستان النخيل قصر محصن، بمنازله الطينية المتراكبة على سفح تلة، شاهد على العمارة الصحراوية التقليدية. وتضم محيطات تاغيت أيضا عدة مواقع نقوش صخرية ما قبل تاريخية، أقل شهرة من نقوش الطاسيلي لكنها لا تقل غنى في تصوير الحيوانات ومشاهد الحياة القديمة.\n\nأصبحت تاغيت اليوم وجهة مرغوبة للسياحة الصحراوية، تشتهر بغروب الشمس فوق الكثبان وأجوائها الواحية المحفوظة، بعيدا عن صخب المدن الكبرى في شمال البلاد.",
                        'visit_info' => 'يُفضل القيام برحلة الكثبان عند الشروق أو الغروب، برفقة مرشد محلي ومركبة رباعية الدفع. يمكن زيارة القصر وبستان النخيل سيرا على الأقدام.',
                    ],
                ],
                'timeline' => [
                    ['year' => -5000, 'fr' => ['period_label' => 'Préhistoire', 'title' => 'Gravures rupestres', 'description' => 'Des gravures rupestres témoignent d\'une présence humaine ancienne dans la région.'], 'ar' => ['period_label' => 'عصور ما قبل التاريخ', 'title' => 'نقوش صخرية', 'description' => 'تشهد نقوش صخرية على وجود بشري قديم في المنطقة.']],
                    ['year' => 1000, 'fr' => ['period_label' => 'Moyen Âge', 'title' => "Développement de l'oasis", 'description' => 'L\'oasis se développe autour de la culture du palmier dattier et de techniques d\'irrigation traditionnelles.'], 'ar' => ['period_label' => 'العصور الوسطى', 'title' => 'تطور الواحة', 'description' => 'تطورت الواحة حول زراعة نخيل التمر وتقنيات الري التقليدية.']],
                    ['year' => 1950, 'fr' => ['period_label' => 'XXe siècle', 'title' => 'Reconnaissance touristique', 'description' => 'Taghit devient une destination reconnue du tourisme saharien pour ses dunes et son ksar.'], 'ar' => ['period_label' => 'القرن العشرون', 'title' => 'الاعتراف السياحي', 'description' => 'أصبحت تاغيت وجهة سياحية صحراوية معروفة بفضل كثبانها وقصرها.']],
                ],
            ],
            [
                'slug' => 'timimoun',
                'category' => 'casbah',
                'wilaya' => 'Adrar',
                'latitude' => 29.2639,
                'longitude' => 0.2306,
                'opening_hours' => 'Accès libre — à titre indicatif',
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Terrasse_de_l%27hotel_Oasis_rouge_de_Timimoun.jpg/500px-Terrasse_de_l%27hotel_Oasis_rouge_de_Timimoun.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/House_in_Timimoun.jpg/1280px-House_in_Timimoun.jpg', 'caption' => 'Maison en pisé rouge à Timimoun'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Gourara_Monument_%28Timimoun%29_01.jpg/1280px-Gourara_Monument_%28Timimoun%29_01.jpg', 'caption' => 'Monument du Gourara à Timimoun'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Oasis_Timimoun.jpg/500px-Oasis_Timimoun.jpg', 'caption' => 'La palmeraie de Timimoun'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Timimoun',
                        'description' => "« Oasis rouge » du Sahara, célèbre pour l'ocre de son architecture traditionnelle et ses ksour environnants.",
                        'history' => "Timimoun est une oasis du Touat, dans le Grand Sud algérien, surnommée « l'oasis rouge » en raison de la couleur ocre caractéristique de son architecture de terre crue, du palais du gouverneur aux maisons les plus modestes. La ville domine une immense sebkha, dépression salée asséchée qui s'étend à perte de vue et offre un contraste saisissant avec la palmeraie verdoyante en contrebas.\n\nLa région du Touat est depuis des siècles un carrefour caravanier essentiel, relais entre le Maghreb méditerranéen et l'Afrique subsaharienne, ce qui explique la richesse de son architecture et le réseau de ksour fortifiés qui parsèment la palmeraie, chacun organisé autour de sa mosquée et alimenté par un système traditionnel de foggaras, galeries souterraines captant les eaux d'infiltration.\n\nTimimoun a également attiré, au XXe siècle, de nombreux architectes et artistes séduits par la pureté géométrique de son architecture vernaculaire, qui a notamment influencé l'œuvre de Fernand Pouillon. La ville demeure aujourd'hui une porte d'entrée privilégiée vers le Grand Erg Occidental et l'une des destinations les plus photographiées du Sahara algérien.",
                        'visit_info' => "La ville et ses ksour environnants se visitent sur une journée, idéalement avec un guide pour accéder aux foggaras et comprendre l'architecture locale. Le point de vue sur la sebkha au coucher du soleil est incontournable.",
                    ],
                    'ar' => [
                        'name' => 'تيميمون',
                        'description' => '«الواحة الحمراء» في الصحراء، تشتهر بلون عمارتها التقليدية الطينية وقصورها المحيطة.',
                        'history' => "تيميمون واحة في إقليم توات، بأقصى جنوب الجزائر، يُطلق عليها لقب «الواحة الحمراء» بسبب اللون الطيني المميز لعمارتها، من قصر الحاكم إلى أبسط المنازل. تهيمن المدينة على سبخة شاسعة، وهي منخفض ملحي جاف يمتد إلى ما لا نهاية، ويشكل تباينا لافتا مع بستان النخيل الأخضر في الأسفل.\n\nكانت منطقة توات منذ قرون ملتقى قوافل أساسيا، محطة وصل بين المغرب المتوسطي وإفريقيا جنوب الصحراء، ما يفسر ثراء عمارتها وشبكة القصور المحصنة المنتشرة في بستان النخيل، ينظم كل منها حول مسجده ويُغذى بنظام الفقارة التقليدي، وهي أنفاق تحت أرضية تجمع مياه التسرب.\n\nجذبت تيميمون أيضا، في القرن العشرين، العديد من المعماريين والفنانين المفتونين بالنقاء الهندسي لعمارتها المحلية، التي أثرت بشكل خاص على أعمال المعماري فرنان بويون. تبقى المدينة اليوم بوابة مميزة نحو العرق الغربي الكبير وواحدة من أكثر وجهات الصحراء الجزائرية تصويرا.",
                        'visit_info' => 'تتم زيارة المدينة وقصورها المحيطة في يوم كامل، ويُفضل برفقة مرشد للوصول إلى الفقارات وفهم العمارة المحلية. الإطلالة على السبخة عند الغروب لا تُفوت.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1000, 'fr' => ['period_label' => 'Moyen Âge', 'title' => 'Carrefour caravanier', 'description' => 'Le Touat devient un relais caravanier essentiel entre le Maghreb et l\'Afrique subsaharienne.'], 'ar' => ['period_label' => 'العصور الوسطى', 'title' => 'ملتقى القوافل', 'description' => 'أصبح توات محطة قوافل أساسية بين المغرب وإفريقيا جنوب الصحراء.']],
                    ['year' => 1500, 'fr' => ['period_label' => 'XVe - XVIe siècle', 'title' => 'Développement des foggaras', 'description' => "Le système d'irrigation par foggaras se perfectionne pour alimenter les palmeraies."], 'ar' => ['period_label' => 'القرنان الخامس عشر والسادس عشر', 'title' => 'تطور الفقارات', 'description' => 'تطور نظام الري بالفقارات لتغذية بساتين النخيل.']],
                    ['year' => 1950, 'fr' => ['period_label' => 'XXe siècle', 'title' => 'Influence architecturale', 'description' => "L'architecture traditionnelle de Timimoun inspire des architectes modernes comme Fernand Pouillon."], 'ar' => ['period_label' => 'القرن العشرون', 'title' => 'التأثير المعماري', 'description' => 'ألهمت عمارة تيميمون التقليدية معماريين محدثين مثل فرنان بويون.']],
                ],
            ],
            [
                'slug' => 'el-oued',
                'category' => 'casbah',
                'wilaya' => 'El Oued',
                'latitude' => 33.3683,
                'longitude' => 6.8630,
                'opening_hours' => 'Accès libre — à titre indicatif',
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/La_ville_d%27El_Oued_%D9%85%D8%AF%D9%8A%D9%86%D8%A9_%D8%A7%D9%84%D9%88%D8%A7%D8%AF%D9%8A.jpg/500px-La_ville_d%27El_Oued_%D9%85%D8%AF%D9%8A%D9%86%D8%A9_%D8%A7%D9%84%D9%88%D8%A7%D8%AF%D9%8A.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/5/53/El_Oued%2C_la_ville_aux_mille_coupole.jpg', 'caption' => 'El Oued, la ville aux mille coupoles'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/7/7d/El_Oued_-_La_Ville_au_mille_Coupole_Vue_G%C3%A9n%C3%A9rale_%28SNED%29.jpg', 'caption' => 'Vue générale d\'El Oued et ses dômes'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b2/BASA-3K-7-350-24-El_Oued%2C_Algeria.jpg/500px-BASA-3K-7-350-24-El_Oued%2C_Algeria.jpg', 'caption' => 'El Oued et ses coupoles (photo ancienne)'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'El Oued',
                        'description' => "« Ville aux mille coupoles », célèbre pour son architecture saharienne aux toits en dômes et ses grandes cuvettes maraîchères.",
                        'history' => "El Oued, surnommée la « ville aux mille coupoles », doit son surnom à une architecture locale distinctive : en l'absence de bois de charpente dans cette région désertique, les habitations traditionnelles sont couvertes de coupoles de brique, une solution technique à la fois esthétique et parfaitement adaptée au climat saharien.\n\nLa ville s'est développée autour d'une pratique agricole unique, les « ghouts » : de vastes cuvettes creusées à la main dans le sable jusqu'à atteindre la nappe phréatique, au fond desquelles sont plantés des palmiers dattiers, une technique ingénieuse qui a permis l'agriculture dans l'une des régions les plus arides du pays.\n\nCarrefour commercial entre le Sahara et le Tell depuis des siècles, El Oued reste aujourd'hui un centre régional dynamique, où l'architecture traditionnelle aux coupoles blanches cohabite avec une ville moderne en expansion, et où les grands marchés hebdomadaires perpétuent une tradition d'échange caravanier ancienne.",
                        'visit_info' => "La visite de la vieille ville aux coupoles se fait à pied ; le grand marché hebdomadaire, très animé, est un moment fort pour découvrir la vie locale.",
                    ],
                    'ar' => [
                        'name' => 'الوادي',
                        'description' => '«مدينة الألف قبة»، تشتهر بعمارتها الصحراوية ذات الأسقف القبيبة وأحواضها الزراعية الكبيرة.',
                        'history' => "تدين مدينة الوادي، الملقبة بـ«مدينة الألف قبة»، بلقبها لعمارة محلية مميزة: في غياب الخشب للسقوف في هذه المنطقة الصحراوية، تُغطى المساكن التقليدية بقباب من الطوب، حل تقني جميل ومتكيف تماما مع المناخ الصحراوي.\n\nتطورت المدينة حول ممارسة زراعية فريدة، «الغيطان»: أحواض واسعة تُحفر يدويا في الرمال حتى بلوغ المياه الجوفية، تُزرع في قاعها نخيل التمر، وهي تقنية بارعة سمحت بالزراعة في واحدة من أكثر مناطق البلاد قحولة.\n\nملتقى تجاري بين الصحراء والتل منذ قرون، تبقى الوادي اليوم مركزا جهويا نشطا، تتعايش فيه العمارة التقليدية ذات القباب البيضاء مع مدينة حديثة في توسع، حيث تُبقي الأسواق الأسبوعية الكبرى على تقليد تبادل قوافلي عريق.",
                        'visit_info' => 'تتم زيارة المدينة القديمة ذات القباب سيرا على الأقدام؛ يُعد السوق الأسبوعي الكبير، النابض بالحياة، لحظة مميزة لاكتشاف الحياة المحلية.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1000, 'fr' => ['period_label' => 'Moyen Âge', 'title' => 'Développement des ghouts', 'description' => 'Les habitants développent la technique des ghouts pour cultiver le palmier dattier.'], 'ar' => ['period_label' => 'العصور الوسطى', 'title' => 'تطور تقنية الغيطان', 'description' => 'طور السكان تقنية الغيطان لزراعة نخيل التمر.']],
                    ['year' => 1500, 'fr' => ['period_label' => 'XVIe siècle', 'title' => 'Premier noyau urbain', 'description' => 'Un premier noyau urbain se développe à l\'emplacement actuel de la ville.'], 'ar' => ['period_label' => 'القرن السادس عشر', 'title' => 'النواة الحضرية الأولى', 'description' => 'تطورت نواة حضرية أولى في الموقع الحالي للمدينة.']],
                    ['year' => 1950, 'fr' => ['period_label' => 'XXe siècle', 'title' => 'Essor commercial régional', 'description' => 'El Oued s\'affirme comme un carrefour commercial majeur entre le Sahara et le Tell.'], 'ar' => ['period_label' => 'القرن العشرون', 'title' => 'الازدهار التجاري الجهوي', 'description' => 'ترسخت الوادي كملتقى تجاري رئيسي بين الصحراء والتل.']],
                ],
            ],
            [
                'slug' => 'bordj-moussa-bejaia',
                'category' => 'colonial',
                'wilaya' => 'Béjaïa',
                'latitude' => 36.7509,
                'longitude' => 5.0567,
                'opening_hours' => '09h00 - 17h00, fermé le lundi — à titre indicatif',
                'entry_fee' => '100 DA — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Fort_au_dessus_du_port_%C3%A0_B%C3%A9ja%C3%AFa_2.jpg/500px-Fort_au_dessus_du_port_%C3%A0_B%C3%A9ja%C3%AFa_2.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/e/ed/Bordj_Moussa.jpeg', 'caption' => 'Fort de Bordj Moussa à Béjaïa'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/0/0f/Bordj_Moussa_bejaia.png', 'caption' => 'Vue du fort de Bordj Moussa'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0f/Bordj_Moussa_bejaia.png/500px-Bordj_Moussa_bejaia.png', 'caption' => 'Le fort-musée Bordj Moussa'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Bordj Moussa (Béjaïa)',
                        'description' => "Fort du XVIe siècle dominant le port de Béjaïa, aujourd'hui musée, témoin des rivalités espagnoles et ottomanes en Méditerranée.",
                        'history' => "Bordj Moussa est édifié en 1508 par les Espagnols, qui occupent brièvement Béjaïa au début du XVIe siècle dans le cadre de leur expansion nord-africaine, avant d'être repris par les Ottomans qui consolident la fortification pour protéger le port, l'un des plus actifs de la Méditerranée occidentale depuis l'époque hammadide.\n\nBéjaïa, alors appelée Bougie par les Européens, est depuis le Moyen Âge un centre intellectuel et commercial de premier plan : la ville a notamment donné son nom à la bougie de cire, exportée en grande quantité vers l'Europe, et a accueilli au XIIe siècle le mathématicien Leonardo Fibonacci, qui y découvre les chiffres arabo-indiens qu'il contribuera à diffuser en Occident.\n\nAujourd'hui restauré et transformé en musée, Bordj Moussa domine toujours le port et la baie de Béjaïa, offrant un point de vue privilégié sur une ville dont l'histoire mêle héritages hammadide, espagnol, ottoman et français.",
                        'visit_info' => "Le fort-musée se visite en une heure, avec une vue imprenable sur le port. Il est facilement accessible à pied depuis le centre-ville de Béjaïa.",
                    ],
                    'ar' => [
                        'name' => 'برج موسى (بجاية)',
                        'description' => 'حصن من القرن السادس عشر يطل على ميناء بجاية، أصبح اليوم متحفا، شاهدا على التنافس الإسباني والعثماني في المتوسط.',
                        'history' => "شُيد برج موسى سنة 1508 على يد الإسبان، الذين احتلوا بجاية لفترة وجيزة في مطلع القرن السادس عشر ضمن توسعهم في شمال إفريقيا، قبل أن يستعيده العثمانيون الذين عززوا التحصين لحماية الميناء، أحد أكثر موانئ غرب المتوسط نشاطا منذ العهد الحمادي.\n\nكانت بجاية، التي أطلق عليها الأوروبيون اسم بوجي، منذ العصور الوسطى مركزا فكريا وتجاريا بارزا: أعطت المدينة اسمها لشمعة الشمع، التي كانت تُصدَّر بكميات كبيرة نحو أوروبا، واستضافت في القرن الثاني عشر عالم الرياضيات ليوناردو فيبوناتشي، الذي اكتشف فيها الأرقام العربية الهندية التي ساهم في نشرها في الغرب.\n\nبعد ترميمه وتحويله إلى متحف، لا يزال برج موسى يهيمن على ميناء بجاية وخليجها، مقدما إطلالة مميزة على مدينة يمتزج تاريخها بإرث حمادي وإسباني وعثماني وفرنسي.",
                        'visit_info' => 'يمكن زيارة الحصن المتحف في ساعة، مع إطلالة رائعة على الميناء. يسهل الوصول إليه سيرا على الأقدام من وسط مدينة بجاية.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1067, 'fr' => ['period_label' => '1067', 'title' => 'Capitale hammadide', 'description' => 'Béjaïa devient la seconde capitale hammadide après l\'abandon de la Kalâa.'], 'ar' => ['period_label' => '1067', 'title' => 'العاصمة الحمادية', 'description' => 'أصبحت بجاية العاصمة الحمادية الثانية بعد هجر القلعة.']],
                    ['year' => 1184, 'fr' => ['period_label' => 'Vers 1184', 'title' => 'Séjour de Fibonacci', 'description' => 'Le mathématicien Leonardo Fibonacci séjourne à Béjaïa, où il découvre les chiffres arabo-indiens.'], 'ar' => ['period_label' => 'نحو 1184', 'title' => 'إقامة فيبوناتشي', 'description' => 'أقام عالم الرياضيات ليوناردو فيبوناتشي في بجاية، حيث اكتشف الأرقام العربية الهندية.']],
                    ['year' => 1508, 'fr' => ['period_label' => '1508', 'title' => 'Construction du fort par les Espagnols', 'description' => 'Les Espagnols édifient Bordj Moussa pour protéger leur occupation temporaire de Béjaïa.'], 'ar' => ['period_label' => '1508', 'title' => 'بناء الحصن على يد الإسبان', 'description' => 'شيد الإسبان برج موسى لحماية احتلالهم المؤقت لبجاية.']],
                    ['year' => 1555, 'fr' => ['period_label' => '1555', 'title' => 'Reprise ottomane', 'description' => 'Les Ottomans reprennent Béjaïa et consolident les fortifications du port.'], 'ar' => ['period_label' => '1555', 'title' => 'الاستعادة العثمانية', 'description' => 'استعاد العثمانيون بجاية وعززوا تحصينات الميناء.']],
                ],
            ],
            [
                'slug' => 'monument-des-martyrs',
                'category' => 'moderne',
                'wilaya' => 'Alger',
                'latitude' => 36.7378,
                'longitude' => 3.0588,
                'opening_hours' => "Esplanade accessible en permanence ; musée du Moudjahid 09h00 - 17h00, fermé le vendredi — à titre indicatif",
                'entry_fee' => 'Gratuit (esplanade) ; tarif symbolique pour le musée — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Martyrs_Memorial._Algiers%2C_Algeria.jpg/500px-Martyrs_Memorial._Algiers%2C_Algeria.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1c/Maqam_Echahid_la_nuit%2C_Alger.jpg/1280px-Maqam_Echahid_la_nuit%2C_Alger.jpg', 'caption' => 'Maqam Echahid illuminé la nuit'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/0c/Maqam_Echahid.jpg/500px-Maqam_Echahid.jpg', 'caption' => 'Le monument illuminé la nuit'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/db/Monument_of_the_Martyrs_04_Algiers.jpg/500px-Monument_of_the_Martyrs_04_Algiers.jpg', 'caption' => "L'une des trois statues de bronze"],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Monument des Martyrs',
                        'description' => "Mémorial monumental dominant Alger, dédié aux martyrs de la guerre d'indépendance, reconnaissable à ses trois palmes de béton et sa flamme éternelle.",
                        'history' => "Le Monument des Martyrs, ou Maqam Echahid (« sanctuaire du martyr »), est inauguré le 5 juillet 1982, jour du vingtième anniversaire de l'indépendance de l'Algérie. Érigé sur les hauteurs d'El Madania, il domine la baie d'Alger et demeure visible depuis une grande partie de la ville, s'imposant comme le symbole architectural le plus reconnaissable de la capitale aux côtés de la Casbah.\n\nHaut de 96 mètres, le monument prend la forme de trois palmes de béton stylisées qui se rejoignent au sommet, abritant en leur centre une flamme éternelle en hommage aux combattants de la guerre d'indépendance (1954-1962) et, plus largement, à l'ensemble des luttes pour la libération du pays. Sa conception associe l'architecte algérien Bachir Yellès et le sculpteur polonais Marian Konieczny, auteur des trois statues de bronze de six mètres de haut installées à la base de chaque palme.\n\nChacune de ces statues, protégée par un soldat, personnifie une étape distincte du combat national : la résistance populaire de 1830 à 1954 contre la colonisation française ; l'Armée de libération nationale, fer de lance de la guerre d'indépendance ; et l'Armée nationale populaire, héritière de la lutte après 1962. Le site abrite également le Musée national du Moudjahid, consacré à l'histoire du mouvement national et de la guerre de libération.\n\nAujourd'hui, le Monument des Martyrs demeure un lieu de mémoire central pour les Algériens, où se déroulent les principales cérémonies officielles liées à l'histoire de l'indépendance, notamment chaque 5 juillet et 1er novembre.",
                        'visit_info' => "Le site extérieur (esplanade, vue panoramique) est accessible librement à toute heure. Le Musée national du Moudjahid, attenant, se visite en une heure environ. Le panorama sur Alger et sa baie est particulièrement spectaculaire au coucher du soleil.",
                    ],
                    'ar' => [
                        'name' => 'مقام الشهيد',
                        'description' => 'نصب تذكاري ضخم يهيمن على الجزائر العاصمة، مخصص لشهداء حرب التحرير، يتميز بسعفاته الثلاث من الخرسانة ولهبه الخالد.',
                        'history' => "افتُتح مقام الشهيد يوم 5 جويلية 1982، بمناسبة الذكرى العشرين لاستقلال الجزائر. شُيد فوق مرتفعات المدنية، ويهيمن على خليج الجزائر العاصمة، ويبقى مرئيا من جزء كبير من المدينة، ليفرض نفسه كأبرز رمز معماري للعاصمة إلى جانب القصبة.\n\nيبلغ ارتفاع النصب 96 مترا، ويتخذ شكل ثلاث سعفات خرسانية مصممة تلتقي عند القمة، تحتضن في مركزها لهبا خالدا تكريما لمقاتلي حرب التحرير (1954-1962)، وبشكل أوسع لكل نضالات تحرير البلاد. جمع تصميمه بين المعماري الجزائري بشير يلس والنحات البولندي ماريان كونيتشني، صاحب التماثيل البرونزية الثلاثة التي يبلغ ارتفاع كل منها ستة أمتار، والمثبتة عند قاعدة كل سعفة.\n\nيجسد كل تمثال من هذه التماثيل، الذي يحرسه جندي، مرحلة مختلفة من الكفاح الوطني: المقاومة الشعبية من 1830 إلى 1954 ضد الاستعمار الفرنسي؛ جيش التحرير الوطني، رأس الحربة في حرب الاستقلال؛ والجيش الوطني الشعبي، وريث النضال بعد 1962. يضم الموقع أيضا المتحف الوطني للمجاهد، المخصص لتاريخ الحركة الوطنية وحرب التحرير.\n\nيبقى مقام الشهيد اليوم مكان ذاكرة مركزيا للجزائريين، تُقام فيه أهم الاحتفالات الرسمية المرتبطة بتاريخ الاستقلال، خاصة في كل 5 جويلية و1 نوفمبر.",
                        'visit_info' => 'يمكن الوصول إلى الموقع الخارجي (الساحة، الإطلالة البانورامية) بحرية في أي وقت. تتم زيارة المتحف الوطني للمجاهد المجاور في نحو ساعة. الإطلالة على الجزائر العاصمة وخليجها رائعة بشكل خاص عند غروب الشمس.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1954, 'fr' => ['period_label' => '1954', 'title' => "Déclenchement de la guerre d'indépendance", 'description' => "Le déclenchement de la lutte armée le 1er novembre 1954 marque le début de la guerre d'indépendance, à laquelle le monument rend hommage."], 'ar' => ['period_label' => '1954', 'title' => 'اندلاع حرب التحرير', 'description' => 'شكل اندلاع الكفاح المسلح في 1 نوفمبر 1954 بداية حرب الاستقلال التي يخلد النصب ذكراها.']],
                    ['year' => 1962, 'fr' => ['period_label' => '1962', 'title' => "Indépendance de l'Algérie", 'description' => "L'Algérie accède à l'indépendance le 5 juillet 1962, après plus de 130 ans de colonisation française."], 'ar' => ['period_label' => '1962', 'title' => 'استقلال الجزائر', 'description' => 'نالت الجزائر استقلالها في 5 جويلية 1962، بعد أكثر من 130 سنة من الاستعمار الفرنسي.']],
                    ['year' => 1978, 'fr' => ['period_label' => '1978', 'title' => 'Début de la construction', 'description' => "Les travaux du monument débutent sur les hauteurs d'El Madania."], 'ar' => ['period_label' => '1978', 'title' => 'بداية البناء', 'description' => 'انطلقت أشغال بناء النصب فوق مرتفعات المدنية.']],
                    ['year' => 1982, 'fr' => ['period_label' => '5 juillet 1982', 'title' => 'Inauguration du monument', 'description' => "Le Monument des Martyrs est inauguré pour le vingtième anniversaire de l'indépendance."], 'ar' => ['period_label' => '5 جويلية 1982', 'title' => 'تدشين النصب', 'description' => 'دُشن مقام الشهيد بمناسبة الذكرى العشرين للاستقلال.']],
                ],
            ],
            [
                'slug' => 'djamaa-el-djazair',
                'category' => 'religieux',
                'wilaya' => 'Alger',
                'latitude' => 36.7472,
                'longitude' => 3.1611,
                'opening_hours' => 'Horaires de prière ; visites guidées de la mosquée et du minaret sur réservation — à titre indicatif',
                'entry_fee' => 'Gratuit (prière) ; tarif pour la visite du minaret et son ascenseur panoramique — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Djama%C3%A2_El_Djaza%C3%AFr_%E2%80%93_The_Great_Mosque_of_Algiers_11.jpg/500px-Djama%C3%A2_El_Djaza%C3%AFr_%E2%80%93_The_Great_Mosque_of_Algiers_11.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Djamaa_El_Djaza%C3%AFr_2.jpg/1280px-Djamaa_El_Djaza%C3%AFr_2.jpg', 'caption' => 'Façade de la Grande Mosquée d\'Alger'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Mosque_of_Algiers_Golden_dome.jpg/500px-Mosque_of_Algiers_Golden_dome.jpg', 'caption' => 'La coupole de la salle de prière'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Grande_Mosqu%C3%A9e_d%27Alger.jpg/500px-Grande_Mosqu%C3%A9e_d%27Alger.jpg', 'caption' => 'Le minaret, le plus haut du monde'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => "Djamaâ El Djazaïr (Grande Mosquée d'Alger)",
                        'description' => "Plus grande mosquée d'Afrique et troisième au monde par sa capacité, dotée du plus haut minaret du monde surplombant la baie d'Alger.",
                        'history' => "Djamaâ El Djazaïr, la Grande Mosquée d'Alger, est conçue comme un projet d'État destiné à doter la capitale algérienne d'un édifice religieux à la mesure de son histoire, porté par la présidence algérienne à partir des années 2000. Le concours international d'architecture est remporté par le cabinet allemand KSP Jürgen Engel Architekten, associé au bureau d'ingénierie Krebs und Kiefer, tandis que la construction est confiée à l'entreprise chinoise China State Construction Engineering Corporation.\n\nLes travaux débutent le 16 août 2012 sur un terrain de près de 28 hectares en bord de mer, dans le quartier de Mohammadia. Après sept années de chantier, la mosquée est inaugurée en avril 2019. Son minaret culmine à 265 mètres, ce qui en fait le plus haut minaret du monde ; un ascenseur panoramique permet aux visiteurs d'accéder à une plateforme d'observation offrant une vue circulaire sur Alger et sa baie.\n\nLa salle de prière, coiffée d'une coupole de 50 mètres de diamètre culminant à 70 mètres de hauteur, s'étend sur 22 000 mètres carrés et peut accueillir 37 000 fidèles, tandis que l'ensemble du complexe, esplanades comprises, peut en recevoir jusqu'à 120 000 — ce qui fait de Djamaâ El Djazaïr la troisième plus grande mosquée du monde par sa capacité, après la Grande Mosquée de La Mecque et la mosquée du Prophète à Médine.\n\nAu-delà du lieu de culte, le complexe abrite une bibliothèque, un centre d'études coraniques, un musée d'art et d'histoire islamiques et une école, faisant de Djamaâ El Djazaïr à la fois un monument religieux, un symbole architectural national et un centre culturel.",
                        'visit_info' => "L'accès à la salle de prière est possible en dehors des heures de culte pour les visiteurs respectueux de la tenue requise. La montée au minaret par ascenseur panoramique se fait généralement sur réservation ou billet séparé. Comptez une demi-journée pour découvrir l'ensemble du complexe (mosquée, musée, bibliothèque).",
                    ],
                    'ar' => [
                        'name' => 'جامع الجزائر',
                        'description' => 'أكبر مسجد في إفريقيا وثالث أكبر مسجد في العالم من حيث السعة، تعلوه أعلى مئذنة في العالم تطل على خليج الجزائر العاصمة.',
                        'history' => "صُمم جامع الجزائر كمشروع دولة يهدف إلى تزويد العاصمة الجزائرية بصرح ديني يليق بتاريخها، حملته الرئاسة الجزائرية ابتداء من سنوات الألفين. فاز بالمسابقة المعمارية الدولية مكتب KSP Jürgen Engel Architekten الألماني، بالشراكة مع مكتب الهندسة Krebs und Kiefer، بينما أُسندت أشغال البناء للشركة الصينية China State Construction Engineering.\n\nانطلقت الأشغال في 16 أوت 2012 على أرض تبلغ مساحتها نحو 28 هكتارا على ضفاف البحر، بحي المحمدية. بعد سبع سنوات من الأشغال، دُشن الجامع في أفريل 2019. تبلغ مئذنته 265 مترا، ما يجعلها أعلى مئذنة في العالم؛ ويتيح مصعد بانورامي للزوار الوصول إلى منصة مراقبة توفر إطلالة دائرية على الجزائر العاصمة وخليجها.\n\nتمتد قاعة الصلاة، التي تعلوها قبة يبلغ قطرها 50 مترا وارتفاعها 70 مترا، على مساحة 22 ألف متر مربع، وتتسع لـ37 ألف مصل، بينما يمكن للمجمع بأكمله، بما فيه الساحات، استقبال ما يصل إلى 120 ألف شخص - ما يجعل جامع الجزائر ثالث أكبر مسجد في العالم من حيث السعة، بعد المسجد الحرام بمكة والمسجد النبوي بالمدينة المنورة.\n\nإلى جانب كونه مكان عبادة، يضم المجمع مكتبة ومركزا للدراسات القرآنية ومتحفا للفن والتاريخ الإسلاميين ومدرسة، ما يجعل من جامع الجزائر في آن واحد معلما دينيا ورمزا معماريا وطنيا ومركزا ثقافيا.",
                        'visit_info' => 'يمكن الدخول إلى قاعة الصلاة خارج أوقات العبادة للزوار الملتزمين باللباس المطلوب. يتم الصعود إلى المئذنة عبر المصعد البانورامي عادة بالحجز أو بتذكرة منفصلة. خصص نصف يوم لاكتشاف المجمع بأكمله (المسجد، المتحف، المكتبة).',
                    ],
                ],
                'timeline' => [
                    ['year' => 2005, 'fr' => ['period_label' => 'Années 2000', 'title' => 'Lancement du projet', 'description' => 'La présidence algérienne engage le projet d\'une grande mosquée nationale pour Alger.'], 'ar' => ['period_label' => 'سنوات الألفين', 'title' => 'إطلاق المشروع', 'description' => 'أطلقت الرئاسة الجزائرية مشروع جامع وطني كبير للجزائر العاصمة.']],
                    ['year' => 2012, 'fr' => ['period_label' => '16 août 2012', 'title' => 'Début des travaux', 'description' => 'La construction démarre à Mohammadia, confiée à l\'entreprise chinoise CSCEC.'], 'ar' => ['period_label' => '16 أوت 2012', 'title' => 'بداية الأشغال', 'description' => 'انطلقت أشغال البناء بالمحمدية، أُسندت للشركة الصينية CSCEC.']],
                    ['year' => 2019, 'fr' => ['period_label' => 'Avril 2019', 'title' => 'Inauguration', 'description' => 'Djamaâ El Djazaïr est inaugurée après sept années de chantier.'], 'ar' => ['period_label' => 'أفريل 2019', 'title' => 'التدشين', 'description' => 'دُشن جامع الجزائر بعد سبع سنوات من الأشغال.']],
                    ['year' => 2020, 'fr' => ['period_label' => '2019-2020', 'title' => 'Record du plus haut minaret', 'description' => 'Avec ses 265 mètres, le minaret de Djamaâ El Djazaïr devient le plus haut du monde.'], 'ar' => ['period_label' => '2019-2020', 'title' => 'رقم قياسي لأعلى مئذنة', 'description' => 'بارتفاع 265 مترا، أصبحت مئذنة جامع الجزائر الأعلى في العالم.']],
                ],
            ],
            [
                'slug' => 'djurdjura',
                'category' => 'naturel',
                'wilaya' => 'Bouira',
                'latitude' => 36.4667,
                'longitude' => 4.1333,
                'opening_hours' => 'Accès libre — à titre indicatif',
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/DJURDJURA6.jpg/500px-DJURDJURA6.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/25/DJURDJURA_MOUNTAINS_FROM_EVERYWHERE_1.jpg/1280px-DJURDJURA_MOUNTAINS_FROM_EVERYWHERE_1.jpg', 'caption' => 'Panorama des montagnes du Djurdjura'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Massif_of_The_Djurdjura.jpg/1280px-Massif_of_The_Djurdjura.jpg', 'caption' => 'Massif montagneux du Djurdjura'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/78/Parc_National_Djurdjura%2C_Bouira%2C_Algerie_%22_une_vue_sur_l%27horizon%2C_wilaya_de_bouira%22.jpg/500px-Parc_National_Djurdjura%2C_Bouira%2C_Algerie_%22_une_vue_sur_l%27horizon%2C_wilaya_de_bouira%22.jpg', 'caption' => "Vue sur l'horizon du massif"],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Djurdjura',
                        'description' => 'Massif montagneux emblématique de la Kabylie, sommets enneigés en hiver, forêts et gorges, refuge du singe magot.',
                        'history' => "Le Djurdjura est le massif montagneux le plus élevé de la chaîne du Tell, culminant au Lalla Khedidja à plus de 2300 mètres. Les Romains le surnommaient « Mons Ferratus » (la montagne de fer), en référence à la richesse minérale du sous-sol et à la résistance farouche que les populations kabyles opposèrent à l'annexion romaine.\n\nLe nom même de Djurdjura viendrait du kabyle « Jjerjer », évoquant le grand froid ou l'altitude. La chaîne est un château d'eau naturel pour toute la Kabylie, entaillée de gorges spectaculaires, de grottes et de forêts de cèdres et de chênes qui abritent l'une des dernières populations sauvages de singes magots, primate endémique d'Afrique du Nord aujourd'hui menacé.\n\nLe massif est érigé en parc national le 23 juillet 1983, afin de protéger ce patrimoine naturel exceptionnel. La station de Tikjda, sur son versant sud, en fait l'un des rares massifs d'Algérie du Nord accessibles pour la randonnée d'altitude et, l'hiver, pour la pratique du ski.",
                        'visit_info' => "Le parc se prête à la randonnée sur plusieurs niveaux de difficulté, de la station de Tikjda aux sommets du Lalla Khedidja. Les gorges et cascades environnantes se visitent au printemps, quand l'eau est abondante. Prévoyez de bonnes chaussures de marche.",
                    ],
                    'ar' => [
                        'name' => 'جرجرة',
                        'description' => 'سلسلة جبلية رمزية لمنطقة القبائل، قمم مثلجة شتاء، غابات وأخاديد، وملجأ لقرد المكاك البربري.',
                        'history' => "جرجرة أعلى سلسلة جبلية في سلسلة التل، ويبلغ أعلى قممها، لالة خديجة، أكثر من 2300 متر. أطلق عليها الرومان اسم «مونس فيراتوس» (الجبل الحديدي)، في إشارة إلى ثراء باطن الأرض المعدني وإلى المقاومة الشرسة التي أبدتها ساكنة القبائل ضد الضم الروماني.\n\nيُرجَّح أن اسم جرجرة مشتق من الكلمة القبائلية «إجرجر»، التي تعني البرد الشديد أو الارتفاع. تشكل السلسلة خزان مياه طبيعيا لكامل منطقة القبائل، وتخترقها أخاديد مذهلة وكهوف وغابات من الأرز والبلوط تأوي إحدى آخر التجمعات البرية لقرد المكاك البربري، وهو نوع مستوطن في شمال إفريقيا مهدد اليوم.\n\nصُنفت السلسلة الجبلية متنزها وطنيا في 23 جويلية 1983، لحماية هذا التراث الطبيعي الاستثنائي. تجعل منها محطة تيكجدة، على سفحها الجنوبي، واحدة من السلاسل الجبلية النادرة في شمال الجزائر التي يمكن الوصول إليها للتنزه في الارتفاعات، وشتاء لممارسة التزلج.",
                        'visit_info' => 'يصلح المتنزه للتنزه على عدة مستويات من الصعوبة، من محطة تيكجدة إلى قمم لالة خديجة. تُزار الأخاديد والشلالات المحيطة في الربيع، حين تكون المياه وفيرة. احرص على حذاء مشي جيد.',
                    ],
                ],
                'timeline' => [
                    ['year' => -100, 'fr' => ['period_label' => 'Antiquité', 'title' => "Résistance à l'annexion romaine", 'description' => 'Les populations kabyles du massif résistent à la domination romaine, qui le surnomme « Mons Ferratus ».'], 'ar' => ['period_label' => 'العصور القديمة', 'title' => 'مقاومة الضم الروماني', 'description' => 'قاومت ساكنة القبائل في السلسلة الجبلية الهيمنة الرومانية التي أطلقت عليها اسم «مونس فيراتوس».']],
                    ['year' => 1983, 'fr' => ['period_label' => '23 juillet 1983', 'title' => 'Création du parc national', 'description' => 'Le Djurdjura est classé parc national pour protéger ses écosystèmes montagnards.'], 'ar' => ['period_label' => '23 جويلية 1983', 'title' => 'إنشاء المتنزه الوطني', 'description' => 'صُنفت جرجرة متنزها وطنيا لحماية أنظمتها البيئية الجبلية.']],
                    ['year' => 1990, 'fr' => ['period_label' => 'XXe siècle', 'title' => 'Développement de Tikjda', 'description' => 'La station de Tikjda se développe comme porte d\'entrée pour la randonnée et le ski.'], 'ar' => ['period_label' => 'القرن العشرون', 'title' => 'تطور محطة تيكجدة', 'description' => 'تطورت محطة تيكجدة كبوابة للتنزه والتزلج.']],
                ],
            ],
            [
                'slug' => 'sidi-boumediene',
                'category' => 'religieux',
                'wilaya' => 'Tlemcen',
                'latitude' => 34.8700,
                'longitude' => -1.2967,
                'opening_hours' => 'Horaires de prière ; visite possible en journée — à titre indicatif',
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/94/Entr%C3%A9e_du_tombeau_de_Sidi_Boumediene%2C_El_Eubbad%2C_Tlemcen.jpg/500px-Entr%C3%A9e_du_tombeau_de_Sidi_Boumediene%2C_El_Eubbad%2C_Tlemcen.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/94/Entr%C3%A9e_du_tombeau_de_Sidi_Boumediene%2C_El_Eubbad%2C_Tlemcen.jpg/1280px-Entr%C3%A9e_du_tombeau_de_Sidi_Boumediene%2C_El_Eubbad%2C_Tlemcen.jpg', 'caption' => 'Entrée du tombeau de Sidi Boumediene'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/76/Historical_passage_in_El-Eubbad%2C_Tlemcen.jpg/1280px-Historical_passage_in_El-Eubbad%2C_Tlemcen.jpg', 'caption' => 'Passage historique du quartier El Eubbad'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/Tlemcen%2C_Sidi_Boumedi%C3%A8ne.jpg/500px-Tlemcen%2C_Sidi_Boumedi%C3%A8ne.jpg', 'caption' => 'Le complexe de Sidi Boumediene'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Sidi Boumediene (El Eubbad)',
                        'description' => "Complexe funéraire et religieux du soufi Abu Madyan, chef-d'œuvre de l'art almohade et mérinide près de Tlemcen.",
                        'history' => "Abu Madyan, dit Sidi Boumediene, est l'un des plus grands maîtres soufis du Maghreb médiéval, mort en 1198 alors qu'il se rendait à Marrakech sur convocation du calife almohade. Il est inhumé sur les hauteurs d'El Eubbad, aux portes de Tlemcen, où un mausolée est édifié peu après sur ordre du souverain almohade Muhammad al-Nasir.\n\nAu fil des siècles, le site s'enrichit considérablement : princes et souverains successifs de Tlemcen y ajoutent monuments et embellissements, faisant du complexe un ensemble comprenant une mosquée, une médersa et le mausolée. Les sultans mérinides du XIVe siècle y financent des travaux d'une grande richesse décorative : portes sculptées, mosaïques de zellige et plafonds à muqarnas.\n\nLe tombeau de Sidi Boumediene demeure aujourd'hui un haut lieu de pèlerinage soufi, visité par des fidèles venus de tout le Maghreb, et l'un des ensembles d'architecture religieuse médiévale les mieux préservés d'Algérie.",
                        'visit_info' => "Le complexe se visite en une heure environ. Il est situé sur les hauteurs d'El Eubbad, à quelques kilomètres du centre de Tlemcen, offrant une belle vue sur la ville. Une tenue respectueuse est recommandée.",
                    ],
                    'ar' => [
                        'name' => 'سيدي بومدين (العبّاد)',
                        'description' => 'مجمع جنائزي وديني للصوفي أبي مدين، تحفة من الفن الموحدي والمريني قرب تلمسان.',
                        'history' => "أبو مدين، المعروف بسيدي بومدين، أحد أعظم شيوخ التصوف في المغرب في العصور الوسطى، توفي سنة 1198 وهو في طريقه إلى مراكش بطلب من الخليفة الموحدي. دُفن فوق مرتفعات العبّاد، عند مشارف تلمسان، حيث شُيد ضريح بعد وفاته بأمر من السلطان الموحدي محمد الناصر.\n\nعلى مر القرون، أُثري الموقع بشكل كبير: أضاف أمراء وسلاطين تلمسان المتعاقبون معالم وزخارف، فأصبح المجمع يضم مسجدا ومدرسة والضريح نفسه. وموّل سلاطين بني مرين في القرن الرابع عشر أشغالا بالغة الثراء الزخرفي، من أبواب منحوتة وفسيفساء الزليج وأسقف بالمقرنصات.\n\nيبقى ضريح سيدي بومدين اليوم مزارا صوفيا رئيسيا، يقصده مريدون من كامل المغرب، ومن أفضل مجمعات العمارة الدينية في العصور الوسطى المحفوظة في الجزائر.",
                        'visit_info' => 'تتم زيارة المجمع في نحو ساعة. يقع فوق مرتفعات العبّاد، على بعد بضعة كيلومترات من وسط تلمسان، ويوفر إطلالة جميلة على المدينة. يُنصح بلباس محتشم.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1198, 'fr' => ['period_label' => '1198', 'title' => "Mort d'Abu Madyan", 'description' => 'Le maître soufi Abu Madyan meurt en chemin vers Marrakech ; il est inhumé à El Eubbad.'], 'ar' => ['period_label' => '1198', 'title' => 'وفاة أبي مدين', 'description' => 'توفي الشيخ الصوفي أبو مدين في طريقه إلى مراكش، ودُفن بالعبّاد.']],
                    ['year' => 1200, 'fr' => ['period_label' => 'Vers 1200', 'title' => 'Construction du mausolée', 'description' => "Le calife almohade Muhammad al-Nasir fait édifier un mausolée sur la tombe d'Abu Madyan."], 'ar' => ['period_label' => 'نحو 1200', 'title' => 'بناء الضريح', 'description' => 'أمر الخليفة الموحدي محمد الناصر ببناء ضريح فوق قبر أبي مدين.']],
                    ['year' => 1339, 'fr' => ['period_label' => '1339', 'title' => 'Enrichissement mérinide', 'description' => 'Le sultan mérinide Abu al-Hasan fait ajouter mosquée, médersa et décors somptueux.'], 'ar' => ['period_label' => '1339', 'title' => 'الإثراء المريني', 'description' => 'أمر السلطان المريني أبو الحسن بإضافة مسجد ومدرسة وزخارف فاخرة.']],
                ],
            ],
            [
                'slug' => 'gorges-du-rhumel-constantine',
                'category' => 'naturel',
                'wilaya' => 'Constantine',
                'latitude' => 36.3667,
                'longitude' => 6.6083,
                'opening_hours' => 'Accès libre — à titre indicatif',
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/1_Pont_de_Sidi_M%27Cid.JPG/500px-1_Pont_de_Sidi_M%27Cid.JPG',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/Gorge_du_Rhumel_Constantine_depuis_le_pont_de_Sidi-M%27cid.jpg/1280px-Gorge_du_Rhumel_Constantine_depuis_le_pont_de_Sidi-M%27cid.jpg', 'caption' => 'Gorges du Rhumel vues du pont Sidi M\'Cid'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/80/Gorges_du_Rhummel_%26_ponts.jpg/500px-Gorges_du_Rhummel_%26_ponts.jpg', 'caption' => 'Les gorges du Rhumel et ses ponts'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/b0/2_Pont_de_Sidi_M%27Cid.JPG/500px-2_Pont_de_Sidi_M%27Cid.JPG', 'caption' => 'Le pont Sidi M\'Cid'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Gorges du Rhumel et ponts suspendus',
                        'description' => 'Impressionnant canyon naturel qui encercle Constantine, enjambé par des ponts suspendus parmi les plus spectaculaires du monde.',
                        'history' => "Constantine, bâtie sur un plateau rocheux, est ceinturée par les gorges du Rhumel, un canyon naturel spectaculaire creusé sur près de 200 mètres de profondeur, qui a valu à la ville son surnom de « cité des ponts suspendus ». Cette configuration géologique exceptionnelle a longtemps fait de Constantine une place naturellement fortifiée.\n\nPour désenclaver la ville, plusieurs ponts audacieux sont construits au tournant du XXe siècle sous l'administration du maire Émile Morinaud. Le plus célèbre, le pont Sidi M'Cid, conçu par l'ingénieur français Ferdinand Arnodin, est mis en chantier en 1909 et inauguré en 1912 ; avec ses 175 mètres de hauteur au-dessus du canyon, il est alors le pont le plus haut du monde, un record qu'il conserve jusqu'en 1929.\n\nD'autres ouvrages remarquables complètent cet ensemble, comme le pont Sidi Rached et le pont Bab El Kantara, faisant de la traversée des gorges par ses passerelles suspendues l'une des expériences les plus vertigineuses et les plus photographiées d'Algérie.",
                        'visit_info' => "La traversée à pied du pont Sidi M'Cid, gratuite et accessible en tout temps, offre les meilleures vues sur les gorges. Plusieurs points de vue aménagés en ville permettent également d'admirer le canyon sans emprunter les passerelles.",
                    ],
                    'ar' => [
                        'name' => 'أخاديد وادي الرمال والجسور المعلقة',
                        'description' => 'أخدود طبيعي مذهل يحيط بقسنطينة، تعبره جسور معلقة من بين الأكثر إثارة في العالم.',
                        'history' => "بُنيت قسنطينة فوق هضبة صخرية، يحيط بها أخدود وادي الرمال، وهو أخدود طبيعي مذهل حُفر على عمق يقارب 200 متر، ما أكسب المدينة لقب «مدينة الجسور المعلقة». جعلت هذه التضاريس الجيولوجية الاستثنائية من قسنطينة على الدوام موقعا محصنا طبيعيا.\n\nلفك عزلة المدينة، شُيدت عدة جسور جريئة عند مطلع القرن العشرين في عهد العمدة إميل مورينو. أشهرها، جسر سيدي مسيد، الذي صممه المهندس الفرنسي فرديناند أرنودان، بدأت أشغاله سنة 1909 ودُشن سنة 1912؛ وبارتفاعه البالغ 175 مترا فوق الأخدود، كان آنذاك أعلى جسر في العالم، وهو رقم قياسي حافظ عليه حتى 1929.\n\nتكمل هذا المجمع منشآت بارزة أخرى، كجسر سيدي راشد وجسر باب القنطرة، ما يجعل عبور الأخاديد عبر ممراتها المعلقة من أكثر التجارب إثارة وتصويرا في الجزائر.",
                        'visit_info' => 'يوفر عبور جسر سيدي مسيد سيرا على الأقدام، المجاني والمتاح في كل وقت، أفضل إطلالات على الأخاديد. تتيح أيضا نقاط مشاهدة مهيأة في المدينة الإعجاب بالأخدود دون عبور الممرات.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1909, 'fr' => ['period_label' => '1909', 'title' => "Début des travaux du pont Sidi M'Cid", 'description' => "La construction du pont, conçu par l'ingénieur Ferdinand Arnodin, débute sous le mandat du maire Émile Morinaud."], 'ar' => ['period_label' => '1909', 'title' => 'بداية أشغال جسر سيدي مسيد', 'description' => 'انطلقت أشغال بناء الجسر، الذي صممه المهندس فرديناند أرنودان، في عهد العمدة إميل مورينو.']],
                    ['year' => 1912, 'fr' => ['period_label' => '19 avril 1912', 'title' => 'Inauguration', 'description' => "Le pont Sidi M'Cid est inauguré ; il devient le pont le plus haut du monde."], 'ar' => ['period_label' => '19 أفريل 1912', 'title' => 'التدشين', 'description' => 'دُشن جسر سيدي مسيد؛ ليصبح أعلى جسر في العالم.']],
                    ['year' => 1929, 'fr' => ['period_label' => '1929', 'title' => 'Perte du record mondial', 'description' => 'Le pont Royal Gorge, aux États-Unis, dépasse le pont Sidi M\'Cid en hauteur.'], 'ar' => ['period_label' => '1929', 'title' => 'فقدان الرقم القياسي العالمي', 'description' => 'تجاوز جسر رويال غورج الأمريكي جسر سيدي مسيد في الارتفاع.']],
                    ['year' => 2000, 'fr' => ['period_label' => '2000', 'title' => 'Restauration', 'description' => 'Les câbles du pont sont remplacés lors d\'importants travaux de restauration.'], 'ar' => ['period_label' => '2000', 'title' => 'الترميم', 'description' => 'استُبدلت كوابل الجسر خلال أشغال ترميم كبرى.']],
                ],
            ],
            [
                'slug' => 'mansourah',
                'category' => 'islamique',
                'wilaya' => 'Tlemcen',
                'latitude' => 34.8781,
                'longitude' => -1.3419,
                'opening_hours' => 'Accès libre — à titre indicatif',
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/14/Mosqu%C3%A9e_de_Mansourah%2C_Tlemcen%2C_2024.jpg/500px-Mosqu%C3%A9e_de_Mansourah%2C_Tlemcen%2C_2024.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/d/d4/Tlemcen%2C_Algeria_-_Ruin_of_Mansoura.jpg', 'caption' => 'Ruines du site mérinide de Mansourah'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/c/cd/Ruines_du_minaret_du_Mansoura_%28Tlemcen%29.jpeg', 'caption' => 'Ruines du minaret de Mansourah'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/Mansourah_Mosque_07.jpg/500px-Mansourah_Mosque_07.jpg', 'caption' => 'Le minaret vu de l\'intérieur'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Mansourah',
                        'description' => 'Ruines d\'une cité royale mérinide du XIVe siècle, dominées par un imposant minaret de 38 mètres, aux portes de Tlemcen.',
                        'history' => "Mansourah, « la Victorieuse », est fondée en 1299 par le sultan mérinide Abu Yaqub Yusuf comme camp retranché pour assiéger Tlemcen, alors capitale de la dynastie zianide rivale. Le siège dure huit années, marquées par la famine dans la ville assiégée, avant de s'achever brutalement par l'assassinat du sultan mérinide en 1307.\n\nLa cité est reconstruite et redevient, en 1335, le quartier général d'un second siège mené cette fois avec succès par le sultan Abu al-Hasan, qui s'empare de Tlemcen. Mansourah se dote alors de remparts en pierre de près de 12 mètres de haut, d'un palais et d'une grande mosquée dont le portail est richement décoré sous le patronage du même souverain.\n\nAbandonnée après le retrait mérinide, la cité tombe en ruine, mais son minaret carré de 38 mètres, inspiré des grandes tours almohades et andalouses, résiste largement au temps : seules trois de ses quatre faces subsistent aujourd'hui, mais il demeure l'un des monuments les plus impressionnants de la région de Tlemcen.",
                        'visit_info' => "Le site, en accès libre, se visite en une heure. Le minaret, visible de loin, est l'élément le mieux conservé ; les vestiges des remparts et du palais se découvrent en se promenant dans l'enceinte.",
                    ],
                    'ar' => [
                        'name' => 'المنصورة',
                        'description' => 'أطلال مدينة ملكية مرينية من القرن الرابع عشر، تهيمن عليها مئذنة ضخمة بارتفاع 38 مترا، عند مشارف تلمسان.',
                        'history' => "أسس السلطان المريني أبو يعقوب يوسف مدينة المنصورة سنة 1299 كمعسكر محصن لحصار تلمسان، عاصمة الدولة الزيانية المنافسة آنذاك. دام الحصار ثماني سنوات، اتسمت بالمجاعة في المدينة المحاصرة، قبل أن ينتهي فجأة باغتيال السلطان المريني سنة 1307.\n\nأعيد بناء المدينة، وأصبحت من جديد سنة 1335 مقرا لحصار ثان قاده هذه المرة بنجاح السلطان أبو الحسن، الذي استولى على تلمسان. تزودت المنصورة آنذاك بأسوار حجرية يبلغ ارتفاعها نحو 12 مترا، وقصر وجامع كبير زُيّنت بوابته بغنى برعاية السلطان نفسه.\n\nهُجرت المدينة بعد انسحاب المرينيين وسقطت في الخراب، لكن مئذنتها المربعة البالغ ارتفاعها 38 مترا، المستوحاة من الأبراج الموحدية والأندلسية الكبرى، قاومت الزمن إلى حد كبير: لم يتبق اليوم سوى ثلاثة من جوانبها الأربعة، لكنها تبقى من أكثر معالم منطقة تلمسان إثارة للإعجاب.",
                        'visit_info' => 'الموقع، ذو الدخول الحر، تتم زيارته في ساعة. تُعد المئذنة، المرئية من بعيد، العنصر الأفضل حفظا؛ وتُكتشف بقايا الأسوار والقصر بالتجول داخل السور.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1299, 'fr' => ['period_label' => '1299', 'title' => 'Fondation par les Mérinides', 'description' => 'Le sultan Abu Yaqub Yusuf fonde Mansourah comme camp de siège contre Tlemcen.'], 'ar' => ['period_label' => '1299', 'title' => 'التأسيس المريني', 'description' => 'أسس السلطان أبو يعقوب يوسف المنصورة كمعسكر حصار ضد تلمسان.']],
                    ['year' => 1307, 'fr' => ['period_label' => '1307', 'title' => 'Fin du premier siège', 'description' => 'Le sultan mérinide est assassiné, mettant fin au premier siège de Tlemcen.'], 'ar' => ['period_label' => '1307', 'title' => 'نهاية الحصار الأول', 'description' => 'اغتيل السلطان المريني، ما وضع حدا للحصار الأول لتلمسان.']],
                    ['year' => 1335, 'fr' => ['period_label' => '1335', 'title' => 'Second siège et prise de Tlemcen', 'description' => "Le sultan Abu al-Hasan reconstruit Mansourah et s'empare finalement de Tlemcen."], 'ar' => ['period_label' => '1335', 'title' => 'الحصار الثاني والاستيلاء على تلمسان', 'description' => 'أعاد السلطان أبو الحسن بناء المنصورة واستولى في النهاية على تلمسان.']],
                    ['year' => 1400, 'fr' => ['period_label' => 'XVe siècle', 'title' => 'Abandon et ruine', 'description' => 'Après le retrait mérinide, la cité est progressivement abandonnée.'], 'ar' => ['period_label' => 'القرن الخامس عشر', 'title' => 'الهجر والخراب', 'description' => 'بعد انسحاب المرينيين، هُجرت المدينة تدريجيا.']],
                ],
            ],
            [
                'slug' => 'parc-national-el-kala',
                'category' => 'naturel',
                'wilaya' => 'El Tarf',
                'latitude' => 36.8167,
                'longitude' => 8.3667,
                'opening_hours' => 'Accès libre — à titre indicatif',
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/df/GM_Parc_national_El_Kala01.jpg/500px-GM_Parc_national_El_Kala01.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/55/Lac_Oubeira%2C_Parc_National_d%27El-Kala%2C_El-Tarf.jpg/1280px-Lac_Oubeira%2C_Parc_National_d%27El-Kala%2C_El-Tarf.jpg', 'caption' => 'Lac Oubeira dans le parc d\'El Kala'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Lac_tonga_el_kala.JPG/500px-Lac_tonga_el_kala.JPG', 'caption' => 'Le lac Tonga'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f2/Blue_Lake._Mahieddine_Boumendjel.jpg/500px-Blue_Lake._Mahieddine_Boumendjel.jpg', 'caption' => 'Le lac Bleu'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => "Parc national d'El Kala",
                        'description' => 'Mosaïque de lacs, marais et forêts de chênes-lièges dans l\'extrême nord-est algérien, réserve de biosphère mondiale.',
                        'history' => "Le parc national d'El Kala, créé le 23 juillet 1983, protège l'un des ensembles de zones humides les plus riches d'Afrique du Nord, à l'extrême nord-est de l'Algérie, à la frontière tunisienne. Il abrite six plans d'eau majeurs, dont le lac Tonga et le lac Oubeira, ainsi que le lac Mellah, unique lagune du pays directement reliée à la mer.\n\nCette diversité de milieux, entre lacs, tourbières et vastes forêts de chênes-lièges culminant au Djebel El-Ghorra, en fait un sanctuaire pour une faune exceptionnelle : cerfs de Barbarie, sangliers, loutres et plus de 60 espèces d'oiseaux d'eau. Cinq zones humides du parc sont classées sites Ramsar d'importance internationale.\n\nEn reconnaissance de cette richesse écologique, l'UNESCO désigne le parc réserve de biosphère mondiale le 17 décembre 1990. El Kala demeure aujourd'hui l'une des destinations privilégiées de l'écotourisme en Algérie, entre randonnées forestières et observation ornithologique.",
                        'visit_info' => "Les rives du lac Tonga, aménagées d'un sentier d'observation, sont idéales pour l'ornithologie, particulièrement à l'aube. La ville d'El Kala et ses plages complètent agréablement la visite. Prévoyez des jumelles.",
                    ],
                    'ar' => [
                        'name' => 'منتزه القالة الوطني',
                        'description' => 'فسيفساء من البحيرات والمستنقعات وغابات الفلين في أقصى شمال شرق الجزائر، محمية محيط حيوي عالمية.',
                        'history' => "أُنشئ منتزه القالة الوطني في 23 جويلية 1983، ويحمي إحدى أغنى مجموعات الأراضي الرطبة في شمال إفريقيا، في أقصى شمال شرق الجزائر، على الحدود التونسية. يضم ست مسطحات مائية رئيسية، من بينها بحيرة الطونڨة وبحيرة أوبيرة، إضافة إلى بحيرة الملاح، البحيرة الوحيدة في البلاد المتصلة مباشرة بالبحر.\n\nيجعل هذا التنوع في البيئات، بين البحيرات والمستنقعات وغابات الفلين الواسعة التي تعلو جبل الغرة، من المنتزه ملاذا لحيوانات استثنائية: الأيل البربري والخنازير البرية وثعالب الماء وأكثر من 60 نوعا من طيور الماء. صُنفت خمس مناطق رطبة في المنتزه كمواقع رامسار ذات أهمية دولية.\n\nتقديرا لهذه الثروة البيئية، صنفت اليونسكو المنتزه محمية محيط حيوي عالمية في 17 ديسمبر 1990. تبقى القالة اليوم من الوجهات المفضلة للسياحة البيئية في الجزائر، بين التنزه في الغابات ومراقبة الطيور.",
                        'visit_info' => 'تُعد ضفاف بحيرة الطونڨة، المهيأة بمسار للمراقبة، مثالية لمراقبة الطيور، خاصة عند الفجر. تُكمل مدينة القالة وشواطئها الزيارة بشكل ممتع. احرص على اصطحاب منظار.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1983, 'fr' => ['period_label' => '23 juillet 1983', 'title' => 'Création du parc national', 'description' => 'El Kala est classé parc national pour protéger ses zones humides exceptionnelles.'], 'ar' => ['period_label' => '23 جويلية 1983', 'title' => 'إنشاء المتنزه الوطني', 'description' => 'صُنفت القالة متنزها وطنيا لحماية أراضيها الرطبة الاستثنائية.']],
                    ['year' => 1990, 'fr' => ['period_label' => '17 décembre 1990', 'title' => 'Réserve de biosphère UNESCO', 'description' => "L'UNESCO désigne El Kala réserve de biosphère mondiale."], 'ar' => ['period_label' => '17 ديسمبر 1990', 'title' => 'محمية المحيط الحيوي لليونسكو', 'description' => 'صنفت اليونسكو القالة محمية محيط حيوي عالمية.']],
                    ['year' => 2004, 'fr' => ['period_label' => '2001-2009', 'title' => 'Classement Ramsar', 'description' => 'Cinq zones humides du parc sont progressivement classées sites Ramsar d\'importance internationale.'], 'ar' => ['period_label' => '2001-2009', 'title' => 'التصنيف بموجب اتفاقية رامسار', 'description' => 'صُنفت خمس مناطق رطبة في المنتزه تدريجيا كمواقع رامسار ذات أهمية دولية.']],
                ],
            ],
            [
                'slug' => 'mausolee-royal-mauretanie',
                'category' => 'romain',
                'wilaya' => 'Tipaza',
                'latitude' => 36.5750,
                'longitude' => 2.5533,
                'opening_hours' => 'Accès libre, sentier balisé — à titre indicatif',
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f6/Royal_Mausoleum_of_Mauretania_01.jpg/500px-Royal_Mausoleum_of_Mauretania_01.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/70/Le_Mausol%C3%A9e_royal_de_Maur%C3%A9tanie_P9070453.JPG/1280px-Le_Mausol%C3%A9e_royal_de_Maur%C3%A9tanie_P9070453.JPG', 'caption' => 'Vue du mausolée royal de Maurétanie'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/74/Le_Mausol%C3%A9e_royal_de_Maur%C3%A9tanie_P9070454.JPG/1280px-Le_Mausol%C3%A9e_royal_de_Maur%C3%A9tanie_P9070454.JPG', 'caption' => 'Détail architectural du mausolée royal'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3a/42-14_Mausol%C3%A9e_royal_de_mauretania_2.jpg/500px-42-14_Mausol%C3%A9e_royal_de_mauretania_2.jpg', 'caption' => 'Détail du monument'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Mausolée royal de Maurétanie',
                        'description' => "Tombe monumentale du roi berbère Juba II et de la reine Cléopâtre Séléné, érigée au Ier siècle av. J.-C. sur les hauteurs de Tipaza.",
                        'history' => "Le Mausolée royal de Maurétanie, connu localement comme le « Tombeau de la Chrétienne » (Kbor er-Roumia) — appellation qui prête à confusion car il n'a aucun lien avec le christianisme — est un monument funéraire monumental érigé vers 3 av. J.-C. par le roi Juba II de Maurétanie et son épouse Cléopâtre Séléné II, fille de Cléopâtre VII d'Égypte et de Marc Antoine. Il s'agit sans doute de la tombe royale destinée à ce couple berbère et hellénistique.\n\nÉdifié sur une colline dominant la plaine côtière à quelques kilomètres de la ville antique de Tipaza, le monument prend la forme d'un tumulus circulaire de plus de 60 mètres de diamètre, dressé sur un soubassement carré et surmonté d'un cône étagé. Sa hauteur d'origine avoisinait 40 mètres — aujourd'hui réduite à environ 30 mètres après les siècles d'érosion et les tentatives de pillage. À l'intérieur, une galerie voûtée en spirale mène à une chambre funéraire centrale, aujourd'hui vide.\n\nLe monument s'inscrit dans la lignée des tombes royales numides comme le Medracen, plus au sud, dont il partage l'inspiration architecturale. Il témoigne de la synthèse entre traditions berbères, influences hellénistiques et art romain qui caractérise le royaume de Juba II, souverain lettré et vassal loyal de Rome. Le site est inscrit au patrimoine mondial de l'UNESCO en 1982 dans le cadre du bien « Tipasa », mais figure depuis 2002 sur la liste du patrimoine en péril.",
                        'visit_info' => "Le mausolée se visite librement, depuis un sentier partant du parking. Comptez une heure aller-retour, davantage si vous souhaitez faire le tour complet du monument à pied. La vue depuis les hauteurs porte jusqu'à la mer par temps clair.",
                    ],
                    'ar' => [
                        'name' => 'الضريح الملكي الموريتاني',
                        'description' => 'ضريح ملكي ضخم للملك الأمازيغي يوبا الثاني وزوجته كليوباترا سيليني، شُيد في القرن الأول قبل الميلاد فوق مرتفعات تيبازة.',
                        'history' => "الضريح الملكي الموريتاني، المعروف محليا بـ«قبر الرومية» - وهي تسمية مضللة إذ لا علاقة له بالمسيحية - نصب جنائزي ضخم شُيد نحو 3 ق.م على يد الملك يوبا الثاني ملك موريتانيا وزوجته كليوباترا سيليني الثانية، ابنة كليوباترا السابعة ملكة مصر ومارك أنطونيوس. ويُرجَّح أنه كان مخصصا كضريح ملكي لهذا الثنائي الأمازيغي الهلنستي.\n\nشُيد فوق تلة تطل على السهل الساحلي على بعد بضعة كيلومترات من مدينة تيبازة الأثرية، ويأخذ شكل تلة دائرية يتجاوز قطرها 60 مترا، ترتفع فوق قاعدة مربعة وتعلوها قمة مخروطية متدرجة. كان ارتفاعه الأصلي يقارب 40 مترا، وتقلص اليوم إلى نحو 30 مترا بفعل قرون من التآكل ومحاولات النهب. في الداخل، يقود ممر مقبب حلزوني إلى غرفة دفن مركزية فارغة اليوم.\n\nيندرج النصب ضمن سلسلة القبور الملكية النوميدية كالمدغاسن جنوبا، الذي يشترك معه في الإلهام المعماري. ويشهد على التقاء التقاليد الأمازيغية بالتأثيرات الهلنستية والفن الروماني الذي ميز مملكة يوبا الثاني، الملك المثقف والتابع الوفي لروما. أُدرج الموقع في قائمة التراث العالمي لليونسكو سنة 1982 ضمن ملف «تيبازة»، وأُدرج منذ 2002 على قائمة التراث العالمي المهدد.",
                        'visit_info' => 'يمكن زيارة الضريح بحرية عبر مسار انطلاقا من الموقف. خصص ساعة ذهابا وإيابا، وأكثر إن رغبت في الدوران حول النصب سيرا على الأقدام. تمتد الإطلالة من المرتفعات حتى البحر في الأيام الصافية.',
                    ],
                ],
                'timeline' => [
                    ['year' => -25, 'fr' => ['period_label' => 'Vers 25 av. J.-C.', 'title' => "Règne de Juba II", 'description' => 'Le roi berbère Juba II est intronisé par Rome comme souverain de Maurétanie, épouse Cléopâtre Séléné et fait de Iol/Caesarea (Cherchell) sa capitale.'], 'ar' => ['period_label' => 'نحو 25 ق.م', 'title' => 'حكم يوبا الثاني', 'description' => 'نصّبت روما الملك الأمازيغي يوبا الثاني على موريتانيا، تزوج كليوباترا سيليني واتخذ من إيول (شرشال) عاصمة له.']],
                    ['year' => -3, 'fr' => ['period_label' => 'Vers 3 av. J.-C.', 'title' => 'Construction du mausolée', 'description' => 'Le couple royal fait ériger le mausolée circulaire, sans doute pour leur propre sépulture.'], 'ar' => ['period_label' => 'نحو 3 ق.م', 'title' => 'بناء الضريح', 'description' => 'شيّد الزوجان الملكيان الضريح الدائري، على الأرجح ليكون مقرا لدفنهما.']],
                    ['year' => 1866, 'fr' => ['period_label' => 'XIXe siècle', 'title' => 'Redécouverte scientifique', 'description' => "Adrien Berbrugger, conservateur du musée d'Alger, entreprend les premières explorations sérieuses du monument."], 'ar' => ['period_label' => 'القرن التاسع عشر', 'title' => 'إعادة الاكتشاف العلمي', 'description' => 'قاد أدريان بيربروجي، محافظ متحف الجزائر العاصمة، أولى الاستكشافات الجادة للنصب.']],
                    ['year' => 1982, 'fr' => ['period_label' => '1982', 'title' => "Inscription à l'UNESCO", 'description' => 'Le mausolée est inscrit dans le périmètre du bien Tipasa au patrimoine mondial.'], 'ar' => ['period_label' => '1982', 'title' => 'التسجيل في اليونسكو', 'description' => 'أُدرج الضريح ضمن محيط تيبازة في قائمة التراث العالمي.']],
                    ['year' => 2002, 'fr' => ['period_label' => '2002', 'title' => 'Liste du patrimoine en péril', 'description' => "Le site est ajouté à la liste UNESCO du patrimoine mondial en péril, en raison de menaces sur son intégrité."], 'ar' => ['period_label' => '2002', 'title' => 'قائمة التراث المهدد', 'description' => 'أُدرج الموقع في قائمة اليونسكو للتراث العالمي المهدد، بسبب التهديدات التي تطال سلامته.']],
                ],
            ],
            [
                'slug' => 'cherchell',
                'category' => 'romain',
                'wilaya' => 'Tipaza',
                'latitude' => 36.6094,
                'longitude' => 2.1908,
                'opening_hours' => "Musée public national : 09h00 - 16h30, fermé le vendredi — à titre indicatif",
                'entry_fee' => '200 DA (musée) ; ruines en accès libre — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Ancient_Roman_theater_%28Cherchell%29_01.jpg/500px-Ancient_Roman_theater_%28Cherchell%29_01.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Ancient_Roman_theater_%28Cherchell%29_01.jpg/1280px-Ancient_Roman_theater_%28Cherchell%29_01.jpg', 'caption' => 'Théâtre romain antique de Cherchell'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/Mosaic_of_the_Three_Graces_%28Cherchell%29.jpg/500px-Mosaic_of_the_Three_Graces_%28Cherchell%29.jpg', 'caption' => 'Mosaïque des Trois Grâces'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/Aqueduc_de_Cherchell%2C_Side_view.jpg/500px-Aqueduc_de_Cherchell%2C_Side_view.jpg', 'caption' => "Vestiges de l'aqueduc romain"],
                ],
                'translations' => [
                    'fr' => [
                        'name' => 'Cherchell (Iol Caesarea)',
                        'description' => "Ancienne capitale de la Maurétanie sous Juba II, port méditerranéen aux ruines romaines et au musée archéologique de renom.",
                        'history' => "Cherchell, l'antique Iol, est fondée vers le VIe siècle av. J.-C. comme comptoir phénicien puis carthaginois sur la côte de la Maurétanie centrale. Elle prend une dimension royale sous le règne du roi berbère Juba II, qui la choisit vers 25 av. J.-C. comme capitale de son royaume et la rebaptise Caesarea, en hommage à son protecteur l'empereur Auguste.\n\nSous l'impulsion de ce souverain lettré, formé à Rome et marié à Cléopâtre Séléné, fille de Cléopâtre VII, la ville devient un brillant centre culturel hellénistique, doté d'un théâtre, d'un amphithéâtre, d'un hippodrome, d'un phare inspiré de celui d'Alexandrie et de nombreuses œuvres d'art. Sa population atteint plusieurs dizaines de milliers d'habitants à son apogée.\n\nAnnexée à l'Empire romain à la mort du fils de Juba II, Ptolémée, en 40 apr. J.-C., Caesarea devient la capitale d'une nouvelle province, la Maurétanie Césarienne. La ville prospère jusqu'aux invasions vandales du Ve siècle, puis décline. Aujourd'hui, Cherchell conserve d'importants vestiges antiques — thermes, théâtre, aqueducs — et son Musée public national abrite l'une des plus riches collections de sculptures et de mosaïques romaines d'Algérie.",
                        'visit_info' => "Le musée archéologique se visite en 1h30 environ ; ne manquez pas les statues et les mosaïques du royaume de Juba II. Les ruines de la ville antique, dispersées dans le tissu urbain moderne, se découvrent librement en une demi-journée.",
                    ],
                    'ar' => [
                        'name' => 'شرشال (إيول قيصرية)',
                        'description' => 'العاصمة القديمة لموريتانيا في عهد يوبا الثاني، مرفأ متوسطي بآثار رومانية ومتحف أثري ذائع الصيت.',
                        'history' => "شرشال، إيول القديمة، تأسست نحو القرن السادس قبل الميلاد كمركز تجاري فينيقي ثم قرطاجي على ساحل موريتانيا الوسطى. اكتسبت مكانة ملكية في عهد الملك الأمازيغي يوبا الثاني، الذي اختارها نحو سنة 25 ق.م عاصمة لمملكته وأعاد تسميتها قيصرية تكريما لحاميه الإمبراطور أوغسطس.\n\nبفضل هذا الملك المثقف الذي تلقى تعليمه في روما وتزوج كليوباترا سيليني، ابنة كليوباترا السابعة، أصبحت المدينة مركزا ثقافيا هلنستيا لامعا، بمسرحها ومدرجها وحلبة سباق خيلها ومنارتها المستوحاة من منارة الإسكندرية والعديد من الأعمال الفنية. وبلغ سكانها عشرات الآلاف في أوج ازدهارها.\n\nضُمت إلى الإمبراطورية الرومانية بعد وفاة بطليموس ابن يوبا الثاني سنة 40م، وأصبحت قيصرية عاصمة لولاية جديدة، هي موريتانيا القيصرية. ازدهرت المدينة حتى غزوات الوندال في القرن الخامس، ثم تراجعت. تحتفظ شرشال اليوم بآثار قديمة مهمة - حمامات، مسرح، قنوات - ويضم متحفها الوطني إحدى أغنى مجموعات المنحوتات والفسيفساء الرومانية في الجزائر.",
                        'visit_info' => 'يمكن زيارة المتحف الأثري في ساعة ونصف تقريبا؛ لا تفوت المنحوتات والفسيفساء من مملكة يوبا الثاني. تنتشر آثار المدينة القديمة في النسيج العمراني الحديث، وتُكتشف بحرية في نصف يوم.',
                    ],
                ],
                'timeline' => [
                    ['year' => -600, 'fr' => ['period_label' => 'VIe - Ve siècle av. J.-C.', 'title' => "Fondation phénicienne d'Iol", 'description' => 'Des marchands phéniciens fondent un comptoir sur ce site côtier stratégique.'], 'ar' => ['period_label' => 'القرنان السادس والخامس ق.م', 'title' => 'التأسيس الفينيقي لإيول', 'description' => 'أسس تجار فينيقيون مركزا تجاريا في هذا الموقع الساحلي الاستراتيجي.']],
                    ['year' => -25, 'fr' => ['period_label' => 'Vers 25 av. J.-C.', 'title' => 'Juba II en fait sa capitale', 'description' => "Le roi berbère Juba II choisit Iol comme capitale de son royaume et la rebaptise Caesarea."], 'ar' => ['period_label' => 'نحو 25 ق.م', 'title' => 'يوبا الثاني يجعلها عاصمة له', 'description' => 'اختار الملك الأمازيغي يوبا الثاني إيول عاصمة لمملكته وأعاد تسميتها قيصرية.']],
                    ['year' => 40, 'fr' => ['period_label' => '40 apr. J.-C.', 'title' => "Annexion à l'Empire romain", 'description' => "Après l'assassinat de Ptolémée, fils de Juba II, la Maurétanie est intégrée à l'Empire ; Caesarea devient capitale de la Maurétanie Césarienne."], 'ar' => ['period_label' => '40م', 'title' => 'الضم إلى الإمبراطورية الرومانية', 'description' => 'بعد اغتيال بطليموس ابن يوبا الثاني، ضُمت موريتانيا إلى الإمبراطورية، وأصبحت قيصرية عاصمة موريتانيا القيصرية.']],
                    ['year' => 429, 'fr' => ['period_label' => 'Ve siècle', 'title' => 'Invasion vandale', 'description' => "La ville est prise par les Vandales, ce qui amorce son déclin."], 'ar' => ['period_label' => 'القرن الخامس', 'title' => 'الغزو الوندالي', 'description' => 'استولى الوندال على المدينة، ما أدى إلى بداية تراجعها.']],
                    ['year' => 1904, 'fr' => ['period_label' => '1904', 'title' => 'Ouverture du musée archéologique', 'description' => "Un musée est fondé pour abriter la riche collection de sculptures et mosaïques exhumées sur place."], 'ar' => ['period_label' => '1904', 'title' => 'افتتاح المتحف الأثري', 'description' => 'أُسس متحف لاحتضان المجموعة الغنية من المنحوتات والفسيفساء التي كُشفت في الموقع.']],
                ],
            ],
            [
                'slug' => 'notre-dame-afrique',
                'category' => 'religieux',
                'wilaya' => 'Alger',
                'latitude' => 36.8014,
                'longitude' => 3.0397,
                'opening_hours' => "Ouvert tous les jours 08h00 - 18h00 environ ; messes régulières — à titre indicatif",
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Notre_Dame_d%27Afrique2_cropped.jpg/500px-Notre_Dame_d%27Afrique2_cropped.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/f8/Alger-Notre-Dame-D%27Afrique_Basilique.jpg/1280px-Alger-Notre-Dame-D%27Afrique_Basilique.jpg', 'caption' => 'Basilique Notre-Dame d\'Afrique surplombant la baie'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/9b/Basilique_Notre-Dame-d%27Afrique_2023_03.jpg/1280px-Basilique_Notre-Dame-d%27Afrique_2023_03.jpg', 'caption' => 'Façade de la basilique Notre-Dame d\'Afrique'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/Notre_Dame_d%27Afrique_at_night.jpg/500px-Notre_Dame_d%27Afrique_at_night.jpg', 'caption' => 'La basilique illuminée la nuit'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => "Basilique Notre-Dame d'Afrique",
                        'description' => "Basilique néo-byzantine perchée sur la falaise de Bologhine, dominant Alger et sa baie depuis 1872.",
                        'history' => "Notre-Dame d'Afrique est une basilique catholique érigée sur les falaises du quartier de Bologhine, sur les hauteurs ouest d'Alger, où elle domine la baie de plus de cent mètres de hauteur. Conçue par l'architecte Jean-Eugène Fromageau dans un style néo-byzantin très libre inspiré à la fois de Sainte-Sophie de Constantinople et de l'art nord-africain, elle est inaugurée en 1872 après quatorze années de construction.\n\nLa basilique doit son existence à l'initiative de Mgr Lavigerie, archevêque d'Alger et fondateur de la congrégation missionnaire des Pères blancs, qui souhaite doter la ville d'un sanctuaire dédié à la Vierge protectrice des marins et des malades. Elle se distingue par sa célèbre inscription dans l'abside : « Notre Dame d'Afrique priez pour nous et pour les Musulmans », qui reflète la volonté de dialogue interreligieux qui présida à son édification et qui reste, dans le contexte algérien contemporain, un message fort.\n\nAprès l'indépendance de l'Algérie en 1962, la basilique demeure un lieu de culte actif, malgré la baisse drastique de la communauté catholique. Restaurée entre 2007 et 2010 grâce à un vaste chantier international, elle reste un symbole architectural fort d'Alger, visitée aussi bien par les fidèles que par les touristes de toutes confessions, séduits par sa vue exceptionnelle et son atmosphère paisible.",
                        'visit_info' => "L'accès à la basilique et à son esplanade est libre en dehors des offices. La vue sur la baie d'Alger et la Casbah est spectaculaire, particulièrement au coucher du soleil. Tenue respectueuse recommandée.",
                    ],
                    'ar' => [
                        'name' => 'كاتدرائية نوتردام دافريك',
                        'description' => 'كاتدرائية بيزنطية حديثة تعتلي منحدر بولوغين، تطل على الجزائر العاصمة وخليجها منذ 1872.',
                        'history' => "نوتردام دافريك كاتدرائية كاثوليكية شُيّدت على منحدرات حي بولوغين، فوق مرتفعات غرب الجزائر العاصمة، حيث تطل على الخليج من ارتفاع يزيد على مائة متر. صمّمها المعماري جان أوجين فروماجو بأسلوب بيزنطي حديث حر مستوحى في آن واحد من آيا صوفيا في القسطنطينية ومن الفن الشمال إفريقي، ودُشّنت سنة 1872 بعد أربعة عشر عاما من الأشغال.\n\nيعود وجود الكاتدرائية إلى مبادرة المونسنيور لافيجري، رئيس أساقفة الجزائر ومؤسس جمعية «الآباء البيض» التبشيرية، الذي أراد أن يُهدي المدينة معلما مخصصا لمريم العذراء حامية البحارة والمرضى. تتميّز بنقش أبسيدها الشهير: «سيدة إفريقيا صلّي من أجلنا ومن أجل المسلمين»، الذي يعكس إرادة الحوار بين الأديان التي رافقت تشييدها ويبقى، في السياق الجزائري المعاصر، رسالة قوية.\n\nبعد استقلال الجزائر سنة 1962، ظلت الكاتدرائية مكان عبادة نشطا، رغم التراجع الحاد للجالية الكاثوليكية. رُممت بين 2007 و2010 بفضل ورش دولي واسع، وتبقى رمزا معماريا قويا للجزائر العاصمة، يقصدها المصلون كما السياح من كل الأديان الذين تجذبهم إطلالتها الاستثنائية وأجواؤها الهادئة.",
                        'visit_info' => 'الدخول إلى الكاتدرائية والساحة أمامها مجاني خارج أوقات الصلوات. الإطلالة على خليج الجزائر والقصبة مذهلة، خاصة عند غروب الشمس. يُنصح باللباس المحتشم.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1858, 'fr' => ['period_label' => '1858', 'title' => 'Début des travaux', 'description' => "Le chantier de la basilique s'ouvre sur les hauteurs de Bologhine sous l'égide de Mgr Pavy puis Mgr Lavigerie."], 'ar' => ['period_label' => '1858', 'title' => 'انطلاق الأشغال', 'description' => 'انطلق ورش الكاتدرائية على مرتفعات بولوغين تحت إشراف المونسنيور بافي ثم لافيجري.']],
                    ['year' => 1872, 'fr' => ['period_label' => '1872', 'title' => 'Inauguration', 'description' => "La basilique est inaugurée après quatorze années de travaux."], 'ar' => ['period_label' => '1872', 'title' => 'التدشين', 'description' => 'دُشّنت الكاتدرائية بعد أربعة عشر عاما من الأشغال.']],
                    ['year' => 1962, 'fr' => ['period_label' => '1962', 'title' => "Indépendance de l'Algérie", 'description' => "Malgré le départ massif de la communauté catholique, la basilique reste ouverte au culte."], 'ar' => ['period_label' => '1962', 'title' => 'استقلال الجزائر', 'description' => 'رغم رحيل الجالية الكاثوليكية بكثافة، بقيت الكاتدرائية مفتوحة للعبادة.']],
                    ['year' => 2010, 'fr' => ['period_label' => '2007-2010', 'title' => 'Grande restauration', 'description' => "Un vaste chantier de restauration internationale redonne à la basilique sa splendeur d'origine."], 'ar' => ['period_label' => '2007-2010', 'title' => 'ترميم كبير', 'description' => 'أعاد ورش ترميم دولي واسع للكاتدرائية بهاءها الأصلي.']],
                ],
            ],
            [
                'slug' => 'ghoufi',
                'category' => 'naturel',
                'wilaya' => 'Batna',
                'latitude' => 35.1483,
                'longitude' => 6.3283,
                'opening_hours' => "Accès libre — à titre indicatif",
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/09/Canyon_de_Ghoufi_01.jpg/500px-Canyon_de_Ghoufi_01.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/3/3d/Aur%C3%A8s_Ghoufi.jpg', 'caption' => 'Canyon des Balcons de Ghoufi'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/5/53/Aur%C3%A8s_Ghoufi_1.jpg', 'caption' => 'Villages berbères accrochés à la falaise'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/bd/Maisons_de_Ghoufi%2C_Wilaya_de_Batna_3.JPG/500px-Maisons_de_Ghoufi%2C_Wilaya_de_Batna_3.JPG', 'caption' => 'Maisons troglodytes accrochées à la falaise'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => "Balcons de Ghoufi",
                        'description' => "Canyon spectaculaire des Aurès, aux villages troglodytes chaouis accrochés à des falaises de plus de 200 mètres.",
                        'history' => "Les Balcons de Ghoufi désignent un canyon spectaculaire creusé par l'oued Abiod dans les hauts plateaux des Aurès, à environ 80 kilomètres au sud de Batna. Sur 3 à 4 kilomètres de longueur, les parois rocheuses s'élèvent par endroits à plus de 200 mètres, révélant une palette de rouges, d'ocres et de gris caractéristique de la région.\n\nLa singularité de Ghoufi tient à ses villages troglodytes accrochés à la falaise : six hameaux — Hitesla, Idharene, Ath Mimoune, Ath Yahia, Ath Mansour et Taouriret — étagent leurs maisons de pierre à même la roche, sur ce qu'on appelle localement les « balcons ». Ces habitations, vieilles de plusieurs siècles, sont l'œuvre des Chaouis, population berbère des Aurès qui les a occupées de manière continue jusqu'aux années 1970, avant que leurs habitants ne descendent progressivement s'installer dans la vallée.\n\nLes constructions présentent une architecture berbère traditionnelle : murs en pierre grossièrement taillée liés à un mortier local, plafonds soutenus par des troncs de palmier et des poutres en bois, greniers-forteresses appelés taqliaths perchés au-dessus des maisons. Aujourd'hui partiellement en ruine, le site témoigne d'un mode de vie remarquable d'adaptation à un environnement rude, et attire les randonneurs comme les amateurs de patrimoine berbère.",
                        'visit_info' => "Le canyon se découvre depuis plusieurs belvédères aménagés en surplomb ; la descente au fond de la gorge se fait à pied par des sentiers escarpés (comptez une demi-journée). Un guide local est recommandé pour explorer les villages troglodytes.",
                    ],
                    'ar' => [
                        'name' => "شرفات الغوفي",
                        'description' => 'أخدود مذهل في جبال الأوراس، بقراه المنحوتة في الصخر والمعلقة على جروف يتجاوز ارتفاعها 200 متر.',
                        'history' => "تشير شرفات الغوفي إلى أخدود مذهل حفره وادي الأبيض في هضاب الأوراس، على بعد نحو 80 كيلومترا جنوب باتنة. على طول يتراوح بين 3 و4 كيلومترات، ترتفع الجدران الصخرية في بعض الأماكن إلى أكثر من 200 متر، كاشفة عن لوحة من الأحمر والمغرة والرمادي المميزة للمنطقة.\n\nتكمن خصوصية الغوفي في قراها المنحوتة في الصخر والمعلقة على الجرف: ست قرى - هيتسلا، إظهارن، آث ميمون، آث يحيى، آث منصور وتاوريرت - تتدرج منازلها الحجرية على الصخر ذاته، فوق ما يُعرف محليا بـ«الشرفات». هذه المساكن التي يعود عمرها إلى قرون عديدة، هي من صنع الشاوية، السكان الأمازيغ للأوراس، الذين شغلوها بشكل متواصل حتى سنوات السبعينيات، قبل أن ينزلوا تدريجيا للاستقرار في الوادي.\n\nتتبنى المباني عمارة أمازيغية تقليدية: جدران من حجارة منحوتة بشكل خشن مربوطة بملاط محلي، أسقف تسندها جذوع النخيل وعوارض خشبية، ومخازن-حصون تُسمى تقليات معلقة فوق المنازل. ورغم أنها اليوم في حالة خراب جزئي، تشهد الشرفات على نمط عيش لافت في التكيف مع بيئة قاسية، وتستقطب المتنزهين وعشاق التراث الأمازيغي.",
                        'visit_info' => 'يُكتشف الأخدود من عدة نقاط مشاهدة مهيأة تعلو الوادي؛ ويتم النزول إلى قاعه سيرا على الأقدام عبر مسارات وعرة (خصص نصف يوم). يُنصح بمرشد محلي لاستكشاف القرى الصخرية.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1600, 'fr' => ['period_label' => 'XVIIe siècle', 'title' => 'Fondation des villages', 'description' => "Les Chaouis édifient leurs premiers hameaux troglodytes dans les falaises du canyon, il y a environ quatre siècles."], 'ar' => ['period_label' => 'القرن السابع عشر', 'title' => 'تأسيس القرى', 'description' => 'شيّد الشاوية أولى قراهم الصخرية في جروف الأخدود منذ نحو أربعة قرون.']],
                    ['year' => 1900, 'fr' => ['period_label' => 'Fin du XIXe siècle', 'title' => "Reconnaissance ethnographique", 'description' => "Les explorateurs et ethnographes français découvrent et documentent le mode de vie chaoui de Ghoufi."], 'ar' => ['period_label' => 'أواخر القرن التاسع عشر', 'title' => 'الاعتراف الإثنوغرافي', 'description' => 'اكتشف المستكشفون وعلماء الإثنوغرافيا الفرنسيون نمط عيش الشاوية في الغوفي ووثقوه.']],
                    ['year' => 1975, 'fr' => ['period_label' => 'Années 1970', 'title' => 'Abandon progressif', 'description' => "Les habitants descendent peu à peu s'installer dans la vallée, laissant les villages en ruine."], 'ar' => ['period_label' => 'سنوات السبعينيات', 'title' => 'الهجر التدريجي', 'description' => 'نزل السكان تدريجيا للاستقرار في الوادي، تاركين القرى مهجورة.']],
                    ['year' => 2000, 'fr' => ['period_label' => 'Depuis 2000', 'title' => 'Tourisme patrimonial', 'description' => "Le site attire de plus en plus de visiteurs, entre randonnée et découverte du patrimoine berbère chaoui."], 'ar' => ['period_label' => 'منذ 2000', 'title' => 'السياحة التراثية', 'description' => 'يستقطب الموقع عددا متزايدا من الزوار، بين التنزه واكتشاف التراث الأمازيغي الشاوي.']],
                ],
            ],
            [
                'slug' => 'grande-poste-alger',
                'category' => 'colonial',
                'wilaya' => 'Alger',
                'latitude' => 36.7736,
                'longitude' => 3.0592,
                'opening_hours' => "Musée : 09h00 - 17h00, fermé le vendredi — à titre indicatif",
                'entry_fee' => 'Gratuit — à titre indicatif',
                'image' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/La_Grande_Poste_d%27Alger.jpg/500px-La_Grande_Poste_d%27Alger.jpg',
                'gallery' => [
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/4/4d/Alger_Grande-Poste_IMG_0269.JPG/1280px-Alger_Grande-Poste_IMG_0269.JPG', 'caption' => 'Façade néo-mauresque de la Grande Poste'],
                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/bd/Alger_Grande-Poste_IMG_0863.JPG/1280px-Alger_Grande-Poste_IMG_0863.JPG', 'caption' => 'Vue de la Grande Poste d\'Alger'],

                    ['path' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/36/Alger-Grande-Poste-interieur.jpg/500px-Alger-Grande-Poste-interieur.jpg', 'caption' => 'Le hall intérieur richement décoré'],
                ],
                'translations' => [
                    'fr' => [
                        'name' => "Grande Poste d'Alger",
                        'description' => "Édifice néo-mauresque de 1910 au cœur de la capitale, chef-d'œuvre du style « arabisant » colonial et emblème d'Alger.",
                        'history' => "La Grande Poste d'Alger est inaugurée en 1910 sur le boulevard qui porte aujourd'hui le nom de Mohamed Khemisti, en plein cœur d'Alger. Conçue par les architectes Jules Voinot et Marius Toudoire, elle est le plus vaste bâtiment postal d'Algérie et l'un des chefs-d'œuvre du style dit « néo-mauresque » ou « arabisant » que la France coloniale déploya au tournant du XXe siècle.\n\nCe style, qui cherchait à revisiter les traditions architecturales maghrébines et andalouses, se traduit ici par une façade principale à trois arcs monumentaux en fer à cheval, un décor de céramique et de bois sculpté, ainsi qu'un hall intérieur en coupole entièrement recouvert de moucharabiehs, de plâtres ciselés et de mosaïques de zellige, dans une esthétique qui évoque les palais almoravides et almohades.\n\nSymbole architectural fort d'Alger — au même titre que la Casbah, le Monument des Martyrs ou Djamaâ El Djazaïr — la Grande Poste a été le théâtre de plusieurs événements politiques marquants, du soulèvement du 11 décembre 1960 à des manifestations du Hirak en 2019. Depuis 2015, tout en conservant sa fonction postale, une partie du bâtiment abrite un musée dédié à l'histoire de la poste et des télécommunications en Algérie, permettant de découvrir librement ses intérieurs remarquables.",
                        'visit_info' => "L'intérieur peut se visiter librement pendant les horaires d'ouverture ; le hall principal et sa coupole méritent qu'on y prenne le temps de lever les yeux. Le musée retrace l'histoire postale et propose de belles collections de timbres.",
                    ],
                    'ar' => [
                        'name' => 'البريد المركزي بالجزائر العاصمة',
                        'description' => "مبنى بأسلوب المغاربي الحديث يعود إلى 1910 في قلب العاصمة، تحفة من الأسلوب «العربي» الاستعماري ورمز للجزائر العاصمة.",
                        'history' => "دُشّن البريد المركزي بالجزائر العاصمة سنة 1910 على الشارع الذي يحمل اليوم اسم محمد خميستي، في قلب العاصمة. صمّمه المعماريان جول فوانو وماريوس توادوار، وهو أوسع مبنى بريدي في الجزائر وواحد من روائع الأسلوب المعروف بـ«المغاربي الحديث» أو «العربي» الذي طوّرته فرنسا الاستعمارية عند مطلع القرن العشرين.\n\nهذا الأسلوب الذي سعى إلى إعادة قراءة التقاليد المعمارية المغاربية والأندلسية، يتجلى هنا في واجهة رئيسية بثلاثة أقواس نعلية ضخمة، وزخرفة من الخزف والخشب المنحوت، وقاعة داخلية بقبة مغطاة بالكامل بالمشربيات والجبس المنحوت وفسيفساء الزليج، في جمالية تستحضر قصور المرابطين والموحدين.\n\nرمز معماري قوي للجزائر العاصمة - على غرار القصبة ومقام الشهيد وجامع الجزائر - كان البريد المركزي مسرحا لأحداث سياسية بارزة، من انتفاضة 11 ديسمبر 1960 إلى مظاهرات الحراك سنة 2019. ومنذ 2015، مع الاحتفاظ بوظيفته البريدية، يضم جزء من المبنى متحفا مخصصا لتاريخ البريد والاتصالات في الجزائر، يتيح اكتشاف داخله المميز بحرية.",
                        'visit_info' => 'يمكن زيارة الداخل بحرية خلال ساعات العمل؛ تستحق القاعة الرئيسية وقبتها أن ترفع بصرك وتتأمل. ويستعرض المتحف تاريخ البريد ويقدم مجموعات جميلة من الطوابع.',
                    ],
                ],
                'timeline' => [
                    ['year' => 1910, 'fr' => ['period_label' => '1910', 'title' => 'Inauguration', 'description' => "La Grande Poste est inaugurée par les autorités françaises comme siège central de la poste algérienne."], 'ar' => ['period_label' => '1910', 'title' => 'التدشين', 'description' => 'دشّنت السلطات الفرنسية البريد المركزي مقرا مركزيا للبريد الجزائري.']],
                    ['year' => 1960, 'fr' => ['period_label' => '11 décembre 1960', 'title' => "Manifestations pour l'indépendance", 'description' => "Le boulevard face à la Grande Poste est l'un des principaux théâtres des manifestations populaires en faveur du FLN."], 'ar' => ['period_label' => '11 ديسمبر 1960', 'title' => 'مظاهرات من أجل الاستقلال', 'description' => 'كان الشارع المقابل للبريد المركزي أحد المسارح الرئيسية للمظاهرات الشعبية المؤيدة لجبهة التحرير الوطني.']],
                    ['year' => 2015, 'fr' => ['period_label' => '2015', 'title' => "Ouverture du musée de la Poste", 'description' => "Une partie du bâtiment est convertie en musée retraçant l'histoire de la poste et des télécommunications en Algérie."], 'ar' => ['period_label' => '2015', 'title' => 'افتتاح متحف البريد', 'description' => 'حُوّل جزء من المبنى إلى متحف يستعرض تاريخ البريد والاتصالات في الجزائر.']],
                    ['year' => 2019, 'fr' => ['period_label' => '2019', 'title' => 'Hirak', 'description' => "La place devant la Grande Poste devient l'épicentre des manifestations hebdomadaires du mouvement Hirak."], 'ar' => ['period_label' => '2019', 'title' => 'الحراك', 'description' => 'أصبحت الساحة أمام البريد المركزي المركز الرئيسي للمظاهرات الأسبوعية لحركة الحراك.']],
                ],
            ],
        ];
    }
}
