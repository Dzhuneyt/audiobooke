---
category: Audiobooks
url_path: '/v1/audiobooks/{id}'
title: 'Single'
type: 'GET'

layout: null
---
Retrieve a paginated list of all audiobooks.

### Request Parameters:
* _page (integer, optional)_

### Response

Sends back a collection of audiobooks.

```Status: 200 OK```


* items (array, required) - An array of Audiobook objects
    * id (integer, required)
    * title (string, required)
    * description (string, required)
    * author_name (string, required)
    * cover_url (string, required)
    * language (string, required) - E.g. "English"
    * num_sections (integer, required) - The number of chapters within the audiobook
    * total_seconds (integer, required) - The total duration of the audiobook, in seconds
    * year (integer, required) - The year the audiobook was first released
* _meta (Object, required)
    * currentPage (integer, required)
    * pageCount (integer, required)
    * perPage (integer, required)
    * totalCount (integer, required)
