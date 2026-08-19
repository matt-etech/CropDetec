# CropDetec System Test Report

Date: 19 August 2026

Source requirements: `C:\Users\user\Documents\Matthew Proj\Matthew Shambare Chapter 5.docx`

## Executive result

The implemented and automatable CropDetec checks pass, including the live Laravel → MySQL → Python AI workflow, held-out model evaluation and Android voice fallback tests. Full field certification still requires physical-device camera/audio checks and User Acceptance Testing with real farmers.

## Automated test evidence

| Area | Result | Evidence |
|---|---:|---|
| Laravel unit and feature suite | PASS | 24 tests, 174 assertions |
| Flutter static analysis | PASS | No issues found |
| Flutter widget/unit suite | PASS | 4 tests passed |
| Android debug APK build | PASS | Native Kotlin TTS bridge compiled |
| Python service syntax compilation | PASS | Application and evaluation scripts compiled |
| MobileNetV2 held-out evaluation | PASS | 92.00% accuracy; 91.90% macro F1 on 450 images |
| Live API health | PASS | HTTP 200, 792 ms |
| Live crop/database retrieval | PASS | HTTP 200, 175 ms, 15,912-byte response |
| GitHub Pages frontend | PASS | HTTP 200, 443 ms |
| Direct AI prediction on Oracle VM | PASS | HTTP 200, 180 ms |

## Live end-to-end test

The test used the deployed API at `https://92.4.150.53.sslip.io/api` and created one disposable test account and one diagnosis record.

| Test | Result | Observed time |
|---|---:|---:|
| Register new user | PASS | 769 ms |
| Reject incorrect password | PASS | 600 ms |
| Login with valid credentials | PASS | 513 ms |
| Upload image and receive AI prediction | PASS | 4,093 ms end-to-end |
| Prediction payload contains disease | PASS | `pepper_bacterial_spot` |
| Retrieve diagnosis history from MySQL | PASS | 410 ms |
| History contains uploaded diagnosis | PASS | 1 record |
| Logout | PASS | 280 ms |
| Reject old token after logout | PASS | HTTP 401 |

## Chapter Five test matrix

### Unit tests

| Requirement | Status | Basis |
|---|---:|---|
| User registration | PASS | Laravel automated test and live API |
| User login | PASS | Laravel automated test and live API |
| Incorrect-password validation | PASS | Laravel automated test and live API |
| Image upload | PASS | Laravel automated test and live API |
| AI prediction | PASS | Laravel integration test and live Oracle AI prediction |
| Diagnosis history | PASS | Laravel automated test and live persisted history |
| Voice assistant | PASS (AUTOMATED) | Language support, Shona fallback and failure paths pass; audibility remains a device check |

### Integration tests

| Requirement | Status | Basis |
|---|---:|---|
| Flutter ↔ Laravel | PARTIAL | Production Flutter URL configuration and live API are valid; a physical-device UI run is still required |
| Laravel ↔ MySQL | PASS | Live registration and diagnosis were retrieved after storage |
| Laravel ↔ Python AI | PASS | Live diagnosis returned the model label |
| Flutter ↔ diagnosis history | PARTIAL | API and Flutter client code pass analysis; physical-device UI confirmation remains |
| Administrator ↔ database CRUD | PASS (AUTOMATED) | Laravel feature test covers crop, disease, and treatment administration |

### System tests

| Requirement | Status | Basis |
|---|---:|---|
| Registration | PASS | Live API |
| Login | PASS | Live API |
| Capture image/camera opens | MANUAL REQUIRED | Requires camera hardware and Android permission interaction |
| Upload image | PASS | Live API |
| AI disease identification | PASS | Live API and direct AI endpoint |
| Treatment recommendation | PASS (AUTOMATED) | Laravel feature tests assert returned treatment/recommendation data |
| Diagnosis history | PASS | Live API |
| Logout/session termination | PASS | Live API; old token returned HTTP 401 |

## Performance comparison

| Chapter target | Observed | Status |
|---|---:|---:|
| Login: 0.5 s | 0.513 s | NEAR TARGET (13 ms over in one internet sample) |
| Image upload: 0.1 s | Not independently separable from prediction in the current API | NOT PROVEN |
| AI prediction: 2.5 s | 0.180 s on the Oracle VM | PASS |
| Diagnosis retrieval: 2.5 s | 0.410 s over the internet | PASS |

The 4.093-second upload-and-predict result includes internet transfer, Laravel processing, database work, and the AI call. A repeatable performance claim should use multiple runs and report median and percentile values rather than a single sample.

## Evidence still required before full certification

1. Run camera capture, image selection, audible English voice, and audible Shona voice on a physical Android phone.
2. Conduct User Acceptance Testing with at least five representative farmers using `docs/PHYSICAL_DEVICE_AND_UAT_CHECKLIST.md` and preserve the completed results.
3. Repeat performance measurements over multiple runs and report median and percentile values if required.

## Corrections made during testing

- Updated deprecated Flutter dropdown APIs and removed all analyzer findings.
- Made the application API client injectable for deterministic testing.
- Repaired the stale widget test so it validates secure-session restoration and the unauthenticated login screen.
- Evaluated the model on 450 held-out images and preserved metrics plus the confusion matrix.
- Added Android speech-language availability checks, localized English fallback messaging and three automated TTS tests.
- Added a physical-device and farmer UAT checklist with measurable acceptance criteria.
