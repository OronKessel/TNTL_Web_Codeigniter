<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
  |--------------------------------------------------------------------------
  | Display Debug backtrace
  |--------------------------------------------------------------------------
  |
  | If set to TRUE, a backtrace will be displayed along with php errors. If
  | error_reporting is disabled, the backtrace will not display, regardless
  | of this setting
  |
 */
defined('SHOW_DEBUG_BACKTRACE') or define('SHOW_DEBUG_BACKTRACE', true);

/*
  |--------------------------------------------------------------------------
  | File and Directory Modes
  |--------------------------------------------------------------------------
  |
  | These prefs are used when checking and setting modes when working
  | with the file system.  The defaults are fine on servers with proper
  | security, but you may wish (or even need) to change the values in
  | certain environments (Apache running a separate process for each
  | user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
  | always be used to set the mode correctly.
  |
 */
defined('FILE_READ_MODE') or define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') or define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') or define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') or define('DIR_WRITE_MODE', 0755);

/*
  |--------------------------------------------------------------------------
  | File Stream Modes
  |--------------------------------------------------------------------------
  |
  | These modes are used when working with fopen()/popen()
  |
 */
defined('FOPEN_READ') or define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') or define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') or define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESCTRUCTIVE') or define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE') or define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') or define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') or define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') or define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
  |--------------------------------------------------------------------------
  | Exit Status Codes
  |--------------------------------------------------------------------------
  |
  | Used to indicate the conditions under which the script is exit()ing.
  | While there is no universal standard for error codes, there are some
  | broad conventions.  Three such conventions are mentioned below, for
  | those who wish to make use of them.  The CodeIgniter defaults were
  | chosen for the least overlap with these conventions, while still
  | leaving room for others to be defined in future versions and user
  | applications.
  |
  | The three main conventions used for determining exit status codes
  | are as follows:
  |
  |    Standard C/C++ Library (stdlibc):
  |       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
  |       (This link also contains other GNU-specific conventions)
  |    BSD sysexits.h:
  |       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
  |    Bash scripting:
  |       http://tldp.org/LDP/abs/html/exitcodes.html
  |
 */
defined('EXIT_SUCCESS') or define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR') or define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG') or define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE') or define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS') or define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') or define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT') or define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE') or define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN') or define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX') or define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
//Admin Panel Route
//Page Route
define('ADMIN_PAGE_REGISTER', 'index.php/admin/registerPage');
define('ADMIN_PAGE_HOME', 'index.php/admin/index');
define('ADMIN_PAGE_DASHBOARD', 'index.php/admin/dashboardPage');

define('ADMIN_PAGE_EMPLOYEE', 'index.php/admin/employeePage');
define('ADMIN_PAGE_EMPLOYEECONTACT', 'index.php/admin/employeeContactPage');

define('ADMIN_PAGE_CHANNEL_SERVER', 'index.php/admin/serverPage');
define('ADMIN_PAGE_CHANNEL_CHANNEL', 'index.php/admin/channelPage');
define('ADMIN_PAGE_CHANNEL_SORT_GROUP', 'index.php/admin/channelsortPage');
define('ADMIN_PAGE_CHANNEL_EPG', 'index.php/admin/epgPage');
define('ADMIN_PAGE_CHANNEL_TRANSCODER', 'index.php/admin/transcoderPage');

define('ADMIN_PAGE_VOD_CATEGORY', 'index.php/admin/categoryPage');
define('ADMIN_PAGE_VOD_CONTENT', 'index.php/admin/contentPage');
define('ADMIN_PAGE_VOD_SHOWS', 'index.php/admin/showsPage');

define('ADMIN_PAGE_MEMBER', 'index.php/admin/memberPage');
define('ADMIN_PAGE_DEVICE', 'index.php/AdminController/devicePage');
define('ADMIN_PAGE_MEMBER_PACKAGE', 'index.php/admin/packagePage');


//Action Route

