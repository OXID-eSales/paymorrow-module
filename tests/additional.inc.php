<?php
// including vfsStream library
require_once dirname( __FILE__ ) . "/libs/vfsStream/vfsStream.php";

// whether to use the original "aModules" chain from the shop
// methods like "initFromMetadata" and "addChain" will append data to the original chain
oxTestModuleLoader::useOriginalChain( false );

// Loads other module classes as dependencies

// oxTestModuleLoader::addDependencies(array(
//     "path/to/the/module"
// ));

// initiates the module from the metadata file
// does nothing if metadata file is not found
oxTestModuleLoader::initFromMetadata();

// appends the module extension chain with the given module files
oxTestModuleLoader::append( array(
    //"oxarticle" => "vendor/mymodule/core/myarticle.php",
));


