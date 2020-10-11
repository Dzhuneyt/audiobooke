---
category: Audiobooks
url_path: '/v1/audiobooks/download/{id}'
title: 'Download'
type: 'GET'

layout: null
---
Get a download link for a single audiobook

### Request Header

* The headers must include a **valid authentication token**.

### Response

* download_url (string, required) - URL to download the audiobook (zip file)
