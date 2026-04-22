#!/usr/bin/env python
"""
Build a DOCX-only WELLME module content package.

This script treats the six files in "WP 3.1. Creating Hands On Training" as the
source of truth for module text. It writes a ZIP package that can be uploaded in
WordPress under WELLME Modules > Import Modules after the plugin update that
supports the extra text fields.
"""

from __future__ import annotations

import argparse
import html
import json
import re
import zipfile
from datetime import datetime, timezone
from pathlib import Path
from xml.etree import ElementTree as ET


DOC_ROOT = Path("WP 3.1. Creating Hands On Training")
OUTPUT_ROOT = Path("output") / "import"

MODULES = {
    1: {
        "file": "WP3.1.Hands on Training Module1_GESEME_WELLME.docx",
        "title": "From Strength to Strength: Positive Psychology for Youth Trainers",
        "slug": "from-strength-to-strength-positive-psychology-for-youth-trainers",
        "color": "#27ae60",
    },
    2: {
        "file": "WP3.1.Hands on Training Module2_EUROPEANPROGRESS_WELLME.docx",
        "title": "Bounce Back Stronger",
        "slug": "bounce-back-stronger",
        "color": "#c0392b",
    },
    3: {
        "file": "WP3_1_Hands_on_Training_Module3_UNIZAR_WELLME_FINAL VERSION.docx",
        "title": "Fuel for Flourishing: Healthy Nutrition & Lifestyle in Youth Work",
        "slug": "fuel-for-flourishing-healthy-nutrition-lifestyle-in-youth-work",
        "color": "#a16f00",
    },
    4: {
        "file": "WP3.1.Hands on Training Module4_ETAP_WELLME.docx",
        "title": "Move to Thrive: Integrating Physical Activity into Youth Wellbeing",
        "slug": "move-to-thrive-integrating-physical-activity-into-youth-wellbeing",
        "color": "#0b5c82",
    },
    5: {
        "file": "WP3.1.Hands on Training Module5_CENTREDOT_WELLME.docx",
        "title": "Spaces that Connect: Designing Environments for Youth Belonging",
        "slug": "spaces-that-connect-designing-environments-for-youth-belonging",
        "color": "#4a2d57",
    },
    6: {
        "file": "WP3.1.Hands on Training Module6_Autokreacja.docx",
        "title": "Bridges to Adulthood: Guiding Youth Transitions through Community Power",
        "slug": "bridges-to-adulthood-guiding-youth-transitions-through-community-power",
        "color": "#00706b",
    },
}

PROMPT_PREFIXES = (
    "provide a concise overview",
    "the purpose of this section",
    "important:",
    "this section should cover",
    "what this module",
    "-define the key concepts",
    "define the key concepts",
    "-briefly explain",
    "briefly explain",
    "clearly connect",
    "in this part describe",
    "in this section, provide",
    "provide clear and concise",
    "tips for trainers:include practical advice",
    "include practical advice",
    "the conclusion must include",
    "any 3 reflection questions",
    "optional:",
    "any additional remarks",
    "you may use any media",
)


def parse_args() -> argparse.Namespace:
    parser = argparse.ArgumentParser(description="Build the WELLME DOCX content import package.")
    parser.add_argument("--doc-root", type=Path, default=DOC_ROOT)
    parser.add_argument("--output-root", type=Path, default=OUTPUT_ROOT)
    return parser.parse_args()


def extract_docx_lines(path: Path) -> list[str]:
    ns = {"w": "http://schemas.openxmlformats.org/wordprocessingml/2006/main"}
    with zipfile.ZipFile(path) as docx:
        xml = docx.read("word/document.xml")

    root = ET.fromstring(xml)
    lines: list[str] = []

    for paragraph in root.iter(f"{{{ns['w']}}}p"):
        parts: list[str] = []
        for node in paragraph.iter():
            if node.tag == f"{{{ns['w']}}}t" and node.text:
                parts.append(node.text)
            elif node.tag == f"{{{ns['w']}}}tab":
                parts.append("\t")
            elif node.tag == f"{{{ns['w']}}}br":
                parts.append("\n")

        text = "".join(parts).replace("\xa0", " ").strip()
        if text:
            lines.append(text)

    while lines and re.fullmatch(r"\d+", lines[-1].strip()):
        lines.pop()

    return lines


def normalized(value: str) -> str:
    return re.sub(r"\s+", " ", value).strip()


