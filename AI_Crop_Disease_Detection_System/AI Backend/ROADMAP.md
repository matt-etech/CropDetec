# AI Crop Disease Detection System Roadmap

## Source Notes

- [x] Chapter Three reviewed for methodology, dataset preparation, MobileNetV2 model development, Flutter mobile app, Laravel backend, MySQL database, Python AI service, integration, deployment, and testing.
- [x] Chapter Four reviewed for intended implementation screens, Laravel API backend, MySQL tables, Python/TensorFlow/OpenCV inference service, REST integration, and administrator dashboard.
- [x] Chapter One PDF comments reviewed where extractable: align objectives/questions, specify AI technique, list exact crops, keep mobile-first scope, and strengthen justification.
- [x] Current repository inspected: Laravel 13.8/PHP 8.3 skeleton with default migrations, minimal routes, empty frontend JavaScript, Tailwind/Vite installed, and no implemented domain models/API yet.
- [x] Reconcile thesis mention of Laravel 12 with actual Laravel 13 workspace.

## Product Scope To Confirm First

- [x] Lock the first supported crop list and disease classes.
- [x] Confirm the first AI technique as MobileNetV2 image classification.
- [x] Rewrite the implementation scope as a mobile-first farmer tool with optional admin web dashboard.
- [x] Define one objective, one research question, and one measurable success criterion for each major capability.
- [x] Add a user-facing disclaimer that predictions support, but do not replace, agricultural professional advice.

## Frontend Roadmap

### Phase 1: Mobile App Foundation

- [x] Create a Flutter app as the primary frontend.
- [x] Establish project structure: `screens`, `widgets`, `models`, `services`, `providers` or state layer, `theme`, and `localization`.
- [x] Implement app theme with agricultural styling, high contrast, large tap targets, and readable typography.
- [x] Add environment configuration for API base URL and storage URL.
- [x] Add shared API client with token persistence, error handling, loading states, and retry-friendly networking.

### Phase 2: Authentication Screens

- [x] Build splash screen that checks stored auth token and routes users to login or dashboard.
- [x] Build registration screen with full name, email, phone number, password, and password confirmation.
- [x] Build login screen with validation, error messages, password visibility toggle, and token storage.
- [x] Build logout flow from profile/settings.
- [x] Add basic offline/no-network messaging.

### Phase 3: Farmer Workflow

- [x] Build dashboard with direct access to disease detection, diagnosis history, profile, language/settings, and help/disclaimer.
- [x] Build crop disease detection screen with camera capture and gallery selection.
- [x] Validate image type and size before upload.
- [x] Show selected image preview, upload progress, and prediction loading state.
- [x] Build diagnosis results screen showing crop, predicted disease, confidence score, symptoms, treatment recommendations, prevention measures, and date.
- [x] Add low-confidence handling: ask user to retake photo or consult an extension officer.
- [x] Save successful diagnosis to history automatically through the backend.

### Phase 4: Accessibility And Localization

- [x] Add English and Shona text resources.
- [x] Add text-to-speech playback for diagnosis results, treatment, and prevention guidance.
- [x] Add clear empty, error, and loading states for all major screens.
- [x] Keep navigation shallow and action buttons obvious.

### Phase 5: History And Profile

- [x] Build diagnosis history list with image thumbnail, predicted disease, confidence, and date.
- [x] Add diagnosis detail view.
- [x] Add filtering by crop, disease, and date if the first dataset has multiple crops.
- [x] Build profile screen for viewing and updating name, phone number, and language preference.

### Phase 6: Admin Frontend

- [x] Decide whether admin is Flutter-only or Laravel Blade/web.
- [x] For quickest start, implement admin as Laravel Blade pages or a small Vite-backed web dashboard.
- [x] Build admin login/role protection.
- [x] Build CRUD screens for crops, diseases, symptoms, treatments, prevention measures, and users.
- [x] Build diagnosis monitoring page with recent diagnoses, confidence distribution, and low-confidence cases.

## Backend Roadmap

### Phase 1: Laravel API Foundation

- [x] Add API routes under `routes/api.php`.
- [x] Install/configure Laravel Sanctum or equivalent token authentication for mobile clients.
- [x] Create API response conventions for success, validation errors, auth errors, and server errors.
- [x] Configure CORS for the Flutter/mobile client and any local admin frontend.
- [x] Add request validation classes for auth, profile updates, image upload, and admin CRUD.

### Phase 2: Database And Domain Models

- [x] Create migrations and models for required tables.
- [x] Create/extend `users`.
- [x] Create `crops`.
- [x] Create `diseases`.
- [x] Create `treatments`.
- [x] Create `diagnoses`.
- [x] Create `admins` or implement role-based users.
- [x] Prefer role-based users unless the academic write-up requires a separate administrators table.
- [x] Store uploaded diagnosis images in Laravel storage with private or signed access where appropriate.
- [x] Define crop-to-diseases relationship.
- [x] Define disease-to-crop relationship.
- [x] Define disease-to-treatments relationship.
- [x] Define user-to-diagnoses relationship.
- [x] Define diagnosis-to-user/crop/disease relationships.
- [x] Seed initial crops, diseases, treatments, symptoms, and prevention guidance.

