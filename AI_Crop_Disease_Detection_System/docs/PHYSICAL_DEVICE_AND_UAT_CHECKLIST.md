# CropDetec Physical Device and User Acceptance Test

Use this form on the target Android phone. Do not mark a result as passed without observing it.

## Test record

- Date:
- App build/version:
- Phone model:
- Android version:
- Android speech engine/version:
- Tester or participant ID (do not record unnecessary personal information):
- Preferred language: English / Shona

## Physical-device functional checks

| ID | Task | Expected result | Result | Notes |
|---|---|---|---|---|
| P01 | Install and open the app | App reaches login without crashing | Not run | |
| P02 | Register a farmer account | Account is created and dashboard opens | Not run | |
| P03 | Log out and log in again | Saved credentials work and session remains stable | Not run | |
| P04 | Allow camera permission | Android permission prompt is handled correctly | Not run | |
| P05 | Capture a maize leaf photo | Camera opens and preview is displayed | Not run | |
| P06 | Select a tomato image from gallery | Gallery opens and preview is displayed | Not run | |
| P07 | Submit the image | Prediction completes without logging the farmer out | Not run | |
| P08 | Review the result | Disease, crop, confidence, prevention and treatment appear | Not run | |
| P09 | Open diagnosis history | The newly created diagnosis appears | Not run | |
| P10 | Read an English result aloud | English speech is audible and understandable | Not run | |
| P11 | Read a Shona result aloud | Shona speech is audible, or the app clearly reports and uses English fallback | Not run | |
| P12 | Deny camera permission and retry | App remains usable and explains how to continue | Not run | |
| P13 | Submit a poor/unclear image | App shows low-confidence guidance when applicable | Not run | |

## User acceptance tasks

Ask the participant to complete these tasks without coaching after a short introduction.

| ID | Task | Completed unaided? | Time | Errors or comments |
|---|---|---|---|---|
| U01 | Create an account | | | |
| U02 | Capture or select a crop image | | | |
| U03 | Find the predicted disease and confidence | | | |
| U04 | Find prevention and treatment guidance | | | |
| U05 | Listen to the diagnosis | | | |
| U06 | Find the diagnosis again in history | | | |
| U07 | Change the preferred language | | | |
| U08 | Log out | | | |

## Participant ratings

Use a 1–5 scale where 1 means strongly disagree and 5 means strongly agree.

| Statement | Rating | Comment |
|---|---:|---|
| The screens were easy to understand. | | |
| I could complete the diagnosis without help. | | |
| The result and confidence were clear. | | |
| The treatment and prevention guidance was useful. | | |
| The spoken result was understandable. | | |
| I would use the application for preliminary crop guidance. | | |

## Acceptance criteria

The UAT passes when all of the following are true:

1. At least five representative farmer participants complete the test.
2. At least 80% of all U01–U08 tasks are completed without assistance.
3. The mean rating across all statements is at least 4.0 out of 5.
4. No unresolved critical defect causes data loss, account exposure, repeated logout or app termination.
5. Camera capture, diagnosis upload, history and logout pass on the target Android build.
6. English audio passes; Shona either passes natively or the documented English fallback is displayed and works.

## Defects and sign-off

| Defect ID | Severity | Description | Resolution | Retest result |
|---|---|---|---|---|
| | | | | |

- Total participants:
- Unaided task completion rate:
- Mean participant rating:
- Critical defects open:
- Final result: PASS / FAIL
- Reviewer name and signature:
- Date:

