import requests, os, sys, subprocess

TAG     = "v1.2.102"
VERSION = "1.2.102"
owner   = "GeorgeWebDevCy"
repo    = "wellme-pamphlets"

# ---------------------------------------------------------------------------
# Token - try gh CLI (Linux/Mac path first, then Windows path), then env var
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
    "name": f"{TAG} - Activity Steps line layout fix",
    "body": """## What's new in v1.2.102

### Activity Steps line layout fix

Restored the Activity Steps presentation to the line-style hotspot layout from yesterday's versions while keeping the requested fixes:

- Removed the hotspot base image from Activity Steps.
- Kept numbered hotspot dots laid out in a horizontal line.
- Ensured Activity Steps render in stable sequential order.
- Restored hotspot coordinate fields/import payloads for compatibility.

### Files changed
- `admin/class-wellme-pamphlets-importer.php`
- `includes/class-wellme-pamphlets-acf.php`
- `public/css/wellme-pamphlets-public.css`
- `public/js/wellme-pamphlets-public.js`
- `public/partials/wellme-pamphlet.php`
- `scripts/build-docx-content-package.py`
- `scripts/build-import-package.py`
- `wellme-pamphlets.php` (version bump)
""",
    "draft": False,
    "prerelease": False,
}

# ---------------------------------------------------------------------------
# Zip path - relative to this script's location (scripts/ to dist/)
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
