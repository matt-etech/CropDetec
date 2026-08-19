import argparse
import csv
import json
from datetime import datetime, timezone
from pathlib import Path

import numpy as np
import tensorflow as tf

DATA_DIR = Path("data/processed/test")
MODEL_PATH = Path("models/mobilenetv2_crop_disease.keras")
OUTPUT_DIR = Path("evaluation")
IMAGE_SIZE = (224, 224)
BATCH_SIZE = 32


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Evaluate the trained CropDetec model.")
    parser.add_argument("--data-dir", type=Path, default=DATA_DIR)
    parser.add_argument("--model", type=Path, default=MODEL_PATH)
    parser.add_argument("--output-dir", type=Path, default=OUTPUT_DIR)
    parser.add_argument("--min-accuracy", type=float, default=0.80)
    parser.add_argument("--min-macro-f1", type=float, default=0.75)
    return parser.parse_args()


def class_metrics(
    predictions: np.ndarray,
    truth: np.ndarray,
    class_names: list[str],
) -> list[dict[str, float | int | str]]:
    results = []
    for index, class_name in enumerate(class_names):
        true_positive = int(np.sum((predictions == index) & (truth == index)))
        false_positive = int(np.sum((predictions == index) & (truth != index)))
        false_negative = int(np.sum((predictions != index) & (truth == index)))
        support = int(np.sum(truth == index))
        precision = true_positive / max(true_positive + false_positive, 1)
        recall = true_positive / max(true_positive + false_negative, 1)
        f1 = 2 * precision * recall / max(precision + recall, 1e-9)
        results.append(
            {
                "class": class_name,
                "precision": precision,
                "recall": recall,
                "f1": f1,
                "support": support,
            }
        )
    return results


def write_outputs(
    output_dir: Path,
    report: dict,
    matrix: np.ndarray,
    class_names: list[str],
) -> None:
    output_dir.mkdir(parents=True, exist_ok=True)
    (output_dir / "model_metrics.json").write_text(
        json.dumps(report, indent=2),
        encoding="utf-8",
    )

    with (output_dir / "confusion_matrix.csv").open("w", newline="", encoding="utf-8") as file:
        writer = csv.writer(file)
        writer.writerow(["actual\\predicted", *class_names])
        for class_name, row in zip(class_names, matrix, strict=True):
            writer.writerow([class_name, *row.tolist()])

    lines = [
        "# CropDetec MobileNetV2 Evaluation",
        "",
        f"Generated: {report['generated_at']}",
        f"Test images: {report['test_images']}",
        f"Accuracy: {report['accuracy']:.4f}",
        f"Macro precision: {report['macro_precision']:.4f}",
        f"Macro recall: {report['macro_recall']:.4f}",
        f"Macro F1: {report['macro_f1']:.4f}",
        f"Acceptance status: **{report['acceptance']['status']}**",
        "",
        "| Class | Precision | Recall | F1 | Support |",
        "|---|---:|---:|---:|---:|",
    ]
    for metric in report["classes"]:
        lines.append(
            f"| {metric['class']} | {metric['precision']:.4f} | "
            f"{metric['recall']:.4f} | {metric['f1']:.4f} | {metric['support']} |"
        )
    lines.extend(
        [
            "",
            "Acceptance criteria:",
            "",
            f"- Accuracy >= {report['acceptance']['minimum_accuracy']:.2f}",
            f"- Macro F1 >= {report['acceptance']['minimum_macro_f1']:.2f}",
            "",
            "See `confusion_matrix.csv` for the full confusion matrix.",
            "",
        ]
    )
    (output_dir / "MODEL_EVALUATION.md").write_text("\n".join(lines), encoding="utf-8")


def main() -> None:
    args = parse_args()
    if not args.model.exists():
        raise SystemExit(f"Model not found: {args.model}")
    if not args.data_dir.exists():
        raise SystemExit(f"Test dataset not found: {args.data_dir}")

    test_ds = tf.keras.utils.image_dataset_from_directory(
        args.data_dir,
        image_size=IMAGE_SIZE,
        batch_size=BATCH_SIZE,
        shuffle=False,
    )
    class_names = list(test_ds.class_names)
    model = tf.keras.models.load_model(args.model)
    probabilities = model.predict(test_ds, verbose=0)
    predictions = np.argmax(probabilities, axis=1)
    truth = np.concatenate([labels.numpy() for _, labels in test_ds])

    metrics = class_metrics(predictions, truth, class_names)
    accuracy = float(np.mean(predictions == truth))
    macro_precision = float(np.mean([item["precision"] for item in metrics]))
    macro_recall = float(np.mean([item["recall"] for item in metrics]))
    macro_f1 = float(np.mean([item["f1"] for item in metrics]))
    matrix = tf.math.confusion_matrix(
        truth,
        predictions,
        num_classes=len(class_names),
    ).numpy()
    passed = accuracy >= args.min_accuracy and macro_f1 >= args.min_macro_f1
    report = {
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "model": str(args.model),
        "test_data": str(args.data_dir),
        "test_images": int(len(truth)),
        "accuracy": accuracy,
        "macro_precision": macro_precision,
        "macro_recall": macro_recall,
        "macro_f1": macro_f1,
        "classes": metrics,
        "acceptance": {
            "minimum_accuracy": args.min_accuracy,
            "minimum_macro_f1": args.min_macro_f1,
            "status": "PASS" if passed else "FAIL",
        },
    }
    write_outputs(args.output_dir, report, matrix, class_names)

    print(f"accuracy={accuracy:.4f}")
    print(f"macro_precision={macro_precision:.4f}")
    print(f"macro_recall={macro_recall:.4f}")
    print(f"macro_f1={macro_f1:.4f}")
    print(f"acceptance={report['acceptance']['status']}")
    print(f"results={args.output_dir.resolve()}")


if __name__ == "__main__":
    main()
