<?

ini_set('session.gc_maxlifetime',10800);

define("DBPersistent", false);
$DBType = "mysql";
$DBHost = "localhost";
$DBLogin = "usrtwg";
$DBPassword = "x#mMZ2T%}tX0";
$DBName = "newtwg";
$DBDebug = false;
$DBDebugToFile = false;

//@set_time_limit(60);

define("DELAY_DB_CONNECT", false);
define("CACHED_b_file", 3600);
define("CACHED_b_file_bucket_size", 10);
define("CACHED_b_lang", 3600);
define("CACHED_b_option", 3600);
define("CACHED_b_lang_domain", 3600);
define("CACHED_b_site_template", 3600);
define("CACHED_b_event", 3600);
define("CACHED_b_agent", 3660);
define("CACHED_menu", 3600);

define("BX_UTF", true);
define("BX_FILE_PERMISSIONS", 0644);
define("BX_DIR_PERMISSIONS", 0755);
@umask(~BX_DIR_PERMISSIONS);

define("BX_DISABLE_INDEX_PAGE", true);
define("BX_USE_MYSQLI", true);

//Uncomment all for memcache:
define("BX_CACHE_TYPE", "memcache");
define("BX_CACHE_SID", $_SERVER["DOCUMENT_ROOT"]."#01");
define("BX_MEMCACHE_HOST", "127.0.0.1");
define("BX_MEMCACHE_PORT", "11211");


//define("BX_CACHE_TYPE", "file");
//define("BX_CACHE_SID", $_SERVER["DOCUMENT_ROOT"]."#01");

if ($argc > 0
    && $argv[0]
    && !defined('BX_SECURITY_SESSION_READONLY')
    && !defined('BX_SECURITY_SESSION_VIRTUAL')
) {
    define('BX_SECURITY_SESSION_READONLY', true);
}
// if ($argc > 0
//     && $argv[0]){
//     @ini_set("memory_limit", "4048M");
// } else {
//     @ini_set("memory_limit", "1024M");
// }

define('BX_SECURITY_SESSION_MEMCACHE_HOST', 'localhost');
define('BX_SECURITY_SESSION_MEMCACHE_PORT', 11211);

if(!(defined("CHK_EVENT") && CHK_EVENT===true))
    define("BX_CRONTAB_SUPPORT", true);

