# AI Crop Disease Detection System Scope

## Framework Version Note

The thesis text may mention Laravel 12, but this workspace uses Laravel 13.8 with PHP 8.3. The implementation should be described as Laravel 13 unless the academic document is intentionally documenting an earlier prototype.

## First Supported Crops And Disease Classes

The first implementation supports these seeded labels:

| Crop | Disease | AI class label |
| --- | --- | --- |
| Tomato | Early Blight | `tomato_early_blight` |
| Maize | Leaf Blight | `maize_leaf_blight` |

Healthy classes can be added when the dataset is collected by adding matching `diseases` records or a separate health-status table if the write-up requires non-disease outputs.

## AI Technique

The planned AI technique is MobileNetV2 transfer learning for image classification. Laravel owns accounts, records, recommendations, and persistence. The Python service owns image preprocessing, model loading, inference, and metrics.

## Product Scope

The product is a mobile-first farmer tool with an optional Laravel web dashboard for administrators. Farmers use the Flutter app to register, upload or capture crop leaf images, view diagnosis results, read recommendations, and review history. Administrators maintain crop, disease, and treatment data and monitor submitted diagnoses.

## Objectives, Questions, And Success Criteria

| Capability | Objective | Research question | Success criterion |
| --- | --- | --- | --- |
| Mobile diagnosis | Help farmers submit crop leaf images and receive disease guidance. | Can a mobile-first workflow reduce the effort needed to obtain disease guidance? | A farmer can complete image upload and receive a stored result in under 5 seconds under normal local-network conditions. |
| AI classification | Classify crop disease images using MobileNetV2 transfer learning. | Can MobileNetV2 classify the selected crop disease labels accurately enough for advisory support? | The exported model reports accuracy, precision, recall, F1-score, and confusion matrix on a held-out test set. |
| Recommendation history | Preserve diagnosis records and advice for later review. | Does saved history improve traceability of farmer diagnosis decisions? | A farmer can retrieve their own history in under 2 seconds and cannot access another farmer's diagnosis. |
| Admin management | Let administrators maintain crops, diseases, and treatments. | Can role-protected administration keep recommendation data maintainable? | Admin users can create/update crop, disease, and treatment records, while farmer accounts receive forbidden responses. |
| Accessibility | Support readable text, Shona preference, and audio playback. | Can language and voice support improve access for farmers with different literacy needs? | Diagnosis results include English/Shona UI text and a text-to-speech playback action. |

## Disclaimer

Predictions support field decisions but do not replace advice from an agricultural professional or extension officer. Low-confidence or severe cases should be verified locally.

## Security And Privacy

Deployment should use HTTPS for the Laravel API, Flutter clients, and the Python prediction service. Diagnosis uploads are stored by Laravel and exposed to clients through generated image URLs rather than raw storage paths. The application stores personal details, diagnosis history, and crop images only for account, diagnostic, and support purposes.
