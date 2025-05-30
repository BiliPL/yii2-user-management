<?php

/*
 * This file is part of the Ikimea Browser.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace bilipl\modules\UserManagement\browser\lib\Ikimea\Browser;

class Browser
{
    private $_agent = '';
    private $_browser_name = '';
    private $_version = '';
    private $_platform = '';
    private $_os = '';
    private $_is_aol = false;
    private $_is_mobile = false;
    private $_is_robot = false;
    private $_aol_version = '';

    const BROWSER_UNKNOWN = 'unknown';
    const VERSION_UNKNOWN = 'unknown';

    const BROWSER_YANDEX = "Yandex Browser"; // http://browser.yandex.ru/
    const BROWSER_OPERA = 'Opera'; // http://www.opera.com/
    const BROWSER_OPERA_MINI = 'Opera Mini'; // http://www.opera.com/mini/
    const BROWSER_OPERA_CHROMIUM = 'Opera Chromium'; // http://www.opera.com/
    const BROWSER_WEBTV = 'WebTV'; // http://www.webtv.net/pc/
    const BROWSER_IE = 'Internet Explorer'; // http://www.microsoft.com/ie/
    const BROWSER_EDGE = 'Edge'; // https://www.microsoft.com/en-us/windows/microsoft-edge
    const BROWSER_POCKET_IE = 'Pocket Internet Explorer'; // http://en.wikipedia.org/wiki/Internet_Explorer_Mobile
    const BROWSER_KONQUEROR = 'Konqueror'; // http://www.konqueror.org/
    const BROWSER_ICAB = 'iCab'; // http://www.icab.de/
    const BROWSER_OMNIWEB = 'OmniWeb'; // http://www.omnigroup.com/applications/omniweb/
    const BROWSER_FIREBIRD = 'Firebird'; // http://www.ibphoenix.com/
    const BROWSER_FIREFOX = 'Firefox'; // http://www.mozilla.com/en-US/firefox/firefox.html
    const BROWSER_ICEWEASEL = 'Iceweasel'; // http://www.geticeweasel.org/
    const BROWSER_SHIRETOKO = 'Shiretoko'; // http://wiki.mozilla.org/Projects/shiretoko
    const BROWSER_MOZILLA = 'Mozilla'; // http://www.mozilla.com/en-US/
    const BROWSER_AMAYA = 'Amaya'; // http://www.w3.org/Amaya/
    const BROWSER_LYNX = 'Lynx'; // http://en.wikipedia.org/wiki/Lynx
    const BROWSER_SAFARI = 'Safari'; // http://apple.com
    const BROWSER_IPHONE = 'iPhone'; // http://apple.com
    const BROWSER_IPOD = 'iPod'; // http://apple.com
    const BROWSER_IPAD = 'iPad'; // http://apple.com
    const BROWSER_CHROME = 'Chrome'; // http://www.google.com/chrome
    const BROWSER_ANDROID = 'Android'; // http://www.android.com/
    const BROWSER_GOOGLEBOT = 'GoogleBot'; // http://en.wikipedia.org/wiki/Googlebot
    const BROWSER_SLURP = 'Yahoo! Slurp'; // http://en.wikipedia.org/wiki/Yahoo!_Slurp
    const BROWSER_W3CVALIDATOR = 'W3C Validator'; // http://validator.w3.org/
    const BROWSER_BLACKBERRY = 'BlackBerry'; // http://www.blackberry.com/
    const BROWSER_ICECAT = 'IceCat'; // http://en.wikipedia.org/wiki/GNU_IceCat
    const BROWSER_NOKIA_S60 = 'Nokia S60 OSS Browser'; // http://en.wikipedia.org/wiki/Web_Browser_for_S60
    const BROWSER_NOKIA = 'Nokia Browser'; // * all other WAP-based browsers on the Nokia Platform
    const BROWSER_MSN = 'MSN Browser'; // http://explorer.msn.com/
    const BROWSER_MSNBOT = 'MSN Bot'; // http://search.msn.com/msnbot.htm
    // http://en.wikipedia.org/wiki/Msnbot  (used for Bing as well)

    const BROWSER_NETSCAPE_NAVIGATOR = 'Netscape Navigator'; // http://browser.netscape.com/ (DEPRECATED)
    const BROWSER_GALEON = 'Galeon'; // http://galeon.sourceforge.net/ (DEPRECATED)
    const BROWSER_NETPOSITIVE = 'NetPositive'; // http://en.wikipedia.org/wiki/NetPositive (DEPRECATED)
    const BROWSER_PHOENIX = 'Phoenix'; // http://en.wikipedia.org/wiki/History_of_Mozilla_Firefox (DEPRECATED)

    const PLATFORM_UNKNOWN = 'unknown';
    const PLATFORM_WINDOWS = 'Windows';
    const PLATFORM_WINDOWS_CE = 'Windows CE';
    const PLATFORM_APPLE = 'mac';
    const PLATFORM_LINUX = 'Linux';
    const PLATFORM_OS2 = 'OS/2';
    const PLATFORM_BEOS = 'BeOS';
    const PLATFORM_IPHONE = 'iPhone';
    const PLATFORM_IPOD = 'iPod';
    const PLATFORM_IPAD = 'iPad';
    const PLATFORM_BLACKBERRY = 'BlackBerry';
    const PLATFORM_NOKIA = 'Nokia';
    const PLATFORM_FREEBSD = 'FreeBSD';
    const PLATFORM_OPENBSD = 'OpenBSD';
    const PLATFORM_NETBSD = 'NetBSD';
    const PLATFORM_SUNOS = 'SunOS';
    const PLATFORM_OPENSOLARIS = 'OpenSolaris';
    const PLATFORM_ANDROID = 'Android';
    const PLATFORM_WINDOWSPHONE = 'Windows Phone';

    const OPERATING_SYSTEM_UNKNOWN = 'unknown';

    public function __construct($useragent = null)
    {
        $this->reset();
        if (null != $useragent) {
            $this->setUserAgent($useragent);
        } else {
            $this->determine();
        }
    }

    /**
     * Reset all properties.
     */
    public function reset()
    {
        $this->_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
        $this->_browser_name = self::BROWSER_UNKNOWN;
        $this->_version = self::VERSION_UNKNOWN;
        $this->_platform = self::PLATFORM_UNKNOWN;
        $this->_os = self::OPERATING_SYSTEM_UNKNOWN;
        $this->_is_aol = false;
        $this->_is_mobile = false;
        $this->_is_robot = false;
        $this->_aol_version = self::VERSION_UNKNOWN;
    }

    /**
     * Check to see if the specific browser is valid.
     *
     * @param string $browserName
     *
     * @return True if the browser is the specified browser
     */
    public function isBrowser($browserName)
    {
        return (0 == strcasecmp($this->_browser_name, trim($browserName)));
    }

    /**
     * The name of the browser.  All return types are from the class contants.
     *
     * @return string Name of the browser
     */
    public function getBrowser()
    {
        return $this->_browser_name;
    }

    /**
     * Set the name of the browser.
     *
     * @param $browser The name of the Browser
     */
    public function setBrowser($browser)
    {
        return $this->_browser_name = $browser;
    }

    /**
     * The name of the platform.  All return types are from the class contants.
     *
     * @return string Name of the browser
     */
    public function getPlatform()
    {
        return $this->_platform;
    }

    /**
     * Set the name of the platform.
     *
     * @param $platform The name of the Platform
     */
    public function setPlatform($platform)
    {
        return $this->_platform = $platform;
    }

    /**
     * The version of the browser.
     *
     * @return string Version of the browser (will only contain alpha-numeric characters and a period)
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Set the version of the browser.
     *
     * @param $version The version of the Browser
     */
    public function setVersion($version)
    {
        $this->_version = preg_replace('/[^0-9,.,a-z,A-Z-]/', '', $version);
    }

    /**
     * Return the Major version of the browser (Chrome 55.0.2883 => 55). If it
     * can't properly parse the Major version from the version (e.g. There are
     * alphabetical-characters in the version string) it will just return the
     * full version.
     *
     * @return string Major version of the Browser.
     */
    public function getMajorVersion()
    {
        return (floor($this->_version) ?: $this->_version);
    }

    /**
     * The version of AOL.
     *
     * @return string Version of AOL (will only contain alpha-numeric characters and a period)
     */
    public function getAolVersion()
    {
        return $this->_aol_version;
    }

    /**
     * Set the version of AOL.
     *
     * @param $version The version of AOL
     */
    public function setAolVersion($version)
    {
        $this->_aol_version = preg_replace('/[^0-9,.,a-z,A-Z]/', '', $version);
    }

    /**
     * Is the browser from AOL?
     *
     * @return bool True if the browser is from AOL otherwise false
     */
    public function isAol()
    {
        return $this->_is_aol;
    }

    /**
     * Is the browser from a mobile device?
     *
     * @return bool True if the browser is from a mobile device otherwise false
     */
    public function isMobile()
    {
        return $this->_is_mobile;
    }

    /**
     * Is the browser from a robot (ex Slurp,GoogleBot)?
     *
     * @return bool True if the browser is from a robot otherwise false
     */
    public function isRobot()
    {
        return $this->_is_robot;
    }

    /**
     * Set the browser to be from AOL.
     *
     * @param $isAol
     */
    public function setAol($isAol)
    {
        $this->_is_aol = $isAol;
    }

    /**
     * Set the Browser to be mobile.
     *
     * @param bool $value is the browser a mobile brower or not
     */
    protected function setMobile($value = true)
    {
        $this->_is_mobile = $value;
    }

    /**
     * Set the Browser to be a robot.
     *
     * @param bool $value is the browser a robot or not
     */
    protected function setRobot($value = true)
    {
        $this->_is_robot = $value;
    }

    /**
     * Get the user agent value in use to determine the browser.
     *
     * @return string The user agent from the HTTP header
     */
    public function getUserAgent()
    {
        return $this->_agent;
    }

    /**
     * Set the user agent value (the construction will use the HTTP header value - this will overwrite it).
     *
     * @param $agent_string The value for the User Agent
     */
    public function setUserAgent($agent_string)
    {
        $this->reset();
        $this->_agent = $agent_string;
        $this->determine();
    }

    /**
     * Used to determine if the browser is actually "chromeframe".
     *
     * @since 1.7
     *
     * @return bool True if the browser is using chromeframe
     */
    public function isChromeFrame()
    {
        return (strpos($this->_agent, 'chromeframe') !== false);
    }

    /**
     * Returns a formatted string with a summary of the details of the browser.
     *
     * @return string formatted string with a summary of the browser
     */
    public function __toString()
    {
        return "<strong>Browser Name:</strong>{$this->getBrowser()}<br/>\n".
        "<strong>Browser Version:</strong>{$this->getVersion()}<br/>\n".
        "<strong>Browser User Agent String:</strong>{$this->getUserAgent()}<br/>\n".
        "<strong>Platform:</strong>{$this->getPlatform()}<br/>";
    }

    /**
     * Protected routine to calculate and determine what the browser is in use (including platform).
     */
    protected function determine()
    {
        $this->checkPlatform();
        $this->checkBrowsers();
        $this->checkForAol();
    }

    /**
     * Protected routine to determine the browser type.
     *
     * @return bool True if the browser was detected otherwise false
     */
    protected function checkBrowsers()
    {
        return (
            // well-known, well-used
            // Special Notes:
            // (1) Opera must be checked before FireFox due to the odd
            //     user agents used in some older versions of Opera
            // (2) WebTV is strapped onto Internet Explorer so we must
            //     check for WebTV before IE
            // (3) (deprecated) Galeon is based on Firefox and needs to be
            //     tested before Firefox is tested
            // (4) OmniWeb is based on Safari so OmniWeb check must occur
            //     before Safari
            // (5) Netscape 9+ is based on Firefox so Netscape checks
            //     before FireFox are necessary
            $this->checkBrowserWebTv() ||
            $this->checkBrowserYandex() ||
            $this->checkBrowserInternetExplorer() ||
            $this->checkBrowserEdge() ||
            $this->checkBrowserOpera() ||
            $this->checkBrowserGaleon() ||
            $this->checkBrowserNetscapeNavigator9Plus() ||
            $this->checkBrowserFirefox() ||
            $this->checkBrowserChrome() ||
            $this->checkBrowserOmniWeb() ||

            // common mobile
            $this->checkBrowserAndroid() ||
            $this->checkBrowseriPad() ||
            $this->checkBrowseriPod() ||
            $this->checkBrowseriPhone() ||
            $this->checkBrowserBlackBerry() ||
            $this->checkBrowserNokia() ||

            // common bots
            $this->checkBrowserGoogleBot() ||
            $this->checkBrowserMSNBot() ||
            $this->checkBrowserSlurp() ||

            // WebKit base check (post mobile and others)
            $this->checkBrowserSafari() ||

            // everyone else
            $this->checkBrowserNetPositive() ||
            $this->checkBrowserFirebird() ||
            $this->checkBrowserKonqueror() ||
            $this->checkBrowserIcab() ||
            $this->checkBrowserPhoenix() ||
            $this->checkBrowserAmaya() ||
            $this->checkBrowserLynx() ||
            $this->checkBrowserShiretoko() ||
            $this->checkBrowserIceCat() ||
            $this->checkBrowserW3CValidator() ||
            $this->checkBrowserMozilla() /* Mozilla is such an open standard that you must check it last */
        );
    }

    /**
     * Determine if the browser is Yandex browser or not.
     *
     * @return bool True if the browser is Yandex otherwise false
     */
    protected function checkBrowserYandex()
    {
        if (stripos($this->_agent, 'YaBrowser') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'YaBrowser'));
            $aversion = explode(' ', $aresult[1]);
            $this->setVersion($aversion[0]);
            $this->setBrowser(self::BROWSER_YANDEX);

            return true;
        }

        return false;
    }

    /**
     * Determine if the user is using a BlackBerry (last updated 1.7).
     *
     * @return bool True if the browser is the BlackBerry browser otherwise false
     */
    protected function checkBrowserBlackBerry()
    {
        if (stripos($this->_agent, 'blackberry') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'BlackBerry'));
            $aversion = explode(' ', $aresult[1]);
            $this->setVersion($aversion[0]);
            $this->_browser_name = self::BROWSER_BLACKBERRY;

            return true;
        }

        return false;
    }

    /**
     * Determine if the user is using an AOL User Agent (last updated 1.7).
     *
     * @return bool True if the browser is from AOL otherwise false
     */
    protected function checkForAol()
    {
        $this->setAol(false);
        $this->setAolVersion(self::VERSION_UNKNOWN);

        if (stripos($this->_agent, 'aol') !== false) {
            $aversion = explode(' ', stristr($this->_agent, 'AOL'));
            $this->setAol(true);
            $this->setAolVersion(preg_replace('/[^0-9\.a-z]/i', '', $aversion[1]));

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is the GoogleBot or not (last updated 1.7).
     *
     * @return bool True if the browser is the GoogletBot otherwise false
     */
    protected function checkBrowserGoogleBot()
    {
        if (stripos($this->_agent, 'googlebot') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'googlebot'));
            $aversion = explode(' ', $aresult[1]);
            $this->setVersion(str_replace(';', '', $aversion[0]));
            $this->_browser_name = self::BROWSER_GOOGLEBOT;
            $this->setRobot(true);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is the MSNBot or not (last updated 1.9).
     *
     * @return bool True if the browser is the MSNBot otherwise false
     */
    protected function checkBrowserMSNBot()
    {
        if (stripos($this->_agent, 'msnbot') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'msnbot'));
            $aversion = explode(' ', $aresult[1]);
            $this->setVersion(str_replace(';', '', $aversion[0]));
            $this->_browser_name = self::BROWSER_MSNBOT;
            $this->setRobot(true);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is the W3C Validator or not (last updated 1.7).
     *
     * @return bool True if the browser is the W3C Validator otherwise false
     */
    protected function checkBrowserW3CValidator()
    {
        if (stripos($this->_agent, 'W3C-checklink') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'W3C-checklink'));
            $aversion = explode(' ', $aresult[1]);
            $this->setVersion($aversion[0]);
            $this->_browser_name = self::BROWSER_W3CVALIDATOR;

            return true;
        } elseif (stripos($this->_agent, 'W3C_Validator') !== false) {
            // Some of the Validator versions do not delineate w/ a slash - add it back in
            $ua = str_replace('W3C_Validator ', 'W3C_Validator/', $this->_agent);
            $aresult = explode('/', stristr($ua, 'W3C_Validator'));
            $aversion = explode(' ', $aresult[1]);
            $this->setVersion($aversion[0]);
            $this->_browser_name = self::BROWSER_W3CVALIDATOR;

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is the Yahoo! Slurp Robot or not (last updated 1.7).
     *
     * @return bool True if the browser is the Yahoo! Slurp Robot otherwise false
     */
    protected function checkBrowserSlurp()
    {
        if (stripos($this->_agent, 'slurp') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'Slurp'));
            $aversion = explode(' ', $aresult[1]);
            $this->setVersion($aversion[0]);
            $this->_browser_name = self::BROWSER_SLURP;
            $this->setRobot(true);
            $this->setMobile(false);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Internet Explorer or not (last updated 1.7).
     *
     * @return bool True if the browser is Internet Explorer otherwise false
     */
    protected function checkBrowserInternetExplorer()
    {
        //  Test for IE11
        if (stripos($this->_agent, 'Trident/7.0') !== false && stripos($this->_agent, 'rv:11.0') !== false) {
            $this->setBrowser(self::BROWSER_IE);
            $this->setVersion('11.0');

            return true;
        } // Test for v1 - v1.5 IE
        elseif (stripos($this->_agent, 'microsoft internet explorer') !== false) {
            $this->setBrowser(self::BROWSER_IE);
            $this->setVersion('1.0');
            $aresult = stristr($this->_agent, '/');
            if (preg_match('/308|425|426|474|0b1/i', $aresult)) {
                $this->setVersion('1.5');
            }

            return true;
        } // Test for versions > 1.5
        elseif (stripos($this->_agent, 'msie') !== false && stripos($this->_agent, 'opera') === false) {
            // See if the browser is the odd MSN Explorer
            if (stripos($this->_agent, 'msnb') !== false) {
                $aresult = explode(' ', stristr(str_replace(';', '; ', $this->_agent), 'MSN'));
                $this->setBrowser(self::BROWSER_MSN);
                $this->setVersion(str_replace(array('(', ')', ';'), '', $aresult[1]));

                return true;
            }
            $aresult = explode(' ', stristr(str_replace(';', '; ', $this->_agent), 'msie'));
            $this->setBrowser(self::BROWSER_IE);
            $this->setVersion(str_replace(array('(', ')', ';'), '', $aresult[1]));

            return true;
        } // Test for Pocket IE
        elseif (stripos($this->_agent, 'mspie') !== false || stripos($this->_agent, 'pocket') !== false) {
            $aresult = explode(' ', stristr($this->_agent, 'mspie'));
            $this->setPlatform(self::PLATFORM_WINDOWS_CE);
            $this->setBrowser(self::BROWSER_POCKET_IE);

            if (stripos($this->_agent, 'mspie') !== false) {
                $this->setVersion($aresult[1]);
            } else {
                $aversion = explode('/', $this->_agent);
                $this->setVersion($aversion[1]);
            }

            return true;
        }

        return false;
    }

 /**
     * Determine if the browser is Microsoft Edge or not
     *
     * @return bool True if the browser is Edge otherwise false
     */
    protected function checkBrowserEdge()
    {
        if (stripos($this->_agent, 'edge') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'Edge'));
            $aversion = explode(' ', $aresult[1]);
            $this->setVersion($aversion[0]);
            $this->setBrowser(self::BROWSER_EDGE);

            return true;

        }

        return false;
    }

    /**
     * Determine if the browser is Opera or not (last updated 1.7).
     *
     * @return bool True if the browser is Opera otherwise false
     */
    protected function checkBrowserOpera()
    {
        if (stripos($this->_agent, 'opera mini') !== false) {
            $resultant = stristr($this->_agent, 'opera mini');
            if (preg_match('/\//', $resultant)) {
                $aresult = explode('/', $resultant);
                $aversion = explode(' ', $aresult[1]);
                $this->setVersion($aversion[0]);
            } else {
                $aversion = explode(' ', stristr($resultant, 'opera mini'));
                $this->setVersion($aversion[1]);
            }
            $this->_browser_name = self::BROWSER_OPERA_MINI;

            return true;
        } elseif (stripos($this->_agent, 'opera') !== false) {
            $resultant = stristr($this->_agent, 'opera');
            if (preg_match('/Version\/([0-9]*.[0-9]*)$/', $resultant, $matches)) {
                $this->setVersion($matches[1]);
            } elseif (preg_match('/\//', $resultant)) {
                $aresult = explode('/', str_replace('(', ' ', $resultant));
                $aversion = explode(' ', $aresult[1]);
                $this->setVersion($aversion[0]);
            } else {
                $aversion = explode(' ', stristr($resultant, 'opera'));
                $this->setVersion(isset($aversion[1]) ? $aversion[1] : '');
            }
            $this->_browser_name = self::BROWSER_OPERA;

            return true;
        } elseif (stripos($this->_agent, 'OPR') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'OPR'));

            if (empty($aresult[1])) {
                return false;
            }
            
            $aversion = explode(' ', $aresult[1]);
            $this->setVersion($aversion[0]);
            $this->setBrowser(self::BROWSER_OPERA_CHROMIUM);

            return true;

        }

        return false;
    }

    /**
     * Determine if the browser is Chrome or not (last updated 1.7).
     *
     * @return bool True if the browser is Chrome otherwise false
     */
    protected function checkBrowserChrome()
    {
        if (stripos($this->_agent, 'Chrome') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'Chrome'));
            $aversion = explode(' ', $aresult[1]);
            $this->setVersion($aversion[0]);
            $this->setBrowser(self::BROWSER_CHROME);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is WebTv or not (last updated 1.7).
     *
     * @return bool True if the browser is WebTv otherwise false
     */
    protected function checkBrowserWebTv()
    {
        if (stripos($this->_agent, 'webtv') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'webtv'));
            $aversion = explode(' ', $aresult[1]);
            $this->setVersion($aversion[0]);
            $this->setBrowser(self::BROWSER_WEBTV);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is NetPositive or not (last updated 1.7).
     *
     * @return bool True if the browser is NetPositive otherwise false
     */
    protected function checkBrowserNetPositive()
    {
        if (stripos($this->_agent, 'NetPositive') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'NetPositive'));
            $aversion = explode(' ', $aresult[1]);
            $this->setVersion(str_replace(array('(', ')', ';'), '', $aversion[0]));
            $this->setBrowser(self::BROWSER_NETPOSITIVE);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Galeon or not (last updated 1.7).
     *
     * @return bool True if the browser is Galeon otherwise false
     */
    protected function checkBrowserGaleon()
    {
        if (stripos($this->_agent, 'galeon') !== false) {
            $aresult = explode(' ', stristr($this->_agent, 'galeon'));
            $aversion = explode('/', $aresult[0]);
            $this->setVersion($aversion[1]);
            $this->setBrowser(self::BROWSER_GALEON);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Konqueror or not (last updated 1.7).
     *
     * @return bool True if the browser is Konqueror otherwise false
     */
    protected function checkBrowserKonqueror()
    {
        if (stripos($this->_agent, 'Konqueror') !== false) {
            $aresult = explode(' ', stristr($this->_agent, 'Konqueror'));
            $aversion = explode('/', $aresult[0]);
            $this->setVersion($aversion[1]);
            $this->setBrowser(self::BROWSER_KONQUEROR);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is iCab or not (last updated 1.7).
     *
     * @return bool True if the browser is iCab otherwise false
     */
    protected function checkBrowserIcab()
    {
        if (stripos($this->_agent, 'icab') !== false) {
            $aversion = explode(' ', stristr(str_replace('/', ' ', $this->_agent), 'icab'));
            $this->setVersion($aversion[1]);
            $this->setBrowser(self::BROWSER_ICAB);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is OmniWeb or not (last updated 1.7).
     *
     * @return bool True if the browser is OmniWeb otherwise false
     */
    protected function checkBrowserOmniWeb()
    {
        if (stripos($this->_agent, 'omniweb') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'omniweb'));
            $aversion = explode(' ', isset($aresult[1]) ? $aresult[1] : '');
            $this->setVersion($aversion[0]);
            $this->setBrowser(self::BROWSER_OMNIWEB);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Phoenix or not (last updated 1.7).
     *
     * @return bool True if the browser is Phoenix otherwise false
     */
    protected function checkBrowserPhoenix()
    {
        if (stripos($this->_agent, 'Phoenix') !== false) {
            $aversion = explode('/', stristr($this->_agent, 'Phoenix'));
            $this->setVersion($aversion[1]);
            $this->setBrowser(self::BROWSER_PHOENIX);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Firebird or not (last updated 1.7).
     *
     * @return bool True if the browser is Firebird otherwise false
     */
    protected function checkBrowserFirebird()
    {
        if (stripos($this->_agent, 'Firebird') !== false) {
            $aversion = explode('/', stristr($this->_agent, 'Firebird'));
            $this->setVersion($aversion[1]);
            $this->setBrowser(self::BROWSER_FIREBIRD);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Netscape Navigator 9+ or not (last updated 1.7)
     * NOTE: (http://browser.netscape.com/ - Official support ended on March 1st, 2008).
     *
     * @return bool True if the browser is Netscape Navigator 9+ otherwise false
     */
    protected function checkBrowserNetscapeNavigator9Plus()
    {
        if (stripos($this->_agent, 'Firefox') !== false && preg_match('/Navigator\/([^ ]*)/i', $this->_agent, $matches)) {
            $this->setVersion($matches[1]);
            $this->setBrowser(self::BROWSER_NETSCAPE_NAVIGATOR);

            return true;
        } elseif (stripos($this->_agent, 'Firefox') === false && preg_match('/Netscape6?\/([^ ]*)/i', $this->_agent, $matches)) {
            $this->setVersion($matches[1]);
            $this->setBrowser(self::BROWSER_NETSCAPE_NAVIGATOR);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Shiretoko or not (https://wiki.mozilla.org/Projects/shiretoko) (last updated 1.7).
     *
     * @return bool True if the browser is Shiretoko otherwise false
     */
    protected function checkBrowserShiretoko()
    {
        if (stripos($this->_agent, 'Mozilla') !== false && preg_match('/Shiretoko\/([^ ]*)/i', $this->_agent, $matches)) {
            $this->setVersion($matches[1]);
            $this->setBrowser(self::BROWSER_SHIRETOKO);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Ice Cat or not (http://en.wikipedia.org/wiki/GNU_IceCat) (last updated 1.7).
     *
     * @return bool True if the browser is Ice Cat otherwise false
     */
    protected function checkBrowserIceCat()
    {
        if (stripos($this->_agent, 'Mozilla') !== false && preg_match('/IceCat\/([^ ]*)/i', $this->_agent, $matches)) {
            $this->setVersion($matches[1]);
            $this->setBrowser(self::BROWSER_ICECAT);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Nokia or not (last updated 1.7).
     *
     * @return bool True if the browser is Nokia otherwise false
     */
    protected function checkBrowserNokia()
    {
        if (preg_match("/Nokia([^\/]+)\/([^ SP]+)/i", $this->_agent, $matches)) {
            $this->setVersion($matches[2]);
            if (stripos($this->_agent, 'Series60') !== false || strpos($this->_agent, 'S60') !== false) {
                $this->setBrowser(self::BROWSER_NOKIA_S60);
            } else {
                $this->setBrowser(self::BROWSER_NOKIA);
            }

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Firefox or not (last updated 1.7).
     *
     * @return bool True if the browser is Firefox otherwise false
     */
    protected function checkBrowserFirefox()
    {
        if (stripos($this->_agent, 'safari') === false) {
            if (preg_match("/Firefox[\/ \(]([^ ;\)]+)/i", $this->_agent, $matches)) {
                $this->setVersion($matches[1]);
                $this->setBrowser(self::BROWSER_FIREFOX);

                return true;
            } elseif (preg_match('/Firefox$/i', $this->_agent, $matches)) {
                $this->setVersion('');
                $this->setBrowser(self::BROWSER_FIREFOX);

                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the browser is Firefox or not (last updated 1.7).
     *
     * @return bool True if the browser is Firefox otherwise false
     */
    protected function checkBrowserIceweasel()
    {
        if (stripos($this->_agent, 'Iceweasel') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'Iceweasel'));
            $aversion = explode(' ', $aresult[1]);
            $this->setVersion($aversion[0]);
            $this->setBrowser(self::BROWSER_ICEWEASEL);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Mozilla or not (last updated 1.7).
     *
     * @return bool True if the browser is Mozilla otherwise false
     */
    protected function checkBrowserMozilla()
    {
        if (stripos($this->_agent, 'mozilla') !== false && preg_match('/rv:[0-9].[0-9][a-b]?/i', $this->_agent) && stripos($this->_agent, 'netscape') === false) {
            $aversion = explode(' ', stristr($this->_agent, 'rv:'));
            preg_match('/rv:[0-9].[0-9][a-b]?/i', $this->_agent, $aversion);
            $this->setVersion(str_replace('rv:', '', $aversion[0]));
            $this->setBrowser(self::BROWSER_MOZILLA);

            return true;
        } elseif (stripos($this->_agent, 'mozilla') !== false && preg_match('/rv:[0-9]\.[0-9]/i', $this->_agent) && stripos($this->_agent, 'netscape') === false) {
            $aversion = explode('', stristr($this->_agent, 'rv:'));
            $this->setVersion(str_replace('rv:', '', $aversion[0]));
            $this->setBrowser(self::BROWSER_MOZILLA);

            return true;
        } elseif (stripos($this->_agent, 'mozilla') !== false && preg_match('/mozilla\/([^ ]*)/i', $this->_agent, $matches) && stripos($this->_agent, 'netscape') === false) {
            $this->setVersion($matches[1]);
            $this->setBrowser(self::BROWSER_MOZILLA);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Lynx or not (last updated 1.7).
     *
     * @return bool True if the browser is Lynx otherwise false
     */
    protected function checkBrowserLynx()
    {
        if (stripos($this->_agent, 'lynx') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'Lynx'));
            $aversion = explode(' ', (isset($aresult[1]) ? $aresult[1] : ''));
            $this->setVersion($aversion[0]);
            $this->setBrowser(self::BROWSER_LYNX);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Amaya or not (last updated 1.7).
     *
     * @return bool True if the browser is Amaya otherwise false
     */
    protected function checkBrowserAmaya()
    {
        if (stripos($this->_agent, 'amaya') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'Amaya'));
            $aversion = explode(' ', $aresult[1]);
            $this->setVersion($aversion[0]);
            $this->setBrowser(self::BROWSER_AMAYA);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Safari or not (last updated 1.7).
     *
     * @return bool True if the browser is Safari otherwise false
     */
    protected function checkBrowserSafari()
    {
        if (stripos($this->_agent, 'Safari') !== false && stripos($this->_agent, 'iPhone') === false && stripos($this->_agent, 'iPod') === false) {
            $aresult = explode('/', stristr($this->_agent, 'Version'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                $this->setVersion($aversion[0]);
            } else {
                $this->setVersion(self::VERSION_UNKNOWN);
            }
            $this->setBrowser(self::BROWSER_SAFARI);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is iPhone or not (last updated 1.7).
     *
     * @return bool True if the browser is iPhone otherwise false
     */
    protected function checkBrowseriPhone()
    {
        if (stripos($this->_agent, 'iPhone') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'Version'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                $this->setVersion($aversion[0]);
            } else {
                $this->setVersion(self::VERSION_UNKNOWN);
            }
            $this->setBrowser(self::BROWSER_IPHONE);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is iPod or not (last updated 1.7).
     *
     * @return bool True if the browser is iPod otherwise false
     */
    protected function checkBrowseriPad()
    {
        if (stripos($this->_agent, 'iPad') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'Version'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                $this->setVersion($aversion[0]);
            } else {
                $this->setVersion(self::VERSION_UNKNOWN);
            }
            $this->setBrowser(self::BROWSER_IPAD);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is iPod or not (last updated 1.7).
     *
     * @return bool True if the browser is iPod otherwise false
     */
    protected function checkBrowseriPod()
    {
        if (stripos($this->_agent, 'iPod') !== false) {
            $aresult = explode('/', stristr($this->_agent, 'Version'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                $this->setVersion($aversion[0]);
            } else {
                $this->setVersion(self::VERSION_UNKNOWN);
            }
            $this->setBrowser(self::BROWSER_IPOD);

            return true;
        }

        return false;
    }

    /**
     * Determine if the browser is Android or not (last updated 1.7).
     *
     * @return bool True if the browser is Android otherwise false
     */
    protected function checkBrowserAndroid()
    {
        if (stripos($this->_agent, 'Android') !== false) {
            $aresult = explode(' ', stristr($this->_agent, 'Android'));
            if (isset($aresult[1])) {
                $aversion = explode(' ', $aresult[1]);
                $this->setVersion($aversion[0]);
            } else {
                $this->setVersion(self::VERSION_UNKNOWN);
            }
            $this->setBrowser(self::BROWSER_ANDROID);

            return true;
        }

        return false;
    }

    /**
     * Determine the user's platform (last updated 1.7).
     */
    protected function checkPlatform()
    {
        if (stripos($this->_agent, 'iPad') !== false) {
            $this->_platform = self::PLATFORM_IPAD;
            $this->setMobile(true);
        } elseif (stripos($this->_agent, 'iPod') !== false) {
            $this->_platform = self::PLATFORM_IPOD;
            $this->setMobile(true);
        } elseif (stripos($this->_agent, 'iPhone') !== false) {
            $this->_platform = self::PLATFORM_IPHONE;
            $this->setMobile(true);
        } elseif (stripos($this->_agent, 'mac') !== false) {
            $this->_platform = self::PLATFORM_APPLE;
        } elseif (stripos($this->_agent, 'android') !== false) {
            $this->_platform = self::PLATFORM_ANDROID;
            $this->setMobile(true);
        } elseif (stripos($this->_agent, 'linux') !== false) {
            $this->_platform = self::PLATFORM_LINUX;
        } elseif (stripos($this->_agent, 'Nokia') !== false) {
            $this->_platform = self::PLATFORM_NOKIA;
            $this->setMobile(true);
        } elseif (stripos($this->_agent, 'BlackBerry') !== false) {
            $this->_platform = self::PLATFORM_BLACKBERRY;
            $this->setMobile(true);
        } elseif (stripos($this->_agent, 'Windows Phone') !== false) {
            $this->_platform = self::PLATFORM_WINDOWSPHONE;
            $this->setMobile(true);
        } elseif (stripos($this->_agent, 'windows') !== false) {
            $this->_platform = self::PLATFORM_WINDOWS;
        } elseif (stripos($this->_agent, 'FreeBSD') !== false) {
            $this->_platform = self::PLATFORM_FREEBSD;
        } elseif (stripos($this->_agent, 'OpenBSD') !== false) {
            $this->_platform = self::PLATFORM_OPENBSD;
        } elseif (stripos($this->_agent, 'NetBSD') !== false) {
            $this->_platform = self::PLATFORM_NETBSD;
        } elseif (stripos($this->_agent, 'OpenSolaris') !== false) {
            $this->_platform = self::PLATFORM_OPENSOLARIS;
        } elseif (stripos($this->_agent, 'SunOS') !== false) {
            $this->_platform = self::PLATFORM_SUNOS;
        } elseif (stripos($this->_agent, 'OS\/2') !== false) {
            $this->_platform = self::PLATFORM_OS2;
        } elseif (stripos($this->_agent, 'BeOS') !== false) {
            $this->_platform = self::PLATFORM_BEOS;
        } elseif (stripos($this->_agent, 'win') !== false) {
            $this->_platform = self::PLATFORM_WINDOWS;
        }
    }
}
