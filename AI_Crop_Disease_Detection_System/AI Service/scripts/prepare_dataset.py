from pathlib import Path
import shutil

RAW_DIR = Path("data/raw")
OUTPUT_DIR = Path("data/processed")
SPLITS = {
    "train": 0.7,
    "validation": 0.15,
    "test": 0.15,
}


def main() -> None:
    if not RAW_DIR.exists():
        raise SystemExit("Create data/raw/<class_label>/ folders before preparing the dataset.")

    OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

    for class_dir in sorted(path for path in RAW_DIR.iterdir() if path.is_dir()):
        images = sorted(
            path for path in class_dir.iterdir()
            if path.suffix.lower() in {".jpg", ".jpeg", ".png", ".webp"}
        )
        if not images:
            continue

        train_end = int(len(images) * SPLITS["train"])
        validation_end = train_end + int(len(images) * SPLITS["validation"])
        split_images = {
            "train": images[:train_end],
            "validation": images[train_end:validation_end],
            "test": images[validation_end:],
        }

        for split, paths in split_images.items():
            target_dir = OUTPUT_DIR / split / class_dir.name
            target_dir.mkdir(parents=True, exist_ok=True)
            for path in paths:
                shutil.copy2(path, target_dir / path.name)

    print(f"Prepared dataset in {OUTPUT_DIR}")


if __name__ == "__main__":
    main()