def lower_plain(value: str) -> str:
    return normalized(value).strip(" .:-").lower()


def find_index(lines: list[str], start: int, predicate) -> int:
    for index in range(start, len(lines)):
        if predicate(lines[index]):
            return index
    return -1


def is_intro_heading(line: str) -> bool:
    plain = lower_plain(line)
    return plain in {"introduction", "1 introduction", "1. introduction"}


def is_activity_heading(line: str) -> bool:
    plain = lower_plain(line)
    return plain in {"module activity", "2 module activity", "2. module activity"}


def is_objectives_heading(line: str) -> bool:
    return lower_plain(line).startswith("2.1 objectives")


def is_guide_heading(line: str) -> bool:
    return lower_plain(line).startswith("2.2 step-by-step")


def is_conclusion_heading(line: str) -> bool:
    plain = lower_plain(line)
    return plain in {"conclusion", "3 conclusion", "3. conclusion"}


def is_annex_heading(line: str) -> bool:
    plain = lower_plain(line)
    return "annex" in plain or plain.startswith("stories to share") or plain.startswith("photo presentation")


def split_sections(lines: list[str]) -> dict[str, list[str]]:
    funding_indices = [
        index
        for index, line in enumerate(lines)
        if line.startswith("Funded by") or line.startswith("El proyecto")
    ]
    front_end = max(funding_indices) + 1 if funding_indices else 0

    intro = find_index(lines, front_end, is_intro_heading)
    activity = find_index(lines, max(intro, 0), is_activity_heading)
    objectives = find_index(lines, max(activity, 0), is_objectives_heading)
    guide = find_index(lines, max(objectives, 0), is_guide_heading)
    conclusion = find_index(lines, max(guide, 0), is_conclusion_heading)
    annex = find_index(lines, conclusion + 1 if conclusion >= 0 else 0, is_annex_heading)

    if intro < 0:
        intro = front_end
    if activity < 0:
        activity = intro
    if objectives < 0:
        objectives = activity
    if guide < 0:
        guide = objectives
    if conclusion < 0:
        conclusion = len(lines)
    if annex < 0:
        annex = len(lines)

    toc_start = find_index(lines, 0, lambda line: lower_plain(line) == "contents")
    toc_end = min(funding_indices) if funding_indices else front_end

    return {
        "front": lines[:front_end],
        "toc": lines[toc_start + 1 : toc_end] if toc_start >= 0 else [],
        "eu": [lines[index] for index in funding_indices],
        "introduction": lines[intro:activity],
        "objectives": lines[objectives:guide],
        "guide": lines[guide:conclusion],
        "conclusion": lines[conclusion:annex],
        "annex": lines[annex:],
    }


def line_is_prompt(line: str) -> bool:
    plain = lower_plain(line)
    return any(plain.startswith(prefix) for prefix in PROMPT_PREFIXES)


def html_from_lines(lines: list[str]) -> str:
    blocks: list[str] = []
    for line in lines:
        text = normalized(line)
        if not text:
            continue
        escaped = html.escape(text, quote=False)
        plain = lower_plain(text)

        if re.match(r"^\d+(\.\d+)?\.?\s+", text) or plain in {
            "introduction",
            "module activity",
            "conclusion",
        }:
            blocks.append(f"<h3>{escaped}</h3>")
        elif len(text) <= 90 and text.endswith(":"):
            blocks.append(f"<p><strong>{escaped}</strong></p>")
        else:
            blocks.append(f"<p>{escaped}</p>")

    return "\n".join(blocks)


def first_substantive_line(lines: list[str]) -> str:
    for line in lines:
        text = normalized(line)
        if len(text) < 50:
            continue
        if line_is_prompt(text):
            continue
        if re.match(r"^\d+(\.\d+)?\.?\s+", text):
            continue
        return text
    return ""


def extract_exercise_title(guide: list[str]) -> str:
    for index, line in enumerate(guide):
        if not lower_plain(line).startswith("title of the exercise"):
            continue

        after_colon = line.split(":", 1)[1].strip() if ":" in line else ""
        after_colon = re.sub(r"\(?\s*provide a short.*", "", after_colon, flags=re.IGNORECASE).strip(" ()")
        if after_colon and not line_is_prompt(after_colon):
            return normalized(after_colon)

        for next_line in guide[index + 1 :]:
            candidate = normalized(next_line).split("Provide a short", 1)[0].strip()
            if candidate and not line_is_prompt(candidate):
                return candidate

    return ""


