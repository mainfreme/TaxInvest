#!/usr/bin/env sh
set -eu

ROOT_DIR="$(CDPATH= cd -- "$(dirname -- "$0")/.." && pwd)"
INPUT="${ROOT_DIR}/src/DataImport/docs/architecture-flow.mmd"
PNG_OUTPUT="${ROOT_DIR}/src/DataImport/docs/architecture-flow.png"
JPG_OUTPUT="${ROOT_DIR}/src/DataImport/docs/architecture-flow.jpg"

npx --yes @mermaid-js/mermaid-cli \
  -i "${INPUT}" \
  -o "${PNG_OUTPUT}" \
  -b white

if command -v sips >/dev/null 2>&1; then
  sips -s format jpeg "${PNG_OUTPUT}" --out "${JPG_OUTPUT}" >/dev/null
else
  cp "${PNG_OUTPUT}" "${JPG_OUTPUT}"
fi

echo "Generated ${JPG_OUTPUT}"
