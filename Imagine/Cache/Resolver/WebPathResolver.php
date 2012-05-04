<?php

namespace Liip\ImagineBundle\Imagine\Cache\Resolver;

use Liip\ImagineBundle\Imagine\Cache\CacheManagerAwareInterface,
    Liip\ImagineBundle\Imagine\Cache\CacheManager;

use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class WebPathResolver extends AbstractFilesystemResolver implements CacheManagerAwareInterface
{
    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * @param CacheManager $cacheManager
     */
    public function setCacheManager(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(Request $request, $path, $filter)
    {
        $browserPath = $this->decodeBrowserPath($this->getBrowserPath($path, $filter));
        $targetPath = $this->getFilePath($path, $filter, $request->getBaseUrl());

        // if the file has already been cached, we're probably not rewriting
        // correctly, hence make a 301 to proper location, so browser remembers
        if (file_exists($targetPath)) {
            return new RedirectResponse($request->getBasePath().$browserPath);
        }

        return $targetPath;
    }

    /**
     * {@inheritDoc}
     */
    public function getBrowserPath($targetPath, $filter, $absolute = false)
    {
        $params = array('path' => ltrim($targetPath, '/'));

        return str_replace(
            urlencode($params['path']),
            urldecode($params['path']),
            $this->cacheManager->getRouter()->generate('_imagine_'.$filter, $params, $absolute)
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function getFilePath($path, $filter, $basePath = '')
    {
        $browserPath = $this->decodeBrowserPath($this->getBrowserPath($path, $filter));

        // if cache path cannot be determined, return 404
        if (null === $browserPath) {
            throw new NotFoundHttpException('Image doesn\'t exist');
        }

        if (!empty($basePath) && 0 === strpos($browserPath, $basePath)) {
            $browserPath = substr($browserPath, strlen($basePath));
        }

        return $this->cacheManager->getWebRoot().$browserPath;
    }

    /**
     * Decodes the URL encoded browser path.
     *
     * @param string $browserPath
     *
     * @return string
     */
    protected function decodeBrowserPath($browserPath)
    {
        //TODO: find out why I need double urldecode to get a valid path
        return urldecode(urldecode($browserPath));
    }
}