def extract_motto(conclusion: list[str], annex: list[str]) -> str:
    candidates = conclusion + annex

    for index, line in enumerate(candidates):
        text = normalized(line)
        if "motto:" in lower_plain(text):
            return text.split(":", 1)[1].strip() or text
        if "as a closing motto:" in lower_plain(text):
            return text.split(":", 1)[1].strip() or text
        if ("\"" in text or "\u201c" in text or "\u201d" in text) and len(text) <= 240:
            if index + 1 < len(candidates):
                next_line = normalized(candidates[index + 1])
                if 0 < len(next_line) <= 80 and not next_line.endswith("?"):
                    return f"{text} {next_line}"
            return text

    for line in reversed(candidates):
        text = normalized(line)
        if 20 <= len(text) <= 220 and not line_is_prompt(text) and not text.endswith("?"):
            return text

    return ""


def extract_outcomes(objectives: list[str]) -> list[dict[str, str]]:
    lower_lines = [lower_plain(line) for line in objectives]
    learning = next((i for i, line in enumerate(lower_lines) if line.startswith("learning outcomes")), -1)
    connection = next((i for i, line in enumerate(lower_lines) if line.startswith("the connection")), -1)

    rows: list[dict[str, str]] = []
    if learning >= 0:
        end = connection if connection > learning else len(objectives)
        detail_lines = objectives[learning + 1 : end]
        if detail_lines:
            rows.append(
                {
                    "outcome_title": objectives[learning].rstrip(":"),
                    "outcome_detail": html_from_lines(detail_lines),
                }
            )

    if connection >= 0:
        detail_lines = objectives[connection + 1 :]
        if detail_lines:
            rows.append(
                {
                    "outcome_title": objectives[connection].rstrip("."),
                    "outcome_detail": html_from_lines(detail_lines),
                }
            )

    if not rows and objectives:
        rows.append(
            {
                "outcome_title": "Objectives of the Activity",
                "outcome_detail": html_from_lines(objectives),
            }
        )

    return rows


def implementation_lines(guide: list[str]) -> list[str]:
    start = find_index(guide, 0, lambda line: lower_plain(line).startswith("step-by-step implementation"))
    end = find_index(guide, start + 1 if start >= 0 else 0, lambda line: lower_plain(line).startswith("tips for trainers"))
    if start < 0:
        return []
    if end < 0:
        end = len(guide)

    lines = guide[start + 1 : end]
    return [line for line in lines if not line_is_prompt(line) and not lower_plain(line).startswith("(maximum")]


def expand_numbered_lines(lines: list[str]) -> list[str]:
    expanded: list[str] = []
    for line in lines:
        parts = [part.strip() for part in re.split(r"(?=\b\d+\.\s+)", line) if part.strip()]
        expanded.extend(parts or [line])
    return expanded


def is_step_start(line: str) -> bool:
    text = normalized(line)
    if re.match(r"^\d+\.\s+", text):
        return True
    if "(approx" in lower_plain(text) and len(text) <= 110:
        return True
    if re.match(r"^[A-Z0-9 &'/-]{8,90}\(\d", text):
        return True
    return False


def extract_steps(guide: list[str]) -> list[dict[str, object]]:
    lines = expand_numbered_lines(implementation_lines(guide))
    if not lines:
        return []

    groups: list[list[str]] = []
    current: list[str] = []
    for line in lines:
        if is_step_start(line) and current:
            groups.append(current)
            current = [line]
        else:
            current.append(line)

    if current:
        groups.append(current)

    if len(groups) == 1 and len(lines) > 1:
        groups = [[line] for line in lines]

    rows: list[dict[str, object]] = []
    total = max(len(groups), 1)
    for index, group in enumerate(groups):
        title = normalized(group[0])
        content_lines = group[1:] if len(group) > 1 else group
        rows.append(
            {
                "step_title": title,
                "step_content": html_from_lines(content_lines),
                "step_hotspot_x": round(12 + (76 * index / max(total - 1, 1)), 2),
                "step_hotspot_y": 50,
            }
        )

    return rows


def extract_reflection_questions(conclusion: list[str]) -> list[str]:
    questions: list[str] = []
    for line in conclusion:
        text = normalized(line)
        if "?" in text:
            for part in re.split(r"(?<=[?])\s+", text):
                candidate = part.strip()
                if candidate.endswith("?"):
                    questions.append(candidate)
    return questions


