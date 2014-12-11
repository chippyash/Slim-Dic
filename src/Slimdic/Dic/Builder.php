<?php
/**
 * Chippyash Slim DIC integration
 * 
 * DIC builder
 * 
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2014, UK
 */
namespace Slimdic\Dic;

use chippyash\Type\String\StringType;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Dumper\XmlDumper;
use Slimdic\Environment;

/**
 * Builder to compile the dic and return the application 
 */
abstract class Builder
{
    /**
     * Base directory name for spool directory
     */
    const SPOOL_DIR = '/spool';
    
    /**
     * Template cache directory inside spool directory
     */
    const TPL_CACHE_DIR = '/tplcache';

    /**
     * Application log directory inside spool directory
     */
    const LOG_DIR = '/logs';
    
    /**
     * DIC php cache name
     */
    const CACHE_PHP_NAME = '/spool/dic.cache.php';
    
    /**
     * DIC XML cache name - written if environment mode == development
     */
    const CACHE_XML_NAME = '/spool/dic.cache.xml';
    
    /**
     * Template for DIC source file name
     */
    const DIC_SOURCE_TPL_NAME = '%s/Site/cfg/dic.%s.xml';
    
    /**
     * Error string
     */
    const ERR_NO_DIC = 'Cannot find DIC definition';
    
    /**
     * Name of parameter inside DIC definition for baseDirectory
     */
    const DIC_PARAM_BASE_DIR = 'baseDir';
    
    /**
     * Name of parameter inside DIC definition for template (view script) cache
     */
    const DIC_PARAM_TPLCACHE_DIR = 'tplCacheDir';
    
    /**
     * Name of parameter inside DIC definition for logging directory
     */
    const DIC_PARAM_LOGDIR = 'logDir';
    
    /**
     * Name of service inside DIC for the Slim application
     */
    const DIC_SRVC_SLIM_APP_NAME = 'slim.app';
    
    /**
     * chmod mode to set spool directory components
     */
    const CHMOD_MODE = 0755;
    
    /**
     * Build the DIC and return the Slim\Slim app component
     * The DIC will be available as app->dic
     * If environment !== 'development' then will create cached version of
     * DIC for subsequent use
     * 
     * @param StringType $baseDir Base dierctory of the application
     * @param StringType $environment environment mode
     * @return \Slim\Slim
     */
    public static function getApp(StringType $baseDir, StringType $environment)
    {
        $diCacheName = $baseDir . self::CACHE_PHP_NAME;
        if ($environment() != Environment::ENVSTATE_DEV && file_exists($diCacheName)) {
            //use the cached version
            require_once $diCacheName;
            $dic = new \ProjectServiceContainer();
        } else {
            $dic = self::buildDic($baseDir, $environment);
        }       
        
        $app = $dic->get(self::DIC_SRVC_SLIM_APP_NAME);
        $app->dic = $dic;
        
        return $app;
    }
    
    /**
     * Build and return the DIC 
     * Will set a parameter 'baseDir' into the DI container
     * Stores cached version of DIC and if environment == 'development', will
     * also write out an xml version in the same location which can be helpful
     * for debugging
     * 
     * @param StringType $baseDir
     * @param StringType $environment
     * 
     * @return Symfony\Component\DependencyInjection\ContainerBuilder
     * 
     * @throws \Exception
     */
    public static function buildDic(StringType $baseDir, StringType $environment)
    {
        $diName = sprintf(self::DIC_SOURCE_TPL_NAME, $baseDir, $environment);
        if (!file_exists($diName)) {
            throw new \Exception(self::ERR_NO_DIC);
        }
        
        //create dic and cache it
        $dic = new ContainerBuilder();
        $loader = new XmlFileLoader($dic, new FileLocator(dirname($diName)));
        $loader->load($diName);

        self::setSpoolDir($dic, $baseDir);
        
        $dic->compile();
        $dumper = new PhpDumper($dic);

        $diCacheName = $baseDir . self::CACHE_PHP_NAME;
        file_put_contents($diCacheName, $dumper->dump());
        
        if ($environment() == Environment::ENVSTATE_DEV) {
            $xmlCacheName = $baseDir . self::CACHE_XML_NAME;
            $xmlDumper = new XmlDumper($dic);
            file_put_contents($xmlCacheName, $xmlDumper->dump());
        }
        
        return $dic;
    }
    
    /**
     * Setup up the spool directory components
     * 
     * @param ContainerBuilder $dic
     * @param StringType $baseDir
     */
    protected static function setSpoolDir(ContainerBuilder $dic, StringType $baseDir)
    {
        $spoolDir = $baseDir . self::SPOOL_DIR;
        $tplCacheDir = $spoolDir . self::TPL_CACHE_DIR;
        $logDir = $spoolDir . self::LOG_DIR;
        
        //make sure we have a spool directory that we can access
        if (!file_exists($spoolDir)) {
            mkdir($spoolDir);
        }
        if (!is_writable($spoolDir)) {
            chmod($spoolDir, self::CHMOD_MODE);
        }
        //make sure we have a view script template cache directory we can access
        if (!file_exists($tplCacheDir)) {
            mkdir($tplCacheDir);
        }
        if (!is_writable($tplCacheDir)) {
            chmod($tplCacheDir, self::CHMOD_MODE);
        }
        
        //make sure we have a logging directory we can access
        if (!file_exists($logDir)) {
            mkdir($logDir);
        }
        if (!is_writable($logDir)) {
            chmod($logDir, self::CHMOD_MODE);
        }
        
        //inject parameters into DIC so it will compile
        $dic->setParameter(self::DIC_PARAM_BASE_DIR, $baseDir());
        $dic->setParameter(self::DIC_PARAM_TPLCACHE_DIR, $tplCacheDir);
        $dic->setParameter(self::DIC_PARAM_LOGDIR, $logDir);
    }
}
