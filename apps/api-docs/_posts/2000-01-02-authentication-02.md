---
category: User
url_path: '/v1/user/sso'
title: 'Finish SSO flow'
type: 'GET'

layout: null
---
This endpoint allows users to complete the SSO procedure after being returned by the third party SSO provider. After this endpoint is called, the user will receive an oAuth2 token and will be fully registered and logged in within our system.

### Request parameters

* _domain (required):_ The current URL of the website, including scheme
* _provider (required):_ Currently the only supported value is "googleplus"
* _[any-other-parameter-here] (optional):_ Any third-party provider specific parameters that the redirect yielded. E.g. "code"

### Response

```Status: 200 OK```
* access_token (string required): The oAuth2 access token that is generated for this user
* expires_in (integer, required): A numeric value after which the token is to be considered invalid (in seconds)
* scope (string, optional): Not implemented
* token_type (string, optional): The only possible value currently is - "Bearer"
