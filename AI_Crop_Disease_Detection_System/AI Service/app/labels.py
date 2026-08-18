import json
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parents[1]
CLASS_NAMES_PATH = ROOT_DIR / "models" / "class_names.txt"
LABELS_PATH = ROOT_DIR / "labels.json"


def load_label_map() -> dict[str, str]:
    if CLASS_NAMES_PATH.exists():
        class_names = [
            line.strip()
            for line in CLASS_NAMES_PATH.read_text(encoding="utf-8").splitlines()
            if line.strip()
        ]

        return {
            str(index): class_name
            for index, class_name in enumerate(class_names)
        }

    with LABELS_PATH.open("r", encoding="utf-8") as labels_file:
        labels = json.load(labels_file)

    ordered_labels = sorted(
        labels.values(),
        key=lambda details: details["class_label"],
    )

    return {
        str(index): details["class_label"]
        for index, details in enumerate(ordered_labels)
    }