### Phase 3: Core API Endpoints

- [x] `POST /api/register`
- [x] `POST /api/login`
- [x] `POST /api/logout`
- [x] `GET /api/me`
- [x] `PATCH /api/me`
- [x] `GET /api/crops`
- [x] `GET /api/diseases`
- [x] `POST /api/diagnoses`
- [x] `GET /api/diagnoses`
- [x] `GET /api/diagnoses/{diagnosis}`
- [x] Admin CRUD endpoints for crops, diseases, treatments, users, and diagnosis review.

### Phase 4: AI Service Integration

- [x] Build a separate Python inference service skeleton.
- [x] Expose a prediction endpoint accepting an uploaded image and returning predicted class and confidence score.
- [x] In Laravel, create an AI client service responsible for forwarding the image and handling service errors/timeouts.
- [x] Map AI class labels to database disease records.
- [x] Persist each diagnosis with image path, predicted label, confidence score, crop, disease, and recommendation snapshot.
- [x] Add fallback handling when the AI service is unavailable or confidence is too low.

### Phase 5: Security And Privacy

- [x] Hash all passwords with Laravel defaults.
- [x] Enforce authentication middleware on protected routes.
- [x] Add role-based authorization for admin operations.
- [x] Validate uploaded images by MIME type, extension, size, and dimensions.
- [x] Limit upload size and prediction request rate.
- [x] Use HTTPS in deployment.
- [x] Avoid exposing raw storage paths or private user data.
- [x] Add privacy wording for personal details, diagnosis history, and uploaded crop images.

### Phase 6: Testing And Quality

- [x] Add backend unit tests for models, validation, auth, and services.
- [x] Add feature tests for registration, login, diagnosis upload, diagnosis history, and admin authorization.
- [x] Add integration tests with a mocked AI service.
- [x] Evaluate AI outside Laravel: accuracy, precision, recall, F1-score, and confusion matrix.
- [x] Confirm login response is under 2 seconds.
- [x] Confirm image upload is under 3 seconds under normal network conditions.
- [x] Confirm disease prediction is under 5 seconds.
- [x] Confirm diagnosis retrieval is under 2 seconds.

## AI/Data Roadmap

- [x] Confirm crops and disease labels before training.
- [x] Collect or select dataset images for healthy and diseased leaves.
- [x] Clean duplicates, corrupted images, and incorrect labels.
- [x] Split data into training, validation, and testing sets.
- [x] Apply preprocessing: resize, normalize, and augment images.
- [x] Train MobileNetV2 baseline.
- [x] Evaluate accuracy, precision, recall, F1-score, and confusion matrix.
- [x] Export model for inference.
- [x] Build Python prediction service.
- [x] Version model artifacts and label mappings.

## Suggested First Sprint

### Week 1

- [x] Align scope: crops, diseases, objectives, research questions, and success criteria.
- [x] Create Laravel API route file, Sanctum auth, and base API response format.
- [x] Create migrations/models for crops, diseases, treatments, and diagnoses.
- [x] Seed one crop with a small set of disease/treatment records.
- [x] Scaffold Flutter app and implement theme, routing, splash, login, and registration screens.

### Week 2

- [x] Implement backend auth endpoints and profile endpoint.
- [x] Implement crop/disease read endpoints.
- [x] Implement diagnosis upload endpoint with temporary mocked AI response.
- [x] Build Flutter dashboard, image picker/camera flow, and diagnosis result screen.
- [x] Add diagnosis history endpoint and Flutter history screen.

### Week 3

- [x] Build Python inference service skeleton.
- [x] Connect Laravel to AI service with timeouts and error handling.
- [x] Replace mocked prediction with real or placeholder model response.
- [x] Add admin CRUD for crops, diseases, and treatments.
- [x] Add tests for auth, diagnosis upload, history, and admin access.

### Week 4

- [x] Add Shona/English localization.
- [x] Add text-to-speech for results.
- [x] Add low-confidence warning and professional-advice disclaimer.
- [x] Run integration testing across Flutter, Laravel, MySQL, and Python service.
- [x] Prepare screenshots/diagrams for Chapter Four figures.

## Immediate Next Tasks In This Repository

- [x] Add `routes/api.php` and bootstrap API routing if Laravel 13 skeleton does not expose it yet.
- [x] Add Sanctum or Laravel token auth for mobile API access.
- [x] Create domain migrations and Eloquent models.
- [x] Create seeders for crops, diseases, symptoms, treatments, and prevention recommendations.
- [x] Create diagnosis upload controller with image validation and mocked prediction.
- [x] Add feature tests for the first API endpoints.
- [x] Decide where the Flutter app will live: a separate repository, a sibling folder, or a `mobile/` folder in this workspace.
