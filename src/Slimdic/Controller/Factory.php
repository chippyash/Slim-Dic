<?php
/*
 * Chippyash Slim DIC integration
 * 
 * A slim MVC Controller factory
 * 
 * @author Ashley Kitson
 * @copyright Ashley Kitson, 2014, UK
 */
namespace Slimdic\Controller;

use Slim\App;
use Chippyash\Type\String\StringType;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Factory class to create controllers
 */
abstract class Factory
{
    /**
     * Do not allow instantiation
     */
    protected function __construct()
    {
    }
    
    /** 
     * Factory method to create a controller
     * 
     * @param App $app  Slim application object
     * @param ServerRequestInterface $request Request object
     * @param ResponseInterface $response Response object
     * @param StringType $ctrlName Namespace name for controller
     * @param array $config Arbitrary configuration for the controller
     * 
     * @return AbstractController
     * 
     * @throws \Exception
     */
    public static function create(App $app, ServerRequestInterface $request, ResponseInterface $response, StringType $ctrlName, array $config = [])
    {
        $cName = $ctrlName();
        if (!class_exists($cName)) {
            throw new \Exception("Class {$cName} does not exist");
        }
        $ctrl = new $cName($app, $request, $response, $config);
        if (!$ctrl instanceof AbstractController) {
            throw new \Exception("{$cName} is not an instance of AbstractController");
        }
        
        return $ctrl;
    }
    
    /**
     * 
     * @param App $app
     * @param String $ctrlName
     * @param array $config
     * @return \Slimdic\Controller\AbstractController
     */
//    protected function createController(App $app, $ctrlName, array $config = [])
//    {
//        $ctrl = new $ctrlName($app, $config);
//
//        return $ctrl;
//    }
}
