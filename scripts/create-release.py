import requests, os, sys

token = os.popen("/mnt/c/Program Files/GitHub CLI/gh.exe auth token 2>/dev/null").read().strip()
owner = "GeorgeWebDevCy"
repo = "wellme-pamphlets"

headers = {
    "Authorization": f"token {token}",
    "Accept": "application/vnd.github.v3+json",
}

# Create the release
release_data = {
    "tag_name": "v1.0.9",
    "name": "v1.0.9 - 5-Slide Interactive Presentation",
    "body": """## What is new

### 5-Slide Interactive Presentation ([wellme_experience])
- **Slide 1**: WELLME Landing — rotating logo, project title, EU branding
- **Slide 2**: Partnership — clickable partner cards with contact details (Partou pattern)
- **Slide 3**: Wellme Overview — purpose, need, expected results (ACF editable)
- **Slide 4**: Modules — 6 clickable cards opening bottom drawer with full pamphlet content
- **Slide 5**: Sum-Up — 6 flip cards with photos on front, mottos on back

### Navigation & Interactions
- Prev/Next arrows, dot indicators, slide counter
- Keyboard arrow keys, touch swipe support
- Bottom drawer opens when clicking module cards on Slide 4
- Flip cards animate on click in Slide 5

### Pamphlet Content (Partou + Outremer patterns)
- Learning outcomes with expandable side panels
- Exercise steps with pulsing hotspot dots on images
- Chapter navigation with tab buttons
- Interactive assessment quizzes
- Reflection questions display

### ACF Options Page
- New Presentation Settings page under WELLME Modules
- Upload WellME logo, EU logo, add partners, overview text
- All module content editable via ACF fields

### Bug Fixes
- Fix: flip cards no longer trigger on keyboard focus (tab)

### Setup
Add the shortcode [wellme_experience] to any page to show the full presentation.""",
    "draft": False,
    "prerelease": False,
}

# Check if release already exists
existing = requests.get(
    f"https://api.github.com/repos/{owner}/{repo}/releases/tags/v1.0.9",
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
        print(resp.text[:300])
        sys.exit(1)

# Upload the plugin zip
zip_path = "/mnt/d/GitHub Projects/wellme-pamphlets/dist/wellme-pamphlets.zip"
upload_url = f"https://uploads.github.com/repos/{owner}/{repo}/releases/{release_id}/assets?name=wellme-pamphlets.zip"

with open(zip_path, "rb") as f:
    resp = requests.post(
        upload_url,
        headers={**headers, "Content-Type": "application/zip"},
        data=f,
        timeout=30,
    )
    print(f"Upload zip: {resp.status_code}")
    if resp.status_code in (200, 201):
        print(f"Asset URL: {resp.json()['browser_download_url']}")
    else:
        print(resp.text[:300])

print("Done!")
