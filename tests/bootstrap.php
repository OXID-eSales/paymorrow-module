<?php

if ( getenv( "oxPATH" ) ) {
    define( "oxPATH", rtrim( getenv( "oxPATH" ), "/" ) . "/" );
} else {
    if ( !defined( "oxPATH" ) ) {
        die( "oxPATH is not defined" );
    }
}

if ( !defined( "OXID_VERSION_SUFIX" ) ) {
    define( "OXID_VERSION_SUFIX", "" );
}

// setting the include path
set_include_path( get_include_path() . PATH_SEPARATOR . dirname( __FILE__ ) );

require_once "unit/test_config.inc.php";
require_once "unit/OxidTestCase.php";
require_once "additional.inc.php";

define( "oxADMIN_LOGIN", oxDb::getDb()->getOne( "select OXUSERNAME from oxuser where oxid=\"oxdefaultadmin\"" ) );
if ( getenv( "oxADMIN_PASSWD" ) ) {
    define( "oxADMIN_PASSWD", getenv( "oxADMIN_PASSWD" ) );
} else {
    define( "oxADMIN_PASSWD", "admin" );
}
