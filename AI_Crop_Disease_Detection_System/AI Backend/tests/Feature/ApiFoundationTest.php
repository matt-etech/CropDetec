<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ApiFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['services.ai.url' => null]);
    }

    public function test_farmer_can_register_and_access_profile(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Matthew Farmer',
            'email' => 'matthew@example.com',
            'phone' => '+263711111111',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'language_preference' => 'en',
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('user.email', 'matthew@example.com')
            ->assertJsonStructure(['token']);

        $token = $response->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('user.email', 'matthew@example.com');
    }

    public function test_farmer_can_update_profile_and_logout(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Profile Farmer',
            'email' => 'profile@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $token = $response->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->patchJson('/api/me', [
                'name' => 'Updated Farmer',
                'phone' => '+263732222222',
                'language_preference' => 'sn',
            ])
            ->assertOk()
            ->assertJsonPath('user.name', 'Updated Farmer')
            ->assertJsonPath('user.language_preference', 'sn');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout')
            ->assertOk();

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/me')
            ->assertUnauthorized();
    }

    public function test_seeded_crops_and_diseases_are_publicly_available(): void
    {
        $this->seed();

        $this->getJson('/api/crops')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Tomato'])
            ->assertJsonFragment(['name' => 'Maize']);

        $this->withHeader('Accept-Language', 'sn')
            ->getJson('/api/crops')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Madomasi'])
            ->assertJsonFragment([
                'name' => 'Madomasi',
                'canonical_name' => 'Tomato',
            ])
            ->assertJsonFragment(['name' => 'Chibage'])
            ->assertJsonFragment(['symptoms' => 'Mavara ebrown akatenderera ane madenderedzwa mukati, mashizha anoita yero, uye mashizha anodonha zvishoma nezvishoma.']);

        $this->getJson('/api/diseases')
            ->assertOk()
            ->assertJsonFragment(['class_label' => 'tomato_early_blight']);
    }

    public function test_crop_library_includes_all_trained_dataset_classes(): void
    {
        $this->seed();

        $response = $this->getJson('/api/crops')
            ->assertOk()
            ->assertJsonCount(6, 'crops');

        foreach ([
            'Maize',
            'Tomato',
            'Potato',
            'Bell Pepper',
            'Soybean',
            'Squash',
        ] as $cropName) {
            $response->assertJsonFragment(['name' => $cropName]);
        }

        foreach ([
            'maize_common_rust',
            'maize_gray_leaf_spot',
            'maize_leaf_blight',
            'pepper_bacterial_spot',
            'potato_early_blight',
            'potato_late_blight',
            'soybean_healthy',
            'squash_powdery_mildew',
            'tomato_early_blight',
            'tomato_late_blight',
        ] as $classLabel) {
            $response->assertJsonFragment(['class_label' => $classLabel]);
        }
    }

    public function test_registration_validation_errors_are_json(): void
    {
        $this->postJson('/api/register', [
            'email' => 'not-an-email',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_registration_rejects_non_zimbabwe_mobile_numbers(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Invalid Phone Farmer',
            'email' => 'invalid-phone@example.com',
            'phone' => '+264811234567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('phone');
    }

    public function test_registration_normalizes_a_local_zimbabwe_mobile_number(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Local Phone Farmer',
            'email' => 'local-phone@example.com',
            'phone' => '077 123 4567',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertCreated()
            ->assertJsonPath('user.phone', '+263771234567');
    }

    public function test_api_responses_include_cors_headers_for_allowed_origins(): void
    {
        $this->withHeader('Origin', 'http://localhost')
            ->getJson('/api/health')
            ->assertOk()
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost');
    }

    public function test_farmer_can_login_upload_diagnosis_and_view_history(): void
    {
        Storage::fake('public');
        $this->seed();

        $this->postJson('/api/register', [
            'name' => 'Diagnosis Farmer',
            'email' => 'diagnosis@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated();

        $login = $this->postJson('/api/login', [
            'email' => 'diagnosis@example.com',
            'password' => 'password123',
        ])->assertOk();

        $token = $login->json('token');

        $diagnosis = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/diagnoses', [
                'crop_id' => 1,
                'image' => $this->fakePngUpload(),
            ])
            ->assertCreated()
            ->assertJsonPath('diagnosis.predicted_label', 'tomato_early_blight')
            ->assertJsonMissingPath('diagnosis.image_path')
            ->assertJsonPath('diagnosis.crop.name', 'Tomato')
            ->assertJsonStructure(['diagnosis' => ['image_url']]);

        $this->assertCount(1, Storage::disk('public')->files('diagnoses'));

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/diagnoses')
            ->assertOk()
            ->assertJsonCount(1, 'diagnoses');
    }

    public function test_diagnosis_upload_uses_configured_ai_service_when_available(): void
    {
        Storage::fake('public');
        Http::fake([
            'https://ai-service.test/predict' => Http::response([
                'predicted_label' => 'maize_leaf_blight',
                'confidence' => 0.73,
            ]),
        ]);
        config(['services.ai.url' => 'https://ai-service.test']);
        $this->seed();

        $token = $this->postJson('/api/register', [
            'name' => 'AI Farmer',
            'email' => 'ai@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/diagnoses', [
                'crop_id' => 2,
                'image' => $this->fakePngUpload(),
            ])
            ->assertCreated()
            ->assertJsonPath('message', 'Diagnosis completed.')
            ->assertJsonPath('diagnosis.predicted_label', 'maize_leaf_blight')
            ->assertJsonPath('diagnosis.confidence', '73.00')
            ->assertJsonMissingPath('diagnosis.image_path');
    }

    public function test_non_leaf_upload_is_rejected_and_not_saved_to_history(): void
    {
        Storage::fake('public');
        Http::fake([
            'https://ai-service.test/predict' => Http::response([
                'detail' => 'No crop leaf was detected. Retake a clear photo with one leaf filling most of the frame.',
            ], 422),
        ]);
        config(['services.ai.url' => 'https://ai-service.test']);
        $this->seed();

        $token = $this->postJson('/api/register', [
            'name' => 'Camera Farmer',
            'email' => 'camera@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/diagnoses', [
                'crop_id' => 1,
                'image' => $this->fakePngUpload(),
            ])
            ->assertUnprocessable()
            ->assertJsonPath('code', 'crop_leaf_not_detected')
            ->assertJsonPath(
                'message',
                'No crop leaf was detected. Retake a clear photo with one leaf filling most of the frame.'
            );

        $this->assertDatabaseCount('diagnoses', 0);
        $this->assertSame([], Storage::disk('public')->files('diagnoses'));
    }

    public function test_api_diagnosis_payload_uses_shona_for_shona_farmer(): void
    {
        Storage::fake('public');
        $this->seed();

        $token = $this->postJson('/api/register', [
            'name' => 'Shona API Farmer',
            'email' => 'shona-api@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'language_preference' => 'sn',
        ])->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/diagnoses', [
                'crop_id' => 1,
                'image' => $this->fakePngUpload(),
            ])
            ->assertCreated()
            ->assertJsonPath('diagnosis.crop.name', 'Madomasi')
            ->assertJsonPath('diagnosis.disease.name', 'Chirwere cheEarly Blight')
            ->assertJsonPath('diagnosis.disease.treatments.0.title', 'Bvisa mashizha ane chirwere')
            ->assertJsonPath('diagnosis.recommendation_snapshot', fn (string $text) => str_contains($text, 'Zviratidzo: Mavara ebrown'))
            ->assertJsonStructure(['diagnosis' => ['image_url']]);

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/diagnoses')
            ->assertOk()
            ->assertJsonPath('diagnoses.0.crop.name', 'Madomasi')
            ->assertJsonPath('diagnoses.0.disease.name', 'Chirwere cheEarly Blight')
            ->assertJsonMissingPath('diagnoses.0.image_path')
            ->assertJsonStructure(['diagnoses' => [['image_url']]]);
    }

    public function test_farmer_cannot_view_another_farmers_diagnosis(): void
    {
        Storage::fake('public');
        $this->seed();

        $first = $this->postJson('/api/register', [
            'name' => 'First Farmer',
            'email' => 'first@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->json('token');

        $second = $this->postJson('/api/register', [
            'name' => 'Second Farmer',
            'email' => 'second@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->json('token');

        $diagnosis = $this->withHeader('Authorization', "Bearer {$first}")
            ->postJson('/api/diagnoses', [
                'crop_id' => 1,
                'image' => $this->fakePngUpload(),
            ])
            ->json('diagnosis');

        $this->withHeader('Authorization', "Bearer {$second}")
            ->getJson('/api/diagnoses/'.$diagnosis['id'])
            ->assertForbidden();
    }

    public function test_admin_can_manage_crop_disease_and_treatment_data(): void
    {
        $adminToken = $this->postJson('/api/register', [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->json('token');

        \App\Models\User::query()
            ->where('email', 'admin@example.com')
            ->update(['role' => 'admin']);

        $crop = $this->withHeader('Authorization', "Bearer {$adminToken}")
            ->postJson('/api/admin/crops', [
                'name' => 'Potato',
                'name_sn' => 'Mbatatisi',
                'scientific_name' => 'Solanum tuberosum',
                'description' => 'A tuber crop for future disease detection.',
                'description_sn' => 'Chirimwa chemudzi chinogona kuwedzerwa mune ramangwana.',
            ])
            ->assertCreated()
            ->assertJsonPath('crop.name', 'Potato')
            ->assertJsonPath('crop.name_sn', 'Mbatatisi')
            ->json('crop');

        $disease = $this->withHeader('Authorization', "Bearer {$adminToken}")
            ->postJson('/api/admin/diseases', [
                'crop_id' => $crop['id'],
                'name' => 'Late Blight',
                'name_sn' => 'Chirwere cheLate Blight',
                'class_label' => 'potato_late_blight',
                'symptoms' => 'Dark water-soaked lesions on leaves.',
                'symptoms_sn' => 'Mavanga matema akaita semvura pamashizha.',
                'prevention' => 'Use clean seed and avoid prolonged leaf wetness.',
                'prevention_sn' => 'Shandisa mbeu yakachena uye dzivisa kunyorova kwemashizha kwenguva refu.',
            ])
            ->assertCreated()
            ->assertJsonPath('disease.class_label', 'potato_late_blight')
            ->assertJsonPath('disease.name_sn', 'Chirwere cheLate Blight')
            ->json('disease');

        $this->withHeader('Authorization', "Bearer {$adminToken}")
            ->postJson('/api/admin/treatments', [
                'disease_id' => $disease['id'],
                'title' => 'Remove affected leaves',
                'title_sn' => 'Bvisa mashizha akabatwa',
                'instructions' => 'Remove infected leaves and seek local agricultural advice.',
                'instructions_sn' => 'Bvisa mashizha ane chirwere uye tsvaga zano renyanzvi yezvekurima.',
                'type' => 'cultural',
            ])
            ->assertCreated()
            ->assertJsonPath('treatment.title', 'Remove affected leaves')
            ->assertJsonPath('treatment.title_sn', 'Bvisa mashizha akabatwa');

        $this->withHeader('Authorization', "Bearer {$adminToken}")
            ->getJson('/api/admin/crops')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Potato']);
    }

    public function test_farmer_cannot_access_admin_endpoints(): void
    {
        $token = $this->postJson('/api/register', [
            'name' => 'Regular Farmer',
            'email' => 'regular@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->json('token');

        $this->withHeader('Authorization', "Bearer {$token}")
            ->getJson('/api/admin/crops')
            ->assertForbidden();
    }

    public function test_admin_web_dashboard_requires_admin_token(): void
    {
        $farmerToken = $this->postJson('/api/register', [
            'name' => 'Web Farmer',
            'email' => 'web-farmer@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->json('token');

        $this->get('/admin?token='.$farmerToken)->assertForbidden();

        $adminToken = $this->postJson('/api/register', [
            'name' => 'Web Admin',
            'email' => 'web-admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->json('token');

        \App\Models\User::query()
            ->where('email', 'web-admin@example.com')
            ->update(['role' => 'admin']);

        $this->get('/admin?token='.$adminToken)
            ->assertOk()
            ->assertSee('Admin Dashboard');
    }

    public function test_browser_user_can_register_login_and_logout(): void
    {
        $this->get('/register')
            ->assertOk()
            ->assertSee('Create account')
            ->assertSee('Sign up')
            ->assertSee('Log in')
            ->assertDontSee('Upload</a>', false);

        $this->post('/register', [
            'name' => 'Browser Farmer',
            'email' => 'browser@example.com',
            'phone' => '+263733333333',
            'language_preference' => 'en',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Hello, Browser Farmer.')
            ->assertSee('Upload')
            ->assertSee('History')
            ->assertDontSee('Sign up');

        $this->post('/logout')->assertRedirect('/login');

        $this->post('/login', [
            'email' => 'browser@example.com',
            'password' => 'password123',
        ])->assertRedirect('/dashboard');
    }

    public function test_browser_dashboard_links_support_farmer_workflow(): void
    {
        Storage::fake('public');
        $this->seed();

        $this->post('/register', [
            'name' => 'Portal Farmer',
            'email' => 'portal@example.com',
            'phone' => '+263774444444',
            'language_preference' => 'en',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Upload image for diagnosis')
            ->assertSee('View diagnosis history')
            ->assertSee('Crop library')
            ->assertSee('Profile');

        $this->get('/diagnose')
            ->assertOk()
            ->assertSee('Upload crop image');

        $this->post('/diagnose', [
            'crop_id' => 1,
            'image' => $this->fakePngUpload(),
        ])->assertRedirect();

        $this->get('/diagnoses')
            ->assertOk()
            ->assertSee('Early Blight');

        $this->get('/crops')
            ->assertOk()
            ->assertSee('Tomato');

        $this->get('/profile')
            ->assertOk()
            ->assertSee('Portal Farmer');
    }

    public function test_farmer_web_routes_redirect_guests_to_login(): void
    {
        foreach (['/dashboard', '/diagnose', '/diagnoses', '/crops', '/profile'] as $path) {
            $this->get($path)->assertRedirect('/login');
        }
    }

    public function test_browser_portal_uses_shona_preferred_language(): void
    {
        $this->post('/register', [
            'name' => 'Shona Farmer',
            'email' => 'shona@example.com',
            'phone' => '+263775555555',
            'language_preference' => 'sn',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Mhoro, Shona Farmer.')
            ->assertSee('Isa mufananidzo kuti uongororwe')
            ->assertSee('Nhoroondo');

        $this->get('/diagnose')
            ->assertOk()
            ->assertSee('Isa mufananidzo wechirimwa')
            ->assertSee('Tanga kuongorora');

        $this->get('/profile')
            ->assertOk()
            ->assertSee('Mutauro waunoda')
            ->assertSee('Chengetedza');
    }

    public function test_crop_library_content_uses_shona_translations_for_shona_users(): void
    {
        $this->seed();

        $this->post('/register', [
            'name' => 'Library Farmer',
            'email' => 'library-shona@example.com',
            'phone' => '+263776666666',
            'language_preference' => 'sn',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->get('/crops')
            ->assertOk()
            ->assertSee('Madomasi')
            ->assertSee('Chibage')
            ->assertSee('Mavara ebrown')
            ->assertSee('Bvisa mashizha ane chirwere');
    }

    public function test_browser_diagnosis_result_uses_shona_recommendation_for_shona_users(): void
    {
        Storage::fake('public');
        $this->seed();

        $this->post('/register', [
            'name' => 'Shona Result Farmer',
            'email' => 'shona-result@example.com',
            'phone' => '+263777777777',
            'language_preference' => 'sn',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->post('/diagnose', [
            'crop_id' => 1,
            'image' => $this->fakePngUpload(),
        ])->assertRedirect();

        $this->get('/diagnoses/1')
            ->assertOk()
            ->assertSee('Madomasi')
            ->assertSee('Chirwere cheEarly Blight')
            ->assertSee('Zviratidzo')
            ->assertSee('Bvisa mashizha ane chirwere');
    }

    private function fakePngUpload(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'leaf');
        file_put_contents(
            $path,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==')
        );

        return new UploadedFile($path, 'leaf.png', 'image/png', null, true);
    }
}
