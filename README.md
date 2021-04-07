# URL Shortener & Tracker
## Description

A simple and basic URL shortener service with back-end tracking and analytic.

Convert your long URL to a short URL referenced by a short code. The service also allows for `aliases` to personalize your short URL.

By default, every URL request gets tracked. This includes basic timestamps and the geolocation. When `BrowserDetect` has been turned on for the shortened URL, a loading page will be previewed to fetch the client's browser information first before redirecting. This includes the platform, the browser, language, ..

*Stripped-down version to purely use as a short-link creator and redirector to use with the Leadcamp platform. No need for user management, tracking, browser detection, ..*