define('ADMIN_ACTION_ADDEMPLOYEE', 'index.php/admin/actionAddEmployee');
define('ADMIN_ACTION_EDITEMPLOYEE', 'index.php/admin/actionEditEmployee');
define('ADMIN_ACTION_DELETEEMPLOYEE', 'index.php/AdminController/actionDeleteEmployee');

define('ADMIN_ACTION_ADDSERVER', 'index.php/admin/actionAddServer');
define('ADMIN_ACTION_EDITSERVER', 'index.php/admin/actionEditServer');
define('ADMIN_ACTION_DELETESERVER', 'index.php/AdminController/actionDeleteServer');

define('ADMIN_ACTION_ADDCHANNEL', 'index.php/admin/actionAddChannel');
define('ADMIN_ACTION_EDITCHANNEL', 'index.php/admin/actionEditChannel');
define('ADMIN_ACTION_DELETECHANNEL', 'index.php/AdminController/actionDeleteChannel');

define('ADMIN_ACTION_ADDCHANNELSORT', 'index.php/admin/actionAddChannelSort');
define('ADMIN_ACTION_EDITCHANNELSORT', 'index.php/admin/actionEditChannelSort');
define('ADMIN_ACTION_DELETECHANNELSORT', 'index.php/AdminController/actionDeleteChannelSort');

define('ADMIN_ACTION_UPLOADEPG', 'index.php/AdminController/actionUploadEpg');

define('ADMIN_ACTION_ADDTRANSCODER', 'index.php/admin/actionAddTranscoder');
define('ADMIN_ACTION_EDITTRANSCODER', 'index.php/admin/actionEditTranscoder');
define('ADMIN_ACTION_DELETETRANSCODER', 'index.php/AdminController/actionDeleteTranscoder');

define('ADMIN_ACTION_ADDCATEGORY', 'index.php/admin/actionAddCategory');
define('ADMIN_ACTION_EDITCATEGORY', 'index.php/admin/actionEditCategory');
define('ADMIN_ACTION_DELETECATEGORY', 'index.php/AdminController/actionDeleteCategory');

define('ADMIN_ACTION_ADDSUBCATEGORY', 'index.php/admin/actionAddSubCategory');
define('ADMIN_ACTION_EDITSUBCATEGORY', 'index.php/admin/actionEditSubCategory');
define('ADMIN_ACTION_DELETESUBCATEGORY', 'index.php/AdminController/actionDeleteSubCategory');

define('ADMIN_ACTION_ADDCONTENT', 'index.php/admin/actionAddContent');
define('ADMIN_ACTION_EDITCONTENT', 'index.php/admin/actionEditContent');
define('ADMIN_ACTION_DELETECONTENT', 'index.php/AdminController/actionDeleteContent');

define('ADMIN_ACTION_ADDSHOW', 'index.php/admin/actionAddShow');
define('ADMIN_ACTION_EDITSHOW', 'index.php/admin/actionEditShow');
define('ADMIN_ACTION_DELETESHOW', 'index.php/AdminController/actionDeleteShow');

define('ADMIN_ACTION_ADDMEMBER', 'index.php/admin/actionAddMember');
define('ADMIN_ACTION_EDITMEMBER', 'index.php/admin/actionEditMember');
define('ADMIN_ACTION_DELETEMEMBER', 'index.php/AdminController/actionDeleteMember');


define('ADMIN_ACTION_ADDPACKAGE', 'index.php/admin/actionAddPackage');
define('ADMIN_ACTION_EDITPACKAGE', 'index.php/admin/actionEditPackage');
define('ADMIN_ACTION_DELETEPACKAGE', 'index.php/AdminController/actionDeletePackage');

define('ADMIN_ACTION_ADDDEVICE', 'index.php/admin/actionAddDevice');
define('ADMIN_ACTION_DELETEDEVICE', 'index.php/AdminController/actionDeleteDevice');


define('ADMIN_ACTION_LOGIN', 'index.php/admin/actionLogin');
define('ADMIN_ACTION_LOGOUT', 'index.php/admin/actionLogout');