def build_module(number: int, meta: dict[str, str], doc_root: Path) -> dict[str, object]:
    doc_path = doc_root / meta["file"]
    lines = extract_docx_lines(doc_path)
    sections = split_sections(lines)

    subtitle = extract_exercise_title(sections["guide"])
    description = first_substantive_line(sections["introduction"])
    motto = extract_motto(sections["conclusion"], sections["annex"])

    chapters = [
        {
            "chapter_title": "Objectives of the Activity",
            "chapter_content": html_from_lines(sections["objectives"]),
        },
        {
            "chapter_title": "Step-by-Step Guide of the Activity",
            "chapter_content": html_from_lines(sections["guide"]),
        },
    ]

    if sections["annex"]:
        chapters.append(
            {
                "chapter_title": normalized(sections["annex"][0]) or "Annex",
                "chapter_content": html_from_lines(sections["annex"]),
            }
        )

    return {
        "preserve_existing_media": True,
        "source_document": str(doc_path),
        "post_title": meta["title"],
        "post_name": meta["slug"],
        "module_number": number,
        "module_subtitle": subtitle,
        "module_description": description,
        "module_color": meta["color"],
        "module_motto": motto,
        "module_video_url": "",
        "module_eu_funding_text": "\n".join(sections["eu"]),
        "module_table_of_contents": "\n".join(sections["toc"]),
        "module_introduction": html_from_lines(sections["introduction"]),
        "module_learning_outcomes": extract_outcomes(sections["objectives"]),
        "module_chapters": chapters,
        "module_exercise_steps": extract_steps(sections["guide"]),
        "module_conclusion": html_from_lines(sections["conclusion"]),
        "module_reflection_questions": extract_reflection_questions(sections["conclusion"]),
        "module_assessment_questions": [],
        "_source_counts": {key: len(value) for key, value in sections.items()},
    }


def write_preview(manifest: dict[str, object], path: Path) -> None:
    lines = [
        "# WELLME DOCX Content Import Preview",
        "",
        "The six Word documents in `WP 3.1. Creating Hands On Training` are treated as the only text source.",
        "Template/instruction lines are retained when they exist in the DOCX files.",
        "Existing cover images, icons, and galleries are preserved by the import package.",
        "",
        "| Module | Source doc | Intro lines | Outcome rows | Step rows | Chapter rows | Reflection questions |",
        "|---:|---|---:|---:|---:|---:|---:|",
    ]

    for module in manifest["modules"]:
        counts = module["_source_counts"]
        source = Path(module["source_document"]).name
        lines.append(
            "| {number} | `{source}` | {intro} | {outcomes} | {steps} | {chapters} | {questions} |".format(
                number=module["module_number"],
                source=source,
                intro=counts["introduction"],
                outcomes=len(module["module_learning_outcomes"]),
                steps=len(module["module_exercise_steps"]),
                chapters=len(module["module_chapters"]),
                questions=len(module["module_reflection_questions"]),
            )
        )

    path.write_text("\n".join(lines) + "\n", encoding="utf-8")


def main() -> int:
    args = parse_args()
    args.output_root.mkdir(parents=True, exist_ok=True)

    manifest = {
        "source": {
            "type": "wp3.1-hands-on-training-docx",
            "doc_root": str(args.doc_root),
        },
        "generated_at": datetime.now(timezone.utc).isoformat(),
        "modules": [
            build_module(number, meta, args.doc_root)
            for number, meta in sorted(MODULES.items())
        ],
    }

    manifest_for_import = json.loads(json.dumps(manifest, ensure_ascii=False))
    for module in manifest_for_import["modules"]:
        module.pop("_source_counts", None)

    manifest_path = args.output_root / "manifest.json"
    zip_path = args.output_root / "wellme-docx-content-import.zip"
    preview_path = args.output_root / "wellme-docx-content-preview.md"

    manifest_path.write_text(
        json.dumps(manifest_for_import, ensure_ascii=False, indent=2) + "\n",
        encoding="utf-8",
    )
    write_preview(manifest, preview_path)

    with zipfile.ZipFile(zip_path, "w", compression=zipfile.ZIP_DEFLATED) as package:
        package.write(manifest_path, "manifest.json")

    print(f"Wrote {zip_path}")
    print(f"Wrote {manifest_path}")
    print(f"Wrote {preview_path}")
    return 0


if __name__ == "__main__":
    raise SystemExit(main())
