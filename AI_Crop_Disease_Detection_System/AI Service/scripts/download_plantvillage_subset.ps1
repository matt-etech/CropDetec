param(
    [int] $ImagesPerClass = 300,
    [string] $RepositoryUrl = "https://github.com/spMohanty/PlantVillage-Dataset",
    [string] $CacheDirectory = ".cache/PlantVillage-Dataset"
)

$ErrorActionPreference = "Stop"

$classes = @(
    @{ Source = "raw/color/Corn_(maize)___Common_rust_"; Target = "maize_common_rust" },
    @{ Source = "raw/color/Corn_(maize)___Cercospora_leaf_spot Gray_leaf_spot"; Target = "maize_gray_leaf_spot" },
    @{ Source = "raw/color/Corn_(maize)___Northern_Leaf_Blight"; Target = "maize_leaf_blight" },
    @{ Source = "raw/color/Pepper,_bell___Bacterial_spot"; Target = "pepper_bacterial_spot" },
    @{ Source = "raw/color/Potato___Early_blight"; Target = "potato_early_blight" },
    @{ Source = "raw/color/Potato___Late_blight"; Target = "potato_late_blight" },
    @{ Source = "raw/color/Soybean___healthy"; Target = "soybean_healthy" },
    @{ Source = "raw/color/Squash___Powdery_mildew"; Target = "squash_powdery_mildew" },
    @{ Source = "raw/color/Tomato___Early_blight"; Target = "tomato_early_blight" },
    @{ Source = "raw/color/Tomato___Late_blight"; Target = "tomato_late_blight" }
)

$serviceRoot = Resolve-Path (Join-Path $PSScriptRoot "..")
$rawRoot = Join-Path $serviceRoot "data/raw"
$cachePath = Join-Path $serviceRoot $CacheDirectory

if (-not (Get-Command git -ErrorAction SilentlyContinue)) {
    throw "Git is required to download the PlantVillage subset."
}

New-Item -ItemType Directory -Force -Path $rawRoot | Out-Null
New-Item -ItemType Directory -Force -Path (Split-Path $cachePath -Parent) | Out-Null

if (-not (Test-Path $cachePath)) {
    git clone --depth 1 --filter=blob:none --sparse $RepositoryUrl $cachePath
}

Push-Location $cachePath
try {
    $sourceFolders = $classes | ForEach-Object { $_.Source }
    git sparse-checkout set --no-cone @sourceFolders
} finally {
    Pop-Location
}

foreach ($class in $classes) {
    $sourcePath = Join-Path $cachePath $class.Source
    $targetPath = Join-Path $rawRoot $class.Target
    New-Item -ItemType Directory -Force -Path $targetPath | Out-Null

    $images = Get-ChildItem -Path (Join-Path $sourcePath "*") -File -Include *.jpg,*.jpeg,*.png,*.webp |
        Sort-Object Name |
        Select-Object -First $ImagesPerClass

    foreach ($image in $images) {
        Copy-Item -LiteralPath $image.FullName -Destination (Join-Path $targetPath $image.Name) -Force
    }

    Write-Host "$($class.Target): copied $($images.Count) images"
}

Write-Host "Dataset subset ready in $rawRoot"
