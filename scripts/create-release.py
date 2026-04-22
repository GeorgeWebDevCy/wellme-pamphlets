import requests, os, sys, subprocess

TAG     = "v1.2.0"
VERSION = "1.2.0"
owner   = "GeorgeWebDevCy"
repo    = "wellme-pamphlets"

# ---------------------------------------------------------------------------
# Token — try gh CLI (Linux/Mac path first, then Windows path), then env var
# ---------------------------------------------------------------------------
def get_token():
    candidates = [
        ["gh", "auth", "token"],
        [r"C:\Program Files\GitHub CLI\gh.exe", "auth", "token"],
    ]
    for cmd in candidates:
        try:
            result = subprocess.run(cmd, capture_output=True, text=True, timeout=5)
            tok = result.stdout.strip()
            if tok:
                return tok
        except Exception:
            pass
    tok = os.environ.get("GITHUB_TOKEN", "")
    if tok:
        return tok
    sys.exit("ERROR: No GitHub token found. Run `gh auth login` or set GITHUB_TOKEN.")

token = get_token()

headers = {
    "Authorization": f"token {token}",
    "Accept": "application/vnd.github.v3+json",
}

# ---------------------------------------------------------------------------
# Release metadata
# ---------------------------------------------------------------------------
release_data = {
    "tag_name": TAG,
    "name": f"{TAG} — Mazda-style 5-Slide Experience + Updater",
    "body": """## What's new in v1.2.0

### 5-Slide Interactive Experience redesign ([wellme_experience])
The experience now follows a Mazda MX-5 / Maglr digital-brochure layout:

- **Slide 1 — WELLME Landing**: Rotating logo, project title, EU branding (partners removed from this slide)
- **Slide 2 — Partnership**: Dedicated full slide with clickable partner cards
- **Slide 3 — Overview**: Purpose, need, expected results (ACF-editable)
- **Slide 4 — Modules**: 6 clickable inline cards → modal with full pamphlet content
- **Slide 5 — Sum-Up**: 6 flip cards with photos on front, module mottos on back

### Mazda-style top navigation bar
- Dark frosted-glass bar (`wellme-exp-topnav`) fixed at the top of the experience
- Clickable chapter tabs — WELLME / Partnership / Overview / Modules / Sum-Up
- Active tab highlighted with an animated underline
- Slide counter (e.g. 2 / 5) on the right

### Reader mode (white A4 pages on dark background)
- Partnership slide now renders as a proper white reader-mode page
- All 5 slides fully styled for the Publitas/Maglr reader aesthetic

### Plugin auto-updater
- Uses `yahnis-elsts/plugin-update-checker` v5
- WordPress will detect new releases from this GitHub repo and offer one-click updates
- Release asset `wellme-pamphlets.zip` is the installable plugin package

### Setup
Add `[wellme_experience]` to any page for the full 5-slide interactive experience.""",
    "draft": False,
    "prerelease": False,
}

# ---------------------------------------------------------------------------
# Zip path — relative to this script's location (scripts/ → dist/)
# ---------------------------------------------------------------------------
SCRIPT_DIR = os.path.dirname(os.path.abspath(__file__))
ZIP_PATH   = os.path.normpath(os.path.join(SCRIPT_DIR, "..", "dist", "wellme-pamphlets.zip"))

if not os.path.exists(ZIP_PATH):
    sys.exit(f"ERROR: zip not found at {ZIP_PATH}\nRun the build script first.")

# ---------------------------------------------------------------------------
# Create or fetch the release
# ---------------------------------------------------------------------------
existing = requests.get(
    f"https://api.github.com/repos/{owner}/{repo}/releases/tags/{TAG}",
    headers=headers,
    timeout=15,
)

if existing.status_code == 200:
    print(f"Release already exists: {existing.json()['html_url']}")
    release_id = existing.json()["id"]
else:
    resp = requests.post(
        f"https://api.github.com/repos/{owner}/{repo}/releases",
        headers=headers,
        json=release_data,
        timeout=30,
    )
    print(f"Create release: {resp.status_code}")
    if resp.status_code in (200, 201):
        release_id = resp.json()["id"]
        print(f"URL: {resp.json()['html_url']}")
    else:
        print(resp.text[:500])
        sys.exit(1)

# ---------------------------------------------------------------------------
# Upload the plugin zip as a release asset
# ---------------------------------------------------------------------------
# Delete existing asset with the same name first (re-run safety)
assets_resp = requests.get(
    f"https://api.github.com/repos/{owner}/{repo}/releases/{release_id}/assets",
    headers=headers,
    timeout=15,
)
if assets_resp.status_code == 200:
    for asset in assets_resp.json():
        if asset["name"] == "wellme-pamphlets.zip":
            del_resp = requests.delete(
                f"https://api.github.com/repos/{owner}/{repo}/releases/assets/{asset['id']}",
                headers=headers,
                timeout=15,
            )
            print(f"Deleted existing asset: {del_resp.status_code}")

upload_url = (
    f"https://uploads.github.com/repos/{owner}/{repo}"
    f"/releases/{release_id}/assets?name=wellme-pamphlets.zip"
)

with open(ZIP_PATH, "rb") as f:
    resp = requests.post(
        upload_url,
        headers={**headers, "Content-Type": "application/zip"},
        data=f,
        timeout=120,
    )
    print(f"Upload zip: {resp.status_code}")
    if resp.status_code in (200, 201):
        print(f"Asset URL: {resp.json()['browser_download_url']}")
    else:
        print(resp.text[:500])
        sys.exit(1)

print("Done! Plugin update checker will detect the new release automatically.")
