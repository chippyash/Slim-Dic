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

use Slim\Slim;
use chippyash\Type\String\StringType;
use Slimdic\Controller\AbstractController;

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
     * @param Slim $app  Slim application object
     * @param StringType $ctrlName Namespace name for controller
     * @param array $config Arbitrary configuration for the controller
     * 
     * @return Slimdic\Controller\AbstractController
     * 
     * @throws \Exception
     */
    public static function create(Slim $app, StringType $ctrlName, array $config = [])
    {
        $cName = $ctrlName();
        if (!class_exists($cName)) {
            throw new \Exception("Class {$cName} does not exist");
        }
        $ctrl = new $cName($app, $config);
        if (!$ctrl instanceof AbstractController) {
            throw new \Exception("{$cName} is not an instance of AbstractController");
        }
        
        return $ctrl;
    }
    
    /**
     * 
     * @param Slim $app
     * @param String $ctrlName
     * @param array $config
     * @return \Slimdic\Controller\AbstractController
     */
    protected function createController(Slim $app, $ctrlName, array $config = [])
    {
        $ctrl = new $ctrlName($app, $config);
        
        return $ctrl;
    }
}
