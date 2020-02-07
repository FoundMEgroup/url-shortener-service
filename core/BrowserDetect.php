<?php

namespace BertMaurau\URLShortener\Core;

/**
 * Description of BrowserDetect
 *
 * @author bertmaurau
 */
class BrowserDetect
{

    /**
     * Render the BrowserDetect page
     *
     * @param string $url The URL to redirect to
     * @param string $trackerGuid The Tracker GUID
     *
     * @throws \Exception
     *
     * @return void
     */
    public static function render(string $url, string $trackerGuid): void
    {

        // check for template
        $bodyFile = Config::getInstance() -> Paths() -> templates . 'browserDetect.html';
        if (!file_exists($bodyFile)) {
            throw new \Exception('Missing template-file `browserDetect.html`.');
        } else {
            $body = file_get_contents($bodyFile);
        }

        // load values
        $body = str_replace('{{URL}}', $url, $body);
        $body = str_replace('{{TRACKER_URL}}', (Config::getInstance() -> Paths() -> base_url . '/url-request?guid=' . $trackerGuid), $body);

        // render on screen
        echo $body;
    }

}
