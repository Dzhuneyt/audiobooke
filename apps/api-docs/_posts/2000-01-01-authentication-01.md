---
category: User
url_path: '/v1/user/sso'
title: 'Start SSO flow'
type: 'GET'

layout: null
---
This endpoint starts the SSO login procedure with the third party SSO provider (e.g. Google+).

### Request parameters

* _domain (required):_ The current URL of the website, including scheme
* _provider (required):_ Currently the only supported value is "googleplus"

### Response

```Status: 200 OK```
```{
    auth_url: [Redirect URL here]
}```

The client is then responsible of redirecting the user to the returned Redirect URL, which usually initiates the SSO flow with the third party provider, e.g. Google.
