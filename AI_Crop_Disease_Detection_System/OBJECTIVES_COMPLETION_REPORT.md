# CropDetec Objectives Completion Review

Date: 19 August 2026

Source requirements: `C:\Users\user\Documents\Matthew Proj\Matthew_Shambare_Chapter_1_Corrected (1).docx`

## Overall conclusion

All three Chapter One objectives are implemented and supported by code-level evidence. Objective 1 now has preserved held-out evaluation metrics. Objective 2 is complete and live. Objective 3 includes tested English/Shona speech selection and a safe English fallback, but audible output and usability must still be confirmed on the target phone with real participants.

## Objective 1

> Collect, prepare and pre-process a labelled dataset of maize and tomato leaf images for training and evaluating a MobileNetV2 crop disease classification model.

**Status: COMPLETE**

Evidence present:

- `AI Service/data/processed/train`: 2,100 images.
- `AI Service/data/processed/validation`: 450 images.
- `AI Service/data/processed/test`: 450 held-out images.
- Ten balanced classes are present, with 45 held-out images per class.
- The dataset includes three maize classes and two tomato classes.
- `prepare_dataset.py` creates 70% training, 15% validation and 15% test splits.
- `train_mobilenetv2.py` uses MobileNetV2 transfer learning, 224×224 resizing, rescaling, random flipping, random rotation and dropout.
- A trained model exists at `AI Service/models/mobilenetv2_crop_disease.keras`.
- `evaluate_model.py` calculates and preserves accuracy, per-class precision, recall, F1 score and a confusion matrix.
- Evaluation on 450 held-out images achieved 92.00% accuracy, 91.94% macro precision, 92.00% macro recall and 91.90% macro F1.
- The defined acceptance thresholds are at least 80% accuracy and at least 75% macro F1; the model passed both.
- Results are preserved under `AI Service/evaluation/` as Markdown, JSON and CSV evidence.

Scope observation:

The Chapter One objective focuses on maize and tomato, while the deployed model also includes pepper, potato, soybean and squash as a product extension. `PROJECT_SCOPE.md` now records the complete ten-class model while retaining maize and tomato as the primary academic focus.

## Objective 2

> Integrate the trained MobileNetV2 model into an Android mobile application that provides real-time disease diagnosis, confidence scores, treatment recommendations and preventive guidance.

**Status: COMPLETE**

Evidence:

- Flutter provides the Android mobile interface and image capture/gallery workflow.
- Laravel receives authenticated multipart image uploads.
- Laravel calls the Python FastAPI prediction service.
- The deployed FastAPI service loads the trained Keras model and returns a predicted label and confidence value.
- The mobile result screen displays disease, crop, confidence, treatment recommendation, prevention guidance and a low-confidence warning.
- MySQL stores diagnoses and the application retrieves diagnosis history.
- Live end-to-end testing passed registration, login, image upload, AI prediction, database persistence, history retrieval and logout.
- Direct AI inference on the Oracle VM returned HTTP 200 in approximately 0.180 seconds.
- The full internet upload-and-predict transaction completed in approximately 4.093 seconds.

Qualification:

The word “real-time” is not assigned a formal threshold in Chapter One. The current response time is interactive, but a defensible claim should define a target and report median and percentile latency across repeated tests.

## Objective 3

> Implement English and Shona voice interaction in the mobile application to improve accessibility for farmers with different literacy and digital-literacy levels.

**Status: COMPLETE IN SOFTWARE; FIELD ACCEPTANCE PENDING**

Evidence:

- The application stores an English (`en`) or Shona (`sn`) language preference.
- User-interface labels and diagnosis content have English and Shona variants.
- Laravel localizes symptoms, prevention guidance and treatments using the farmer's language preference.
- The result screen builds a spoken diagnosis containing disease, crop, confidence, recommendations and the safety disclaimer.
- Android implements a native `TextToSpeech` method channel and checks whether the requested speech locale is installed.
- English uses `Locale.US`; Shona requests `Locale("sn", "ZW")`.
- When Shona speech is unavailable, Android uses English and the Flutter interface displays a localized explanation.
- Three automated TTS bridge tests cover supported Shona, English fallback and platform failure.
- The Android debug APK builds successfully with the native TTS implementation.

Outstanding verification:

- Audible English and Shona output has not been verified on a physical phone.
- A farmer usability test has not established whether the speech actually improves accessibility.
- The project scope now defines “voice interaction” as spoken diagnosis output; speech-command input is outside the current scope.

## Required actions for full completion

1. Install the new Android build on the target phone and complete `docs/PHYSICAL_DEVICE_AND_UAT_CHECKLIST.md`.
2. Test with at least five representative farmers and record the real results; participant outcomes cannot be generated through automated testing.
3. Repeat network-performance measurements across multiple runs if the report requires median and percentile latency.
