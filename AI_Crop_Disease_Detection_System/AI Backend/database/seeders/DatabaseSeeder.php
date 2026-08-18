<?php

namespace Database\Seeders;

use App\Models\Crop;
use App\Models\Disease;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $demoFarmer = User::query()->firstOrNew(['email' => 'farmer@example.com']);
        $demoFarmer->fill([
            'name' => 'Demo Farmer',
            'phone' => '+263700000000',
            'password' => 'password123',
            'role' => 'farmer',
            'language_preference' => 'en',
        ]);
        $demoFarmer->save();

        $tomato = Crop::query()->firstOrCreate(
            ['name' => 'Tomato'],
            [
                'scientific_name' => 'Solanum lycopersicum',
                'description' => 'A high-priority vegetable crop for early disease detection trials.',
                'name_sn' => 'Madomasi',
                'description_sn' => 'Chirimwa chemuriwo chakakosha chinoshandiswa pakuedza kuona zvirwere nekukurumidza.',
            ],
        );
        $tomato->update([
            'name_sn' => 'Madomasi',
            'description_sn' => 'Chirimwa chemuriwo chakakosha chinoshandiswa pakuedza kuona zvirwere nekukurumidza.',
        ]);

        $maize = Crop::query()->firstOrCreate(
            ['name' => 'Maize'],
            [
                'scientific_name' => 'Zea mays',
                'description' => 'A staple crop included for later model expansion.',
                'name_sn' => 'Chibage',
                'description_sn' => 'Chirimwa chikuru chekudya chakaiswa kuti chizowedzerwa mumhando yeAI mune ramangwana.',
            ],
        );
        $maize->update([
            'name_sn' => 'Chibage',
            'description_sn' => 'Chirimwa chikuru chekudya chakaiswa kuti chizowedzerwa mumhando yeAI mune ramangwana.',
        ]);

        $earlyBlight = Disease::query()->firstOrCreate(
            ['class_label' => 'tomato_early_blight'],
            [
                'crop_id' => $tomato->id,
                'name' => 'Early Blight',
                'name_sn' => 'Chirwere cheEarly Blight',
                'description' => 'A fungal tomato disease that commonly appears as dark leaf spots.',
                'description_sn' => 'Chirwere chefungus pamadomasi chinowanzoonekwa nemavara matema pamashizha.',
                'symptoms' => 'Brown circular spots with concentric rings, yellowing leaves, and gradual leaf drop.',
                'symptoms_sn' => 'Mavara ebrown akatenderera ane madenderedzwa mukati, mashizha anoita yero, uye mashizha anodonha zvishoma nezvishoma.',
                'prevention' => 'Remove infected leaves, rotate crops, avoid overhead watering, and improve air circulation.',
                'prevention_sn' => 'Bvisa mashizha ane chirwere, chinjanisa zvirimwa, dzivisa kudiridza kubva pamusoro, uye wedzera kufefetera kwemhepo.',
            ],
        );
        $earlyBlight->update([
            'name_sn' => 'Chirwere cheEarly Blight',
            'description_sn' => 'Chirwere chefungus pamadomasi chinowanzoonekwa nemavara matema pamashizha.',
            'symptoms_sn' => 'Mavara ebrown akatenderera ane madenderedzwa mukati, mashizha anoita yero, uye mashizha anodonha zvishoma nezvishoma.',
            'prevention_sn' => 'Bvisa mashizha ane chirwere, chinjanisa zvirimwa, dzivisa kudiridza kubva pamusoro, uye wedzera kufefetera kwemhepo.',
        ]);

        $earlyBlight->treatments()->firstOrCreate(
            ['title' => 'Remove infected leaves'],
            [
                'title_sn' => 'Bvisa mashizha ane chirwere',
                'instructions' => 'Carefully remove affected leaves and dispose of them away from the field.',
                'instructions_sn' => 'Bvisa mashizha abatwa nechirwere zvine hungwaru uye uarase kure nemunda.',
                'type' => 'cultural',
            ],
        )->update([
            'title_sn' => 'Bvisa mashizha ane chirwere',
            'instructions_sn' => 'Bvisa mashizha abatwa nechirwere zvine hungwaru uye uarase kure nemunda.',
        ]);

        $earlyBlight->treatments()->firstOrCreate(
            ['title' => 'Apply recommended fungicide'],
            [
                'title_sn' => 'Shandisa mushonga wefungus wakakurudzirwa',
                'instructions' => 'Use an approved fungicide according to local agricultural guidance and label instructions.',
                'instructions_sn' => 'Shandisa mushonga wefungus wakatenderwa uchitevedza zano renyanzvi dzezvekurima nemirayiridzo iri pachiratidzo chemushonga.',
                'type' => 'chemical',
            ],
        )->update([
            'title_sn' => 'Shandisa mushonga wefungus wakakurudzirwa',
            'instructions_sn' => 'Shandisa mushonga wefungus wakatenderwa uchitevedza zano renyanzvi dzezvekurima nemirayiridzo iri pachiratidzo chemushonga.',
        ]);

        $leafBlight = Disease::query()->firstOrCreate(
            ['class_label' => 'maize_leaf_blight'],
            [
                'crop_id' => $maize->id,
                'name' => 'Leaf Blight',
                'name_sn' => 'Chirwere cheLeaf Blight',
                'description' => 'A maize disease that causes elongated lesions and reduced photosynthesis.',
                'description_sn' => 'Chirwere chechibage chinokonzera mavanga marefu pamashizha uye chinoderedza kugadzirwa kwechikafu nechirimwa.',
                'symptoms' => 'Long grey-green or tan lesions on leaves, often spreading in humid conditions.',
                'symptoms_sn' => 'Mavanga marefu ane ruvara rwegirini-grey kana tan pamashizha, kazhinji achipararira kana kune hunyoro.',
                'prevention' => 'Use tolerant varieties, rotate crops, manage residue, and monitor fields regularly.',
                'prevention_sn' => 'Shandisa mhando dzinotsungirira chirwere, chinjanisa zvirimwa, chengetedza zvisaririra zvemumunda, uye ongorora minda nguva dzose.',
            ],
        );
        $leafBlight->update([
            'name_sn' => 'Chirwere cheLeaf Blight',
            'description_sn' => 'Chirwere chechibage chinokonzera mavanga marefu pamashizha uye chinoderedza kugadzirwa kwechikafu nechirimwa.',
            'symptoms_sn' => 'Mavanga marefu ane ruvara rwegirini-grey kana tan pamashizha, kazhinji achipararira kana kune hunyoro.',
            'prevention_sn' => 'Shandisa mhando dzinotsungirira chirwere, chinjanisa zvirimwa, chengetedza zvisaririra zvemumunda, uye ongorora minda nguva dzose.',
        ]);

        $leafBlight->treatments()->firstOrCreate(
            ['title' => 'Improve field sanitation'],
            [
                'title_sn' => 'Vandudza kuchena kwemunda',
                'instructions' => 'Remove heavily infected residues and avoid planting susceptible maize repeatedly in the same field.',
                'instructions_sn' => 'Bvisa zvisaririra zvine chirwere zvakanyanya uye dzivisa kudyara chibage chiri nyore kubatwa nechirwere kakawanda pamunda mumwe chete.',
                'type' => 'cultural',
            ],
        )->update([
            'title_sn' => 'Vandudza kuchena kwemunda',
            'instructions_sn' => 'Bvisa zvisaririra zvine chirwere zvakanyanya uye dzivisa kudyara chibage chiri nyore kubatwa nechirwere kakawanda pamunda mumwe chete.',
        ]);

        $this->seedZimbabweExpansionCrops();
    }

    private function seedZimbabweExpansionCrops(): void
    {
        $tomato = Crop::query()->firstWhere('name', 'Tomato');
        $maize = Crop::query()->firstWhere('name', 'Maize');
        $potato = $this->upsertCrop(
            'Potato',
            'Solanum tuberosum',
            'A widely grown food and cash crop that benefits from early blight and late blight monitoring.',
            'Mbatatisi',
            'Chirimwa chekudya nekutengesa chinorimwa zvakanyanya uye chinobatsirwa nekuonekwa kwechirwere nekukurumidza.',
        );
        $pepper = $this->upsertCrop(
            'Bell Pepper',
            'Capsicum annuum',
            'A vegetable crop grown for fresh markets and vulnerable to leaf spot diseases.',
            'Mhiripiri hombe',
            'Chirimwa chemuriwo chinorimwa kutengeswa chiri nyore kubatwa nezvirwere zvemashizha.',
        );
        $soybean = $this->upsertCrop(
            'Soybean',
            'Glycine max',
            'A protein and oil crop used in rotation systems and livestock feed value chains.',
            'Soya bhinzi',
            'Chirimwa chine mapuroteni nemafuta chinoshandiswa mukuchinjanisa zvirimwa uye mukudya kwezvipfuyo.',
        );
        $squash = $this->upsertCrop(
            'Squash',
            'Cucurbita pepo',
            'A cucurbit vegetable crop included for powdery mildew detection practice.',
            'Manhanga',
            'Chirimwa chemhuri yemanhanga chakaiswa pakudzidzira kuona powdery mildew.',
        );

        if ($maize) {
            $this->upsertDisease(
                $maize,
                'Gray Leaf Spot',
                'Chirwere cheGray Leaf Spot',
                'maize_gray_leaf_spot',
                'A fungal maize disease that creates rectangular gray to tan lesions on leaves.',
                'Chirwere chefungus pachibage chinoita mavanga marefu ane ruvara rwegrey kana tan pamashizha.',
                'Long rectangular gray-brown leaf lesions that can join and dry large leaf areas.',
                'Mavanga marefu ane grey-brown pamashizha anogona kusangana oomesera nzvimbo huru dzeshizha.',
                'Use resistant varieties, rotate crops, and bury or remove infected residue.',
                'Shandisa mhando dzinotsungirira chirwere, chinjanisa zvirimwa, uye viga kana kubvisa zvisaririra zvine chirwere.',
                [
                    [
                        'title' => 'Manage maize residue',
                        'title_sn' => 'Chengetedza zvisaririra zvechibage',
                        'instructions' => 'Remove or incorporate infected maize residue to reduce spores before the next season.',
                        'instructions_sn' => 'Bvisa kana kuviga zvisaririra zvechibage zvine chirwere kuti uderedze utachiona mwaka usati watanga.',
                        'type' => 'cultural',
                    ],
                ],
            );

            $this->upsertDisease(
                $maize,
                'Common Rust',
                'Ngura yechibage',
                'maize_common_rust',
                'A maize disease that forms rust-colored pustules on leaf surfaces.',
                'Chirwere chechibage chinoita mapundu ane ruvara rwengura pamashizha.',
                'Small reddish-brown pustules on both sides of leaves, sometimes surrounded by yellow tissue.',
                'Mapundu madiki ane ruvara rutsvuku-brown kumativi ose emashizha, dzimwe nguva aine yero yakapoteredza.',
                'Plant tolerant varieties and monitor fields early in cool, humid conditions.',
                'Dyara mhando dzinotsungirira chirwere uye ongorora minda pakutanga kana kuchitonhorera uye kune hunyoro.',
                [
                    [
                        'title' => 'Plant tolerant varieties',
                        'title_sn' => 'Dyara mhando dzinotsungirira chirwere',
                        'instructions' => 'Choose locally recommended maize varieties with rust tolerance where available.',
                        'instructions_sn' => 'Sarudza mhando dzechibage dzinokurudzirwa munharaunda dzinotsungirira ngura kana dziripo.',
                        'type' => 'cultural',
                    ],
                ],
            );
        }

        if ($tomato) {
            $this->upsertDisease(
                $tomato,
                'Late Blight',
                'Chirwere cheLate Blight pamadomasi',
                'tomato_late_blight',
                'A serious tomato disease that can spread quickly during cool, wet weather.',
                'Chirwere chakakomba pamadomasi chinogona kupararira nekukurumidza kana kuchitonhorera uye kune hunyoro.',
                'Water-soaked leaf lesions, dark patches, white mold under leaves, and rapid plant decline.',
                'Mavanga akaita semvura pamashizha, mavara matema, mold chena pasi pemashizha, uye kuderera kwechirimwa nekukurumidza.',
                'Remove infected plants, avoid overhead watering, improve airflow, and seek local fungicide guidance.',
                'Bvisa zvirimwa zvine chirwere, dzivisa kudiridza kubva pamusoro, wedzera kufefetera, uye tsvaga zano remishonga munharaunda.',
                [
                    [
                        'title' => 'Act quickly',
                        'title_sn' => 'Tora matanho nekukurumidza',
                        'instructions' => 'Late blight spreads fast. Separate affected plants and contact an extension officer.',
                        'instructions_sn' => 'Late blight inopararira nekukurumidza. Siyanisa zvirimwa zvabatwa uye bata nyanzvi yezvekurima.',
                        'type' => 'advisory',
                    ],
                ],
            );
        }

        $this->upsertDisease(
            $potato,
            'Early Blight',
            'Chirwere cheEarly Blight pamatatisi',
            'potato_early_blight',
            'A potato fungal disease that causes target-like leaf spots and weakens the crop canopy.',
            'Chirwere chefungus pamatatisi chinoita mavara akaita semadenderedzwa pamashizha uye chinoderedza simba rechirimwa.',
            'Dark circular leaf spots with rings, yellowing around spots, and lower leaves drying first.',
            'Mavara matema akatenderera ane madenderedzwa, yero yakapoteredza mavara, uye mashizha epasi anotanga kuoma.',
            'Rotate fields, keep plants vigorous, remove infected leaves, and avoid wet foliage.',
            'Chinjanisa minda, chengetedza zvirimwa zvine simba, bvisa mashizha ane chirwere, uye dzivisa mashizha akanyorova.',
            [
                [
                    'title' => 'Remove infected foliage',
                    'title_sn' => 'Bvisa mashizha ane chirwere',
                    'instructions' => 'Remove badly infected leaves and dispose of them away from the production area.',
                    'instructions_sn' => 'Bvisa mashizha abatwa zvakanyanya uye uarase kure nenzvimbo yekurima.',
                    'type' => 'cultural',
                ],
            ],
        );

        $this->upsertDisease(
            $potato,
            'Late Blight',
            'Chirwere cheLate Blight pamatatisi',
            'potato_late_blight',
            'A destructive potato disease that can spread quickly in cool, wet conditions.',
            'Chirwere chine ngozi pamatatisi chinopararira nekukurumidza kana kuchitonhorera uye kune hunyoro.',
            'Water-soaked leaf lesions, white mold under leaves in humid weather, and rapid leaf collapse.',
            'Mavanga akaita semvura pamashizha, mold chena pasi pemashizha kana kune hunyoro, uye mashizha anokurumidza kuwira pasi.',
            'Use clean seed, remove volunteer plants, improve airflow, and seek urgent extension advice.',
            'Shandisa mbeu yakachena, bvisa zvirimwa zvazvimera, wedzera kufefetera, uye tsvaga zano renyanzvi nekukasika.',
            [
                [
                    'title' => 'Seek urgent advice',
                    'title_sn' => 'Tsvaga zano nekukasika',
                    'instructions' => 'Late blight spreads fast. Contact an extension officer for locally approved control options.',
                    'instructions_sn' => 'Late blight inopararira nekukurumidza. Bata nyanzvi yezvekurima kuti uwane nzira dzakakodzera munharaunda.',
                    'type' => 'advisory',
                ],
            ],
        );

        $this->upsertDisease(
            $pepper,
            'Bacterial Spot',
            'Bacterial Spot pamhiripiri',
            'pepper_bacterial_spot',
            'A bacterial pepper disease that damages leaves and can reduce fruit quality.',
            'Chirwere chebhakitiriya pamhiripiri chinokuvadza mashizha uye chinoderedza kunaka kwemichero.',
            'Small water-soaked spots that turn brown or black, sometimes with yellow halos.',
            'Mavara madiki akaita semvura anozoita brown kana matema, dzimwe nguva aine yero yakapoteredza.',
            'Use clean seed, avoid overhead irrigation, remove infected debris, and rotate crops.',
            'Shandisa mbeu yakachena, dzivisa kudiridza kubva pamusoro, bvisa marara ane chirwere, uye chinjanisa zvirimwa.',
            [
                [
                    'title' => 'Avoid wet leaves',
                    'title_sn' => 'Dzivisa mashizha akanyorova',
                    'instructions' => 'Water at the base of plants and avoid handling plants while leaves are wet.',
                    'instructions_sn' => 'Diridza pazasi pezvirimwa uye dzivisa kubata zvirimwa mashizha achiri manyoro.',
                    'type' => 'cultural',
                ],
            ],
        );

        $this->upsertDisease(
            $soybean,
            'Healthy Leaf',
            'Shizha resoya rine hutano',
            'soybean_healthy',
            'Healthy soybean leaves included so the AI can learn a non-disease class for soybean.',
            'Mashizha esoya ane hutano akaiswa kuti AI idzidze kusiyanisa chirwere nekusava nechirwere pasoya.',
            'Leaves appear green and even, without disease lesions or abnormal yellowing.',
            'Mashizha anoita girini uye akajairika, asina mavanga echirwere kana kuyero kusina kujairika.',
            'Continue scouting and keep records so disease changes can be noticed early.',
            'Ramba uchiongorora uye chengeta marekodhi kuitira kuti shanduko dzechirwere dzionekwe nekukurumidza.',
            [
                [
                    'title' => 'Keep monitoring',
                    'title_sn' => 'Ramba uchiongorora',
                    'instructions' => 'Inspect soybean leaves regularly, especially after humid weather.',
                    'instructions_sn' => 'Ongorora mashizha esoya nguva dzose, zvikuru mushure memamiriro ane hunyoro.',
                    'type' => 'monitoring',
                ],
            ],
        );

        $this->upsertDisease(
            $squash,
            'Powdery Mildew',
            'Powdery Mildew pamanhanga',
            'squash_powdery_mildew',
            'A common cucurbit disease that leaves white powdery patches on leaves.',
            'Chirwere chinowanzoitika pamanhanga chinoisa mavara machena akaita seupfu pamashizha.',
            'White powdery growth on leaf surfaces, yellowing, and early leaf drying.',
            'Chinhu chichena chakaita seupfu pamusoro pemashizha, kuyero, uye kuoma kwemashizha nekukurumidza.',
            'Improve spacing, remove badly affected leaves, and avoid unnecessary leaf wetness.',
            'Wedzera nzvimbo pakati pezvirimwa, bvisa mashizha abatwa zvakanyanya, uye dzivisa kunyorovesa mashizha zvisina basa.',
            [
                [
                    'title' => 'Improve spacing',
                    'title_sn' => 'Wedzera nzvimbo pakati pezvirimwa',
                    'instructions' => 'Give squash plants enough spacing and airflow to slow powdery mildew spread.',
                    'instructions_sn' => 'Ipa manhanga nzvimbo yakakwana nekufefetera kwemhepo kuti powdery mildew isapararire nekukurumidza.',
                    'type' => 'cultural',
                ],
            ],
        );
    }

    private function upsertCrop(
        string $name,
        string $scientificName,
        string $description,
        string $nameSn,
        string $descriptionSn
    ): Crop {
        $crop = Crop::query()->firstOrCreate(['name' => $name]);
        $crop->update([
            'scientific_name' => $scientificName,
            'description' => $description,
            'name_sn' => $nameSn,
            'description_sn' => $descriptionSn,
            'is_active' => true,
        ]);

        return $crop;
    }

    private function upsertDisease(
        Crop $crop,
        string $name,
        string $nameSn,
        string $classLabel,
        string $description,
        string $descriptionSn,
        string $symptoms,
        string $symptomsSn,
        string $prevention,
        string $preventionSn,
        array $treatments
    ): Disease {
        $disease = Disease::query()->firstOrNew(['class_label' => $classLabel]);
        $disease->fill([
            'crop_id' => $crop->id,
            'name' => $name,
            'name_sn' => $nameSn,
            'description' => $description,
            'description_sn' => $descriptionSn,
            'symptoms' => $symptoms,
            'symptoms_sn' => $symptomsSn,
            'prevention' => $prevention,
            'prevention_sn' => $preventionSn,
            'is_active' => true,
        ]);
        $disease->save();

        foreach ($treatments as $treatmentData) {
            $treatment = $disease->treatments()->firstOrNew(['title' => $treatmentData['title']]);
            $treatment->fill($treatmentData);
            $treatment->save();
        }

        return $disease;
    }
}
