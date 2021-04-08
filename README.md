
# URL Shortener ~~& Tracker~~
*Stripped-down version to purely use as a short-link creator and redirector. This to use in combination with the Leadcamp platform, which has no need for user management, tracking, browser detection, ..*

## Description

A simple and basic URL shortener service ~~with back-end tracking and analytic~~.

Convert your long URL to a short URL referenced by a short code. ~~The service also allows for `aliases` to personalize your short URL.~~

~~By default, every URL request gets tracked. This includes basic timestamps and the geolocation. When `BrowserDetect` has been turned on for the shortened URL, a loading page will be previewed to fetch the client's browser information first before redirecting. This includes the platform, the browser, language, ..~~

## Usage

Make a `POST` request to the `/urls` endpoint with the following payload:

**Header**: 
`Authorization: Bearer <leadcamp-user-token>`

**Body**:
```json
{
	// The full URL you want to shorten (up to 1024 characters)
	"url": "your-url-to-shorten",
	
	// By default, if there already exists a shortened-version of your full URL, from the same
	// User ID, that one will be returned. Setting `force_new` to `true` will generate a new
	// unique one.
	"force_new": false 
}
```

This will then return a response body like:

**Response**:
```json
{
	"leadcamp_user_id": 5,
	"short_code": "K_Eq2QkjenQN",
	"url": "https://www.leadcamp.io",
	"attributes": {
		"target_url_short": "http://lcu.ninja/K_Eq2QkjenQN",
		"target_url": "http://link.leadcamp.ninja/K_Eq2QkjenQN"
	},
	"id": 13,
	"updated_at": {
		"date": "2021-04-07 15:48:01.000000",
		"timezone_type": 3,
		"timezone": "UTC"
	},
	"created_at": {
		"date": "2021-04-07 15:48:01.000000",
		"timezone_type": 3,
		"timezone": "UTC"
	}
}
```
