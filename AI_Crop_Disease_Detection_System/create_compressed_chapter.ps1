$ErrorActionPreference = "Stop"

Add-Type -AssemblyName System.IO.Compression
Add-Type -AssemblyName System.IO.Compression.FileSystem

$outputPath = Join-Path (Get-Location) "CHAPTER THREE - COMPRESSED WITH DIAGRAMS.docx"
$tempDir = Join-Path (Get-Location) "_docx_build"

if (Test-Path $tempDir) {
    Remove-Item -LiteralPath $tempDir -Recurse -Force
}

New-Item -ItemType Directory -Force -Path $tempDir, (Join-Path $tempDir "_rels"), (Join-Path $tempDir "word") | Out-Null

function Escape-Xml([string] $text) {
    if ($null -eq $text) { return "" }
    return [System.Security.SecurityElement]::Escape($text)
}

function Paragraph([string] $text, [string] $type = "body") {
    $escaped = Escape-Xml $text
    $pPr = ""
    $rPr = ""

    switch ($type) {
        "title" {
            $pPr = '<w:pPr><w:jc w:val="center"/><w:spacing w:after="240"/></w:pPr>'
            $rPr = '<w:rPr><w:b/><w:sz w:val="32"/></w:rPr>'
        }
        "h1" {
            $pPr = '<w:pPr><w:spacing w:before="260" w:after="140"/></w:pPr>'
            $rPr = '<w:rPr><w:b/><w:sz w:val="28"/></w:rPr>'
        }
        "h2" {
            $pPr = '<w:pPr><w:spacing w:before="180" w:after="100"/></w:pPr>'
            $rPr = '<w:rPr><w:b/><w:sz w:val="24"/></w:rPr>'
        }
        "caption" {
            $pPr = '<w:pPr><w:jc w:val="center"/><w:spacing w:before="80" w:after="160"/></w:pPr>'
            $rPr = '<w:rPr><w:i/><w:sz w:val="20"/></w:rPr>'
        }
        "bullet" {
            $escaped = "• " + $escaped
            $pPr = '<w:pPr><w:spacing w:after="80"/></w:pPr>'
        }
        "diagram" {
            $pPr = '<w:pPr><w:spacing w:before="120" w:after="120"/></w:pPr>'
            $rPr = '<w:rPr><w:rFonts w:ascii="Consolas" w:hAnsi="Consolas"/><w:sz w:val="18"/></w:rPr>'
        }
        default {
            $pPr = '<w:pPr><w:spacing w:after="120"/><w:jc w:val="both"/></w:pPr>'
        }
    }

    $lines = $escaped -split "(`r`n|`n|`r)"
    $runs = @(foreach ($line in $lines) {
        "<w:t xml:space=`"preserve`">$line</w:t><w:br/>"
    })
    if ($runs.Count -gt 0) {
        $runs[$runs.Count - 1] = $runs[$runs.Count - 1] -replace '<w:br/>$', ''
    }

    return "<w:p>$pPr<w:r>$rPr$($runs -join '')</w:r></w:p>"
}

$docParts = New-Object System.Collections.Generic.List[string]

$docParts.Add((Paragraph "CHAPTER THREE" "title"))
$docParts.Add((Paragraph "METHODOLOGY" "title"))

$docParts.Add((Paragraph "3.1 Introduction" "h1"))
$docParts.Add((Paragraph "This chapter explains the methodology used to design, develop, implement and evaluate the AI-Powered Mobile Crop Disease Detection System. The study combines mobile application development, backend API development, database design and artificial intelligence model development to support early crop disease diagnosis. The proposed system enables farmers to capture or upload crop leaf images, receive disease predictions, view confidence scores, access treatment and prevention recommendations, and keep diagnosis history."))
$docParts.Add((Paragraph "The methodology is based on Design Science Research because the study produces a practical technological artefact that addresses delayed crop disease identification. Agile software development complements this approach by supporting iterative development, testing and improvement. The main technical components are Flutter for the mobile application, Laravel for backend services, MySQL for data storage, and Python with TensorFlow/OpenCV for image processing and model inference."))

$docParts.Add((Paragraph "3.2 Research Design" "h1"))
$docParts.Add((Paragraph "The research design integrates Design Science Research, Agile development and experimental AI model evaluation. Design Science Research guides the creation and evaluation of the artefact, while Agile development supports incremental implementation of the Flutter application, Laravel backend, MySQL database and Python AI service. Experimental evaluation is used to train, validate and test the deep learning model using labelled crop leaf images."))
$docParts.Add((Paragraph "Figure 3.1: Research Design and Development Approach" "caption"))
$docParts.Add((Paragraph @"
+-----------------------+      +----------------------+      +----------------------+
| Problem Identification| ---> | Artefact Design      | ---> | System Development   |
| Delayed crop diagnosis|      | Mobile + AI solution |      | Flutter/Laravel/AI   |
+-----------------------+      +----------------------+      +----------------------+
          |                              |                              |
          v                              v                              v
+-----------------------+      +----------------------+      +----------------------+
| Requirements Analysis | ---> | Iterative Testing    | ---> | Evaluation & Review  |
| Farmer/admin needs    |      | Agile improvement    |      | Accuracy/usability   |
+-----------------------+      +----------------------+      +----------------------+
"@ "diagram"))

$docParts.Add((Paragraph "3.2.1 Requirements Analysis" "h2"))
$docParts.Add((Paragraph "Requirements were derived from the research objectives, literature on AI crop disease detection, and the practical needs of farmers who often experience delays in accessing agricultural extension support. The system therefore focuses on rapid image-based diagnosis, simple navigation, history tracking, treatment guidance and bilingual accessibility."))
$docParts.Add((Paragraph "Functional requirements" "h2"))
$docParts.Add((Paragraph "Register and authenticate farmers and administrators." "bullet"))
$docParts.Add((Paragraph "Capture or upload crop leaf images for disease analysis." "bullet"))
$docParts.Add((Paragraph "Process uploaded images using a trained CNN/MobileNetV2 model." "bullet"))
$docParts.Add((Paragraph "Display predicted disease, confidence score, symptoms, prevention and treatment recommendations." "bullet"))
$docParts.Add((Paragraph "Store and retrieve diagnosis history for each farmer." "bullet"))
$docParts.Add((Paragraph "Support English and Shona text/voice assistance." "bullet"))
$docParts.Add((Paragraph "Allow administrators to manage crops, diseases, treatments, users and diagnosis records." "bullet"))
$docParts.Add((Paragraph "Non-functional requirements" "h2"))
$docParts.Add((Paragraph "Performance: diagnosis results should be returned within a few seconds under normal conditions." "bullet"))
$docParts.Add((Paragraph "Reliability: the application should validate inputs, handle errors and consistently store diagnosis records." "bullet"))
$docParts.Add((Paragraph "Security: passwords must be hashed, protected routes must require authentication, and user data must be restricted by role." "bullet"))
$docParts.Add((Paragraph "Usability: interfaces should use readable text, simple navigation and clear actions suitable for farmers with different digital literacy levels." "bullet"))
$docParts.Add((Paragraph "Scalability and maintainability: the system must support additional crops, diseases, datasets and future model improvements." "bullet"))

$docParts.Add((Paragraph "3.2.2 Data Collection and Dataset Preparation" "h2"))
$docParts.Add((Paragraph "The AI model requires labelled images of healthy and diseased crop leaves. Public datasets such as PlantVillage may be used as the initial source, while additional local images can improve relevance to Zimbabwean farming conditions. Images should be organised by class label, checked for quality, cleaned to remove duplicates and corrupt files, and split into training, validation and testing sets."))
$docParts.Add((Paragraph "Figure 3.2: Dataset Preparation Workflow" "caption"))
$docParts.Add((Paragraph @"
+----------------+   +----------------+   +----------------+   +----------------+
| Collect images |-->| Clean dataset  |-->| Label classes  |-->| Split dataset  |
| Public/local   |   | Remove errors  |   | Crop/disease   |   | Train/val/test |
+----------------+   +----------------+   +----------------+   +----------------+
                                                              |
                                                              v
                                                    +----------------+
                                                    | Augment images |
                                                    | Flip/rotate    |
                                                    +----------------+
"@ "diagram"))

$docParts.Add((Paragraph "3.2.3 Image Pre-processing" "h2"))
$docParts.Add((Paragraph "Before training, each image is resized to the model input size, converted to a consistent colour format, normalised and optionally augmented. Pre-processing improves consistency, reduces noise and helps the CNN learn disease patterns more effectively."))
$docParts.Add((Paragraph "Figure 3.3: Image Pre-processing Flow" "caption"))
$docParts.Add((Paragraph @"
+-------------+     +------------+     +-------------+     +--------------+
| Input image | --> | Resize     | --> | Normalize   | --> | Augment      |
| Leaf photo  |     | 224 x 224  |     | Pixel range |     | Flip/rotate  |
+-------------+     +------------+     +-------------+     +--------------+
                                                                  |
                                                                  v
                                                        +------------------+
                                                        | Model-ready data |
                                                        +------------------+
"@ "diagram"))

$docParts.Add((Paragraph "3.2.4 Deep Learning Model Selection" "h2"))
$docParts.Add((Paragraph "MobileNetV2 was selected because it is lightweight, efficient and suitable for mobile-oriented image classification. Compared with heavier models, it provides a good balance between accuracy and computational efficiency. Transfer learning allows the model to use pre-trained visual features and adapt them to crop disease classes."))

$docParts.Add((Paragraph "3.2.5 Artificial Intelligence Model Development" "h2"))
$docParts.Add((Paragraph "The AI model development process involves loading the prepared dataset, applying pre-processing, training MobileNetV2, validating the model, evaluating it using test data and exporting it for inference. Model performance is assessed using accuracy, precision, recall, F1-score and a confusion matrix."))
$docParts.Add((Paragraph "Figure 3.4: AI Model Development Process" "caption"))
$docParts.Add((Paragraph @"
+---------------+   +----------------+   +------------------+   +---------------+
| Prepared data |-->| MobileNetV2    |-->| Train/validate   |-->| Evaluate      |
| Class folders |   | Transfer model |   | Tune performance |   | Metrics       |
+---------------+   +----------------+   +------------------+   +---------------+
                                                                    |
                                                                    v
                                                          +--------------------+
                                                          | Export model file  |
                                                          | .keras / labels    |
                                                          +--------------------+
"@ "diagram"))

$docParts.Add((Paragraph "3.2.6 System Architecture and Integration Strategy" "h2"))
$docParts.Add((Paragraph "The system uses a modular architecture. The Flutter mobile application provides the farmer interface. The Laravel backend manages authentication, crop and disease data, diagnosis records and administration. MySQL stores persistent data. The Python AI service receives uploaded images, performs inference and returns prediction results to Laravel."))
$docParts.Add((Paragraph "Figure 3.5: Overall System Architecture" "caption"))
$docParts.Add((Paragraph @"
+------------------+       HTTPS/API       +-------------------+
| Flutter Mobile   | <-------------------> | Laravel Backend   |
| Farmer interface |                       | Auth, records API |
+------------------+                       +---------+---------+
                                                    |
                          +-------------------------+-------------------------+
                          |                                                   |
                          v                                                   v
                +-------------------+                              +-------------------+
                | MySQL Database    |                              | Python AI Service |
                | Users/diagnoses   |                              | TensorFlow model  |
                +-------------------+                              +-------------------+
"@ "diagram"))

$docParts.Add((Paragraph "3.2.7 System Workflow" "h2"))
$docParts.Add((Paragraph "The workflow begins when a farmer logs in and uploads or captures a crop leaf image. Laravel validates the image and forwards it to the AI service. The AI service returns a predicted class and confidence score. Laravel maps the class label to disease records, stores the diagnosis and returns the result with recommendations."))
$docParts.Add((Paragraph "Figure 3.6: Diagnosis Workflow" "caption"))
$docParts.Add((Paragraph @"
+--------+   +--------------+   +--------------+   +--------------+   +-------------+
| Farmer |-->| Upload image |-->| Laravel API  |-->| AI Service   |-->| Prediction  |
+--------+   +--------------+   +--------------+   +--------------+   +-------------+
                                                    |
                                                    v
                                      +----------------------------+
                                      | Store diagnosis in MySQL   |
                                      +----------------------------+
                                                    |
                                                    v
                                      +----------------------------+
                                      | Display result and advice  |
                                      +----------------------------+
"@ "diagram"))

$docParts.Add((Paragraph "3.3 System Requirements" "h1"))
$docParts.Add((Paragraph "Hardware requirements include an Android smartphone with a working camera for farmers, a development computer for Flutter/Laravel/Python development, and a server capable of hosting Laravel, MySQL and the AI inference service. Software requirements include Flutter, Dart, Laravel, PHP, MySQL, Python, TensorFlow, OpenCV, Composer, Git and a suitable code editor."))

$docParts.Add((Paragraph "3.4 System Design" "h1"))
$docParts.Add((Paragraph "The design includes use cases for farmers and administrators. Farmers register, log in, upload images, view predictions, access treatment guidance and review history. Administrators manage crop, disease, treatment and user records. The database design includes users, crops, diseases, treatments, diagnoses and API tokens or administrator roles."))
$docParts.Add((Paragraph "Figure 3.7: Simplified Use Case Diagram" "caption"))
$docParts.Add((Paragraph @"
                +-----------------------------------+
                | AI Crop Disease Detection System  |
                +-----------------------------------+
 Farmer  --->   | Register / Login                  |
 Farmer  --->   | Upload crop image                 |
 Farmer  --->   | View diagnosis and recommendations|
 Farmer  --->   | Review diagnosis history          |
 Admin   --->   | Manage crops/diseases/treatments  |
 Admin   --->   | Monitor users and diagnoses       |
                +-----------------------------------+
"@ "diagram"))
$docParts.Add((Paragraph "Figure 3.8: Entity Relationship Overview" "caption"))
$docParts.Add((Paragraph @"
+-------+       +-----------+       +-------+
| Users | 1---* | Diagnoses | *---1 | Crops |
+-------+       +-----------+       +-------+
                      |                 |
                      | *               | 1
                      v                 v
                  +----------+ 1---* +------------+
                  | Diseases |       | Treatments |
                  +----------+       +------------+
"@ "diagram"))

$docParts.Add((Paragraph "3.5 Tools and Materials Used" "h1"))
$docParts.Add((Paragraph "The project uses Flutter and Dart for the mobile interface, Laravel and PHP for backend services, MySQL for structured storage, Python/TensorFlow/OpenCV for the AI component, and Git/Composer for development management. These tools were selected because they support modular development, secure APIs, efficient data handling and image-based machine learning."))

$docParts.Add((Paragraph "3.6 Development Procedure" "h1"))
$docParts.Add((Paragraph "Development followed an iterative sequence: requirements analysis, user interface design, database development, backend API implementation, AI model development, Flutter mobile development, system integration and deployment. Agile iteration allowed earlier stages to be revisited whenever testing revealed improvements."))
$docParts.Add((Paragraph "Figure 3.9: Development Procedure Flowchart" "caption"))
$docParts.Add((Paragraph @"
+-------------------+ --> +-------------+ --> +------------------+
| Requirements      |     | UI Design   |     | Database Design  |
+-------------------+     +-------------+     +------------------+
                                                        |
                                                        v
+-------------------+ <-- +-------------+ <-- +------------------+
| Deployment        |     | Integration |     | Backend + AI Dev |
+-------------------+     +-------------+     +------------------+
"@ "diagram"))

$docParts.Add((Paragraph "3.7 Testing Methods" "h1"))
$docParts.Add((Paragraph "Testing combines software testing and AI evaluation. Unit tests verify individual modules such as registration, authentication, image upload and prediction functions. Integration tests verify communication between Flutter, Laravel, MySQL and the AI service. System testing evaluates the complete application against functional and non-functional requirements. User acceptance testing collects feedback from representative users."))
$docParts.Add((Paragraph "AI model evaluation uses accuracy, precision, recall, F1-score and a confusion matrix. Performance testing targets login under two seconds, image upload under three seconds, disease prediction under five seconds and diagnosis retrieval under two seconds."))
$docParts.Add((Paragraph "Figure 3.10: Testing Strategy" "caption"))
$docParts.Add((Paragraph @"
+------------+   +----------------+   +---------------+   +----------------+
| Unit Tests |-->| Integration    |-->| System Tests  |-->| User Acceptance|
+------------+   +----------------+   +---------------+   +----------------+
       |                 |                    |                    |
       v                 v                    v                    v
  Modules OK       Components OK       Full system OK       Users accept
"@ "diagram"))

$docParts.Add((Paragraph "3.8 Ethical and Safety Considerations" "h1"))
$docParts.Add((Paragraph "The system processes user details, diagnosis history and crop images; therefore, privacy and confidentiality must be protected. User access is controlled through authentication and role-based authorisation, and passwords are stored using secure hashing. Communication should use HTTPS in deployment."))
$docParts.Add((Paragraph "The AI component must be used responsibly because incorrect predictions may influence crop-management decisions. The system displays confidence scores and should advise users to consult agricultural professionals when confidence is low or symptoms are severe. Dataset integrity is also important: images should be diverse, correctly labelled and cleaned to reduce bias. The interface supports accessibility through simple navigation, readable text and English/Shona assistance."))

$docParts.Add((Paragraph "3.9 Chapter Summary" "h1"))
$docParts.Add((Paragraph "This chapter presented the methodology used to develop the AI-Powered Mobile Crop Disease Detection System. It explained the research design, requirements, dataset preparation, image pre-processing, MobileNetV2 model development, system architecture, workflow, design models, tools, development procedure, testing strategy and ethical considerations. The methodology provides a structured foundation for implementing a secure, usable and AI-assisted mobile system that helps farmers detect and manage crop diseases earlier."))

$body = $docParts -join "`n"

$documentXml = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
  <w:body>
    $body
    <w:sectPr>
      <w:pgSz w:w="11906" w:h="16838"/>
      <w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="720" w:footer="720" w:gutter="0"/>
    </w:sectPr>
  </w:body>
</w:document>
"@

$contentTypes = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
  <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
  <Default Extension="xml" ContentType="application/xml"/>
  <Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
</Types>
"@

$rels = @"
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
  <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>
</Relationships>
"@

Set-Content -Encoding UTF8 -LiteralPath (Join-Path $tempDir "[Content_Types].xml") -Value $contentTypes
Set-Content -Encoding UTF8 -LiteralPath (Join-Path $tempDir "_rels\.rels") -Value $rels
Set-Content -Encoding UTF8 -LiteralPath (Join-Path $tempDir "word\document.xml") -Value $documentXml

if (Test-Path $outputPath) {
    Remove-Item -LiteralPath $outputPath -Force
}

[System.IO.Compression.ZipFile]::CreateFromDirectory($tempDir, $outputPath)
Remove-Item -LiteralPath $tempDir -Recurse -Force

Write-Output $outputPath
