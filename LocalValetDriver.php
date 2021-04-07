<?php

class LocalValetDriver extends LaravelValetDriver
{
    const REMOTE_HOST = 'https://www.energieausweis-online-erstellen.de/';
    const URI_PREFIX = '/app/uploads/';
    private static $tryRemoteFallback = false;

    /**
     * Determine if the driver serves the request.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return bool
     */
    public function serves($sitePath, $siteName, $uri)
    {
        return true;
    }

    /**
     * Determine if the incoming request is for a static file.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string|false
     */
    public function isStaticFile($sitePath, $siteName, $uri)
    {
        $staticFilePath = $sitePath.'/public'.$uri;

        if ( self::stringStartsWith( $uri, self::URI_PREFIX ) ) {
			self::$tryRemoteFallback = true;

			return rtrim( self::REMOTE_HOST, '/' ) . $uri;
		}

        if ($this->isActualFile($staticFilePath)) {
            return $staticFilePath;
        }

        return false;
    }

    /**
	 * This method checks if the remote flag is set and, if so, redirects the request by setting the Location header.
	 *
	 * @param string $staticFilePath
	 * @param string $sitePath
	 * @param string $siteName
	 * @param string $uri
	 */
	public function serveStaticFile( $staticFilePath, $sitePath, $siteName, $uri ) {
		if ( self::$tryRemoteFallback ) {
			header( "Location: $staticFilePath" );
		} else {
			parent::serveStaticFile( $staticFilePath, $sitePath, $siteName, $uri );
		}
	}

    /**
     * Get the fully resolved path to the application's front controller.
     *
     * @param  string  $sitePath
     * @param  string  $siteName
     * @param  string  $uri
     * @return string
     */
    public function frontControllerPath($sitePath, $siteName, $uri)
    {
        if (strpos($uri, '/core') !== false) {
            return is_dir( $sitePath.'/public/'.$uri )
                            ? $sitePath.'/public/'.$uri.'/index.php'
                            : $sitePath.'/public/'.$uri;
        }

        return $sitePath.'/public/index.php';
    }

    /**
	 * @param string $string
	 * @param string $startsWith
	 *
	 * @return bool
	 */
	private static function stringStartsWith( $string, $startsWith ) {
		return strpos( $string, $startsWith ) === 0;
	}
}